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


    function connectHetzner(){

        $Name = sanitize_text_field($this->data['Name']);
        $Token = sanitize_text_field($this->data['Token']);

        $token = 'Bearer ' . $Token;

        /// Check if hostname alrady exists
        global $wpdb;

        $result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}cyberpanel_hetzner WHERE name = '$Name'" );

        if ($result == null) {
            $wpdb->insert(
                $wpdb->prefix . TN_CYBERPANEL_HTZ,
                array(
                    'name' => $Name,
                    'token' => $token
                ),
                array(
                    '%s',
                    '%s'
                )
            );

            $this->job->setDescription('Hetzner server successfully added, name: ' . $Name);
            $this->job->updateJobStatus(WPCP_JobSuccess, 100);

            $cu = new CommonUtils(1, '');
            $cu->fetchJson();
        }
        else{

            $this->job->setDescription('Failed to add hetzner: ' . $Name . ' Error message: This API already exists.');
            $this->job->updateJobStatus(WPCP_JobFailed, 0);

            $cu = new CommonUtils(0, 'Already exists.');
            $cu->fetchJson();
        }
    }
}