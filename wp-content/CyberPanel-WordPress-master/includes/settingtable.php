<?php


class settingtable {
	private $tablename;
	private $wpdb;

	/**
	 * settingtable constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb      = $wpdb;
		$this->tablename = $wpdb->prefix . "cyberpanel_settings";
	}
	function getSettings(){
		$result = $this->wpdb->get_results( "SELECT * FROM {$this->tablename}");
		if (count($result) == 0){
			return false;
		}else{
			return $result;
		}
	}
	function checkSetting( $key ) {
		$result = $this->wpdb->get_results( "SELECT * FROM {$this->tablename} WHERE setting LIKE '%{$key}%'");
		if ( $result == null ) {
			return false;
		} else {
			return $result;
		}
	}
	function insertSetting( $args ) {
		foreach ($args as $setting){
			$this->wpdb->insert( $this->tablename,
				array(
					"setting"       => $setting->getSetting(),
					"setting_value" => $setting->getSettingValue()
				), array( "%s", "%s" ) );
		}
		return true;
	}
	function updateSetting($args){
		foreach ($args as $setting){
			$this->wpdb->update($this->tablename,array(
				"setting"       => $setting->getSetting(),
				"setting_value" => $setting->getSettingValue()
			),array(
				"setting" => $setting->getSetting()
			));
		}
	}
	function deleteSettings(){
		return $this->wpdb->query("TRUNCATE TABLE {$this->tablename}");
	}
}