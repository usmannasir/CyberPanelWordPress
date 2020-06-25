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

        $this->body = array(
            'controller' => 'verifyLogin',
            'serverUserName' => $this->username
        );

        $response = $this->HTTPPostCall();

        $data = json_decode(wp_remote_retrieve_body( $response ));

        if ($data['status'] == 1){
            global $wpdb;
            $wpdb->insert(
                'wp_cyberpanel_servers',
                array(
                    'name' => $this->serverHostname,
                    'userName' => $this->username,
                    'token' => $this->userToken
                ),
                array(
                    '%s',
                    '%s',
                    '%s'
                )
            );
        }

        return $response;
    }
}