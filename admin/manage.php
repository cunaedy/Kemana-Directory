<?php
// part of qEngine
require './../includes/admin_init.php';

admin_check('manage_module');
$cmd = get_param('cmd');
if (empty($cmd)) {
    $cmd = post_param('cmd');
}

$module_engine = $config['enable_module_engine'];
$module_man = $config['enable_module_man'];

// def
$short = array('T1', 'T2', 'L1', 'L2', 'R1', 'R2', 'B1', 'B2');	// list of positions
$mod_n = 5;	// number of mods per position

// get modules
$mod_def = array();
$res = sql_query("SELECT * FROM ".$db_prefix."module WHERE mod_enabled='1' AND mod_type='general' ORDER BY mod_name");
while ($row = sql_fetch_array($res)) {
    $mod_def[$row['mod_id']] = $row['mod_name'];
}

$tpl = load_tpl('adm', 'manage.tpl');

switch ($cmd) {
    case 'save':
        // create the actual form
        foreach ($short as $k => $m) {
            for ($i = 1; $i <= $mod_n; $i++) {
                $j = 'mod_'.$m.'_'.$i;
                $mod = post_param($j, '', 'html');
                if (!empty($mod)) {
                    $mod_title = addslashes($mod_def[$mod]);
                    sql_query("INSERT INTO ".$db_prefix."module_pos SET mod_id='$mod', mod_title='$mod_title', mod_pos='$m'");
                }
            }
        }

        qcache_clear();
        admin_die('admin_ok');
    break;


    case 'del':
        $idx = get_param('idx');
        sql_query("DELETE FROM ".$db_prefix."module_pos WHERE idx='$idx' LIMIT 1");
        qcache_clear();
        admin_die('admin_ok');
    break;


    case 'edit':
        $idx = get_param('idx');

        $row = sql_qquery("SELECT * FROM ".$db_prefix."module_pos WHERE idx='$idx' LIMIT 1");
        $row['idx'] = $idx;
        $row['mod_config'] = html_unentities(stripslashes($row['mod_config']));
        $txt['main_body'] = quick_tpl($tpl_section['mod_man_edit'], $row);
        flush_tpl('adm_popup');
    break;


    case 'save_config':
        $idx = post_param('idx');
        $mod_title = addslashes(post_param('mod_title'));
        $mod_config = addslashes(post_param('mod_config'));
        sql_query("UPDATE ".$db_prefix."module_pos SET mod_title='$mod_title', mod_config='$mod_config' WHERE idx='$idx' LIMIT 1");
        qcache_clear();
        admin_die('admin_ok');
    break;


    default:
        // arrange modules
        $cur = array();
        foreach ($short as $k => $v) {
            $f = 'mod_'.$v;
            $txt[$f] = '';
            $cur[$v] = 1;
        }

        // what pos?
        $pos = $idx = array();
        $res = sql_query("SELECT * FROM ".$db_prefix."module_pos ORDER BY idx");
        while ($row = sql_fetch_array($res)) {
            $j = $row['mod_pos'].'_'.$cur[$row['mod_pos']];
            $cur[$row['mod_pos']]++;
            $pos[$j] = $row['mod_id'];
            $idx[$j] = $row['idx'];
            $tit[$j] = $row['mod_title'];
        }

        // create the actual form
        foreach ($short as $k => $m) {
            $row = array();
            $txt['mod_'.$m.'_form'] = '';
            for ($i = 1; $i <= $mod_n; $i++) {
                $j = $m.'_'.$i;
                if (empty($pos[$j])) {
                    $pos[$j] = $idx[$j] = $row['mod_name'] = '';
                    $row['mod_select'] = create_select_form('mod_'.$m.'_'.$i, $mod_def, $pos[$j], '[none]');
                } else {
                    $row['idx'] = $idx[$j];
                    $row['mod_name'] = $tit[$j] ? $tit[$j] : $mod_def[$pos[$j]];
                    $row['mod_def'] = $mod_def[$pos[$j]];
                }

                if ((substr($m, 0, 1) == 'L') || (substr($m, 0, 1) == 'R')) {
                    if (empty($pos[$j])) {
                        $txt['mod_'.$m.'_form'] .= quick_tpl($tpl_section['mod_man_ver'], $row);
                    } else {
                        $txt['mod_'.$m.'_form'] .= quick_tpl($tpl_section['mod_man_ver_2'], $row);
                    }
                } else {
                    if (empty($pos[$j])) {
                        $txt['mod_'.$m.'_form'] .= quick_tpl($tpl_section['mod_man_hor'], $row);
                    } else {
                        $txt['mod_'.$m.'_form'] .= quick_tpl($tpl_section['mod_man_hor_2'], $row);
                    }
                }
            }
        }

        $txt['skin_info'] = '../'.$config['skin'].'/info.html';
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
