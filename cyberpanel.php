<?php

/**
 * Plugin Name:       CyberPanel
 * Plugin URI:        https://cyberwp.cloud
 * Description:       Manage multiple CyberPanel installations via WordPress.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.1
 * Author:            Usman Nasir
 * Author URI:        https://cyberwp.cloud
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('WPINC')) {
    die("Please don't run via command line.");
}

define('CPWP_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('CPWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CPWP_ERROR_LOGS', CPWP_PLUGIN_DIR . 'error_log');

// Table names for CyberPanel

define('TN_CYBERPANEL_SERVERS', 'cyberpanel_servers');
define('TN_CYBERPANEL_JOBS', 'cyberpanel_jobs');
define('TN_CYBERPANEL_PVD', 'cyberpanel_providers');

/// Jobs Statues

define('WPCP_StartingJob', 0);
define('WPCP_JobFailed', 1);
define('WPCP_JobSuccess', 2);
define('WPCP_JobRunning', 3);

## META VALUES

define('WPCP_PROVIDER', 'wpcp_provider');
define('WPCP_PROVIDERPLANS', 'wpcp_providerplans');
define('WPCP_DUEDATE', 'wpcp_duedate');
define('WPCP_ACTIVEINVOICE', 'wpcp_activeinvoice');
define('WPCP_ORDERID', 'wpcp_orderid');
define('WPCP_INTERVAL', 'P60S');
define('WPCP_PRODUCTID', 'wpcp_productid');


require_once(CPWP_PLUGIN_DIR . 'main/functions.php');

// Create Table where Connected servers will be stored

register_activation_hook(__FILE__, 'on_activation');
function on_activation()
{
    global $wpdb;

    /// Tables that contain details of CyberPanel Server.

    $table_name = $wpdb->prefix . TN_CYBERPANEL_SERVERS;

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  userid mediumint(9) NOT NULL,
  name varchar(500) DEFAULT '' NOT NULL,
  userName varchar(500) DEFAULT '' NOT NULL,
  token varchar(500) DEFAULT '' NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE (name)
)";
    $wpdb->query( $sql );

    /// Table that will contain details of currently queued jobs.

    $table_name = $wpdb->prefix . TN_CYBERPANEL_JOBS;

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  userid mediumint(9) NOT NULL,
  function varchar(50) DEFAULT '' NOT NULL,
  description varchar(500) DEFAULT '' NOT NULL,
  status mediumint(9) DEFAULT 0 NOT NULL,
  percentage mediumint(9) DEFAULT 0,
  token varchar(200) DEFAULT '' NOT NULL,
  date datetime(6) DEFAULT NOW(),
  PRIMARY KEY  (id)
)";
    $wpdb->query( $sql );

    $table_name = $wpdb->prefix . TN_CYBERPANEL_PVD;

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  provider varchar(200) DEFAULT '' NOT NULL,
  name varchar(200) DEFAULT '' NOT NULL,
  apidetails varchar(800) DEFAULT '' NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE (name)
)";

    $wpdb->query( $sql );
}