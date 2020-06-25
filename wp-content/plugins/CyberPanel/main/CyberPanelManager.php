<?php


class CyberPanelManager
{
    protected $serverHostname;
    protected $username;
    protected $userToken;
    protected $body;

    function __construct($serverHostname, $username, $userToken)
    {
        $this->serverHostname = $serverHostname;
        $this->username = $username;
        $this->userToken = $userToken;
    }

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
    function VerifyConnection(){
        $this->body = array('controller' => 'verifyLogin');
        return $this->HTTPPostCall();
    }
}