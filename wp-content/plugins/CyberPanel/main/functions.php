<?php


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