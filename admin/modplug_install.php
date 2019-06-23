<?php
// part of qEngine
// create admin menu for modules
function generate_admin_menu($what)
{
    global $db_prefix, $tpl_block, $tpl_subblock;
    global $db_name, $db_field;

    // load tpl

    $txt['block_menu'] = '';

    $tpl = load_tpl('adm', 'mod_menu.xml.tpl');
    $res = sql_query("SELECT * FROM ".$db_prefix."module ORDER BY mod_name");
    $fn_output = './module/admin_menu.xml';

    while ($row = sql_fetch_array($res)) {
        if ($what == 'module') {
            $id = $row['mod_id'];
        } else {
            $id = $row['plug_id'];
        }
        $fn = "./module/$id/ini.xml";
        $xml = read_xml($fn);

        $i = 0;
        $ok = true;
        $row['subblock_menu'] = '';
        while ($ok) {
            if (!empty($xml['qmodule']['#']['adminMenu'][$i]['#']['adminTitle'][0]['#'])) {
                $row['id'] = $id;
                $row['menu_title'] = $xml['qmodule']['#']['adminMenu'][$i]['#']['adminTitle'][0]['#'];
                $row['menu_url'] = $xml['qmodule']['#']['adminMenu'][$i]['#']['adminUrl'][0]['#'];
                $row['subblock_menu'] .= quick_tpl($tpl_subblock['menu'], $row);
                $i++;
            } else {
                $ok = false;
            }
        }

        // create menu
        $row['name'] = $xml['qmodule']['#']['name'][0]['#'];
        $txt['block_menu'] .= quick_tpl($tpl_block['menu'], $row);
    }

    $output = quick_tpl($tpl, $txt);
    $fp = fopen($fn_output, 'w');
    fwrite($fp, $output);
    fclose($fp);
}


require './../includes/admin_init.php';
admin_check(4);

$what = get_param('what');
$cmd = get_param('cmd');
$id = get_param('id');
if (empty($id)) {
    $id = get_param('mod_id');
}

switch ($cmd) {
    case 'confirm_uninstall':
    case 'ask_uninstall':
     $fn = "./module/$id/ini.xml";
     $xml = read_xml($fn);

     $lang['l_modplug'] = 'module';
     $row['name'] = $xml['qmodule']['#']['name'][0]['#'];
     $row['id'] = $id;
     $row['what'] = $what;
     $tpl = load_tpl('adm', 'modplug_uninstall.tpl');
     $txt['main_body'] = quick_tpl($tpl, $row);
     flush_tpl('adm');
    break;


    case 'uninstall':
     // demo mode?
     if ($config['demo_mode']) {
         admin_die('demo_mode');
     }
     AXSRF_check();

     $remove_db = get_param('remove_db');
     $remove_file = get_param('remove_file');

     // see if it installed
     $row = sql_qquery("SELECT * FROM ".$db_prefix."module WHERE mod_id = '$id' LIMIT 1");
     if (empty($row)) {
         admin_die($lang['msg']['mod_not_installed']);
     }

     // is custom uninstall, uninstall.php, exists?
     if (file_exists("./module/$id/uninstall.php")) {
         include "./module/$id/uninstall.php";
     } else {
         $fn = "./module/$id/ini.xml";
         $xml = read_xml($fn);

         // remove file
         if ($remove_file) {
             $i = 0;
             $ok = true;
             while ($ok) {
                 if (!empty($xml['qmodule']['#']['uninstall']['0']['#']['file'][$i]['#'])) {
                     $fn = $xml['qmodule']['#']['uninstall']['0']['#']['file'][$i]['#'];
                     @unlink($fn);
                     $i++;
                 } else {
                     $ok = false;
                 }
             }

             // remove folder & its contents
             $i = 0;
             $ok = true;
             while ($ok) {
                 if (!empty($xml['qmodule']['#']['uninstall'][0]['#']['folder'][$i]['#'])) {
                     $fold = $xml['qmodule']['#']['uninstall'][0]['#']['folder'][$i]['#'];
                     $fl = get_file_list($fold);
                     foreach ($fl as $val) {
                         @unlink($fold.'/'.$val);
                     }
                     @rmdir($fold);
                     $i++;
                 } else {
                     $ok = false;
                 }
             }
         }

         // execute query
         if ($remove_db) {
             $sql = empty($xml['qmodule']['#']['uninstall'][0]['#']['query'][0]['#']) ? '' : $xml['qmodule']['#']['uninstall'][0]['#']['query'][0]['#'];
             splitSqlFile($cmd, $sql);
             foreach ($cmd as $val) {
                 // replace __PREFIX__ with $db_prefix
                 $val = str_replace('__PREFIX__', $db_prefix, $val);
                 sql_query($val);
             }
         }
     }

     // remove config from db
     sql_query("DELETE FROM ".$db_prefix."config WHERE group_id='mod_$id'");

     // remove module from db
     sql_query("DELETE FROM ".$db_prefix."module WHERE mod_id='$id' LIMIT 1");

     // rebuild menu
     generate_admin_menu($what);
     admin_die($lang['msg']['mod_uninstall_ok'], $config['site_url'].'/'.$config['admin_folder'].'/module.php?cmd=scan');
    break;


    case 'install':
     // demo mode?
     if ($config['demo_mode']) {
         admin_die('demo_mode');
     }
     AXSRF_check();

     // see if it installed
     $row = sql_qquery("SELECT * FROM ".$db_prefix."module WHERE mod_id = '$id' LIMIT 1");
     if (!empty($row)) {
         admin_die('mod_installed');
     }

     // is custom install, install.php, exists?
     if (file_exists("./module/$id/install.php")) {
         include "./module/$id/install.php";
     } else {
         $fn = "./module/$id/ini.xml";
         $xml = read_xml($fn);

         // create folder
         $i = 0;
         $ok = true;
         while ($ok) {
             if (!empty($xml['qmodule']['#']['install'][0]['#']['folder'][$i]['#'])) {
                 $fold = $xml['qmodule']['#']['install'][0]['#']['folder'][$i]['#'];
                 @mkdir($fold);
                 $i++;
             } else {
                 $ok = false;
             }
         }

         // create file
         $i = 0;
         $ok = true;
         while ($ok) {
             if (!empty($xml['qmodule']['#']['install']['0']['#']['file'][$i]['#'])) {
                 $fn = $xml['qmodule']['#']['install']['0']['#']['file'][$i]['#'];
                 $fp = @fopen($fn, 'w');
                 fclose($fp);
                 $i++;
             } else {
                 $ok = false;
             }
         }

         // include files
         $i = 0;
         $ok = true;
         $css = array();
         while ($ok) {
             if (!empty($xml['qmodule']['#']['include']['0']['#']['css'][$i]['#'])) {
                 $css[] = $xml['qmodule']['#']['include']['0']['#']['css'][$i]['#'];
                 $i++;
             } else {
                 $ok = false;
             }
         }

         $i = 0;
         $ok = true;
         $js = array();
         while ($ok) {
             if (!empty($xml['qmodule']['#']['include']['0']['#']['js'][$i]['#'])) {
                 $js[] = $xml['qmodule']['#']['include']['0']['#']['js'][$i]['#'];
                 $i++;
             } else {
                 $ok = false;
             }
         }

         // execute query
         $sql = $xml['qmodule']['#']['install'][0]['#']['query'][0]['#'];
         splitSqlFile($cmd, $sql);
         foreach ($cmd as $val) {
             // replace __PREFIX__ with $db_prefix
             $val = str_replace('__PREFIX__', $db_prefix, $val);
             sql_query($val);
         }


         // insert lang
         $i = 0;
         $ok = true;
         while ($ok) {
             if (!empty($xml['qmodule']['#']['language'][$i]['#'])) {
                 $lang_id = $xml['qmodule']['#']['language'][$i]['#']['id'][0]['#'];
                 $j = 0;
                 $okj = true;
                 while ($okj) {
                     if (!empty($xml['qmodule']['#']['language'][$i]['#']['lang_key'][$j]['#'])) {
                         $foo = $xml['qmodule']['#']['language'][$i]['#'];
                         $lk = $foo['lang_key'][$j]['#'];
                         $lv = $foo['lang_value'][$j]['#'];
                         add_new_language($lang_id, $lk, $lv);
                         $j++;
                     } else {
                         $okj = false;
                     }
                 }
                 $i++;
             } else {
                 $ok = false;
             }
         }
     }

     // insert config
     $i = 0; $ok = true;
     while ($ok) {
         if (!empty($xml['qmodule']['#']['config'][0]['#']['configId'][$i]['#'])) {
             $cid = $xml['qmodule']['#']['config'][0]['#']['configId'][$i]['#'];
             $cval = $xml['qmodule']['#']['config'][0]['#']['configValue'][$i]['#'];
             sql_query("INSERT INTO ".$db_prefix."config VALUES ('mod_$id', '$cid', '$cval')");
             $i++;
         } else {
             $ok = false;
         }
     }

     // update db
     $name = $xml['qmodule']['#']['name'][0]['#'];
     $desc = $xml['qmodule']['#']['description'][0]['#'];
     $version = $xml['qmodule']['#']['version'][0]['#'];
     $type = empty($xml['qmodule']['#']['type'][0]['#']) ? 'general' : $xml['qmodule']['#']['type'][0]['#'];
     $css = implode("\n", $css);
     $js = implode("\n", $js);

     sql_query("INSERT INTO ".$db_prefix."module SET mod_id='$id', mod_type='$type', mod_name='$name', mod_desc='$desc', mod_version='$version', mod_css='$css', mod_js='$js', mod_enabled='1'");

     // rebuild menu
     generate_admin_menu($what);
     admin_die($lang['msg']['mod_install_ok'], $config['site_url'].'/'.$config['admin_folder'].'/module.php?cmd=scan');
    break;
}
