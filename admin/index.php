<?php
// part of qEngine
require './../includes/admin_init.php';

if (!$current_user_info['admin_level']) {
    redir($config['site_url'].'/'.$config['admin_folder'].'/login.php');
}

$cmd = get_param('cmd');
switch ($cmd) {
    case 'feed':
        $xml = read_xml('https://www.c97.net/index.php?cmd=rss');
        if (!$xml) {
            die('<b>NOTICE</b> Can not open RSS feed, please open C97net web site for latest update!');
        }

        $txt['block_rssfeed'] = '';
        $xtpl = load_tpl('var', $tpl_section['rssfeed']);
        for ($i = 0; $i < 10; $i++) {
            $row = array();
            $row['title'] = $xml['rss']['#']['channel'][0]['#']['item'][$i]['#']['title'][0]['#'];
            $row['link'] = $xml['rss']['#']['channel'][0]['#']['item'][$i]['#']['link'][0]['#'];
            $row['description'] = $xml['rss']['#']['channel'][0]['#']['item'][$i]['#']['description'][0]['#'];
            $row['shortDesc'] = nl2br(line_wrap(strip_tags($xml['rss']['#']['channel'][0]['#']['item'][$i]['#']['description'][0]['#'])));
            $row['pubDate'] = $xml['rss']['#']['channel'][0]['#']['item'][$i]['#']['pubDate'][0]['#'];
            $row['shortDate'] = convert_date(date('Y-m-d', strtotime($row['pubDate'])));
            $txt['block_rssfeed'] .= quick_tpl($tpl_block['rssfeed'], $row);
        }
        $txt['main_body'] = quick_tpl($xtpl, $txt);
        flush_tpl('popup');
        die;
    break;


    case 'notify_go':
        $idx = get_param('idx');
        $row = sql_qquery("SELECT * FROM ".$db_prefix."notification WHERE idx='$idx' AND notify_admin='1' LIMIT 1");
        if (!empty($row)) {
            sql_query("UPDATE ".$db_prefix."notification SET notify_read='1' WHERE idx='$idx' LIMIT 1");
            if (!empty($row['notify_url'])) {
                redir($row['notify_url']);
            }
        }

        redir();
    break;


    case 'notify_read':
        sql_query("UPDATE ".$db_prefix."notification SET notify_read='1' WHERE notify_admin='1' AND notify_read='0'");
        redir();
    break;


    case 'notify_clear':
        sql_query("DELETE FROM ".$db_prefix."notification WHERE notify_admin='1'");
        redir();
    break;


    case 'listing_feat':
        $msg = $tpl_section['listing_feat'];
        admin_die($msg);
    break;
}

// tpl
$tpl = load_tpl('adm', 'summary.tpl');

// last login info
$ss1 = sql_qquery("SELECT * FROM ".$db_prefix."ip_log WHERE log_user_type='A' ORDER BY idx DESC LIMIT 2");
if (empty($ss1)) {
    $ss1 = array('idx' => 0, 'log_time' => 0, 'log_success' => 0, 'log_user_id' => '(never)', 'log_ip_addr' => '0.0.0.0');
}
$txt['log_user_id'] = $ss1['log_user_id'];
$txt['log_ip_addr'] = $ss1['log_ip_addr'];
$txt['log_idx'] = $ss1['idx'];
$txt['log_time'] = convert_date(date('Y-m-d', $ss1['log_time'])).' '.date('H:i:s', $ss1['log_time']);
$txt['log_success'] = $ss1['log_success'] ? 'Success' : 'Failed';

// new users since last 7 days.
$since7 = convert_date($sql_now, 'sql', -7);
$ss2 = sql_qquery("SELECT COUNT(*) AS num_m, SUM(user_since > '$since7') AS num_mn FROM ".$db_prefix."user");
$txt['total_user'] = $ss2['num_m'];
$txt['total_user_7'] = $ss2['num_m'];

// db info
$ss3 = array(); $db_size = 0; $n = strlen($db_prefix);
$res = sql_query("SELECT TABLE_NAME AS 'tbl', table_rows AS 'qty', data_length + index_length AS 'size'
FROM information_schema.TABLES WHERE information_schema.TABLES.table_schema='$db_name'");
while ($row = sql_fetch_array($res)) {
    $tbl = substr($row['tbl'], $n);
    $ss3[$tbl.'_qty'] = $row['qty'];
    $ss3[$tbl.'_size'] = num_format($row['size'] / 1024, 2);
    $db_size = $db_size + $row['size'];
}

$txt = array_merge($txt, $ss3);
$txt['db_size'] = num_format($db_size / 1024 / 1024, 2);
$txt['free_space'] = num_format(disk_free_space('./') / 1024 / 1024, 2);
$txt['max_space'] = num_format(disk_total_space('./') / 1024 / 1024, 2);
$txt['used_space'] = num_format(disk_total_space('./')-disk_free_space('./'));

// flush
$rnd = random_str(16);
if (!empty($module_enabled['qstats'])) {
    $qstat_module = true;
    $chart_x = $chart_y1 = $chart_y2 = array();
    for ($i = 0; $i <= 7; $i++) {
        $date = convert_date('now', 'sql', -1 * (7 - $i));
        $row = sql_qquery("SELECT stats_hit, stats_visit FROM ".$db_prefix."stats_daily WHERE stats_date = '$date'");
        $chart_x[] = '"'.convert_date($date, 2).'"';
        $chart_y1[] = empty($row['stats_hit']) ? 0 : $row['stats_hit'];
        $chart_y2[] = empty($row['stats_visit']) ? 0 : $row['stats_visit'];
    }
    $txt['chart_x'] = implode(',', $chart_x);
    $txt['chart_y1'] = implode(',', $chart_y1);
    $txt['chart_y2'] = implode(',', $chart_y2);
} else {
    $qstat_module = false;
}
$txt['main_body'] = quick_tpl(load_tpl('adm', 'summary.tpl'), $txt);
flush_tpl('adm');
