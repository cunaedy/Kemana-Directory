<?php
function kickme($log = true)
{
    global $db_prefix, $txt;
    $ip = get_ip_address();
    $admin_id = post_param('user_id');
    if ($log) {
        sql_query("INSERT INTO ".$db_prefix."ip_log VALUES ('', UNIX_TIMESTAMP(), '$ip', '$admin_id', 'A', '0')");
    }
    qvc_init();
    echo quick_tpl(load_tpl('adm', 'login.tpl'), $txt);
    die;
}

// part of qEngine
require './../includes/admin_init.php';
$admin_id = post_param('user_id');
$admin_passwd = post_param('user_passwd');
$redir = post_param('redir');
if (empty($redir)) {
    $redir = get_param('redir');
}
$visual = strtolower(post_param('visual'));
$admin_passwd = qhash(post_param('user_passwd'));

// if form not filled OR visual incorrect
if (empty($admin_id) || empty($admin_passwd)  || empty($visual)) {
    kickme(false);
}
if (qhash($visual) != qvc_value()) {
    kickme();
}

// verify
$row = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE user_id='$admin_id' LIMIT 1");
$level = $row['admin_level'];
if (!$level) {
    kickme();
}
if (($row['user_id'] != $admin_id) || ($row['user_passwd'] != $admin_passwd)) {
    kickme();
}

// everything seems OK
authorize_user($admin_id, $admin_passwd);

// log
$ip = get_ip_address();
sql_query("INSERT INTO ".$db_prefix."ip_log VALUES ('', UNIX_TIMESTAMP(), '$ip', '$admin_id', 'A', '1')");

// redir
$redir = urldecode($redir);
if (!strpos('.'.$redir, $config['site_url'])) {
    $redir = $config['site_url'].'/'.$config['admin_folder'].'/index.php';
}
redir($redir);
kickme();
