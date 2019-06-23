<?php
// called after add/edit/del anything
function post_func($cmd, $id, $savenew = false, $old, $new)
{
    global $db_prefix, $config, $cat_structure, $cat_structure_id, $cat_name_def, $dbh, $dir_info;

    // if delete
    if ($new['is_removed'] || $old['is_removed']) {
        html_header();
        echo '<h1>Removing Category...</h1>';
        echo '<p>This may take a while, if the script is time out, please resubmit the form to continue removal.';
        echo '<p>Removing listings ';

        // 'remove' items from this cat & its children, grand children, great grand... <descendant>
        // by 'remove' it means simply update item's category to '0', as we don't want to remove items yet (it could be listed in other cats)
        $list = get_cat_child($old['dir_id'], $id, true);
        $list[] = $id;
        foreach ($list as $k) {
            $c = create_cat_where($k);
            $res = sql_query("SELECT * FROM ".$db_prefix."listing WHERE $c", 1);
            while ($row = sql_fetch_array($res)) {
                for ($i = 1; $i <= 6; $i++) {
                    if ($row['category_'.$i] == $k) {
                        $f = 'category_'.$i;
                        sql_query("UPDATE ".$db_prefix."listing SET $f='0' WHERE idx='$row[idx]' LIMIT 1");
                        recount_num_link($old['dir_id'], $k, 'dec');
                    }
                }
                echo '. ';
            }
        }
        echo '</p>';

        // - remove orphaned listing
        $c = create_cat_where(0, 6, 'AND');
        $res = sql_query("SELECT idx FROM ".$db_prefix."listing WHERE $c");
        while ($row = sql_fetch_array($res)) {
            remove_item($row['idx']);
        }

        // finally, remove cat & its descendant
        foreach ($list as $k) {
            $foo = sql_qquery("SELECT * FROM ".$db_prefix."listing_cat WHERE idx='$k' LIMIT 1");

            // remove permalink
            sql_query("DELETE FROM ".$db_prefix."permalink WHERE target_script='listing_search.php' AND target_idx='$foo[idx]' LIMIT 1");

            // cat menu
            sql_query("DELETE FROM ".$db_prefix."menu_item WHERE idx='$foo[menu_item_id]' LIMIT 1");

            // remove cat
            sql_query("DELETE FROM ".$db_prefix."listing_cat WHERE idx='$k' LIMIT 1");
        }

        echo '<h1>Finished!</h1>';
        html_footer();
        $url = safe_send($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=design&midx=".$old['menu_idx']);
        redir($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=reorder3&midx=$old[menu_idx]&return_url=$url");
    }

    // recreate cache
    qcache_clear('dir_'.$new['dir_id'].'.%', false);

    // if new cat -> add to menu
    if (($cmd == 'new') || ($cmd == 'update')) {
        $row = $new;
        $mid = $row['menu_idx'];
        $row['cat_name'] = addslashes($row['cat_name']);
        $url = '__SITE__/listing_search.php?cmd=list&amp;cat_id='.$row['idx'];
        $permalink = '__SITE__/'.$row['permalink'];

        // get menu parent
        if (!empty($row['parent_id'])) {
            $p = sql_qquery("SELECT menu_item_id FROM ".$db_prefix."listing_cat WHERE idx='$row[parent_id]' LIMIT 1");
            $parent_mid = $p['menu_item_id'];
        } else {
            $parent_mid = 0;
        }

        if ($cmd == 'new') {
            sql_query("INSERT INTO ".$db_prefix."menu_item SET menu_id='$mid', menu_parent='$parent_mid', menu_item='$row[cat_name]', menu_url='$url', menu_permalink='$permalink', menu_order='999999', ref_idx='$id'");
            $row['menu_item_id'] = $mmid = mysqli_insert_id($dbh);

            // get dir id from menu_set
            $menu_set = sql_qquery("SELECT menu_preset FROM ".$db_prefix."menu_set WHERE idx='$mid' LIMIT 1");
            $did = substr($menu_set['menu_preset'], 8);
            sql_query("UPDATE ".$db_prefix."listing_cat SET dir_id='$did', menu_item_id='$mmid' WHERE idx='$id' LIMIT 1");
        } else {
            sql_query("UPDATE ".$db_prefix."menu_item SET menu_id='$mid', menu_item='$row[cat_name]', menu_url='$url', menu_permalink='$permalink' WHERE idx='$row[menu_item_id]' LIMIT 1");
        }
    }

    // otherwise, recreate menu, and redirect back to this page
    $url = safe_send($config['site_url'].'/'.$config['admin_folder']."/listing_cat.php?primary_val=menu.man.edit&id=$row[menu_item_id]&midx=$row[menu_idx]");
    redir($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=reorder3&midx=$row[menu_idx]&return_url=$url");
}

// important files
require "./../includes/admin_init.php";
admin_check(4);

$parent = get_param('parent');
$midx = get_param('midx');
$id = get_param('id');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($id)) {
    $id = post_param('primary_val');
}

// special primary val value from menu_man
$pv = get_param('primary_val');
$qadmin_cfg['cmd_remove_enable'] = false;
$qadmin_cfg['cmd_new_enable'] = false;
$dir_id = 0;

// edit from menu man -> must get ID
if ($pv == 'menu.man.edit') {
    $cat_structure[0] = '-';
    $iidx = get_param('id');
    $row = sql_qquery("SELECT * FROM ".$db_prefix."listing_cat WHERE menu_idx='$midx' AND menu_item_id='$iidx' LIMIT 1");
    $_GET['id'] = $id = $row['idx'];

    // dir info
    $dir_id = $row['dir_id'];
    get_dir_info($dir_id);
}
// new cat from menu man -> must get ID
elseif ($pv == 'menu.man.new') {
    // get dir id
    $foo = sql_qquery("SELECT menu_preset FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
    $dir_id = substr($foo['menu_preset'], 8);
    get_dir_info($dir_id);

    // get parent
    if (!empty($parent)) {
        $foo = sql_qquery("SELECT idx FROM ".$db_prefix."listing_cat WHERE menu_item_id='$parent' LIMIT 1");
        $parent = $foo['idx'];
    }
    $_GET['primary_val'] = $_GET['id'] = $primary_val = $id = '';
    $qadmin_cfg['cmd_new_enable'] = true;
}


// if id defined, get info from db
if ($id) {
    $row = sql_qquery("SELECT * FROM ".$db_prefix."listing_cat WHERE idx='$id' LIMIT 1");

    // get featured listing
    $foo = explode(',', $row['cat_featured']);
    $mm = array();
    $i = 0;
    if ($row['cat_featured']) {
        foreach ($foo as $k => $v) {
            $i++;
            $mem = sql_qquery("SELECT idx, item_title FROM ".$db_prefix."listing WHERE idx='$v' LIMIT 1");
            $mm[] = array('id' => $mem['idx'], 'name' => $mem['item_title']);
        }
    }
    $cat_featured_preset = $i ? json_encode($mm) : 'null';

    // get page
    if ($row['cat_page']) {
        $foo = sql_qquery("SELECT page_title FROM ".$db_prefix."page WHERE page_id='$row[cat_page]' LIMIT 1");
        $page_preset = json_encode(array(array('id' => $row['cat_page'], 'name' => $foo[0])));
    } else {
        $page_preset = 'null';
    }

    // menu idx
    $menu_idx = $row['menu_idx'];
} else {
    $cat_featured_preset = $page_preset = 'null';
    $menu_idx = get_param('midx');
    if (empty($menu_idx)) {
        $menu_idx = post_param('menu_idx');
    }
    if (empty($menu_idx)) {
        admin_die(sprintf($lang['msg']['echo'], 'Please use category editor to add a new category!'));
    }
}

//
$qadmin_def['menu_idx']['title'] = 'Menu IDX';
$qadmin_def['menu_idx']['field'] = 'menu_idx';
$qadmin_def['menu_idx']['type'] = 'hidden';
$qadmin_def['menu_idx']['value'] = $menu_idx;
$qadmin_def['menu_idx']['required'] = true;

//
$qadmin_def['dir_id']['title'] = 'Dir ID';
$qadmin_def['dir_id']['field'] = 'dir_id';
$qadmin_def['dir_id']['type'] = 'hidden';
$qadmin_def['dir_id']['value'] = $dir_id;
$qadmin_def['dir_id']['required'] = true;

// idx :: int :: 10
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['size'] = 10;
$qadmin_def['idx']['value'] = 'sql';

// parent_id :: int :: 10
$qadmin_cmd = post_param('qadmin_cmd');
if ($qadmin_cmd != 'update') {
    $qadmin_def['parent_id']['title'] = 'Parent';
    $qadmin_def['parent_id']['field'] = 'parent_id';
    $qadmin_def['parent_id']['type'] = ($id) ? 'static' : 'select';
    $qadmin_def['parent_id']['option'] = $dir_id ? $dir_info[$dir_id]['cat_structure'] : array();
    $qadmin_def['parent_id']['value'] = ($parent) ? $parent : 'sql';
}

// cat_name :: string :: 300
$qadmin_def['cat_name']['title'] = 'Category Name';
$qadmin_def['cat_name']['field'] = 'cat_name';
$qadmin_def['cat_name']['type'] = 'varchar';
$qadmin_def['cat_name']['size'] = 255;
$qadmin_def['cat_name']['value'] = 'sql';
$qadmin_def['cat_name']['required'] = true;

// permalink :: string :: 255
$qadmin_def['permalink']['title'] = 'Permalink';
$qadmin_def['permalink']['field'] = 'permalink';
$qadmin_def['permalink']['type'] = 'permalink';
$qadmin_def['permalink']['size'] = 255;
$qadmin_def['permalink']['value'] = 'sql';

// cat_details :: blob :: 196605
$qadmin_def['cat_details']['title'] = 'Description';
$qadmin_def['cat_details']['field'] = 'cat_details';
$qadmin_def['cat_details']['type'] = 'wysiwyg';
$qadmin_def['cat_details']['size'] = '500,200';
$qadmin_def['cat_details']['value'] = 'sql';

// cat_image :: string :: 63
$qadmin_def['cat_image']['title'] = 'Image';
$qadmin_def['cat_image']['field'] = 'cat_image';
$qadmin_def['cat_image']['type'] = 'thumb';
$qadmin_def['cat_image']['value'] = 'sql';

// cat_keywords :: blob :: 765
$qadmin_def['cat_keywords']['title'] = 'Keywords';
$qadmin_def['cat_keywords']['field'] = 'cat_keywords';
$qadmin_def['cat_keywords']['type'] = 'varchar';
$qadmin_def['cat_keywords']['size'] = 255;
$qadmin_def['cat_keywords']['value'] = 'sql';

// cat_num_link :: blob :: 765
$qadmin_def['cat_num_link']['title'] = 'Number of Listings';
$qadmin_def['cat_num_link']['field'] = 'cat_num_link';
$qadmin_def['cat_num_link']['type'] = 'echo';
$qadmin_def['cat_num_link']['value'] = 'sql';
$qadmin_def['cat_num_link']['help'] = 'Including its sub categories (if any)';

// cat_featured :: string :: 765
$qadmin_def['cat_featured']['title'] = 'Featured Listings';
$qadmin_def['cat_featured']['field'] = 'cat_featured';
$qadmin_def['cat_featured']['type'] = 'varchar';
$qadmin_def['cat_featured']['size'] = 255;
$qadmin_def['cat_featured']['value'] = 'sql';

// cat_page
$qadmin_def['cat_page']['title'] = 'Show this page instead';
$qadmin_def['cat_page']['field'] = 'cat_page';
$qadmin_def['cat_page']['type'] = 'varchar';
$qadmin_def['cat_page']['size'] = 255;
$qadmin_def['cat_page']['value'] = 'sql';
$qadmin_def['cat_page']['help'] = 'Instead of displaying category information, list of entries &amp; featured listings; you can display a page.';

if ($id) {
    // dir_logo :: 254 :: 0
    $qadmin_def['div3']['title'] = 'Remove';
    $qadmin_def['div3']['field'] = 'div3';
    $qadmin_def['div3']['type'] = 'div';

    // dir_pre_fee :: 246 :: 0
    $qadmin_def['is_removed']['title'] = 'Remove this category?';
    $qadmin_def['is_removed']['field'] = 'is_removed';
    $qadmin_def['is_removed']['type'] = 'checkbox';
    $qadmin_def['is_removed']['value'] = 'sql';
    $qadmin_def['is_removed']['option'] = '<span class="text-danger bg-danger">Yes, remove this category along with its sub categories &amp; its listings. This operation can not be undone.</span>';
    $qadmin_def['is_removed']['help'] = 'This operation may take a long time to finish, and may time out. If it happens, simply re-submit this form to continue process.';
}

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'listing_cat';					// table name
$qadmin_cfg['primary_key'] = 'idx';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['permalink_script'] = 'listing_search.php';				// script name for permalink
$qadmin_cfg['permalink_param'] = 'cmd=list';				// script name for permalink
$qadmin_cfg['permalink_source'] = 'cat_name';				// script name for permalink
$qadmin_cfg['permalink_folder'] = 'category';				// script name for permalink
$qadmin_cfg['post_process'] = 'post_func';
$qadmin_cfg['rebuild_cache'] = true;
$qadmin_cfg['back'] = 'menu_man.php?cmd=design&amp;midx='.$menu_idx;

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,parent_id,cat_name,cat_num_link';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Parent,Category Name,Number of Links';	// mask other key
$qadmin_cfg['search_result_mask'] = ",cat_structure,,";

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = false;
$qadmin_cfg['cmd_list_enable'] = false;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['footer'] =
"<script type=\"text/javascript\">
//<![CDATA[
var sss = '';
$('#".$db_prefix."listing_cat-cat_featured>input').tokenInput('admin_ajax.php?cmd=item', { queryParam:'query', preventDuplicates:true, prePopulate:$cat_featured_preset});
$('#".$db_prefix."listing_cat-cat_page>input').tokenInput('admin_ajax.php?cmd=related_page', { queryParam:'query', preventDuplicates:true, tokenLimit:1, prePopulate:$page_preset});
//]]>
</script>";


// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = '4';

// form title
$qadmin_title['new'] = 'Add New Category';
$qadmin_title['update'] = 'Update Category';
$qadmin_title['search'] = 'Search Category';
$qadmin_title['list'] = 'Category List';
qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
