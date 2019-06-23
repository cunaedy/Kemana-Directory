<?php
// part of qEngine
// the following syntaxes will be automatically executed EVERYTIME USER INTERFACE (SHOP) ACCESSED

// -- session start --
ini_set('session.use_only_cookies', 1);
session_start();

// load required files
$lang = $config = $txt = array();
$inc_folder = dirname(__FILE__);
$in_admin_cp = false;
require $inc_folder.'/db_config.php';
require $inc_folder.'/config.php';
require $inc_folder.'/function.php';
require $inc_folder.'/tpl.php';
require $inc_folder.'/xmlize.php';
require $inc_folder.'/init.php';
require $inc_folder.'/vars.php';
require $inc_folder.'/local.php';

// mobile version (may be not used in qE6 ???
$vm = isset($_SESSION[$db_prefix.'view_mode']) ? $_SESSION[$db_prefix.'view_mode'] : '';
$os = $config['skin'];
if (check_mobile()) {
    $config['skin'] = 'skins/_mobile';
}
if ($vm == 'mobile') {
    $config['skin'] = 'skins/_mobile';
}
if ($vm == 'desktop') {
    $config['skin'] = $os;
}

// load template && init section
load_section('section.tpl');

// default template mode for outline.tpl
$outline_tpl_mode = 'default';

// close site?
if ($config['close_site'] && !$current_admin_level && !strpos(cur_url(false), '/includes/login_process.php') && !strpos(cur_url(false), '/visual.php')) {
    qvc_init(3);
    $txt['main_body'] = quick_tpl($tpl_section['site_closed'], $txt);
    flush_tpl('popup');
}
