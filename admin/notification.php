<?php
// part of qEngine
require './../includes/admin_init.php';

$id = get_param('id');
if (empty($id)) {
    $id = post_param('primary_val');
}
if (!empty($id)) {
    sql_query("UPDATE ".$db_prefix."notification SET notify_read='1', notify_popup='1' WHERE idx='$id' AND notify_to='$current_user_id' LIMIT 1");
    $val = sql_qquery("SELECT * FROM ".$db_prefix."notification WHERE idx='$id' AND notify_to='$current_user_id' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."notification SET notify_popup='1' WHERE notify_to='$current_user_id' AND notify_popup='0'");
} else {
    $val = create_blank_tbl($db_prefix.'notification');
}

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['value'] = 'sql';

// notify_time :: int :: 10
$qadmin_def['notify_time']['title'] = 'Sent Time';
$qadmin_def['notify_time']['field'] = 'notify_time';
$qadmin_def['notify_time']['type'] = 'echo';
$qadmin_def['notify_time']['value'] = $val['notify_time'] ? convert_date(date('Y-m-d', $val['notify_time'])).' @ '.date('H:i:s', $val['notify_time']) : '';

// notify_from :: string :: 765
$qadmin_def['notify_from']['title'] = 'From';
$qadmin_def['notify_from']['field'] = 'notify_from';
$qadmin_def['notify_from']['type'] = 'echo';
$qadmin_def['notify_from']['value'] = 'sql';

// notify_to :: string :: 765
$qadmin_def['notify_to']['title'] = 'To';
$qadmin_def['notify_to']['field'] = 'notify_to';
$qadmin_def['notify_to']['type'] = 'echo';
$qadmin_def['notify_to']['value'] = 'sql';

// notify_admin :: string :: 3
$qadmin_def['notify_admin']['title'] = 'For Admin?';
$qadmin_def['notify_admin']['field'] = 'notify_admin';
$qadmin_def['notify_admin']['type'] = 'mask';
$qadmin_def['notify_admin']['option'] = $yesno;
$qadmin_def['notify_admin']['value'] = 'sql';

// notify_subject :: string :: 765
$qadmin_def['notify_subject']['title'] = 'Subject';
$qadmin_def['notify_subject']['field'] = 'notify_subject';
$qadmin_def['notify_subject']['type'] = 'echo';
$qadmin_def['notify_subject']['value'] = 'sql';

// notify_body :: blob :: 196605
$qadmin_def['notify_body']['title'] = 'Message';
$qadmin_def['notify_body']['field'] = 'notify_body';
$qadmin_def['notify_body']['type'] = 'echo';
$qadmin_def['notify_body']['value'] = 'sql';

// notify_url :: string :: 765
$qadmin_def['notify_url']['title'] = 'Related Link';
$qadmin_def['notify_url']['field'] = 'notify_url';
$qadmin_def['notify_url']['type'] = 'echo';
$qadmin_def['notify_url']['value'] = empty($val['notify_url']) ? '-' : "<a href=\"$val[notify_url]\" target=\"wNotify\">$val[notify_url]</a>";

// notify_read :: string :: 3
$qadmin_def['notify_read']['title'] = 'Read';
$qadmin_def['notify_read']['field'] = 'notify_read';
$qadmin_def['notify_read']['type'] = 'radio';
$qadmin_def['notify_read']['option'] = $yesno;
$qadmin_def['notify_read']['value'] = 'sql';

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'notification';				// table name
$qadmin_cfg['primary_key'] = 'idx';							// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';			// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';			// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$fb = get_param('filter_by');
if (empty($fb) || ($fb > 3)) {
    $_GET['filter_by'] = 1;
}
$qadmin_cfg['search_filterby'] = "notify_to='$current_user_id',notify_to='$current_user_id' AND notify_read='0',notify_to='$current_user_id' AND notify_read='1'";	// filter by sql_query (use , to separate queries) *
$qadmin_cfg['search_filtermask'] = 'All,Unread,Read';				// mask filter *
$qadmin_cfg['search_key'] = 'idx,notify_from,notify_subject,notify_read';	// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,From,Subject,Read?';			// mask other key
$qadmin_cfg['search_result_mask'] = ",,,yesno";

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = false;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// form title
$qadmin_title['new'] = 'Add Notification';
$qadmin_title['update'] = 'Update Notification';
$qadmin_title['search'] = 'Search Notification';
$qadmin_title['list'] = 'Notification List';
$qadmin_cfg['admin_level'] = 3;
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
