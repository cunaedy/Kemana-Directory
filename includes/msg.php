<?php
// part of qEngine
global $tpl_section, $lang, $txt;
$r = get_param('r');
if (empty($full)) {
    $full = false;
}
if (empty($popup)) {
    $popup = false;
}
if (empty($admin)) {
    $admin = false;
}
if (empty($mini)) {
    $mini = false;
}	// if true indicates that popup message should use mini popup, which is: very simple (no whistles, etc), and disappear in 1 second, aka unobtrusive message box) -- see OK case
// $msg_id = strtolower ($msg_id);
if (strpos(cur_url(), '%2F'.$config['admin_folder'].'%2F')) {
    $admin = true;
};

// if loop -> go to front page.
if ($r) {
    redir($config['site_url']);
}

if (empty($url)) {
    if (empty($_SERVER['HTTP_REFERER'])) {
        if ($admin) {
            $url = $config['site_url'].'/'.$config['admin_folder'].'/index.php';
        } else {
            $url = $config['site_url'].'/index.php';
        }
    } else {
        $url = $_SERVER['HTTP_REFERER'];
        if (!strpos('.'.$url, $config['site_url'])) {
            $url = $config['site_url'];
        }	// do NOT redirect to external page
    }
}

// add loop tag
if (strpos($url, '?')) {
    $repeat = '&r=1';
} else {
    $repeat = '?r=1';
}
if (strpos($url, '&r=1') || strpos($url, '?r=1')) {
    $repeat = '';
}
if (empty($msg_id) && !empty($msg_txt)) {
    $msg_id = 'echo';
}

// redirection
$url = str_replace('&amp;', '&', $url);
if (is_numeric($url)) {
    $url = '';
} else {
    $url = $url.$repeat;
}

$txt['message'] = $msg_txt;

// flush
if ($admin && !$full) {
    $tpl_mode = 'admin';
    $output = quick_tpl($tpl_section['normal_msg'], $txt);
    ip_config_update('system_msg', $mini ? 'mini//'.addslashes($output) : $output);
    redir($url);
} elseif ($popup) {
    $output = quick_tpl($tpl_section['popup_msg'], $txt);
    ip_config_update('system_msg', $mini ? 'mini//'.addslashes($output) : $output);
    redir($url);
} elseif ($full) {
    if ($admin) {
        $txt['main_body'] = quick_tpl(load_tpl('var', $tpl_section['fullpage_msg']), $txt);
        flush_tpl('adm');
    } else {
        $txt['main_body'] = quick_tpl(load_tpl('var', $tpl_section['fullpage_msg']), $txt);
        flush_tpl();
    }
} else {
    $tpl_mode = 'user';
    $output = quick_tpl($tpl_section['normal_msg'], $txt);
    ip_config_update('system_msg', $mini ? 'mini//'.addslashes($output) : $output);
    redir($url);
}
