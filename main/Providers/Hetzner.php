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
        $productName = wc_get_product( $product_id )->get_title();;
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
        $respData = json_decode(wp_remote_retrieve_body($response));

        try{
            $serverID = $respData->server->id;

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

        $rawHTML = '<!-- wp:html -->
<ul class="horizontal gray">
    <li><a href="javascript:void(0)">{productLine}</a></li>
    <li style="float:right"><a href="javascript:void(0)">Rebuild</a></li>
    <li style="float:right"><a href="javascript:void(0)">Access CyberPanel</a></li>
    <li class="rightli" style="float:right"><a href="javascript:void(0)">Manage</a></li>
</ul>
<!-- /wp:html -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:html -->
        <div class="WPCPBoxed">
            <h3>Registration Date</h3>
            <p>{orderDate}</p>
            <div style="float:left">
                <h4>Recurring Charges</h4>
                <p>$ 28.99</p>
            </div>
            <div style="float:left; margin-left: 5%">
                <h4>State</h4>
                <p>Active</p>
            </div>
        </div>
        <!-- /wp:html --></div>
    <!-- /wp:column -->

    <!-- wp:column -->
    <div class="wp-block-column"><!-- wp:table {"backgroundColor":"subtle-pale-green","className":"is-style-regular"} -->
        <figure class="wp-block-table is-style-regular"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>IPv4 Address</td><td>95.217.221.59</td></tr><tr><td>IPv6 Address</td><td>2a01:4f9:c010:a150::/64</td></tr><tr><td>Data center</td><td>hel1-dc2</td></tr><tr><td>City</td><td>Helsinki</td></tr></tbody></table></figure>
        <!-- /wp:table --></div>
    <!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:heading {"level":3} -->
        <h3>Server Specs</h3>
        <!-- /wp:heading -->

        <!-- wp:table {"backgroundColor":"subtle-pale-green"} -->
        <figure class="wp-block-table"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>CPU Cores</td><td>1</td></tr><tr><td>Memory</td><td>2 GB</td></tr><tr><td>NVME Disk</td><td>40 GB</td></tr></tbody></table></figure>
        <!-- /wp:table -->

        <!-- wp:paragraph -->
        <p></p>
        <!-- /wp:paragraph --></div>
    <!-- /wp:column -->

    <!-- wp:column -->
    <div class="wp-block-column"><!-- wp:heading {"level":3} -->
        <h3>Server Activities</h3>
        <!-- /wp:heading -->

        <!-- wp:table {"backgroundColor":"subtle-pale-green"} -->
        <figure class="wp-block-table"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>create_server</td><td>success</td></tr><tr><td>start_server</td><td>success</td></tr><tr><td>create_server</td><td>success</td></tr></tbody></table></figure>
        <!-- /wp:table --></div>
    <!-- /wp:column --></div>
<!-- /wp:columns -->';

        $replacements = array(
            '{productLine}' => $productName . ' - ' . $serverID,
            '{orderDate}' => $orderDate,
            );

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $rawHTML);

        $my_post = array(
            'post_title'    => $serverID,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'wpcp_server',
        );

        wp_insert_post( $my_post );

        //$this->job->setDescription(wp_remote_retrieve_body($response));
        //$this->job->updateJobStatus(WPCP_JobSuccess, 100);
    }
}