<?php

class WPCPHTTP
{
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