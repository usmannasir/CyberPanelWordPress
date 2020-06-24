<?php

/**
 * Plugin Name:       CyberPanel
 * Plugin URI:        https://cyberwp.com
 * Description:       Manage multiple CyberPanel installations via WordPress.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.1
 * Author:            Usman Nasir
 * Author URI:        https://cyberwp.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('WPINC')) {
    die("Please don't run via command line.");
}

if (!defined('CPWP_PLUGIN_DIR')) {
    define('CPWP_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
    define('CPWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

/// Load all required JS and CSS files for this plugin

function CPWP_load_js(){
    wp_enqueue_style( 'bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css' );
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', 'jQuery');
    wp_enqueue_script('CPJS', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel.js', 'jQuery');
}

add_action('wp_enqueue_scripts', 'CPWP_load_js');

require_once(CPWP_PLUGIN_DIR . '/main/functions.php');

// Create Table where Connected servers will be stored

register_activation_hook(__FILE__, 'on_activation');
function on_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberpanel_servers';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar(500) DEFAULT '' NOT NULL,
  userName varchar(500) DEFAULT '' NOT NULL,
  token varchar(500) DEFAULT '' NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE (name)
)";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}