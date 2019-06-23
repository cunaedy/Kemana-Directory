<?php
// part of qEngine
require_once "./user_init.php";
AXSRF_check();

// get param
if (!$isLogin) {
    msg_die($lang['msg']['not_member']);
}
$user_passwd = post_param('user_passwd');
$new_user_passwd = post_param('new_user_passwd');
$confirm_new_user_passwd = post_param('confirm_new_user_passwd');
$user_email = post_param('user_email');
$err = array();

// get current password & email
$current = sql_qquery("SELECT user_id, user_passwd, user_email FROM ".$db_prefix."user WHERE user_id = '$current_user_id' LIMIT 1");

// update email?
if ($current['user_email'] != $user_email) {
    // verify entry & password
    if (empty($user_passwd)) {
        $err[] = $lang['l_password_empty'];
    }
    if (qhash($user_passwd) != $current['user_passwd']) {
        $err[] = $lang['l_password_error'];
    }
    if (!validate_email_address($user_email)) {
        $err[] = $lang['l_email_empty'];
    }

    // email exists?
    $row = sql_qquery("SELECT user_email FROM ".$db_prefix."user WHERE user_email = '$user_email' LIMIT 1");
    if (!empty($row['email'])) {
        $err[] = $lang['l_email_used'];
    }

    // error
    if (!empty($err)) {
        msg_die(sprintf($lang['msg']['update_error'], '<ul><li>'.implode('</li><li>', $err).'</li></ul>'));
    }

    // success
    sql_query("UPDATE ".$db_prefix."user SET user_email = '$user_email' WHERE user_id = '$current_user_id'");
}


// change passwd?
if (!empty($new_user_passwd) && !empty($confirm_new_user_passwd) && !empty($user_passwd) &&
   (qhash($user_passwd) == $current['user_passwd']) && ($new_user_passwd == $confirm_new_user_passwd)) {
    $current['user_passwd'] = $new_user_passwd;
    $current['site_name'] = $config['site_name'];
    $current['site_url'] = $config['site_url'];
    $new_user_passwd = qhash($new_user_passwd);
    sql_query("UPDATE ".$db_prefix."user SET user_passwd = '$new_user_passwd' WHERE user_id = '$current_user_id' LIMIT 1");
    email($current['user_email'], $lang['l_passwd_changed_subject'], quick_tpl(load_tpl('mail', 'change_pwd'), $current), true);
    kick_user('pwd');
}

redir($config['site_url'].'/account.php');
