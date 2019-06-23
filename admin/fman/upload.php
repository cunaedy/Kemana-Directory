<?php
/***************************************************************************/
# fMan II
# (c)2006, c97.net
# part of qEngine
# This is not a freeware! You may modify it, but you may NOT distribute it,
# sell it, or publish it without our permission.
# To be used only in admin CP
/***************************************************************************/


require './../../includes/admin_init.php';
admin_check('site_file');
if ($config['demo_mode']) {
    admin_die('demo_mode');
}
// get param
$chdir = get_param('chdir');
$n = get_param('n', 5);

// upload form
$upload_n[5] = 5;
$upload_n[10] = 10;
$upload_n[15] = 15;
$upload_n[20] = 20;
$upload_n[25] = 25;
$upload_n[30] = 30;

// init
$top_path = str_replace('\\', '/', $config['abs_path']);							// root path for user
$cur_path = str_replace('\\', '/', realpath($top_path.$chdir));						// current path
$prefix = str_replace($top_path, '', $cur_path);								// mask current path

// security
if ($cur_path == $top_path) {
    $in_top_path = 1;
} else {
    $in_top_path = 0;
}

$txt['block_upload_item'] = '';
$tpl = load_tpl('etc', $config['fman_skin'].'/upload.tpl');

for ($i = 1; $i <= $n; $i++) {
    $txt['n'] = $i;
    $txt['block_upload_item'] .= quick_tpl($tpl_block['upload_item'], $txt);
}

$txt['free_space'] = num_format(disk_free_space('/'));
$txt['max_space'] = num_format(disk_total_space('/'));
$txt['used_space'] = num_format(disk_total_space('/')-disk_free_space('/'));
$txt['chdir'] = $chdir;
$txt['n'] = $n;
$txt['n_select'] = create_select_form('n', $upload_n, $n);
$txt['where'] = $config['abs_path'].$prefix;

// output
$txt['main_body'] = quick_tpl($tpl, $txt);
flush_tpl('adm');
