<?php
// part of qEngine
require "./user_init.php";

// ambil param
$cmd = post_param('cmd');
$user_id = post_param('user_id');
$user_email = post_param('user_email');
$user_passwd = post_param('user_passwd');
$visual = post_param('visual');

$err = array();
save_form('register');

// visual confirmation
if (empty($visual) || qhash(strtolower($visual)) != qvc_value()) {
    $err[] = $lang['l_captcha_error'];
}

// get username in db
$row = sql_qquery("SELECT user_id FROM ".$db_prefix."user WHERE user_id='$user_id' LIMIT 1");
if (!empty($row['user_id'])) {
    $err[] = "$lang[l_username_used]";
}

// get email in db
$row = sql_qquery("SELECT user_email FROM ".$db_prefix."user WHERE user_email='$user_email' LIMIT 1");
if (!empty($row['user_email'])) {
    $err[] = "$lang[l_email_used]";
}

// validate entries
if (!preg_match("/^[[:alnum:]]+$/", $user_id)) {
    $err[] = "$lang[l_username_error]";
}
if (!validate_email_address($user_email)) {
    $err[] = "$lang[l_email_error]";
}
if (empty($user_passwd)) {
    $err[] = "$lang[l_password_empty]";
}

// if error -> HALT!
if (!empty($err)) {
    msg_die(sprintf($lang['msg']['register_error'], '<ul><li>'.implode('</li><li>', $err).'</li></ul>'));
}

// if success -> SAVE to db
reset_form();
$user_passwd = qhash($user_passwd);
sql_query("INSERT INTO ".$db_prefix."user SET user_id='$user_id', user_passwd='$user_passwd', user_email='$user_email', user_level='1', user_since='$sql_today'");

// if user_activation required -> add user activation code
$row['act'] = ''; $act = false;
if ($config['user_activation']) {
    $row['act'] = $act = random_str(16);
    sql_query("UPDATE ".$db_prefix."user SET user_activation='$act' WHERE user_id='$user_id' LIMIT 1");
}

// send email
$row['user_id'] = $user_id;
$row['user_passwd'] = post_param('user_passwd');
$row['user_email'] = $user_email;
$row['site_name'] = $config['site_name'];
$row['site_url'] = $config['site_url'];

$body = quick_tpl(load_tpl('mail', 'register'), $row);
email($user_email, sprintf($lang['l_mail_register'], $config['site_name']), $body, true);

// if user_activation -> tell to activate
if ($config['user_activation']) {
    msg_die($lang['msg']['user_act'], $config['site_url']);
} else {
    // everything seems OK
    authorize_user($user_id, $user_passwd);

    // redir
    $url = ip_config_value('redir');
    if (!empty($url)) {
        ip_config_update('redir', '');
        msg_die($lang['msg']['register_ok'], $url);
    } else {
        msg_die($lang['msg']['register_ok'], $config['site_url']);
    }
}
