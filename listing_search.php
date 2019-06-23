<?php
// search item by cf from table cf_value
// $query: string to search (for varchar), other cf uses get_param ($cf_key)
// return: list of item_id with matched cf
function filter_by_cf($query = '', $dir_id)
{
    global $db_prefix, $dir_info;
    $sql = $sql_query = array();

    $k = 0;
    get_dir_info($dir_id);
    foreach ($dir_info[$dir_id]['cf_define'] as $row) {
        $key = 'cf_'.$row['idx'];
        $val = get_param($key);
        $k++;

        switch ($row['cf_type']) {
            case 'varchar':
            case 'textarea':
            case 'country':
                if (!empty($val) && $row['is_searchable']) {
                    $sql[$k] = "(t2.$key='$val')";
                }
            break;

            case 'time':
                // try to get 'time' value from time_form (marked by keyID_hou form field)
                if (!empty(get_param($key.'_hou'))) {
                    $val = time_param($key);
                }
                if (verify_time($val)) {
                    $sql[$k] = "(t2.$key='$val')";
                }
            break;

            case 'date':
                if (verify_date($val)) {
                    $sql[$k] = "(t2.$key='$val')";
                }
            break;

            // for rating we need to convert, eg: 2 = 2.00 ~ 2.90
            case 'rating':
                $foo = rating_sql('t2.'.$key, $val);
                if ($foo) {
                    $sql[$k] = $foo;
                }
            break;

            case 'select':
                $val = verify_selected($val, $dir_info[$dir_id]['cf_define'][$key]['cf_option']);
                if ($val) {
                    $sql[$k] = "(t2.$key='$val')";
                }
            break;

            case 'multi':
                if (!empty(get_param($key))) {
                    $selected = array(get_param($key));
                } else {
                    $selected = checkbox_param($key, 'get', true);
                }

                if (!empty($selected)) {
                    $opts = $dir_info[$dir_id]['cf_define'][$key]['cf_option'];
                    $selected = verify_selected($selected, $opts);
                    if ($selected) {
                        $daa = array();
                        foreach ($selected as $k => $v) {
                            $daa[] = "(t2.$key LIKE '%\r\n$v\r\n%')";
                        }
                        $sql[$k] = '('.implode(' AND ', $daa).')';
                    }
                }
            break;
        }
    }

    // $sql = sql for all CF types except string & text
    // $sql_query = sql only for string & text CF
    if (!empty($sql) || !empty($sql_query)) {
        return array('sql' => implode(' AND ', $sql), 'sql_query' => implode(' OR ', $sql_query));
    } else {
        return false;
    }
}

require_once './includes/user_init.php';

// get parameters
if ($isPermalink) {
    $_GET['cat_id'] = $original_idx;
    $_GET['cmd'] = 'list';
}
$query = get_param('query');
$dir_id = get_param('dir_id');
$cat_id = get_param('cat_id', 0);
$rating = get_param('rating');
$owner_id = get_param('owner_id');
$sort = get_param('sort');
$cmd = get_param('cmd', 'list');
$mode = get_param('mode');
$p = get_param('p', 1);
$ajax = get_param('ajax');
$cfonly = get_param('cfonly');

// filter non UTF-8 alnum
$query = preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($query));

// force listview || gridview && listmode || searchmode
$query_url = html_unentities(clean_get_query(array('p', 'AXSRF_token', 'mod_id', 'ajax', 'cmd'), false));
if ($cmd != 'search') {
    $cmd = 'list';
}
if (!empty($query)) {
    $cmd = 'search';
}
if (empty($query) && empty($cat_id)) {
    $cmd = 'search';
}

// init when mode is list
if ($cmd == 'list') {
    $tpl_mode = 'list';
    $cat_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_cat WHERE idx='$cat_id' LIMIT 1");
    if (empty($cat_inf)) {
        msg_die($lang['msg']['cat_error']);
    }

    $dir_id = $cat_inf['dir_id'];
    get_dir_info($dir_id);
    if ($cat_inf['cat_page']) {
        $_GET = array('pid' => $cat_inf['cat_page']);
        $isPermalink = false;
        require './page.php';
        die;
    }

    // default sorting per dir
    $ds = $dir_info[$dir_id]['dir_inf']['dir_default_sort'][0];
    $do = $dir_info[$dir_id]['dir_inf']['dir_default_sort'][1];

    // default view
    $dv = $dir_info[$dir_id]['dir_inf']['dir_default_view'];
} else {
    $ds = 'x';
    $do = 'd';
    $dv = '';
}

// determine view mode
if (empty($mode)) {
    $mode = $dv;
}
if ($mode != 'list') {
    $mode = 'grid';
}

// load tpl
$featured_listing = false;
$tpl = load_tpl('listing_search_'.$mode.'.tpl');

// 0.0 dir id must be defined
if (empty($dir_id)) {
    $dir_id = $dir_info['config']['default'];
}

// 1.0 search by cats
$sql = array();
$list_only = true;

// 1.1 by category
if ($cat_id) {
    if (empty($cat_inf)) {
        $cat_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_cat WHERE idx='$cat_id' LIMIT 1");
    }
    $sql[] = create_cat_where($cat_id, 6);
} else {
    $sql[] = "(dir_id='$dir_id')";
}

// 1.2 filter by visibility
$foo = array("(item_visibility = 'A')");
if ($isLogin) {
    $foo[] = "(item_visibility = 'M')";
}
$sql[] = '('.implode(' OR ', $foo).')';
$sql[] = "(item_status = 'P')";

// 1.3 by rating
if ($rating) {
    $list_only = false;
    $foo = rating_sql('item_rating', $rating);
    if ($foo) {
        $sql[] = $foo;
    }
}

// 1.4 by owner_id
if ($owner_id) {
    $sql[] = "owner_id='$owner_id'";
}


// 2.0 search: get custom field filtering result
$cf_sql = filter_by_cf($query, $dir_id);
if (!empty($cf_sql['sql'])) {
    $list_only = false;
    $sql[] = '('.$cf_sql['sql'].')';
}

// 2.1 search by query or not
$item = array();
if ($query) {
    $list_only = false;
    $w = array();
    if (!$cfonly) {
        $w[] = create_where('smart_search', $query);
    }
    if (!empty($cf_sql['sql_query'])) {
        $w[] = $cf_sql['sql_query'];
    }
    $sql[] = '('.implode(' OR ', $w).')';
}

// 3.0 determine sorting method
if (!$sort) {
    $sort = $ds.$do;
}
$s = substr($sort, 0, 1);
$o = substr($sort, 1, 1);

$sortby = $sortorder = array();
$sortby['x'] = 'item_sort_point';
$sortby['t'] = 'item_title';
$sortby['d'] = 'item_date';
$sortby['r'] = 'item_rating';
$sortorder['a'] = 'asc';
$sortorder['d'] = 'desc';

if (empty($s) or !array_key_exists($s, $sortby)) {
    $s = $ds;
}
if (empty($o) or !array_key_exists($o, $sortorder)) {
    $o = $do;
}

// 4.0 search: combine search by cat_id (or title etc) + cf search + other filters (price, etc)
$txt['block_search_item'] = '';
$ssql = implode(' AND ', $sql);

// 5.0 finally, display the result!
$i = 0;
unset($_GET['ajax']);
if ($isPermalink) {
    unset($_GET['mod_id'], $_GET['cmd'], $_GET['cat_id']);
}

$foo = sql_multipage($db_prefix.'listing AS t1 LEFT JOIN '.$db_prefix.'listing_cf_value AS t2 ON (t1.idx=t2.item_id)', '*, t1.idx AS item_id', "$ssql", "CASE WHEN item_class='S' THEN 1 ELSE 2 END, $sortby[$s] $sortorder[$o]", $p, '', $dir_info[$dir_id]['dir_inf']['dir_per_page']);
foreach ($foo as $row) {
    $i++;
    get_dir_info($row['dir_id']);
    process_listing_info($row);

    // custom fields
    $cf = get_custom_field($row['dir_id'], $row, $row['item_class'], false);
    $row['cf_list'] = '';
    if ($cf) {
        foreach ($cf as $k => $v) {
            // CF pre-process goes here
            // Place your custom CF pre-processor here, see /detail.php for explanation
            // See also: /module/ke_core/window.php, /listing_search.php & /detail.php

            // cf standard output, for custom output see below
            $row['cf_list'] .= quick_tpl($tpl_section['cf_list'], $v);
        }
    }

    $row['item_rating_pct'] = $row['item_rating'] / 5;
    $row['list_class'] = $list_class[$row['item_class']];
    $row['listing_label'] = $lang[$row['item_class'].'_label'];
    $row['visible_icon'] = $listing_visible_icon[$row['item_visibility']];
    $row['visible_help'] = $listing_visible_def[$row['item_visibility']];

    if ($row['owner_id'] && ($current_user_id == $row['owner_id'])) {
        $row['edit_btn'] = quick_tpl($tpl_section['edit'], $row);
    } else {
        $row['edit_btn'] = '';
    }
    $txt['block_search_item'] .= quick_tpl($tpl_block['search_item'], $row);
}

if (!$i) {
    $no_search_result = true;
} else {
    $no_search_result = false;
}

// output
$txt['cmd'] = $cmd;
$txt['cat_id'] = $cat_id;
$txt['owner_id'] = $owner_id;
$txt['query'] = stripslashes($query);
$txt['query_url'] = $query_url;
$txt['search_sort'] = create_select_form('sort', $search_sort, $sort);
$txt['mode_select'] = create_select_form('mode', $list_mode, $mode);
$txt['dir_id'] = $dir_id;

// display category list on list mode (aka not search mode)
if ($cmd == 'list') {
    $tpl_mode = 'list';
    $txt['cat_name'] = $cat_name = $cat_inf['cat_name'];
    $txt['cat_details'] = $cat_details = $cat_inf['cat_details'];
    $txt['cat_keywords'] = $cat_keywords = $cat_inf['cat_keywords'];
    if (!empty($cat_inf['cat_image'])) {
        $txt['cat_image'] = "<img src=\"$config[site_url]/public/image/$cat_inf[cat_image]\" alt=\"image\" />";
    } else {
        $txt['cat_image'] = $cat_inf['cat_name'];
    }

    // cat list
    $txt['block_cat_list'] = '';
    $foo = create_cat_list($dir_id, $cat_id);
    foreach ($foo as $val) {
        $txt['block_cat_list'] .= quick_tpl($tpl_block['cat_list'], $val);
    }

    // bread crumb
    $bc = array('bc_link' => $dir_info[$dir_id]['dir_inf']['url'], 'bc_title' => $dir_info[$dir_id]['dir_inf']['dir_title']);
    $txt['block_cat_bread_crumb'] = quick_tpl($tpl_block['cat_bread_crumb'], $bc);
    $foo = explode(',', $dir_info[$dir_id]['cat_structure_id'][$cat_id]);
    foreach ($foo as $v) {
        $bc = array();
        $bc['bc_link'] = $dir_info[$dir_id]['cat_url'][$v];
        $bc['bc_title'] = $dir_info[$dir_id]['cat_name_def'][$v];
        if ($v != $cat_id) {
            $txt['block_cat_bread_crumb'] .= quick_tpl($tpl_block['cat_bread_crumb'], $bc);
        }
    }

    // show feat listing on first page && when list only (no filtering, search, etc)
    if ((!empty($cat_inf['cat_featured'])) && ($list_only) && ($p == 1)) {
        $featured_listing = true;
    }
    generate_html_header("$config[site_name] $config[cat_separator] $cat_name", $cat_details, $cat_keywords);
} else {
    $tpl_mode = 'search';
    generate_html_header("$config[site_name] $config[cat_separator] ".ucwords(strtolower($query)));
}

// output
$txt['main_body'] = quick_tpl(load_tpl('listing_search_'.$mode.'.tpl'), $txt);

// add acp shortcut
if (($current_admin_level) && empty($query) && !empty($cat_inf)) {
    $txt['main_body'] = sprintf($lang['edit_listing_cat_in_acp'], $cat_inf['menu_idx']).$txt['main_body'];
}

if ($ajax) {
    flush_tpl('ajax');
} else {
    flush_tpl('body_list.tpl');
}
