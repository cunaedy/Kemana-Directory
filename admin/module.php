<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('manage_module');

$cmd = get_param('cmd');
$mod_id = get_param('mod_id');
$module_engine = $config['enable_module_engine'];

switch ($cmd) {
    case 'enable':
     sql_query("UPDATE ".$db_prefix."module SET mod_enabled=1 WHERE mod_id='$mod_id' LIMIT 1");
     redir();
    break;


    case 'disable':
     sql_query("UPDATE ".$db_prefix."module SET mod_enabled=0 WHERE mod_id='$mod_id' LIMIT 1");
     redir();
    break;


    case 'scan':
     // load tpl
     $tpl_mode = 'scan';
     $tpl = load_tpl('adm', 'module.tpl');
     $txt['block_avail'] = '';
     $axsrf = axsrf_value();
     $res = sql_query("SELECT * FROM ".$db_prefix."module ORDER BY mod_name");
     while ($row = sql_fetch_array($res)) {
         $mod_installed[$row['mod_id']]['mod_id'] = $mod_id = $row['mod_id'];
         $mod_installed[$row['mod_id']]['mod_version'] = $row['mod_version'];
     }

     // scan new/existing modue -> admin/module/ folder
     $handle = opendir('./module');
     $mod_avail = array();
     while (false !== ($file = readdir($handle))) {
         if ($file != '.' && $file != '..' && is_dir("./module/$file")) {
             $fn = './module/'.$file.'/ini.xml';
             $xml = read_xml($fn);

             $mod_avail[$file]['axsrf'] = $axsrf;
             $mod_avail[$file]['mod_id'] = $mod_id = $xml['qmodule']['#']['id'][0]['#'];
             $mod_avail[$file]['mod_type'] = $mod_type = empty($xml['qmodule']['#']['type'][0]['#']) ? 'general' : $xml['qmodule']['#']['type'][0]['#'];
             $mod_avail[$file]['mod_name'] = $xml['qmodule']['#']['name'][0]['#'];
             $mod_avail[$file]['mod_version'] = $xml['qmodule']['#']['version'][0]['#'];
             $mod_avail[$file]['mod_license'] = $xml['qmodule']['#']['license'][0]['#'];
             $mod_avail[$file]['mod_copyright'] = $xml['qmodule']['#']['copyright'][0]['#'];
             $mod_avail[$file]['mod_author'] = $xml['qmodule']['#']['author'][0]['#'];
             $mod_avail[$file]['mod_authorUrl'] = $xml['qmodule']['#']['authorUrl'][0]['#'];
             $mod_avail[$file]['mod_authorEmail'] = $xml['qmodule']['#']['authorEmail'][0]['#'];
             $mod_avail[$file]['mod_desc'] = $xml['qmodule']['#']['description'][0]['#'];

             // icon
             if ($mod_type == 'payment') {
                 $mod_avail[$file]['icon'] = '<span class="glyphicon glyphicon-credit-card icon-xl text-primary"></span>';
             } elseif ($mod_type == 'shipping') {
                 $mod_avail[$file]['icon'] = '<span class="glyphicon glyphicon-plane icon-xl text-primary"></span>';
             } else {
                 $mod_avail[$file]['icon'] = '<span class="glyphicon glyphicon-cog icon-xl text-primary"></span>';
             }

             // is it installed?
             if (!empty($mod_installed[$mod_id])) {
                 // different version?
                 if ($mod_installed[$mod_id]['mod_version'] != $mod_avail[$file]['mod_version']) {
                     $lang['l_install'] = 'Upgrade';
                     $txt['block_avail'] .= quick_tpl($tpl_block['avail'], $mod_avail[$file]);
                 }
             } else {
                 $lang['l_install'] = 'Install';
                 $txt['block_avail'] .= quick_tpl($tpl_block['avail'], $mod_avail[$file]);
             }
         }
     }

     closedir($handle);

     // display result (including installed)
     $txt['main_body'] = quick_tpl($tpl, $txt);
     flush_tpl('adm');
    break;


    default:
     // load tpl
     $tpl_mode = 'list';
     $tpl = load_tpl('adm', 'module.tpl');
     $txt['block_list'] = '';

     // get installed modules
     $axsrf = axsrf_value();
     $res = sql_query("SELECT * FROM ".$db_prefix."module ORDER BY mod_type, mod_name");
     while ($row = sql_fetch_array($res)) {
         $fn = './module/'.$row['mod_id'].'/ini.xml';
         $xml = read_xml($fn);
         $mod_id = $row['mod_id'];
         $row['axsrf'] = $axsrf;
         $row['mod_license'] = $xml['qmodule']['#']['license'][0]['#'];
         $row['mod_copyright'] = $xml['qmodule']['#']['copyright'][0]['#'];
         $row['mod_author'] = $xml['qmodule']['#']['author'][0]['#'];
         $row['mod_authorUrl'] = $xml['qmodule']['#']['authorUrl'][0]['#'];
         $row['mod_authorEmail'] = $xml['qmodule']['#']['authorEmail'][0]['#'];

         if ($row['mod_type'] == 'payment') {
             $row['icon'] = '<span class="glyphicon glyphicon-credit-card icon-xl text-primary"></span>';
         } elseif ($row['mod_type'] == 'shipping') {
             $row['icon'] = '<span class="glyphicon glyphicon-plane icon-xl text-primary"></span>';
         } else {
             $row['icon'] = '<span class="glyphicon glyphicon-cog icon-xl text-primary"></span>';
         }

         if ($row['mod_enabled']) {
             $row['mod_enabled'] = "<a href=\"module.php?cmd=disable&amp;mod_id=$mod_id\"  class=\"module_setup\">".
                                  "<span class=\"glyphicon glyphicon-ok icon-l\"></span></a>";
         } else {
             $row['mod_enabled'] = "<a href=\"module.php?cmd=enable&amp;mod_id=$mod_id\" class=\"module_setup\">".
                                  "<span class=\"glyphicon glyphicon-remove icon-l\"></span></a>";
         }
         $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
     }

     // load tpl
     $txt['main_body'] = quick_tpl($tpl, $txt);
     flush_tpl('adm');
    break;
}
