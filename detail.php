<?php
// part of qEngine
require_once './includes/user_init.php';

$item_id = get_param('item_id');
if ($isPermalink) {
    $item_id = $original_idx;
}

$item = sql_qquery("SELECT *, idx AS item_id FROM ".$db_prefix."listing WHERE idx='$item_id' LIMIT 1");

// is available?
if (!$item) {
    msg_die($lang['msg']['item_not_found']);
}

// listing owner can see owned listing no matter what
if ($item['owner_id'] != $current_user_id) {
    // is pending?
    if ($item['item_status'] != 'P' && !$current_admin_level) {
        msg_die($lang['msg']['edit_item_not_found']);
    }

    // is member only?
    if (($item['item_visibility'] == 'M') && !$isLogin) {
        fullpage_die($lang['l_page_member_only']);
    }
}
$owner_id = $item['owner_id'];

// template vars
$dir_id = $item['dir_id'];
get_dir_info($dir_id);
$dir_inf = $dir_info[$dir_id]['dir_inf'];

// no details page?
if ($dir_inf['dir_no_detail'] && $item['item_url']) {
    redir($item['item_url']);
}

// dir configurations
$require_summary = $require_url = $require_logo = $enable_comment = $allow_url_mask = $edit_btn = false;
if ($dir_inf['dir_summary']) {
    $require_summary = true;
}
if ($dir_inf['dir_url']) {
    $require_url = true;
}
if ($dir_inf['dir_logo']) {
    $require_logo = true;
}
if ($dir_inf['dir_comment']) {
    $enable_comment = true;
}
if ($dir_inf['dir_url_mask']) {
    $allow_url_mask = true;
}
if ($owner_id == $current_user_id) {
    $edit_btn = true;
}
$custom_field = true;
$add_fave = false;

// contents are cached, but since we still need some dynamic contents (eg. Edit Link [by owner], Valid Period [for owner]), thus the cache stores only variables, not the whole output.
// .. therefore, not some template vars must be recreated even in cached content (see the 'else' condition below).
$content = qcache_get('detail_'.$item_id);
if (!$content) {
    // init tpl
    $tpl = load_tpl('user', 'detail_'.$dir_inf['dir_short'].'.tpl', false);
    if (!$tpl) {
        $tpl = load_tpl('detail.tpl');
    }

    // process $item info
    $item = process_listing_info($item, 'detail');

    // image
    if (!$require_logo) {
        $item['image_big'] = $item['image_thumb'] = '';
    }

    // url masking?
    if ($allow_url_mask && !empty($item['item_url_mask'])) {
        $item['item_url_string'] = $item['item_url_mask'];
    } else {
        $item['item_url_string'] = $item['item_url'];
    }

    // bread crumb
    $item['block_cat_bread_crumb'] = '';
    foreach ($item['bread_cat'] as $k => $v) {
        $item['block_cat_bread_crumb'] .= quick_tpl($tpl_block['cat_bread_crumb'], $v);
    }

    // listed in other cats
    $foo = array();
    for ($i = 1; $i <= $dir_inf['dir_multi_cat']; $i++) {
        if (!empty($item['category_'.$i])) {
            $foo[] = '<li><a href="'.$dir_info[$dir_id]['cat_url'][$item['category_'.$i]].'">'.$dir_info[$dir_id]['cat_structure'][$item['category_'.$i]].'</a></li>';
        }
    }
    $item['listed_cat'] = '<ul>'.implode("\n", $foo).'</ul>';

    // also by
    $item['also_by'] = '';
    $foo = array();
    if (!empty($item['owner_id'])) {
        $res = sql_query("SELECT idx, item_title, item_permalink FROM ".$db_prefix."listing WHERE owner_id='$item[owner_id]' AND idx != '$item[idx]' AND item_status='P' AND item_visibility='A' ORDER BY RAND() LIMIT 3");
        while ($also = sql_fetch_array($res)) {
            if ($config['enable_adp'] && $also['item_permalink']) {
                $url = $also['item_permalink'];
            } else {
                $url = "detail.php?item_id=$also[idx]";
            }
            $foo[] = "<li><a href=\"$config[site_url]/$url\">$also[item_title]</a>";
        }
    }

    if (empty($foo)) {
        $also_by = false;
    } else {
        $also_by = true;
        $item['also_by'] = '<ul>'.implode("\n", $foo).'</ul>';
    }

    // comments
    if ($enable_comment && $enable_facebook_comment) {
        $enable_comment = false;
        $enable_page_facebook_comment = true;
    } else {
        $enable_page_facebook_comment = false;
    }

    // custom fields
    $item['cf_list'] = '';
    $cf_arr = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");
    $cf = get_custom_field($item['dir_id'], $cf_arr, $item['item_class']);
    if ($cf) {
        foreach ($cf as $k => $v) {
            // CF pre-process goes here
            // Place your custom CF pre-processor here, see below explanation
            // See also: /module/ke_core/window.php, /listing_search.php & /detail.php

            // cf standard output, for custom output see below
            $v['class'] = 'cf_tr_cell';
            if ($v['cf_type'] == 'div') {
                $item['cf_list'] .= quick_tpl($tpl_section['cf_list_div'], $v)."\n";
            } else {
                $item['cf_list'] .= quick_tpl($tpl_section['cf_list'], $v)."\n";
            }

            /* =============================================================================================
               Custom Design & Pre-Processor For Custom Fields
               ---------------------------------------------------------------------------------------------
               You can create a custom design for any CF, first use:
                    print_r ($v);

               You will see that cf structure consists of: cf_idx, cf_title, cf_value, cf_type & cf_raw

               Let say you have: short_text cf, with idx = cf_99, to create a custom design:

               The easiest:
               { after: // CF pre-process goes here, place }
                    if ($v['cf_idx'] == 'cf_99') $v['cf_value'] = do_something_with_cf ($v['cf_raw']);

               Advanced example:
               { after: // CF pre-process goes here, place }
                    if ($v['cf_idx'] == 'cf_99')
                        $item['my_cf_design'] = do_something_with_cf ($v['cf_raw']);
                    else
                        $item['block_cf_list'] .= quick_tpl ($tpl_block['cf_list'], $v);

               { in detail.tpl }
                    <p>Calling Name: {$my_cf_design}</p>
               =============================================================================================
            */
        }
    } else {
        $custom_field = false;
    }

    // fave?
    $current_f = array();
    if (!empty($current_user_info['user_fave'])) {
        $current_f = explode(',', $current_user_info['user_fave']);
    }
    if (in_array($item_id, $current_f)) {
        $add_fave = false;
    } else {
        $add_fave = true;
    }

    // others
    $item['item_id'] = $item_id;
    $item['owner_id'] = empty($item['owner_id']) ? $lang['l_guest'] : $item['owner_id'];
    $item['item_class'] = $listing_class_def[$item['item_class']];
    $item['visible_icon'] = $listing_visible_icon[$item['item_visibility']];
    $item['visible_help'] = $listing_visible_def[$item['item_visibility']];
    $lang['l_also_by'] = sprintf($lang['l_also_by'], $item['owner_id']);

    // recount rating
    $rat = sql_qquery("SELECT COUNT(*) AS total, AVG(comment_rate) AS avg FROM ".$db_prefix."qcomment WHERE mod_id='listing' AND item_id='$item_id' AND comment_approve='1' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."listing SET item_rating='$rat[avg]', item_votes='$rat[total]' WHERE idx='$item_id' LIMIT 1");

    // output
    $tpl = load_tpl('user', 'detail_'.$dir_inf['dir_short'].'.tpl', false, true);
    if (!$tpl) {
        $tpl = load_tpl('user', 'detail.tpl', false, true);
    }
    $item['current_url'] = $config['site_url'].'/'.$item['url'];

    // save tpl to cache
    $item['tpl'] = $tpl;
    qcache_update('detail_'.$item_id, serialize($item));
    $tpl = load_tpl('var', $tpl);
} else {
    $item = unserialize($content);

    // some template vars can't be cached
    // - owner?
    if ($owner_id == $current_user_id) {
        $edit_btn = true;
    } else {
        $edit_btn = false;
    }

    // - also by?
    if ($item['also_by']) {
        $also_by = true;
    } else {
        $also_by = false;
    }
    $lang['l_also_by'] = sprintf($lang['l_also_by'], $item['owner_id']);

    // - fave?
    $current_f = array();
    if (!empty($current_user_info['user_fave'])) {
        $current_f = explode(',', $current_user_info['user_fave']);
    }
    if (in_array($item_id, $current_f)) {
        $add_fave = false;
    } else {
        $add_fave = true;
    }

    $tpl = load_tpl('var', $item['tpl']);
}

$txt['main_body'] = quick_tpl($tpl, $item);

// update hits
sql_query("UPDATE ".$db_prefix."listing SET stat_hits=stat_hits+1 WHERE idx='$item_id' LIMIT 1");

// draft page only available to admin only
if (($item['item_status'] == 'E') && ($current_admin_level || ($current_user_id == $owner_id))) {
    $txt['main_body'] = $lang['l_page_draft'].$txt['main_body'];
}
if ($current_admin_level) {
    $txt['main_body'] = sprintf($lang['edit_listing_in_acp'], $item_id).$txt['main_body'];
}

generate_html_header($config['site_name'].' '.$config['cat_separator'].' '.$item['item_title'], $item['item_details'], $item['item_keyword']);
flush_tpl();
