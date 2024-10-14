<?php
/**
 * Plugin Name: Extensions Keep
 * Plugin URI: https://github.com/pflry/extensions-keep
 * Description: Simplifiez la gestion de vos extensions : exportez, installez et partagez en un clic.
 * Version: 1.0.0
 * Author: Paul Fleury
 * Author URI: https://paulfleury.fr
 * License: GPLv3
 * Text Domain: extensions-keep
 * Domain Path: /languages
 */

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
  exit;
}

// Définir les constantes
define('EKEEP_VERSION', '1.0.0');
define('EKEEP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EKEEP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Charger les fichiers nécessaires
require_once EKEEP_PLUGIN_DIR . 'includes/class-ekeep-manager.php';
require_once EKEEP_PLUGIN_DIR . 'includes/class-ekeep-saver.php';
require_once EKEEP_PLUGIN_DIR . 'includes/class-ekeep-installer.php';
require_once EKEEP_PLUGIN_DIR . 'includes/class-ekeep-logger.php';
require_once EKEEP_PLUGIN_DIR . 'includes/class-ekeep-icons.php';
require_once EKEEP_PLUGIN_DIR . 'admin/class-ekeep-admin.php';

// Initialiser le plugin
function ekeep_init() {
  $manager = new EKEEP_Plugin_Manager();
  $manager->init();
  
  // Ajouter l'action pour le téléchargement
  $saver = new EKEEP_Saver();
  add_action('admin_post_ekeep_download_export', array($saver, 'handle_download'));
}
add_action('plugins_loaded', 'ekeep_init');