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

        if (!$this->data) {
            return;
        }

        $order = wc_get_order($this->data);

        $items = $order->get_items();
        foreach ($items as $item) {

            //$product_name = $item->get_name();
            $product_id = $item->get_product_id();

            $wpcp_provider = get_post_meta($product_id, 'wpcp_provider');
            $wpcp_providerplans = get_post_meta($product_id, 'wpcp_providerplans');

            global $wpdb;

            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

            $this->body = '{
  "name": "my-server",
  "server_type": "cx11",
  "location": "nbg1",
  "start_after_create": true,
  "image": "ubuntu-20.04",
  "volumes": [
    1
  ],
  "networks": [
    1
  ],
  "automount": false
}';

            $token = 'Bearer qQyRuvISbepOGjDmdyJBakqXSDAQIVTsK7nLhYpouxaE8rq19kqfZdphei9nfn87';
            $this->url = 'https://api.hetzner.cloud/v1/servers';
            $response = $this->HTTPPostCall($token);
            error_log(wp_remote_retrieve_body($response), 3, CPWP_ERROR_LOGS);

        }

        $order->update_status('wc-completed');

//        if ($order->data['status'] == 'wc-completed') {
//            $payment_method = $order->get_payment_method();
//            if ($payment_method != "cod") {
//                $order->update_status('wc-completed');
//            }
//        }
    }
}