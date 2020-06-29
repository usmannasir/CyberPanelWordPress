<?php

require_once(CPWP_PLUGIN_DIR . 'main/CPJobManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');

function cyberpanel_hetzner_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_hetzner_html');
    if( ! $cc->checkCapability()){return;}

    include( CPWP_PLUGIN_DIR . 'views/hetzner.php' );

}

function CyberPanel_Hetzner()
{
    add_submenu_page("cyberpanel","Configure Hetzner Cloud API",
        "Hetzner","manage_options","cyberpanel-hetzner"
        ,"cyberpanel_hetzner_html"
    );
}

add_action('admin_menu', 'CyberPanel_Hetzner');

//// Ajax handler

add_action( 'wp_ajax_connectHetzner', 'ajax_connectHetzner' );

//// Ajax to fetch job status

function ajax_connectHetzner() {
    // Handle the ajax request

    $cc = new CapabilityCheck('connectHetzner');
    if( ! $cc->checkCapability()){return;}

    check_ajax_referer( 'CPWP' );

    $cpjm = new CPJobManager('connectHetzner', $_POST, sprintf('Configuring Hetzner account named: %s..', sanitize_text_field($_POST['name'])));
    $cpjm->RunJob();
}