<?php
function post_process($cmd, $id, $savenew)
{
    global $config, $db_prefix;

    // if user removed -> redir to user list
    if ($cmd == 'remove_item') {
        sql_query("DELETE FROM ".$db_prefix."user WHERE user_id='$id' LIMIT 1");
        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/user.php');
    }

    if ($savenew) {
        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/user.php?qadmin_cmd=new');
    } else {
        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/user.php?id='.$id);
    }
}

// part of qEngine
require './../includes/admin_init.php';

// params
$id = get_param('id');
$cmd = get_param('cmd');
$qadmin_cmd = get_param('qadmin_cmd');
if (empty($id)) {
    $id = post_param('primary_val');
}
if (empty($qadmin_cmd)) {
    $qadmin_cmd = post_param('qadmin_cmd');
}

// rights view
$lvl = admin_check(4);

// you can't defined higher level!
if ($lvl == 4) {
    unset($admin_level_def[5]);
    unset($user_level_def[5]);
}

// you can't edit/view higher level!
if (!empty($id)) {
    $foo = sql_qquery("SELECT admin_level FROM ".$db_prefix."user WHERE user_id='$id' LIMIT 1");
    if ($foo['admin_level'] > $lvl) {
        admin_die($lang['msg']['no_level']);
    }
}

// data definitions
// user_id :: string :: 80
$qadmin_def['user_id']['title'] = 'User ID';
$qadmin_def['user_id']['field'] = 'user_id';
$qadmin_def['user_id']['type'] = $qadmin_cmd == 'new' ? 'varchar' : 'echo';
$qadmin_def['user_id']['size'] = 80;
$qadmin_def['user_id']['value'] = 'sql';

// user_email :: string :: 255
$qadmin_def['user_email']['title'] = 'Email';
$qadmin_def['user_email']['field'] = 'user_email';
$qadmin_def['user_email']['type'] = 'email';
$qadmin_def['user_email']['size'] = 255;
$qadmin_def['user_email']['value'] = 'sql';

// password only for new
$qadmin_def['user_passwd']['title'] = 'Password';
$qadmin_def['user_passwd']['field'] = 'user_passwd';
$qadmin_def['user_passwd']['type'] = 'password';
$qadmin_def['user_passwd']['size'] = 255;
$qadmin_def['user_passwd']['value'] = 'sql';
if ($qadmin_cmd != 'new') {
    $qadmin_def['user_passwd']['value'] = '';
    $qadmin_def['user_passwd']['help'] = 'Enter a new password to reset password, or leave empty.';
}

// user_level :: string :: 3
$qadmin_def['user_level']['title'] = 'User Level';
$qadmin_def['user_level']['field'] = 'user_level';
$qadmin_def['user_level']['type'] = 'select';
$qadmin_def['user_level']['option'] = $user_level_def;
if ($qadmin_cmd == 'new') {
    $qadmin_def['user_level']['value'] = 1;
} else {
    $qadmin_def['user_level']['value'] = 'sql';
}

// admin_level :: string :: 3
$qadmin_def['admin_level']['title'] = 'Admin Level';
$qadmin_def['admin_level']['field'] = 'admin_level';
$qadmin_def['admin_level']['type'] = 'select';
$qadmin_def['admin_level']['option'] = $admin_level_def;
$qadmin_def['admin_level']['value'] = 'sql';

// user_since :: date :: 10
$qadmin_def['user_since']['title'] = 'Registered on';
$qadmin_def['user_since']['field'] = 'user_since';
$qadmin_def['user_since']['type'] = 'date';
$qadmin_def['user_since']['value'] = 'sql';


// user_notes :: date :: 10
$qadmin_def['user_notes']['title'] = 'Notes';
$qadmin_def['user_notes']['field'] = 'user_notes';
$qadmin_def['user_notes']['type'] = 'text';
$qadmin_def['user_notes']['value'] = 'sql';

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'user';				// table name
$qadmin_cfg['primary_key'] = 'user_id';					// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';					// primary key value
$qadmin_cfg['template'] = 'default';					// template to use
$qadmin_cfg['post_process'] = 'post_process';
$qadmin_cfg['log_title'] = 'user_id';					// qadmin field to be used as log title (REQUIRED even if you don't use log)

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/img';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'user_id,user_email,admin_level';			// list other key to search
$qadmin_cfg['search_key_mask'] = 'User ID,Email Address,Admin?';	// mask other key
$qadmin_cfg['search_result_mask'] = ',,admin_level_def';	// mask other key

$qadmin_cfg['search_filterby'] = 'admin_level>0';	// filter by sql_query (use , to separate queries) *
$qadmin_cfg['search_filtermask'] = 'Administrators Only';				// mask filter *

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';					// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 'manage_user';

// form title
$qadmin_title['new'] = 'New User';
$qadmin_title['update'] = 'User Edit';
$qadmin_title['search'] = 'User Search';
$qadmin_title['list'] = 'User List';
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
