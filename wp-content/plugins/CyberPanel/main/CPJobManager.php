<?php


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

    function __construct($function, $data, $description)
    {
        $this->function = $function;
        $this->data = $data;
        $this->description = $description;

        if ($description != 0) {
            global $wpdb;
            $wpdb->insert(
                'wp_cyberpanel_jobs',
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

    }

    function jobStatus()
    {
        global $wpdb;
        $results = $wpdb->get_results('select * from wp_cyberpanel_jobs');

        $finalResult = '';

        foreach ($results as $result) {
            $finalResult = $finalResult . '<br>' . $result->description;

        }
        $data = array('status' => 1,
            'result' => $finalResult
        );
        wp_send_json(json_encode($data));
    }

    function RunJob()
    {

        if ($this->function = 'VerifyConnection') {

            $hostname = $this->data['hostname'];
            $username = $this->data['username'];
            $password = $this->data['password'];

            $token = 'Basic ' . base64_encode($username . ':' . $password);

            require_once(CPWP_PLUGIN_DIR . 'main/CyberPanelManager.php');
            $cpm = new CyberPanelManager($hostname, $username, $token);
            wp_send_json($cpm->VerifyConnection());

        } elseif ($this->function = 'VerifyConnection') {
            $this->jobStatus();
        }

    }
}