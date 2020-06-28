<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelManager extends WPCPHTTP
{

    function __construct($job, $serverHostname, $username, $userToken)
    {
        $this->job = $job;
        $this->serverHostname = $serverHostname;
        $this->username = $username;
        $this->userToken = $userToken;
    }


    function VerifyConnection(){

        /// Check if hostname alrady exists
        global $wpdb;

        $result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}cyberpanel_servers WHERE name = '$this->serverHostname'" );

        if ($result == null) {

            $this->body = array(
                'controller' => 'verifyLogin',
                'serverUserName' => $this->username
            );

            $response = $this->HTTPPostCall();
            $data = json_decode(wp_remote_retrieve_body($response));

            if ($data->status == 1) {
                $wpdb->insert(
                    $wpdb->prefix . TN_CYBERPANEL_SERVERS,
                    array(
                        'name' => $this->serverHostname,
                        'userName' => $this->username,
                        'token' => $this->userToken,
                        'userid' => get_current_user_id()
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%d'
                    )
                );

                $this->job->setDescription($this->serverHostname . ' successfully added.');
                $this->job->updateJobStatus(WPCP_JobSuccess, 100);

                return $data;
            }
            else{

                $this->job->setDescription('Failed to add: ' . $this->serverHostname . ' Error message: ' . $data->error_message);
                $this->job->updateJobStatus(WPCP_JobFailed, 0);

                $cu = new CommonUtils(0, $data->error_message);
                $cu->fetchJson();
            }
        }
        else{

            $this->job->setDescription('Failed to add: ' . $this->serverHostname . ' Error message: This server already exists.');
            $this->job->updateJobStatus(WPCP_JobFailed, 0);

            $cu = new CommonUtils(0, 'Already exists.');
            $cu->fetchJson();
        }
    }
}