<?php
// part of qEngine
require_once "./../includes/admin_init.php";
admin_check('site_setting');

// demo mode?
if ($config['demo_mode']) {
    admin_die('demo_mode');
}

echo '<h1>Please wait...</h1>';
echo '<div style="overflow:auto; width:100%; height:33%">';

$fn = get_param('fn');
$fn = 'backup/'.$fn;
$start = getmicrotime();

// do IT!
if (!file_exists($fn)) {
    admin_die($lang['msg']['fman_not_found']);
}

// eg: backup/MY_VERY_OWN_BACKUP_69.sql.gz
unset($sql);
unset($cmd);
$sql = ''; $line = 0;
$cmd = array();
$zp = gzopen($fn, "r");
while ($j = gzgets($zp, 4096)) {
    $sql .= $j;
}
gzclose($zp);
splitSqlFile($cmd, $sql);
foreach ($cmd as $key => $val) {
    $line++;
    if ($line % 10 == 0) {
        echo '. ';
    }
    @set_time_limit(1);
    sql_query($val);
}
$finish = getmicrotime();
$time = $finish - $start;
echo '</div>';
echo '<h2>Done!</h2>';
echo num_format($line),' lines in ',num_format($time, 5),' seconds.<br />';
echo 'Please close this window.';
