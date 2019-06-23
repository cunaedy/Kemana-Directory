<?php
$output = '';
global $tpl_mode, $list_class, $dir_info, $listing_visible_icon, $listing_visible_def;
if (empty($mod_ini['mode'])) {
    $mod_ini['mode'] = '';
}
if (empty($mod_ini['mode']) && $inline) {
    $mod_ini['mode'] = $mod_raw;
}

switch ($mod_ini['mode']) {
    case 'backlink':
        $output = $config['ke']['backlink_code'];
        if ($isLogin) {
            $output = str_replace('[user_id]', $current_user_id, $output);
        }
    break;


    case 'item_list':
        // init vars
        $dir_id = empty($mod_ini['dir_id']) ? false : $mod_ini['dir_id'];
        $items = empty($mod_ini['items']) ? 'random' : $mod_ini['items'];
        $limit = empty($mod_ini['limit']) ? 10 : $mod_ini['limit'];
        $random = empty($mod_ini['random']) ? false : $mod_ini['random'];
        $display = empty($mod_ini['display']) ? 'grid' : $mod_ini['display'];
        $user_id = empty($mod_ini['user_id']) ? false : $mod_ini['user_id'];
        $item_status = empty($mod_ini['item_status']) ? 'P' : $mod_ini['item_status'];
        $item_visibility = empty($mod_ini['item_visibility']) ? 'A' : $mod_ini['item_visibility'];
        $csswrapper = empty($mod_ini['csswrapper']) ? false : $mod_ini['csswrapper'];
        $div_id = empty($mod_ini['div_id']) ? false : $mod_ini['div_id'];

        // overwrite display mode
        if (!empty($mod_ini['display_overwrite'])) {
            $display = $mod_ini['display_overwrite'];
        }
        if (!empty($mod_ini['csswrapper_grid']) && $display == 'grid') {
            $csswrapper = $mod_ini['csswrapper_grid'];
        }
        if (!empty($mod_ini['csswrapper_list']) && $display == 'list') {
            $csswrapper = $mod_ini['csswrapper_list'];
        }

        // fix stufss
        if (!in_array($item_status, array('P', 'E', 'T', 'ALL'))) {
            $item_status = 'P';
        }
        if (!in_array($item_visibility, array('A', 'M', 'H', 'ALL'))) {
            $item_visibility = 'A';
        }

        // init sql
        $sql_where = $sql_order = '';
        if ($items == 'random') {
            if ($dir_id) {
                $sql_where = "dir_id='$dir_id'";
            }
            $sql_order = "RAND()";
        } elseif ($items == 'best') {
            if ($dir_id) {
                $sql_where = "dir_id='$dir_id'";
            }
            $sql_order = "stat_hits DESC";
        } elseif ($items == 'newest') {
            if ($dir_id) {
                $sql_where = "dir_id='$dir_id'";
            }
            $sql_order = "item_date DESC";
        } elseif ($items == 'see_also') {
            if ($isPermalink) {
                $_GET['item_id'] = $original_idx;
            }
            $item_id = get_param('item_id');
            $foo = sql_qquery("SELECT see_also FROM ".$db_prefix."listing WHERE idx='$item_id' LIMIT 1");
            if (empty($foo['see_also'])) {
                if ($random) {
                    $sql_order = "RAND()";
                } else {
                    $sql_where = '1=2';
                }
            } else {
                $sql_where = "t1.idx IN ($foo[see_also])";
            }
        } elseif ($items == 'cat_featured') {
            if ($isPermalink) {
                $_GET['cat_id'] = $original_idx;
            }
            $cat_id = get_param('cat_id');
            $foo = sql_qquery("SELECT cat_featured FROM ".$db_prefix."listing_cat WHERE idx='$cat_id' LIMIT 1");
            if (empty($foo['cat_featured'])) {
                if ($random) {
                    $sql_order = "RAND()";
                } else {
                    $sql_where = '1=2';
                }
            } else {
                $sql_where = "t1.idx IN ($foo[cat_featured])";
                $limit = substr_count($foo['cat_featured'], ',') + 1;
            }
        } elseif ($items == 'dir_featured') {
            if (!$dir_id) {
                if ($isPermalink) {
                    $_GET['dir_id'] = $original_idx;
                }
                $dir_id = get_param('dir_id');
            }
            $foo = sql_qquery("SELECT dir_featured FROM ".$db_prefix."listing_dir WHERE idx='$dir_id' LIMIT 1");
            if (empty($foo['dir_featured'])) {
                if ($random) {
                    $sql_order = "RAND()";
                } else {
                    $sql_where = '1=2';
                }
            } else {
                $sql_where = "t1.idx IN ($foo[dir_featured])";
                $limit = substr_count($foo['dir_featured'], ',') + 1;
            }
        } elseif ($items == 'site_featured') {
            if (empty($config['ke']['featured_listing'])) {
                if ($random) {
                    $sql_order = "RAND()";
                } else {
                    $sql_where = '1=2';
                }
            } else {
                $sql_where = "t1.idx IN (".$config['ke']['featured_listing'].")";
                $limit = substr_count($config['ke']['featured_listing'], ',') + 1;
            }
        } elseif ($items == 'history') {
            $history = empty($_COOKIE[$db_prefix.'history']) ? array() : $_COOKIE[$db_prefix.'history'];

            $i = 0;
            $foo = array();
            $count = count($history);
            foreach ($history as $k => $v) {
                if (is_numeric($v)) {
                    $foo[] = $v;
                }
            }
            $history = implode(',', array_slice(array_unique($foo), -1 * $limit, $limit));
            if (empty($history)) {
                $history = 0;
            }

            $sql_where = "t1.idx IN ($history)";
            $sql_order = "FIELD(t1.idx,$history) DESC";

            // update cookies
            $item_id = get_param('item_id');
            if ($item_id && !in_array($item_id, $foo) && is_numeric($item_id)) {
                setcookie($db_prefix."history[$count]", $item_id, 0, '/', cookie_domain());
            }
        } elseif ($items == 'user_id') {
            if (!empty($user_id)) {
                $sql_where = "owner_id = '$user_id'";
            } else {
                $output = '<!-- user_id may not be empty -->';
                return false;
            }
        } elseif ($items == 'fave') {
            if ($isLogin) {
                global $current_user_info;
                $fave = $current_user_info['user_fave'];
                if (!$fave) {
                    $fave = 0;
                }
                $sql_where = "t1.idx IN ($fave)";
            } else {
                $output = '<!-- user may not login -->';
                return false;
            }
        } else {
            $sql_where = "t1.idx IN ($items)";
        }

        // load tpl
        if ($display == 'grid') {
            $tpl_mode = 'list_gridbox';
        } elseif ($display == 'list') {
            $tpl_mode = 'list_listbox';
        } else {
            $tpl_mode = 'list_list';
        }
        $tpl = load_tpl('mod', 'module_ke_core_list.tpl');

        // build sql
        $sql = "SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id)";

        // - where
        $where = array();
        if ($item_status != 'ALL') {
            $where[] = "(item_status='$item_status')";
        }
        if ($item_visibility != 'ALL') {
            $where[] = "(item_visibility='$item_visibility')";
        }
        if ($sql_where) {
            $where[] = "($sql_where)";
        }

        $sql = $sql.' WHERE '.implode(' AND ', $where);

        if ($sql_order) {
            $sql .= " ORDER BY $sql_order";
        }
        $sql .= " LIMIT $limit";

        // go sql, go!
        $i = 0;
        $res = sql_query($sql);
        while ($row = sql_fetch_array($res)) {
            $i++;
            get_dir_info($row['dir_id']);
            process_listing_info($row);

            $row['item_rating_pct'] = $row['item_rating'] / 5;
            $row['csswrapper'] = $csswrapper;
            $row['rating'] = '';
            $row['list_class'] = $list_class[$row['item_class']];
            $row['listing_label'] = $lang[$row['item_class'].'_label'];
            $row['visible_icon'] = $listing_visible_icon[$row['item_visibility']];
            $row['visible_help'] = $listing_visible_def[$row['item_visibility']];
            if ($row['owner_id'] && ($current_user_id == $row['owner_id'])) {
                $row['edit_btn'] = quick_tpl($tpl_section['edit'], $row);
            } else {
                $row['edit_btn'] = '';
            }

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
            $output .= quick_tpl($tpl, $row);
        }

        if (!$i) {
            $output = '<!-- no items to display -->';
        } else {
            if ($div_id) {
                $mod_ini_str = str_replace('%', '%%', safe_send($mod_raw));
                $js = "$('#$div_id').load('$config[site_url]/task.php?mod=ke_core&amp;mod_ini=$mod_ini_str&amp;mode=%s')";
                $nav = "<div style=\"text-align:right; width:100%\">\n";
                if ($display == 'list') {
                    $nav .= "<a href=\"javascript:void(0)\" onclick=\"".sprintf($js, 'grid')."\"><span class=\"glyphicon glyphicon-th\"></span></a>\n";
                } else {
                    $nav .= "<span class=\"glyphicon glyphicon-th\"></span>\n";
                }

                if ($display == 'grid') {
                    $nav .= "<a href=\"javascript:void(0)\" onclick=\"".sprintf($js, 'list')."\"><span class=\"glyphicon glyphicon-th-list\"></span></a></div>\n";
                } else {
                    $nav .= "<span class=\"glyphicon glyphicon-th-list\"></span>";
                }

                $output = $nav.$output;
            }
        }
    break;
}
