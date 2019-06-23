<?php
// part of qEngine
// the following syntaxes will be automatically executed EVERYTIME (both for user & admin UI)

// debug info
if (function_exists('memory_get_usage')) {
    $memory_when_start = memory_get_usage();
} else {
    $memory_when_start = 0;
}
$config['time_start'] = getmicrotime();			// start time

// browser's cache control
if ($config['disable_browser_cache']) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
    header('Expires: -1');
}

// user init start
$current_user_info = isMember();
if ($current_user_info) {
    $isLogin = true;
    $current_user_id = $txt['current_user_id'] = $current_user_info['user_id'];
    $current_user_level = $current_user_info['user_level'];
    $current_admin_level = $current_user_info['admin_level'];
} else {
    $isLogin = false;
    $current_user_id = session_param($db_prefix.'user_id');
    $current_user_level = $current_admin_level = 0;
}

// preload
srand(make_seed());
mt_srand(make_seed());

// other variables
$isPermalink = $permalink_param = false;
$sql_today = date('Y-m-d');
$sql_now = date('Y-m-d H:i:s');

// system message
$sys_msg = ip_config_value('system_msg');
if (!empty($sys_msg)) {
    if (substr($sys_msg, 0, 6) == 'mini//') {
        $sys_msg = substr($sys_msg, 6);
        $txt['system_message'] = $sys_msg;
        $mini_message = true;
        ip_config_update('system_msg', '');
    } elseif (substr($sys_msg, 0, 4) != 'MSG|') {
        $system_message = true;
        $txt['system_message'] = $sys_msg;
        ip_config_update('system_msg', '');
    } else {
        $system_message = false;
    }
}

// init module
$txt['module_css_list'] = $txt['module_js_list'] = '';
if ($config['enable_module_engine']) {
    // Get enabled modules
    $res = sql_query("SELECT * FROM ".$db_prefix."module WHERE mod_type='general'");
    while ($row = sql_fetch_array($res)) {
        // Add css & js if necessary
        $module_enabled[$row['mod_id']] = $row['mod_enabled'];
        $module_css_list = $module_js_list = array();
        $css = explode("\n", $row['mod_css']);
        $js = explode("\n", $row['mod_js']);
        foreach ($css as $k => $v) {
            if (!empty($v)) {
                $module_css_list[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$config[site_url]/skins/_module/$v\" />";
            }
        }
        foreach ($js as $k => $v) {
            if (!empty($v)) {
                $module_js_list[] = "<script type=\"text/javascript\" src=\"$config[site_url]/skins/_module/$v\"></script>";
            }
        }
        if (!empty($module_css_list)) {
            $txt['module_css_list'] .= implode("\n", $module_css_list)."\n";
        }
        if (!empty($module_js_list)) {
            $txt['module_js_list'] .= implode("\n", $module_js_list)."\n";
        }
    }
}


// READ LANGUAGE DB (ENGLISH IS REQUIRED)
$lang = array();

if ($config['default_lang'] == 'en') {
    $site_lang = array('en');
} else {
    $config['multi_lang'] = true;
    $foo = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$config[default_lang]' AND lang_key='_config:cache' LIMIT 1");
    if (!$foo['lang_value']) {
        $site_lang = array('en', $config['default_lang']);
    } else {
        $lang = unserialize(gzuncompress(base64_decode($foo['lang_value'])));
        $site_lang = array();
    }
}

foreach ($site_lang as $skv) {
    $lang['l_lang_id'] = $skv;
    $foo = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$skv' AND lang_key='_config:cache' LIMIT 1");
    if (!$foo['lang_value']) {
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$skv'");
        while ($row = sql_fetch_array($res)) {
            if ($row['lang_key'][0] != '_') {
                $f = explode('.', $row['lang_key']);
                if (!empty($f[2])) {
                    $lang[$f[0]][$f[1]][$f[2]] = str_replace('__SITE__', $config['site_url'], $row['lang_value']);
                } elseif (!empty($f[1])) {
                    $lang[$f[0]][$f[1]] = str_replace('__SITE__', $config['site_url'], $row['lang_value']);
                } else {
                    $lang[$row['lang_key']] = str_replace('__SITE__', $config['site_url'], $row['lang_value']);
                }
            }
        }
        $c = base64_encode(gzcompress(serialize($lang)));
        sql_query("UPDATE ".$db_prefix."language SET lang_value='$c' WHERE lang_id='$skv' AND lang_key='_config:cache' LIMIT 1");
    } else {
        $lang = unserialize(gzuncompress(base64_decode($foo['lang_value'])));
    }
}

$txt['current_user_id'] = $isLogin ? $current_user_id : $lang['l_guest'];
