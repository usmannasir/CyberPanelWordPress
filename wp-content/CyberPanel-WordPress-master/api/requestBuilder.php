<?php


class requestBuilder {
	private $hostname;
	private $url;

	/**
	 * requestBuilder constructor.
	 *
	 * @param String $hostname
	 * @param String $url
	 */
	public function __construct( String $url) {

		$this->url = $url;
	}
	public function getApiUrl() : String {
		$settings = new settingtable();
		$this->hostname = $settings->checkSetting("hostname")[0]->setting_value;
		return "https://".$this->hostname.":8090".$this->url;
	}
	function sendApiRequest($data) {
		return wp_remote_post($this->getApiUrl(),array(
				"body" => $data )
		);
	}
}