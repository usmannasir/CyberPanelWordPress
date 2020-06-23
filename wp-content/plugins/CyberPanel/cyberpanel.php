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

if (! defined('WPINC')){
    die("Please don't run via command line.");
}

if (! defined('CPWP_PLUGIN_DIR')){
    define ('CPWP_PLUGIN_DIR', plugin_dir_url(__FILE__));
}

function CPWP_load_js(){
    wp_enqueue_script('CPJS', CPWP_PLUGIN_DIR . 'assets/js/cyberpanel.js', 'jQuery');
}

add_action('wp_enqueue_scripts', 'CPWP_load_js');