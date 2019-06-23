<?php
function post_func($cmd, $id)
{
    global $config, $db_prefix;

    // verify the ID
    $row = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$id' LIMIT 1");

    // only works for new item
    if ($cmd == 'new') {
        // is it empty?
        if (empty($row['menu_id'])) {
            $new_mid = strtolower(preg_replace("/[^a-zA-Z0-9]/", "_", $row['menu_title']));
            sql_query("UPDATE ".$db_prefix."menu_set SET menu_id='$new_mid' WHERE idx='$id' LIMIT 1");
            $row['menu_id'] = $new_mid;
        }

        // is it unique?
        $mid = $row['menu_id'];
        $row = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE menu_id='$mid' AND idx != '$id' LIMIT 1");
        if (!empty($row)) {
            $new_mid = $mid.'_'.$id;
            sql_query("UPDATE ".$db_prefix."menu_set SET menu_id='$new_mid' WHERE idx='$id' LIMIT 1");
        }

        admin_die('admin_ok', '', $config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=reorder&midx='.$id);
    } else {
        redir($config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=reorder2&midx='.$id);
    }
}

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

// locked?
$foo = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$id' LIMIT 1");
if ($foo['menu_locked']) {
    admin_die($lang['msg']['menuman_locked_err']);
}

// mode
if (empty($id)) {
    $cmd = 'new';
} else {
    $cmd = 'edit';
}

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['size'] = 10;
$qadmin_def['idx']['value'] = 'sql';

// menu_id :: string :: 255
$qadmin_def['menu_id']['title'] = 'Unique ID';
$qadmin_def['menu_id']['field'] = 'menu_id';
$qadmin_def['menu_id']['type'] = 'echo';
$qadmin_def['menu_id']['size'] = 255;
$qadmin_def['menu_id']['value'] = 'sql';

// menu_title :: string :: 255
$qadmin_def['menu_title']['title'] = 'Title';
$qadmin_def['menu_title']['field'] = 'menu_title';
$qadmin_def['menu_title']['type'] = 'varchar';
$qadmin_def['menu_title']['size'] = 255;
$qadmin_def['menu_title']['value'] = 'sql';
$qadmin_def['menu_title']['required'] = true;

// menu_preset :: int :: 10
$qadmin_def['menu_preset']['title'] = 'Preset Style';
$qadmin_def['menu_preset']['field'] = 'menu_preset';
$qadmin_def['menu_preset']['type'] = 'select';
$qadmin_def['menu_preset']['option'] = $menu_man_preset;
$qadmin_def['menu_preset']['value'] = 'sql';
$qadmin_def['menu_preset']['help'] = 'Drop down menu only supports two level menus. If you choose Other, please fill the User Class field below!';

// menu_class :: string :: 255
$qadmin_def['menu_class']['title'] = 'User Class';
$qadmin_def['menu_class']['field'] = 'menu_class';
$qadmin_def['menu_class']['type'] = 'varchar';
$qadmin_def['menu_class']['size'] = 255;
$qadmin_def['menu_class']['value'] = 'sql';
$qadmin_def['menu_class']['help'] = 'Only fill this field, if you choose Other for Preset Style. Fill this field with your own CSS class, eg: myownmenu';

// menu_notes :: blob :: 65535
$qadmin_def['menu_notes']['title'] = 'Notes';
$qadmin_def['menu_notes']['field'] = 'menu_notes';
$qadmin_def['menu_notes']['type'] = 'varchar';
$qadmin_def['menu_notes']['size'] = 255;
$qadmin_def['menu_notes']['value'] = 'sql';

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'menu_set';					// table name
$qadmin_cfg['primary_key'] = 'idx';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['log_title'] = 'menu_title';					// qadmin field to be used as log title (REQUIRED even if you don't use log)
$qadmin_cfg['back'] = 'menu_man.php';

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,menu_id,menu_title';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Unique ID,Title';	// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = false;
$qadmin_cfg['post_process'] = 'post_func';

// form title
$qadmin_title['new'] = 'Add Menu';
$qadmin_title['update'] = 'Update Menu';
$qadmin_title['search'] = 'Search Menu';
$qadmin_title['list'] = 'Menu List';

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 'manage_menu';
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
