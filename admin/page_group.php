<?php
// part of qEngine
require './../includes/admin_init.php';

// comment def
$comment_def = array(0 => '- Disabled -');
if ($config['enable_module_engine']) {
    $res = sql_query("SELECT mod_id, notes FROM ".$db_prefix."qcomment_set WHERE (mod_id != 'conc') && (mod_id != 'gbook') ORDER BY notes");
    while ($row = sql_fetch_array($res)) {
        $comment_def[$row['mod_id']] = $row['notes'];
    }
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

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['size'] = 10;
$qadmin_def['idx']['value'] = 'sql';

// group_id :: string :: 5
$qadmin_def['group_id']['title'] = 'Identifier';
$qadmin_def['group_id']['field'] = 'group_id';
$qadmin_def['group_id']['type'] = 'varchar';
$qadmin_def['group_id']['size'] = 5;
$qadmin_def['group_id']['value'] = 'sql';
$qadmin_def['group_id']['unique'] = 'true';
$qadmin_def['group_id']['required'] = 'true';
$qadmin_def['group_id']['help'] = 'Must be unique!';

// group_title :: string :: 255
$qadmin_def['group_title']['title'] = 'Title';
$qadmin_def['group_title']['field'] = 'group_title';
$qadmin_def['group_title']['type'] = 'varchar';
$qadmin_def['group_title']['size'] = 255;
$qadmin_def['group_title']['value'] = 'sql';

// group_notes :: blob :: 65535
$qadmin_def['group_notes']['title'] = 'Notes';
$qadmin_def['group_notes']['field'] = 'group_notes';
$qadmin_def['group_notes']['type'] = 'varchar';
$qadmin_def['group_notes']['size'] = 255;
$qadmin_def['group_notes']['value'] = 'sql';

// page_folder :: string :: 75
$qadmin_def['page_folder']['title'] = 'Virtual Folder for SEF URL';
$qadmin_def['page_folder']['field'] = 'page_folder';
$qadmin_def['page_folder']['type'] = 'varchar';
$qadmin_def['page_folder']['size'] = 25;
$qadmin_def['page_folder']['value'] = 'sql';
$qadmin_def['page_folder']['help'] = 'Eg, enter &quot;read&quot; to display your URL as: www.example.com/read/my-page-title.php.';

// page_comment :: string :: 1
$qadmin_def['page_comment']['title'] = 'Allow Comment?';
$qadmin_def['page_comment']['field'] = 'page_comment';
$qadmin_def['page_comment']['type'] = 'select';
$qadmin_def['page_comment']['option'] = $comment_def;
$qadmin_def['page_comment']['value'] = 'sql';
$qadmin_def['page_comment']['help'] = 'Pick a comment rule. You can define one in Modules &gt; qComment &gt; Rules. Requires qComment module.';

// group_title :: string :: 255
$qadmin_def['group_template']['title'] = 'Main Template';
$qadmin_def['group_template']['field'] = 'group_template';
$qadmin_def['group_template']['type'] = 'select';
$qadmin_def['group_template']['option'] = $group_template_def;
$qadmin_def['group_template']['value'] = 'sql';
$qadmin_def['group_template']['help'] = 'You can define a sub template for this type of contents by creating body_[your_template].tpl, and put it in /skins/_common folder';

// group_title :: string :: 255
$qadmin_def['page_template']['title'] = 'Page Default Template';
$qadmin_def['page_template']['field'] = 'page_template';
$qadmin_def['page_template']['type'] = 'select';
$qadmin_def['page_template']['option'] = $page_template_def;
$qadmin_def['page_template']['value'] = 'sql';
$qadmin_def['page_template']['help'] = 'You can define a default page template for this type of content by creating page_[your_template].tpl, and put it in /skins/_common folder';

//
$qadmin_def['div2']['title'] = 'File Upload';
$qadmin_def['div2']['field'] = 'div2';
$qadmin_def['div2']['type'] = 'div';

// page_image :: string :: 1
$qadmin_def['page_image']['title'] = 'Main Image';
$qadmin_def['page_image']['field'] = 'page_image';
$qadmin_def['page_image']['type'] = 'radio';
$qadmin_def['page_image']['option'] = $yesno;
$qadmin_def['page_image']['value'] = 'sql';
$qadmin_def['page_image']['help'] = 'Allow editor to upload an image for this page type.';

// page_image_size :: int :: 10
$qadmin_def['page_image_size']['title'] = 'Main Image Resize';
$qadmin_def['page_image_size']['field'] = 'page_image_size';
$qadmin_def['page_image_size']['type'] = 'varchar';
$qadmin_def['page_image_size']['size'] = 10;
$qadmin_def['page_image_size']['value'] = 'sql';
$qadmin_def['page_image_size']['help'] = 'Enter 0 (zero) to disable image resizing. If you change this value, you have to re-upload the images!';

// page_thumb :: int :: 10
$qadmin_def['page_thumb']['title'] = 'Main Thumbnail Size';
$qadmin_def['page_thumb']['field'] = 'page_thumb';
$qadmin_def['page_thumb']['type'] = 'varchar';
$qadmin_def['page_thumb']['size'] = 10;
$qadmin_def['page_thumb']['value'] = 'sql';
$qadmin_def['page_thumb']['help'] = 'Enter 0 (zero) to disable thumbnail. If you change this value, you have to re-upload the images!';

// page_gallery :: string :: 1
$qadmin_def['page_gallery']['title'] = 'Enable Gallery?';
$qadmin_def['page_gallery']['field'] = 'page_gallery';
$qadmin_def['page_gallery']['type'] = 'radio';
$qadmin_def['page_gallery']['option'] = $yesno;
$qadmin_def['page_gallery']['value'] = 'sql';
$qadmin_def['page_gallery']['help'] = 'Allow you to upload images to create an image gallery.';

// page_gallery_thumb :: int :: 10
$qadmin_def['page_gallery_thumb']['title'] = 'Gallery Thumbnail Size';
$qadmin_def['page_gallery_thumb']['field'] = 'page_gallery_thumb';
$qadmin_def['page_gallery_thumb']['type'] = 'varchar';
$qadmin_def['page_gallery_thumb']['size'] = 10;
$qadmin_def['page_gallery_thumb']['value'] = 'sql';
$qadmin_def['page_gallery_thumb']['help'] = 'If you change this value, you have to re-upload the images!';

// page_attachment :: string :: 1
$qadmin_def['page_attachment']['title'] = 'Allow file attachment?';
$qadmin_def['page_attachment']['field'] = 'page_attachment';
$qadmin_def['page_attachment']['type'] = 'radio';
$qadmin_def['page_attachment']['option'] = $yesno;
$qadmin_def['page_attachment']['value'] = 'sql';


$qadmin_def['div1']['title'] = 'Advanced';
$qadmin_def['div1']['field'] = 'div1';
$qadmin_def['div1']['type'] = 'div';

// hidden_private :: string :: 1
$qadmin_def['hidden_private']['title'] = 'Hide This Type?';
$qadmin_def['hidden_private']['field'] = 'hidden_private';
$qadmin_def['hidden_private']['type'] = 'radio';
$qadmin_def['hidden_private']['option'] = $yesno;
$qadmin_def['hidden_private']['value'] = 'sql';
$qadmin_def['hidden_private']['help'] = 'Hide this type so admin can not select it when creating a new content (aka script use only).';

// cat_list :: string :: 1
$qadmin_def['cat_list']['title'] = 'Allow Page Listing';
$qadmin_def['cat_list']['field'] = 'cat_list';
$qadmin_def['cat_list']['type'] = 'radio';
$qadmin_def['cat_list']['option'] = $yesno;
$qadmin_def['cat_list']['value'] = 'sql';
$qadmin_def['cat_list']['help'] = 'Allow users to view list of pages for this group. Also affect Site Map.';

// page_sort :: string :: 1
$qadmin_def['page_sort']['title'] = 'Page Default Sorting';
$qadmin_def['page_sort']['field'] = 'page_sort';
$qadmin_def['page_sort']['type'] = 'radio';
$qadmin_def['page_sort']['option'] = $page_sort;	// from /includes/vars.php
$qadmin_def['page_sort']['value'] = 'sql';

// page_cat :: string :: 1
$qadmin_def['page_cat']['title'] = 'Display Category Path (Breadcrumbs)';
$qadmin_def['page_cat']['field'] = 'page_cat';
$qadmin_def['page_cat']['type'] = 'radio';
$qadmin_def['page_cat']['option'] = $yesno;
$qadmin_def['page_cat']['value'] = 'sql';

// all_cat_list :: string :: 1
$qadmin_def['all_cat_list']['title'] = 'Display Other Categories in Page Listing?';
$qadmin_def['all_cat_list']['field'] = 'all_cat_list';
$qadmin_def['all_cat_list']['type'] = 'radio';
$qadmin_def['all_cat_list']['option'] = $yesno;
$qadmin_def['all_cat_list']['value'] = 'sql';

// page_author :: string :: 1
$qadmin_def['page_author']['title'] = 'Display Page Author?';
$qadmin_def['page_author']['field'] = 'page_author';
$qadmin_def['page_author']['type'] = 'radio';
$qadmin_def['page_author']['option'] = $yesno;
$qadmin_def['page_author']['value'] = 'sql';

// page_date :: string :: 1
$qadmin_def['page_date']['title'] = 'Display Date?';
$qadmin_def['page_date']['field'] = 'page_date';
$qadmin_def['page_date']['type'] = 'radio';
$qadmin_def['page_date']['option'] = $yesno;
$qadmin_def['page_date']['value'] = 'sql';

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'page_group';				// table name
$qadmin_cfg['primary_key'] = 'idx';							// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['fastsearch'] = 'page';							// fast search ID
$qadmin_cfg['rebuild_cache'] = true;							// rebuild cache
$qadmin_cfg['log_title'] = 'group_title';					// qadmin field to be used as log title (REQUIRED even if you don't use log)

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';			// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';			// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,group_title,group_notes';	// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Title,Notes';			// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;

// form title
$qadmin_title['new'] = 'Add Type';
$qadmin_title['update'] = 'Update Type';
$qadmin_title['search'] = 'Search Type';
$qadmin_title['list'] = 'Type List';

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 'page_manager';

// auto sql query generated by qAdmin: "SELECT * FROM table WHERE primary_key='primary_val' LIMIT 1"
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
