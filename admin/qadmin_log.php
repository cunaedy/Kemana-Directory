<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_log');

$mode = get_param('mode');
$w = get_param('w');
$h = get_param('h');
$pid = get_param('pid');
$log_id = get_param('log_id');
$p = get_param('p', 1);

// def
$act_def = array(1 => 'New Item', 2 => 'Edit Item', 3 => 'Delete Item', 4 => 'File Upload', 5 => 'File Removal', 6 => 'Restore');

//
$enable_detailed_log = $config['enable_detailed_log'];

// log
switch ($mode) {
    case 'restore':
        axsrf_check();
        $row = sql_qquery("SELECT * FROM ".$db_prefix."qadmin_log WHERE log_id = '$log_id' LIMIT 1");
        if (empty($row) || empty($row['log_previous'])) {
            admin_die(sprintf($lang['msg']['echo'], 'Log not available!'));
        }
        $old = (unserialize(gzuncompress(base64_decode($row['log_previous']))));
        if (empty($old)) {
            admin_die(sprintf($lang['msg']['echo'], 'Previous values not available!'));
        }
        if (($row['log_action'] == 1) || ($row['log_action'] == 4) || ($row['log_action'] == 5)) {
            admin_die(sprintf($lang['msg']['echo'], 'Unfortunately qEngine can\'t restore changed files or item creation.'));
        }

        // create sql
        $sql = array();
        foreach ($old as $k => $v) {
            if (!is_numeric($k)) {
                $sql[] = "$k='".addslashes($v)."'";
            }
        }

        if (!empty($sql)) {
            $sql = implode(', ', $sql);

            // get primary field
            $tbl = sql_qquery("show index from $row[log_table] where Key_name = 'PRIMARY'");
            if (!$tbl || empty($tbl['Column_name'])) {
                admin_die(sprintf($lang['msg']['echo'], 'This feature is not supported by this version of MySQL!'));
            }

            // save current values for logging
            $current = sql_qquery("SELECT * FROM $row[log_table] WHERE $tbl[Column_name] = '$row[log_pid]' LIMIT 1");

            // restore
            sql_query("UPDATE $row[log_table] SET $sql WHERE $tbl[Column_name] = '$row[log_pid]' LIMIT 1");
            $res = mysqli_affected_rows($dbh);

            // restore success?
            if ($res) {
                qadmin_log($row['log_pid'], $row['log_title'], 6, $current, $old, $row['log_table']);
                admin_die(sprintf($lang['msg']['echo'], 'This entry has been restored. Some values may need to be restored manually!'));
            } else {
                admin_die(sprintf($lang['msg']['echo'], 'Restore failed! Item may have been removed, or no restore necessary.'));
            }
        }
    break;


    case 'delall':
        admin_check(5);
        axsrf_check();
        sql_query("TRUNCATE TABLE ".$db_prefix."qadmin_log");
        admin_die('admin_ok');
    break;

    case 'del':
        axsrf_check();
        sql_query("DELETE FROM ".$db_prefix."qadmin_log WHERE log_id = '$log_id' LIMIT 1");
        admin_die('admin_ok');
    break;


    case 'detail':
        $tpl_mode = 'detail';
        $tpl = load_tpl('adm', 'qadmin_log.tpl');
        $res = sql_query("SELECT * FROM ".$db_prefix."qadmin_log WHERE log_id = '$log_id' LIMIT 1");
        $row = sql_fetch_array($res);
        if (empty($row)) {
            redir($config['site_url'].'/'.$config['admin_folder'].'/qadmin_log.php');
        }
        $row['log_time'] = date('Y-m-d H:m:s', $row['log_date']);
        $row['log_action_def'] = $act_def[$row['log_action']];
        $row['axsrf'] = axsrf_value();

        // comparison table
        $no_details = false;
        if (!empty($row['log_previous'])) {
            $old = (unserialize(gzuncompress(base64_decode($row['log_previous']))));
            if (($row['log_action'] == 1) || ($row['log_action'] == 3)) {
                $new = $old;
            } else {
                if (!empty($row['log_now'])) {
                    $new = (unserialize(gzuncompress(base64_decode($row['log_now']))));
                } else {
                    $new = array('notice' => 'Detailed log is disabled');
                    $no_details = true;
                }
            }
            $keys = array_keys(array_merge($old, $new));
            $row['values'] = "<table class=\"table table-bordered\" border=\"0\" width=\"100%\">\n";

            if (($row['log_action'] == 1) || ($row['log_action'] == 3)) {
                $row['values'].= "<tr><th width=\"20%\">Field ID</th><th width=\"40%\">Original Values</th></tr>\n";
            } else {
                $row['values'].= "<tr><th width=\"20%\">Field ID</th><th width=\"40%\">Original Values</th><th width=\"40%\">Entered Values</th></tr>\n";
            }
            foreach ($keys as $k => $v) {
                if (!is_numeric($v)) {
                    $o = isset($old[$v]) ? $old[$v] : '';
                    $n = isset($new[$v]) ? $new[$v] : '';
                    if ($o != $n) {
                        $row['values'] .= "<tr><td><b>$v</b></td><td class=\"danger\">$o</td><td class=\"danger\">$n</td></tr>\n";
                    } else {
                        if (($row['log_action'] == 1) || ($row['log_action'] == 3)) {
                            $row['values'] .= "<tr><td>$v</td><td>$o</td></tr>\n";
                        } else {
                            $row['values'] .= "<tr><td>$v</td><td>$o</th><td>$n</td></tr>\n";
                        }
                    }
                }
            }
            $row['values'] .= "</table>\n";
        } else {
            $row['values'] = '';
        }
        if ($no_details) {
            $row['values'] = '';
        }
        $txt['main_body'] = quick_tpl($tpl, $row);
        flush_tpl('adm');
    break;


    default:
     $tpl_mode = 'list';
     $tpl = load_tpl('adm', 'qadmin_log.tpl');
     $txt['block_log_item'] = '';

     if ($w == 'date') {
         $s = convert_date($h, 'unix');
         $f = convert_date($h, 'unix', 1);
         $where = "(log_date >= '$s') AND (log_date <= '$f')";
     } elseif ($w == 'file') {
         $where = "log_file='$h'";
     } elseif ($w == 'user') {
         $where = "log_user='$h'";
     } elseif ($w == 'action') {
         $where = "log_action='$h'";
     } elseif ($w == 'pid') {
         $where = "(log_file='$h') AND (log_pid='$pid')";
     } elseif ($w == 'ip') {
        $where = "(log_ip='$h')";
    } else {
         $where = '1=1';
     }

     $axsrf = axsrf_value();
     $tbl = sql_multipage($db_prefix.'qadmin_log', '*', $where, 'log_id DESC', $p, 'qadmin_log.php', 20);
     foreach ($tbl as $row) {
         $row['axsrf'] = axsrf_value();
         $row['log_action_def'] = $act_def[$row['log_action']];
         $row['log_time'] = date('H:i:s', $row['log_date']);
         $row['log_date'] = date('Y-m-d', $row['log_date']);
         $txt['block_log_item'] .= quick_tpl($tpl_block['log_item'], $row);
     }

     $txt['axsrf'] = axsrf_value();
     $txt['main_body'] = quick_tpl($tpl, $txt);
     flush_tpl('adm');
}
