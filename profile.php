<?php
// part of qEngine
require "./includes/user_init.php";

$mode = get_param('mode');

switch ($mode) {
    case 'logout':
        kick_user();
    break;


    case 'lost':
        qvc_init(3);
        $tpl_mode = 'lost';
        $txt['main_body'] = quick_tpl(load_tpl('lost.tpl'), $txt);
        generate_html_header("$config[site_name] $config[cat_separator] Lost Password");
    break;


    case 'reset':
        qvc_init(3);
        $tpl_mode = 'reset';
        $row['user_id'] = get_param('user_id');
        $row['reset'] = get_param('reset');
        $txt['main_body'] = quick_tpl(load_tpl('lost.tpl'), $row);
        generate_html_header("$config[site_name] $config[cat_separator] Reset Password");
    break;


    case 'register':
        qvc_init(3);
        if ($isLogin) {
            redir($config['site_url'].'/profile.php');
        }
        if (!$row = load_form('register')) {
            $row = create_blank_tbl($db_prefix.'user');
        }
    
        $txt = array_merge($txt, $row);
        $txt['main_body'] = quick_tpl(load_tpl('register.tpl'), $txt);
        
        generate_html_header("$config[site_name] $config[cat_separator] Registration");
    break;


    case 'act':
        $row['user_id'] = get_param('user_id');
        $row['act'] = get_param('act');
        $txt['main_body'] = quick_tpl(load_tpl('act.tpl'), $row);
        generate_html_header("$config[site_name] $config[cat_separator] Account Activation");
    break;


    default:
        if (!$isLogin) {
            // login form
            qvc_init(3);
            $profile_mode = 'login';
            $redir = get_param('redir');
            if (!empty($redir)) {
                ip_config_update('redir', html_unentities($redir));
            }
            $txt['main_body'] = quick_tpl(load_tpl('login.tpl'), $txt);
            generate_html_header("$config[site_name] $config[cat_separator] Login");
        } else {
            // get ID
            $res = sql_query("SELECT * FROM ".$db_prefix."user WHERE user_id = '$current_user_id' LIMIT 1");
            $row = sql_fetch_array($res);
            $txt['main_body'] = quick_tpl(load_tpl('profile.tpl'), $row);
            generate_html_header("$config[site_name] $config[cat_separator] My Profile");
        }
    break;
}

flush_tpl();
