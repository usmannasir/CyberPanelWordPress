<?php

require_once(CPWP_PLUGIN_DIR . 'main/CPJobManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');
require_once(CPWP_PLUGIN_DIR . 'main/CommonUtils.php');
require_once(CPWP_PLUGIN_DIR . 'main/WPCPHTTP.php');

/// Load all required JS and CSS files for this plugin

function CPWP_load_static()
{

    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', 'jQuery');
    wp_enqueue_style('CPCSS', CPWP_PLUGIN_DIR_URL . 'assets/css/cyberpanel.css');
    wp_enqueue_script('CPJS', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel.js', 'jQuery');

    $title_nonce = wp_create_nonce('CPWP');

    wp_localize_script('CPJS', 'CPWP', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => $title_nonce,
    ));
}

add_action('admin_enqueue_scripts', 'CPWP_load_static');

// Load scripts js for user end

function CPWP_load_static_frontend()
{

    wp_enqueue_style('CPCSSFE', CPWP_PLUGIN_DIR_URL . 'assets/css/cyberpanel-frontend.css');
    wp_enqueue_script('CPJSFE', CPWP_PLUGIN_DIR_URL . 'assets/js/cyberpanel-frontend.js', array('jquery'));

    $title_nonce = wp_create_nonce('CPWP');

    wp_localize_script('CPJSFE', 'CPWP', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => $title_nonce,
    ));
}

add_action('wp_enqueue_scripts', 'CPWP_load_static_frontend');

///

function Main_CyberPanel()
{
    add_menu_page(
        'CyberPanel', //Page Title
        'CyberPanel', //Menu Title
        'manage_options', //Capability
        'cyberpanel', //Page slug
        'cyberpanel_main_html' //Callback to print html
    );
}

function cyberpanel_main_html()
{

    $cc = new CapabilityCheck('cyberpanel_main_html');
    if (!$cc->checkCapability()) {
        return;
    }

    include(CPWP_PLUGIN_DIR . 'views/connect-server.php');

}

add_action('admin_menu', 'Main_CyberPanel');

// Add the emails page

function Main_CyberPanel_Emails()
{

    add_submenu_page("cyberpanel", "Configure Emails",
        "Email Templates", "manage_options", "wpcp-emails"
        , "cyberpanel_main_emails_html"
    );

}

function cyberpanel_main_emails_html()
{

    $cc = new CapabilityCheck('cyberpanel_main_emails_html');
    if (!$cc->checkCapability()) {
        return;
    }

    include(CPWP_PLUGIN_DIR . 'views/emails.php');

}

add_action('admin_menu', 'Main_CyberPanel_Emails');

//// Ajax handler

add_action('wp_ajax_saveSettings', 'ajax_saveSettings');

function ajax_saveSettings()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('saveSettings');
    if (!$cc->checkCapability()) {
        return;
    }

    $message = 'Saving sitewide CyberPanel settings.';

    check_ajax_referer('CPWP');
    $cpjm = new CPJobManager('saveSettings', $_POST, $message);
    $cpjm->RunJob();
}

// Ajax fetch email settings

add_action('wp_ajax_fetchTemplateContent', 'ajax_fetchTemplateContent');

function ajax_fetchTemplateContent()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchTemplateContent');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $templateName = sanitize_text_field($_POST['templateName']);

    if ($templateName == 'New Server Created') {
        $content = get_option(WPCP_NEW_SERVER, WPCPHTTP::$ServerDetails);
    } elseif ($templateName == 'Server Cancelled') {
        $content = get_option(WPCP_SERVER_CANCELLED, WPCPHTTP::$ServerCancelled);
    } elseif ($templateName == 'Server Suspended') {
        $content = get_option(WPCP_SERVER_SUSPENDED, WPCPHTTP::$ServerSuspended);
    } elseif ($templateName == 'Server Terminated') {
        $content = get_option(WPCP_SERVER_TERMINATED, WPCPHTTP::$ServerTerminated);
    }

    wp_send_json(array('status' => 1, 'result' => nl2br($content)));
}

add_action('wp_ajax_saveTemplate', 'ajax_saveTemplate');

function ajax_saveTemplate()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchTemplateContent');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $templateName = sanitize_text_field($_POST['templateName']);
    //$templateContent = sanitize_text_field($_POST['templateContent']);
    $templateContent = $_POST['templateContent'];

    //$breaks = array("<br />","<br>","<br/>", '\n\n');

    //$templateContent = str_ireplace($breaks, "\r\n", $templateContent);

    if ($templateName == 'New Server Created') {
        update_option(WPCP_NEW_SERVER, $templateContent, 'no');
    } elseif ($templateName == 'Server Cancelled') {
        update_option(WPCP_SERVER_CANCELLED, $templateContent, 'no');
    } elseif ($templateName == 'Server Suspended') {
        update_option(WPCP_SERVER_SUSPENDED, $templateContent, 'no');
    } elseif ($templateName == 'Server Terminated') {
        update_option(WPCP_SERVER_TERMINATED, $templateContent, 'no');
    }

    wp_send_json(array('status' => 1));
}

//// Ajax to fetch job status

add_action('wp_ajax_jobStatus', 'ajax_jobStatus');

function ajax_jobStatus()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('jobStatus');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('jobStatus', $_POST);
    $cpjm->RunJob();
}

// Proviers page html

function cyberpanel_provider_html()
{

    $cc = new CapabilityCheck('cyberpanel_provider_html');
    if (!$cc->checkCapability()) {
        return;
    }

    include(CPWP_PLUGIN_DIR . 'views/providers.php');
}

function CyberPanel_Providers()
{
    add_submenu_page("cyberpanel", "Configure Providers",
        "Cloud Providers", "manage_options", "cyberpanel-providers"
        , "cyberpanel_provider_html"
    );
}

add_action('admin_menu', 'CyberPanel_Providers');

// Ajax for providers

add_action('wp_ajax_connectProvider', 'ajax_connectProvider');

function ajax_connectProvider()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('connectProvider');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('connectProvider', $_POST, sprintf('Configuring %s account named: %s..', sanitize_text_field($_POST['provider']), sanitize_text_field($_POST['name'])));
    $cpjm->RunJob();
}

/// Create meta box to disable for woocommerce posts

/* Fire our meta box setup function on the post editor screen. */

function wpcp_add_custom_box()
{
    add_meta_box(
        'wpcp_box_id',           // Unique ID
        'Configure Backend Package for this product.',  // Box title
        'wpcp_custom_box_html',  // Content callback, must be of type callable
        'product'                   // Post type
    );

    add_meta_box(
        'wpcp_state_box',           // Unique ID
        'Server State',  // Box title
        'wpcp_custom_box_state_html',  // Content callback, must be of type callable
        'wpcp_server'                   // Post type
    );

    add_meta_box(
        'wpcp_invoice_box',           // Unique ID
        'Invoices',  // Box title
        'wpcp_custom_box_invoices_html',  // Content callback, must be of type callable
        'wpcp_server'                   // Post type
    );
}

add_action('add_meta_boxes', 'wpcp_add_custom_box');

function wpcp_custom_box_html($post)
{
    global $wpdb;
    $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_providers");
    $wpcp_provider = get_post_meta($post->ID, WPCP_PROVIDER, true);
    $wpcp_providerplan = get_post_meta($post->ID, WPCP_PROVIDERPLANS, true);

    CommonUtils::writeLogs(sprintf('WPCP_CUSTOM_BOX POSTID: %s', $post->ID), CPWP_ERROR_LOGS);
    CommonUtils::writeLogs(sprintf('WPCP_CUSTOM_BOX wpcp_provider: %s', $wpcp_provider), CPWP_ERROR_LOGS);
    CommonUtils::writeLogs(sprintf('WPCP_CUSTOM_BOX wpcp_providerplan: %s', $wpcp_providerplan), CPWP_ERROR_LOGS);

    ?>

    <div id="shipping_product_data" class="panel woocommerce_options_panel hidden" style="display: block;">
        <div class="options_group">
            <p class="form-field shipping_class_field">
                <label for="product_shipping_class">Select Provider</label>
                <select name="wpcp_provider" id="wpcp_provider" class="select short">
                    <option>Select</option>
                    <?php
                    foreach ($results as $result) {
                        echo sprintf('<option>%s</option>', $result->name);
                    } ?>
                </select><span class="description">Current Provider: <?php echo $wpcp_provider ?></span>
            </p>
            <div id="WPCPSpinner" class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div id="wpcp_providerplans_label" class="options_group">
            <p class="form-field shipping_class_field">
                <label for="product_shipping_class">Select Plan</label>
                <select name="wpcp_providerplans" id="wpcp_providerplans" class="select short">
                </select><span class="description">Current package:  <?php echo $wpcp_providerplan ?></span>
            </p>
        </div>
    </div>

    <?php

}

function wpcp_custom_box_state_html($post)
{
    global $wpdb;
    $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_providers");

    $state = get_post_meta($post->ID, WPCP_STATE, true);

    if ($state == WPCP_ACTIVE)
        $state = 'ACTIVE';
    elseif ($state == WPCP_SUSPENDED)
        $state = 'SUSPENDED';
    elseif ($state == WPCP_CANCELLED)
        $state = 'CANCELLED';
    else
        $state = 'TERMINATED';

    ?>

    <label class="screen-reader-text" for="post_author_override">Author</label>
    <select name="wpcp_server_state" id="wpcp_server_state" class="">
        <option value="1">Active</option>
        <option value="2">Suspended</option>
        <option value="2">Cancelled</option>
        <option value="2">Terminated</option>
    </select>
    </select><span class="description">Current State: <?php echo $state ?></span>
    <?php

}

function wpcp_custom_box_invoices_html($post)
{
    $dueDate = new DateTime();
    $orders = wc_get_orders(array(
        'limit' => -1, // Query all orders
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    ?>
    <label for="order_date">Date created:</label>
    <input type="text" class="date-picker hasDatepicker" name="order_date" maxlength="10" value="2020-07-27" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" id="dp1595870973781">
    <table style="width:100%">
        <tr><th>Next invoice in</th><td><?php echo human_time_diff($dueDate->getTimestamp(), (int) get_post_meta($post->ID, WPCP_DUEDATE, true)) ?></td></tr>
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Status</th>
        </tr>
        <?php

        foreach ($orders as $order) {
            if ((int)get_post_meta($order->id, WPCP_INVOICESERVER, true) == $post->ID) {
                echo sprintf('<tr>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
        </tr>', $order->id, get_woocommerce_currency_symbol() . $order->get_total(), $order->get_status());
            }
        }

        ?>

    </table>
    <?php

}

// Ajax for fetching provider plans

add_action('wp_ajax_fetchProviderPlans', 'ajax_fetchProviderPlans');

function ajax_fetchProviderPlans()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchProviderPlans');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('fetchProviderPlans', $_POST);
    $cpjm->RunJob();
}

// Save data from product meta

function wpcp_save_postdata($post_id)
{
    if (array_key_exists('wpcp_providerplans', $_POST)) {

        $wpcp_provider = sanitize_text_field($_POST['wpcp_provider']);
        $wpcp_providerplans = sanitize_text_field($_POST['wpcp_providerplans']);

        update_post_meta(
            $post_id,
            WPCP_PROVIDER,
            $wpcp_provider
        );
        update_post_meta(
            $post_id,
            WPCP_PROVIDERPLANS,
            $wpcp_providerplans
        );
    }

    if (array_key_exists('wpcp_server_state', $_POST)) {

        $state = sanitize_text_field($_POST['wpcp_server_state']);

        if ($state == 'Active')
            $state = WPCP_ACTIVE;
        elseif ($state == 'Suspended')
            $state = WPCP_SUSPENDED;
        elseif ($state == 'Cancelled')
            $state = WPCP_CANCELLED;
        elseif ($state == 'Terminated')
            $state = WPCP_TERMINATED;

        update_post_meta(
            $post_id,
            WPCP_STATE,
            $state
        );
    }
}

add_action('save_post', 'wpcp_save_postdata');

// On woocomm order complete

add_action('woocommerce_payment_complete', 'so_payment_complete');

function so_payment_complete($order_id)
{
//    error_log('woocommerce_payment_complete', 3, CPWP_ERROR_LOGS);
//    $order = wc_get_order($order_id);
//    $billingEmail = $order->billing_email;
//    $products = $order->get_items();
//
//    foreach ($products as $prod) {
//        error_log($prod['product_id'], 3, CPWP_ERROR_LOGS);
//    }
}

add_action('woocommerce_order_status_changed', 'woocommerce_payment_complete_order_status', 10, 3);
function woocommerce_payment_complete_order_status($order_id)
{
    $order = wc_get_order($order_id);

    $wpcp_invoice = get_post_meta($order->id, WPCP_INVOICE, true);

    if ($wpcp_invoice != 'yes') {

        CommonUtils::writeLogs(sprintf('Order status: %s', $order->data['status']), CPWP_ERROR_LOGS);

        if ($order->data['status'] == 'processing') {

            $message = sprintf('Processing order %s', $order_id);
            $cpjm = new CPJobManager('createServer', $order_id, $message);
            $cpjm->RunJob();

        }

    } else {
        if ($order->data['status'] == 'processing') {
            $server_post_id = get_post_meta($order->id, WPCP_INVOICESERVER, true);
            $data = array('serverID' => get_the_title($server_post_id), 'json' => 0);
            update_post_meta($server_post_id, WPCP_STATE, WPCP_ACTIVE);
            update_post_meta($server_post_id, WPCP_ACTIVEINVOICE, 0, true);
            delete_post_meta($server_post_id, WPCP_PAYMENTID);
            $order->update_status('wc-completed');
            $cpjm = new CPJobManager('rebootNow', $data);
            $cpjm->RunJob();
        }elseif ($order->data['status'] == 'cancelled'){

            $server_post_id = get_post_meta($order->id, WPCP_INVOICESERVER, true);
            $data = array('serverID' => get_the_title($server_post_id), 'json' => 0);
            update_post_meta($server_post_id, WPCP_STATE, WPCP_CANCELLED);
            update_post_meta($server_post_id, WPCP_ACTIVEINVOICE, 0, true);
            delete_post_meta($server_post_id, WPCP_PAYMENTID);
            $order->update_status('wc-cancelled');
            $cpjm = new CPJobManager('cancelNow', $data);
            $cpjm->RunJob();

        }
    }
}

// Register CyberPanel Servers Post Type

function wpcp_custom_post_type()
{
    register_post_type('wpcp_server',
        array(
            'labels' => array(
                'name' => __('Servers', 'textdomain'),
                'singular_name' => __('Server', 'textdomain'),
            ),
            'public' => true,
            'has_archive' => false,
            'delete_with_user' => false,
            "supports" => array("customer", "author"),
        )
    );
}

add_action('init', 'wpcp_custom_post_type');

//

add_action('wp_ajax_fetchProviderAPIs', 'ajax_fetchProviderAPIs');

function ajax_fetchProviderAPIs()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchProviderAPIs');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('fetchProviderAPIs', $_POST);
    $cpjm->RunJob();
}

add_action('wp_ajax_deleteAPIDetails', 'ajax_deleteAPIDetails');

function ajax_deleteAPIDetails()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('deleteAPIDetails');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('deleteAPIDetails', $_POST);
    $cpjm->RunJob();
}

add_action('wp_ajax_cancelNow', 'ajax_cancelNow');

function ajax_cancelNow()
{
    // Handle the ajax request
    $cc = new CapabilityCheck('cancelNow', $_POST);
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $_POST['json'] = 1;
    $cpjm = new CPJobManager('cancelNow', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_rebuildNow', 'ajax_rebuildNow');

function ajax_rebuildNow()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('rebuildNow', $_POST);
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('rebuildNow', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_serverActions', 'ajax_serverActions');

function ajax_serverActions()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('serverActions', $_POST);
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('serverActions', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_rebootNow', 'ajax_rebootNow');

function ajax_rebootNow()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('rebootNow', $_POST);
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $_POST['json'] = 1;
    $cpjm = new CPJobManager('rebootNow', $_POST);
    $cpjm->RunJob();

}

add_filter('the_content', 'filter_the_content_in_the_main_loop', 1);

function filter_the_content_in_the_main_loop($content)
{
    $current_user = wp_get_current_user();
    $post = get_post();

    if (get_post_type($post->id) == 'wpcp_server') {
        if ($current_user->id == $post->post_author || current_user_can('manage_options')) {
            $state = get_post_meta($post->ID, WPCP_STATE, true);
            if ($state == WPCP_ACTIVE)
                return $content;
            else {
                if ($state == WPCP_ACTIVE)
                    $state = 'ACTIVE';
                elseif ($state == WPCP_SUSPENDED)
                    $state = 'SUSPENDED';
                elseif ($state == WPCP_CANCELLED)
                    $state = 'CANCELLED';
                else
                    $state = 'TERMINATED';

                if ($state == WPCP_ACTIVE)
                    return $content;

                $content = sprintf('Server is currently %s. Kindly check if you have any pending invoices or contact support if this is a mistake', $state);
                return $content;

            }
        } else {
            return "You are not allowed to manage this server.";
        }
    }

    return $content;
}

remove_filter('the_content', 'filter_the_content_in_the_main_loop');

function wpcp_cron_exec()
{
    CommonUtils::RunCron();
}

add_action('wpcp_croncp_hook', 'wpcp_cron_exec');

if (!wp_next_scheduled('wpcp_croncp_hook')) {
    wp_schedule_event(time(), 'daily', 'wpcp_croncp_hook');
}

/**

 * Add the text field as item data to the cart object
 * @param Array $cart_item_data Cart item meta data.
 * @param Integer $product_id Product ID.
 * @param Integer $variation_id Variation ID.
 * @param Boolean $quantity Quantity
 * @since 1.0.0

 */

function wpcp_add_custom_field_item_data($cart_item_data, $product_id, $variation_id, $quantity)
{
    if (!empty($_POST['wpcp_location'])) {
        // Add the item data
        $cart_item_data['wpcp_location'] = $_POST['wpcp_location'];
    }
    return $cart_item_data;
}

add_filter('woocommerce_add_cart_item_data', 'wpcp_add_custom_field_item_data', 10, 4);

/**
 * Add custom field to order object
 */
function wpcp_add_custom_data_to_order($item, $cart_item_key, $values, $order)
{
    foreach ($item as $cart_item_key => $values) {
        if (isset($values['wpcp_location'])) {
            $item->add_meta_data(__('wpcp_location', 'wpcp'), $values['wpcp_location'], true);
        }
    }
}

add_action('woocommerce_checkout_create_order_line_item', 'wpcp_add_custom_data_to_order', 10, 4);

/**
 * Display custom field on the front end
 * @since 1.0.0
 */
function wpcp_display_custom_field_locations()
{
    global $post;

    $data = array(WPCP_PROVIDER => get_post_meta($post->ID, WPCP_PROVIDER, true));

    if($data[WPCP_PROVIDER] != '') {
        $cpjm = new CPJobManager('fetchLocations', $data);
        $locations = $cpjm->RunJob();
        printf('
<div class="WPCPLocationDIV">
<label for="wpcp_location">Select Location</label>
<select id="wpcp_location" name="wpcp_location">
    %s
</select>
</div>
', $locations);
    }
}

add_action('woocommerce_before_add_to_cart_button', 'wpcp_display_custom_field_locations');
//add_action('woocommerce_loop_add_to_cart_link', 'wpcp_display_custom_field_locations');

function wpcp_validate_custom_field($passed, $product_id, $quantity)
{
    $wpcp_provider = get_post_meta($product_id, 'wpcp_provider', true);

    CommonUtils::writeLogs(sprintf('Value of provider: %s', $wpcp_provider), CPWP_ERROR_LOGS);

    if($wpcp_provider != '') {
        if (empty($_POST['wpcp_location'])) {
            // Fails validation
            $passed = false;
            wc_add_notice(__('Please select location from product page before ordering.', 'wpcp'), 'error');
        }
        return $passed;
    }
    return $passed;
}

add_filter('woocommerce_add_to_cart_validation', 'wpcp_validate_custom_field', 10, 3);

/* */

add_shortcode('wpcpservers', 'wpcp_servers_fetch');
function wpcp_servers_fetch($atts = [], $content = null)
{
    if (is_user_logged_in()) {
        $userID = get_current_user_id();

        if (current_user_can('manage_options')) {
            $query = new WP_Query(array(
                'post_type' => 'wpcp_server',
                'post_status' => 'publish',
                'posts_per_page' => -1
            ));
        } else {
            $query = new WP_Query(array(
                'post_type' => 'wpcp_server',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'author__in' => array($userID),
            ));
        }

        $finalData = '';

        while ($query->have_posts()) {

            $query->the_post();
            $postTitle = get_the_title();
            $date = get_the_date("F j, Y, g:i a", get_the_id());
            $link = get_the_permalink();

            $productName = get_post_meta(get_the_id(), WPCP_PRODUCTNAME, true);
            $state = get_post_meta(get_the_id(), WPCP_STATE, true);

            if ($state == WPCP_ACTIVE)
                $state = 'ACTIVE';
            elseif ($state == WPCP_SUSPENDED)
                $state = 'SUSPENDED';
            elseif ($state == WPCP_CANCELLED)
                $state = 'CANCELLED';
            else
                $state = 'TERMINATED';

            $finalData = $finalData . sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><a href="%s">View</a></td></tr>', $postTitle, $date, $state, $productName, $link);

        }

        wp_reset_query();
        $content = sprintf('<!-- wp:table -->
<figure class="wp-block-table">
<table>
<thead>
<tr><th>ID</th><th>Date</th><th>Status</th><th>Product</th><th>Manage</th></tr>
</thead>
<tbody>
%s
</tbody>
</table>
</figure>
<!-- /wp:table -->', $finalData);
        return $content;
    }

    return 'You must be logged in to view this page';
}

function wpcp_show_monthly( $price ) {
    $price .= ' per month';
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'wpcp_show_monthly' );
add_filter( 'woocommerce_cart_item_price', 'wpcp_show_monthly' );