<?php


class CommonUtils
{
    protected $json;
    static $CurrentLog = 3;
    static $DEBUG = 3;

    function __construct($status, $error)
    {
        $this->json = json_encode(array('status' => $status, 'result' => $error));
    }

    function fetchJson(){
        wp_send_json($this->json);
    }

    static function writeLogs($message, $filePath){
        if(CommonUtils::$CurrentLog == CommonUtils::$DEBUG) {
            $writeToFile = fopen($filePath, "a");
            fwrite($writeToFile, $message . '\n');
            fclose($writeToFile);
        }
    }
}