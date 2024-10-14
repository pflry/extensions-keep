<?php
class EKEEP_Admin {
  private $logger;

  public function __construct() {
    $this->logger = new EKEEP_Logger();
  }

  public function init() {
    add_action('admin_menu', array($this, 'add_admin_menu'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('admin_post_ekeep_reset_log', array($this, 'handle_reset_log'));
  }

  public function add_admin_menu() {
    add_plugins_page(
      __('Extensions Keep', 'extensions-keep'),
      __('Extensions Keep', 'extensions-keep'),
      'manage_options',
      'extensions-keep',
      array($this, 'display_admin_page')
    );
  }

  public function display_admin_page() {
    require_once EKEEP_PLUGIN_DIR . 'admin/views/admin-page.php';
  }

  public function enqueue_admin_scripts($hook) {
    if ('plugins_page_extensions-keep' !== $hook) {
      return;
    }

    wp_enqueue_style('ekeep-admin-style', EKEEP_PLUGIN_URL . 'admin/css/admin-style.css', array(), EKEEP_VERSION);
    wp_enqueue_script('ekeep-admin-script', EKEEP_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), EKEEP_VERSION, true);
  }

  public function handle_reset_log() {
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('You do not have sufficient permissions to perform this action.', 'extensions-keep'));
    }

    check_admin_referer('ekeep_reset_log', 'ekeep_reset_log_nonce');

    $this->logger->reset_log();

    wp_safe_redirect(admin_url('plugins.php?page=extensions-keep'));
    exit;
  }

  public static function render_export_form() {
    ?>
    <form id="ekeep-export-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="ekeep_export_plugins">
      <?php submit_button(esc_html__('Export plugins', 'extensions-keep'), 'primary'); ?>
    </form>
    <?php
  }

  public static function render_upload_form() {
    ?>
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="ekeep_upload_plugins">
      <?php wp_nonce_field('ekeep_upload_plugins', 'ekeep_upload_nonce'); ?>
      <input type="file" name="plugins_list_file" accept=".json">
      <?php 
      submit_button(
        esc_html__('Import and install plugins', 'extensions-keep'), 
        'primary', 
        'submit', 
        true, 
        array('disabled' => 'disabled')
      ); 
      ?>
    </form>
    <?php
  }
}