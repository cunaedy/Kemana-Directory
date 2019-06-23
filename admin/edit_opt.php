<?php
// part of qEngine
/* NOTICE! Edit Option (aka Quick Option) as of qEngine 12 is no longer availabe by default.
   To use it, you need to run this MySQL query to create 'quick option' table:

    CREATE TABLE `[PREFIX]_option` (
      `idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `option_name` varchar(255) NOT NULL,
      `option_value` varchar(255) NOT NULL,
      PRIMARY KEY (`idx`),
      KEY `option_name` (`option_name`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

function get_sublist($fid)
{
    global $db_prefix;
    $sublist = post_param('sublist');
    if (empty($sublist)) {
        $sublist = get_param('sublist');
    }
    $on = '';

    switch ($fid) {
        case 'sector_subclass':
         $on = 'sector_class';
         $label = 'Classification';
         $reindex = false;
        break;

        case 'service_subclass':
         $on = 'service_class';
         $label = 'Classification';
         $reindex = false;
        break;

        case 'stage_workstage':
         $on = 'stage_workplan';
         $label = 'Classification';
         $reindex = false;
        break;

        case 'company_class':
         $on = 'company_type';
         $label = 'Company Type';
         $reindex = false;
        break;
    }

    if ($on) {
        $foo = array();
        $i = 0;
        $res = sql_query("SELECT * FROM ".$db_prefix."option WHERE option_name='$on'");
        while ($row = sql_fetch_array($res)) {
            $i++;
            if ($reindex) {
                $foo[$i] = $row['option_value'];
            } else {
                $foo[$row['idx']] = $row['option_value'];
            }
        }
        return "<b>$label:</b> ".create_select_form('sublist', $foo, $sublist, '---', 0, 'onchange="update_opt(this.value)"');
    } else {
        return false;
    }
}

require './../includes/admin_init.php';
admin_check('4');

$cmd = post_param('cmd');
$show = post_param('show');
$fid = post_param('fid');
$sublist = post_param('sublist');

if (empty($cmd)) {
    $cmd = get_param('cmd');
}
if (empty($show)) {
    $show = get_param('show');
}
if (empty($fid)) {
    $fid = get_param('fid');
}
if (empty($sublist)) {
    $sublist = get_param('sublist');
}

if (empty($cmd)) {
    $cmd = 'default';
}
if (empty($show)) {
    $show = 10;
}

//
$show_def = array(10 => 10, 15 => 15, 20 => 20, 30 => 30, 50 => 50);

switch ($cmd) {
    case 'del':
     $oid = get_param('oid');
     AXSRF_check();
     sql_query("DELETE FROM ".$db_prefix."option WHERE idx='$oid' LIMIT 1");
     redir($config['site_url']."/$config[admin_folder]/edit_opt.php?fid=$fid&show=$show&sublist=$sublist");
    break;


    case 'save':
     AXSRF_check();
     foreach ($_POST as $key => $val) {
         if (substr($key, 0, 6) == 'value_') {
             $oid = substr($key, 6);

             // update
             if (is_numeric($oid)) {
                 $val = post_param($key);
                 if (!empty($val)) {
                     sql_query("UPDATE ".$db_prefix."option SET option_value='$val' WHERE idx='$oid' LIMIT 1");
                 } else {
                     sql_query("DELETE FROM ".$db_prefix."option WHERE idx='$oid' LIMIT 1");
                 }
             }
             // insert
             else {
                 $val = post_param($key);
                 if (!empty($val)) {
                     if (empty($sublist)) {
                         sql_query("INSERT INTO ".$db_prefix."option SET option_name='$fid', option_value='$val'");
                     } else {
                         sql_query("INSERT INTO ".$db_prefix."option SET option_name='{$fid}_{$sublist}', option_value='$val'");
                     }
                 }
             }
         }
     }
     redir($config['site_url']."/$config[admin_folder]/edit_opt.php?fid=$fid&show=$show&sublist=$sublist");
    break;


    default:
        // load tpl
        $txt['block_list'] = '';
        $tpl = load_tpl('adm', 'edit_opt.tpl');
        $axsrf = AXSRF_value();

        // fill with db
        $i = 0;
        if (empty($sublist)) {
            $res = sql_query("SELECT * FROM ".$db_prefix."option WHERE option_name='$fid' ORDER BY option_value");
        } else {
            $res = sql_query("SELECT * FROM ".$db_prefix."option WHERE option_name='{$fid}_{$sublist}' ORDER BY option_value");
        }
        while ($row = sql_fetch_array($res)) {
            $i++;
            $row['i'] = $i;
            $row['fid'] = $fid;
            $row['show'] = $show;
            $row['sublist'] = $sublist;
            $row['axsrf'] = $axsrf;
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        // fill the rest with blank
        for ($i = $i + 1; $i <= $show; $i++) {
            $row['i'] = $i;
            $row['fid'] = $fid;
            $row['show'] = $show;
            $row['sublist'] = $sublist;
            $row['idx'] = 'new_'.$i;
            $row['option_value'] = '';
            $row['axsrf'] = $axsrf;
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        // output
        $sublist_avail = get_sublist($fid);
        $txt['sublist_select'] = $sublist_avail;
        $txt['show_select'] = create_select_form('show', $show_def, $show, '', 0, 'onchange="this.form.submit()"');
        $txt['sublist'] = $sublist;
        $txt['show'] = $show;
        $txt['fid'] = $fid;
        $txt['main_body'] = quick_tpl(load_tpl('adm', 'edit_opt.tpl'), $txt);
        flush_tpl('adm_popup');
    break;
}
