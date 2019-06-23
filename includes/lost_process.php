<?php
// part of qEngine
require_once "./user_init.php";

$user_id = post_param('user_id');
$reset = post_param('reset');
$user_passwd = post_param('user_passwd');
$visual = qhash(strtolower(post_param('qvc')));
$do_reset = post_param('do_reset');

if ($do_reset) {
    // check entry
    $err = array();
    if ($visual != qvc_value()) {
        msg_die($lang['msg']['captcha_error']);
    }
    if (empty($user_id) || empty($reset) || empty($user_passwd)) {
        $err[] = 'Invalid entries';
    }

    // get user id
    $row = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE user_id='$user_id' LIMIT 1");
    if (empty($row['user_id']) || empty($row['reset_passwd']) || ($row['reset_passwd'] != $reset)) {
        msg_die(sprintf($lang['msg']['reset_error'], '<ul><li>'.implode('</li><li>', $err).'</li></ul>'));
    }
    $user_passwd = qhash($user_passwd);
    sql_query("UPDATE ".$db_prefix."user SET user_passwd = '$user_passwd', reset_passwd = '' WHERE user_id = '$user_id' LIMIT 1");

    // hurray!
    redir($config['site_url'].'/profile.php?mode=login');
} else {
    if ($visual != qvc_value()) {
        msg_die($lang['msg']['captcha_error']);
    }

    // get user id
    $row = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE (user_id='$user_id' OR user_email='$user_id') LIMIT 1");
    if (empty($row['user_id'])) {
        msg_die($lang['msg']['lost_error']);
    }

    // create code
    $reset = random_str(16, false, 2);
    sql_query("UPDATE ".$db_prefix."user SET reset_passwd = '$reset' WHERE user_id = '$user_id' LIMIT 1");

    // create email
    $row['reset'] = $reset;
    $row['site_url'] = $config['site_url'];
    $row['site_name'] = $config['site_name'];
    $body = quick_tpl(load_tpl('mail', 'lost'), $row);
    email($row['user_email'], sprintf($lang['l_mail_lost_subject'], $config['site_name']), $body, true, true);

    msg_die($lang['msg']['lost_ok']);
}
