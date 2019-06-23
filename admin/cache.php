<?php
// recount number of item for a cat, including its sub cats, sub-sub cats, etc
// $top = the category id to count
// $cat_id = current child to inspect its num of items
function recount_from_top($top, $cat_id = 0)
{
    global $cat_parent, $cat_num, $rec_num_item;

    // init if cat_id = 0
    if (empty($cat_id)) {
        $cat_id = $top;
        set_time_limit(1);	// request additional time limit, in case it's a big db
        echo '. ';
    }

    // what are the children?
    foreach ($cat_parent as $k => $v) {
        if ($v == $cat_id) {
            // add children num of items to top & redo the same for children
            $rec_num_item[$top] = $rec_num_item[$top] + $cat_num[$k];
            recount_from_top($top, $k);
        }
    }
}


// part of qEngine
require_once "./../includes/admin_init.php";
admin_check('site_setting');

$cmd = get_param('cmd');
$all_start = getmicrotime();


switch ($cmd) {
    case 'do_cache':
        html_header();
        echo '<div style="float:left; width:80px"><img src="../skins/_common/images/loading.gif" alt="loading" /></div><h1>Please Wait...</h1>';

        // 1. remove local caches
        echo '<p><b>[Removing Cache]</b><br />';
        $start = getmicrotime();
        sql_query("TRUNCATE TABLE ".$db_prefix."cache");
        sql_query("UPDATE ".$db_prefix."language SET lang_value='' WHERE lang_key='_config:cache'");
        $finish = getmicrotime();
        echo num_format($finish - $start, 3).'s</p>';

        // 2. optimize tables
        $table_prefix = $db_prefix;
        $len_prefix = strlen($table_prefix);
        echo '<p><b>[Optimizing Tables]</b><br />';
        $start = getmicrotime();
        $res = sql_query("SHOW TABLES");
        while ($row = sql_fetch_array($res)) {
            $t = $row[0];
            if (substr($t, 0, $len_prefix) == $table_prefix) {
                echo '. ';
                sql_query('OPTIMIZE TABLE `$t`');
            }
        }
        $finish = getmicrotime();
        echo num_format($finish - $start, 3).'s</p>';

        // 3. recount
        echo '<p><b>[Recounting Number of Items]</b><br />';
        $start = getmicrotime();

        // 3.1 - recount num of items for each cats, (cat_num = num of item for a cat, rec_num_item = recursive total num of items, cat_parent[cat_id] = cat_parent_id)
        $cat_num = $rec_num_item = $cat_parent = array();
        $res = sql_query("SELECT idx, parent_id FROM ".$db_prefix."listing_cat");
        while ($row = sql_fetch_array($res)) {
            $cat_num[$row['idx']] = $rec_num_item[$row['idx']] = 0;
            $cat_parent[$row['idx']] = $row['parent_id'];
        }

        $res = sql_query("SELECT category_1, category_2, category_3, category_4, category_5, category_6 FROM ".$db_prefix."listing");
        while ($row = sql_fetch_array($res)) {
            for ($i = 1; $i < 7; $i++) {
                $ck = 'category_'.$i;
                if ($row[$ck]) {
                    $cat_num[$row[$ck]]++;
                }
            }
        }

        // 3.2 - start recounting
        $rec_num_item = $cat_num;
        foreach ($cat_parent as $k => $v) {
            recount_from_top($k);
        }

        // 3.3 - finally, update db
        foreach ($rec_num_item as $k => $v) {
            sql_query("UPDATE ".$db_prefix."listing_cat SET cat_num_link='$v' WHERE idx='$k' LIMIT 1");
        }
        $finish = getmicrotime();
        echo num_format($finish - $start, 3).'s</p>';

        // 4. complete cache
        $finish = getmicrotime();
        echo '</div><h2 style="clear:both">Done in '.num_format($finish - $all_start, 3).'s. Please close this window.</h2>';
        html_footer();
    break;

    default:
        $txt['main_body'] = quick_tpl(load_tpl('adm', 'cache.tpl'), 0);
        flush_tpl('adm');
    break;
}
