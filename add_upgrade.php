<?php
require_once './includes/user_init.php';
require './includes/pay.php';

if (!$isLogin && !$config['ke']['guess_allow_submission']) {
    msg_die($lang['msg']['not_member']);
}

$cmd = get_param('cmd');
$item_id = get_param('item_id');

// verify item
$row = verify_owner($item_id, '*');
if ($row['item_status'] != 'P') {
    msg_die(sprintf($lang['msg']['echo'], $lang['l_can_not_upgrade']));
}

// verify class, period & fee
if (($cmd == 'confirm') || ($cmd == 'payment')) {
    $item_valid_date = $row['item_valid_date'];
    $row = process_listing_info($row);
    $target_class = get_param('target_class');

    // allow to upgrade?
    if (!$dir_info[$row['dir_id']]['dir_inf']['dir_pre_allow'] && !$dir_info[$row['dir_id']]['dir_inf']['dir_spo_allow']) {
        redir();
    }

    if ($target_class != 'S') {
        $target_class = 'P';
    }
    if ($target_class == 'S') {
        $price = $dir_info[$row['dir_id']]['dir_inf']['dir_spo_fee'];
        $month = get_param('Speriod');
    } else {
        $price = $dir_info[$row['dir_id']]['dir_inf']['dir_pre_fee'];
        $month = get_param('Pperiod');
    }

    // get fee
    if ($month < 1) {
        $month = 1;
    }
    if ($month > 12) {
        $month = 12;
    }
    $month = floor($month);
    $total = $price * $month;
    $row['price'] = num_format($price, 0, 1);
    $row['total'] = num_format($total, 0, 1);

    // warning?
    if ((($row['item_class'] == 'P') && ($target_class == 'S')) || (($row['item_class'] == 'S') && ($target_class == 'P'))) {
        load_section('add_upgrade.tpl');
        $row['from'] = $listing_class_def[$row['item_class']];
        $row['to'] = $listing_class_def[$target_class];
        $row['warning_class'] = quick_tpl($tpl_section['warning_class'], $row);
    } else {
        $row['warning_class'] = '';
    }

    // get period
    if ($row['item_class'] == $target_class) {
        $today = $item_valid_date;
    } else {
        $today = $sql_today;
    }
    $row['period'] = $month;
    $row['valid_until'] = convert_date($today, 'long', 30 * $month);
    $row['upgrade_to'] = $listing_class_def[$target_class];
}


//
switch ($cmd) {
    case 'success':
        // get info from order_id
        $order_id = get_param('order_id');
        $payment = get_param('payment');
        $sum = sql_qquery("SELECT * FROM ".$db_prefix."order WHERE order_id='$order_id' LIMIT 1");

        // get payment 'how to pay' information
        $form = get_payment_form($payment, $sum);
        if (!empty($form['txt_howtopay'])) {
            $howtopay = true;
            $txt['howtopay'] = $form['txt_howtopay'];
        } else {
            $howtopay = false;
            $txt['howtopay'] = '';
        }

        // redirect?
        if ($form['pay_redirect_to_gateway']) {
            $pay_redirect_to_gateway = true;
            $txt['method'] = $form['method'];
            $txt['action'] = $form['action'];
            $txt['hidden_field'] = $form['hidden'];
        } else {
            $pay_redirect_to_gateway = false;
            $txt['hidden_field'] = $txt['method'] = $txt['action'] = '';
        }

        $txt['item_id'] = $item_id;
        $txt['main_body'] = quick_tpl(load_tpl('add_upgrade_success.tpl'), $txt);
        flush_tpl();
    break;


    case 'confirm':
        $payment = get_param('payment');
        $pay = get_payment_method($payment);
        if (!$pay) {
            msg_die($lang['msg']['payment_not_selected']);
        }

        // order id
        $foo = sql_qquery("SHOW TABLE STATUS LIKE '".$db_prefix."order'");
        $now = $foo['Auto_increment'];
        $l = strlen($now);
        for ($i = $l; $i < 6; $i++) {
            $now = '0'.$now;
        }
        $order_id = date('Ymd').'-'.$now;

        // htp
        $row['howtopay'] = get_payment_htp($payment, array());
        $howtopay = ($row['howtopay'])  ? true : false;

        $row['order_id'] = $order_id;
        $row['today'] = convert_date('today', 'short');
        $row['site_name'] = $config['site_name'];
        $row['site_url'] = $config['site_url'];
        $row['current_user_id'] = $current_user_id;
        $row['payment_method'] = $pay['name'];
        $row['payment_status'] = 'Pending';
        $row['target_class'] = $target_class;
        $row['trx_check_url'] = $config['site_url'].'/trx.php?order_id='.$row['order_id'];
        $row['adm_check_url'] = $config['site_url'].'/'.$config['admin_folder'].'/trx.php?order_id='.$row['order_id'];

        sql_query("INSERT INTO ".$db_prefix."order SET order_id='$order_id', user_id='$current_user_id', user_email='$row[owner_email]', item_id='$item_id',
			target_class='$target_class', item_period='$month', order_date='$sql_today', order_price='$price', order_total='$total', order_payment='$pay[name]', order_paystat='E', order_status='E'");
        $blah = quick_tpl(load_tpl('mail', 'checkout'), $row);
        $blah2 = quick_tpl(load_tpl('mail', 'checkout_admin'), $row);

        // send email
        email($row['owner_email'], sprintf($lang['l_mail_order_subject'], $config['site_name'], $row['order_id']), $blah, true, true);
        email($config['site_email'], sprintf($lang['l_mail_order_admin_subject'], $config['site_name'], $row['order_id']), $blah2, true);
        create_notification('', 'You have a new order '.$row['order_id'], $row['adm_check_url'], true);

        //
        redir($config['site_url'].'/add_upgrade.php?cmd=success&order_id='.$order_id.'&item_id='.$item_id.'&payment='.$payment);
    break;


    case 'payment':
        $tpl_mode = 'payment';
        $tpl = load_tpl('add_upgrade.tpl');

        // payment
        $txt['block_pay_item'] = ''; $i = 0;
        $t = get_payment_method();
        foreach ($t as $val) {
            $val['i'] = $i++;
            $val['fee'] = $val['fee'] ? num_format($val['fee'], 0, 1) : '-';
            if (count($t) == 1) {
                $val['selected'] = 'checked="checked"';
            } else {
                $val['selected'] = '';
            }
            $txt['block_pay_item'] .= quick_tpl($tpl_block['pay_item'], $val);
        }
        $txt = array_merge($txt, $row);
        $txt['target_class'] = $target_class;
        $txt['pperiod'] = get_param('Pperiod');
        $txt['speriod'] = get_param('Speriod');
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl();
    break;


    default:
        $tpl_mode = 'form';
        $tpl = load_tpl('add_upgrade.tpl');
        $row = process_listing_info($row);
        $row['item_class'] = $listing_class_def[$row['item_class']];
        for ($i = 1; $i < 13; $i++) {
            $row['pfee'.$i] = num_format($i * $dir_info[$row['dir_id']]['dir_inf']['dir_pre_fee'], 0, 1);
            $row['sfee'.$i] = num_format($i * $dir_info[$row['dir_id']]['dir_inf']['dir_spo_fee'], 0, 1);
        }
        $txt['main_body'] = quick_tpl($tpl, $row);
        flush_tpl();
    break;
}
