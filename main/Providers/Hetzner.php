<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelHetzner extends WPCPHTTP
{

    function __construct($job, $data, $order_id = null)
    {
        $this->job = $job;
        $this->data = $data;
        $this->orderid = $order_id;
    }

    function setupTokenImagePostID(){

        $serverID = sanitize_text_field($this->data['serverID']);
        $page = get_page_by_title($serverID,OBJECT, 'wpcp_server'); // enter your page title
        $this->postIDServer = $page->ID;

        ## Get product id of this server.
        $product_id = get_post_meta($this->postIDServer, 'wpcp_productid', true);
        $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);

        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $this->token = json_decode($result->apidetails)->token;
        $this->image = json_decode($result->apidetails)->image;

    }

    function fetchPlans()
    {
        $wpcp_provider = sanitize_text_field($this->data['wpcp_provider']);

        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $token = json_decode($result->apidetails)->token;
        $this->url = 'https://api.hetzner.cloud/v1/pricing';
        $response = $this->HTTPPostCall($token, 'GET');
        $data = json_decode(wp_remote_retrieve_body($response));
        $types = $data->pricing->server_types;

        $finalResult = '';

        foreach ($types as $type) {
            $finalResult = $finalResult . sprintf('<option>%s</option>', $type->name . ',' . rtrim($type->prices[0]->price_monthly->net, '0'));
        }

        $data = array(
            'status' => 1,
            'result' => $finalResult
        );

        return $data;
    }

    function fetchLocations()
    {
        $wpcp_provider = sanitize_text_field($this->data['wpcp_provider']);

        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $token = json_decode($result->apidetails)->token;
        $this->url = 'https://api.hetzner.cloud/v1/datacenters';
        $response = $this->HTTPPostCall($token, 'GET');
        $data = json_decode(wp_remote_retrieve_body($response));

        $finalResult = '';

        foreach ($data->datacenters as $datacenter){
            $finalResult = $finalResult . sprintf('<option>%s</option>', $datacenter->location->city . ',' . $datacenter->location->name);
        }

        return $finalResult;
    }

    function createServer()
    {
        ## Fetch token image and provider that can be used to create this server. This function will populate $this->globalData

        $this->fetchImageTokenProvider();

        ##

        $this->globalData['serverName'] = substr(str_shuffle(WPCPHTTP::$permitted_chars), 0, 10);
        $this->globalData['CyberPanelPassword'] = substr(str_shuffle(WPCPHTTP::$permitted_chars), 0, 15);
        $this->globalData['RootPassword'] = substr(str_shuffle(WPCPHTTP::$permitted_chars), 0, 15);

        $this->body = array(
            'name' => $this->globalData['serverName'],
            'server_type' => $this->globalData['finalPlan'],
            'start_after_create' => true,
            'image' => $this->image,
            'location' => $this->globalData['finalLocation'],
            'user_data' =>  sprintf("#cloud-config
# run commands
# default: none
# runcmd contains a list of either lists or a string
# each item will be executed in order at rc.local like level with
# output to the console
# - runcmd only runs during the first boot
# - if the item is a list, the items will be properly executed as if
#   passed to execve(3) (with the first arg as the command).
# - if the item is a string, it will be simply written to the file and
#   will be interpreted by 'sh'
#
# Note, that the list has to be proper yaml, so you have to quote
# any characters yaml would eat (':' can be problematic)
# /var/lib/cloud/instance/scripts/runcmd
runcmd:
 - /usr/local/CyberCP/bin/python /usr/local/CyberCP/plogical/adminPass.py --password %s
 - /usr/local/CyberCP/bin/python /usr/local/CyberCP/plogical/apiAccess.py
 - echo \"root:%s\"|chpasswd
", $this->globalData['CyberPanelPassword'], $this->globalData['RootPassword']),
            'automount' => false
        );

        $this->url = 'https://api.hetzner.cloud/v1/servers';
        $response = $this->HTTPPostCall($this->token);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{

            $this->globalData['serverID'] = $respData->server->id;
            $this->globalData['ipv4'] = $respData->server->public_net->ipv4->ip;
            $this->globalData['ipv6'] = $respData->server->public_net->ipv6->ip;
            $this->globalData['cores'] = $respData->server->server_type->cores;
            $this->globalData['memory'] = $respData->server->server_type->memory . 'G';
            $this->globalData['disk'] = $respData->server->server_type->disk . 'GB NVME';
            $this->globalData['datacenter'] = $respData->server->datacenter->name;
            $this->globalData['city'] = $respData->server->datacenter->location->city;

            if( ! isset($this->globalData['serverID']) ){
                throw new Exception($respData->error->message);
            }

            CommonUtils::writeLogs(wp_remote_retrieve_body($response),CPWP_ERROR_LOGS);
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to create server for product id: %s, order id was %s and product name %s. Error message: %s.', $this->globalData['productID'], $this->orderid, $this->globalData['productName'], $respData->error->message), CPWP_ERROR_LOGS);
            return 0;
        }

        $this->serverPostProcessing();

        return 1;
    }

    function cancelNow()
    {

        $serverID = sanitize_text_field($this->data['serverID']);

        $this->url = 'https://api.hetzner.cloud/v1/servers/' . $serverID;

        CommonUtils::writeLogs('Setup credentials.', CPWP_ERROR_LOGS);

        $this->setupTokenImagePostID();

        CommonUtils::writeLogs('Credentials set.', CPWP_ERROR_LOGS);

        $response = $this->HTTPPostCall($this->token, 'DELETE');
        //$respData = wp_remote_retrieve_body($response);
        //CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);

        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;

            if( ! isset($status) ){
                throw new Exception($respData->error->message);
            }
            $post = array(
                'ID' => $this->postIDServer,
                'post_content' => WPCPHTTP::$cancelled,
            );

            wp_update_post($post, true);
            update_post_meta($this->postIDServer, WPCP_STATE, WPCP_TERMINATED);

            ### Send Termination Email

            $orderID= get_post_meta($this->postIDServer, WPCP_ORDERID, true);
            $order = wc_get_order($orderID);

            $replacements = array(
                '{FullName}' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                '{ServerID}' => sanitize_text_field($this->data['serverID'])
            );

            $subject = sprintf('Server with ID# %s cancelled.', sanitize_text_field($this->data['serverID']));

            $content = str_replace(
                array_keys($replacements),
                array_values($replacements),
                get_option(WPCP_SERVER_CANCELLED, WPCPHTTP::$ServerCancelled)
            );

            wp_mail($order->get_billing_email(), $subject, $content);

            ##


            $data = array(
                'status' => 1,
            );
            if( !wp_doing_cron()) {
                wp_send_json($data);
            }
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to cancel server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            if(! wp_doing_cron()) {
                wp_send_json($data);
            }
        }
    }

    function shutDown()
    {

        $serverID = sanitize_text_field($this->data['serverID']);

        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions/poweroff', $serverID);

        $this->setupTokenImagePostID();

        $response = $this->HTTPPostCall($this->token, null, 0);
        //$respData = wp_remote_retrieve_body($response);
        //CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);

        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception($respData->error->message);
            }
            $data = array(
                'status' => 1,
            );

            if(! wp_doing_cron()) {
                wp_send_json($data);
            }
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to shutdown server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            if(! wp_doing_cron()) {
                wp_send_json($data);
            }
        }
    }

    function rebuildNow()
    {

        $this->setupTokenImagePostID();

        $this->body = array(
            'image' => $this->image,
        );

        $serverID = sanitize_text_field($this->data['serverID']);

        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions/rebuild', $serverID);
        $response = $this->HTTPPostCall($this->token);
        //$respData = wp_remote_retrieve_body($response);
        //CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception($respData->error->message);
            }
            $data = array(
                'status' => 1,
            );
            wp_send_json($data);
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to rebuild server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            wp_send_json($data);
        }
    }

    function serverActions()
    {
        $this->setupTokenImagePostID();

        $serverID = sanitize_text_field($this->data['serverID']);

        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions', $serverID);
        $response = $this->HTTPPostCall($this->token, 'GET');
        //$respData = wp_remote_retrieve_body($response);
        //CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $actions = $respData->actions;
            if( ! isset($actions) ){
                throw new Exception($respData->error->message);
            }
            $finalData = '';
            $running = 0;

            foreach (array_reverse($actions) as $action){

                $finalData = $finalData . sprintf('<tr><td>%s</td><td>%s</td></tr>', $action->command, $action->status);

                if($action->status == 'running'){
                    $running = 1;
                }

            }

            $data = array(
                'status' => 1,
                'result' => $finalData,
                'running' => $running
            );
            wp_send_json($data);
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to retrieve server actions. Error message: %s', $e->getMessage()),CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            wp_send_json($data);
        }
    }

    function rebootNow()
    {
        $this->setupTokenImagePostID();

        $serverID = sanitize_text_field($this->data['serverID']);

        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions/reset', $serverID);
        $response = $this->HTTPPostCall($this->token, null, 0);
        //$respData = wp_remote_retrieve_body($response);
        //CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception($respData->error->message);
            }
            $data = array(
                'status' => 1,
            );

            CommonUtils::writeLogs(sprintf('Value of json %d.', $this->data['json']), CPWP_ERROR_LOGS);

            if( !wp_doing_cron() && $this->data['json'] == 1) {
                wp_send_json($data);
            }
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to reboot server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            if( !wp_doing_cron() && $this->data['json'] == 1) {
                wp_send_json($data);
            }
        }
    }
}