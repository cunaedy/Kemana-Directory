<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_log');

$mode = get_param('mode');
$log_id = get_param('log_id');
$p = get_param('p', 1);
$w = get_param('w');
$h = get_param('h');

// dd
$user_def['U'] = 'user';
$user_def['A'] = 'admin';

switch ($mode) {
    case 'delall':
     axsrf_check();
     sql_query("TRUNCATE TABLE ".$db_prefix."ip_log");
     admin_die('admin_ok');
    break;

    case 'del':
     axsrf_check();
     sql_query("DELETE FROM ".$db_prefix."ip_log WHERE idx = '$log_id' LIMIT 1");
     admin_die('admin_ok');
    break;

    default:
     $tpl_mode = 'list';
     $tpl = load_tpl('adm', 'iplog.tpl');
     $txt['block_log_item'] = '';
     $axsrf = axsrf_value();

     // filter by?
     if ($w == 'ip') {
         $where = "log_ip_addr='$h'";
     } else {
         $where = '1=1';
     }


     $tbl = sql_multipage($db_prefix.'ip_log', '*', $where, "idx DESC", $p);
     foreach ($tbl as $row) {
         $row['axsrf'] = $axsrf;
         $row['log_time'] = date('Y-m-d H:m:s', $row['log_time']);
         $row['log_success'] = $row['log_success'] ? 'Success' : 'Failed';
         $row['log_user_type'] = $user_def[$row['log_user_type']];
         $txt['block_log_item'] .= quick_tpl($tpl_block['log_item'], $row);
     }

     $txt['axsrf'] = $axsrf;
     $txt['main_body'] = quick_tpl($tpl, $txt);
     flush_tpl('adm');
}
