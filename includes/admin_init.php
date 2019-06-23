<?php
// part of qEngine
// the following syntaxes will be automatically executed EVERYTIME ADMIN CP ACCESSED

// -- session start --
ini_set('session.use_only_cookies', 1);
session_start();

$in_admin_cp = true;
$lang = $config = $txt = array();
$inc_folder = dirname(__FILE__);
require $inc_folder.'/db_config.php';
require $inc_folder.'/config.php';
require $inc_folder.'/function.php';
require $inc_folder.'/admin_func.php';
require $inc_folder.'/tpl.php';
require $inc_folder.'/xmlize.php';
require $inc_folder.'/qadmin.php';
require $inc_folder.'/init.php';
require $inc_folder.'/vars.php';
require $inc_folder.'/local.php';
load_section('adm', 'section.tpl');

// -- admin init start --
$txt['number_of_online_users'] = 'n/a';

// build module menu
$tpl = load_tpl('adm', 'outline.tpl');
$txt['block_module'] = '';
$xml = read_xml($config['abs_path'].'/'.$config['admin_folder'].'/module/admin_menu.xml', false, false);

if ($xml) {
    $ok = true;
    $i = 0;
    $mi = 1100;
    while ($ok) {
        if (!empty($xml['qmodule']['#']['adminGroup'][$i]['#']['title'][0]['#'])) {
            $mi++;
            $smi = $mi * 10;
            $row['mi'] = $mi;
            $row['title'] = $xml['qmodule']['#']['adminGroup'][$i]['#']['title'][0]['#'];

            $ok2 = true;
            $j = 0;
            $row['subblock_module'] = '';
            while ($ok2) {
                if (!empty($xml['qmodule']['#']['adminGroup'][$i]['#']['adminMenu'][$j]['#']['adminTitle'][0]['#'])) {
                    $smi++;
                    $row['smi'] = $smi;
                    $row['item_title'] = $xml['qmodule']['#']['adminGroup'][$i]['#']['adminMenu'][$j]['#']['adminTitle'][0]['#'];
                    $row['item_url'] = $xml['qmodule']['#']['adminGroup'][$i]['#']['adminMenu'][$j]['#']['adminUrl'][0]['#'];
                    $row['item_url'] = str_replace('&', '&amp;', $row['item_url']);
                    $row['subblock_module'] .= quick_tpl($tpl_subblock['module'], $row);
                    $j++;
                } else {
                    $ok2 = false;
                }
            }
            if (!empty($xml['qmodule']['#']['adminGroup'][$i]['#']['adminMenu'][0]['#']['adminTitle'][0]['#'])) {
                @$txt['block_module'] .= quick_tpl($tpl_block['module'], $row);
            }
            $i++;
        } else {
            $ok = false;
        }
    }
} else {
    $row = array('title' => 'WARNING!', 'item_url' => '#', 'item_title' => 'Can not load /'.$config['admin_folder'].'/module/admin_menu.xml');
    $row['subblock_module'] = quick_tpl($tpl_subblock['module'], $row);
    $txt['block_module'] = quick_tpl($tpl_block['module'], $row);
}
