<?php


class CapabilityCheck
{
    protected $function;
    protected $data;

    function __construct($function, $data = null)
    {
        $this->function = $function;
        $this->data = $data;
    }

    function checkCapability(){

        if($this->function == 'saveSettings' || $this->function == 'jobStatus' || $this->function == 'cyberpanel_provider_html'
        || $this->function == 'connectProvider' || $this->function == 'fetchProviderPlans' || $this->function == 'fetchProviderAPIs'
        || $this->function == 'deleteAPIDetails'){
            if(current_user_can('manage_options'))
                return 1;
        }else if ($this->function == 'cancelNow' || $this->function == 'rebuildNow' || $this->function == 'serverActions' || $this->function == 'rebootNow'){
            if(current_user_can('manage_options'))
                return 1;
            else{
                $post = get_page_by_title(sanitize_text_field($this->data['serverID']),OBJECT, 'wpcp_server');
                CommonUtils::writeLogs(sprintf('Server whose access is being checked: %s', $this->data['serverID']), CPWP_ERROR_LOGS);
                if($post->post_author == get_current_user_id()){
                    return 1;
                }
            }
        }

        return 0;
    }

    function jobOwnerShipCheck($jobid){return 1;}

}