<?php
// part of qEngine
require_once "./includes/user_init.php";

// close site?
if (!$isLogin) {
    redir($config['site_url'].'/profile.php?mode=login');
}

$cmd = get_param('cmd');
$item_id = get_param('item_id');
switch ($cmd) {
    case 'fave_del':
        // verify item_id
        $row = sql_qquery("SELECT idx FROM ".$db_prefix."listing WHERE idx='$item_id' LIMIT 1");
        if (!$row) {
            msg_die($lang['msg']['item_not_found']);
        }
        $current_f = explode(',', $current_user_info['user_fave']);
        // array_unshift ($current_f, 'foo');
        $j = array_search($item_id, $current_f);
        if ($j !== false) {
            unset($current_f[$j]);
        }
        $current_f = array_clean(array_unique($current_f));
        $current = implode(',', $current_f);

        sql_query("UPDATE ".$db_prefix."user SET user_fave='$current' WHERE user_id='$current_user_id' LIMIT 1");
        redir();
    break;


    case 'fave_add':
        // verify item_id
        $row = sql_qquery("SELECT idx FROM ".$db_prefix."listing WHERE idx='$item_id' LIMIT 1");
        if (!$row) {
            msg_die($lang['msg']['item_not_found']);
        }
        $current_f = explode(',', $current_user_info['user_fave']);
        $current_f[] = $item_id;
        $current_f = array_clean(array_unique($current_f));
        $current = implode(',', $current_f);

        sql_query("UPDATE ".$db_prefix."user SET user_fave='$current' WHERE user_id='$current_user_id' LIMIT 1");
        redir();
    break;


    case 'fave':
        $txt['main_body'] = quick_tpl(load_tpl('fave.tpl'), $txt);
        generate_html_header("$config[site_name] $config[cat_separator] My Favorites");
        flush_tpl();
    break;


    case 'listing':
        $txt['main_body'] = quick_tpl(load_tpl('listing_my.tpl'), $txt);
        generate_html_header("$config[site_name] $config[cat_separator] My Listing");
        flush_tpl();
    break;


    default:
        $txt['main_body'] = quick_tpl(load_tpl('account.tpl'), $txt);
        generate_html_header("$config[site_name] $config[cat_separator] My Account");
        flush_tpl();
    break;
}
