<?php
function get_tree($midx, $id = 0, $level = 0)
{
    $cid = get_param('id');
    global $menu_list, $db_prefix;
    $res = sql_query("SELECT * FROM ".$db_prefix."menu_item WHERE menu_id='$midx' AND menu_parent='$id' AND idx != '$cid'");
    while ($row = sql_fetch_array($res)) {
        $iid = $row['idx'];
        $menu_list[$iid] = str_repeat('&rarr;', $level).' '.$row['menu_item'];
        get_tree($midx, $iid, $level + 1);
    }
}

function post_func($cmd, $id, $savenew = false)
{
    global $config, $db_prefix;

    // only works for new item
    if ($cmd == 'new') {
        sql_query("UPDATE ".$db_prefix."menu_item SET menu_order = 9999999 WHERE idx='$id' LIMIT 1");
    }
    $row = sql_qquery("SELECT * FROM ".$db_prefix."menu_item WHERE idx='$id' LIMIT 1");

    // update sub menu, remove title
    if (substr($row['menu_url'], 0, 5) == '[[sm:') {
        sql_query("UPDATE ".$db_prefix."menu_item SET menu_item='#' WHERE idx='$id' LIMIT 1");
    }

    if ($savenew) {
        $url = safe_send($config['site_url']."/$config[admin_folder]/menu_item.php?qadmin_cmd=new&midx=$row[menu_id]&parent=$row[menu_parent]");
        redir($config['site_url']."/$config[admin_folder]/menu_man.php?cmd=reorder3&midx=$row[menu_id]&return_url=$url");
    } else {
        redir($config['site_url']."/$config[admin_folder]/menu_man.php?cmd=reorder&midx=$row[menu_id]");
    }
}

// part of qEngine
require './../includes/admin_init.php';

// get params
$id = get_param('id');
$midx = get_param('midx');
$parent = get_param('parent');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($id)) {
    $id = post_param('primary_val');
}

// mode
if (empty($id)) {
    $cmd = 'new';
} else {
    $cmd = 'edit';
}

// locked?
$foo = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
if ($foo['menu_locked']) {
    admin_die($lang['msg']['menuman_locked_err']);
}

// create parents
$menu_list = array();
get_tree($midx);

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['size'] = 10;
$qadmin_def['idx']['value'] = 'sql';

// menu_id :: string :: 255
$qadmin_def['menu_id']['title'] = 'Menu ID';
$qadmin_def['menu_id']['field'] = 'menu_id';
$qadmin_def['menu_id']['type'] = 'static';
$qadmin_def['menu_id']['size'] = 255;
$qadmin_def['menu_id']['value'] = ($cmd == 'new') ? $midx : 'sql';

// menu_parent :: int :: 10
$qadmin_def['menu_parent']['title'] = 'Parent';
$qadmin_def['menu_parent']['field'] = 'menu_parent';
$qadmin_def['menu_parent']['type'] = 'select';
$qadmin_def['menu_parent']['option'] = $menu_list;
$qadmin_def['menu_parent']['value'] = ($parent) ? $parent : 'sql';

// menu_item :: string :: 255
$qadmin_def['menu_item']['title'] = 'Title';
$qadmin_def['menu_item']['field'] = 'menu_item';
$qadmin_def['menu_item']['type'] = 'varchar';
$qadmin_def['menu_item']['size'] = 255;
$qadmin_def['menu_item']['value'] = 'sql';
$qadmin_def['menu_item']['required'] = true;
$qadmin_def['menu_item']['help'] = 'You can also use simple HTML tags. Use __SITE__ to insert your site URL.';

// menu_url :: string :: 255
$qadmin_def['menu_url']['title'] = 'URL';
$qadmin_def['menu_url']['field'] = 'menu_url';
$qadmin_def['menu_url']['type'] = 'varchar';
$qadmin_def['menu_url']['size'] = 255;
$qadmin_def['menu_url']['value'] = 'sql';
$qadmin_def['menu_url']['help'] = 'Enter a URL, or if you referring to an internal page, enter the page ID.<br />Enter # to remove links.<br />Enter --- to insert separator.<br />Use __SITE__ to insert your site URL.<br />You can also include another menu by using [[sm:menu_id]].';

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'menu_item';					// table name
$qadmin_cfg['primary_key'] = 'idx';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['back'] = $config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design&amp;midx='.$midx;

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,menu_id,menu_item';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Menu ID,Title';	// mask other key

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = false;
$qadmin_cfg['post_process'] = 'post_func';

// jq
$qadmin_cfg['footer'] =
"<script type=\"text/javascript\">
//<![CDATA[
$('#".$db_prefix."menu_item-menu_url>input').autocomplete({ serviceUrl:'admin_ajax.php', params:{cmd:'page'}, onSelect: function(result){  $('#".$db_prefix."menu_item-menu_url>input').val(result.data) } });
//]]>
</script>";

// form title
$qadmin_title['new'] = 'Add Item';
$qadmin_title['update'] = 'Update Item';
$qadmin_title['search'] = 'Search Item';
$qadmin_title['list'] = 'Item List';

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = 'manage_menu';
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
