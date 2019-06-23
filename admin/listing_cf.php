<?php
// called after add/edit/del anything
function post_func($cmd, $id, $savenew, $old, $new)
{
    global $db_prefix, $config, $dbh, $dir_def, $dir_short_def;

    // recreate cache
    qcache_clear('dir_'.$new['dir_id'].'.%', false);

    // if delete..
    if ($old['is_removed'] || $new['is_removed']) {
        // remove from menu
        sql_query("DELETE FROM ".$db_prefix."menu_item WHERE idx='$old[menu_item_id]' LIMIT 1");

        // remove field
        $foo = sql_qquery("SHOW COLUMNS FROM `".$db_prefix."listing_cf_value` LIKE 'cf_".$id."'");
        if (!empty($foo)) {
            sql_query("ALTER TABLE `".$db_prefix."listing_cf_value` DROP `cf_".$id."`");
        }

        // finally remove item
        sql_query("DELETE FROM ".$db_prefix."listing_cf_define WHERE idx='$id' LIMIT 1");
        $url = safe_send($config['site_url'].'/'.$config['admin_folder']."/listing_cf.php");
        redir($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=reorder3&midx=$old[menu_idx]&return_url=$url");
    }

    // otherwise, do some maintenances
    $row = $new;
    $row['cf_title'] = addslashes($row['cf_title']);
    $row['cf_help'] = addslashes($row['cf_help']);
    if (!empty($row['cf_help'])) {
        $cf_notes_str = $row['cf_title'].' - '.$row['cf_help'];
    } else {
        $cf_notes_str = $row['cf_title'];
    }

    // update menu_item
    $dir_title_str = $dir_def[$row['dir_id']];
    $dir_short_str = $dir_short_def[$row['dir_id']];
    $dir_menu_id = 'dir_cf.'.$row['dir_id'];

    // - get menu id
    $f = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE menu_id='dir_cf_$dir_short_str' AND menu_preset='$dir_menu_id' LIMIT 1");
    if (empty($f)) {
        die($dir_short_str.' is not a valid directory identifier! Something is really wrong...');
    } else {
        $mid = $f['idx'];
    }

    if ($cmd == 'new') {
        // if new cf -> add to menu
        sql_query("INSERT INTO ".$db_prefix."menu_item SET menu_id='$mid', menu_item='$cf_notes_str', menu_order='999999', ref_idx='$id'");
        $mmid = mysqli_insert_id($dbh);
        sql_query("UPDATE ".$db_prefix."listing_cf_define SET menu_idx='$mid', menu_item_id='$mmid' WHERE idx='$id' LIMIT 1");

        // create new mysql column
        if (($row['cf_type'] == 'textarea') || ($row['cf_type'] == 'multi')) {
            sql_query("ALTER TABLE `".$db_prefix."listing_cf_value` ADD `cf_".$id."` text COLLATE 'utf8_general_ci' NOT NULL");
        } else {
            sql_query("ALTER TABLE `".$db_prefix."listing_cf_value` ADD `cf_".$id."` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ADD INDEX `cf_".$id."` (`cf_".$id."`)");
        }
    }

    if ($cmd == 'update') {
        // update menu
        sql_query("UPDATE ".$db_prefix."menu_item SET menu_id='$mid', menu_item='$cf_notes_str' WHERE idx='$row[menu_item_id]' LIMIT 1");
    }

    // otherwise, recreate cache, and redirect back to this page
    if ($savenew) {
        $url = safe_send($config['site_url'].'/'.$config['admin_folder'].'/listing_cf.php?qadmin_cmd=new');
    } else {
        $url = safe_send($config['site_url'].'/'.$config['admin_folder'].'/listing_cf.php?id='.$id);
    }
    redir($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=reorder3&midx=$row[menu_idx]&return_url=$url");
}

// important files
require "./../includes/admin_init.php";
admin_check(4);
$id = get_param('id');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($id)) {
    $id = post_param('primary_val');
}

// cf def
$field_def = array();
$field_def['***1'] = 'Common Inputs';
$field_def['varchar'] = 'Short Text';
$field_def['textarea'] = 'Long Text';
$field_def['select'] = 'Drop Down Select';
$field_def['multi'] = 'Multi Select';
$field_def['///1'] = '';
$field_def['***2'] = 'More Inputs';
$field_def['img'] = 'Image Upload';
$field_def['file'] = 'File Upload';
$field_def['rating'] = 'Rating';
$field_def['date'] = 'Date';
$field_def['time'] = 'Time';
$field_def['url'] = 'URL';
$field_def['video'] = 'Video Embed';
//$field_def['datetime'] = 'Date &amp; Time';
$field_def['///2'] = '';
$field_def['***3'] = 'Address';
$field_def['tel'] = 'Phone Number';
$field_def['country'] = 'Country List';
$field_def['gmap'] = 'Google Map Location';
$field_def['///3'] = '';
$field_def['***4'] = 'Others';
$field_def['div'] = 'Separator';
$field_def['///4'] = '';

//
if ($dir_info['config']['number'] < 1) {
    admin_die(sprintf($lang['msg']['echo'], 'Please define at least one directory first!'));
}
if (is_numeric($id)) {
    $inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_define WHERE idx='$id' LIMIT 1");
    $foo = array($inf['cf_type'] => $field_def[$inf['cf_type']]);
    $field_def = $foo;
}

// dir def
$dir_def = $dir_id_def = array();
$res = sql_query("SELECT idx, dir_short, dir_title FROM ".$db_prefix."listing_dir ORDER BY dir_title");
while ($row = sql_fetch_array($res)) {
    $dir_def[$row['idx']] = $row['dir_title'];
    $dir_short_def[$row['idx']] = $row['dir_short'];
}

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['value'] = 'sql';

// dir_id :: string :: 765
$qadmin_def['dir_id']['title'] = 'Directory';
$qadmin_def['dir_id']['field'] = 'dir_id';
$qadmin_def['dir_id']['type'] = ($id && $id != 'dummy') ? 'mask' : 'select';
$qadmin_def['dir_id']['option'] = $dir_def;
$qadmin_def['dir_id']['value'] = 'sql';
$qadmin_def['dir_id']['required'] = is_numeric($id) ? false : true;

// cf_title :: string :: 765
$qadmin_def['cf_title']['title'] = 'Title';
$qadmin_def['cf_title']['field'] = 'cf_title';
$qadmin_def['cf_title']['type'] = 'varchar';
$qadmin_def['cf_title']['size'] = 255;
$qadmin_def['cf_title']['value'] = 'sql';
$qadmin_def['cf_title']['required'] = true;
$qadmin_def['cf_title']['suffix'] = is_numeric($id) ? '<a href="menu_man.php?cmd=design&amp;midx='.$inf['menu_idx'].'" classs="btn btn-default"><span class="glyphicon glyphicon-sort"></span> Reorder</a>' : '';

// cf_type :: string :: 30
$qadmin_def['cf_type']['title'] = 'Type';
$qadmin_def['cf_type']['field'] = 'cf_type';
$qadmin_def['cf_type']['type'] = 'select';
$qadmin_def['cf_type']['option'] = $field_def;
$qadmin_def['cf_type']['value'] = 'sql';
$qadmin_def['cf_type']['help'] = 'Short Text = 255 characters. Long Text = 65,000 characters. Separator = acts to separate custom fields, it doesn&apos;t have any input.';
$qadmin_def['cf_type']['required'] = true;

// cf_help :: string :: 765
$qadmin_def['cf_help']['title'] = 'Help Text';
$qadmin_def['cf_help']['field'] = 'cf_help';
$qadmin_def['cf_help']['type'] = 'varchar';
$qadmin_def['cf_help']['size'] = 255;
$qadmin_def['cf_help']['value'] = 'sql';

// cf_option :: blob :: 196605
$qadmin_def['cf_option']['title'] = 'Options';
$qadmin_def['cf_option']['field'] = 'cf_option';
$qadmin_def['cf_option']['type'] = 'text';
$qadmin_def['cf_option']['size'] = '500,200';
$qadmin_def['cf_option']['value'] = 'sql';
$qadmin_def['cf_option']['help'] = 'Only available for drop down select &amp; multi select, use &amp;lt;enter&amp;gt; to separate values.';

// avail_to :: blob :: 196605
$qadmin_def['avail_to']['title'] = 'Available To';
$qadmin_def['avail_to']['field'] = 'avail_to';
$qadmin_def['avail_to']['type'] = 'multicsv';
$qadmin_def['avail_to']['option'] = $listing_class_def;
$qadmin_def['avail_to']['value'] = is_numeric($id) ? 'sql' : 'R,P,S';

// is_required :: string :: 3
$qadmin_def['is_required']['title'] = 'Required?';
$qadmin_def['is_required']['field'] = 'is_required';
$qadmin_def['is_required']['type'] = 'radio';
$qadmin_def['is_required']['option'] = $yesno;
$qadmin_def['is_required']['value'] = 'sql';

// is_searchable :: string :: 3
$qadmin_def['is_searchable']['title'] = 'Searchable?';
$qadmin_def['is_searchable']['field'] = 'is_searchable';
$qadmin_def['is_searchable']['type'] = 'radio';
$qadmin_def['is_searchable']['option'] = $yesno;
$qadmin_def['is_searchable']['value'] = 'sql';
$qadmin_def['is_searchable']['help'] = 'Also for listing filtering. Not all cf are searchable. URL, phone, Google Maps, images &amp; files are not searchable.<br />CAUTION! Searchable multi select may affect performance!';

// is_list :: string :: 3
$qadmin_def['is_list']['title'] = 'Displayed in Search Results?';
$qadmin_def['is_list']['field'] = 'is_list';
$qadmin_def['is_list']['type'] = 'radio';
$qadmin_def['is_list']['option'] = $yesno;
$qadmin_def['is_list']['value'] = 'sql';
$qadmin_def['is_list']['help'] = 'Also affect search results. Not all cf are displayed in list page. Google Maps, images &amp; files are not listable.';

if ($id) {
    // dir_logo :: 254 :: 0
    $qadmin_def['div3']['title'] = 'Remove';
    $qadmin_def['div3']['field'] = 'div3';
    $qadmin_def['div3']['type'] = 'div';

    // dir_pre_fee :: 246 :: 0
    $qadmin_def['is_removed']['title'] = 'Remove this custom field?';
    $qadmin_def['is_removed']['field'] = 'is_removed';
    $qadmin_def['is_removed']['type'] = 'checkbox';
    $qadmin_def['is_removed']['value'] = 'sql';
    $qadmin_def['is_removed']['option'] = '<span class="text-danger bg-danger">Yes, remove this custom field along with its values. This operation can not be undone.</span>';
    $qadmin_def['is_removed']['help'] = 'This operation may take a long time to finish, and may time out. If it happens, simply re-submit this form to continue process.';
}

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'listing_cf_define';							// table name
$qadmin_cfg['primary_key'] = 'idx';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['ezf_mode'] = false;							// TRUE to use EZF mode (see ./_qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['ezd_mode'] = false;							// TRUE to use ezDesign mode (see ./qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['post_process'] = 'post_func';

// logging
$qadmin_cfg['enable_log'] = true;			// log all changes (add/edit/remove), default = from qe_config
$qadmin_cfg['detailed_log'] = true;			// store modification values (may be big!), default = from qe_config
$qadmin_cfg['log_title'] = 'cf_title';	// qadmin field to be used as log title (empty = disable log, no matter other cfg's)

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,dir_id,cf_title,cf_type,cf_help,is_searchable';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Directory,Title,Type,Help,Searchable?';	// mask other key
$qadmin_cfg['search_result_mask'] = ',dir_def,,field_def,,yesno';	// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = false;

// jquery qe_listing_cf_define-cf_type
$qadmin_cfg['footer'] =
'<script>
function showfield()
{
	val = $("#'.$qadmin_cfg['table'].'-cf_type>select").val();
	if ((val == "select") || (val == "multi"))
		$("#'.$qadmin_cfg['table'].'-cf_option>textarea").fadeIn();
	else
		$("#'.$qadmin_cfg['table'].'-cf_option>textarea").fadeOut();
}
$("#'.$qadmin_cfg['table'].'-cf_type>select").change(function () { showfield() });

showfield();
</script>';

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = '4';

// form title
$qadmin_title['new'] = 'Add Custom Field';
$qadmin_title['update'] = 'Update Custom Field';
$qadmin_title['search'] = 'Search Custom Field';
$qadmin_title['list'] = 'Custom Field List';

qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
