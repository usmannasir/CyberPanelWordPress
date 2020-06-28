<?php


class WPCPHTTP
{
    protected $job;
    protected $serverHostname;
    protected $username;
    protected $userToken;
    protected $body;
    protected $data;

    function HTTPPostCall(){
        $headers = array(
            'Authorization' => $this->userToken,
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
        );
        return wp_remote_post( 'https://' . $this->serverHostname . ':8090/cloudAPI/', $args );

    }

    function HTTPPostCallGeneral(){
        $headers = array(
            'Authorization' => $this->data['Token'],
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
        );
        return wp_remote_post( 'https://' . $this->serverHostname . ':8090/cloudAPI/', $args );

    }
}