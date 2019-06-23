<?php
require 'includes/user_init.php';

// remove domain & protocol from URL, eg: https://wwww.c97.net/page.php?page_id=5 -> page.php?cmd=5
$s = array('http://', 'http://www.', 'https://', 'https://www.');
$request = str_replace($s, '', get_param('permalink_request'));
$site_url = str_replace($s, '', $config['site_url']);
$x = str_replace($site_url.'/', '', $request);

$row = sql_qquery("SELECT * FROM ".$db_prefix."permalink WHERE url='$x' LIMIT 1");
if (!empty($row)) {
    $original_idx = $row['target_idx'];
    $permalink_param = $row['target_param'];
    $isPermalink = true;
    parse_str($permalink_param, $arr);
    if (!empty($arr)) {
        foreach ($arr as $k => $v) {
            $_GET[$k] = $v;
        }
    }
    require $row['target_script'];
} else {
    if (function_exists('http_response_code')) {
        http_response_code(404);
    } else {
        header("HTTP/1.0 404 Not Found");
    }
    fullpage_die(sprintf($lang['msg']['permalink_error'], $request));
}
