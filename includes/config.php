<?php
// configure script to server's environment
// you should not need to change any of these values
// part of qEngine

// init database
if (!$dbh = mysqli_connect($db_hostname, $db_username, $db_password)) {
    echo mysqli_error();
    exit;
}
mysqli_select_db($dbh, $db_name);

// compatibility with MySQL 5 Strict Mode
$mysql_ver = substr(mysqli_get_server_info($dbh), 0, 1);
if ($mysql_ver > 4) {
    mysqli_query($dbh, "SET @@global.sql_mode=''");
    mysqli_query($dbh, "SET NAMES 'utf8'");
    mysqli_query($dbh, "SET sql_mode = 'NO_UNSIGNED_SUBTRACTION'");
}

// READ CONFIG DB
// Reason why we seperate shop config from qe_config...... because we are lazy :D This way we can re-use
// the same table, tpl & php for qe_config without mod anything.
$module_config = $config = array();
$res = mysqli_query($dbh, 'SELECT * FROM '.$db_prefix.'config');
if (!$res) {
    die("<h1>Fatal Error!</h1><p>Can not connect to configuration table. Please verify your database configuration.</p><p><b>MySQL Respond:</b> ".mysqli_error($dbh)."</p>");
}
while ($row = mysqli_fetch_array($res)) {
    if ($row['group_id']) {
        if (substr($row['group_id'], 0, 4) == 'mod_') {
            $mid = substr($row['group_id'], 4);
            $module_config[$mid][$row['config_id']] = $row['config_value'];
        } else {
            $config[$row['group_id']][$row['config_id']] = $row['config_value'];
        }
    } else {
        $config[$row['config_id']] = $row['config_value'];
    }
}

// server dependent config
if (!get_magic_quotes_gpc()) {
    $config['gpc_quotes'] = 0;
} else {
    $config['gpc_quotes'] = 1;
}	// detect magic quote gpc
if (substr(php_uname(), 0, 7) == "Windows") {
    $config['under_windows'] = 1;
} else {
    $config['under_windows'] = 0;
}

// power config (DO NOT CHANGE IF YOU DON'T UNDERSTAND IT)
$config['short_query'] = 0;
$config['multi_rte'] = 0;
$config['multi_code_editor'] = 0;
$config['total_mysql_query'] = 0;
$config['force_redir'] = 1;						// still redir after header sent?
$config['list_ppp'] = 10;						// num of pagination per page * REQUIRED IN PAGINATION() *
$config['abs_path'] = str_replace('\\', '/', $config['abs_path']);	// for Windows
$config['original_default_lang'] = $config['default_lang'];
if (isset($_SESSION[$db_prefix.'override_skin'])) {
    $config['skin'] = 'skins/'.$_SESSION[$db_prefix.'override_skin'];
}
if (isset($_SESSION[$db_prefix.'language'])) {
    $config['default_lang'] = $_SESSION[$db_prefix.'language'];
}

// sub-scripts
$config['fman_path'] = $config['site_url'].'/'.$qe_admin_folder.'/fman';	// location of fMan script
$config['fman_skin'] = 'skins/_fman';				// location of fMan skin
$config['fman_imagelib_enable'] = true;				// enable/disable imagelib
$config['fman_imagelib_folder'] = '../../public/image';	// location of images to store (relative to fman)
$config['fman_imagelib_url']    = 'public/image';		// location of images to store (relative to site url)
$config['fman_imagelib_admin']  = '1';				// minimum admin level

// demo mode (as seen in our demo site)
$config['demo_mode'] = false;					// enable demo mode => caution! everything will be reset every 24 hours (u: admin, p: admin)
$config['demo_path'] = './reset';				// demo mode support files location

// SMTP CRLF
$config['smtp_crlf'] = "\r\n";

// social media
$enable_facebook_like = $config['facebook_like'];
$enable_facebook_comment = $config['facebook_comment'];
$enable_twitter_share = $config['twitter_share'];

// debug mode (show error)
$debug_info = array('sql' => array(), 'mod' => array(), 'tpl' => array());
if ($config['debug_mode']) {
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    ini_set('opcache.enable', false);
}
