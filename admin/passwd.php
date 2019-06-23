<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(1);

$cmd = post_param('cmd');
if (empty($cmd)) {
    $cmd = get_param('cmd');
}

switch ($cmd) {
    case 'change':
        // demo mode?
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }

        AXSRF_check();
        $curr_passwd = post_param('curr_passwd');
        $new_passwd = post_param('new_passwd');
        if (empty($new_passwd)) {
            admin_die($lang['l_password_empty']);
        }

        // verify password
        $row = sql_qquery("SELECT user_passwd FROM ".$db_prefix."user WHERE user_id='$current_user_id' LIMIT 1");
        $hashed = qhash($curr_passwd);

        if ($row['user_passwd'] == $hashed) {
            $new_passwd = qhash($new_passwd);
            sql_query("UPDATE ".$db_prefix."user SET user_passwd='$new_passwd' WHERE user_id='$current_user_id' LIMIT 1");
            redir($config['site_url'].'/'.$config['admin_folder'].'/login.php');
        }
        admin_die($lang['l_password_error']);
    break;


    default:
        $txt['main_body'] = quick_tpl(load_tpl('adm', 'passwd.tpl'), $txt);
        flush_tpl('adm');
    break;
}
