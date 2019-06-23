<?php
// part of qEngine
require_once './includes/user_init.php';
if (!$config['enable_module_engine']) {
    msg_die($lang['msg']['module_engine_disabled']);
}

$mod_id = get_param('mod');
$popup = get_param('popup');
if (empty($mod_id)) {
    $mod_id = post_param('mod');
}
if (empty($popup)) {
    $popup = post_param('popup');
}

// is it active/installed?
$row = sql_qquery("SELECT mod_enabled FROM ".$db_prefix."module WHERE mod_id='$mod_id' LIMIT 1");
if (empty($row['mod_enabled'])) {
    msg_die(sprintf($lang['msg']['module_engine_error'], $mod_id));
}

// find module
if (!@file_exists('./module/'.$mod_id.'/window.php')) {
    die("<!-- module $mod_id is not available as inline -->");
}

// open module
include "./module/$mod_id/main.php";

if ($popup) {
    flush_tpl('popup');
} else {
    flush_tpl();
}
