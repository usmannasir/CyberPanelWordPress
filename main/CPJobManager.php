<?php

require_once(CPWP_PLUGIN_DIR . 'main/CyberPanelManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');
require_once(CPWP_PLUGIN_DIR . 'main/CyberPanelProvider.php');
require_once(CPWP_PLUGIN_DIR . 'main/Providers/ProviderHandler.php');

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
                        'userid' => get_current_user_id(),
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
            $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_jobs where token = '$token' ORDER BY `id` DESC");
        }else{
            $userid = get_current_user_id();
            $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_jobs where token = '$token' and where userid = '$userid' ORDER BY `id` DESC");
        }
        $tableHead = '<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Function</th>
      <th scope="col">Status</th>
      <th scope="col">State</th>
    </tr>
  </thead>
  <tbody>';
        $tableFooter = '</tbody></table>';
        $finalResult = $tableHead;

        foreach ($results as $result) {
            $id = sprintf('<tr><th scope="row">%d</th>', $result->id);
            $function = sprintf('<td scope="row">%s</td>', $result->function);
            if($result->percentage == 100 or $result->status != WPCP_JobRunning){
                $status = sprintf('<td scope="row">%s</td>', $result->description);
            }else{
                $status = sprintf('<td scope="row"><div class="progress">
  <div class="progress-bar" role="progressbar" style="width: %d%%" aria-valuenow="%d" aria-valuemin="0" aria-valuemax="100"></div>
</div></td>', $result->percentage, $result->percentage);
            }

            if($result->status == WPCP_StartingJob){
                $jobStatus = 'Job starter..';
            }elseif ($result->status == WPCP_JobFailed){
                $jobStatus = 'Job failed.';
            }
            elseif ($result->status == WPCP_JobSuccess){
                $jobStatus = 'Job successful.';
            }
            elseif ($result->status == WPCP_JobRunning){
                $jobStatus = 'Job running..';
            }
            $jobStatus = sprintf('<td scope="row">%s</td>', $jobStatus);

            $finalResult = $finalResult . $id . $function . $status . $jobStatus;
        }

        $finalResult = $finalResult . $tableFooter;

        $data = array(
            'status' => 1,
            'result' => $finalResult
        );
        wp_send_json($data);
    }

    function RunJob()
    {
        try {
            if ($this->function == 'saveSettings') {

                $invoice = (string) sanitize_text_field($this->data['invoice']);
                $autoSuspend = (string) sanitize_text_field($this->data['autoSuspend']);
                $autoTerminate = (string) sanitize_text_field($this->data['autoTerminate']);

                update_option('wpcp_invoice', $invoice, 'no');
                update_option('wpcp_auto_suspend', $autoSuspend, 'no');
                update_option('wpcp_terminate', $autoTerminate, 'no');

                $this->setDescription('Site wide settings configured.');
                $this->updateJobStatus(WPCP_JobSuccess, 100);

                wp_send_json(json_encode(array('status' => 1)));

            }
            elseif ($this->function == 'connectProvider') {
                $cpp = new CyberPanelProvider($this, $this->data);
                wp_send_json($cpp->connectProvider());
            }
            elseif ($this->function == 'fetchProviderAPIs') {
                $cpp = new CyberPanelProvider($this, $this->data);
                $cpp->fetchProviderAPIs();
            }
            elseif ($this->function == 'deleteAPIDetails') {
                $cpp = new CyberPanelProvider($this, $this->data);
                $cpp->deleteAPIDetails();
            }
            elseif ($this->function == 'createServer') {
                $cpp = new ProviderHandler($this, $this->data);
                $cpp->createServer();
            }
            elseif ($this->function == 'cancelNow') {
                return 0;
                $cpp = new ProviderHandler($this, $this->data);
                $cpp->cancelNow();
            }
            elseif ($this->function == 'shutDown') {
                return 0;
                $cpp = new ProviderHandler($this, $this->data);
                $cpp->shutDown();
            }
            elseif ($this->function == 'rebuildNow') {
                $cpp = new ProviderHandler($this, $this->data);
                $cpp->rebuildNow();
            }
            elseif ($this->function == 'serverActions') {
                $cpp = new ProviderHandler($this, $this->data);
                $cpp->serverActions();
            }
            elseif ($this->function == 'rebootNow') {
                $cpp = new ProviderHandler($this, $this->data);
                $cpp->rebootNow();
            }
            elseif ($this->function == 'fetchProviderPlans') {
                $cpp = new ProviderHandler($this, $this->data);
                wp_send_json($cpp->fetchPlans());
            }
            elseif ($this->function == 'fetchLocations') {
                $cpp = new ProviderHandler($this, $this->data);
                return $cpp->fetchLocations();
            }
            elseif ($this->function == 'jobStatus') {
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