<?php

require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

class CyberPanelManual extends WPCPHTTP
{

    function __construct($job, $data, $order_id = null)
    {
        $this->job = $job;
        $this->data = $data;
        $this->orderid = $order_id;
    }

    function createServer()
    {
        $this->globalData['productID'] = $this->data->get_product_id();
        $this->globalData['order'] = wc_get_order($this->orderid);
        $product = wc_get_product( $this->globalData['productID'] );
        $this->globalData['productName'] = $product->get_title();

        $replacements = array(
            '{productLine}' =>  $this->orderid . ' ' . $this->globalData['productName'],
            '{serverID}' =>  $this->orderid . ' ' . $this->globalData['productName']
        );

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            WPCPHTTP::$productHTMLManual);

        $my_post = array(
            'post_author' => $this->globalData['order']->user_id,
            'post_title'    => $this->orderid . ' ' . $this->globalData['productName'],
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
        add_post_meta( $post_id, WPCP_ORDERID, $this->globalData['order']->id, true );
        add_post_meta( $post_id, WPCP_PRODUCTNAME, $this->globalData['productName'], true );
        add_post_meta( $post_id, WPCP_STATE, WPCP_ACTIVE, true );
        add_post_meta( $post_id, WPCP_PRODUCTID, $this->globalData['productID'], true );

        $this->globalData['order']->update_status('wc-completed');

        return 1;
    }

    function cancelNow()
    {}
}