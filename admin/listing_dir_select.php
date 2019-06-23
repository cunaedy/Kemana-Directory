<?php
// part of qEngine
require './../includes/admin_init.php';

$what = get_param('what');

// more than one dir?
if ($dir_info['config']['number'] < 1) {
    admin_die(sprintf($lang['msg']['echo'], 'Please define at least one directory first!'));
} elseif ($dir_info['config']['number'] == 1) {
    if ($what == 'cat') {
        $row = sql_qquery("SELECT dir_cat_menu_id FROM ".$db_prefix."listing_dir LIMIT 1");
        redir($config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design&midx='.$row['dir_cat_menu_id']);
    } elseif ($what == 'item') {
        redir($config['site_url'].'/'.$config['admin_folder'].'/listing.php?dir_id='.$dir_info['config']['default']);
    }
}

// data definitions
// idx :: 3 :: 0
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['value'] = 'sql';

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'listing_dir';			// table name
$qadmin_cfg['primary_key'] = 'idx';							// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['ezf_mode'] = false;							// TRUE to use EZF mode (see ./_qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['ezd_mode'] = false;							// TRUE to use ezDesign mode (see ./qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['template'] = 'default';						// template to use

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,dir_short,dir_title';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Identifier,Title';	// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = false;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = false;
if ($what == 'cat') {
    $qadmin_cfg['search_edit'] = 'menu_man.php?cmd=custom.dir&amp;did=__KEY__';
} else {
    $qadmin_cfg['search_edit'] = 'listing.php?dir_id=__KEY__';
}

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = '4';

// form title
$qadmin_title['new'] = 'Add New Directory';
$qadmin_title['update'] = 'Update Directory';
$qadmin_title['search'] = 'Search Directory';
$qadmin_title['list'] = 'Please choose a directory first';

qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
