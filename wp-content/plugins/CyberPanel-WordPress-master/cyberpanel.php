<?php

/*
Plugin Name: Cyberpanel
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: humzayunas
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/


define( 'CyberPanel_PLUGINDIR', plugin_dir_path( __FILE__ ) );
define('CyberPanel_PLUGINDIR_URL' , plugin_dir_url(__FILE__));

require_once( CyberPanel_PLUGINDIR . '/includes/init.php' );
register_activation_hook( __FILE__, 'on_activation' );
function on_activation() {
	global $wpdb;
	$sql = "CREATE TABLE " . $wpdb->prefix . "cyberpanel_settings (id bigint(20) NOT NULL auto_increment,setting TEXT(50) ,setting_value TEXT(100), PRIMARY KEY (id))";
	if ( $wpdb->query( $sql )){
		echo "Plugin Successfully activated";
	}else{
		echo "There was an error";
	}
}