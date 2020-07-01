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

        $product_name = $this->data->get_name();
        $product_id = $this->data->get_product_id();
        $wpcp_provider = get_post_meta($product_id, 'wpcp_provider');
        $wpcp_providerplans = get_post_meta($product_id, 'wpcp_providerplans');

        $finalPlan = explode(',', $wpcp_providerplans)[0];

        $message = sprintf('Final plan for product id %s is %s', $product_id, $finalPlan);
        error_log($message, 3, CPWP_ERROR_LOGS);

        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $token = json_decode($result->apidetails)->token;
        $message = sprintf('Token product id %s is %s', $product_id, $token);
        error_log($message, 3, CPWP_ERROR_LOGS);

        $this->body = array(
            'name' => $product_name . '-' . $this->orderid,
            'server_type' => $finalPlan,
            'location' => 'nbg1',
            'start_after_create' => true,
            'image' => 'ubuntu-20.04',
            'automount' => false,
        );

        $this->url = 'https://api.hetzner.cloud/v1/servers';
        $response = $this->HTTPPostCall($token);
        error_log(wp_remote_retrieve_body($response), 3, CPWP_ERROR_LOGS);

        $this->job->setDescription(wp_remote_retrieve_body($response));
        $this->job->updateJobStatus(WPCP_JobSuccess, 100);
    }
}