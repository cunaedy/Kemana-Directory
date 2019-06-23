<?php
function load_page_tpl($page_id, $page_template)
{
    $custom_tpl = load_tpl('user', 'page_'.$page_id.'.tpl', false);
    if ($custom_tpl) {
        $tpl = $custom_tpl;
    } else {
        if ($page_template == '_blank') {
            $tpl = '{$page_body}';
        } else {
            $tpl = load_tpl('user', $page_template, false);
            if (!$tpl) {
                $tpl = load_tpl('page_default.tpl');
            }
        }
    }
    return $tpl;
}


// part of qEngine
require_once "./includes/user_init.php";

// page_id taken from table page
$page_id = get_param('pid');
$cid = get_param('cid');
$author = get_param('author');
$dn = get_param('dn');
$sort = get_param('sort');
$popup = get_param('popup');
$p = get_param('p', 1);

if ($isPermalink && !$permalink_param) {
    $page_id = $original_idx;
} elseif ($isPermalink && $permalink_param) {
    $cid = $original_idx;
}

// mode
$cmd = 'default';
if (!empty($page_id)) {
    $cmd = 'pid';
}
if ($cid) {
    $cmd = 'cid';
}
if ($author) {
    $cmd = 'author';
}
if ($dn) {
    $cmd = 'download';
}



switch ($cmd) {
    case 'download':
        permission_check('page_download');
        $row = sql_qquery("SELECT * FROM ".$db_prefix."page WHERE page_id = '$dn' LIMIT 1");
        if (empty($row)) {
            $txt['main_body'] = '<h1>Page not found!</h1>';
            flush_tpl();
        }
        if (empty($row['page_attachment'])) {
            msg_die($lang['msg']['page_attachment_error']);
        }
        $fn = './public/file/'.$row['page_attachment'];
        if (!file_exists($fn)) {
            msg_die($lang['msg']['page_attachment_error']);
        }
        sql_query("UPDATE ".$db_prefix."page SET page_attachment_stat=page_attachment_stat+1 WHERE page_id='$dn' LIMIT 1");
        $row['fn'] = $config['site_url'].'/public/file/'.$row['page_attachment'];
        echo quick_tpl($tpl_section['download_attachment'], $row);
        die;
    break;


    case 'pid':
        // get permissions
        $row = sql_qquery("SELECT * FROM ".$db_prefix."page WHERE page_id = '$page_id' LIMIT 1");

        // get rules
        $rules = sql_qquery("SELECT * FROM ".$db_prefix."page_group WHERE group_id='$row[group_id]' LIMIT 1");

        // is available?
        if (empty($row)) {
            fullpage_die($lang['l_page_not_found']);
        }

        // is draft?
        if ($row['page_status'] == 'D' && !$current_admin_level) {
            fullpage_die($lang['l_page_not_found']);
        }

        // is member only?
        if (($row['page_status'] == 'M') && !$isLogin) {
            fullpage_die($lang['l_page_member_only']);
        }

        // is admin only?
        if (($row['page_status'] == 'A') && !$current_admin_level) {
            fullpage_die($lang['l_page_admin_only']);
        }

        // get content
        $content = qcache_get('page_'.$page_id.".$p");
        if (!$content) {
            $row = sql_qquery("SELECT * FROM ".$db_prefix."page WHERE page_id = '$page_id' LIMIT 1");

            // get cat info
            $cat_info = sql_qquery("SELECT * FROM ".$db_prefix."page_cat WHERE idx='$row[cat_id]' LIMIT 1");

            // tpl
            $sub_page = true;
            $author = $rules['page_author'];
            $main_image = $rules['page_image'];
            $main_image_th = $rules['page_thumb'];
            $page_gallery = $rules['page_gallery'];
            $page_cat = $rules['page_cat'];
            $page_date = $rules['page_date'];
            $comment_rule = $rules['page_comment'];
            $attachment = $rules['page_attachment'];
            $allow_comment = $row['page_allow_comment'];

            // page template
            if (empty($row['page_template']) && empty($rules['page_template'])) {
                $page_template = 'page_default.tpl';
            } elseif (empty($row['page_template']) && !empty($rules['page_template'])) {
                $page_template = $rules['page_template'];
            } else {
                $page_template = $row['page_template'];
            }
            if ($page_template == '_blank') {
                $blank_tpl = true;
            } else {
                $blank_tpl = false;
            }

            if (!$rules['page_comment'] && $row['page_allow_comment']) {
                $allow_comment = false;
            }
            if ($allow_comment && $enable_facebook_comment) {
                $allow_comment = false;
                $enable_page_facebook_comment = true;
            } else {
                $enable_page_facebook_comment = false;
            }

            // load tpl
            $tpl = load_page_tpl($page_id, $page_template);

            // pagebreak
            $foo = explode('<!-- pagebreak -->', $row['page_body']);
            $row['page_body'] = $foo[$p-1];
            $row['pagination'] = generate_pagination("page.php?pid=$page_id", count($foo), $p, 1);
            if ($row['page_mode'] == 'raw') {
                $row['page_body'] = quick_tpl($row['page_body'], array());
            }

            // main_image
            $row['page_image_th'] = '';
            if (!$main_image) {
                $main_image = $main_image_th = false;
            }
            if ($main_image && !$row['page_image']) {
                $main_image = $main_image_th = false;
            }
            if ($main_image && $main_image_th && $row['page_image']) {
                $row['page_image_th'] = $row['page_image'];
                $main_image = false;
                $main_image_thumb = true;
            }

            // image gallery
            if ($page_gallery && !$blank_tpl) {
                $i = 0;
                $ok = $any = false;
                $row['block_gallery'] = '';
                while (!$ok) {
                    $i++;
                    $foo = array();
                    $fn = 'page_img_'.$page_id.'_'.$i;
                    $folder = 'public/image';
                    if (file_exists($folder.'/'.$fn.'.jpg')) {
                        $any = true;
                        $foo['image'] = $fn.'.jpg';
                        $row['block_gallery'] .= quick_tpl($tpl_block['gallery'], $foo);
                    } else {
                        $ok = true;
                    }
                }
                if (!$any) {
                    $page_gallery = false;
                }
            }

            // attachment
            if ($attachment && !empty($row['page_attachment'])) {
                $fn = './public/file/'.$row['page_attachment'];
                $attachment = false;
                if (file_exists($fn)) {
                    $fs = num_format(filesize($fn) / 1024); // in KB
                    $row['page_attachment_size'] = $fs;
                    $row['page_attachment_stat'] = num_format($row['page_attachment_stat']);
                    $attachment = true;
                }
            } else {
                $attachment = false;
            }

            // sub-pages list
            $sub_page = false;
            if ($row['page_related'] && !$blank_tpl) {
                $row['block_list'] = '';
                $l = substr_count($row['page_related'], ',') + 1;
                $res2 = sql_query("SELECT page_id, permalink, page_title, page_body, page_image FROM ".$db_prefix."page WHERE page_id IN ($row[page_related]) LIMIT $l");
                while ($row2 = sql_fetch_array($res2)) {
                    if ($config['enable_adp'] && $row2['permalink']) {
                        $row2['page_url'] = $row2['permalink'];
                    } else {
                        $row2['page_url'] = "page.php?pid=$row2[page_id]";
                    }
                    $row2['page_image_small'] = empty($row2['page_image']) ? '' : "<img src=\"$config[site_url]/public/thumb/$row2[page_image]\" alt=\"$row2[page_title]\" width=\"50\" />";
                    $row2['page_image_thumb'] = empty($row2['page_image']) ? '' : "<img src=\"$config[site_url]/public/thumb/$row2[page_image]\" alt=\"$row2[page_title]\" />";
                    $row2['page_image'] = empty($row2['page_image']) ? '' : "<img src=\"$config[site_url]/public/image/$row2[page_image]\" alt=\"$row2[page_title]\" />";
                    $row['block_list'] .= quick_tpl($tpl_block['list'], $row2);
                }
                if (!empty($row['block_list'])) {
                    $sub_page = true;
                }
            }

            // comment
            if ($allow_comment) {
                $row['comment_mid'] = $comment_rule;
            }

            $row['page_thumb_small'] = empty($row['page_thumb']) ? '' : "<img src=\"$config[site_url]/public/thumb/$row[page_thumb]\" alt=\"$row[page_title]\" />";
            $row['page_thumb'] = empty($row['page_thumb']) ? '' : "<img src=\"$config[site_url]/public/image/$row[page_thumb]\" alt=\"$row[page_title]\" />";
            if ($config['enable_adp'] && $cat_info['permalink']) {
                $row['page_cat'] = "<a href=\"$config[site_url]/$cat_info[permalink]\">$cat_info[cat_name]</a>";
            } else {
                $row['page_cat'] = "<a href=\"$config[site_url]/page.php?cid=$cat_info[idx]\">$cat_info[cat_name]</a>";
            }
            $row['page_date'] = convert_date($row['page_date']);
            $row['page_time'] = date('h:ia', $row['page_unix']);
            $row['update_date'] = convert_date(date('Y-m-d', $row['last_update']));
            $row['update_time'] = date('h:ia', $row['last_update']);

            // load tpl
            $tpl = load_page_tpl($page_id, $page_template);

            $row['current_url'] = $config['site_url'].'/'.($config['enable_adp'] ? $row['permalink'] : 'page.php?pid='.$page_id);
            $txt['main_body'] = quick_tpl($tpl, $row);	// reload page.tpl
            qcache_update('page_'.$page_id.".$p", serialize(array($row['page_cat'], $txt['main_body'])));
        } else {
            $foo = unserialize($content);
            $row['page_cat'] = $foo[0];
            $txt['main_body'] = $foo[1];
        }

        if ($current_admin_level) {
            $txt['main_body'] = sprintf($lang['edit_in_acp'], $page_id).$txt['main_body'];
        }

        // update hit
        sql_query("UPDATE ".$db_prefix."page SET page_hit=page_hit+1 WHERE page_id = '$page_id' LIMIT 1");

        generate_html_header($config['site_name'].' '.$config['cat_separator'].' '.strip_tags($row['page_cat']).' '.$config['cat_separator'].' '.$row['page_title'], '', $row['page_keyword']);

        // draft page only available to admin only
        if ($row['page_status'] == 'D' && $current_admin_level) {
            $txt['main_body'] = $lang['l_page_draft'].$txt['main_body'];
            flush_tpl();
        }

        if ($popup) {
            flush_tpl('popup');
        } else {
            if ($rules['group_template']) {
                flush_tpl($rules['group_template']);
            } else {
                flush_tpl();
            }
        }
    break;


    case 'cid':
        // get rules
        $info = sql_qquery("SELECT * FROM ".$db_prefix."page_cat WHERE idx='$cid' LIMIT 1");
        $rules = sql_qquery("SELECT * FROM ".$db_prefix."page_group WHERE group_id='$info[group_id]' LIMIT 1");

        // is listing allowed?
        if (!$rules['cat_list']) {
            msg_die($lang['msg']['no_cat_list']);
        }
        if ($rules['all_cat_list']) {
            $all_cat_list = true;
        } else {
            $all_cat_list = false;
        }

        // tpl
        $tpl_mode = 'cat';
        $tpl = load_tpl('page_list.tpl');

        // cat list
        $i = 0;
        if ($all_cat_list) {
            $txt['block_cat_list'] = '';
            $res = sql_query("SELECT * FROM ".$db_prefix."page_cat WHERE group_id='$info[group_id]' AND idx != '$cid' ORDER BY cat_name");
            while ($row = sql_fetch_array($res)) {
                $i++;
                if ($config['enable_adp'] && $row['permalink']) {
                    $row['cat_url'] = $row['permalink'];
                } else {
                    $row['cat_url'] = "page.php?cid=$row[idx]";
                }
                $row['cat_image'] = empty($row['cat_image']) ? $config['site_url'].'/skins/_common/images/noimages.gif' : $config['site_url']."/public/image/$row[cat_image]";
                $txt['block_cat_list'] .= quick_tpl($tpl_block['cat_list'], $row);
            }
        }
        if (!$i) {
            ($all_cat_list = false);
        }

        // page list
        if (empty($sort)) {
            $sort = $rules['page_sort'];
        }
        if ($sort == 'd') {
            $s = 'page_id DESC';
        } else {
            $s = 'page_title';
            $sort = 't';
        }
        $s = "page_pinned DESC, $s";

        $txt['block_list'] = '';

        // page status sql
        $page_status_sql = create_page_status_sql();

        $foo = sql_multipage($db_prefix.'page', 'page_id, permalink, page_title, page_body, page_image, page_date, page_author, page_attachment, page_pinned, page_status', "($page_status_sql) AND cat_id='$cid' AND page_list='1'", $s, $p);
        foreach ($foo as $k => $v) {
            if ($config['enable_adp'] && $v['permalink']) {
                $v['page_url'] = $v['permalink'];
            } else {
                $v['page_url'] = "page.php?pid=$v[page_id]";
            }
            if (!empty($v['page_attachment'])) {
                $v['page_attachment'] = $lang['page_attachment'];
            } else {
                $v['page_attachment'] = '';
            }
            if ($v['page_pinned']) {
                $v['page_pinned'] = $lang['page_pinned'];
            } else {
                $v['page_pinned'] = '';
            }

            // locked page?
            if ($v['page_status'] == 'M') {
                if ($isLogin) {
                    $v['page_locked'] = $lang['page_unlocked'];
                } else {
                    $v['page_locked'] = $lang['page_locked'];
                }
            } else {
                $v['page_locked'] = '';
            }

            $v['page_image_thumb'] = empty($v['page_image']) ? $config['site_url'].'/skins/_common/images/noimage.gif' : $config['site_url']."/public/thumb/$v[page_image]";
            $v['page_image'] = empty($v['page_image']) ? $config['site_url'].'/skins/_common/images/noimage.gif' : $config['site_url']."/public/image/$v[page_image]";
            $v['page_date'] = convert_date($v['page_date']);
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $v);
        }

        // cat info
        $txt['cat_image'] = empty($info['cat_image']) ? 'skins/_common/images/noimages.gif' : "public/image/$info[cat_image]";

        $txt = array_merge($info, $txt);
        $txt['cid'] = $cid;
        $txt['page_author'] = '';
        $txt['cat_details'] = empty($info['cat_details']) ? '<p>'.$info['cat_name'].'</p>' : $info['cat_details'];
        $txt['sortby'] = create_select_form('sort', $page_sort, $sort);
        $txt['main_body'] = quick_tpl(load_tpl('page_list.tpl'), $txt);	// reload page.tpl
        if ($current_admin_level) {
            $txt['main_body'] = sprintf($lang['edit_pcat_in_acp'], $cid).$txt['main_body'];
        }
        generate_html_header($config['site_name'].' '.$config['cat_separator'].' '.$info['cat_name'], $row['page_body'], $row['page_keyword']);
        if ($rules['group_template']) {
            flush_tpl($rules['group_template']);
        } else {
            flush_tpl();
        }
    break;


    case 'author':
        // get rules
        $info = sql_qquery("SELECT * FROM ".$db_prefix."page_cat WHERE idx='$cid' LIMIT 1");
        $rules = sql_qquery("SELECT * FROM ".$db_prefix."page_group WHERE group_id='$info[group_id]' LIMIT 1");

        // tpl
        $tpl_mode = 'author';
        $tpl = load_tpl('page_list.tpl');

        // page list
        if ($sort == 'd') {
            $s = 'page_unix DESC';
        } else {
            $s = 'page_title';
            $sort = 't';
        }

        $txt['block_list'] = '';
        $page_status_sql = create_page_status_sql();
        $foo = sql_multipage($db_prefix.'page', 'page_id, permalink, page_attachment, page_pinned, page_title, page_body, page_image, page_date, page_author, page_status', "($page_status_sql) AND page_author='$author' AND page_list='1'", $s, $p);
        foreach ($foo as $k => $v) {
            if ($config['enable_adp'] && $v['permalink']) {
                $v['page_url'] = $v['permalink'];
            } else {
                $v['page_url'] = "page.php?pid=$v[page_id]";
            }
            if (!empty($v['page_attachment'])) {
                $v['page_attachment'] = $lang['page_attachment'];
            } else {
                $v['page_attachment'] = '';
            }

            // locked page?
            if ($v['page_status'] == 'M') {
                if ($isLogin) {
                    $v['page_locked'] = $lang['page_unlocked'];
                } else {
                    $v['page_locked'] = $lang['page_locked'];
                }
            } else {
                $v['page_locked'] = '';
            }

            $v['page_pinned'] = '';
            $v['page_image_thumb'] = empty($v['page_image']) ? $config['site_url'].'/skins/_common/images/noimage.gif' : "public/thumb/$v[page_image]";
            $v['page_image'] = empty($v['page_image']) ? $config['site_url'].'/skins/_common/images/noimage.gif' : "public/image/$v[page_image]";
            $v['page_date'] = convert_date($v['page_date']);
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $v);
        }

        $txt['cid'] = 0;
        $txt['page_author'] = $author;
        $txt['sortby'] = create_select_form('sort', $page_sort, $sort);
        $txt['main_body'] = quick_tpl(load_tpl('page_list.tpl'), $txt);	// reload page.tpl
        flush_tpl();
    break;


    default:
        redir();
    break;
}
