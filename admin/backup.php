<?php
// part of qEngine
function output($string)
{
    global $fp, $gzip;
    if ($gzip) {
        gzwrite($fp, $string);
    } else {
        fwrite($fp, $string);
    }
    return strlen($string);
}

require_once "./../includes/admin_init.php";
admin_check('site_setting');

$cmd = get_param('cmd');
$gzip = get_param('gzip');

// table list
$table_prefix = $db_prefix;
$len_prefix = strlen($table_prefix);

switch ($cmd) {
    case 'do_backup':
     // demo mode?
     if ($config['demo_mode']) {
         die('Demo Mode! Back up is cancelled!');
     }
     html_header();
     echo '<div style="float:left; width:80px"<img src="../skins/_common/images/loading.gif" alt="loading" /></div>';
     echo '<div style="float:left; width:100%; overflow:auto; height:200px"><p>Backing up...</p>';

     // -- start backup process
     $fn = 'backup/Full Backup '.date("Ymd").'_'.random_str(3);
     if ($gzip) {
         $fp = gzopen($fn.'.gz', 'w9');
         $full_fn = $fn.'.gz';
     } else {
         $fp = fopen($fn.'.sql', 'w');
         $full_fn = $fn.'.sql';
     }

     // header
     $gen_date = date("D M j Y, G:i:s T");
     $counter = output("# SimpleBackup (c) C97.net\n# Database backup created at $gen_date\n#\n\n");

     // get table list
     $res = sql_query("SHOW TABLES");
     while ($row = sql_fetch_array($res)) {
         $t = $row[0];
         if (substr($t, 0, $len_prefix) == $table_prefix) {
             $tables[] = $t;
         }
     }

     // backup per table
     foreach ($tables as $key => $val) {
         $cur_table = $val;
         $ep = $ed = array();
         echo '[',$cur_table,']';

         // 1 -- get table's structure
         $counter += output("#\n# Table structure for $cur_table\n#\n\n");
         $counter += output("DROP TABLE IF EXISTS `$cur_table`;\n\n");

         $res = sql_query("SHOW CREATE TABLE $cur_table");

         $row = sql_fetch_array($res);
         output($row['Create Table'].";\n");

         // 2 -- get table's data
         $counter += output("\n#\n# Table data for $cur_table\n#\n\n");

         $res = sql_query("SELECT * FROM $cur_table");
         while ($row = mysqli_fetch_row($res)) {
             @set_time_limit(1);
             $cmd = "INSERT INTO $cur_table VALUES (";
             foreach ($row as $k => $v) {
                 $cmd .= "'".addslashes($v)."', ";
             }
             $cmd = substr($cmd, 0, -2);
             $counter += output($cmd.");\n");

             echo '. ';
         }

         $counter += output("\n");
         @set_time_limit(5);
         echo '<br />';
     }

     // 3 -- close
     output("\n#\n# End of file\n#");
     if ($gzip) {
         gzclose($fp);
     } else {
         fclose($fp);
     }

     // 4 -- redir
     echo '</div>';
     echo '<h2 style="clear:both">Done</h2><p>Database backed up to <b><a href="'.$full_fn.'">/'.$config['admin_folder'].'/'.$full_fn.'</a></b>. Please close this window.</p>';
     html_footer();
     die;
    break;

    default:
     $row['db_prefix'] = $db_prefix;
     $txt['main_body'] = quick_tpl(load_tpl('adm', 'backup.tpl'), $row);
     flush_tpl('adm');
    break;
}
