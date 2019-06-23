<?php
// part of qEngine
require './../includes/admin_init.php';

// get params
$id = get_param('id');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($id)) {
    $id = post_param('primary_val');
}

// get group
$group_def = $page_folder = array();
$res = sql_query("SELECT idx, group_id, group_title, page_folder FROM ".$db_prefix."page_group ORDER BY group_title");
while ($row = sql_fetch_array($res)) {
    $group_def[$row['group_id']] = $row['group_title'];
    $page_folder[$row['group_id']] = $row['page_folder'];
}

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['size'] = 10;
$qadmin_def['idx']['value'] = 'sql';

// group_id :: int :: 10
$qadmin_def['group_id']['title'] = 'Group';
$qadmin_def['group_id']['field'] = 'group_id';
$qadmin_def['group_id']['type'] = 'select';
$qadmin_def['group_id']['option'] = $group_def;
$qadmin_def['group_id']['value'] = 'sql';

// cat_name :: string :: 255
$qadmin_def['cat_name']['title'] = 'Title';
$qadmin_def['cat_name']['field'] = 'cat_name';
$qadmin_def['cat_name']['type'] = 'varchar';
$qadmin_def['cat_name']['size'] = 255;
$qadmin_def['cat_name']['value'] = 'sql';
$qadmin_def['cat_name']['required'] = 'sql';

// permalink :: string :: 255
$qadmin_def['permalink']['title'] = 'Permalink';
$qadmin_def['permalink']['field'] = 'permalink';
$qadmin_def['permalink']['type'] = 'permalink';
$qadmin_def['permalink']['size'] = 255;
$qadmin_def['permalink']['value'] = 'sql';

if (!empty($id)) {
    $cat_info = sql_qquery("SELECT * FROM ".$db_prefix."page_cat WHERE idx='$id' LIMIT 1");
    // preview :: string :: 255
    $qadmin_def['preview']['title'] = 'Preview';
    $qadmin_def['preview']['field'] = 'preview';
    $qadmin_def['preview']['type'] = 'echo';
    $qadmin_def['preview']['help'] = 'Preview saved content in new window (you must save first)';
    if ($config['enable_adp']) {
        $qadmin_def['preview']['value'] = "<a href=\"../$cat_info[permalink]\" target=\"_blank\">Preview Now</a>";
    } else {
        $qadmin_def['preview']['value'] = "<a href=\"../page.php?cid=$id\" target=\"_blank\">Preview Now</a>";
    }
}

// cat_details :: blob :: 65535
$qadmin_def['cat_details']['title'] = 'Details';
$qadmin_def['cat_details']['field'] = 'cat_details';
$qadmin_def['cat_details']['type'] = 'wysiwyg';
$qadmin_def['cat_details']['size'] = '500,200';
$qadmin_def['cat_details']['value'] = 'sql';

// cat_image :: string :: 255
$qadmin_def['cat_image']['title'] = 'Image';
$qadmin_def['cat_image']['field'] = 'cat_image';
$qadmin_def['cat_image']['type'] = 'image';
$qadmin_def['cat_image']['size'] = 255;
$qadmin_def['cat_image']['value'] = 'sql';

// general configuration ( * = optional )
$gid = post_param('group_id');
$qadmin_cfg['table'] = $db_prefix.'page_cat';					// table name
$qadmin_cfg['primary_key'] = 'idx';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['log_title'] = 'cat_name';					// qadmin field to be used as log title (REQUIRED even if you don't use log)
$qadmin_cfg['permalink_script'] = 'page.php';				// script name for permalink
$qadmin_cfg['permalink_param'] = 'list';				// script name for permalink
$qadmin_cfg['permalink_source'] = 'cat_name';				// script name for permalink
if (!empty($gid)) {
    $qadmin_cfg['permalink_folder'] = $page_folder[$gid];
}				// script name for permalink
else {
    $qadmin_cfg['permalink_folder'] = '';
}
$qadmin_cfg['rebuild_cache'] = true;

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,cat_name,cat_details';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Title,Summary';	// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// form title
$qadmin_title['new'] = 'Add Category';
$qadmin_title['update'] = 'Update Category';
$qadmin_title['search'] = 'Search Category';
$qadmin_title['list'] = 'Category List';

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 'page_manager';

qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
