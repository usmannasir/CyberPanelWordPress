<?php

class WPCPHTTP
{
    static $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    static $productHTML = '<!-- wp:heading {"align":"center","level":4} -->
<h4 id="jobRunning" class="has-text-align-center"><strong><span class="has-inline-color has-luminous-vivid-orange-color">Functions unavailable while job is running on the server..</span></strong></h4>
<!-- /wp:heading -->
<!-- wp:html -->
<ul id="menu" class="horizontal gray">
    <li><a href="javascript:void(0)">{productLine}</a></li>
    <li class="loader"><a href="javascript:void(0)"><img style="display: inline"  src="{loader}"></a></li>
     <li id="myBtn" style="float:right; color:red"><a href="#">
     Cancel
<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
  <div class="modal-body">
    <p>Are you sure you want to cancel <span id="serverID">{serverID}</span>?</p>
    <button type="button" id="cancelNow">Cancel Now <img style="display: inline" class="loader" src="{loader}"> </button>
  </div>
  </div>
</div>
     </a></li>
    <li id="rebuild" style="float:right"><a href="#">Rebuild
    <!-- The Modal -->
<div id="rebuildModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
  <div class="modal-body">
    <p>Are you sure you want to rebuild <span id="serverID">{serverID}</span>? You will loose everything on this server.</p>
    <button type="button" id="rebuildNow">Rebuild Now <img style="display: inline" class="loader" src="{loader}"> </button>
  </div>
  </div>
</div>
    </a></li>
    <li id="reboot" style="float:right"><a href="#">Reboot
    <!-- The Modal -->
<div id="rebootModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
  <div class="modal-body">
    <p>Are you sure you want to reboot? <span id="serverID">{serverID}</span></p>
    <button type="button" id="rebootNow">Reboot Now <img style="display: inline" class="loader" src="{loader}"> </button>
  </div>
  </div>
</div>
    </a></li>
    <li style="float:right"><a target="_blank" href="https://{serverIP}:8090/cloudAPI/access?token={token}&serverUserName=admin">Access CyberPanel</a></li>
    <li class="rightli" style="float:right"><a href="#">Manage</a></li>
</ul>
<!-- /wp:html -->

<!-- wp:columns -->
<div id="col1" class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:html -->
        <div class="WPCPBoxed">
            <h3>Registration Date</h3>
            <p>{orderDate}</p>
            <div style="float:left">
                <h4>Recurring Charges</h4>
                <p>{price}</p>
            </div>
            <div style="float:left; margin-left: 5%">
                <h4>State</h4>
                <p>Active</p>
            </div>
        </div>
        <!-- /wp:html --></div>
    <!-- /wp:column -->

    <!-- wp:column -->
    <div class="wp-block-column"><!-- wp:table {"backgroundColor":"subtle-pale-green","className":"is-style-regular"} -->
        <figure class="wp-block-table is-style-regular"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>IPv4 Address</td><td>{ipv4}</td></tr><tr><td>IPv6 Address</td><td>{ipv6}</td></tr><tr><td>Data center</td><td>{datacenter}</td></tr><tr><td>City</td><td>{city}</td></tr></tbody></table></figure>
        <!-- /wp:table --></div>
    <!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div id="col2" class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:heading {"level":3} -->
        <h3>Server Specs</h3>
        <!-- /wp:heading -->

        <!-- wp:table {"backgroundColor":"subtle-pale-green"} -->
        <figure class="wp-block-table"><table class="has-subtle-pale-green-background-color has-background"><tbody><tr><td>CPU Cores</td><td>{cores}</td></tr><tr><td>Memory</td><td>{memory}</td></tr><tr><td>Disk</td><td>{disk}</td></tr></tbody></table></figure>
        <!-- /wp:table -->

        <!-- wp:paragraph -->
        <p></p>
        <!-- /wp:paragraph --></div>
    <!-- /wp:column -->

    <!-- wp:column -->
    <div class="wp-block-column"><!-- wp:heading {"level":3} -->
        <h3>Server Activities</h3>
        <!-- /wp:heading -->

        <!-- wp:table {"backgroundColor":"subtle-pale-green"} -->
        <figure class="wp-block-table"><table class="has-subtle-pale-green-background-color has-background">
        <tbody id="serverActions">
        </tbody>
        </table>
        </figure>
        <!-- /wp:table --></div>
    <!-- /wp:column --></div>
<!-- /wp:columns -->';

    static $cancelled = '<!-- wp:heading {"align":"center"} -->
<h2 class="has-text-align-center"><span class="has-inline-color has-vivid-red-color"><strong>This service is cancelled.</strong></span></h2>
<!-- /wp:heading -->';


    static $ServerDetails = 'Hello {FullName} !

{PlanName} has been successfully activated.

SSH Credentials:

Server IP: {IPAddress}
Username: root
Password: {RootPassword}

You can manage your service at:

https://{IPAddressCP}:8090
User Name: admin
Password: {CPPassword}

Kind Regards';

    static $ServerCancelled = 'Hello {FullName} !

{ServerID} has been successfully cancelled.

Kind Regards';

    static $ServerSuspended = 'Hello {FullName} !

{ServerID} has been successfully suspended. Suspension reason: {Reason}

Kind Regards';

    static $ServerTerminated = 'Hello {FullName} !

{ServerID} has been successfully terminated.

Kind Regards';



    protected $job;
    protected $data;
    protected $url;
    protected $body;

    function HTTPPostCall($token, $method = null, $body = 1)
    {

        $headers = array(
            'Authorization' => $token,
            'Content-type' => 'application/json'
        );

        if ($method == null) {
            if ($body) {
                $args = array(
                    'body' => json_encode($this->body),
                    'timeout' => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'cookies' => array(),
                    'sslverify' => false
                );
            }else{
                $args = array(
                    'timeout' => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => $headers,
                    'cookies' => array(),
                    'sslverify' => false
                );
            }
            return wp_remote_post($this->url, $args);
        } else if ($method == 'GET') {
            $args = array(
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'cookies' => array(),
                'sslverify' => false
            );
            return wp_remote_get($this->url, $args);
        } else if ($method == 'DELETE') {
            $args = array(
                'method' => 'DELETE',
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'cookies' => array(),
                'sslverify' => false
            );
            return wp_remote_request($this->url, $args);
        }


    }
}