<?php
/***************************************************************************/
# fMan II
# (c)2006, c97.net
# part of qEngine
# This is not a freeware! You may modify it, but you may NOT distribute it,
# sell it, or publish it without our permission.
# To be used only in admin CP
/***************************************************************************/


function get_dir($dirname)
{
    global $config, $tmp, $posd;
    $handle=opendir($dirname);
    while (false !== ($file = readdir($handle))) {
        $abs = "$dirname/$file";
        if (is_dir($abs) && ($file != '.') && ($file != '..')) {
            $posd++;

            $parent = str_replace($config['abs_path'], '/', $dirname);
            $fn = str_replace($config['abs_path'], '', $abs);

            $tmp[$posd]['key'] = $file;
            $tmp[$posd]['parent'] = str_replace('\\', '/', $parent);
            $tmp[$posd]['name'] = str_replace('\\', '/', $file);
            $tmp[0]['parent'] = $posd;
            get_dir($abs);
        }
    }
    closedir($handle);
}


function get_tree($parent, $path = '')
{
    global $tmp, $tree, $pos;

    for ($i = 1; $i <= $tmp[0]['parent']; $i++) {
        if ($tmp[$i]['parent'] == $parent) {
            $pos++;
            $cur_dir = $tmp[$i]['parent'].'/'.$tmp[$i]['name'];
            $tree[$pos]['open'] = 1;
            $tree[$pos]['chdir'] = str_replace('//', '/', $cur_dir);
            $tree[$pos]['name']  = $tmp[$i]['name'];
            $tree[$pos]['level'] = substr_count($tree[$pos]['chdir'], '/');
            if (!get_tree($cur_dir, '')) {
                $tree[$pos]['open'] = 0;
            }
        }
    }
}

require './../../includes/admin_init.php';
admin_check('site_file');
if ($config['demo_mode']) {
    admin_die('demo_mode');
}
// set vars
$chdir = get_param('chdir');
$fn = get_param('fn');
$cmd = get_param('cmd');
$show_tree = get_param('show_tree');

// init
$top_path = str_replace('\\', '/', $config['abs_path']);							// root path for user
$cur_path = str_replace('\\', '/', realpath($top_path.$chdir));						// current path
$prefix = str_replace($top_path, '', $cur_path);								// mask current path
$fl = $top_path.$chdir.'/'.$fn;
$max = 0; $tree = array(); $pos = 0; $posd = 0;
$tpl = load_tpl('etc', $config['fman_skin'].'/tree.tpl');

// security
if ($cur_path == $top_path) {
    $in_top_path = 1;
} else {
    $in_top_path = 0;
}

// not show tree? -> show form
if (!$show_tree) {
    $txt['cmd'] = $cmd;
    $txt['chdir'] = $chdir;
    $txt['fn'] = $fn;

    if ($cmd == 'move') {
        $txt['message'] = $fman_lang['fman_move_info'];
    }

    if ($cmd == 'browse') {
        $txt['message'] = $fman_lang['fman_browse_info'];
    }

    $txt['where'] = $config['abs_path'];
    echo quick_tpl($tpl, $txt);
    die;
}

// get dirs
get_dir($top_path);
if (!empty($tmp)) {
    asort($tmp);
}

// inject top dir
$tree[0]['open'] = 1;
$tree[0]['chdir'] = '';
$tree[0]['name']  = '/';
$tree[0]['level'] = 0;

get_tree('/');

//
$txt['block_tree'] = '';
for ($i = 0; $i <= $tmp[0]['parent']; $i++) {
    $txt['spacer'] = '';

    for ($j = 1; $j <= $tree[$i]['level']; $j++) {
        $txt['spacer'] .= $fman_lang['fman_folder_space'];
    }

    if ($tree[$i]['open']) {
        $txt['folder'] = $fman_lang['fman_folder_open'];
    } else {
        $txt['folder'] = $fman_lang['fman_folder_close'];
    }

    if ($cmd == 'move') {
        $txt['name'] = "<a href=\"#\" onclick=\"moveto ('{$tree[$i]['chdir']}')\" class=\"fman_tree\">{$tree[$i]['name']}</a>";
    }

    if ($cmd == 'browse') {
        $txt['name'] = "<a href=\"#\" onclick=\"jumpto ('{$tree[$i]['chdir']}')\" class=\"fman_tree\">{$tree[$i]['name']}</a>";
    }

    $txt['block_tree'] .= quick_tpl($tpl_block['tree'], $txt);
}

// output
echo quick_tpl($tpl, $txt);
