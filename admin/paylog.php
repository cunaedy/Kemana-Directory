<?php
require './../includes/admin_init.php';
admin_check(3);

$mode = get_param('mode');
$log_id = get_param('log_id');
$order_id = get_param('order_id');
$p = get_param('p', 1);

// def
$res = sql_query("SELECT mod_id, mod_name FROM ".$db_prefix."module WHERE mod_type='payment'");
while ($row = sql_fetch_array($res)) {
    $payment_def[$row['mod_id']] = $row['mod_name'];
}

// log
switch ($mode) {
    case 'delall':
        sql_query("TRUNCATE TABLE ".$db_prefix."payment_log");
        admin_die('admin_ok');
    break;

    case 'del':
        admin_check(4);
        sql_query("DELETE FROM ".$db_prefix."payment_log WHERE log_id = '$log_id' LIMIT 1");
        admin_die('admin_ok');
    break;


    case 'detail':
        $tpl_mode = 'detail';
        $tpl = load_tpl('adm', 'paylog.tpl');

        if (!empty($order_id)) {
            $res = sql_query("SELECT * FROM ".$db_prefix."payment_log WHERE order_id = '$order_id' LIMIT 1");
        } else {
            $res = sql_query("SELECT * FROM ".$db_prefix."payment_log WHERE log_id = '$log_id' LIMIT 1");
        }
        $row = sql_fetch_array($res);
        if (empty($row)) {
            admin_die('Log not found! Payment method may not support payment log.');
        }

        // outuput
        if (empty($row['order_id'])) {
            $row['order_id'] = "Missing ID";
        }
        if (!empty($row['notes'])) {
            $doh = array();
            $foo = explode("\r\n", $row['notes']);
            foreach ($foo as $val) {
                $array[] = "<li>$val</li>";
            }
            $row['notes'] = '<ul>'.implode("\n", $array).'</ul>';
        }
        $row['sent_request'] = nl2br($row['sent_request']);
        $row['response'] = nl2br($row['response']);
        $row['payment_method'] = $payment_def[$row['pay_type']];
        $row['payment_status'] = $payment_status_def[$row['payment_status']];
        $row['log_time'] = date('Y-m-d H:m:s', $row['log_time']);
        $txt['main_body'] = quick_tpl($tpl, $row);
        flush_tpl('adm');
    break;


    default:
        $tpl_mode = 'list';
        $tpl = load_tpl('adm', 'paylog.tpl');
        $txt['block_log_item'] = '';

        $tbl = sql_multipage($db_prefix.'payment_log', '*', "1=1", "log_id DESC", $p, "paylog.php", 20);
        foreach ($tbl as $row) {
            if (empty($row['order_id'])) {
                $row['order_id'] = "Missing ID";
            }
            $row['payment_method'] = $payment_def[$row['pay_type']];
            $row['payment_status'] = $payment_status_def[$row['payment_status']];
            $row['notes'] = line_wrap($row['notes'], 100);
            $row['log_time'] = date('Y-m-d H:m:s', $row['log_time']);
            $txt['block_log_item'] .= quick_tpl($tpl_block['log_item'], $row);
        }

        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
}
