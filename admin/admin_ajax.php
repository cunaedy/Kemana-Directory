<?php
// fix string to be used for json output (eg. convert " to &quot;), useful if you don't use json_encode
// @param input = array of string to be fixe
function fixit($input)
{
    $output = array();
    foreach ($input as $k => $v) {
        $output[$k] = str_replace('"', '&quot;', $v);
    }

    return $output;
}

require './../includes/admin_init.php';
$adm_lvl = admin_check(1);

$cmd = get_param('cmd');
$q = get_param('query');
$limit = 20;

if (empty($q)) {
    flush_json(9998, 'Undefined query (q) for ajax mode: '.$cmd);
}

switch ($cmd) {
    // show sign of new notification only
    case 'notifydot':
        $row = sql_qquery("SELECT COUNT(*) AS num FROM ".$db_prefix."notification WHERE notify_admin='1' AND notify_read='0'");
        if (empty($row['num'])) {
            $output = '';
        } else {
            $output = '<span>&bull;</span>';
        }
        echo $output;
        die;
    break;

    // show number of notification, and fire up gritter for new notification
    case 'notify':
        $row = sql_qquery("SELECT COUNT(*) AS num FROM ".$db_prefix."notification WHERE notify_admin='1' AND notify_read='0'");
        if (empty($row['num'])) {
            $output = '';
        } else {
            $output = $row['num'];
            $row = sql_qquery("SELECT * FROM ".$db_prefix."notification WHERE notify_admin='1' AND notify_read='0' AND notify_popup='0' ORDER BY idx LIMIT 1");
            if ($row) {
                $output .= "<script>$.gritter.add({title: 'Notification',text: '$row[notify_subject]'})</script>";
                sql_query("UPDATE ".$db_prefix."notification SET notify_popup='1' WHERE idx='$row[idx]' LIMIT 1");
            }
        }
        echo $output;
        die;
    break;


    // display list of notification
    case 'notifylist':
        $output = "<div style=\"text-align:right\"><a href=\"$config[site_url]/$config[admin_folder]/index.php?cmd=notify_read\" title=\"Mark all as read\" style=\"margin-right:10px\"><span class=\"glyphicon glyphicon-eye-close\" aria-hidden=\"true\"></span></a>
		<a href=\"$config[site_url]/$config[admin_folder]/index.php?cmd=notify_clear\" title=\"Remove all notifications\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></a></div>
		<div style=\"clear:both\"></div>";

        $i = 0;
        $res = sql_query("SELECT * FROM ".$db_prefix."notification WHERE notify_admin='1' ORDER BY idx");
        while ($row = sql_fetch_array($res)) {
            $i++;
            $date = convert_date(date('Y-m-d', $row['notify_time'])).', '.date('H:i', $row['notify_time']);
            if (!empty($row['notify_url'])) {
                $ii = "<a href=\"$config[site_url]/$config[admin_folder]/index.php?cmd=notify_go&amp;idx=$row[idx]\" target=\"acp\">$row[notify_subject]</a><div class=\"small\">$date</div>\n";
            } else {
                $ii = "$date: $row[notify_subject]\n<div class=\"small\">$date</div>";
            }
            if ($row['notify_read']) {
                $output .= "$ii\n<hr />";
            } else {
                $output .= "<b>$ii</b>\n<hr />";
            }
        }
        if (!$i) {
            $output .= "You don't have any notifications!";
        } else {
            $output = substr($output, 0, -6);
        }
        sql_query("UPDATE ".$db_prefix."notification SET notify_popup='1' WHERE notify_admin='1' AND notify_popup='0'");
        echo $output;
        die;
    break;


    // list of pages & submenu for menu editor
    case 'page':
        $suggest = $data = '';
        $output = array();
        $res = sql_query("SELECT page_id, page_title FROM ".$db_prefix."page WHERE (page_title LIKE '%$q%') OR (page_id LIKE '$q%') ORDER BY page_title LIMIT $limit");
        while ($row = sql_fetch_array($res)) {
            $output[] = array('value' => "$row[page_id]: $row[page_title]", 'data' => $row['page_id']);
        }

        $res = sql_query("SELECT menu_id, menu_title FROM ".$db_prefix."menu_set WHERE (menu_title LIKE '%$q%') OR (menu_id LIKE '$q%') LIMIT $limit");
        while ($row = sql_fetch_array($res)) {
            $output[] = array('value' => "(submenu) $row[menu_title]", 'data' => "[[sm:$row[menu_id]]]");
        }

        // format data to JSON
        echo "{\n";
        echo "\"query\":\"$q\",\n\"suggestions\":";
        echo json_encode($output);
        echo "}";
        die;
    break;


    // list of pages for page editor (related page field)
    case 'related_page':
        $suggest = array();
        $res = sql_query("SELECT page_id, page_title FROM ".$db_prefix."page WHERE (page_title LIKE '%$q%') OR (page_id LIKE '$q%') LIMIT $limit");
        while ($row = sql_fetch_array($res)) {
            $suggest[] = array('id' => $row['page_id'], 'name' => $row['page_title']);
        }

        // output
        echo json_encode($suggest);
        die;
    break;


    // kemana
    case 'item':
        $suggest = $data = '';
        $res = sql_query("SELECT idx, item_title FROM ".$db_prefix."listing WHERE (item_title LIKE '%$q%') OR (idx LIKE '$q%') ORDER BY item_title LIMIT $limit");
        while ($row = sql_fetch_array($res)) {
            $row = fixit($row);
            $suggest .= "{\"id\":\"$row[idx]\",\"name\":\"$row[item_title]\"},";
        }

        // format data to JSON
        $suggest = substr($suggest, 0, -1);
        echo "[$suggest]";
        die;
    break;


    case 'user':
    case 'email':
        $output = array();
        if ($cmd == 'user') {
            $res = sql_query("SELECT user_id FROM ".$db_prefix."user WHERE (user_id LIKE '%$q%') ORDER BY user_id LIMIT $limit");
            while ($row = sql_fetch_array($res)) {
                $output[] = array('value' => "$row[user_id]", 'data' => $row['user_id']);
            }
        } else {
            $res = sql_query("SELECT user_email FROM ".$db_prefix."user WHERE (user_email LIKE '%$q%') ORDER BY user_email LIMIT $limit");
            while ($row = sql_fetch_array($res)) {
                $output[] = array('value' => "$row[user_email]", 'data' => $row['user_email']);
            }
        }

        // format data to JSON
        echo "{\n";
        echo "\"query\":\"$q\",\n\"suggestions\":";
        echo json_encode($output);
        echo "}";
        die;
    break;


    case 'cat_form':
        $output = array();
        $dir_id = get_param('dir_id');
        $cat_id = get_param('cat_id');
        if ($q == 'listing_list') {
            if (empty($dir_id)) {
                $cat_structure = array();
            } else {
                get_dir_info($dir_id);
                $cat_structure = $dir_info[$dir_id]['cat_structure'];
            }
            echo create_select_form('cat_id', $cat_structure, $cat_id, '(Any)');
        }
    break;


    case 'backlink_update':
        $item_id = get_param('item_id');
        if (!empty($q) && !empty($item_id)) {
            if (verify_backlink($q)) {
                sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok='1' WHERE idx='$item_id' LIMIT 1");
                flush_json(1);
            } else {
                sql_query("UPDATE ".$db_prefix."listing SET item_backlink_ok='0' WHERE idx='$item_id' LIMIT 1");
                flush_json(0);
            }
        }
    break;


    default:
        flush_json(9999, 'Undefined ajax mode '.$cmd);
    break;
}
