<?php

require_once(CPWP_PLUGIN_DIR . 'main/Providers/Hetzner.php');
require_once(CPWP_PLUGIN_DIR . 'main/Providers/DigitalOcean.php');
require_once(CPWP_PLUGIN_DIR . 'main/Providers/manual.php');
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

        CommonUtils::writeLogs(sprintf('Provider currently being used: %s', $result->provider), CPWP_ERROR_LOGS);

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
            elseif ($result->provider == 'DigitalOcean'){
                $cpd = new CyberPanelDigitalOcean($this, $item, $this->data);
                $cpd->createServer();
            }
            else{
                $cpd = new CyberPanelManual($this, $item, $this->data);
                $cpd->createServer();
            }

            CommonUtils::writeLogs('Provider not set', CPWP_ERROR_LOGS);
        }
    }

    function cancelNow(){

        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->cancelNow();
        }
        elseif ($this->findProvider() == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            $cpd->cancelNow();
        }
        else{
            $cpd = new CyberPanelManual($this, $this->data);
            $cpd->cancelNow();
        }
    }

    function shutDown(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->shutDown();
        }
        elseif ($this->findProvider() == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            $cpd->shutDown();
        }
    }

    function rebuildNow(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->rebuildNow();
        }
        elseif ($this->findProvider() == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            $cpd->rebuildNow();
        }
    }

    function serverActions(){
        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->serverActions();
        }
        elseif ($this->findProvider() == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            $cpd->serverActions();
        }
    }

    function rebootNow(){

        if($this->findProvider() == 'Hetzner'){
            $cph = new CyberPanelHetzner($this, $this->data);
            $cph->rebootNow();
        }
        elseif ($this->findProvider() == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            $cpd->rebootNow();
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
        elseif ($result->provider == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            return $cpd->fetchPlans();
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
        elseif ($result->provider == 'DigitalOcean'){
            $cpd = new CyberPanelDigitalOcean($this, $this->data);
            return $cpd->fetchLocations();
        }
    }

}