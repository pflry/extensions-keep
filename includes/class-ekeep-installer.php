<?php
class EKEEP_Installer {
  private $logger;

  public function __construct() {
    $this->logger = new EKEEP_Logger();
  }

  public function install_plugins($plugins) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    $results = [];
    foreach ($plugins as $plugin) {
      $result = $this->install_plugin($plugin);
      if ($result === true) {
        $results[] = $plugin;
        $this->logger->add_log_entry('Extension installée', sprintf('<strong>%s</strong> (%s)', esc_html($plugin['title']), esc_html($plugin['version'])));
      } else {
        $this->logger->add_log_entry('Échec d\'installation', sprintf('<strong>%s</strong> (%s) - %s', esc_html($plugin['title']), esc_html($plugin['version']), $result));
      }
    }
    return $results;
  }

  public function update_plugins($plugins) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    $results = [];
    foreach ($plugins as $plugin) {
      $result = $this->update_plugin($plugin);
      if ($result === true) {
        $results[] = $plugin;
        $this->logger->add_log_entry('Extension mise à jour', sprintf('<strong>%s</strong> (%s)', esc_html($plugin['title']), esc_html($plugin['version'])));
      } else {
        $this->logger->add_log_entry('Échec de mise à jour', sprintf('<strong>%s</strong> (%s) - %s', esc_html($plugin['title']), esc_html($plugin['version']), $result));
      }
    }
    return $results;
  }

  private function install_plugin($plugin) {
    $api = plugins_api('plugin_information', ['slug' => dirname($plugin['name'])]);

    if (is_wp_error($api)) {
      return $api->get_error_message();
    }

    $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
    $installed = $upgrader->install($api->download_link);

    if (is_wp_error($installed) || $installed === false) {
      return is_wp_error($installed) ? $installed->get_error_message() : 'Installation échouée';
    }

    activate_plugin($plugin['name']);
    return true;
  }

  private function update_plugin($plugin) {
    $current = get_site_transient('update_plugins');
    if (!isset($current->response[$plugin['name']])) {
      return false;
    }

    $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
    $upgraded = $upgrader->upgrade($plugin['name']);

    return $upgraded !== false;
  }
}