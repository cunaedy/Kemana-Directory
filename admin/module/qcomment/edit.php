<?php
// get param
$id = get_param('id');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($id)) {
    $id = post_param('primary_val');
}

// get module list
$res = sql_query("SELECT mod_id, notes FROM ".$db_prefix."qcomment_set");
while ($row = sql_fetch_array($res)) {
    $r = $row['mod_id'].'*rate';
    $mod_def[$row['mod_id']] = $row['notes'];
    $mod_def[$r] = $row['notes'].' (Rating)';
}

// get id
$row = sql_qquery("SELECT * FROM ".$db_prefix."qcomment WHERE comment_id = '$id' LIMIT 1");
$mod_name = array_key_exists($row['mod_id'], $mod_def) ? $mod_def[$row['mod_id']] : '';
if (substr($row['mod_id'], -5) == '*rate') {
    $rating = true;
    $f = explode('|', $row['comment_title']);
    $result = 'Average '.num_format($f[0], 2).' from '.$f[1].' votes.';
} else {
    $rating = false;
    $result = '';
}

// comment_id :: int :: 10
$qadmin_def['comment_id']['title'] = 'Comment Id';
$qadmin_def['comment_id']['field'] = 'comment_id';
$qadmin_def['comment_id']['type'] = 'echo';
$qadmin_def['comment_id']['value'] = 'sql';

// mod_id :: string :: 255
$qadmin_def['mod_id']['title'] = 'Module';
$qadmin_def['mod_id']['field'] = 'mod_id';
$qadmin_def['mod_id']['type'] = 'echo';
$qadmin_def['mod_id']['value'] = $mod_name;

// item_id :: int :: 10
$qadmin_def['item_id']['title'] = 'Item ID';
$qadmin_def['item_id']['field'] = 'item_id';
$qadmin_def['item_id']['type'] = 'echo';
$qadmin_def['item_id']['value'] = 'sql';

// item_title :: string :: 255
$qadmin_def['item_title']['title'] = 'Item Title';
$qadmin_def['item_title']['field'] = 'item_title';
$qadmin_def['item_title']['type'] = 'echo';
$qadmin_def['item_title']['value'] = 'sql';

// item_title :: string :: 255
$qadmin_def['item_url']['title'] = 'Item URL';
$qadmin_def['item_url']['field'] = 'item_url';
$qadmin_def['item_url']['type'] = 'url';
$qadmin_def['item_url']['value'] = 'sql';

if (!$rating) {
    // comment_user :: string :: 255
    $qadmin_def['comment_user']['title'] = 'Username';
    $qadmin_def['comment_user']['field'] = 'comment_user';
    $qadmin_def['comment_user']['type'] = 'echo';
    $qadmin_def['comment_user']['value'] = 'sql';
    $qadmin_def['comment_user']['help'] = 'Who submit it.';
}

// comment_title :: string :: 255
$qadmin_def['comment_title']['title'] = $rating ? 'Average' : 'Comment Title';
$qadmin_def['comment_title']['field'] = 'comment_title';
$qadmin_def['comment_title']['type'] = $rating ? 'echo' : 'varchar';
$qadmin_def['comment_title']['size'] = 255;
$qadmin_def['comment_title']['value'] = $rating ? $result : 'sql';;


if (!$rating) {
    // comment_body :: blob :: 65535
    $qadmin_def['comment_body']['title'] = 'Body';
    $qadmin_def['comment_body']['field'] = 'comment_body';
    $qadmin_def['comment_body']['type'] = 'text';
    $qadmin_def['comment_body']['size'] = '500,200';
    $qadmin_def['comment_body']['value'] = 'sql';

    // comment_rate :: int :: 3
    $qadmin_def['comment_rate']['title'] = 'Rate';
    $qadmin_def['comment_rate']['field'] = 'comment_rate';
    $qadmin_def['comment_rate']['type'] = 'select';
    $qadmin_def['comment_rate']['option'] = $rating_def;
    $qadmin_def['comment_rate']['value'] = 'sql';
    $qadmin_def['comment_rate']['help'] = 'User rating of the article';

    // comment_date :: date :: 10
    $qadmin_def['comment_date']['title'] = 'Date';
    $qadmin_def['comment_date']['field'] = 'comment_date';
    $qadmin_def['comment_date']['type'] = 'date';
    $qadmin_def['comment_date']['value'] = 'sql';

    // comment_approve :: string :: 1
    $qadmin_def['comment_approve']['title'] = 'Approved?';
    $qadmin_def['comment_approve']['field'] = 'comment_approve';
    $qadmin_def['comment_approve']['type'] = 'radio';
    $qadmin_def['comment_approve']['option'] = $yesno;
    $qadmin_def['comment_approve']['value'] = 'sql';
}

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'qcomment';		// table name
$qadmin_cfg['primary_key'] = 'comment_id';					// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['action'] = 'task.php?mod=qcomment&run=edit.php';
$qadmin_cfg['auto_recache'] = true;

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'comment_id,mod_id,item_title,comment_title,comment_approve';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'Comment ID,Module ID,Item Title,Comment Title,Approved?';	// mask other key
$qadmin_cfg['search_result_mask'] = ",mod_def,,,yesno,";

$qadmin_cfg['search_date_field'] = 'comment_date';				// search by date field name *
$qadmin_cfg['search_start_date'] = true;					// show start date *
$qadmin_cfg['search_end_date'] = true;						// show end date *

$qadmin_cfg['search_filterby'] = "comment_approve='1',comment_approve='0'";	// filter by sql_query (use , to separate queries) *
$qadmin_cfg['search_filtermask'] = 'Approved,Not Approved';				// mask filter *

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = false;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
// Highest to lowest: 5, 4, 3, 2, 1
// higher level can access lower level features
$qadmin_cfg['admin_level'] = '3';

// auto sql query generated by qAdmin: "SELECT * FROM table WHERE primary_key='primary_val' LIMIT 1"
// to overwrite >> $qadmin_cfg['sql_select'] = "SELECT * FROM ".$db_prefix."news WHERE news_id = '2' LIMIT 1"; <<
// auto sql & manual sql used only for cmd = 'update'
qadmin_manage($qadmin_def, $qadmin_cfg);
