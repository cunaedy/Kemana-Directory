<?php
require './../../includes/user_init.php';
require './../../includes/admin_func.php';

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
$req_id = 0;
$request = $response = $title = $notes = array();

foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
    $$key = $value;
}

// default payment status is PENDING
$mypayment_status = 'E';
$url = 'www.paypal.com';
if ($module_config['pay_paypal']['sandbox']) {
    $url = 'www.sandbox.paypal.com';
}

// post back to PayPal system to validate
$req_id++;
$title[$req_id] = '----------- ('.$req_id.')';
$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: $url\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Connection: close\r\n\r\n";
$request[$req_id] = $header;

$fp = fsockopen('ssl://'.$url, 443, $errno, $errstr, 30);
$response[$req_id] = "Status:$fp - Err No: $errno - Err Str: $errstr";

if (!$fp) {
    $notes[] = $response[$req_id] .= ' Can not open connection to paypal';
    $mypayment_status = 'E';	// PENDING
} else {
    $req_id++;
    $title[$req_id] = '----------- ('.$req_id.')';
    $request[$req_id] = $header.$req;
    $response[$req_id] = '';
    fputs($fp, $header . $req);
    while (!feof($fp)) {
        $res = fgets($fp, 1024);
        $response[$req_id] .= $res;
        if (stripos($res, "VERIFIED") !== false) {
            $mypayment_status = 'E';

            // check the payment_status is Completed
            if ($payment_status == 'Completed') {
                $ps1 = true;
            } else {
                $ps1 = false;
                $notes[] = "This payment may be pending, denied, etc. PayPal payment status: [$payment_status], [$pending_reason].";
            }

            // check that txn_id has not been previously processed
            $frow = sql_qquery("SELECT * FROM ".$db_prefix."payment_log WHERE txn_id='$txn_id' LIMIT 1");
            if (!$frow) {
                $ps2 = true;
            } else {
                $ps2 = false;
                $notes[] = 'This payment has same PayPal\'s TXN ID, may be a forgery attempt.';
            }

            // check that receiver_email is your Primary PayPal email
            $module_config['pay_paypal']['bussiness'] = urlencode($module_config['pay_paypal']['bussiness']);
            if ($receiver_email == $module_config['pay_paypal']['bussiness']) {
                $ps3 = true;
            } else {
                $ps3 = false;
                $notes[] = 'This payment was addressed to other email address. Proceed with caution!';
            }

            // check that payment_amount && payment_currency are correct
            if ($mc_currency == $module_config['pay_paypal']['currency_code']) {
                $ps4 = true;
            } else {
                $ps4 = false;
                $notes[] = 'This payment was made in different currency! For safety reason, it has been put in PENDING status.';
            }

            // get conclusion
            if ($ps1 && $ps2 && $ps3 && $ps4) {
                $mypayment_status = 'P';
            }	// SUCCESS (PAID)
            else {
                $mypayment_status = 'E';
            }	// PENDING
        } elseif (stripos($res, "INVALID") !== false) {
            $mypayment_status = 'E';	// PENDING
        }
    }
    fclose($fp);
}

// log
$request_log = $response_log = '';
foreach ($title as $key => $val) {
    $request_log .= "$title[$key]\n\n$request[$key]\n\n";
    $response_log .= "$title[$key]\n\n$response[$key]\n\n";
}

$order_id = post_param('invoice');
$order_info = sql_qquery("SELECT * FROM ".$db_prefix."order WHERE order_id='$order_id' LIMIT 1");
$total = $order_info['order_total'] + $order_info['order_tax'];
if (!$order_info) {
    $order_id = 'Invalid Order ID';
}
if ($mc_gross < $total) {
    $mypayment_status = 'E';
    $notes[] = 'Total payment less than invoice!';
}
$notes_log = implode("\r\n", $notes);
sql_query("INSERT INTO ".$db_prefix."payment_log SET log_time = UNIX_TIMESTAMP(), pay_type = 'pay_paypal', order_id = '$order_id', txn_id = '$txn_id',
order_total = '$total', order_paid = '$mc_gross', sent_request = '$request_log', response = '$response_log', payment_status = '$mypayment_status',
notes = '$notes_log'");

// $mypayment_status = 'P';
if ($mypayment_status == 'P') {
    // update status payment
    sql_query("UPDATE ".$db_prefix."order SET order_paystat='P' WHERE order_id='$order_id' LIMIT 1");

    // auto approval?
    if ($module_config['pay_paypal']['direct_approval']) {
        $order = sql_qquery("SELECT * FROM ".$db_prefix."order WHERE order_id='$order_id' LIMIT 1");
        upgrade_item($order);
    }
}
