<?php
require_once './user_init.php';

// get param
$user_id = post_param('user_id');
$act = post_param('act');

$err = array();

// validate entries
// get user id in db
$row = sql_qquery("SELECT user_id, user_passwd, user_activation FROM ".$db_prefix."user WHERE user_id='$user_id' LIMIT 1");
if (empty($row['user_id'])) {
    $err[] = 'User ID not found';
}
if (empty($row['user_activation'])) {
    $err[] = 'This account has been activated';
}
if (empty($act) || $act != $row['user_activation']) {
    $err[] = 'Invalid activation key';
}

// if error -> HALT!
if (!empty($err)) {
    msg_die(sprintf($lang['msg']['act_error'], '<ul><li>'.implode('</li><li>', $err).'</li></ul>'));
}

// if success => remove act_key
sql_query("UPDATE ".$db_prefix."user SET user_activation = ''");

// set cookies
setcookie($db_prefix.'user_id', $user_id, time()+31536000, "/");
setcookie($db_prefix.'password', $row['user_passwd'], time()+31536000, "/");
redir($config['site_url']);
