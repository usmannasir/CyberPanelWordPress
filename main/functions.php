<?php

require_once(CPWP_PLUGIN_DIR . 'main/CPJobManager.php');
require_once(CPWP_PLUGIN_DIR . 'main/CapabilityCheck.php');

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

// This function will generate HTML for Main Screen

function cyberpanel_main_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_main_html');
    if (!$cc->checkCapability()) {
        return;
    }

    include(CPWP_PLUGIN_DIR . 'views/connect-server.php');

}

add_action('admin_menu', 'Main_CyberPanel');

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
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    $cc = new CapabilityCheck('cyberpanel_hetzner_html');
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
    $screens = ['product'];
    foreach ($screens as $screen) {
        add_meta_box(
            'wpcp_box_id',           // Unique ID
            'Configure Backend Package for this product.',  // Box title
            'wpcp_custom_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}

add_action('add_meta_boxes', 'wpcp_add_custom_box');

function wpcp_custom_box_html($post)
{
    global $wpdb;
    $results = $wpdb->get_results("select * from {$wpdb->prefix}cyberpanel_providers");
    $wpcp_provider = get_post_meta($post->ID, WPCP_PROVIDER, true);
    $wpcp_providerplan = get_post_meta($post->ID, WPCP_PROVIDERPLANS, true);

    error_log(sprintf('WPCP_CUSTOM_BOX POSTID: %s', $post->ID), 3, CPWP_ERROR_LOGS);
    error_log(sprintf('WPCP_CUSTOM_BOX wpcp_provider: %s', $wpcp_provider), 3, CPWP_ERROR_LOGS);
    error_log(sprintf('WPCP_CUSTOM_BOX wpcp_providerplan: %s', $wpcp_providerplan), 3, CPWP_ERROR_LOGS);

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
            'wpcp_provider',
            $wpcp_provider
        );
        update_post_meta(
            $post_id,
            'wpcp_providerplans',
            $wpcp_providerplans
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

        error_log(sprintf('Order status: %s', $order->data['status']), 3, CPWP_ERROR_LOGS);

        if ($order->data['status'] == 'processing') {
            $message = sprintf('Processing order %s', $order_id);
            $cpjm = new CPJobManager('createServer', $order_id, $message);
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
            "supports" => array("title", "editor", "author", "customer"),
            'delete_with_user' => false,
            //'capability_type' => 'product'
        )
    );
}

add_action('init', 'wpcp_custom_post_type');

//

add_action('wp_ajax_fetchProviderAPIs', 'ajax_fetchProviderAPIs');

function ajax_fetchProviderAPIs()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('fetchProviderPlans');
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

    $cc = new CapabilityCheck('fetchProviderPlans');
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

    $cc = new CapabilityCheck('cancelNow');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('cancelNow', $_POST);
    $cpjm->RunJob();

}

add_action('wp_ajax_rebuildNow', 'ajax_rebuildNow');

function ajax_rebuildNow()
{
    // Handle the ajax request

    $cc = new CapabilityCheck('rebuildNow');
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

    $cc = new CapabilityCheck('serverActions');
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

    $cc = new CapabilityCheck('rebootNow');
    if (!$cc->checkCapability()) {
        return;
    }

    check_ajax_referer('CPWP');

    $cpjm = new CPJobManager('rebootNow', $_POST);
    $cpjm->RunJob();

}

/*add_filter('the_content', 'filter_the_content_in_the_main_loop', 1);

function filter_the_content_in_the_main_loop($content)
{
    $current_user = wp_get_current_user();
    $post = get_post();

    if (get_post_type($post->id) == 'wpcp_server') {
        if($current_user->id == $post->post_author){
            return $content;
        }else{
            return "You are not allowed to manage this server.";
        }
    }

    return $content;
}*/

remove_filter('the_content', 'filter_the_content_in_the_main_loop');

function wpcp_cron_exec()
{

//    $query = new WP_Query(array(
//        'post_type' => 'wpcp_server',
//        'post_status' => 'publish',
//        'posts_per_page' => -1
//    ));
//
//    while ($query->have_posts()) {
//
//        $query->the_post();
//        $post_id = get_the_ID();
//
//        $wpcp_productid = get_post_meta($post_id, WPCP_PRODUCTID, true);
//        $wpcp_duedate = (int)get_post_meta($post_id, WPCP_DUEDATE, true);
//        $wpcp_activeinvoice = get_post_meta($post_id, WPCP_ACTIVEINVOICE, true);
//        $wpcp_orderid = get_post_meta($post_id, WPCP_ORDERID, true);
//
//        $now = new DateTime();
//        $diff = $wpcp_duedate - $now->getTimestamp();
//        $autoInvoice = (int)get_option(WPCP_INVOICE, '14') * 86400;
//
//        error_log(sprintf('WPCP ID: %s. wpcp_productid: %s. wpcp_duedate: %d. wpcp_activeinvoice: %d. wpcp_orderid: %s  ', $post_id, $wpcp_productid, $wpcp_duedate, $wpcp_activeinvoice, $wpcp_orderid), 3, CPWP_ERROR_LOGS);
//
//        if (!$wpcp_activeinvoice) {
//
//            if ($diff <= $autoInvoice) {
//
//                update_post_meta($post_id, WPCP_DUEDATE, (string)$now->getTimestamp());
//                $order = wc_get_order($wpcp_orderid);
//
//                $address = array(
//                    'first_name' => $order->get_billing_first_name(),
//                    'last_name' => $order->get_billing_last_name(),
//                    'company' => $order->get_billing_company(),
//                    'email' => $order->get_billing_email(),
//                    'phone' => $order->get_billing_phone(),
//                    'address_1' => $order->get_billing_address_1(),
//                    'address_2' => $order->get_billing_address_2(),
//                    'city' => $order->get_billing_city(),
//                    'state' => $order->get_billing_state(),
//                    'postcode' => $order->get_billing_postcode(),
//                    'country' => $order->get_billing_country(),
//                );
//
//                // Now we create the order
//                $nOrder = wc_create_order(array('customer_id' => $order->get_user_id()));
//                $nOrder->add_product(get_product($wpcp_productid), 1);
//                ## Set custom description of order
//
//                $postTitle = get_the_title($post_id);
//                $itemName = sprintf('Recurring payment for server id %s.', $postTitle);
//
//                global $wpdb;
//                $table_name = $wpdb->prefix . 'woocommerce_order_items';
//                $sql = "UPDATE $table_name SET order_item_name = '$itemName' where order_id = $nOrder->id";
//                $wpdb->query($sql);
//
//                ##
//
//                $nOrder->set_address($address, 'billing');
//
//                //
//
//                $nOrder->calculate_totals();
//                $nOrder->update_status('pending');
//
//                update_post_meta($post_id, WPCP_ACTIVEINVOICE, 1);
//                add_post_meta($post_id, WPCP_PAYMENTID, $nOrder->id, true);
//                add_post_meta($nOrder->id, WPCP_INVOICE, 'yes', true);
//
//
//            }
//        }
//
//    }
//    wp_reset_query();
}

add_filter('cron_schedules', 'wpcp_add_cron_interval');

function wpcp_add_cron_interval($schedules)
{
    $schedules['five_seconds'] = array(
        'interval' => 5,
        'display' => esc_html__('Every Five Seconds'),);
    return $schedules;
}

add_action('wpcp_croncp_hook', 'wpcp_cron_exec');

if (!wp_next_scheduled('wpcp_croncp_hook')) {
    wp_schedule_event(time(), 'five_seconds', 'wpcp_croncp_hook');
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

    $data = array('wpcp_provider' => get_post_meta($post->ID, WPCP_PROVIDER, true));

    $cpjm = new CPJobManager('fetchLocations', $data);
    $locations = $cpjm->RunJob();

    printf('
<div>
<label for="wpcp_location">Select Location</label>
<select id="wpcp_location" name="wpcp_location">
    %s
</select>
</div>
', $locations);
}

add_action('woocommerce_before_add_to_cart_button', 'wpcp_display_custom_field_locations');
//add_action('woocommerce_loop_add_to_cart_link', 'wpcp_display_custom_field_locations');

function wpcp_validate_custom_field($passed, $product_id, $quantity)
{
    if (empty($_POST['wpcp_location'])) {
        // Fails validation
        $passed = false;
        wc_add_notice(__('Please select location from product page before ordering.', 'wpcp'), 'error');
    }
    return $passed;
}

add_filter('woocommerce_add_to_cart_validation', 'wpcp_validate_custom_field', 10, 3);

/* */

add_shortcode('wpcpservers', 'wpcp_servers_fetch');
function wpcp_servers_fetch($atts = [], $content = null)
{
    // do something to $content
    // always return

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

            $finalData = $finalData . sprintf('<tr><td>%s</td><td>%s</td><td>Completed</td><td>CyberPanel</td><td><a href="/wpcp_server/%s/">View</a></td></tr>', $postTitle, $date, $postTitle);

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