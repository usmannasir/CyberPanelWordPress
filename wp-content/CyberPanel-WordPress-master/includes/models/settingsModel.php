<?php


class settingsModel {
	public $setting;
	public $setting_value;

	/**
	 * settingsTable constructor.
	 *
	 * @param $settingName
	 * @param $settingValue
	 */
	public function __construct( $settingName, $settingValue ) {
		$this->setting      = $settingName;
		$this->setting_value = $settingValue;
	}

	/**
	 * @return mixed
	 */
	public function getSetting() {
		return $this->setting;
	}

	/**
	 * @param mixed $setting
	 */
	public function setSetting( $setting ): void {
		$this->setting = $setting;
	}

	/**
	 * @return mixed
	 */
	public function getSettingValue() {
		return $this->setting_value;
	}

	/**
	 * @param mixed $settingValue
	 */
	public function setSettingValue( $settingValue ): void {
		$this->setting_value = $settingValue;
	}


}