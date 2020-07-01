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

        $this->body = array(
            'name' => $serverName,
            'server_type' => $finalPlan,
            'location' => 'nbg1',
            'start_after_create' => true,
            'image' => $image,
            'automount' => false,
        );

        $this->url = 'https://api.hetzner.cloud/v1/servers';
        $response = $this->HTTPPostCall($token);
        $serverID = json_decode(wp_remote_retrieve_body($response))->server->id;
        error_log(sprintf('server id %s', $serverID), 3, CPWP_ERROR_LOGS);

        ## Store the order as server post type

        $content = array(
                        'productid' => $product_id,
                        'orderid' => $this->orderid
                        );

        $my_post = array(
            'post_title'    => $serverID,
            'post_content'  => json_encode($content),
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'wpcp_server',
        );

        wp_insert_post( $my_post );

        //$this->job->setDescription(wp_remote_retrieve_body($response));
        //$this->job->updateJobStatus(WPCP_JobSuccess, 100);
    }
}