<?php

class WPCPHTTP
{
    static $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    static $productHTML = '<!-- wp:heading {"align":"center","level":4} -->
<h4 id="jobRunning" class="has-text-align-center"><strong><span class="has-inline-color has-luminous-vivid-orange-color"><img style="display: inline"  src="{loader}"> Functions unavailable while job is running on the server..</span></strong></h4>
<!-- /wp:heading -->
<!-- wp:html -->
<ul id="menu" class="horizontal gray">
    <li><a id="productHREF" href="#">{productLine}</a></li>
     <li id="myBtn" style="float:right; color:red"><a id="cancelHREF" href="#">
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
    <li id="rebuild" style="float:right"><a id="rebuildHREF" href="#">Rebuild
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
    <li id="reboot" style="float:right"><a id="rebootHREF" href="#">Reboot
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

    static $productHTMLManual = '<!-- wp:html -->
<ul id="menu" class="horizontal gray">
    <li><a id="productHREF" href="#">{productLine}</a></li>
     <li id="myBtn" style="float:right; color:red"><a id="cancelHREF" href="#">
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
</ul>
<!-- /wp:html -->';

    protected $job;
    protected $data;
    protected $url;
    protected $body;

    ### Added later to refactor code

    protected $orderid;
    protected $postIDServer;
    protected $token;
    protected $image;

    ## Global data array, could be emtpy or contain data

    protected $globalData;

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

    function setupTokenImagePostID(){

        $serverID = sanitize_text_field($this->data['serverID']);
        $page = get_page_by_title($serverID,OBJECT, 'wpcp_server'); // enter your page title
        $this->postIDServer = $page->ID;

        ## Get product id of this server.
        $product_id = get_post_meta($this->postIDServer, 'wpcp_productid', true);
        $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);

        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $this->token = json_decode($result->apidetails)->token;
        $this->image = json_decode($result->apidetails)->image;

    }

    function fetchImageTokenProvider(){

        $this->globalData['productID'] = $this->data->get_product_id();
        $this->globalData['order'] = wc_get_order($this->orderid);
        $product = wc_get_product( $this->globalData['productID'] );
        $this->globalData['productName'] = $product->get_title();
        $this->globalData['productPrice'] = $product->get_regular_price();
        $wpcp_provider = get_post_meta($this->globalData['productID'], WPCP_PROVIDER, true);
        $wpcp_providerplans = get_post_meta($this->globalData['productID'], WPCP_PROVIDERPLANS, true);

        $this->globalData['finalPlan'] = explode(',', $wpcp_providerplans)[0];
        $this->globalData['finalLocation'] = explode(',', $this->data->get_meta(WPCP_LOCATION, true, 'view'))[1];

        $message = sprintf('Backend provider for product %s is %s and provider is %s.', $this->globalData['productName'], $this->globalData['finalPlan'], $wpcp_provider);
        CommonUtils::writeLogs($message,CPWP_ERROR_LOGS);

        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cyberpanel_providers WHERE name = '$wpcp_provider'");

        $this->token = json_decode($result->apidetails)->token;
        $this->image = json_decode($result->apidetails)->image;

        $message = sprintf('Token for product %s is %s', $this->globalData['productName'], $this->token);
        CommonUtils::writeLogs($message, CPWP_ERROR_LOGS);
    }

    function serverPostProcessing(){
        ## Store the order as server post type

        $token = base64_encode('admin:' . $this->globalData['CyberPanelPassword']);

        $replacements = array(
            '{serverIP}' =>  $this->globalData['ipv4'],
            '{token}' =>  $token,
            '{productLine}' => $this->globalData['productName'] . ' - ' . $this->globalData['serverID'],
            '{serverID}' => $this->globalData['serverID'],
            '{orderDate}' => get_the_date("F j, Y, g:i a", $this->orderid),
            '{price}' => get_woocommerce_currency_symbol() . ' ' . $this->globalData['productPrice'],
            '{ipv4}' => $this->globalData['ipv4'],
            '{ipv6}' => $this->globalData['ipv6'],
            '{cores}' => $this->globalData['cores'],
            '{memory}' => $this->globalData['memory'],
            '{disk}' => $this->globalData['disk'],
            '{datacenter}' => $this->globalData['datacenter'],
            '{city}' => $this->globalData['city'],
            '{loader}' => CPWP_PLUGIN_DIR_URL . 'assets/images/loading.gif'
        );

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            WPCPHTTP::$productHTML);

        $my_post = array(
            'post_author' => $this->globalData['order']->user_id,
            'post_title'    => $this->globalData['serverID'],
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'wpcp_server',
        );

        $post_id = wp_insert_post( $my_post );

        $dueDate = new DateTime();
        $interval = new DateInterval(WPCP_INTERVAL);
        $dueDate->add($interval);

        add_post_meta( $post_id, WPCP_DUEDATE, (string) $dueDate->getTimestamp(), true );
        add_post_meta( $post_id, WPCP_ACTIVEINVOICE, 0, true );
        add_post_meta( $post_id, WPCP_ORDERID, $this->globalData['order']->id, true );
        add_post_meta( $post_id, WPCP_PRODUCTNAME, $this->globalData['productName'], true );
        add_post_meta( $post_id, WPCP_STATE, WPCP_ACTIVE, true );
        add_post_meta( $post_id, WPCP_TOKEN, $token, true );
        add_post_meta( $post_id, WPCP_PRODUCTID, $this->globalData['productID'], true );

        $this->globalData['order']->update_status('wc-completed');

        /// Send Email To Customer

        $replacements = array(
            '{FullName}' =>  $this->globalData['order']->get_billing_first_name() . ' ' . $this->globalData['order']->get_billing_last_name(),
            '{PlanName}' =>  $this->globalData['productName'],
            '{IPAddress}' => $this->globalData['ipv4'],
            '{RootPassword}' => $this->globalData['RootPassword'],
            '{IPAddressCP}' => $this->globalData['ipv4'],
            '{CPPassword}' => $this->globalData['CyberPanelPassword'],
        );

        $subject = sprintf('Managed CyberPanel service for Order# %s successfully activated.', $this->globalData['order']->id);

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            get_option(WPCP_NEW_SERVER, WPCPHTTP::$ServerDetails)
        );

        wp_mail($this->globalData['order']->get_billing_email(), $subject, $content);
    }

    ## Post cancellaltion

    function serverPostCancellation()
    {
        $post = array(
            'ID' => $this->postIDServer,
            'post_content' => WPCPHTTP::$cancelled,
        );

        wp_update_post($post, true);
        update_post_meta($this->postIDServer, WPCP_STATE, WPCP_TERMINATED);

        ### Send Termination Email

        $orderID= get_post_meta($this->postIDServer, WPCP_ORDERID, true);
        $order = wc_get_order($orderID);

        $replacements = array(
            '{FullName}' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            '{ServerID}' => sanitize_text_field($this->data['serverID'])
        );

        $subject = sprintf('Server with ID# %s cancelled.', sanitize_text_field($this->data['serverID']));

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            get_option(WPCP_SERVER_CANCELLED, WPCPHTTP::$ServerCancelled)
        );

        wp_mail($order->get_billing_email(), $subject, $content);

        ##


        $data = array(
            'status' => 1,
        );

        if( !wp_doing_cron() && $this->globalData['json'] == 1) {
            wp_send_json($data);
        }
    }

    ## Post actions

    function serverPostActions($provider = null){
        $finalData = '';
        $running = 0;

        if($provider == null) {

            foreach (array_reverse($this->globalData['actions']) as $action) {

                $finalData = $finalData . sprintf('<tr><td>%s</td><td>%s</td></tr>', $action->command, $action->status);

                if ($action->status == 'running') {
                    $running = 1;
                }

            }
        }
        elseif ($provider == 'DigitalOcean'){
            foreach ($this->globalData['actions'] as $action) {
                $finalData = $finalData . sprintf('<tr><td>%s</td><td>%s</td></tr>', $action->type, $action->status);
                if ($action->status == 'in-progress') {
                    $running = 1;
                }
            }
        }

        $data = array(
            'status' => 1,
            'result' => $finalData,
            'running' => $running
        );
        wp_send_json($data);
    }
}