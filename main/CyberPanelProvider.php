<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');

class CyberPanelProvider extends WPCPHTTP
{
    function __construct($job, $data)
    {
        $this->job = $job;
        $this->data = $data;
    }
    function connectProvider(){


        $provider = sanitize_text_field($this->data['provider']);
        $name = sanitize_text_field($this->data['name']);
        $token = sanitize_text_field($this->data['token']);
        $imageID = sanitize_text_field($this->data['imageID']);

        if($provider == 'Shared'){

            $localIP = explode(',', $token)[0];
            $localUser = explode(',', $token)[1];
            $localPass = explode(',', $token)[2];

            CommonUtils::writeLogs($localUser,CPWP_ERROR_LOGS);
            CommonUtils::writeLogs($localPass,CPWP_ERROR_LOGS);

            $localToken = base64_encode($localUser . ':' . $localPass);

            CommonUtils::writeLogs($localToken,CPWP_ERROR_LOGS);

            $token = $localIP . ":" . $localUser . ':' . $localToken;

        }else {
            $token = 'Bearer ' . $token;
        }

        $finalDetails = json_encode(array('token'=> $token,
                                          'image'=> $imageID));

        /// Check if hostname already exists
        global $wpdb;

        $result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$name'" );

        if ($result == null) {
            $wpdb->insert(
                $wpdb->prefix . TN_CYBERPANEL_PVD,
                array(
                    'provider' => $provider,
                    'name' => $name,
                    'apidetails' => $finalDetails
                ),
                array(
                    '%s',
                    '%s',
                    '%s'
                )
            );


            $this->job->setDescription(sprintf('Successfully configured %s account named: %s', $provider, $name));
            $this->job->updateJobStatus(WPCP_JobSuccess, 100);

            $cu = new CommonUtils(1, '');
            $cu->fetchJson();
        }
        else{

            $this->job->setDescription(sprintf('Failed to configure %s account named: %s. Error message: Account already exists.', $provider, $name));
            $this->job->updateJobStatus(WPCP_JobFailed, 0);

            $cu = new CommonUtils(0, 'Already exists.');
            $cu->fetchJson();
        }
    }
    function fetchProviderAPIs(){

        $provider = sanitize_text_field($this->data['provider']);

        /// Check if hostname alrady exists
        global $wpdb;

        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE provider = '$provider'" );

        $finalResult = '';

        foreach ($results as $result){
            $finalResult = $finalResult . sprintf('<tr><th scope="row">%s</th><td>%s</td><td>%s</td><td>%s</td><td><button onclick="deleteAPIDetails(%s)" type="button" class="btn btn-danger">Delete</button></td></tr>', $result->id, $result->name, $result->provider, $result->apidetails, $result->id);
        }

        $data = array(
            'status' => 1,
            'result' => $finalResult
        );
        wp_send_json($data);

    }
    function deleteAPIDetails(){

        $id = sanitize_text_field($this->data['id']);

        /// Check if hostname alrady exists
        global $wpdb;

        $table = $wpdb->prefix . TN_CYBERPANEL_PVD;
        $wpdb->delete( $table, array( 'id' => $id ) );

        $data = array(
            'status' => 1,
        );
        wp_send_json($data);

    }
}