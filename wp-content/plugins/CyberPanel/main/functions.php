<?php

add_action('admin_menu', 'Main_CyberPanel');

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
function admin_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    /*$result = null;
    $setting = new settingtable();
    $type = $setting->getSettings();
    if (gettype ($type) != "boolean"){
        $result = $type;
    }
    include( CyberPanel_PLUGINDIR . '/views/main_settings_page.php' );*/

    echo "hello world";
}