<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(4);

// demo mode?
if ($config['demo_mode']) {
    admin_die('demo_mode');
}
$what = get_param('what');
$id = get_param('id');

// modplug
if ($what == 'module') {
    if (empty($id)) {
        $id = get_param('mod_id');
    }
    $folder = 'module';
} elseif ($what == 'plugin') {
} else {
    die;
}

// find configure.php
if (file_exists("./$folder/$id/configure.php")) {
    require "./$folder/$id/configure.php";
} else {
    admin_die($lang['msg']['no_config']);
}

flush_tpl('adm');
