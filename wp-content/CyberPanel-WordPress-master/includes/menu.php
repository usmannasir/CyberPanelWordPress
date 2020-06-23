<?php

//Add admin page to the menu
add_action( 'admin_menu', 'add_admin_page' );
function add_admin_page() {
	// add top level menu page
	add_menu_page(
		'Cyberpanel Hosting', //Page Title
		'Cyberpanel', //Menu Title
		'manage_options', //Capability
		'cyberpanel-hosting-config', //Page slug
		'admin_page_html' //Callback to print html
	);
	add_submenu_page("cyberpanel-hosting-config","Choose Packages CyberPanel",
		"Choose Packages","manage_options","cyberpanel-hosting-packages"
		,"cyberpanel_choose_packages"
	);
}

function admin_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$result = null;
	$setting = new settingtable();
	$type = $setting->getSettings();
	if (gettype ($type) != "boolean"){
		$result = $type;
	}
	include( CyberPanel_PLUGINDIR . '/views/main_settings_page.php' );
}