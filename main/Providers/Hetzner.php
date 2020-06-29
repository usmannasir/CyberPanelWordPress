<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelHetzner extends WPCPHTTP
{
    function __construct($job, $data)
    {
        $this->job = $job;
        $this->data = $data;
        $hostname = sanitize_text_field($this->data['hostname']);
        $this->url = 'https://' . $hostname . ':8090/cloudAPI/';
    }

    function fetchPlans(){

        $data = array(
            'status' => 1,
            'result' => '<option>abc</option><option>def</option>'
        );

        return $data;
    }
}