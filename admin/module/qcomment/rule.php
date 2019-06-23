<?php
$qcomment_def = array();
$qcomment_def[2] = 'Comment only';
$qcomment_def[3] = 'Comment &amp; Rating';

// group_id :: int :: 10
$qadmin_def['group_id']['title'] = 'Group Id';
$qadmin_def['group_id']['field'] = 'group_id';
$qadmin_def['group_id']['type'] = 'echo';
$qadmin_def['group_id']['value'] = 'sql';

// comment_mode :: string :: 1
$qadmin_def['comment_mode']['title'] = 'Comment Mode';
$qadmin_def['comment_mode']['field'] = 'comment_mode';
$qadmin_def['comment_mode']['type'] = 'select';
$qadmin_def['comment_mode']['option'] = $qcomment_def;
$qadmin_def['comment_mode']['value'] = 'sql';

// comment_apporval :: string :: 1
$qadmin_def['comment_approval']['title'] = 'Comment Approval';
$qadmin_def['comment_approval']['field'] = 'comment_approval';
$qadmin_def['comment_approval']['type'] = 'radio';
$qadmin_def['comment_approval']['option'] = $yesno;
$qadmin_def['comment_approval']['value'] = 'sql';

// member_only :: string :: 1
$qadmin_def['member_only']['title'] = 'Member Only';
$qadmin_def['member_only']['field'] = 'member_only';
$qadmin_def['member_only']['type'] = 'radio';
$qadmin_def['member_only']['option'] = $yesno;
$qadmin_def['member_only']['value'] = 'sql';

// unique_comment :: string :: 1
$qadmin_def['unique_comment']['title'] = 'Unique Comment';
$qadmin_def['unique_comment']['field'] = 'unique_comment';
$qadmin_def['unique_comment']['type'] = 'radio';
$qadmin_def['unique_comment']['option'] = $yesno;
$qadmin_def['unique_comment']['value'] = 'sql';
$qadmin_def['unique_comment']['help'] = 'One person one comment. This is member only feature. Please also <b>enable Member Only</b> rule!';

$qadmin_def['comment_helpful']['title'] = 'Enable Useful Poll';
$qadmin_def['comment_helpful']['field'] = 'comment_helpful';
$qadmin_def['comment_helpful']['type'] = 'radio';
$qadmin_def['comment_helpful']['option'] = $yesno;
$qadmin_def['comment_helpful']['value'] = 'sql';
$qadmin_def['comment_helpful']['help'] = 'Visitors can vote if the comment is useful or not.';

// comment_on_comment :: string :: 1
$qadmin_def['comment_on_comment']['title'] = 'Enable Reply on Comment';
$qadmin_def['comment_on_comment']['field'] = 'comment_on_comment';
$qadmin_def['comment_on_comment']['type'] = 'radio';
$qadmin_def['comment_on_comment']['option'] = $yesno;
$qadmin_def['comment_on_comment']['value'] = 'sql';
$qadmin_def['comment_on_comment']['help'] = 'Visitors can send reply on the comments.';

// detail :: string :: 1
$qadmin_def['detail']['title'] = 'Show Poster Detail';
$qadmin_def['detail']['field'] = 'detail';
$qadmin_def['detail']['type'] = 'radio';
$qadmin_def['detail']['option'] = $yesno;
$qadmin_def['detail']['value'] = 'sql';
$qadmin_def['detail']['help'] = 'Show/hide poster information: username &amp; date posted.';

// captcha :: string :: 1
$qadmin_def['captcha']['title'] = 'Enable Captcha';
$qadmin_def['captcha']['field'] = 'captcha';
$qadmin_def['captcha']['type'] = 'radio';
$qadmin_def['captcha']['option'] = $yesno;
$qadmin_def['captcha']['value'] = 'sql';
$qadmin_def['captcha']['help'] = 'Captcha can be used to reduce comment spamming. Guest must always enter captcha even if this setting is disabled!';

// mod_id :: string :: 15
$qadmin_def['mod_id']['title'] = 'Mod ID';
$qadmin_def['mod_id']['field'] = 'mod_id';
$qadmin_def['mod_id']['type'] = 'varchar';
$qadmin_def['mod_id']['size'] = 15;
$qadmin_def['mod_id']['value'] = 'sql';
$qadmin_def['mod_id']['help'] = 'Enter ID you want this rule to be applied to. Module ID is user defined.';
$qadmin_def['mod_id']['required'] = true;

// notes :: string :: 255
$qadmin_def['notes']['title'] = 'Mod Name';
$qadmin_def['notes']['field'] = 'notes';
$qadmin_def['notes']['type'] = 'varchar';
$qadmin_def['notes']['size'] = 255;
$qadmin_def['notes']['value'] = 'sql';
$qadmin_def['notes']['help'] = 'Enter a name, so you can remember what it is for.';
$qadmin_def['notes']['required'] = true;

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'qcomment_set';		// table name
$qadmin_cfg['primary_key'] = 'group_id';					// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['action'] = 'task.php?mod=qcomment&run=rule.php';

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'group_id,mod_id,notes';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'Group ID,Module ID,Notes';	// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = '4';
qadmin_manage($qadmin_def, $qadmin_cfg);
