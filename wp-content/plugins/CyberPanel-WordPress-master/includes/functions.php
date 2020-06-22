<?php
include( CyberPanel_PLUGINDIR . "/api/requestBuilder.php" );
require( CyberPanel_PLUGINDIR . "/includes/settingtable.php" );
include( CyberPanel_PLUGINDIR . "/includes/models/settingsModel.php" );
add_action( 'admin_post_cyberpanel_verify', 'cyberPanel_connect_verify' );

function cyberPanel_connect_verify() {
	$settings = new settingtable();
	$args = array(
		new settingsModel( "hostname", $_POST['cyberpanel_hostname'] ),
		new settingsModel( "adminUser", $_POST['cyberpanel_admin_user'] ),
		new settingsModel( "adminPassword", $_POST['cyberpanel_admin_password'] )
	);
	if ($settings->checkSetting( "hostname")) {
		$settings->updateSetting($args);
	}else{
		$settings->insertSetting( $args );
	}


	wp_redirect( 'admin.php?page=cyberpanel-hosting-config' );
}

add_action( 'wp_ajax_verify_connection', 'verify_connection' );

function verify_connection() {
	$request = new requestBuilder("/api/verifyConn");
	$response =  $request->sendApiRequest(
		json_encode((object)array(
			"adminUser" => $_POST['cyberpanel_admin_user'],
			"adminPass" => $_POST['cyberpanel_admin_password']
		))
	);
	if ( json_decode($response["body"])->verifyConn == 1){
		echo "Success";
	}else{
		echo "Failure";
	}

	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_delete_settings', 'delete_settings' );

function delete_settings(){
	$setting = new settingtable();
	$del = $setting->deleteSettings();
	echo $del;
	die();
}
