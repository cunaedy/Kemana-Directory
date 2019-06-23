<?php
// part of qEngine
require "./user_init.php";

$user_id = post_param('user_id');
$user_passwd = qhash(post_param('user_passwd'));
$visual = post_param('qvc');
$ip = get_ip_address();

// get user id
if (qvc_value() != qhash(strtolower($visual))) {
    msg_die($lang['msg']['captcha_error']);
}
$row = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE (user_id='$user_id' OR user_email='$user_id') AND user_passwd = '$user_passwd' AND user_activation = '' AND (admin_level = '0' OR admin_level='') LIMIT 1");
if (empty($row['user_id'])) {
    sql_query("INSERT INTO ".$db_prefix."ip_log VALUES ('', UNIX_TIMESTAMP(), '$ip', '$user_id', 'U', '0')");
    msg_die($lang['msg']['bad_login']);
}

// everything seems OK
authorize_user($user_id, $user_passwd);

// log
sql_query("INSERT INTO ".$db_prefix."ip_log VALUES ('', UNIX_TIMESTAMP(), '$ip', '$user_id', 'U', '1')");

// redir
// send url in safe_receive to ip_config table, field: redir
$url = ip_config_value('redir');
if (!empty($url)) {
    ip_config_update('redir', '');
    redir($url);
} else {
    redir($config['site_url'].'/account.php');
}
