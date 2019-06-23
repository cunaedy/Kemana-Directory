<?php
// get list of available payment method
// $method = return only $method method info
function get_payment_method($method = '')
{
    global $db_prefix;
    $tmp = array();

    if (!$method) {
        $res = sql_query("SELECT * FROM ".$db_prefix."module WHERE mod_type='payment' AND mod_enabled = '1'");
        while ($row = sql_fetch_array($res)) {
            // get fee
            $module_config = $summary = array();
            $payment_cmd = 'init';
            $payment_extra_fee = 0;
            $mod = $row['mod_id'];
            if (!file_exists('./module/'.$mod.'/window.php')) {
                msg_die(sprintf($lang['msg']['internal_error'], 'Payment module '.$method.' not found.'));
            }
            require('./module/'.$mod.'/window.php');
            $tmp[$row['mod_id']]['method'] = $row['mod_id'];
            $tmp[$row['mod_id']]['name'] = $row['mod_name'];
            $tmp[$row['mod_id']]['fee'] = $payment_extra_fee;
        }

        return $tmp;
    } else {
        $payment_cmd = 'init';
        $payment_extra_fee = 0;
        $row = sql_qquery("SELECT * FROM ".$db_prefix."module WHERE mod_id='$method' AND mod_type='payment' AND mod_enabled = '1'");
        if (!$row) {
            return false;
        }
        $mod = $row['mod_id'];
        if (!file_exists('./module/'.$mod.'/window.php')) {
            msg_die(sprintf($lang['msg']['internal_error'], 'Payment module '.$method.' not found.'));
        }
        require('./module/'.$mod.'/window.php');
        return (array('method' => $row['mod_id'], 'name' => $row['mod_name'], 'fee' => $payment_extra_fee));
    }
}


// get payment form fields
// $summary = order summary: subtotal, discount, tax, total, shipping_fee, order_id
function get_payment_form($method, $summary)
{
    global $config, $db_prefix, $current_user_id, $module_config;

    // summary
    $summary['order_total'] = number_format($summary['order_total'], 2, '.', '');

    // load related module
    $payment_cmd = 'form';
    if (!file_exists('./module/'.$method.'/window.php')) {
        msg_die(sprintf($lang['msg']['internal_error'], 'Payment module '.$method.' not found.'));
    }
    require('./module/'.$method.'/window.php');
    return $form;
}


// get how to pay information from a method
function get_payment_htp($method, $summary)
{
    global $config, $db_prefix, $current_user_id, $module_config;

    $payment_cmd = 'htp';
    if (!file_exists('./module/'.$method.'/window.php')) {
        msg_die(sprintf($lang['msg']['internal_error'], 'Payment module '.$method.' not found.'));
    }
    require('./module/'.$method.'/window.php');
    return $txt_howtopay;
}
