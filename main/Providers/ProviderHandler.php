<?php

require_once(CPWP_PLUGIN_DIR . 'main/Providers/Hetzner.php');

class ProviderHandler
{
    protected $job;
    protected $data;
    function __construct($job, $data)
    {
        $this->job = $job;
        $this->data = $data;
    }

    function createServer(){

        $message = sprintf('Creating servers for order id: %s', $this->data);
        error_log($message, 3, CPWP_ERROR_LOGS);

        $order = wc_get_order($this->data);
        $items = $order->get_items();

        foreach ($items as $item) {

            $product_id = $item->get_product_id();
            $wpcp_provider = get_post_meta($product_id, 'wpcp_provider');

            global $wpdb;

            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");
            $message = sprintf('Provider for product id %s is %s, order id: %s', $product_id, $result->provider, $this->data);
            error_log($message, 3, CPWP_ERROR_LOGS);

            if($result->provider == 'Hetzner'){
                $cph = new CyberPanelHetzner($this, $item, $this->data);
                $cph->createServer();
            }
        }

        //$order->update_status('wc-completed');

//        if ($order->data['status'] == 'wc-completed') {
//            $payment_method = $order->get_payment_method();
//            if ($payment_method != "cod") {
//                $order->update_status('wc-completed');
//            }
//        }

    }

    function fetchPlans(){
        $wpcp_provider = sanitize_text_field($this->data['wpcp_provider']);
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        if ($result->provider == 'Hetzner') {
            $cph = new CyberPanelHetzner($this, $this->data);
            return $cph->fetchPlans();
        }
    }

}