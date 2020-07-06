<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelHetzner extends WPCPHTTP
{
    protected $orderid;

    function __construct($job, $data, $order_id = null)
    {
        $this->job = $job;
        $this->data = $data;
        $this->orderid = $order_id;
    }

    function fetchPlans()
    {
        $wpcp_provider = sanitize_text_field($this->data['wpcp_provider']);
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");
        if ($result->provider == 'Hetzner') {

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
        }

        return $data;
    }

    function createServer()
    {

        $product_id = $this->data->get_product_id();
        $order = wc_get_order($this->orderid);
        $orderDate = $order->order_date;
        $product = wc_get_product( $product_id );
        $productName = $product->get_title();
        $productPrice = $product->get_regular_price();
        $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);
        $wpcp_providerplans = get_post_meta($product_id, 'wpcp_providerplans', true);

        $finalPlan = explode(',', $wpcp_providerplans)[0];

        $message = sprintf('Final plan for product id %s is %s', $product_id, $finalPlan);
        error_log($message, 3, CPWP_ERROR_LOGS);

        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $token = json_decode($result->apidetails)->token;
        $image = json_decode($result->apidetails)->image;
        $message = sprintf('Token product id %s is %s', $product_id, $token);
        error_log($message, 3, CPWP_ERROR_LOGS);

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

            error_log(wp_remote_retrieve_body($response), 3, CPWP_ERROR_LOGS);
        }
        catch (Exception $e) {
            error_log(sprintf('Failed to create server for product id: %s, order id was %s and product name %s. Error message: %s.', $product_id, $this->orderid, $productName, $respData->error->message), 3, CPWP_ERROR_LOGS);
            return 0;
        }


        ## Store the order as server post type

        $token = base64_encode('admin:' . $CyberPanelPassword);

        $replacements = array(
            '{serverIP}' =>  $ipv4,
            '{token}' =>  $token,
            '{productLine}' => $productName . ' - ' . $serverID,
            '{serverID}' => $serverID,
            '{orderDate}' => date("F j, Y, g:i a",strtotime($orderDate)),
            '{price}' => get_woocommerce_currency_symbol() . ' ' . $productPrice,
            '{ipv4}' => $ipv4,
            '{ipv6}' => $ipv6,
            '{cores}' => $cores,
            '{memory}' => $memory,
            '{disk}' => $disk,
            '{datacenter}' => $datacenter,
            '{city}' => $city,
            );

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            WPCPHTTP::$productHTML);

        $my_post = array(
            'post_title'    => $serverID,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'wpcp_server',
        );

        $post_id = wp_insert_post( $my_post );

        update_post_meta(
            $post_id,
            'wpcp_token',
            $token
        );

        update_post_meta(
            $post_id,
            'wpcp_productid',
            $product_id
        );

        //$this->job->setDescription(wp_remote_retrieve_body($response));
        //$this->job->updateJobStatus(WPCP_JobSuccess, 100);
    }
}