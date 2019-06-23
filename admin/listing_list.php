<?php
require './../includes/admin_init.php';

admin_check(4);

// dir def
$dir_def = array();
$res = sql_query("SELECT * FROM ".$db_prefix."listing_dir");
while ($row = sql_fetch_array($res)) {
    $dir_def[$row['idx']] = $row;
    $dir_select_def[$row['idx']] = $row['dir_title'];
}

// sort def
$sort_def = array('ia' => 'ID', 'ta' => 'Title', 'da' => 'Entry Date Ascending', 'dd' => 'Entry Date Descending', 'sa' => 'Sponsored Date', 'sd' => 'Sponsored Date Descending');
$sort_sql = array('ia' => 'idx', 'ta' => 'item_title', 'da' => 'item_date', 'dd' => 'item_date desc', 'sa' => 'item_valid_date', 'sd' => 'item_valid_date desc');

// search?
$cmd = get_param('cmd');
$keyword = get_param('keyword');
$dir_id = get_param('dir_id');
$cat_id = get_param('cat_id');
$start = date_param('start_date');
$end = date_param('end_date');
$items = get_param('items');
$owner_id = get_param('owner_id');
$item_status = get_param('item_status');
$item_class = get_param('item_class');
$sort = get_param('sort', 'ta');
$mode = get_param('mode');
$p = get_param('p', 1);

$sql_arr = array();
switch ($cmd) {
    case 'setE':
    case 'setP':
        foreach ($_GET as $k => $v) {
            if (substr($k, 0, 7) == 'select_') {
                $kk = substr($k, 7);
                $row = sql_qquery("SELECT * FROM ".$db_prefix."listing WHERE idx='$kk' LIMIT 1");
                if (($row['item_status'] != 'P') && ($cmd == 'setP')) {	// from pending to published
                    for ($i = 1; $i <= 6; $i++) {
                        recount_num_link($row['dir_id'], $row['category_'.$i], 'inc');
                    }
                    sql_query("UPDATE ".$db_prefix."listing SET item_status='P' WHERE idx='$kk' LIMIT 1");
                } elseif ($cmd == 'setE') {	// from [any] to pending
                // from Published to pending -> recount
                    if ($row['item_status'] == 'P') {
                        for ($i = 1; $i <= 6; $i++) {
                            recount_num_link($row['dir_id'], $row['category_'.$i], 'dec');
                        }
                    }
                    sql_query("UPDATE ".$db_prefix."listing SET item_status='E' WHERE idx='$kk' LIMIT 1");
                }
                qcache_clear('dir_'.$row['dir_id'].'.cat_num_link', false);
            }
        }

        redir();
    break;

    case 'delAll':
        foreach ($_GET as $k => $v) {
            if (substr($k, 0, 7) == 'select_') {
                $kk = substr($k, 7);
                $row = sql_qquery("SELECT * FROM ".$db_prefix."listing WHERE idx='$kk' LIMIT 1");
                if ($row['item_status'] == 'P') {
                    for ($i = 1; $i <= 6; $i++) {
                        recount_num_link($row['dir_id'], $row['category_'.$i], 'dec');
                    }
                }
                sql_query("DELETE FROM ".$db_prefix."listing WHERE idx='$kk' LIMIT 1");
                sql_query("DELETE FROM ".$db_prefix."listing_cf_value WHERE item_id='$kk' LIMIT 1");
                sql_query("DELETE FROM ".$db_prefix."permalink WHERE target_script='detail.php' AND target_idx='$kk' LIMIT 1");
                @unlink('../public/listing/'.$kk.'_1.jpg');
                @unlink('../public/listing_thumb/'.$kk.'_1.jpg');
                @unlink('../public/listing_thumb/small_'.$kk.'_1.jpg');
                qcache_clear('dir_'.$row['dir_id'].'.cat_num_link', false);
            }
        }
        redir();
    break;

    default:
        if ($cmd == 'search') {
            if (!empty($keyword)) {
                $foo = array();
                $foo[] = create_where('item_title', $keyword);
                $foo[] = create_where('item_summary', $keyword);
                $foo[] = create_where('item_details', $keyword);
                $foo[] = create_where('item_url', $keyword);
                $sql_arr[] = '('.implode(') OR (', $foo).')';
            }

            if (!empty($cat_id)) {
                $foo = array();
                for ($i = 1; $i <= 6; $i++) {
                    $foo[] = 'category_'.$i."='$cat_id'";
                }
                $sql_arr[] = '('.implode(') OR (', $foo).')';
            }

            if (!empty($dir_id)) {
                $sql_arr[] = "dir_id='$dir_id'";
            }

            if (!empty($owner)) {
                $sql_arr[] = create_where('owner_id', $keyword);
            }

            if (!empty($start)) {
                $sql_arr[] = "(item_date >= '$start') AND (item_date <= '$end')";
            }

            if (!empty($item_status)) {
                $sql_arr[] = "item_status = '$item_status'";
            }

            if (!empty($item_class)) {
                $sql_arr[] = "item_class = '$item_class'";
            }

            if (!empty($items)) {
                $sql_arr[] = "idx IN ($items)";
            }

            if ($mode == 'or') {
                $sql_str = '('.implode(') OR (', $sql_arr).')';
            } else {
                $sql_str = '('.implode(') AND (', $sql_arr).')';
            }

            if ($sql_str == '()') {
                admin_die($lang['msg']['admin_err']);
            }
        } else {
            $sql_str = '1=1';
        }

        //
        $tpl = load_tpl('adm', 'listing_list.tpl');
        $txt['block_list'] = '';

        // get list
        $result = sql_multipage($db_prefix.'listing', '*', $sql_str, $sort_sql[$sort], $p);
        foreach ($result as $row) {
            get_dir_info($row['dir_id']);
            $row['dir_short'] = $dir_def[$row['dir_id']]['dir_short'];
            $row['dir_title'] = $dir_def[$row['dir_id']]['dir_title'];

            // summary
            if (empty($row['item_summary'])) {
                $row['item_summary'] = line_wrap($row['item_details'], 100);
            }

            // image
            if (file_exists('../public/listing_thumb/small_'.$row['idx'].'_1.jpg')) {
                $row['image_small'] = '../public/listing_thumb/small_'.$row['idx'].'_1.jpg';
            } else {
                $row['image_small'] = '../skins/_common/images/noimage.gif';
            }

            // cats
            $cat_arr = array();
            for ($i = 1; $i <= 6; $i++) {
                if ($row['category_'.$i]) {
                    $cat_arr[] = '<span title="'.$dir_info[$row['dir_id']]['cat_structure'][$row['category_'.$i]].'" class="helpblack">'.$dir_info[$row['dir_id']]['cat_name_def'][$row['category_'.$i]].'</span>';
                }
            }
            $row['category'] = implode(', ', $cat_arr);

            // class
            if ($row['item_class'] != 'R') {
                $valid_date = ' till '.convert_date($row['item_valid_date']);
            } else {
                $valid_date = '';
            }
            $row['item_class'] = $listing_class_def[$row['item_class']].$valid_date;

            // change request?
            if ($row['original_idx']) {
                $row['change'] = "<div class=\"bg-info small\"><span class=\"glyphicon glyphicon-transfer\"></span> <a href=\"listing.php?cmd=edit&amp;item_id=$row[original_idx]\">Change request for item ID $row[original_idx]</a></div>";
            } else {
                $row['change'] = '';
            }

            // others
            $row['item_status'] = $listing_status_def[$row['item_status']];
            $row['item_date'] = convert_date($row['item_date']);

            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        $txt['dir_id'] = $dir_id;
        $txt['cat_id'] = $cat_id;
        $txt['owner_id'] = $owner_id;
        $txt['keyword'] = $keyword;
        // ($select_name, $source, $selected_value = '', $first_line = '', $disabled = 0, $addtl_option = '')
        $txt['dir_select'] = create_select_form('dir_id', $dir_select_def, $dir_id, '(Any)', false, 'onchange=update_cat(this.value)');
        $txt['start_date'] = date_form('start_date', date('Y'), 1, 1, $start ? $start : $sql_today);
        $txt['end_date'] = date_form('end_date', date('Y'), 1, 1, $end ? $end : $sql_today);
        $txt['status_select'] = create_select_form('item_status', $listing_status_def, $item_status, '(Any)');
        $txt['class_select'] = create_select_form('item_class', $listing_class_def, $item_class, '(Any)');
        $txt['mode_select'] = create_select_form('mode', array('and' => 'AND', 'or' => 'OR'), $mode);
        $txt['sort_select'] = create_select_form('sort', $sort_def, $sort);
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
