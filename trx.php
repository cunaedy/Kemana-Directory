<?php
// part of qEngine
require_once "./includes/user_init.php";

// close site?
if (!$isLogin) {
    redir($config['site_url'].'/profile.php?mode=login');
}

$p = get_param('p', 1);
$order_id = get_param('order_id');
if ($order_id) {
    $cmd = 'detail';
} else {
    $cmd = 'list';
}
switch ($cmd) {
    case 'detail':
        $tpl_mode = 'detail';
        $tpl = load_tpl('trx.tpl');
        $trx = sql_qquery("SELECT * FROM ".$db_prefix."order WHERE user_id='$current_user_id' AND order_id='$order_id' LIMIT 1");
        if (!$trx) {
            msg_die($lang['msg']['edit_item_not_found']);
        }

        // trx info
        $trx['order_date'] = convert_date($trx['order_date']);
        $trx['order_price'] = num_format($trx['order_price'], 0, 1);
        $trx['order_total'] = num_format($trx['order_total'], 0, 1);
        $trx['order_paystat'] = $payment_status_def[$trx['order_paystat']];
        $trx['order_status'] = $order_status_def[$trx['order_status']];
        $trx['target_class'] = $listing_class_def[$trx['target_class']];


        // get item info
        $item = sql_qquery("SELECT *, idx AS item_id FROM ".$db_prefix."listing WHERE idx='$trx[item_id]' LIMIT 1");
        $item = process_listing_info($item);
        $item['item_class'] = $listing_class_def[$item['item_class']];

        $txt = array_merge($txt, $trx, $item);

        $txt['main_body'] = quick_tpl($tpl, $txt);
        generate_html_header("$config[site_name] $config[cat_separator] My Orders");
        flush_tpl();
    break;


    default:
        $tpl_mode = 'list';
        $tpl = load_tpl('trx.tpl');
        $foo = sql_multipage($db_prefix.'order', '*', "user_id='$current_user_id'", 'idx DESC', $p);
        $txt['block_list'] = '';
        foreach ($foo as $row) {
            // get item info
            $bar = sql_qquery("SELECT *, idx AS item_id FROM ".$db_prefix."listing WHERE idx='$row[item_id]' LIMIT 1");
            $bar = process_listing_info($bar);
            $row['item_title'] = $bar['item_title'];
            $row['order_date'] = convert_date($row['order_date']);
            $row['order_status'] = $order_status_def[$row['order_status']];
            $row['target_class'] = $listing_class_def[$row['target_class']];
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }
        $txt['main_body'] = quick_tpl($tpl, $txt);
        generate_html_header("$config[site_name] $config[cat_separator] My Orders");
        flush_tpl();
    break;
}
