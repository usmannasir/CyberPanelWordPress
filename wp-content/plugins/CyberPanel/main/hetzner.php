<?php

require_once(CPWP_PLUGIN_DIR . 'main/CPJobManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');

function CyberPanel_Hetzner()
{
    add_submenu_page("cyberpanel","Configure Hetzner Cloud API",
        "Hetzner","manage_options","cyberpanel-hetzner"
        ,"cyberpanel_main_html"
    );
}

add_action('admin_menu', 'CyberPanel_Hetzner');