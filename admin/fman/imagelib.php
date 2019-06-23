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
if ($config['demo_mode']) {
    echo "<img src=\"./../../public/image/fman.png\" alt=\"preview\" align=\"center\" />";
    die;
}
if (!$config['fman_imagelib_enable']) {
    just_die($lang['msg']['fman_not_allowed']);
}
admin_check($config['fman_imagelib_admin']);

//
$cmd = get_param('cmd');
$preview = get_param('preview', 0);
$field = get_param('field');
if (empty($cmd)) {
    $cmd = post_param('cmd');
}

// init
$cur_dir = $config['fman_imagelib_folder'];
if (!chdir($cur_dir)) {
    just_die($lang['msg']['fman_not_allowed']);
}

switch ($cmd) {
    case 'preview':
        $fn = get_param('fn');
        $fullfn = $config['abs_path'].'/public/image/'.$fn;
        if (!file_exists($fullfn)) {
            redir();
        }
        $s = stat($fullfn);
        $img_size = getimagesize($fullfn);
        $txt['f_size'] = number_format(round($s['size'] / 1024)).'K';
        $txt['f_mtime'] = date("m-d-Y g:i a", $s['mtime']);
        $txt['f_dimension'] = $img_size[0].' &times; '.$img_size[1].' px';
        $txt['fn'] = $fn;
        $txt['image_url'] = $config['site_url'].'/'.$config['fman_imagelib_url'];
        $txt['field'] = $field;
        $tpl_mode = 'preview';
        $tpl = load_tpl('etc', $config['fman_skin'].'/imagelib.tpl'); // <-- modify if necess.
        $txt['main_body'] = quick_tpl($tpl, $txt);
    break;


    case 'thumb':
        $img_src = $config['abs_path'].'/public/image/'.get_param('fn');
        $img_th = $config['abs_path'].'/public/thumb/'.get_param('fn');
        image_optimizer($img_src, $img_th, $config['thumb_quality'], 'thumb', 'thumb', true);
    break;


    case 'del':
        $fn = get_param('fn');
        unlink($config['abs_path'].'/public/image/'.$fn);
        if (file_exists($config['abs_path'].'/public/thumb/'.$fn)) {
            unlink($config['abs_path'].'/public/thumb/'.$fn);
        }
        redir();
    break;


    case 'upload':
        $compress = post_param('compress');
        $thumb = post_param('thumb');
        $tmp = $_FILES['upload']['tmp_name'];
        $fn = $_FILES['upload']['name'];

        if ($compress) {
            if (!empty($config['watermark_file'])) {
                image_watermark($tmp, $config['abs_path'].'/public/image/'.$config['watermark_file']);
            }
            image_optimizer($tmp, $cur_dir.'/'.$fn, $config['optimizer']);
        } else {
            $foo = upload_file('upload', $cur_dir.'/'.$fn);
            if ($foo['success'] && $foo['count']) {
                $tgt = $foo[0]['filename'];
            }
            @chmod($cur_dir.'/'.$tgt, 0644);
            if (!empty($config['watermark_file'])) {
                image_watermark($cur_dir.'/'.$tgt, $config['abs_path'].'/public/image/'.$config['watermark_file']);
            }
        }
        redir();
    break;


    default:
        // load tpl
        $view = get_param('view');
        if ($view == 'list') {
            $tpl_mode = 'list';
        } else {
            $tpl_mode = 'thumb';
        }
        $tpl = load_tpl('etc', $config['fman_skin'].'/imagelib.tpl'); // <-- modify if necess.
        $txt['block_fileman_item'] = '';
        $file_array = array(); $ttl_size = 0; $f = 0;

        // start retreiving files
        $handle = @opendir($cur_dir);
        if (!$handle) {
            just_die($lang['msg']['fman_not_allowed']);
        }
        while (false !== ($file = readdir($handle))) {
            $fn = $cur_dir.'/'.$file;
            $s = stat($fn);
            $f_size = number_format(round($s['size'] / 1024)).'K';
            $f_mtime = date("m-d-Y g:i a", $s['mtime']);

            // display image files only
            if (is_file($fn)) {
                $foo = pathinfo($file);
                $ext = empty($foo['extension']) ? '' : strtolower($foo['extension']);
                if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    $f++;
                    $ttl_size = $ttl_size + $s['size'];
                    $file_array[$f]['key'] = strtolower($file);
                    $file_array[$f]['name'] = $file;
                    $file_array[$f]['size'] = $f_size;
                    $file_array[$f]['mtime'] = $f_mtime;
                    $file_array[$f]['field'] = $field;
                    if (file_exists($config['abs_path'].'/public/thumb/'.$file)) {
                        $file_array[$f]['thumb'] = $config['site_url'].'/public/thumb/'.$file;
                    } else {
                        $file_array[$f]['thumb'] = $config['site_url'].'/'.$config['admin_folder'].'/fman/imagelib.php?cmd=thumb&amp;fn='.$file;
                    }
                }
            }
        }

        closedir($handle);

        // sort & merge
        asort($file_array);

        // output
        while (list($key, $val) = each($file_array)) {
            $txt['block_fileman_item'] .= quick_tpl($tpl_block['fileman_item'], $file_array[$key]);
        }
        $txt['cur_dir'] = $cur_dir;
        $txt['real_dir'] = realpath($cur_dir);
        $txt['site_url'] = $config['site_url'];
        $txt['image_url'] = $config['site_url'].'/'.$config['fman_imagelib_url'];
        $txt['ttl_size'] = number_format($ttl_size);
        $txt['num_files'] = number_format(count($file_array));
        $txt['field'] = $field;
        $txt['upload_form'] = quick_tpl($tpl_section['upload'], $txt);
        $txt['main_body'] = quick_tpl($tpl, $txt);
    break;
}

generate_html_header();
echo quick_tpl(load_tpl('adm', 'popup.tpl'), $txt);
