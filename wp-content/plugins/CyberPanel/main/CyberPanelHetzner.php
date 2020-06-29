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

        $name = sanitize_text_field($this->data['name']);
        $token = sanitize_text_field($this->data['token']);

        $token = 'Bearer ' . $token;

        /// Check if hostname alrady exists
        global $wpdb;

        $result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}cyberpanel_hetzner WHERE name = '$name'" );

        if ($result == null) {
            $wpdb->insert(
                $wpdb->prefix . TN_CYBERPANEL_HTZ,
                array(
                    'name' => $name,
                    'token' => $token
                ),
                array(
                    '%s',
                    '%s'
                )
            );

            sprintf('Successfully configured Hetzner account  named: %s', $name);

            $this->job->setDescription(sprintf('Successfully configured Hetzner account named: %s', $name));
            $this->job->updateJobStatus(WPCP_JobSuccess, 100);

            $cu = new CommonUtils(1, '');
            $cu->fetchJson();
        }
        else{

            $this->job->setDescription(sprintf('Failed to configure Hetzner account named: %s. Error message: Account already exists.', $name));
            $this->job->updateJobStatus(WPCP_JobFailed, 0);

            $cu = new CommonUtils(0, 'Already exists.');
            $cu->fetchJson();
        }
    }
}