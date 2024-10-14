<?php
class EKEEP_Plugin_Manager {
  private $saver;
  private $installer;
  private $admin;
  private $logger;

  public function init() {
    $this->saver = new EKEEP_Saver();
    $this->installer = new EKEEP_Installer();
    $this->admin = new EKEEP_Admin();
    $this->logger = new EKEEP_Logger();

    $this->admin->init();

    add_action('admin_post_ekeep_export_plugins', array($this->saver, 'export_plugin_list'));
    add_action('admin_post_ekeep_upload_plugins', array($this, 'handle_upload'));
  }

  public function handle_upload() {
    $nonce = isset($_POST['ekeep_upload_nonce']) ? sanitize_text_field(wp_unslash($_POST['ekeep_upload_nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'ekeep_upload_plugins')) {
      wp_die(esc_html__('Security error. Please try again.', 'extensions-keep'));
    }

    if (!function_exists('WP_Filesystem')) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('You do not have sufficient permissions to perform this action.', 'extensions-keep'));
    }

    if (!isset($_FILES['plugins_list_file']) || !isset($_FILES['plugins_list_file']['error']) || $_FILES['plugins_list_file']['error'] !== UPLOAD_ERR_OK) {
      wp_redirect(add_query_arg('ekeep_message', urlencode(__('No file was uploaded or an error occurred during upload.', 'extensions-keep')), admin_url('plugins.php?page=extensions-keep')));
      exit;
    }

    // Sanitiser les données du fichier uploadé
    $uploaded_file = array(
      'name'     => isset($_FILES['plugins_list_file']['name']) ? sanitize_file_name($_FILES['plugins_list_file']['name']) : '',
      'type'     => isset($_FILES['plugins_list_file']['type']) ? sanitize_mime_type($_FILES['plugins_list_file']['type']) : '',
      'tmp_name' => isset($_FILES['plugins_list_file']['tmp_name']) ? sanitize_text_field($_FILES['plugins_list_file']['tmp_name']) : '',
      'error'    => isset($_FILES['plugins_list_file']['error']) ? intval($_FILES['plugins_list_file']['error']) : UPLOAD_ERR_NO_FILE,
      'size'     => isset($_FILES['plugins_list_file']['size']) ? intval($_FILES['plugins_list_file']['size']) : 0
    );

    // Vérifier que le fichier est bien un JSON
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($finfo->file($uploaded_file['tmp_name']) !== 'application/json') {
      wp_redirect(add_query_arg('ekeep_message', urlencode(__('The uploaded file is not a valid JSON file.', 'extensions-keep')), admin_url('plugins.php?page=extensions-keep')));
      exit;
    }

    WP_Filesystem();
    global $wp_filesystem;

    if (!$wp_filesystem->exists($uploaded_file['tmp_name'])) {
      wp_redirect(add_query_arg('ekeep_message', urlencode(__('The uploaded file was not found.', 'extensions-keep')), admin_url('plugins.php?page=extensions-keep')));
      exit;
    }

    $file_content = $wp_filesystem->get_contents($uploaded_file['tmp_name']);
    if (false === $file_content) {
      wp_redirect(add_query_arg('ekeep_message', urlencode(__('Error reading uploaded file.', 'extensions-keep')), admin_url('plugins.php?page=extensions-keep')));
      exit;
    }

    $imported_data = json_decode($file_content, true);

    if (json_last_error() !== JSON_ERROR_NONE || !$this->validate_imported_data($imported_data)) {
      $this->logger->add_log_entry('Import failed', 'Invalid JSON file or incorrect structure');
      wp_redirect(add_query_arg('ekeep_message', urlencode(__('The imported file is not a valid JSON or does not respect the expected structure.', 'extensions-keep')), admin_url('plugins.php?page=extensions-keep')));
      exit;
    }

    $existing_plugins = $this->get_existing_plugins();
    $plugins_to_install = [];
    $plugins_to_update = [];
    $plugins_unchanged = [];

    foreach ($imported_data['plugins'] as $plugin) {
      if (!isset($existing_plugins[$plugin['name']])) {
        $plugins_to_install[] = $plugin;
      } elseif (version_compare($plugin['version'], $existing_plugins[$plugin['name']]['Version'], '>')) {
        $plugins_to_update[] = $plugin;
      } else {
        $plugins_unchanged[] = $plugin;
      }
    }

    $results = [
      'installed' => $this->installer->install_plugins($plugins_to_install),
      'updated' => $this->installer->update_plugins($plugins_to_update)
    ];

    // Créer un résumé détaillé des plugins importés
    $import_summary = "<p>Imported file: <strong>" . esc_html($uploaded_file['name']) . "</strong></p>";
    $import_summary .= "<p>Installed plugins: " . $this->format_plugin_list($results['installed']) . "</p>";
    $import_summary .= "<p>Upgraded plugins: " . $this->format_plugin_list($results['updated']) . "</p>";
    $import_summary .= "<p>Ignored plugins: " . $this->format_plugin_list($plugins_unchanged) . "</p>";

    $this->logger->add_log_entry('Successful import', $import_summary);

    // Rediriger avec un message de succès
    /* translators: %s: résumé de l'importation */
    $message = sprintf(__('Import complete. %s', 'extensions-keep'), esc_html($import_summary));
    wp_redirect(add_query_arg('ekeep_message', urlencode($message), admin_url('plugins.php?page=extensions-keep')));

    exit;
  }

  private function format_plugin_list($plugins) {
    if (empty($plugins)) {
      return esc_html__('none', 'extensions-keep');
    }
    $formatted = [];
    foreach ($plugins as $plugin) {
      if (isset($plugin['title']) && isset($plugin['version'])) {
        $formatted[] = '<strong>' . esc_html($plugin['title']) . '</strong> (' . esc_html($plugin['version']) . ')';
      } elseif (is_string($plugin)) {
        $formatted[] = '<strong>' . esc_html($plugin) . '</strong>';
      }
    }
    return implode(' | ', $formatted);
  }

  private function validate_imported_data($data) {
    return isset($data['plugins']) && is_array($data['plugins']) &&
           !empty($data['plugins']) && $this->validate_plugin_structure($data['plugins']);
  }

  private function validate_plugin_structure($plugins) {
    foreach ($plugins as $plugin) {
      if (!isset($plugin['name'], $plugin['version'], $plugin['title'], $plugin['author'], $plugin['description'])) {
        return false;
      }
    }
    return true;
  }

  private function get_existing_plugins() {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return get_plugins();
  }
}