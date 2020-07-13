<?php

require_once(CPWP_PLUGIN_DIR . 'main/Providers/Hetzner.php');
require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');

class ProviderHandler
{
    protected $job;
    protected $data;
    function __construct($job, $data)
    {
        $this->job = $job;
        $this->data = $data;
    }

    function findProvider(){

        $serverID = sanitize_text_field($this->data['serverID']);
        $message = sprintf('Original post id for server: %s', $serverID);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

        ## Get post ID for server.
        $page = get_page_by_title($serverID, OBJECT, 'wpcp_server'); // enter your page title
        $postIDServer = $page->ID;
        $message = sprintf('Server post id: %s', $postIDServer);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

        ## Get product id of this server.
        $product_id = get_post_meta($postIDServer, 'wpcp_productid', true);
        $message = sprintf('Product post id: %s', $product_id);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

        ## Get provider name to decide which class to call
        $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);
        $message = sprintf('Provider %s', $wpcp_provider);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        return $result->provider;
    }

    function createServer(){

        $message = sprintf('Creating servers for order id: %s', $this->data);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

        $order = wc_get_order($this->data);
        $items = $order->get_items();

        foreach ($items as $item) {

            $product_id = $item->get_product_id();
            $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);

            global $wpdb;

            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");
            $message = sprintf('Provider for product id %s is %s, order id: %s', $product_id, $result->provider, $this->data);
            CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);

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

    function cancelNow(){

        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->cancelNow();
        }
    }

    function shutDown(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->shutDown();
        }
    }

    function rebuildNow(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, sanitize_text_field($this->data['serverID']));
            $cph->rebuildNow();
        }
    }

    function serverActions(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, sanitize_text_field($this->data['serverID']));
            $cph->serverActions();
        }
    }

    function rebootNow(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, sanitize_text_field($this->data['serverID']));
            $cph->rebootNow();
        }
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

    function fetchLocations(){

        $wpcp_provider = sanitize_text_field($this->data['wpcp_provider']);
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        if ($result->provider == 'Hetzner') {
            $cph = new CyberPanelHetzner($this, $this->data);
            return $cph->fetchLocations();
        }
    }

}