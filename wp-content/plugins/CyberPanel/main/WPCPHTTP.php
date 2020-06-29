<?php


class WPCPHTTP
{
    protected $job;
    protected $data;
    protected $url;
    protected $body;

    function HTTPPostCall($token){
        $headers = array(
            'Authorization' => $token,
            'Content-type' => 'application/json'
        );

        $args = array(
            'body'        => json_encode($this->body),
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $headers,
            'cookies'     => array(),
            'sslverify'   => false
        );
        return wp_remote_post( $this->url, $args );

    }
}