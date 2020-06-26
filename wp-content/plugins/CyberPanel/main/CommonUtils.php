<?php


class CommonUtils
{
    protected $json;

    function __construct($status, $error)
    {
        $this->json = json_encode(array('status' => $status, 'error_message' => $error));
    }

    function fetchJson(){
        wp_send_json($this->json);
    }
}