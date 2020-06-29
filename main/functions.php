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
    <label for="wporg_field">Select Provider
        <div id="WPCPSpinner" class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </label>
    <select name="wpcp_field" id="wpcp_provider" class="postbox">
        <?php
        foreach ($results as $result){
            echo sprintf('<option value="">%s</option>', $result->name);
        }?>
    </select>

    <select name="wporg_field" id="wpcp_provider" class="postbox">
        <?php
        foreach ($results as $result){
            echo sprintf('<option value="">%s</option>', $result->name);
        }?>
    </select>

    <select name="wpcp_providerplans" id="wpcp_providerplans" class="postbox">
    </select>

    <?php


}

