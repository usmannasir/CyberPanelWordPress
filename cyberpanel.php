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
define('WPCP_INTERVAL', 'P30D');
define('WPCP_PRODUCTID', 'wpcp_productid');
define('WPCP_INVOICE', 'wpcp_invoice');
define('WPCP_PAYMENTID', 'wpcp_paymentid');
define('WPCP_LOCATION', 'wpcp_location');
define('WPCP_PRODUCTNAME', 'wpcp_productname');


require_once(CPWP_PLUGIN_DIR . 'main/functions.php');

// Create Table where Connected servers will be stored

register_activation_hook(__FILE__, 'wpcp_on_activation');
function wpcp_on_activation()
{

    /**
     * Check if WooCommerce is active
     **/
    if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Put your plugin code here
        add_action( 'admin_notices', 'CyberPanel Plugin requires WooCommerce plugin.' );
        die('CyberPanel Plugin requires WooCommerce plugin.');
    }

    global $wpdb;

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

    ## Create Servers Page

    $post_details = array(
        'post_title'    => 'Servers',
        'post_content'  => '<!-- wp:shortcode -->[wpcpservers]<!-- /wp:shortcode -->',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type' => 'page'
    );
    wp_insert_post( $post_details );
}

register_deactivation_hook( __FILE__, 'wpcp_on_deactivation' );

function wpcp_on_deactivation()
{
    //unregister_post_type( 'wpcp_server' );
    //flush_rewrite_rules();
    remove_filter( 'woocommerce_add_cart_item_data', 'wpcp_add_custom_field_item_data');
    remove_filter( 'woocommerce_add_to_cart_validation', 'wpcp_validate_custom_field' );
    remove_filter( 'the_content', 'filter_the_content_in_the_main_loop' );
    remove_shortcode('wpcp_servers');
}
