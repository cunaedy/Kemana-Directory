<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_log');

$mode = get_param('mode');
$log_id = get_param('log_id');
$p = get_param('p', 1);
// log
switch ($mode) {
    case 'delall':
     axsrf_check();
     sql_query("TRUNCATE TABLE ".$db_prefix."mailog");
     admin_die('admin_ok');
    break;

    case 'del':
     axsrf_check();
     sql_query("DELETE FROM ".$db_prefix."mailog WHERE log_id = '$log_id' LIMIT 1");
     admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/mailog.php');
    break;


    case 'detail':
     $search = array("'<script[^>]*?>.*?</script>'si",  "'<style[^>]*?>.*?</style>'si", "'<head[^>]*?>.*?</head>'si", "'<link[^>]*?>.*?</link>'si", "'<object[^>]*?>.*?</object>'si");
     $tpl_mode = 'detail';
     $tpl = load_tpl('adm', 'mailog.tpl');
     $res = sql_query("SELECT * FROM ".$db_prefix."mailog WHERE log_id = '$log_id' LIMIT 1");
     $row = sql_fetch_array($res);
     $row['log_address'] = htmlentities($row['log_address'], ENT_COMPAT, 'UTF-8');
     $row['log_time'] = date('Y-m-d H:m:s', $row['log_time']);
     $row['log_body'] = $row['log_html'] ? preg_replace($search, '', $row['log_body']) : nl2br($row['log_body']);
     $row['keyword'] = '';
     $row['axsrf'] = axsrf_value();
     $txt['main_body'] = quick_tpl($tpl, $row);
     flush_tpl('adm');
    break;


    case 'search':
     $tpl_mode = 'list';
     $keyword = get_param('keyword');
     $axsrf = axsrf_value();
     $tpl = load_tpl('adm', 'mailog.tpl');
     $txt['block_log_item'] = '';

     $b = create_where('log_body', $keyword);
     if (empty($keyword)) {
         $b = '1=2';
     }
     $tbl = sql_multipage($db_prefix.'mailog', '*', $b, "log_id DESC", $p, "mail_log.php", 20);
     foreach ($tbl as $row) {
         $row['axsrf'] = $axsrf;
         $row['log_address'] = htmlentities($row['log_address'], ENT_COMPAT, 'UTF-8');
         $row['log_time'] = date('Y-m-d H:m:s', $row['log_time']);
         $txt['block_log_item'] .= quick_tpl($tpl_block['log_item'], $row);
     }

     $txt['keyword'] = $keyword;
     $txt['axsrf'] = $axsrf;
     $txt['main_body'] = quick_tpl($tpl, $txt);
     flush_tpl('adm');
    break;


    default:
     $tpl_mode = 'list';
     $tpl = load_tpl('adm', 'mailog.tpl');
     $txt['block_log_item'] = '';
     $axsrf = axsrf_value();

     $tbl = sql_multipage($db_prefix.'mailog', '*', "1=1", "log_id DESC", $p, "mail_log.php", 20);
     foreach ($tbl as $row) {
         $row['axsrf'] = $axsrf;
         $row['log_address'] = htmlentities($row['log_address'], ENT_COMPAT, 'UTF-8');
         $row['log_time'] = date('Y-m-d H:m:s', $row['log_time']);
         $txt['block_log_item'] .= quick_tpl($tpl_block['log_item'], $row);
     }

     $txt['keyword'] = '';
     $txt['axsrf'] = $axsrf;
     $txt['main_body'] = quick_tpl($tpl, $txt);
     flush_tpl('adm');
}
