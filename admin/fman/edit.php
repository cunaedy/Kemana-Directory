<?php
/***************************************************************************/
# fMan II
# (c)C97.net
# part of qEngine
# This is not a freeware! You may modify it, but you may NOT distribute it,
# sell it, or publish it without our permission.
# To be used only in admin CP
/***************************************************************************/


require './../../includes/admin_init.php';
admin_check('site_file');
if ($config['demo_mode']) {
    admin_die($lang['msg']['demo_mode']);
}
// set vars
$chdir = get_param('chdir');
$fn = basename(get_param('fn'));
$save = post_param('save');
$cancel = post_param('cancel');

if (empty($fn)) {
    $fn = basename(post_param('fn'));
}
if ($chdir == '/') {
    $chdir = '';
}

if ($save || $cancel) {
    $chdir = post_param('chdir');
    $fn = basename(post_param('fn'));
}

// init
$top_path = str_replace('\\', '/', $config['abs_path']);							// root path for user
$cur_path = str_replace('\\', '/', realpath($top_path.$chdir));						// current path
$prefix = str_replace($top_path, '', $cur_path);								// mask current path
$fl = $top_path.$chdir.'/'.$fn;

// security
if ($cur_path == $top_path) {
    $in_top_path = 1;
} else {
    $in_top_path = 0;
}

if ($cancel) {
    redir("$config[site_url]/$config[admin_folder]/fman/fileman.php?chdir=$chdir");
} elseif ($save) {
    $edit = html_entity_decode(stripslashes(post_param('editArea', 'html')));		// convert &gt; to >, etc
    $edit = str_replace('&#039;', "'", $edit);
    // save file
    $fp = fopen($fl, 'w');
    fputs($fp, $edit);
    fclose($fp);

    redir("$config[site_url]/$config[admin_folder]/fman/edit.php?chdir=$chdir&fn=$fn");
    die;
} else {
    // syntax (file extention => syntax)
    $syntax = array('html' => 'html', 'htm' => 'html', 'tpl' => 'html', 'js' => 'js', 'css' => 'css', 'sql' => 'sql', 'xml' => 'xml');

    // load file
    $string = '';
    $readonly = is_writable($fl) ? false : true;
    $fp = fopen($fl, 'r');
    while (!feof($fp)) {
        $string .= fgets($fp, 4096);
    }
    fclose($fp);

    $ext = pathinfo($fl, PATHINFO_EXTENSION);
    if (array_key_exists($ext, $syntax)) {
        $editArea = true;
        $txt['syntax'] = $syntax[$ext];
        $txt['is_editable'] = !$readonly;
    } else {
        $editArea = false;
    }
    $txt['html'] = htmlentities($string, ENT_COMPAT, 'UTF-8');
    $txt['chdir'] = $chdir;
    $txt['fn'] = $fn;
    $txt['main_body'] = quick_tpl(load_tpl('etc', $config['fman_skin'].'/edit.tpl'), $txt);
    generate_html_header();
    echo quick_tpl(load_tpl('adm', 'popup.tpl'), $txt);
}
