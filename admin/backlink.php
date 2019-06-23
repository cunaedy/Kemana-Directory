<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(3);
$cmd = get_param('cmd');
$p = get_param('p');
$w = get_param('w');

// status icons
$isOK = '<span class="glyphicon glyphicon-ok text-success"></span>';
$isNOT = '<span class="glyphicon glyphicon-remove text-danger"></span>';
$isUM =  '<span class="text-warning"><b>?</b></span>';

switch ($cmd) {
    case 'reset':
        sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok=''");
        admin_die('admin_ok');
    break;


    default:
        // dir def
        $dir_def = array();
        $res = sql_query("SELECT * FROM ".$db_prefix."listing_dir WHERE dir_backlink='1'");
        while ($row = sql_fetch_array($res)) {
            $dir_def[] = $row['idx'];
        }
        $dir_backlink = implode(',', $dir_def);
        if (!$dir_backlink) {
            $dir_backlink = 0;
        }

        // fix empty backlink
        sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok='0' WHERE item_backlink_url=''");

        // tpl
        $tpl = load_tpl('adm', 'backlink.tpl');

        // filter
        $where = array("(dir_id IN ($dir_backlink))");
        if ($w == 'valid') {
            $where[] = "(item_backlink_ok='1')";
        } elseif ($w == 'invalid') {
            $where[] = "(item_backlink_ok='0')";
        } elseif ($w == 'notyet') {
            $where[] = "(item_backlink_ok='')";
        }
        $sql_where = implode(' AND ', $where);

        // list
        $items = array();
        $txt['block_list'] = '';
        $result = sql_multipage($db_prefix.'listing', '*', $sql_where, '', $p);
        foreach ($result as $row) {
            get_dir_info($row['dir_id']);
            $row['dir_title'] = $dir_info['structure'][$row['dir_id']];

            // image
            if (file_exists('../public/listing_thumb/small_'.$row['idx'].'_1.jpg')) {
                $row['image_small'] = '../public/listing_thumb/small_'.$row['idx'].'_1.jpg';
            } else {
                $row['image_small'] = '../skins/_common/images/noimage.gif';
            }

            // cats
            $row['category'] = '<span title="'.$dir_info[$row['dir_id']]['cat_structure'][$row['category_1']].'" class="helpblack">'.$dir_info[$row['dir_id']]['cat_name_def'][$row['category_1']].'</span>';

            // BL status
            if ($row['item_backlink_ok'] == '1') {
                $row['status'] = $isOK;
            } elseif ($row['item_backlink_ok'] == '0') {
                $row['status'] = $isNOT;
            } else {
                $row['status'] = $isUM;
            }

            //if (!$row['item_backlink_url']) $row['status'] = $isNOT;

            // others
            $items[] = $row['idx'];
            $row['item_status'] = $listing_status_def[$row['item_status']];
            $row['item_date'] = convert_date($row['item_date']);
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        $txt['items'] = implode(',', $items);
        $txt['axsrf'] = AXSRF_value();
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
