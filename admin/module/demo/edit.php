<?php
// you can always use qAdmin to manage data
// or, do it manually
// if you are using qAdmin, please note the qadmin_cfg['action'] below!

$sex_def = array('m' => 'Male', 'f' => 'Female');

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['value'] = 'sql';

// ddate :: date :: 10
$qadmin_def['ddate']['title'] = 'Date of Birth';
$qadmin_def['ddate']['field'] = 'ddate';
$qadmin_def['ddate']['type'] = 'date';
$qadmin_def['ddate']['value'] = 'sql';

// dname :: string :: 255
$qadmin_def['dname']['title'] = 'Name';
$qadmin_def['dname']['field'] = 'dname';
$qadmin_def['dname']['type'] = 'varchar';
$qadmin_def['dname']['size'] = 255;
$qadmin_def['dname']['value'] = 'sql';

// daddress :: string :: 255
$qadmin_def['daddress']['title'] = 'Address';
$qadmin_def['daddress']['field'] = 'daddress';
$qadmin_def['daddress']['type'] = 'varchar';
$qadmin_def['daddress']['size'] = 255;
$qadmin_def['daddress']['value'] = 'sql';

// dsex :: string :: 1
$qadmin_def['dsex']['title'] = 'Sex';
$qadmin_def['dsex']['field'] = 'dsex';
$qadmin_def['dsex']['type'] = 'select';
$qadmin_def['dsex']['option'] = $sex_def;
$qadmin_def['dsex']['value'] = 'sql';

// dnotes :: blob :: 65535
$qadmin_def['dnotes']['title'] = 'Notes';
$qadmin_def['dnotes']['field'] = 'dnotes';
$qadmin_def['dnotes']['type'] = 'wysiwyg';
$qadmin_def['dnotes']['size'] = '500,200';
$qadmin_def['dnotes']['value'] = 'sql';

// do qadmin
$qadmin_cfg['table'] = $db_prefix.'demo';					// table name
$qadmin_cfg['primary_key'] = 'idx';							// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['ezf_mode'] = false;							// TRUE to use EZF mode (see /ezf_demo.php), FALSE to use QADMIN
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['action'] = 'task.php?mod=demo&run=edit.php';	// form action <<< this is required for modules!

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';			// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';			// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,dname,daddress';			// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Name,Address';			// mask other key

$qadmin_cfg['search_date_field'] = 'ddate';					// search by date field name *
$qadmin_cfg['search_start_date'] = true;					// show start date *
$qadmin_cfg['search_end_date'] = true;						// show end date *

$qadmin_cfg['search_filterby'] = "dsex='m',dsex='f'";		// filter by sql_query (use , to separate queries) *
$qadmin_cfg['search_filtermask'] = 'Male,Female';			// mask filter *
                                                                        
// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;
$qadmin_cfg['admin_level'] = '3';

// form title
$qadmin_title['new'] = 'Add Data';
$qadmin_title['update'] = 'Update Data';
$qadmin_title['search'] = 'Search Data';
$qadmin_title['list'] = 'Data List';
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
