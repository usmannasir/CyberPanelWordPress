<?php

require_once(CPWP_PLUGIN_DIR . 'main/CPJobManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');

/// Load all required JS and CSS files for this plugin

function CPWP_load_static($hook){

    $screen = get_current_screen();

    wp_enqueue_style( 'bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css' );
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', 'jQuery');
    wp_enqueue_style( 'CPCSS', CPWP_PLUGIN_DIR_URL . 'assets/css/cyberpanel.css' );
    wp_enqueue_script('CPJS', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel.js', 'jQuery');

    $title_nonce = wp_create_nonce( 'CPWP' );

    wp_localize_script( 'CPJS', 'CPWP', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $title_nonce,
    ) );
}

add_action('admin_enqueue_scripts', 'CPWP_load_static');

function Main_CyberPanel()
{
    add_menu_page(
        'Cyberpanel', //Page Title
        'Cyberpanel', //Menu Title
        'manage_options', //Capability
        'cyberpanel', //Page slug
        'cyberpanel_main_html' //Callback to print html
    );
}

// This function will generate HTML

function cyberpanel_main_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_main_html');
    if( ! $cc->checkCapability()){return;}

    include( CPWP_PLUGIN_DIR . 'views/connect-server.php' );

}

add_action('admin_menu', 'Main_CyberPanel');

//// Ajax handler

add_action( 'wp_ajax_connectServer', 'ajax_Connect_Server' );

//// Ajax to fetch job status

function ajax_Connect_Server() {
    // Handle the ajax request

    $cc = new CapabilityCheck('Connect_Server');
    if( ! $cc->checkCapability()){return;}

    check_ajax_referer( 'CPWP' );
    $cpjm = new CPJobManager('VerifyConnection', $_POST, 'Verifying connection to: ' . $_POST['hostname']);
    $cpjm->RunJob();
}

add_action( 'wp_ajax_jobStatus', 'ajax_jobStatus' );

function ajax_jobStatus() {
    // Handle the ajax request

    $cc = new CapabilityCheck('jobStatus');
    if( ! $cc->checkCapability()){return;}

    check_ajax_referer( 'CPWP' );

    $cpjm = new CPJobManager('jobStatus', $_POST);
    $cpjm->RunJob();
}

// Configure providers page

function cyberpanel_provider_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_hetzner_html');
    if( ! $cc->checkCapability()){return;}

    include( CPWP_PLUGIN_DIR . 'views/providers.php' );
}

function CyberPanel_Providers()
{
    add_submenu_page("cyberpanel","Configure Providers",
        "Cloud Providers","manage_options","cyberpanel-providers"
        ,"cyberpanel_provider_html"
    );
}

add_action('admin_menu', 'CyberPanel_Providers');

add_action( 'wp_ajax_connectProvider', 'ajax_connectProvider' );

function ajax_connectProvider() {
    // Handle the ajax request

    $cc = new CapabilityCheck('connectProvider');
    if( ! $cc->checkCapability()){return;}

    check_ajax_referer( 'CPWP' );

    $cpjm = new CPJobManager('connectProvider', $_POST, sprintf('Configuring %s account named: %s..',sanitize_text_field($_POST['provider']), sanitize_text_field($_POST['name'])));
    $cpjm->RunJob();
}