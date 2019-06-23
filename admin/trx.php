<?php
function post_process($cmd, $id, $qadmin_savenew, $old, $new)
{
    global $config, $db_prefix, $sql_today;
    if ($cmd == 'update') {
        // approved order
        if (($old['order_status'] != 'C') && ($new['order_status'] == 'C')) {
            upgrade_item($new);
        }

        // cancelled order
        if (($old['order_status'] != 'X') && ($new['order_status'] == 'X')) {
            sql_query("UPDATE ".$db_prefix."order SET order_cancelled='$sql_today', order_completed='' WHERE idx='$id' LIMIT 1");
        }

        // pending order
        if (($old['order_status'] != 'E') && ($new['order_status'] == 'E')) {
            sql_query("UPDATE ".$db_prefix."order SET order_cancelled='', order_completed='' WHERE idx='$id' LIMIT 1");
        }
    }

    redir($config['site_url'].'/'.$config['admin_folder'].'/trx.php?id='.$id);
}


// part of qEngine
require './../includes/admin_init.php';

// order id?
if ($oid = get_param('order_id')) {
    $foo = sql_qquery("SELECT idx FROM ".$db_prefix."order WHERE order_id='$oid' LIMIT 1");
    if ($foo) {
        $_GET['id'] = $foo['idx'];
    }
}

$idx = get_param('id');
if (empty($idx)) {
    $idx = get_param('primary_val');
}
if (empty($idx)) {
    $idx = post_param('primary_val');
}

$qadmin_def = array();
if ($idx) {
    $order = sql_qquery("SELECT * FROM ".$db_prefix."order WHERE idx='$idx' LIMIT 1");
    if (!$order) {
        redir();
    }

    // idx :: int :: 10
    $qadmin_def['idx']['title'] = 'ID';
    $qadmin_def['idx']['field'] = 'idx';
    $qadmin_def['idx']['type'] = 'echo';
    $qadmin_def['idx']['value'] = 'sql';

    // order_id :: varchar :: 48
    $qadmin_def['order_id']['title'] = 'Order ID';
    $qadmin_def['order_id']['field'] = 'order_id';
    $qadmin_def['order_id']['type'] = 'echo';
    $qadmin_def['order_id']['value'] = 'sql';

    // user_id :: varchar :: 240
    $qadmin_def['user_id']['title'] = 'User Id';
    $qadmin_def['user_id']['field'] = 'user_id';
    $qadmin_def['user_id']['type'] = 'echo';
    $qadmin_def['user_id']['value'] = 'sql';

    // user_email :: varchar :: 765
    $qadmin_def['user_email']['title'] = 'User Email';
    $qadmin_def['user_email']['field'] = 'user_email';
    $qadmin_def['user_email']['type'] = 'echo';
    $qadmin_def['user_email']['value'] = 'sql';

    // item_id :: int :: 10
    $item = sql_qquery("SELECT * FROM ".$db_prefix."listing WHERE idx='$order[item_id]' LIMIT 1");
    $item['item_id'] = $item['idx'];
    $item = process_listing_info($item);
    $item_title = '<div class="small">'.$item['dir_name'].' &raquo; '.$item['cat_name'].'</div><div><a href="'.$config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&amp;item_id='.$item['item_id'].'" target="_blank">'.$item['item_title'].'</a></div>';
    $qadmin_def['item_id']['title'] = 'Listing';
    $qadmin_def['item_id']['field'] = 'item_id';
    $qadmin_def['item_id']['type'] = 'echo';
    $qadmin_def['item_id']['value'] = $item_title;

    // target_class :: char :: 3
    $val = $listing_class_def[$order['target_class']].' for '.$order['item_period'].' month(s)';
    $qadmin_def['target_class']['title'] = 'Requested Target Class';
    $qadmin_def['target_class']['field'] = 'target_class';
    $qadmin_def['target_class']['type'] = 'echo';
    $qadmin_def['target_class']['value'] = $val;

    // current class
    $val = $listing_class_def[$item['item_class']].' until '.$item['item_valid_date'];
    $qadmin_def['current_class']['title'] = 'Current Class';
    $qadmin_def['current_class']['field'] = 'current_class';
    $qadmin_def['current_class']['type'] = 'echo';
    $qadmin_def['current_class']['value'] = $val;

    // order_price :: decimal :: 12
    $qadmin_def['order_price']['title'] = 'Monthly Fee';
    $qadmin_def['order_price']['field'] = 'order_price';
    $qadmin_def['order_price']['type'] = 'echo';
    $qadmin_def['order_price']['value'] = 'sql';
    $qadmin_def['order_price']['prefix'] = $lang['l_cur_name'];

    // order_total :: decimal :: 12
    $qadmin_def['order_total']['title'] = 'Total';
    $qadmin_def['order_total']['field'] = 'order_total';
    $qadmin_def['order_total']['type'] = 'echo';
    $qadmin_def['order_total']['value'] = 'sql';
    $qadmin_def['order_total']['prefix'] = $lang['l_cur_name'];

    // order_payment :: varchar :: 240
    $qadmin_def['order_payment']['title'] = 'Payment Type';
    $qadmin_def['order_payment']['field'] = 'order_payment';
    $qadmin_def['order_payment']['type'] = 'echo';
    $qadmin_def['order_payment']['value'] = 'sql';
    $qadmin_def['order_payment']['suffix'] = "<a href=\"paylog.php?mode=detail&amp;order_id=$order[order_id]\" target=\"_blank\" class=\"btn btn-default btn-xs\">See Log</a>";

    // order_paystat :: char :: 3
    $qadmin_def['order_paystat']['title'] = 'Payment Status';
    $qadmin_def['order_paystat']['field'] = 'order_paystat';
    $qadmin_def['order_paystat']['type'] = 'select';
    $qadmin_def['order_paystat']['option'] = $payment_status_def;
    $qadmin_def['order_paystat']['value'] = 'sql';
    $qadmin_def['order_paystat']['required'] = true;

    // order_date :: date :: 10
    $qadmin_def['order_date']['title'] = 'Order Date';
    $qadmin_def['order_date']['field'] = 'order_date';
    $qadmin_def['order_date']['type'] = 'echo';
    $qadmin_def['order_date']['value'] = convert_date($order['order_date']);

    // order_completed :: date :: 10
    $qadmin_def['order_completed']['title'] = 'Order Completion Date';
    $qadmin_def['order_completed']['field'] = 'order_completed';
    $qadmin_def['order_completed']['type'] = 'echo';
    $qadmin_def['order_completed']['value'] = convert_date($order['order_completed']);

    // order_cancelled :: date :: 10
    $qadmin_def['order_cancelled']['title'] = 'Order Cancellation Date';
    $qadmin_def['order_cancelled']['field'] = 'order_cancelled';
    $qadmin_def['order_cancelled']['type'] = 'echo';
    $qadmin_def['order_cancelled']['value'] = convert_date($order['order_cancelled']);

    // order_status :: char :: 3
    $qadmin_def['order_status']['title'] = 'Order Status';
    $qadmin_def['order_status']['field'] = 'order_status';
    $qadmin_def['order_status']['type'] = 'select';
    $qadmin_def['order_status']['option'] = $order_status_def;
    $qadmin_def['order_status']['value'] = 'sql';
    $qadmin_def['order_status']['required'] = true;
    $qadmin_def['order_status']['suffix'] = '<p class="small">&bull; Changing status to <b>Completed</b> will approve this transaction and automatically enable the requested target class.</p>
	<p class="small">&bull; Changing to <b>Denied</b> will not revert listing class back to Regular, you need to do it manually by editing the listing.</p>';
}

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'order';				// table name
$qadmin_cfg['primary_key'] = 'idx';							// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['rebuild_cache'] = true;							// rebuild cache
$qadmin_cfg['log_title'] = 'order_id';					// qadmin field to be used as log title (REQUIRED even if you don't use log)
$qadmin_cfg['post_process'] = 'post_process';

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';			// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';			// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,order_id,user_email,order_total';	// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Order ID,Owner Email,Total';			// mask other key
$qadmin_cfg['search_date_field'] = 'order_date';				// search by date field name *
$qadmin_cfg['search_start_date'] = true;					// show start date *
$qadmin_cfg['search_end_date'] = true;						// show end date *

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = false;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = false;

// form title
$qadmin_title['new'] = 'Add Order';
$qadmin_title['update'] = 'Update Order';
$qadmin_title['search'] = 'Search Order';
$qadmin_title['list'] = 'Order List';

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 3;

// auto sql query generated by qAdmin: "SELECT * FROM table WHERE primary_key='primary_val' LIMIT 1"
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
