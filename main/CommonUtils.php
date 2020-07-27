<?php


class CommonUtils
{
    protected $json;
    static $CurrentLog = 0;
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
            fwrite($writeToFile, $message . PHP_EOL);
            fclose($writeToFile);
        }
    }

    static function RunCron(){

        $query = new WP_Query(array(
            'post_type' => 'wpcp_server',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ));
        while ($query->have_posts()) {

            $query->the_post();
            $serverID = get_the_ID();

            // Get current meta values of this server id

            $wpcp_productid = get_post_meta($serverID, WPCP_PRODUCTID, true);
            $wpcp_duedate = (int) get_post_meta($serverID, WPCP_DUEDATE, true);
            $wpcp_activeinvoice = get_post_meta($serverID, WPCP_ACTIVEINVOICE, true);
            $wpcp_orderid = get_post_meta($serverID, WPCP_ORDERID, true);
            $state = get_post_meta($serverID, WPCP_STATE, true);

            /// If state is already terminated, no need to check anything.

            if ($state != WPCP_TERMINATED) {

                ## Payment id is set below when invoice is generated for this server.

                $paymentOrderID = get_post_meta($serverID, WPCP_PAYMENTID, true);

                ## Get current timeStamp

                $now = new DateTime();
                $nowTimeStamp = $now->getTimestamp();

                ##

                $diff = $wpcp_duedate - $nowTimeStamp;

                ### Get site-wide option

                $autoInvoice = (int) get_option(WPCP_INVOICE, '14') * 86400;
                $WPCP_AUTOSUSPEND = (int) get_option(WPCP_AUTOSUSPEND, '3') * 86400;
                $WPCP_TERMINATE = (int) get_option(WPCP_TERMINATE, '10') * 86400;

                CommonUtils::writeLogs(sprintf('Original Server ID: %s. Product id of this server: %s. Due date of this server: %d. Active invoice: %d. Order id from which this server was created: %s  ', $serverID, $wpcp_productid, $wpcp_duedate, $wpcp_activeinvoice, $wpcp_orderid), CPWP_ERROR_LOGS);
                CommonUtils::writeLogs(sprintf('If this server (%s) already have active invoice order, then id is: %s', $serverID, $paymentOrderID), CPWP_ERROR_LOGS);

                if ($paymentOrderID != '') {

                    $dataToSend = array('serverID' => get_the_title(), 'json' => 0);

                    CommonUtils::writeLogs(sprintf('Server Title being checked for suspension/termination %s.', get_the_title()), CPWP_ERROR_LOGS);

                    $order = wc_get_order($paymentOrderID);
                    $orderTimeStamp = (int)get_post_meta($order->id, WPCP_DUEDATE, true);

                    CommonUtils::writeLogs(sprintf('Timestamp when the invoice order is created %s. Order status: %s.', $orderTimeStamp, $order->data['status']), CPWP_ERROR_LOGS);

                    if ($WPCP_AUTOSUSPEND) {

                        CommonUtils::writeLogs(sprintf('Auto suspend is active for order id %s with timestamp %d.', $order->id, $orderTimeStamp), CPWP_ERROR_LOGS);

                        $finalTimeStamp = $orderTimeStamp + $WPCP_AUTOSUSPEND;

                        CommonUtils::writeLogs(sprintf('Finale timestamp: %d. Now timestamp: %d', $finalTimeStamp, $now->getTimestamp()), CPWP_ERROR_LOGS);

                        if ($state == WPCP_ACTIVE) {

                            if ($finalTimeStamp < $nowTimeStamp) {

                                CommonUtils::writeLogs(sprintf('Performing suspension for order id %d with timestamp of %s.', $order->id, $orderTimeStamp), CPWP_ERROR_LOGS);

                                $dataToSend = array('serverID' => get_the_title());
                                $cpjm = new CPJobManager('shutDown', $dataToSend);
                                $cpjm->RunJob();

                                ## Turn server state to suspended

                                update_post_meta($serverID, WPCP_STATE, WPCP_SUSPENDED);

                                ### Send Suspension Email

                                $orderOriginal = wc_get_order($wpcp_orderid);

                                $replacements = array(
                                    '{FullName}' => $orderOriginal->get_billing_first_name() . ' ' . $orderOriginal->get_billing_last_name(),
                                    '{ServerID}' => get_the_title(),
                                    '{Reason}' => 'Invoice overdue.'

                                );

                                $subject = sprintf('Server with ID# %s suspended.', get_the_title());

                                $content = str_replace(
                                    array_keys($replacements),
                                    array_values($replacements),
                                    get_option(WPCP_SERVER_SUSPENDED, WPCPHTTP::$ServerSuspended)
                                );

                                wp_mail($orderOriginal->get_billing_email(), $subject, $content);

                                ##

                            }
                        } else {
                            CommonUtils::writeLogs(sprintf('Shutdown for order id %s of server id %s is not needed as state is not active.', $order->id, $serverID), CPWP_ERROR_LOGS);
                        }
                    }
                    if ($WPCP_TERMINATE) {

                        CommonUtils::writeLogs(sprintf('Auto terminate is active for order id %s with timestamp %d.', $order->id, $orderTimeStamp), CPWP_ERROR_LOGS);
                        $finalTimeStamp = $orderTimeStamp + $WPCP_TERMINATE;
                        CommonUtils::writeLogs(sprintf('Final timestamp: %d. Now timestamp: %d', $finalTimeStamp, $now->getTimestamp()), CPWP_ERROR_LOGS);

                        if ($state == WPCP_ACTIVE || $state == WPCP_CANCELLED) {

                            if ($finalTimeStamp < $nowTimeStamp) {

                                CommonUtils::writeLogs(sprintf('Performing termination for order id %d with timestamp of %s.', $order->id, $orderTimeStamp), CPWP_ERROR_LOGS);

                                $cpjm = new CPJobManager('cancelNow', $dataToSend);
                                $cpjm->RunJob();
                                update_post_meta($serverID, WPCP_STATE, WPCP_TERMINATED);

                                ### Send Suspension Email

                                $orderOriginal = wc_get_order($wpcp_orderid);

                                $replacements = array(
                                    '{FullName}' => $orderOriginal->get_billing_first_name() . ' ' . $orderOriginal->get_billing_last_name(),
                                    '{ServerID}' => get_the_title(),
                                );

                                $subject = sprintf('Server with ID# %s terminated.', get_the_title());

                                $content = str_replace(
                                    array_keys($replacements),
                                    array_values($replacements),
                                    get_option(WPCP_SERVER_TERMINATED, WPCPHTTP::$ServerTerminated)
                                );

                                wp_mail($orderOriginal->get_billing_email(), $subject, $content);

                                ##

                            }
                        } else {
                            CommonUtils::writeLogs(sprintf('Terminate for order id %s of server id %s is not needed as state is not active.', $order->id, $serverID), CPWP_ERROR_LOGS);
                        }
                    }
                }

                if (!$wpcp_activeinvoice) {

                    if ($diff <= $autoInvoice) {

                        update_post_meta($serverID, WPCP_DUEDATE, (string)($wpcp_duedate + (30 * 86400)));

                        $order = wc_get_order($wpcp_orderid);

                        $address = array(
                            'first_name' => $order->get_billing_first_name(),
                            'last_name' => $order->get_billing_last_name(),
                            'company' => $order->get_billing_company(),
                            'email' => $order->get_billing_email(),
                            'phone' => $order->get_billing_phone(),
                            'address_1' => $order->get_billing_address_1(),
                            'address_2' => $order->get_billing_address_2(),
                            'city' => $order->get_billing_city(),
                            'state' => $order->get_billing_state(),
                            'postcode' => $order->get_billing_postcode(),
                            'country' => $order->get_billing_country(),
                        );

                        // Now we create the order
                        $nOrder = wc_create_order(array('customer_id' => $order->get_user_id()));
                        $nOrder->add_product(get_product($wpcp_productid), 1);
                        ## Set custom description of order
                        $postTitle = get_the_title($serverID);
                        $itemName = sprintf('Recurring payment for service id %s.', $postTitle);

                        global $wpdb;
                        $table_name = $wpdb->prefix . 'woocommerce_order_items';
                        $sql = "UPDATE $table_name SET order_item_name = '$itemName' where order_id = $nOrder->id";
                        $wpdb->query($sql);

                        ##

                        $nOrder->set_address($address, 'billing');

                        //

                        $nOrder->calculate_totals();
                        $nOrder->update_status('pending');

                        update_post_meta($serverID, WPCP_ACTIVEINVOICE, 1);
                        add_post_meta($serverID, WPCP_PAYMENTID, $nOrder->id, true);
                        add_post_meta($nOrder->id, WPCP_INVOICE, 'yes', true);
                        add_post_meta($nOrder->id, WPCP_DUEDATE, (string)$wpcp_duedate, true);
                        add_post_meta($nOrder->id, WPCP_INVOICESERVER, $serverID, true);
                    }
                }
            }

        }
        wp_reset_query();

    }
}