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
    just_die($lang['msg']['demo_mode']);
}
// usage: miniman.php?chdir=../admin/backup&script=../admin/restore.php => relative to fman

// load tpl
$tpl = load_tpl('etc', $config['fman_skin'].'/miniman.tpl');

// init
$script = get_param('script');
$chdir = get_param('chdir');
$cur_dir = $chdir;
if (!chdir($cur_dir)) {
    just_die($lang['msg']['fman_not_allowed']);
}

$txt['block_fileman_item'] = '';
$dir_array = array(); $file_array = array(); $ttl_size = 0; $f = 0; $d = 0;

// start retreiving files
$handle = @opendir('.');
if (!$handle) {
    just_die($lang['msg']['fman_not_allowed']);
}
while (false !== ($file = readdir($handle))) {
    $fn = $file;
    $s = stat($fn);
    $f_size = number_format($s['size']);
    $f_mtime = date("m-d-Y g:i a", $s['mtime']);

    // skin non-file (including folder)
    if (is_file($fn)) {
        $f++;
        $fullpath = $config['site_url'].'/'.$config['admin_folder'].'/fman/'.$chdir;
        $ttl_size = $ttl_size + $s['size'];
        $file_array[$f]['key'] = strtolower($file);
        $file_array[$f]['name'] = $file;
        $file_array[$f]['size'] = $f_size;
        $file_array[$f]['mtime'] = $f_mtime;
        $file_array[$f]['path'] = $fullpath.'/'.$file;
    }
}

closedir($handle);

// sort & merge
asort($dir_array);
asort($file_array);
$dsp = array_merge($dir_array, $file_array);

// output
while (list($key, $val) = each($dsp)) {
    $txt['block_fileman_item'] .= quick_tpl($tpl_block['fileman_item'], $dsp[$key]);
}
$txt['script'] = $script;
$txt['cur_dir'] = $chdir;
$txt['abs_dir'] = realpath('.');
$txt['cur_url'] = $config['site_url'].'/'.$config['admin_folder'].'/fman/'.$chdir;
$txt['ttl_size'] = number_format($ttl_size);
$txt['num_files'] = number_format(count($file_array));
$txt['num_dirs'] = number_format(count($dir_array));

$txt['main_body'] = quick_tpl($tpl, $txt);
generate_html_header();
flush_tpl('adm');
