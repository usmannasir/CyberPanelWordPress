<?php

require_once(CPWP_PLUGIN_DIR . 'main/CyberPanelManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');

class CPJobManager
{
    /// Job status variables
    static public $StartingJob = 0;
    static public $JobFailed = 1;
    static public $JobSuccess = 2;
    static public $JobRunning = 3;

    protected $function;
    protected $jobid;
    protected $data;
    protected $description;

    function __construct($function, $data = null, $description = null)
    {
        $this->function = $function;
        $this->data = $data;
        $this->description = $description;

        try {
            if ($description != null) {
                global $wpdb;
                $wpdb->insert(
                    $wpdb->prefix . TN_CYBERPANEL_JOBS,
                    array(
                        'function' => $this->function,
                        'description' => $this->description,
                        'status' => CPJobManager::$StartingJob,
                        'percentage' => 0
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d',
                        '%d'
                    )
                );
                $this->jobid = $wpdb->insert_id;
            }
        } catch (Exception $e) {
            $cu = new CommonUtils(0, $e->getMessage());
            $cu->fetchJson();
        }

    }

    function jobStatus()
    {
        global $wpdb;
        $results = $wpdb->get_results('select * from {$wpdb->prefix}cyberpanel_jobs');

        $finalResult = '';

        foreach ($results as $result) {
            $finalResult = $finalResult . '<br>' . $result->description;
        }
        $data = array(
            'status' => 1,
            'result' => $finalResult
        );
        wp_send_json($data);
    }

    function RunJob()
    {
        try {

            if ($this->function == 'VerifyConnection') {

                $hostname = $this->data['hostname'];
                $username = $this->data['username'];
                $password = $this->data['password'];

                $token = 'Basic ' . base64_encode($username . ':' . $password);

                $cpm = new CyberPanelManager($this->jobid, $hostname, $username, $token);
                wp_send_json($cpm->VerifyConnection());

            } elseif ($this->function == 'jobStatus') {
                $this->jobStatus();
            }
        } catch (Exception $e) {
            $cu = new CommonUtils(0, $e->getMessage());
            $cu->fetchJson();
        }

    }
}