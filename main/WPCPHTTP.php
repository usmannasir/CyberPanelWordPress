<?php

class WPCPHTTP
{
    static $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    static $productHTML = '<!-- wp:html -->
<ul class="horizontal gray">
    <li><a target="_blank" href="https://{serverIP}:8090/cloudAPI/access?token={token}&serverUserName=admin">{productLine}</a></li>
     <li style="float:right; color:red"><a href="javascript:void(0)">Cancel</a></li>
    <li style="float:right"><a href="javascript:void(0)">Rebuild</a></li>
    <li style="float:right"><a target="_blank" href="https://{serverIP}:8090/cloudAPI/access?={token}">Access CyberPanel</a></li>
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
                <p>{price}</p>
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
        <figure class="wp-block-table is-style-regular"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>IPv4 Address</td><td>{ipv4}</td></tr><tr><td>IPv6 Address</td><td>{ipv6}</td></tr><tr><td>Data center</td><td>{datacenter}</td></tr><tr><td>City</td><td>{city}</td></tr></tbody></table></figure>
        <!-- /wp:table --></div>
    <!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:heading {"level":3} -->
        <h3>Server Specs</h3>
        <!-- /wp:heading -->

        <!-- wp:table {"backgroundColor":"subtle-pale-green"} -->
        <figure class="wp-block-table"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>CPU Cores</td><td>{cores}</td></tr><tr><td>Memory</td><td>{memory}</td></tr><tr><td>Disk</td><td>{disk}</td></tr></tbody></table></figure>
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
    protected $job;
    protected $data;
    protected $url;
    protected $body;

    function HTTPPostCall($token, $method = null){

        $headers = array(
            'Authorization' => $token,
            'Content-type' => 'application/json'
        );

        if ($method == null) {
            $args = array(
                'body' => json_encode($this->body),
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'cookies' => array(),
                'sslverify' => false
            );
            return wp_remote_post( $this->url, $args );
        }else{
            $args = array(
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'cookies' => array(),
                'sslverify' => false
            );
            return wp_remote_get( $this->url, $args );
        }


    }
}