<?php
class EKEEP_Logger {
  private $log_option = 'ekeep_action_log';
  private $max_entries = 100; // Nombre maximal d'entrées à conserver

  public function add_log_entry($action, $details) {
    $log = get_option($this->log_option, array());
    
    $new_entry = array(
      'timestamp' => current_time('mysql'),
      'action' => $action,
      'details' => $details
    );

    array_unshift($log, $new_entry);
    $log = array_slice($log, 0, $this->max_entries);
    update_option($this->log_option, $log);
  }

  public function get_log() {
    return get_option($this->log_option, array());
  }

  public function reset_log() {
    delete_option($this->log_option);
  }

  public function display_log() {
    $log = $this->get_log();
    if (empty($log)) {
      echo '<p>' . esc_html__('Aucune action enregistrée.', 'extensions-keep') . '</p>';
    } else {
      echo '<table class="widefat ekeep-log">';
      echo '<thead><tr><th>' . esc_html__('Date', 'extensions-keep') . '</th><th>' . esc_html__('Action', 'extensions-keep') . '</th><th>' . esc_html__('Détails', 'extensions-keep') . '</th></tr></thead>';
      echo '<tbody>';
      foreach ($log as $entry) {
        echo '<tr>';
        echo '<td>' . esc_html($entry['timestamp']) . '</td>';
        echo '<td>' . esc_html($entry['action']) . '</td>';
        echo '<td>' . wp_kses_post($entry['details']) . '</td>';
        echo '</tr>';
      }
      echo '</tbody></table>';
    }

    // Ajout du formulaire de réinitialisation
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="ekeep_reset_log">';
    wp_nonce_field('ekeep_reset_log', 'ekeep_reset_log_nonce');
    submit_button(__('Réinitialiser le journal', 'extensions-keep'), 'ekeep-delete', 'submit', false);
    echo '</form>';
  }
}