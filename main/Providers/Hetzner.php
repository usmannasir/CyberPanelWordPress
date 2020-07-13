<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelHetzner extends WPCPHTTP
{
    protected $orderid;
    protected $postIDServer;
    protected $token;
    protected $image;

    function __construct($job, $data, $order_id = null)
    {
        $this->job = $job;
        $this->data = $data;
        $this->orderid = $order_id;
    }

    function setupTokenImagePostID(){

        $page = get_page_by_title($this->data,OBJECT, 'wpcp_server'); // enter your page title
        $this->postIDServer = $page->ID;

        ## Get product id of this server.
        $product_id = get_post_meta($this->postIDServer, 'wpcp_productid', true);

        $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);
        CommonUtils::writeLogs($product_id, CPWP_ERROR_LOGS);
        CommonUtils::writeLogs($wpcp_provider, CPWP_ERROR_LOGS);

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

        $product_id = $this->data->get_product_id();
        $order = wc_get_order($this->orderid);
        $product = wc_get_product( $product_id );
        $productName = $product->get_title();
        $productPrice = $product->get_regular_price();
        $wpcp_provider = get_post_meta($product_id, WPCP_PROVIDER, true);
        $wpcp_providerplans = get_post_meta($product_id, WPCP_PROVIDERPLANS, true);

        $finalPlan = explode(',', $wpcp_providerplans)[0];
        $finalLocation = explode(',', $this->data->get_meta(WPCP_LOCATION, true, 'view'))[1];

        $message = sprintf('Final plan for product id %s is %s', $product_id, $finalPlan);
        CommonUtils::writeLogs($message,CPWP_ERROR_LOGS);

        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $token = json_decode($result->apidetails)->token;
        $image = json_decode($result->apidetails)->image;
        $message = sprintf('Token product id %s is %s', $product_id, $token);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

        $serverName = substr(str_shuffle(WPCPHTTP::$permitted_chars), 0, 10);
        $CyberPanelPassword = substr(str_shuffle(WPCPHTTP::$permitted_chars), 0, 15);
        $rootPassword = substr(str_shuffle(WPCPHTTP::$permitted_chars), 0, 15);

        $this->body = array(
            'name' => $serverName,
            'server_type' => $finalPlan,
            'location' => 'nbg1',
            'start_after_create' => true,
            'image' => $image,
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
", $CyberPanelPassword, $rootPassword),
            'automount' => false,
            'location' => $finalLocation
        );

        $this->url = 'https://api.hetzner.cloud/v1/servers';
        $response = $this->HTTPPostCall($token);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $serverID = $respData->server->id;
            $ipv4 = $respData->server->public_net->ipv4->ip;
            $ipv6 = $respData->server->public_net->ipv6->ip;
            $cores = $respData->server->server_type->cores;
            $memory = $respData->server->server_type->memory . 'G';
            $disk = $respData->server->server_type->disk . 'GB NVME';
            $datacenter = $respData->server->datacenter->name;
            $city = $respData->server->datacenter->location->city;

            if( ! isset($serverID) ){
                throw new Exception('Server failed to create.');
            }

            CommonUtils::writeLogs(wp_remote_retrieve_body($response),CPWP_ERROR_LOGS);
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to create server for product id: %s, order id was %s and product name %s. Error message: %s.', $product_id, $this->orderid, $productName, $respData->error->message), CPWP_ERROR_LOGS);
            return 0;
        }


        ## Store the order as server post type

        $token = base64_encode('admin:' . $CyberPanelPassword);

        $replacements = array(
            '{serverIP}' =>  $ipv4,
            '{token}' =>  $token,
            '{productLine}' => $productName . ' - ' . $serverID,
            '{serverID}' => $serverID,
            '{orderDate}' => get_the_date("F j, Y, g:i a", $this->orderid),
            '{price}' => get_woocommerce_currency_symbol() . ' ' . $productPrice,
            '{ipv4}' => $ipv4,
            '{ipv6}' => $ipv6,
            '{cores}' => $cores,
            '{memory}' => $memory,
            '{disk}' => $disk,
            '{datacenter}' => $datacenter,
            '{city}' => $city,
            '{loader}' => CPWP_PLUGIN_DIR_URL . 'assets/images/loading.gif'
            );

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            WPCPHTTP::$productHTML);

        $my_post = array(
            'post_author' => $order->user_id,
            'post_title'    => $serverID,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'wpcp_server',
        );

        $post_id = wp_insert_post( $my_post );

        $dueDate = new DateTime();
        $interval = new DateInterval(WPCP_INTERVAL);
        $dueDate->add($interval);

        add_post_meta( $post_id, WPCP_DUEDATE, (string) $dueDate->getTimestamp(), true );
        add_post_meta( $post_id, WPCP_ACTIVEINVOICE, 0, true );
        add_post_meta( $post_id, WPCP_ORDERID, $order->id, true );
        add_post_meta( $post_id, WPCP_PRODUCTNAME, $productName, true );
        add_post_meta( $post_id, WPCP_STATE, WPCP_ACTIVE, true );

        update_post_meta(
            $post_id,
            'wpcp_token',
            $token
        );

        update_post_meta(
            $post_id,
            WPCP_PRODUCTID,
            $product_id
        );

        $order->update_status('wc-completed');

        //$this->job->setDescription(wp_remote_retrieve_body($response));
        //$this->job->updateJobStatus(WPCP_JobSuccess, 100);
    }

    function cancelNow()
    {

        $serverID = sanitize_text_field($this->data['serverID']);

        $this->url = 'https://api.hetzner.cloud/v1/servers/' . $serverID;

        $this->setupTokenImagePostID();

        $response = $this->HTTPPostCall($this->token, 'DELETE');
        $respData = wp_remote_retrieve_body($response);

        CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);

        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception('Failed to cancel server.');
            }
            $post = array(
                'ID' => $this->postIDServer,
                'post_content' => WPCPHTTP::$cancelled,
            );
            wp_update_post($post, true);
            $data = array(
                'status' => 1,
            );
            if(! isset($this->data['cron'])) {
                wp_send_json($data);
            }
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to cancel server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            if(! isset($this->data['cron'])) {
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
        $respData = wp_remote_retrieve_body($response);

        CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);

        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception('Failed to shutdown server.');
            }
            $data = array(
                'status' => 1,
            );

            if(! isset($this->data['cron'])) {
                wp_send_json($data);
            }
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to shutdown server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            if(! isset($this->data['cron'])) {
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

        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions/rebuild', $this->data);
        $response = $this->HTTPPostCall($this->token);
        $respData = wp_remote_retrieve_body($response);
        CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception('Failed to rebuild server.');
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

        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions', $this->data);
        $response = $this->HTTPPostCall($this->token, 'GET');
        $respData = wp_remote_retrieve_body($response);
        CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $actions = $respData->actions;
            if( ! isset($actions) ){
                throw new Exception('Failed to retrieve server actions.');
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
        $this->url = sprintf('https://api.hetzner.cloud/v1/servers/%s/actions/reset', $this->data);
        $response = $this->HTTPPostCall($this->token, null, 0);
        $respData = wp_remote_retrieve_body($response);
        CommonUtils::writeLogs($respData, CPWP_ERROR_LOGS);
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $status = $respData->action->status;
            if( ! isset($status) ){
                throw new Exception('Failed to reboot server.');
            }
            $data = array(
                'status' => 1,
            );
            wp_send_json($data);
        }
        catch (Exception $e) {
            CommonUtils::writeLogs(sprintf('Failed to reboot server. Error message: %s', $e->getMessage()), CPWP_ERROR_LOGS);
            $data = array(
                'status' => 0
            );
            wp_send_json($data);
        }
    }
}