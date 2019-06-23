<?php
/************************************************
 * Powered with qEngine v16.1 (c) C97.net
 * All rights reserved
 ************************************************/

// check install/
if (file_exists('./install/')) {
    die('If you have just installed Kemana, please delete the <b>"install/"</b> directory on your server before using Kemana v3.2 (build 2017.09.09). Or <a href="install/index.php">'
        .'click here</a> to install Kemana for the first time.');
}

// very important file
require_once "./includes/user_init.php";

// get param?
$cmd = get_param('cmd');

// referral?
// v3.1 only in index.php, other dir which uses index.php should not check for refer
if (!$isPermalink) {
    foreach ($_GET as $k => $v) {
        if (!$v) {
            $cmd = 'refer';
            $uid = filter_param($k);
        }
    }
}

switch ($cmd) {
    case 'refer':
        $usr = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE user_id='$uid' LIMIT 1");
        if ($usr) {
            $ref = $usr['user_refer_history'];
            $ip = get_ip_address();
            if (!strpos('foo,'.$ref, ','.$ip.',')) {
                if (!$ref) {
                    $ref = ',';
                }
                $ref = $ref.$ip.',';

                // Kemana can only stored about 4400 ipv4 address, or 1650 ipv6 address. When Kemana can no longer store ip history, it will remove oldest entries.
                if (strlen($ref) > 65000) {
                    $ref = substr($ref, -65000);
                }
                sql_query("UPDATE ".$db_prefix."user SET user_refer_history='$ref', user_refer_num=user_refer_num+1 WHERE user_id='$uid' LIMIT 1");
            }
        }

        redir($config['site_url']);
    break;


    case 'skin':
        $skin = get_param('skin');
        if (file_exists('./skins/'.$skin.'/outline.tpl')) {
            $_SESSION[$db_prefix.'override_skin'] = $skin;
        }
        redir();
    break;


    case 'viewmode':
        $view_mode = get_param('mode');
        if ($view_mode == 'desktop') {
            $_SESSION[$db_prefix.'view_mode'] = 'desktop';
        } else {
            $_SESSION[$db_prefix.'view_mode'] = 'mobile';
        }
        redir();
    break;


    case 'lang':
        $l = get_param('lang');
        $foo = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$l' LIMIT 1");
        if ($foo) {
            $_SESSION[$db_prefix.'language'] = $l;
        }
        redir();
    break;
}

// demo mode? -- if it is, check if it needs content reset
if (($config['demo_mode']) && ($config['last_autoexec'] != $sql_today)) {
    require './includes/admin_func.php';
    require $config['demo_path'].'/reset.php';
}

// auto exec (this block will be executed daily)
if ($config['last_autoexec'] != $sql_today) {
    // get $ok from /includes/autoexec.php
    $ok = true;
    require './includes/autoexec.php';
    if ($ok) {
        sql_query("UPDATE ".$db_prefix."config SET config_value='$sql_today' WHERE config_id='last_autoexec' LIMIT 1");
    }
}

// affects 10% of visits
if ((mt_rand(1, 10) == 1) && ($config['ke']['backlink_autocheck'])) {
    // automatically verify backlink url
    $burl = sql_qquery("SELECT idx, item_backlink_url FROM ".$db_prefix."listing WHERE item_backlink_ok='' LIMIT 1");
    if ($burl) {
        // verify backlink
        if (verify_backlink($burl['item_backlink_url'])) {
            sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok='1' WHERE idx='$burl[idx]' LIMIT 1");
        } else {
            sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok='0' WHERE idx='$burl[idx]' LIMIT 1");
        }
    } else {
        // reset verification status, aka never ending job
        sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok=''");

        // fix empty backlink
        sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok='0' WHERE item_backlink_url=''");
    }

    // update sort_point
    $res = sql_query("SELECT idx, owner_id, item_update, item_rating, item_votes, user_refer_num FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."user AS t2 ON t1.owner_id=t2.user_id WHERE last_calculated != '$sql_today' LIMIT 100");
    while ($row = sql_fetch_array($res)) {
        $upd = $config['ke']['update_point'] - ($config['ke']['update_multiplier'] * diff_date($row['item_update'], $sql_today));
        if ($upd < 0) {
            $upd = 0;
        }
        $spoint = ($upd) + ($config['ke']['referral_multiplier'] * $row['user_refer_num']) + ($config['ke']['rating_multipier'] * $row['item_rating'] * $row['item_votes']);
        sql_query("UPDATE ".$db_prefix."listing SET item_sort_point='$spoint', last_calculated='$sql_today' WHERE idx='$row[idx]' LIMIT 1");
    }
}

// show directory
if ($isPermalink && $permalink_param) {
    $dir_id = $original_idx;
} else {
    $dir_id = get_param('dir_id');
}
if (empty($dir_id)) {
    $dir_id = $dir_info['config']['default'];
}
if (!array_key_exists($dir_id, $dir_info['structure'])) {
    $dir_id = $dir_info['config']['default'];
}

// show dir cats
get_dir_info($dir_id);
if ($dir_info[$dir_id]['dir_inf']['dir_featured']) {
    $featured_listing = true;
} else {
    $featured_listing = false;
}
$tpl = load_tpl('user', 'welcome_'.$dir_info[$dir_id]['dir_inf']['dir_short'].'.tpl', false);
if (!$tpl) {
    $tpl = load_tpl('welcome.tpl');
}
$_GET['dir_id'] = $txt['dir_id'] = $dir_id;

$txt['block_cat_list'] = '';
$foo = create_cat_list($dir_id);
foreach ($foo as $val) {
    $txt['block_cat_list'] .= quick_tpl($tpl_block['cat_list'], $val);
}

// search form
$txt['dir_select'] = create_select_form('dir_id', $dir_info['structure'], $dir_id);

// reload tpl
$tpl = load_tpl('user', 'welcome_'.$dir_info[$dir_id]['dir_inf']['dir_short'].'.tpl', false);
if (!$tpl) {
    $tpl = load_tpl('welcome.tpl');
}

//
$txt['main_body'] = quick_tpl($tpl, $txt);

generate_html_header($config['site_name'].' '.$config['cat_separator'].' '.$config['site_slogan']);
flush_tpl('_blank');
