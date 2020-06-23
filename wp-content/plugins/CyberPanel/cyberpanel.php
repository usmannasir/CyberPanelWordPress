<?php

/**
 * Plugin Name:       CyberPanel
 * Plugin URI:        https://cyberwp.com
 * Description:       Manage multiple CyberPanel installations via command line.
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

if (! defined(CP_PLUGIN_DIR)){
    define ('CP_PLUGIN_DIR', plugin_dir_path(__FILE__));
}