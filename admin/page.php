<?php
// called after add/edit/del anything
function post_func($cmd, $id, $savenew = false)
{
    global $db_prefix, $config;
    $inf = sql_qquery("SELECT page_date, page_time, page_status FROM ".$db_prefix."page WHERE page_id='$id' LIMIT 1");
    $now = time();
    $tt = $inf['page_time'];
    $dd = $inf['page_date'];
    $st = $inf['page_status'];
    if (empty($st)) {
        $st = 'D';
    }
    $unix = mktime(substr($tt, 0, 2), substr($tt, 3, 2), substr($tt, 6, 2), substr($dd, 5, 2), substr($dd, 8, 2), substr($dd, 0, 4));
    sql_query("UPDATE ".$db_prefix."page SET page_unix='$unix', page_status='$st', last_update='$now' WHERE page_id='$id' LIMIT 1");
    if ($cmd == 'remove_item') {
        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/page.php');
    }
    if ($savenew) {
        redir($config['site_url'].'/'.$config['admin_folder'].'/page.php?qadmin_cmd=new');
    } else {
        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/page.php?id='.$id);
    }
    die;
}

// part of qEngine
require './../includes/admin_init.php';

// get params
$gid = get_param('gid');
$id = get_param('id');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($gid)) {
    $gid = post_param('group_id');
}

// group & page template
$group_template_def = array('_blank' => 'Empty Template');
$page_template_def = array('_blank' => 'Empty Template');
$flist = get_file_list('../skins/_common');
foreach ($flist as $k => $v) {
    if (substr($v, 0, 5) == 'body_') {
        $group_template_def[$v] = $v;
    }
    if ((substr($v, 0, 5) == 'page_') && ($v != 'page_list.tpl')) {
        $page_template_def[$v] = $v;
    }
}
ksort($group_template_def);
ksort($page_template_def);

// get info
$gid_sql = 0;
if ($id) {
    $page_info = sql_qquery("SELECT * FROM ".$db_prefix."page WHERE page_id='$id' LIMIT 1");
    $gid_sql = $page_info['group_id'];

    // get related pages
    $foo = explode(',', $page_info['page_related']);
    $mm = array();
    $i = 0;
    if ($page_info['page_related']) {
        foreach ($foo as $k => $v) {
            $i++;
            $mem = sql_qquery("SELECT page_id, page_title FROM ".$db_prefix."page WHERE page_id='$v' LIMIT 1");
            $mm[] = array('id' => $mem['page_id'], 'name' => $mem['page_title']);
        }
    }
    $related_preset = $i ? json_encode($mm) : 'null';
} else {
    $page_info = create_blank_tbl($db_prefix.'page');
    $related_preset = 'null';
}

// determine mode
if (empty($gid) && empty($gid_sql)) {
    $mode = 'new';
} elseif (!empty($gid) && empty($gid_sql)) {
    $mode = 'new_change';
} elseif (empty($gid) && !empty($gid_sql)) {
    $mode = 'edit';
    $gid = $gid_sql;
} elseif (!empty($gid) && !empty($gid_sql)) {
    $mode = 'edit_change';
} else {
    $mode = 'new';
}

// status def
$status_def = array('D' => 'Draft', '***1' => 'Published', 'P' => 'Public', 'M' => 'Members Only', 'A' => 'Administrators Only', 'H' => 'Hidden', '///1' => '');

// content def
$content_def = array('html' => 'WYSIWYG Editor', 'raw' => 'Code Editor');

// get group
$group_def = $rule_def = $cat_def = array();
$res = sql_query("SELECT idx, group_id, group_title FROM ".$db_prefix."page_group WHERE hidden_private='0' ORDER BY group_title");
while ($row = sql_fetch_array($res)) {
    $group_def[$row['group_id']] = $row['group_title'];
}

// get rule
$rule_def = sql_qquery("SELECT * FROM ".$db_prefix."page_group WHERE group_id='$gid' LIMIT 1");

// get cats
$res = sql_query("SELECT idx, cat_name FROM ".$db_prefix."page_cat WHERE group_id='$gid' ORDER BY cat_name");
while ($row = sql_fetch_array($res)) {
    $cat_def[$row['idx']] = $row['cat_name'];
}

$cmd = get_param('qadmin_cmd');
$do = get_param('do');
$id = get_param('id');

// data definitions
// page_id :: int :: 10
$qadmin_def['page_id']['title'] = 'ID';
$qadmin_def['page_id']['field'] = 'page_id';
$qadmin_def['page_id']['type'] = 'echo';
$qadmin_def['page_id']['size'] = 10;
$qadmin_def['page_id']['value'] = 'sql';

// group_id :: int :: 10
$qadmin_def['group_id']['title'] = 'Group';
$qadmin_def['group_id']['field'] = 'group_id';
$qadmin_def['group_id']['type'] = 'select';
$qadmin_def['group_id']['option'] = $group_def;
$qadmin_def['group_id']['value'] = 'sql';
$qadmin_def['group_id']['required'] = true;
if ($gid) {
    $qadmin_def['group_id']['value'] = $gid;
}

// cat_id :: int :: 10
$qadmin_def['cat_id']['title'] = 'Category';
$qadmin_def['cat_id']['field'] = 'cat_id';
$qadmin_def['cat_id']['type'] = 'select';
$qadmin_def['cat_id']['option'] = $cat_def;
$qadmin_def['cat_id']['value'] = 'sql';
$qadmin_def['cat_id']['required'] = true;

// page_image :: string :: 255
if ($rule_def['page_image']) {
    $qadmin_def['page_image']['title'] = 'Main Image';
    $qadmin_def['page_image']['field'] = 'page_image';
    $qadmin_def['page_image']['type'] = 'thumb';
    $qadmin_def['page_image']['size'] = $rule_def['page_thumb'];
    $qadmin_def['page_image']['value'] = 'sql';
    $qadmin_def['page_image']['help'] = 'This image also be used as page thumbnail image.';
}

// page_img_tmp :: string :: 255
if ($rule_def['page_gallery']) {
    $qadmin_def['page_img_tmp']['title'] = 'Additional Images';
    $qadmin_def['page_img_tmp']['field'] = 'page_img_tmp';
    $qadmin_def['page_img_tmp']['type'] = 'img_set';
    $qadmin_def['page_img_tmp']['prefix'] = 'page_img';
    $qadmin_def['page_img_tmp']['value'] = 'sql';
    $qadmin_def['page_img_tmp']['help'] = 'You can create image gallery by uploading several images here.';
    // $qadmin_def['page_img_tmp']['resize'] = 500;
    $qadmin_def['page_img_tmp']['thumb_size'] = $rule_def['page_gallery_thumb'];
}


// page_status :: string :: 255
$qadmin_def['page_status']['title'] = 'Status';
$qadmin_def['page_status']['field'] = 'page_status';
$qadmin_def['page_status']['type'] = 'select';
$qadmin_def['page_status']['option'] = $status_def;
$qadmin_def['page_status']['value'] = (($mode == 'new') || ($mode == 'new_change'))? 'D' : 'sql';

// page_title :: string :: 255
$qadmin_def['page_title']['title'] = 'Title';
$qadmin_def['page_title']['field'] = 'page_title';
$qadmin_def['page_title']['type'] = 'varchar';
$qadmin_def['page_title']['size'] = 255;
$qadmin_def['page_title']['value'] = 'sql';
$qadmin_def['page_title']['index'] = true;
$qadmin_def['page_title']['required'] = true;
if ($mode == 'new') {
    $qadmin_def['page_title']['type'] = 'disabled';
    $qadmin_def['page_title']['value'] = 'Please choose a group first.';
}

if ($mode != 'new') {
    // preview :: string :: 255
    $qadmin_def['preview']['title'] = 'Preview';
    $qadmin_def['preview']['field'] = 'preview';
    $qadmin_def['preview']['type'] = 'echo';
    $qadmin_def['preview']['help'] = 'Preview saved content in new window (you must save first)';
    if ($config['enable_adp']) {
        $qadmin_def['preview']['value'] = "<a href=\"../$page_info[permalink]\" target=\"_blank\">Preview Now</a>";
    } else {
        $qadmin_def['preview']['value'] = "<a href=\"../page.php?pid=$id\" target=\"_blank\">Preview Now</a>";
    }
}


// page_body :: blob :: 65535
$qadmin_def['page_body']['title'] = 'Body';
$qadmin_def['page_body']['field'] = 'page_body';
if ($page_info['page_mode'] == 'raw') {
    $qadmin_def['page_body']['type'] = 'code';
    $qadmin_def['page_body']['lang'] = 'html';
} else {
    $qadmin_def['page_body']['type'] = 'wysiwyg';
    $qadmin_def['page_body']['wysiwyg_pagebreak'] = true;
}
$qadmin_def['page_body']['size'] = '500,500';
$qadmin_def['page_body']['value'] = 'sql';
$qadmin_def['page_body']['index'] = true;
if ($id) {
    $qadmin_def['page_body']['help'] = 'Need a custom page? Easy, create your page in HTML and save as page_'.$id.'.tpl, and put it in /skins/_common folder.';
}

$qadmin_def['div2']['title'] = 'Advanced';
$qadmin_def['div2']['field'] = 'div2';
$qadmin_def['div2']['type'] = 'div';

// page_attachment :: string :: 255
if ($rule_def['page_attachment']) {
    $qadmin_def['page_attachment']['title'] = 'File Attachment';
    $qadmin_def['page_attachment']['field'] = 'page_attachment';
    $qadmin_def['page_attachment']['type'] = 'file';
    $qadmin_def['page_attachment']['value'] = 'sql';
}

// page_related :: int :: 10
$qadmin_def['page_related']['title'] = 'Related Page';
$qadmin_def['page_related']['field'] = 'page_related';
$qadmin_def['page_related']['type'] = 'varchar';
$qadmin_def['page_related']['size'] = 255;
$qadmin_def['page_related']['value'] = 'sql';
// $qadmin_def['page_related']['help'] = 'Enter related page ID, separated with comma, eg: 1,2,3. <br />PS: You can also search by page title.';

// page_keyword :: string :: 255
$qadmin_def['page_keyword']['title'] = 'Keywords';
$qadmin_def['page_keyword']['field'] = 'page_keyword';
$qadmin_def['page_keyword']['type'] = 'varchar';
$qadmin_def['page_keyword']['size'] = 255;
$qadmin_def['page_keyword']['value'] = 'sql';
$qadmin_def['page_keyword']['help'] = 'For search engine optimization.';

// page_author :: blob :: 255
$qadmin_def['page_author']['title'] = 'Author';
$qadmin_def['page_author']['field'] = 'page_author';
$qadmin_def['page_author']['type'] = 'varchar';
$qadmin_def['page_author']['size'] = 255;
$qadmin_def['page_author']['value'] = 'sql';

// permalink :: string :: 255
$qadmin_def['permalink']['title'] = 'Permalink';
$qadmin_def['permalink']['field'] = 'permalink';
$qadmin_def['permalink']['type'] = 'permalink';
$qadmin_def['permalink']['size'] = 255;
$qadmin_def['permalink']['value'] = 'sql';

// page_list :: string :: 1
$qadmin_def['page_list']['title'] = 'Include This Page in Page List?';
$qadmin_def['page_list']['field'] = 'page_list';
$qadmin_def['page_list']['type'] = 'radio';
$qadmin_def['page_list']['option'] = $yesno;
$qadmin_def['page_list']['value'] = $id ? 'sql' : 1;

// page_pinned :: string :: 1
$qadmin_def['page_pinned']['title'] = 'Pin This Page?';
$qadmin_def['page_pinned']['field'] = 'page_pinned';
$qadmin_def['page_pinned']['type'] = 'radio';
$qadmin_def['page_pinned']['option'] = $yesno;
$qadmin_def['page_pinned']['value'] = 'sql';
$qadmin_def['page_pinned']['help'] = 'Pin this page to put it always on the top of the list on page list.';

// page_allow_comment :: string :: 1
$qadmin_def['page_allow_comment']['title'] = 'Allow Comment';
$qadmin_def['page_allow_comment']['field'] = 'page_allow_comment';
$qadmin_def['page_allow_comment']['type'] = 'radio';
$qadmin_def['page_allow_comment']['option'] = $yesno;
$qadmin_def['page_allow_comment']['value'] = $id ? 'sql' : ($rule_def['page_comment'] ? 1 : 0);

// page_mode :: string :: 255
$qadmin_def['page_mode']['title'] = 'Content Editor';
$qadmin_def['page_mode']['field'] = 'page_mode';
$qadmin_def['page_mode']['type'] = 'radio';
$qadmin_def['page_mode']['option'] = $content_def;
$qadmin_def['page_mode']['value'] = $id ? 'sql' : 'html';
$qadmin_def['page_mode']['help'] = 'Code editor offers more flexibility in editing. Caution! Switching between modes may have unexpected results.';

// page_template :: string :: 255
$qadmin_def['page_template']['title'] = 'Page Template';
$qadmin_def['page_template']['field'] = 'page_template';
$qadmin_def['page_template']['type'] = 'select';
$qadmin_def['page_template']['option'] = $page_template_def;
$qadmin_def['page_template']['value'] = (($mode == 'new') || ($mode == 'new_change'))? $rule_def['page_template'] : 'sql';
// $qadmin_def['page_template']['help'] = 'You can define a unique template for this content by creating page_[your_template].tpl, and put it in /skins/_common folder.';

// page_date :: date :: 10
$qadmin_def['page_date']['title'] = 'Date';
$qadmin_def['page_date']['field'] = 'page_date';
$qadmin_def['page_date']['type'] = 'date';
$qadmin_def['page_date']['value'] = 'sql';

// page_time :: time :: 8
$qadmin_def['page_time']['title'] = 'Time';
$qadmin_def['page_time']['field'] = 'page_time';
$qadmin_def['page_time']['type'] = 'time';
$qadmin_def['page_time']['value'] = 'sql';

$qadmin_def['div1']['title'] = 'Information';
$qadmin_def['div1']['field'] = 'div1';
$qadmin_def['div1']['type'] = 'div';

// last_update :: int :: 10
$qadmin_def['last_update']['title'] = 'Last Update';
$qadmin_def['last_update']['field'] = 'last_update';
$qadmin_def['last_update']['type'] = 'echo';
$qadmin_def['last_update']['size'] = 10;
$qadmin_def['last_update']['value'] = 'sql';
if ($id) {
    $qadmin_def['last_update']['value'] = convert_date(date('Y-m-d', $page_info['last_update'])).' @ '.date('h:ia', $page_info['last_update']);
}

// page_rating :: real :: 4
$qadmin_def['page_rating']['title'] = 'Rating';
$qadmin_def['page_rating']['field'] = 'page_rating';
$qadmin_def['page_rating']['type'] = 'echo';
$qadmin_def['page_rating']['size'] = 4;
$qadmin_def['page_rating']['value'] = 'sql';

// page_comment :: int :: 10
$qadmin_def['page_comment']['title'] = 'Comments';
$qadmin_def['page_comment']['field'] = 'page_comment';
$qadmin_def['page_comment']['type'] = 'echo';
$qadmin_def['page_comment']['size'] = 10;
$qadmin_def['page_comment']['value'] = 'sql';

// page_hit :: int :: 10
$qadmin_def['page_hit']['title'] = 'Hits';
$qadmin_def['page_hit']['field'] = 'page_hit';
$qadmin_def['page_hit']['type'] = 'echo';
$qadmin_def['page_hit']['size'] = 10;
$qadmin_def['page_hit']['value'] = 'sql';

// notes
if ($id) {
    $qadmin_def['notes1']['title'] = 'Custom Design';
    $qadmin_def['notes1']['field'] = 'notes1';
    $qadmin_def['notes1']['type'] = 'echo';
    $qadmin_def['notes1']['value'] = 'By default, qEngine will use <kbd>/skins/[your_skin]/page.tpl</kbd> to display this page. To create your own design, simply create a <kbd>/skins/[your_skin]/page_'.$id.'.tpl</kbd> file.';
}


// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'page';					// table name
$qadmin_cfg['primary_key'] = 'page_id';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['permalink_script'] = 'page.php';				// script name for permalink
$qadmin_cfg['permalink_source'] = 'page_title';				// script name for permalink
$qadmin_cfg['permalink_folder'] = $rule_def['page_folder'];				// script name for permalink
$qadmin_cfg['post_process'] = 'post_func';
$qadmin_cfg['rebuild_cache'] = true;

// log
$qadmin_cfg['log_title'] = 'page_title';	// qadmin field to be used as log title (REQUIRED even if you don't use log)

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// filter
$fs = $ft = ''; foreach ($group_def as $k => $v) {
    $fs .= "group_id='$k',";
    $ft .= "$v,";
}; $fs = substr($fs, 0, -1); $ft = substr($ft, 0, -1);
$qadmin_cfg['search_filterby'] = $fs;	// filter by sql_query (use , to separate queries) *
$qadmin_cfg['search_filtermask'] = $ft;				// mask filter *

// search configuration
$qadmin_cfg['search_key'] = 'page_id,group_id,page_title,page_body,page_status';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Group,Content Title,Content,Status';	// mask other key
$qadmin_cfg['search_result_mask'] = ',group_def,,,status_def';

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// jq
$qadmin_cfg['footer'] =
"<script type=\"text/javascript\">
//<![CDATA[
var sss = '$page_info[page_related]';

$('#".$db_prefix."page-group_id>select').change (function () { document.location = 'page.php?id=$id&qadmin_cmd=new&gid='+this.value });
$('#".$db_prefix."page-page_related>input').tokenInput('admin_ajax.php?cmd=related_page', { queryParam:'query', preventDuplicates:true, prePopulate:$related_preset});
//]]>
</script>";

// form title
$qadmin_title['new'] = 'Add Page';
$qadmin_title['update'] = 'Update Page';
$qadmin_title['search'] = 'Search Page';
$qadmin_title['list'] = 'Page List';

// disabled fields
if ($mode == 'new') {
    $qadmin_def['page_author']['type'] = 'disabled';
    $qadmin_def['page_body']['value'] = $qadmin_def['page_author']['value'] = 'Please choose a group first.';
} elseif ($mode == 'new_change') {
    $qadmin_def['page_author']['value'] = $current_user_id;
}

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 'page_editor';
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
