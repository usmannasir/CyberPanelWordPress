<?php

require_once(CPWP_PLUGIN_DIR . 'main/CyberPanelManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');

class CPJobManager
{

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
                        'status' => WPCP_StartingJob,
                        'percentage' => 0,
                        'userid ' => get_current_user_id(),
                        'token' => wp_get_session_token(),
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                    )
                );
                $this->jobid = $wpdb->insert_id;
            }
        } catch (Exception $e) {
            $cu = new CommonUtils(0, $e->getMessage());
            $cu->fetchJson();
        };

    }

    /**
     * @param null $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    function jobStatus()
    {
        global $wpdb;
        $token = wp_get_session_token();

        if(is_admin()) {
            $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_jobs where token = '$token' ORDER BY `id` ASC");
        }else{
            $userid = get_current_user_id();
            $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_jobs where token = '$token' and where userid = '$userid' ORDER BY `id` ASC");
        }
        $finalResult = '';

        foreach ($results as $result) {
            $currentValue = sprintf('<div class="progress">
  <div class="progress-bar" role="progressbar" style="width: %d%%" aria-valuenow="%d" aria-valuemin="0" aria-valuemax="100"></div>
</div>', $result->percentage, $result->percentage);
            $finalResult = $result->description . $currentValue . $finalResult;
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

                $hostname = sanitize_text_field($this->data['hostname']);
                $username = sanitize_text_field($this->data['username']);
                $password = sanitize_text_field($this->data['password']);

                $token = 'Basic ' . base64_encode($username . ':' . $password);

                $cpm = new CyberPanelManager($this, $hostname, $username, $token);
                wp_send_json($cpm->VerifyConnection());

            } elseif ($this->function == 'jobStatus') {
                $this->jobStatus();
            }
        } catch (Exception $e) {
            $cu = new CommonUtils(0, $e->getMessage());
            $cu->fetchJson();
        }

    }

    function updateJobStatus($status, $percentage)
    {

        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . TN_CYBERPANEL_JOBS,
            array(
                'description' => $this->description,    // string
                'status' => $status,
                'percentage' => $percentage
            ),
            array('id' => $this->jobid),
            array(
                '%s',
                '%d',
                '%d'
            ),
            array('%d')
        );

    }
}