<?php


class CPJobManager
{
    protected $function;
    protected $jobid;
    protected $data;

    function __construct($function, $data)
    {
        $this->function = $function;
        $this->data = $data;
    }

    function RunJob(){

        if($this->function = 'VerifyConnection'){

            $hostname = $this->data['hostname'];
            $username = $this->data['username'];
            $password = $this->data['password'];

            $token = 'Basic ' . base64_encode($username . ':' . $password);

            require_once(CPWP_PLUGIN_DIR . 'main/CyberPanelManager.php');
            $cpm = new CyberPanelManager($hostname, $username, $token);
            wp_send_json( $cpm->VerifyConnection() );

        }
    }
}