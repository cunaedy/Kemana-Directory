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
// get params
$n = post_param('n');
$chdir = post_param('chdir');

// init
$top_path = str_replace('\\', '/', $config['abs_path']);							// root path for user
$cur_path = str_replace('\\', '/', realpath($top_path.$chdir));						// current path
$prefix = str_replace($top_path, '', $cur_path);								// mask current path
$ttl_uploaded = 0;
$err = $msg = '';

// security
if ($cur_path == $top_path) {
    $in_top_path = 1;
} else {
    $in_top_path = 0;
}

for ($i = 1; $i <= $n; $i++) {
    if (!empty($_FILES["userfile_$i"]['name'])) {								// if file upload
        $fn = $_FILES["userfile_$i"]['name'];
        $ft = $_FILES["userfile_$i"]['type'];
        $fs = $_FILES["userfile_$i"]['size'];
        $fm = $_FILES["userfile_$i"]['tmp_name'];
        $fe = $_FILES["userfile_$i"]['error'];
        $fl = $top_path.$chdir.'/'.$fn;

        // check php error
        if ($fe && $fe != 4) {
            $err = 1;
        }

        // start REAL uploading
        if (!$err) {
            // upload files: if error -> die (invalid server config?)
            $x = upload_file('userfile_'.$i, $fl);
            if (!$x['success']) {
                just_die($lang['msg']['fman_not_allowed']);
            }
            @chmod($fl, 0644);

            // total uploaded bytes
            $ttl_uploaded += $fs;
        }
    }
}

if ($err) {
    admin_die($lang['msg']['fman_not_allowed']);
} else {
    admin_die($lang['msg']['ok']);
}
