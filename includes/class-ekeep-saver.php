<?php
class EKEEP_Saver {
  private $logger;

  public function __construct() {
    $this->logger = new EKEEP_Logger();
  }

  public function export_plugin_list() {
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('Vous n\'avez pas les permissions suffisantes pour effectuer cette action.', 'extensions-keep'));
    }

    $active_plugins = get_option('active_plugins');
    $plugin_data = array();

    foreach ($active_plugins as $plugin) {
      $plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
      if (file_exists($plugin_file)) {
        $plugin_info = get_plugin_data($plugin_file);
        $plugin_data[] = array(
          'name' => $plugin,
          'version' => $plugin_info['Version'],
          'title' => $plugin_info['Name'],
          'author' => $plugin_info['Author'],
          'description' => $plugin_info['Description']
        );
      }
    }

    $data_to_export = array('plugins' => $plugin_data);
    $json_data = wp_json_encode($data_to_export, JSON_PRETTY_PRINT);

    // Générer le nom du fichier
    $site_slug = sanitize_title(get_bloginfo('name'));
    $utc_time = gmdate('YmdHis');
    $filename = "ekeep-{$site_slug}-{$utc_time}.json";

    // Créer un résumé détaillé des plugins exportés
    $plugin_summary = "<span class='ekeep-plugin-list'>";
    foreach ($plugin_data as $plugin) {
      $plugin_summary .= sprintf("<strong>%s</strong> (%s) <span class='sep'>|</span> ", esc_html($plugin['title']), esc_html($plugin['version']));
    }
    $plugin_summary = rtrim($plugin_summary, " <span class='sep'>|</span> ") . "</span>";

    // Mettre à jour le journal
    $this->logger->add_log_entry('Export réussi', sprintf('Fichier exporté : <strong>%s</strong><div class="export-plugins">Extensions : %s</div>', esc_html($filename), $plugin_summary));

    // Forcer le téléchargement du fichier
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $json_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    exit;
  }
}