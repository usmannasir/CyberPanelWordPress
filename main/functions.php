<?php

require_once(CPWP_PLUGIN_DIR . 'main/CPJobManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');

/// Load all required JS and CSS files for this plugin

function CPWP_load_static()
{

    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', 'jQuery');
    wp_enqueue_style('CPCSS', CPWP_PLUGIN_DIR_URL . 'assets/css/cyberpanel.css');
    wp_enqueue_script('CPJS', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel.js', 'jQuery');

    $title_nonce = wp_create_nonce('CPWP');

    wp_localize_script('CPJS', 'CPWP', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => $title_nonce,
    ));
}

add_action('admin_enqueue_scripts', 'CPWP_load_static');

// Load scripts js for user end

function CPWP_load_static_frontend()
{

    wp_enqueue_style('CPCSSFE', CPWP_PLUGIN_DIR_URL . 'assets/css/cyberpanel-frontend.css');
    wp_enqueue_script('CPJSFE', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel-frontend.js', array( 'jquery' ));

    $title_nonce = wp_create_nonce('CPWP');

    wp_localize_script('CPJSFE', 'CPWP', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => $title_nonce,
    ));
}

add_action('wp_enqueue_scripts', 'CPWP_load_static_frontend');

///

function Main_CyberPanel()
{
    add_menu_page(
        'CyberPanel', //Page Title
        'CyberPanel', //Menu Title
        'manage_options', //Capability
        'cyberpanel', //Page slug
        'cyberpanel_main_html' //Callback to print html
    );
}

// This function will generate HTML for Main Screen

function cyberpanel_main_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_main_html');
    if (!$cc->checkCapability()) {
        return;
    }

    include(CPWP_PLUGIN_DIR . 'views/connect-server.php');

}

add_action('admin_menu', 'Main_CyberPanel');

//// Ajax handler

add_action('wp_ajax_connectServer', 'ajax_Connect_Server');


function ajax_Connect_Server()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('Connect_Server');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');
    $cpjm = new CPJobManager('VerifyConnection', $_POST, 'Verifying connection to: ' . $_POST['hostname']);
    $cpjm->RunJob();
}

//// Ajax to fetch job status

add_action('wp_ajax_jobStatus', 'ajax_jobStatus');

function ajax_jobStatus()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('jobStatus');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('jobStatus', $_POST);
    $cpjm->RunJob();
}

// Proviers page html

function cyberpanel_provider_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_hetzner_html');
    if (!$cc->checkCapability()) {
        return;
    }

    include(CPWP_PLUGIN_DIR . 'views/providers.php');
}

function CyberPanel_Providers()
{
    add_submenu_page("cyberpanel", "Configure Providers",
        "Cloud Providers", "manage_options", "cyberpanel-providers"
        , "cyberpanel_provider_html"
    );
}

add_action('admin_menu', 'CyberPanel_Providers');

// Ajax for providers

add_action('wp_ajax_connectProvider', 'ajax_connectProvider');

function ajax_connectProvider()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('connectProvider');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('connectProvider', $_POST, sprintf('Configuring %s account named: %s..', sanitize_text_field($_POST['provider']), sanitize_text_field($_POST['name'])));
    $cpjm->RunJob();
}

/// Create meta box to disable for woocommerce posts

/* Fire our meta box setup function on the post editor screen. */

function wpcp_add_custom_box()
{
    $screens = ['product'];
    foreach ($screens as $screen) {
        add_meta_box(
            'wpcp_box_id',           // Unique ID
            'Configure Backend Package for this product.',  // Box title
            'wpcp_custom_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}

add_action('add_meta_boxes', 'wpcp_add_custom_box');

function wpcp_custom_box_html($post)
{
    global $wpdb;
    $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_providers");
    ?>
    <label for="wpcp_provider">Select Provider
        <div id="WPCPSpinner" class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </label>
    <select name="wpcp_provider" id="wpcp_provider" class="postbox">
        <?php
        foreach ($results as $result) {
            echo sprintf('<option>%s</option>', $result->name);
        } ?>
    </select>

    <label id="wpcp_providerplans_label" for="wpcp_providerplans">Select Provider Plan</label>
    <select name="wpcp_providerplans" id="wpcp_providerplans" class="postbox">
    </select>

    <?php


}

// Ajax for fetching provider plans

add_action('wp_ajax_fetchProviderPlans', 'ajax_fetchProviderPlans');

function ajax_fetchProviderPlans()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchProviderPlans');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('fetchProviderPlans', $_POST);
    $cpjm->RunJob();
}

// Save data from product meta

function wpcp_save_postdata($post_id)
{
    if (array_key_exists('wpcp_providerplans', $_POST)) {

        $wpcp_provider = sanitize_text_field($_POST['wpcp_provider']);
        $wpcp_providerplans = sanitize_text_field($_POST['wpcp_providerplans']);

        update_post_meta(
            $post_id,
            'wpcp_provider',
            $wpcp_provider
        );
        update_post_meta(
            $post_id,
            'wpcp_providerplans',
            $wpcp_providerplans
        );
    }
}

add_action('save_post', 'wpcp_save_postdata');

// On woocomm order complete

add_action('woocommerce_payment_complete', 'so_payment_complete');

function so_payment_complete($order_id)
{
//    error_log('woocommerce_payment_complete', 3, CPWP_ERROR_LOGS);
//    $order = wc_get_order($order_id);
//    $billingEmail = $order->billing_email;
//    $products = $order->get_items();
//
//    foreach ($products as $prod) {
//        error_log($prod['product_id'], 3, CPWP_ERROR_LOGS);
//    }
}

add_action('woocommerce_order_status_changed', 'woocommerce_payment_complete_order_status',10,3);
function woocommerce_payment_complete_order_status($order_id)
{
    $order = wc_get_order($order_id);

    error_log(sprintf('Order status: %s', $order->data['status']), 3, CPWP_ERROR_LOGS);

    if($order->data['status'] == 'processing') {
        $message = sprintf('Processing order %s', $order_id);
        $cpjm = new CPJobManager('createServer', $order_id, $message);
        $cpjm->RunJob();
    }
}

// Register CyberPanel Servers Post Type

function wpcp_custom_post_type() {
    register_post_type('wpcp_server',
        array(
            'labels'      => array(
                'name'          => __('Servers', 'textdomain'),
                'singular_name' => __('Server', 'textdomain'),
            ),
            //'public'      => false,
            'has_archive' => false,
            "supports" => array( "title", "editor", "author" ),
            'capability_type' => 'product'
        )
    );
}
add_action('init', 'wpcp_custom_post_type');

//

add_action('wp_ajax_fetchProviderAPIs', 'ajax_fetchProviderAPIs');

function ajax_fetchProviderAPIs()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchProviderPlans');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('fetchProviderAPIs', $_POST);
    $cpjm->RunJob();
}

add_action('wp_ajax_deleteAPIDetails', 'ajax_deleteAPIDetails');

function ajax_deleteAPIDetails()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchProviderPlans');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('deleteAPIDetails', $_POST);
    $cpjm->RunJob();
}

add_action('wp_ajax_cancelNow', 'ajax_cancelNow');

function ajax_cancelNow()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('cancelNow');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('cancelNow', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_rebuildNow', 'ajax_rebuildNow');

function ajax_rebuildNow()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('rebuildNow');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('rebuildNow', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_serverActions', 'ajax_serverActions');

function ajax_serverActions()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('serverActions');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('serverActions', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_rebootNow', 'ajax_rebootNow');

function ajax_rebootNow()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('rebootNow');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('rebootNow', $_POST);
    $cpjm->RunJob();

}