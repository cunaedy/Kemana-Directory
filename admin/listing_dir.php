<?php
// called after add/edit/del anything
function post_func($cmd, $id, $savenew, $old, $new)
{
    global $db_prefix, $config, $dbh;

    // remove a dir
    if ($new['is_removed'] || $old['is_removed']) {
        html_header();
        echo '<h1>Removing Directory...</h1>';
        echo '<p>This may take a while, if the script is time out, please resubmit the form to continue removal.';
        echo '<p>Removing listings ';

        // remove items
        $res = sql_query("SELECT idx FROM ".$db_prefix."listing WHERE dir_id='$id'");
        while ($row = sql_fetch_array($res)) {
            remove_item($row['idx'], false, false);
            echo $row['idx'].'. ';
        }
        echo '</p>';

        // remove menu_set & menu_items
        // - cat menu
        $menu = sql_qquery("SELECT idx FROM ".$db_prefix."menu_set WHERE menu_preset='dir_cat.$id' LIMIT 1");
        sql_query("DELETE FROM ".$db_prefix."menu_set WHERE idx='$menu[idx]' LIMIT 1");
        sql_query("DELETE FROM ".$db_prefix."menu_item WHERE menu_id='$menu[idx]'");

        // - cf menu
        $menu = sql_qquery("SELECT idx FROM ".$db_prefix."menu_set WHERE menu_preset='dir_cf.$id' LIMIT 1");
        sql_query("DELETE FROM ".$db_prefix."menu_set WHERE idx='$menu[idx]' LIMIT 1");
        sql_query("DELETE FROM ".$db_prefix."menu_item WHERE menu_id='$menu[idx]'");

        // - dir menu
        sql_query("DELETE FROM ".$db_prefix."menu_item WHERE idx='$new[menu_item_id]' LIMIT 1");

        // remove permalink
        sql_query("DELETE FROM ".$db_prefix."permalink WHERE url='$new[dir_permalink]' LIMIT 1");

        // remove cf define
        // - alter db
        $res = sql_query("SELECT * FROM ".$db_prefix."listing_cf_define WHERE dir_id='$id'");
        while ($row = sql_fetch_array($res)) {
            $foo = sql_qquery("SHOW COLUMNS FROM `".$db_prefix."listing_cf_value` LIKE 'cf_".$row['idx']."'");
            if (!empty($foo)) {
                sql_query("ALTER TABLE `".$db_prefix."listing_cf_value` DROP `cf_".$row['idx']."`");
            }
        }

        // - remove field
        sql_query("DELETE FROM ".$db_prefix."listing_cf_define WHERE dir_id='$id'");

        // remove cats
        $res = sql_query("SELECT permalink FROM ".$db_prefix."listing_cat WHERE dir_id='$id'");
        while ($row = sql_fetch_array($res)) {
            sql_query("DELETE FROM ".$db_prefix."permalink WHERE url='$row[permalink]' LIMIT 1");
        }
        sql_query("DELETE FROM ".$db_prefix."listing_cat WHERE dir_id='$id'");

        // finally remove the dir
        sql_query("DELETE FROM ".$db_prefix."listing_dir WHERE idx='$id' LIMIT 1");

        // is no more default dir? make _any_ dir to be default one!
        $foo = sql_qquery("SELECT idx FROM ".$db_prefix."listing_dir WHERE dir_default='1'");
        if (!$foo) {
            sql_query("UPDATE ".$db_prefix."listing_dir SET dir_default='1' LIMIT 1");
        }

        echo '<h1>Finished!</h1>';
        html_footer();
        $redir = safe_send($config['site_url'].'/'.$config['admin_folder'].'/listing_dir.php');
        redir($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=reorder3&midx=5&return_url=$redir");
    }

    // always clear cache first
    qcache_clear('dir.%', false);
    qcache_clear('dir_%', false);

    // action if new dir
    if ($cmd == 'new') {
        // create unique id, eg: General Directory -> 'gen'
        if (empty($new['dir_short'])) {
            $dis = create_unique_id($new['dir_title'], 3, 0);
            $i = 0;
            $ok = false;
            while (!$ok) {
                $i++;
                if (!sql_qquery("SELECT dir_short FROM ".$db_prefix."listing_dir WHERE dir_short='$dis' LIMIT 1")) {
                    $ok = true;
                } else {
                    $dis = create_unique_id($new['dir_title'], 3, $i, true);
                }
            }

            sql_query("UPDATE ".$db_prefix."listing_dir SET dir_short='$dis' WHERE idx='$id' LIMIT 1");
        }

        // create menu_man entries
        // - cat menu
        sql_query("INSERT INTO ".$db_prefix."menu_set SET menu_id='dir_cat_$dis', menu_title='Categories for $new[dir_title]', menu_preset='dir_cat.$id', menu_notes='Container for category menu of $new[dir_title]. Do NOT remove!', menu_locked='1'");
        $catidx = mysqli_insert_id($dbh);

        // - cf menu
        sql_query("INSERT INTO ".$db_prefix."menu_set SET menu_id='dir_cf_$dis', menu_title='Custom field for $new[dir_title]', menu_preset='dir_cf.$id', menu_notes='Container for custom field menu of $new[dir_title]. Do NOT remove!', menu_locked='1'");
        $cfidx = mysqli_insert_id($dbh);

        sql_query("UPDATE ".$db_prefix."listing_dir SET dir_cat_menu_id='$catidx', dir_cf_menu_id='$cfidx' WHERE idx='$id' LIMIT 1");
    }

    // if new or edit a dir
    if (($cmd == 'new') || ($cmd == 'update')) {
        $row = sql_qquery("SELECT * FROM ".$db_prefix."listing_dir WHERE idx='$id' LIMIT 1");
        $row['dir_title'] = addslashes($row['dir_title']);
        $url = '__SITE__/index.php?dir_id='.$row['idx'];
        $permalink = '__SITE__/'.$row['dir_permalink'];
        $redir = safe_send($config['site_url'].'/'.$config['admin_folder']."/listing_dir.php?id=$id");

        if ($cmd == 'new') {
            // if new dir -> add dir to menu
            sql_query("INSERT INTO ".$db_prefix."menu_item SET menu_id='5', menu_item='$row[dir_title]', menu_url='$url', menu_permalink='$permalink', menu_order='999999', ref_idx='$id'");
            $mmid = mysqli_insert_id($dbh);
            sql_query("UPDATE ".$db_prefix."listing_dir SET menu_item_id='$mmid' WHERE idx='$id' LIMIT 1");
        } else {
            // update menu entry
            sql_query("UPDATE ".$db_prefix."menu_item SET menu_item='$row[dir_title]', menu_url='$url', menu_permalink='$permalink' WHERE idx='$row[menu_item_id]' LIMIT 1");
        }
    }

    // set as default dir
    if ($new['dir_default']) {
        sql_query("UPDATE ".$db_prefix."listing_dir SET dir_default='0' WHERE idx != '$id'");
    } else {
        $foo = sql_qquery("SELECT idx FROM ".$db_prefix."listing_dir WHERE dir_default='1'");
        if (!$foo) {
            sql_query("UPDATE ".$db_prefix."listing_dir SET dir_default='1' LIMIT 1");
        }
    }

    // no details page requires dir_url
    if ($new['dir_no_detail']) {
        sql_query("UPDATE ".$db_prefix."listing_dir SET dir_url='1' WHERE idx = '$id' LIMIT 1");
    }

    // redir
    if ($savenew) {
        $redir = safe_send($config['site_url'].'/'.$config['admin_folder'].'/listing_dir.php?qadmin_cmd=new');
    } else {
        $redir = safe_send($config['site_url'].'/'.$config['admin_folder'].'/listing_dir.php?id='.$id);
    }
    redir($config['site_url'].'/'.$config['admin_folder']."/menu_man.php?cmd=reorder3&midx=5&return_url=$redir");
    die;
}


// part of qEngine
require './../includes/admin_init.php';

$id = get_param('id');
if (empty($id)) {
    $id = get_param('primary_val');
}
if (empty($id)) {
    $id = post_param('primary_val');
}
if (is_numeric($id)) {
    get_dir_info($id);
    $row = sql_qquery("SELECT * FROM ".$db_prefix."listing_dir WHERE idx='$id' LIMIT 1");

    // get featured listing
    $foo = explode(',', $row['dir_featured']);
    $mm = array();
    $i = 0;
    if ($row['dir_featured']) {
        foreach ($foo as $k => $v) {
            $i++;
            $mem = sql_qquery("SELECT idx, item_title FROM ".$db_prefix."listing WHERE idx='$v' LIMIT 1");
            $mm[] = array('id' => $mem['idx'], 'name' => $mem['item_title']);
        }
    }
    $dir_featured_preset = $i ? json_encode($mm) : 'null';
} else {
    $dir_featured_preset = 'null';
}

// data definitions
// idx :: 3 :: 0
$qadmin_def['idx']['title'] = 'ID';
$qadmin_def['idx']['field'] = 'idx';
$qadmin_def['idx']['type'] = 'echo';
$qadmin_def['idx']['value'] = 'sql';

// dir_short :: 254 :: 0
$qadmin_def['dir_short']['title'] = 'Directory Unique Identifier';
$qadmin_def['dir_short']['field'] = 'dir_short';
$qadmin_def['dir_short']['type'] = 'echo';
$qadmin_def['dir_short']['value'] = 'sql';
$qadmin_def['dir_short']['unique'] = true;
$qadmin_def['dir_short']['help'] = 'This is a unique identifier for this directory. This identifier will be used in multiple features.';

// dir_title :: 253 :: 0
$qadmin_def['dir_title']['title'] = 'Directory Name';
$qadmin_def['dir_title']['field'] = 'dir_title';
$qadmin_def['dir_title']['type'] = 'varchar';
$qadmin_def['dir_title']['size'] = 255;
$qadmin_def['dir_title']['value'] = 'sql';
$qadmin_def['dir_title']['required'] = true;
$qadmin_def['dir_title']['suffix'] = is_numeric($id) ? '<a href="menu_man.php?cmd=design&amp;midx=5" classs="btn btn-default"><span class="glyphicon glyphicon-sort"></span> Reorder</a>' : '';

// permalink :: string :: 255
$qadmin_def['dir_permalink']['title'] = 'Permalink';
$qadmin_def['dir_permalink']['field'] = 'dir_permalink';
$qadmin_def['dir_permalink']['type'] = 'permalink';
$qadmin_def['dir_permalink']['size'] = 255;
$qadmin_def['dir_permalink']['value'] = 'sql';

// dir_image :: 254 :: 0
$qadmin_def['dir_image']['title'] = 'Upload an Image';
$qadmin_def['dir_image']['field'] = 'dir_image';
$qadmin_def['dir_image']['type'] = 'thumb';
$qadmin_def['dir_image']['value'] = 'sql';
$qadmin_def['dir_image']['help'] = 'Minimum recommended size for default skin: 300x300px';

// dir_body :: 252 :: 0
$qadmin_def['dir_body']['title'] = 'Details';
$qadmin_def['dir_body']['field'] = 'dir_body';
$qadmin_def['dir_body']['type'] = 'wysiwyg';
$qadmin_def['dir_body']['value'] = 'sql';
$qadmin_def['dir_body']['size'] = '500,200';

// dir_featured :: string :: 765
$qadmin_def['dir_featured']['title'] = 'Featured Listings';
$qadmin_def['dir_featured']['field'] = 'dir_featured';
$qadmin_def['dir_featured']['type'] = 'varchar';
$qadmin_def['dir_featured']['size'] = 255;
$qadmin_def['dir_featured']['value'] = 'sql';

// dir_multi_cat :: 1 :: 0
$qadmin_def['dir_multi_cat']['title'] = 'Number of Multiple Categories';
$qadmin_def['dir_multi_cat']['field'] = 'dir_multi_cat';
$qadmin_def['dir_multi_cat']['type'] = 'select';
$qadmin_def['dir_multi_cat']['option'] = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6);
$qadmin_def['dir_multi_cat']['value'] = 'sql';
$qadmin_def['dir_multi_cat']['required'] = true;

// dir_default :: 1 :: 0
$qadmin_def['dir_default']['title'] = 'Default Directory?';
$qadmin_def['dir_default']['field'] = 'dir_default';
$qadmin_def['dir_default']['type'] = 'radio';
$qadmin_def['dir_default']['option'] = $yesno;
$qadmin_def['dir_default']['value'] = 'sql';
$qadmin_def['dir_default']['help'] = 'For multiple directory, a default directory will be displayed as home page.';

// dir_url :: 254 :: 0
$qadmin_def['dir_url']['title'] = 'Requires URL?';
$qadmin_def['dir_url']['field'] = 'dir_url';
$qadmin_def['dir_url']['type'] = 'radio';
$qadmin_def['dir_url']['option'] = $yesno;
$qadmin_def['dir_url']['value'] = $id ? 'sql' : 1;
$qadmin_def['dir_url']['help'] = 'Submitted URL must be unique.';

// dir_url_mask :: 254 :: 0
$qadmin_def['dir_url_mask']['title'] = 'Allow URL Masking?';
$qadmin_def['dir_url_mask']['field'] = 'dir_url_mask';
$qadmin_def['dir_url_mask']['type'] = 'radio';
$qadmin_def['dir_url_mask']['option'] = $yesno;
$qadmin_def['dir_url_mask']['value'] = $id ? 'sql' : 0;
$qadmin_def['dir_url_mask']['help'] = 'Conceal real URL by a string or another URL.';

// dir_backlink :: 254 :: 0
$qadmin_def['dir_backlink']['title'] = 'Requires Backlink?';
$qadmin_def['dir_backlink']['field'] = 'dir_backlink';
$qadmin_def['dir_backlink']['type'] = 'radio';
$qadmin_def['dir_backlink']['option'] = $yesno;
$qadmin_def['dir_backlink']['value'] = 'sql';
$qadmin_def['dir_backlink']['help'] = 'Backlink is a HTML code to be placed on listing (resource) web site. When required, Kemana will randomly check for backlinks.';

// dir_summary :: 254 :: 0
$qadmin_def['dir_summary']['title'] = 'Requires Listing Summary?';
$qadmin_def['dir_summary']['field'] = 'dir_summary';
$qadmin_def['dir_summary']['type'] = 'radio';
$qadmin_def['dir_summary']['option'] = $yesno;
$qadmin_def['dir_summary']['value'] = 'sql';
$qadmin_def['dir_summary']['help'] = 'When summary required, list page will display summary instead of title only.';

// dir_no_detail :: 254 :: 0
$qadmin_def['dir_no_detail']['title'] = 'No Details Page';
$qadmin_def['dir_no_detail']['field'] = 'dir_no_detail';
$qadmin_def['dir_no_detail']['type'] = 'radio';
$qadmin_def['dir_no_detail']['option'] = $yesno;
$qadmin_def['dir_no_detail']['value'] = 'sql';
$qadmin_def['dir_no_detail']['help'] = 'When enabled, clicking on an item will redirect visitors to target URL instead of details page.';

// dir_logo :: 254 :: 0
$qadmin_def['div1']['title'] = 'Looks';
$qadmin_def['div1']['field'] = 'div1';
$qadmin_def['div1']['type'] = 'div';

// dir_url :: 254 :: 0
$qadmin_def['dir_comment']['title'] = 'Allow Member Comments?';
$qadmin_def['dir_comment']['field'] = 'dir_comment';
$qadmin_def['dir_comment']['type'] = 'radio';
$qadmin_def['dir_comment']['option'] = $yesno;
$qadmin_def['dir_comment']['value'] = 'sql';
$qadmin_def['dir_comment']['help'] = 'Listing rating needs member comments.';

// dir_logo :: 254 :: 0
$qadmin_def['dir_logo']['title'] = 'Allow Logo Upload?';
$qadmin_def['dir_logo']['field'] = 'dir_logo';
$qadmin_def['dir_logo']['type'] = 'radio';
$qadmin_def['dir_logo']['option'] = $yesno;
$qadmin_def['dir_logo']['value'] = 'sql';

// dir_per_page :: 3 :: 0
$qadmin_def['dir_per_page']['title'] = 'Listing per Page';
$qadmin_def['dir_per_page']['field'] = 'dir_per_page';
$qadmin_def['dir_per_page']['type'] = 'varchar';
$qadmin_def['dir_per_page']['size'] = 3;
$qadmin_def['dir_per_page']['value'] = $id ? 'sql' : '24';
$qadmin_def['dir_per_page']['help'] = 'Number of listings per page on listing list &amp; listing search. Recommended 24.';


// dir_logo_size :: 3 :: 0
$qadmin_def['dir_logo_size']['title'] = 'Logo Resize';
$qadmin_def['dir_logo_size']['field'] = 'dir_logo_size';
$qadmin_def['dir_logo_size']['type'] = 'varchar';
$qadmin_def['dir_logo_size']['size'] = 3;
$qadmin_def['dir_logo_size']['value'] = 'sql';
$qadmin_def['dir_logo_size']['suffix'] = 'px';
$qadmin_def['dir_logo_size']['help'] = 'Enter a dimension to automatically resize an image. Leave blank or 0 to disable resizing.';

// dir_multi_cat :: 1 :: 0
$qadmin_def['dir_default_sort']['title'] = 'Default Sorting Method';
$qadmin_def['dir_default_sort']['field'] = 'dir_default_sort';
$qadmin_def['dir_default_sort']['type'] = 'select';
$qadmin_def['dir_default_sort']['option'] = $search_sort;
$qadmin_def['dir_default_sort']['value'] = $id ? 'sql' : 'xd';
$qadmin_def['dir_default_sort']['required'] = true;
$qadmin_def['dir_default_sort']['help'] = 'Default sorting uses calculated sort_point to determine position. See Directory Settings for more information.';

// dir_multi_cat :: 1 :: 0
$qadmin_def['dir_default_view']['title'] = 'Default Listing View';
$qadmin_def['dir_default_view']['field'] = 'dir_default_view';
$qadmin_def['dir_default_view']['type'] = 'select';
$qadmin_def['dir_default_view']['option'] = $list_mode;
$qadmin_def['dir_default_view']['value'] = $id ? 'sql' : 'grid';
$qadmin_def['dir_default_view']['required'] = true;

// dir_logo :: 254 :: 0
if ($id) {
    $qadmin_def['notes1']['title'] = 'Custom Design';
    $qadmin_def['notes1']['field'] = 'notes1';
    $qadmin_def['notes1']['type'] = 'echo';
    $qadmin_def['notes1']['value'] = 'By default, Kemana will use <kbd>/skins/[your_skin]/welcome.tpl</kbd> to display this directory page. To create your own design, simply create a <kbd>/skins/[your_skin]/welcome_'.$dir_info[$id]['dir_inf']['dir_short'].'.tpl</kbd> file.';
}

// dir_logo :: 254 :: 0
$qadmin_def['div2']['title'] = 'Pricing';
$qadmin_def['div2']['field'] = 'div2';
$qadmin_def['div2']['type'] = 'div';

// dir_reg_fee :: 246 :: 0
$qadmin_def['dir_reg_fee']['title'] = 'Regular Listing Fee';
$qadmin_def['dir_reg_fee']['field'] = 'dir_reg_fee';
$qadmin_def['dir_reg_fee']['type'] = 'echo';
$qadmin_def['dir_reg_fee']['value'] = 'Always Free';

// dir_pre_fee :: 246 :: 0
$qadmin_def['dir_pre_allow']['title'] = 'Enable Premium &amp; Sponsored?';
$qadmin_def['dir_pre_allow']['field'] = 'dir_pre_allow';
$qadmin_def['dir_pre_allow']['type'] = 'radio';
$qadmin_def['dir_pre_allow']['option'] = $yesno;
$qadmin_def['dir_pre_allow']['value'] = 'sql';
$qadmin_def['dir_pre_allow']['help'] = 'Premium listings to be displayed with different background/design. Sponsored listings displayed on top of listing list.';

// dir_pre_fee :: 246 :: 0
$qadmin_def['dir_pre_fee']['title'] = 'Premium Listing Fee';
$qadmin_def['dir_pre_fee']['field'] = 'dir_pre_fee';
$qadmin_def['dir_pre_fee']['type'] = 'varchar';
$qadmin_def['dir_pre_fee']['size'] = 10;
$qadmin_def['dir_pre_fee']['value'] = 'sql';
$qadmin_def['dir_pre_fee']['prefix'] = $config['num_currency'];
$qadmin_def['dir_pre_fee']['suffix'] = '/month';

// dir_spo_fee :: 246 :: 0
$qadmin_def['dir_spo_fee']['title'] = 'Sponsored Listing Fee';
$qadmin_def['dir_spo_fee']['field'] = 'dir_spo_fee';
$qadmin_def['dir_spo_fee']['type'] = 'varchar';
$qadmin_def['dir_spo_fee']['size'] = 10;
$qadmin_def['dir_spo_fee']['value'] = 'sql';
$qadmin_def['dir_spo_fee']['prefix'] = $config['num_currency'];
$qadmin_def['dir_spo_fee']['suffix'] = '/month';

if ($id) {
    // dir_logo :: 254 :: 0
    $qadmin_def['div3']['title'] = 'Remove';
    $qadmin_def['div3']['field'] = 'div3';
    $qadmin_def['div3']['type'] = 'div';

    // dir_pre_fee :: 246 :: 0
    $qadmin_def['is_removed']['title'] = 'Remove this directory?';
    $qadmin_def['is_removed']['field'] = 'is_removed';
    $qadmin_def['is_removed']['type'] = 'checkbox';
    $qadmin_def['is_removed']['value'] = 'sql';
    $qadmin_def['is_removed']['option'] = '<span class="text-danger bg-danger">Yes, remove this directory along with its categories &amp; listings. This operation can not be undone.</span>';
    $qadmin_def['is_removed']['help'] = 'This operation may take a long time to finish, and may time out. If it happens, simply re-submit this form to continue process.';
}

// general configuration ( * = optional )
$qadmin_cfg['table'] = $db_prefix.'listing_dir';			// table name
$qadmin_cfg['primary_key'] = 'idx';							// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['ezf_mode'] = false;							// TRUE to use EZF mode (see ./_qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['ezd_mode'] = false;							// TRUE to use ezDesign mode (see ./qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['template'] = 'default';						// template to use
$qadmin_cfg['permalink_script'] = 'index.php';				// script name for permalink
$qadmin_cfg['permalink_source'] = 'dir_title';				// script name for permalink
$qadmin_cfg['post_process'] = 'post_func';
$qadmin_cfg['footer'] =
"<script type=\"text/javascript\">
//<![CDATA[
var sss = '';
$('#".$db_prefix."listing_dir-dir_featured>input').tokenInput('admin_ajax.php?cmd=item', { queryParam:'query', preventDuplicates:true, prePopulate:$dir_featured_preset});
//]]>
</script>";

// permalink (hooray, finally qE has a proper SEF URL) -- see also permalink qadmin_def above
$qadmin_cfg['permalink_folder'] = '';						// virtual folder for permalink, eg: www.c97.net/virtual_folder/mypage.html, empty = no folder (eg: www.c97.net/mypage.html), end without / (optional)
$qadmin_cfg['permalink_script'] = 'index.php';				// script name for permalink to open, eg: page.php
$qadmin_cfg['permalink_source'] = 'dir_title';				// the source field for permalink if user doesn't enter any permalink value, eg: page_title
$qadmin_cfg['permalink_param'] = 'dir';						// you can use this as extra field, it will be sent to your script AS-IS, eg: cat (optional)
$qadmin_cfg['log_title'] = 'dir_title';	// qadmin field to be used as log title (empty = disable log, no matter other cfg's)

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';					// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';				// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';			// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] = 'idx,dir_short,dir_title,dir_default';		// list other key to search
$qadmin_cfg['search_key_mask'] = 'ID,Identifier,Title,As Default?';	// mask other key
$qadmin_cfg['search_result_mask'] = ",,,yesno";

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';						// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = false;

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
$qadmin_cfg['admin_level'] = '4';

// form title
$qadmin_title['new'] = 'Add New Directory';
$qadmin_title['update'] = 'Update Directory';
$qadmin_title['search'] = 'Search Directory';
$qadmin_title['list'] = 'Directory List';

qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);
