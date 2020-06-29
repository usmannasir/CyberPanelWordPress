<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelManager extends WPCPHTTP
{

    function __construct($job, $data)
    {
        $hostname = sanitize_text_field($this->data['hostname']);
        $this->job = $job;
        $this->data = $data;
        $this->url = 'https://' . $hostname . ':8090/cloudAPI/';
    }


    function VerifyConnection(){

        $hostname = sanitize_text_field($this->data['hostname']);
        $username = sanitize_text_field($this->data['username']);
        $password = sanitize_text_field($this->data['password']);

        $token = 'Basic ' . base64_encode($username . ':' . $password);

        /// Check if hostname already exists
        global $wpdb;

        $result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}cyberpanel_servers WHERE name = '$hostname'" );

        if ($result == null) {

            $this->body = array(
                'controller' => 'verifyLogin',
                'serverUserName' => $username
            );

            $response = $this->HTTPPostCall($token);
            $data = json_decode(wp_remote_retrieve_body($response));

            if ($data->status == 1) {
                $wpdb->insert(
                    $wpdb->prefix . TN_CYBERPANEL_SERVERS,
                    array(
                        'name' => $hostname,
                        'userName' => $username,
                        'token' => $token,
                        'userid' => get_current_user_id()
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%d'
                    )
                );

                $this->job->setDescription(sprintf('%s successfully added.', $hostname));
                $this->job->updateJobStatus(WPCP_JobSuccess, 100);

                return $data;
            }
            else{

                $this->job->setDescription(sprintf('Failed to add %s. Error message: %s', $hostname, $response));
                $this->job->updateJobStatus(WPCP_JobFailed, 0);

                $cu = new CommonUtils(0, $data->error_message);
                $cu->fetchJson();
            }
        }
        else{

            $this->job->setDescription(sprintf('Failed to add %s. Error message: This server already exists.', $hostname));
            $this->job->updateJobStatus(WPCP_JobFailed, 0);

            $cu = new CommonUtils(0, 'Already exists.');
            $cu->fetchJson();
        }
    }
}