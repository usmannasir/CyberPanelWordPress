<?php


/// Load all required JS and CSS files for this plugin

function CPWP_load_js(){
    wp_enqueue_style( 'bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css' );
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', 'jQuery');
    wp_enqueue_script('CPJS', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel.js', 'jQuery');
}

add_action('wp_enqueue_scripts', 'CPWP_load_js');

function Main_CyberPanel()
{
    add_menu_page(
        'Cyberpanel', //Page Title
        'Cyberpanel', //Menu Title
        'manage_options', //Capability
        'cyberpanel', //Page slug
        'cyberpanel_main_html' //Callback to print html
    );

    add_submenu_page("cyberpanel","Connect Server",
        "Connect Server","manage_options","cyberpanel-connect-servers"
        ,"cyberpanel_main_html"
    );
}

// This function will generate HTML

function cyberpanel_main_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    include( CPWP_PLUGIN_DIR . '/views/connect-server.php' );

}

add_action('admin_menu', 'Main_CyberPanel');