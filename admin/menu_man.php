<?php
// used to refresh cache & order (weight) of a menu
// need a global vars of item_list & menu_cache
// $menu_id = menu id to be refreshed
function refresh_order($menu_id)
{
    global $item_list, $db_prefix, $menu_cache;

    // update db
    $i = 90;
    foreach ($item_list as $k => $v) {
        $i = $i + 10;
        sql_query("UPDATE ".$db_prefix."menu_item SET menu_order = $i WHERE idx='{$item_list[$k]['idx']}' AND menu_id='$menu_id' LIMIT 1");
    }

    sql_query("UPDATE ".$db_prefix."menu_set SET menu_cache='".addslashes($menu_cache)."' WHERE idx='$menu_id' LIMIT 1");
}


// create tree of menu
// $menu_id = menu id to be refreshed
// $id = id of menu_item as parent, enter 0 if it's a top menu
// $level = used to add padding for cache
//
// it returns global vars of: item_list (list of menu item for selected menu in array: menu_item_idx, title, parent, order, level) & menu_cache (string of menu in HTML ul & li)
function get_tree($menu_id, $id, $level = 0)
{
    global $item_list, $cursor, $db_prefix, $menu_cache, $config;
    $exists = false;

    // bsnav = bootstrap navbar, requires some special html tags, so the script a little different than usual UL, LI.
    if ($config['menu_class'] == 'bsnav') {
        $bsnav = true;
    } else {
        $bsnav = false;
    }
    $res = sql_query("SELECT * FROM ".$db_prefix."menu_item WHERE menu_id='$menu_id' AND menu_parent='$id' ORDER BY menu_order");
    while ($row = sql_fetch_array($res)) {
        // if any item, add <ul>
        if (!$exists) {
            if ($level == 0) {
                if ($bsnav) {
                    $menu_class = 'nav navbar-nav';
                } else {
                    $menu_class= $config['menu_class'];
                }
                $menu_cache .= str_repeat("\t", $level)."<ul id=\"qmenu_$config[menu_id_txt]\" class=\"$menu_class\">\n";
            } else {
                if ($bsnav) {
                    $menu_cache .= str_repeat("\t", $level)."<ul class=\"dropdown-menu\">\n";
                } else {
                    $menu_cache .= str_repeat("\t", $level)."<ul>\n";
                }
            }
            $exists = true;
        }

        $iid = $row['idx'];
        $cursor++;
        $item_list[$cursor]['idx'] = $row['idx'];
        $item_list[$cursor]['title'] = $row['menu_item'];
        $item_list[$cursor]['parent'] = $row['menu_parent'];
        $item_list[$cursor]['order'] = $row['menu_order'];
        $item_list[$cursor]['ref_idx'] = $row['ref_idx'];
        $item_list[$cursor]['level'] = $level;
        $row['menu_item'] = html_unentities(str_replace('__SITE__', $config['site_url'], $row['menu_item']));

        // for url
        if (is_numeric($row['menu_url'])) {
            if ($config['enable_adp']) {
                $pt = sql_qquery("SELECT permalink, page_title FROM ".$db_prefix."page WHERE page_id='$row[menu_url]' LIMIT 1");
                if ($pt['permalink']) {
                    $url = $config['site_url'].'/'.$pt['permalink'];
                } else {
                    $url = $config['site_url'].'/page.php?pid='.$row['menu_url'];
                }
            } else {
                $url = $config['site_url'].'/page.php?pid='.$row['menu_url'];
            }
            $item_list[$cursor]['url'] = $config['site_url'].'/page.php?pid='.$row['menu_url'];
        } else {
            if (!empty($row['menu_permalink']) && $config['enable_adp']) {
                $url = $row['menu_permalink'];
            } else {
                $url = $row['menu_url'];
            }
            $item_list[$cursor]['url'] = str_replace('__SITE__', $config['site_url'], $url);
        }
        $url = str_replace('__SITE__', $config['site_url'], $url);

        // contents, LI
        if (($url == '---') && $bsnav) {
            // separator
            $current = str_repeat("\t", $level + 1)."<li role=\"separator\" class=\"divider\">\n";
        } elseif (!empty($url)) {
            // text with link
            $current = str_repeat("\t", $level + 1)."<li><a href=\"$url\">$row[menu_item]</a>\n";
        } else {
            // text without link
            if ($bsnav) {
                $current = str_repeat("\t", $level + 1)."<li class=\"dropdown-header\">$row[menu_item]\n";
            } else {
                $current = str_repeat("\t", $level + 1)."<li>$row[menu_item]\n";
            }
        }

        // has child?
        $ccc = sql_qquery("SELECT * FROM ".$db_prefix."menu_item WHERE menu_id='$menu_id' AND menu_parent='$iid' LIMIT 1");
        if (!empty($ccc)) {
            // -- has child -> add special li
            // bootstrap only supports 2 level menu (0 - top0 & 1 - child)
            if ($bsnav && ($level < 1)) {
                $current = str_repeat("\t", $level + 1)."<li class=\"dropdown\"><a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\">$row[menu_item] <span class=\"caret\"></span></a>";
            }
            $item_list[$cursor]['hasChild'] = true;
        } else {
            $item_list[$cursor]['hasChild'] = false;
        }

        // add current text to cache
        $menu_cache .= $current;

        // get children
        $x = get_tree($menu_id, $iid, $level + 1);
        if (!$x) {
            $menu_cache = substr($menu_cache, 0, -1)."</li>\n";
        } else {
            $menu_cache .= str_repeat("\t", $level)."</li>\n";
        }
    }
    if ($exists) {
        $menu_cache .= str_repeat("\t", $level)."</ul>\n";
    }

    return $exists;
}

// part of qEngine
require './../includes/admin_init.php';

admin_check('manage_menu');
$cmd = get_param('cmd');
$midx = get_param('midx');
$iidx = get_param('iidx');
$ref_idx = get_param('ref_idx');
if (empty($cmd)) {
    $cmd = post_param('cmd');
}

// get tree
if (!empty($midx)) {
    $cfg = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
    if (!$cfg) {
        admin_die(sprintf($lang['msg']['echo'], 'Invalid Menu IDX!'));
    }
    if ($cfg['menu_preset'] != '--') {
        $config['menu_class'] = $cfg['menu_preset'];
    } else {
        $config['menu_class'] = $cfg['menu_class'];
    }
    $config['menu_id_txt'] = $cfg['menu_id'];

    $item_list = array();
    $cursor = 0;
    $menu_cache = '';
    get_tree($midx, 0);
}

// designer configuration
$designer_cfg = array();
$designer_cfg['preview'] = true;
$designer_cfg['show_url'] = true;							// display or hide linked url
$designer_cfg['new_url'] = 'menu_item.php?qadmin_cmd=new';	// &midx=[menu_id]&ref_idx=[ref_idx]
$designer_cfg['force_del'] = false;							// allow item deletion even if the item locked by another application?
$designer_cfg['post_del_url'] = '';							// executed after deleting an item, [url]?[param]&midx=[menu_id]&iidx=[item_id]&ref_idx=[ref_idx]
$designer_cfg['edit_url'] = 'menu_item.php?';				// $midx=[menu_id]&id=[item_id]&ref_idx=[ref_idx]
$designer_cfg['add_child_url'] = 'menu_item.php?qadmin_cmd=new';	// &midx=[menu_id]&parent=[item_id]&ref_idx=[ref_idx]

// shortcuts configuration
$designer_cfg['allow_add'] = true;
$designer_cfg['allow_add_child'] = true;
$designer_cfg['allow_del'] = true;
$designer_cfg['allow_edit'] = true;

// custom
// if your script needs a special features from menu editor (eg. disable preview, or override new_item_url) use this section
if (!empty($midx)) {
    if (substr($cfg['menu_id'], 0, 7) == 'dir_cf_') {
        $designer_cfg['allow_add'] = false;
        $designer_cfg['allow_add_child'] = false;
        $designer_cfg['allow_edit'] = false;
        $designer_cfg['preview'] = false;
        $designer_cfg['allow_del'] = false;
        $designer_cfg['show_url'] = false;
    } elseif (substr($cfg['menu_id'], 0, 8) == 'dir_cat_') {
        $designer_cfg['preview'] = false;
        $designer_cfg['show_url'] = false;
        $designer_cfg['new_url'] = 'listing_cat.php?qadmin_cmd=new&primary_val=menu.man.new';
        $designer_cfg['allow_del'] = false;
        $designer_cfg['post_del_url'] = $config['site_url'].'/'.$config['admin_folder'].'/listing_cat.php?qadmin_cmd=remove_item&primary_val=menu.man.remove';	// as we don't know the real cat_id, we send 'spc' as cat_id <thus listing_cat.php will detect the 'spc'>
        $designer_cfg['edit_url'] = 'listing_cat.php?primary_val=menu.man.edit';
        $designer_cfg['add_child_url'] = 'listing_cat.php?qadmin_cmd=new&primary_val=menu.man.new';
    } elseif ($midx == 5) {
        $designer_cfg['allow_add'] = false;
        $designer_cfg['allow_add_child'] = false;
        $designer_cfg['allow_del'] = false;
        $designer_cfg['allow_edit'] = false;
    }
}

// buttons
$dn = "<a href=\"javascript:void(0)\" onclick=\"$('#main_body').load('menu_man.php?cmd=dn&amp;midx=%1\$s&amp;iidx=%2\$s')\"><span class=\"glyphicon glyphicon-menu-down\"></span></a>";
$up = "<a href=\"javascript:void(0)\" onclick=\"$('#main_body').load('menu_man.php?cmd=up&amp;midx=%1\$s&amp;iidx=%2\$s')\"><span class=\"glyphicon glyphicon-menu-up\"></span></a>";

$dnd = "<span class=\"glyphicon glyphicon-menu-down\" style=\"color:#bbb\"></span>";
$upd = "<span class=\"glyphicon glyphicon-menu-up\" style=\"color:#bbb\"></span>";

$add_button = $designer_cfg['allow_add'] ? "<a href=\"%1\$s&amp;midx=%2\$s\"><span class=\"glyphicon glyphicon-plus tips\" title=\"Add Child\"></span> New Item</a>" : '';
$add_child_button = $designer_cfg['allow_add_child'] ? "<a href=\"%1\$s&amp;midx=%2\$s&amp;parent=%3\$s\"><span class=\"glyphicon glyphicon-plus-sign tips\" title=\"Add Child\"></span></a>" : '';
$edit_button = $designer_cfg['allow_edit'] ? "<a href=\"%1\$s&amp;id=%2\$s&amp;midx=%3\$s\"><span class=\"glyphicon glyphicon-list-alt tips\" title=\"Edit properties\"></span></a>" : '';
$del_button = $designer_cfg['allow_del'] ? "<a href=\"javascript:confirm_remove(%1\$s,%2\$s)\"><span class=\"glyphicon glyphicon-remove text-danger tips\" title=\"Remove menu\"></span></a>" : '';

$module_engine = $config['enable_module_engine'];

// the core...
switch ($cmd) {
    case 'guide':
        $tpl_mode = 'guide';
        $tpl = load_tpl('adm', 'menu_man.tpl');

        $foo = sql_qquery("SELECT menu_id, menu_preset, menu_class FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
        $txt['mymenu'] = $foo['menu_id'];
        $txt['myclass'] = empty($foo['menu_preset']) ? $foo['menu_class'] : $foo['menu_preset'];
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;


    case 'reorder_all':
        $res = sql_query("SELECT idx FROM ".$db_prefix."menu_set");
        while ($row = sql_fetch_array($res)) {
            $midx = $row['idx'];
            $cfg = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
            if ($cfg['menu_preset'] != '--') {
                $config['menu_class'] = $cfg['menu_preset'];
            } else {
                $config['menu_class'] = $cfg['menu_class'];
            }
            $config['menu_id_txt'] = $cfg['menu_id'];

            $item_list = array();
            $cursor = 0;
            $menu_cache = '';
            get_tree($midx, 0);
            refresh_order($midx);
        }
        admin_die('admin_ok');
    break;


    case 'reorder':
    case 'reorder2':
    case 'reorder3':
        // each "reorder" has its own redirection after completed
        refresh_order($midx);
        $return_url = safe_receive(get_param('return_url'));
        if ($cmd == 'reorder') {			// simply back to menu designer
            admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design&midx='.$midx);
        } elseif ($cmd == 'reorder2') {		// back to menu selector
            admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/menu_set.php?id='.$midx);
        } else {							// custom url
            admin_die('admin_ok', $return_url);
        }
    break;


    case 'del_menu':
        $foo = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
        if ($foo['menu_locked']) {
            admin_die($lang['msg']['menuman_locked_err']);
        }
        sql_query("DELETE FROM ".$db_prefix."menu_item WHERE menu_id='$midx'");
        sql_query("DELETE FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
        redir();
    break;


    case 'del_item':
        // make sure menu_man allowed to remove (un)locked item
        if (!$designer_cfg['force_del']) {
            $foo = sql_qquery("SELECT * FROM ".$db_prefix."menu_set WHERE idx='$midx' LIMIT 1");
            if ($foo['menu_locked']) {
                admin_die($lang['msg']['menuman_locked_err']);
            }
        }

        $item_list = array(); $cursor = 0; $menu_cache = '';
        get_tree($midx, $iidx);
        foreach ($item_list as $k => $v) {
            $i = $v['idx'];
            sql_query("DELETE FROM ".$db_prefix."menu_item WHERE idx='$i' LIMIT 1");
        }
        sql_query("DELETE FROM ".$db_prefix."menu_item WHERE idx='$iidx' LIMIT 1");

        // reset tree & cache
        $menu_cache = ''; $item_list = array(); $cursor = 0;
        get_tree($midx, 0);
        refresh_order($midx);
        if ($designer_cfg['post_del_url']) {
            redir($designer_cfg['post_del_url'].'&midx='.$midx.'&iidx='.$iidx.'&ref_idx='.$ref_idx);
        } else {
            redir();
        }
    break;


    case 'up':
        $current = sql_qquery("SELECT * FROM ".$db_prefix."menu_item WHERE idx='$iidx' LIMIT 1");
        $prev = sql_qquery("SELECT * FROM ".$db_prefix."menu_item WHERE menu_order < $current[menu_order] AND menu_parent='$current[menu_parent]' AND menu_id='$midx' ORDER BY menu_order DESC, idx DESC LIMIT 1");

        if (!empty($prev)) {
            sql_query("UPDATE ".$db_prefix."menu_item SET menu_order=$prev[menu_order] WHERE idx='$current[idx]' LIMIT 1");
            sql_query("UPDATE ".$db_prefix."menu_item SET menu_order=$current[menu_order] WHERE idx='$prev[idx]' LIMIT 1");
        }

        // reset tree & cache
        $menu_cache = ''; $item_list = array(); $cursor = 0;
        get_tree($midx, 0);
        refresh_order($midx);
        redir($config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design_refresh&midx='.$midx);
    break;


    case 'dn':
        $current = sql_qquery("SELECT * FROM ".$db_prefix."menu_item WHERE idx='$iidx' LIMIT 1");

        // same parent?
        foreach ($item_list as $k => $v) {
            if ($v['idx'] == $iidx) {
                $c = $k;
                break;
            }
        }
        $n = $c + 1;
        $norder = $item_list[$c]['order'] + 5;

        // top most?
        if ($c >= count($item_list)) {
            redir();
        }

        // if same parent (or same depth)
        if ($item_list[$n]['level'] == $item_list[$c]['level']) {
            sql_query("UPDATE ".$db_prefix."menu_item SET menu_order='$norder' WHERE idx='$iidx' LIMIT 1");
            sql_query("UPDATE ".$db_prefix."menu_item SET menu_order='{$item_list[$c]['order']}' WHERE idx='{$item_list[$n]['idx']}' LIMIT 1");
        }
        // otherwise
        else {
            // scan next items for same depth
            $i = $c;
            $cl = $item_list[$c]['level'];
            $ok = false;
            while (!$ok) {
                $i++;
                if (!empty($item_list[$i]['idx'])) {
                    if ($item_list[$i]['level'] == $cl) {
                        $ok = true;
                    }
                } else {
                    $ok = true;
                    $i = 0;
                }
            }

            // update!
            if ($i) {
                $nidx = $item_list[$i]['idx'];
                $norder = $item_list[$i]['order'];
                $corder = $current['menu_order'];
                sql_query("UPDATE ".$db_prefix."menu_item SET menu_order='$norder' WHERE idx='$iidx' LIMIT 1");
                sql_query("UPDATE ".$db_prefix."menu_item SET menu_order='$corder' WHERE idx='$nidx' LIMIT 1");
            }
        }

        // update list & reassign order
        $menu_cache = ''; $item_list = array(); $cursor = 0;
        get_tree($midx, 0);
        refresh_order($midx);
        redir($config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design_refresh&midx='.$midx);
    break;


    case 'design':
    case 'design_refresh':
        $tpl_mode = 'design';
        $tpl = load_tpl('adm', 'menu_man.tpl');
        if (empty($midx)) {
            admin_die(sprintf($lang['msg']['echo'], 'Invalid Menu IDX!'));
        }

        // create menu
        $txt['block_itemlist'] = '';

        $max = count($item_list);

        foreach ($item_list as $k => $v) {
            $v['pad'] = $v['level'] * 19;
            $v['midx'] = $midx;
            $v['up'] = $upd;
            $v['dn'] = $dnd;
            $v['edit_button'] = sprintf($edit_button, $designer_cfg['edit_url'], $v['idx'], $midx);
            $v['add_child_button'] = sprintf($add_child_button, $designer_cfg['add_child_url'], $midx, $v['idx']);
            $v['del_button'] = sprintf($del_button, $v['idx'], $v['ref_idx']);

            // show up button
            if ($k > 1) {
                $any = false;
                for ($i = $k - 1; $i > 0; $i--) {
                    if (($item_list[$i]['parent'] == $v['parent']) && ($item_list[$i]['level'] == $v['level'])) {
                        $any = true;
                    }
                }
                if (!$any) {
                    $v['up'] = $upd;
                } else {
                    $v['up'] = sprintf($up, $midx, $v['idx']);
                }
            }

            // show dn button
            if ($k < $max) {
                $any = false;
                for ($i = $k + 1; $i <= $max; $i++) {
                    if (($item_list[$i]['parent'] == $v['parent']) && ($item_list[$i]['level'] == $v['level'])) {
                        $any = true;
                    }
                }
                if (!$any) {
                    $v['dn'] = $dnd;
                } else {
                    $v['dn'] = sprintf($dn, $midx, $v['idx']);
                }
            }

            $v['show_url'] = $designer_cfg['show_url'] ? "<span class=\"glyphicon glyphicon-link icon-xs\"></span> <a href=\"$v[url]\" target=\"menu_man\" class=\"small bg-warning\">$v[url]</a>" : '';
            $v['edit_url'] = $designer_cfg['edit_url'];
            $v['add_child_url'] = $designer_cfg['add_child_url'];
            $txt['block_itemlist'] .= quick_tpl($tpl_block['itemlist'], $v);
        }

        $txt['add_button'] = sprintf($add_button, $designer_cfg['new_url'], $midx);
        $txt['midx'] = $midx;
        $txt['menu_id'] = $midx;
        $txt['menu_cache'] = $menu_cache;
        $txt['menu_id_txt'] = $config['menu_id_txt'];
        $txt['menu_class'] = $config['menu_class'];
        $txt['new_url'] = $designer_cfg['new_url'];
        if (($config['menu_class'] == 'sf-menu') || ($config['menu_class'] == 'sf-menu vertical')) {
            $txt['preview_bg'] = '#000';
        } else {
            $txt['preview_bg'] = '#fff';
        }

        // create preview?
        $txt['preview'] = '';
        if ($designer_cfg['preview']) {
            $tpl_mode = 'preview';
            $tpl_prev = load_tpl('adm', 'menu_man.tpl');
            $txt['preview'] = quick_tpl($tpl_prev, $txt);
        }

        // dump
        $txt['main_body'] = quick_tpl($tpl, $txt);
        if ($cmd == 'design_refresh') {
            echo $txt['main_body'];
        } else {
            flush_tpl('adm');
        }
    break;


    case 'custom.dir':
        $did = get_param('did');
        $foo = sql_qquery("SELECT dir_cat_menu_id FROM ".$db_prefix."listing_dir WHERE idx='$did' LIMIT 1");
        redir($config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design&midx='.$foo['dir_cat_menu_id']);
    break;


    default:
        $tpl_mode = 'list';
        $tpl = load_tpl('adm', 'menu_man.tpl');

        // load menu
        $txt['block_list'] = '';
        $res = sql_query("SELECT * FROM ".$db_prefix."menu_set ORDER BY menu_title");
        while ($row = sql_fetch_array($res)) {
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
