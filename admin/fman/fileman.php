<?php
/***************************************************************************/
# fMan II
# (c)2006, c97.net
# part of qEngine
# This is not a freeware! You may modify it, but you may NOT distribute it,
# sell it, or publish it without our permission.
# To be used only in admin CP
/***************************************************************************/


// get file type (image, txt, etc)
function get_type($fn)
{
    global $fman_type;

    $fn = strtolower($fn);

    // no extension?
    $ext_pos = strrpos($fn, ".") + 1;
    if (!$ext_pos) {
        $fx= '';
    } else {
        $fx = substr($fn, $ext_pos);
    }

    //
    $t = explode(',', $fman_type['text']);
    $i = explode(',', $fman_type['image']);
    if (in_array($fx, $t)) {
        return 'txt';
    }
    if (in_array($fx, $i)) {
        return 'img';
    }
    return 'file';
}

require './../../includes/admin_init.php';
admin_check('site_file');
if ($config['demo_mode']) {
    $txt['main_body'] = "<img src=\"./../../public/image/fman.png\" alt=\"preview\" align=\"center\" />";
    flush_tpl('adm');
    die;
}

// set vars
$cmd = get_param('cmd');
$chdir = get_param('chdir');
$fn = basename(get_param('fn'));
$newfn = basename(get_param('newfn'));
$new_filename = basename(get_param('new_filename'));
$target = get_param('target');

// filter option
$fman_type['text'] = 'tpl,htm,html,xhtm,xhtml,css,txt,js,sql,ini,xml';
$fman_type['image'] = 'jpg,jpeg,bmp,gif,pcx,png';

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

switch ($cmd) {
    case 'copy':
     $fn_old = $fn;
     $fn_new = "copy_of_$fn";
     $fname_old = $cur_path.'/'.$fn_old;
     $fname_new = $cur_path.'/'.$fn_new;

     // check if file exists
     if (file_exists($fname_new)) {
         just_die($lang['msg']['fman_copy_err']);
     }

     // copy
     if (!@copy($fname_old, $fname_new)) {
         just_die($lang['msg']['fman_copy_err']);
     }
     @chmod($fname_new, 0644);

     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'move':
     $fname_old = $cur_path.'/'.$fn;
     $fname_new = $top_path.$target.'/'.$fn;

     // is file exists?
     if (file_exists($fname_new)) {
         just_die($lang['msg']['fman_move_err']);
     }

     // move
     if (!@copy($fname_old, $fname_new)) {
         just_die($lang['msg']['fman_move_err']);
     }
     if (!@unlink($fname_old)) {
         just_die($lang['msg']['fman_move_err']);
     }
     @chmod($fname_new, 0644);

     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'ren':
     $fn_old = $fn;
     $fn_new = $newfn;
     $fname_old = $cur_path.'/'.$fn_old;
     $fname_new = $cur_path.'/'.$fn_new;
     $redir = "$config[site_url]/members/fileman.php?chdir=$chdir";

     // rename
     if (!@rename($fname_old, $fname_new)) {
         just_die($lang['msg']['fman_ren_err']);
     }
     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'del':
     $fname = $cur_path.'/'.$fn;
     if (@!unlink($fname)) {
         just_die($lang['msg']['fman_del_err']);
     }
     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'new':
     $fname = $cur_path.'/'.$fn;

     // check if file exists
     if (file_exists($fname)) {
         just_die($lang['msg']['fman_new_err']);
     }

     // create new file
     if (!$fp = @fopen($fname, 'w')) {
         just_die($lang['msg']['fman_new_err']);
     }
     fclose($fp);
     @chmod($fname_new, 0644);
     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'mkdir':
     $fname = $cur_path.'/'.$fn;
     if (!@mkdir($fname)) {
         just_die($lang['msg']['fman_mkdir_err']);
     }
     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'rendir':
     $fn_old = $fn;
     $fn_new = $newfn;
     $fname_old = $cur_path.'/'.$fn_old;
     $fname_new = $cur_path.'/'.$fn_new;
     if (!@rename($fname_old, $fname_new)) {
         just_die($lang['msg']['fman_rendir_err']);
     }
     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    case 'rmdir':
     $fname = $cur_path.'/'.$fn;
     if (!@rmdir($fname)) {
         just_die($lang['msg']['fman_rmdir_err']);
     }
     redir("$config[fman_path]/fileman.php?chdir=$chdir");
    break;


    default:
     // load tpl
     $tpl = load_tpl('etc', $config['fman_skin'].'/fileman.tpl'); // <-- modify if necess.
     $txt['block_fileman_item'] = '';
     $dir_array = array(); $file_array = array(); $ttl_size = 0; $f = 0; $d = 0;

     // start retreiving files
     $handle = @opendir($cur_path);
     if (!$handle) {
         just_die($lang['msg']['fman_not_allowed_err']);
     }
     while (false !== ($fn = readdir($handle))) {
         $s = stat($cur_path.'/'.$fn);
         $f_size = num_format($s['size']);
         $f_mtime = date("m-d-Y g:i a", $s['mtime']);
         if (is_dir($cur_path.'/'.$fn)) {
             $dir = 1;
         } else {
             $dir = 0;
         }

         // put dir to dir_array and file to file_array, useful when sorting (dir first, next file)
         // may be we can use additional colum (dir) and merge these arrays into one array
         if ($dir) {
             if (($fn != '.') && ($fn != '..')) {
                 $d++;
                 $dir_array[$d]['key'] = strtolower($fn); // <- trick for SORTING (we can use size or mtime as key!)
                 $dir_array[$d]['name'] = "<b><a href=\"fileman.php?chdir=$prefix/$fn\">$fn/</a></b>";
                 $dir_array[$d]['size'] = '';
                 $dir_array[$d]['mtime'] = $f_mtime;
                 $dir_array[$d]['tools'] =  "<a href=\"#\" onClick=\"confirm_rendir ('$fn')\">$fman_lang[fman_rename]</a> ".
                                           "<a href=\"#\" onClick=\"confirm_rmdir ('$fn')\">$fman_lang[fman_delete]</a> ";
             }
         } else {
             $f++;
             $ttl_size = $ttl_size + $s['size'];
             $file_array[$f]['key'] = strtolower($fn);
             $file_array[$f]['name'] = $fn;
             $file_array[$f]['size'] = $f_size;
             $file_array[$f]['mtime'] = $f_mtime;
             $file_array[$f]['tools'] = "<a href=\"".$config['site_url'].$chdir.'/'.$fn."\">$fman_lang[fman_view]</a> ".
                                       "<a href=\"#\" onclick=\"confirm_rename('$fn');\">$fman_lang[fman_rename]</a> ".
                                       "<a href=\"#\" onclick=\"confirm_move('$fn');\">$fman_lang[fman_move]</a> ".
                                       "<a href=\"#\" onclick=\"confirm_copy('$fn');\">$fman_lang[fman_copy]</a> ".
                                       "<a href=\"#\" onclick=\"confirm_delete('$fn');\">$fman_lang[fman_delete]</a> ";
             if (get_type($fn) == 'txt') {
                 $file_array[$f]['tools'] .= "<a href=\"edit.php?chdir=$chdir&amp;fn=$fn\" class=\"popiframe\">$fman_lang[fman_edit]</a>";
             }
         }
     }

     // add parent dir
     if (!$in_top_path) {
         $d++;
         $dir_array[$d]['key'] = '..';
         $dir_array[$d]['name'] = "<b><a href=\"fileman.php?chdir=$prefix/../\">../</a></b>";
         $dir_array[$d]['size'] = '';
         $dir_array[$d]['mtime'] = '';
         $dir_array[$d]['tools'] = '';
     }

     // sort & merge
     asort($dir_array);
     asort($file_array);
     $dsp = array_merge($dir_array, $file_array);

     // output
     while (list($key, $val) = each($dsp)) {
         $txt['block_fileman_item'] .= quick_tpl($tpl_block['fileman_item'], $dsp[$key]);
     }
     $txt['free_space'] = num_format(disk_free_space('./'));
     $txt['max_space'] = num_format(disk_total_space('./'));
     $txt['used_space'] = num_format(disk_total_space('./')-disk_free_space('./'));
     $txt['redir'] = "fileman.php";
     $txt['where'] = $config['abs_path'].$prefix;
     $txt['cur_path'] = $prefix;
     $txt['ttl_size'] = num_format($ttl_size);
     $txt['num_files'] = num_format(count($file_array));
     $txt['num_dirs'] = num_format(count($dir_array));
     $txt['main_body'] = quick_tpl($tpl, $txt);
    break;
}

generate_html_header();
$txt['main_body'] = quick_tpl($tpl, $txt);
flush_tpl('adm');
