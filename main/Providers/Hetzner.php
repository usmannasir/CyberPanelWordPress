<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelHetzner extends WPCPHTTP
{
    function __construct($job, $data)
    {
        $this->job = $job;
        $this->data = $data;
    }

    function fetchPlans(){

        global $wpdb;

        //$result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}cyberpanel_providers WHERE name = 'wpcp'" );

        //$token = json_decode($result->apidetails)->token;

        $token = 'Bearer qQyRuvISbepOGjDmdyJBakqXSDAQIVTsK7nLhYpouxaE8rq19kqfZdphei9nfn87';
        $this->url = 'https://api.hetzner.cloud/v1/pricing';

        $response = $this->HTTPPostCall($token, 'GET');

        error_log(wp_remote_retrieve_body($response), 3, CPWP_ERROR_LOGS);

        $data = json_decode(wp_remote_retrieve_body($response));

        $finalResult = '';

        foreach ($data as $result){
            $finalResult = $finalResult . sprintf('<option>%s</option>', $result->pricing->server_types[1]);
        }

        $data = array(
            'status' => 1,
            'result' => $finalResult
        );

        return $data;
    }
}