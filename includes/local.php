<?php
// part of qEngine

/******************************************************************

 LOCAL FUNCTIONS

******************************************************************/

// get category structure, like: cat 1; cat 1 > cat 1-1; cat 1 > cat 1-1 > cat 1-1-1
// returns: GLOBAL vars of: (contained in $dir_info)
// $cat_structure = array of each cat ID & its name with its PARENT ([1] => 'Abc'; [2] => 'Abc > Cde'; [3] => 'Abc > Cde > Fgh');
// $cat_name_def = array of category name only with out parent name ([1] => 'Abc'; [2] => 'Cde'; [3] => 'Fgh');
// $cat_url = array of category url (permalink if enabled, dynamic otherwise);
// $cat_num_link = array of number of links;
// $cat_structure_top = array of all TOP LEVEL ONLY cat ID & its name
// $cat_structure_id = array of PATH of cat ID ([1] => 1; [2] => 1,2; [3] => 1,2,3)
// $cat_structure_html = string of category structure in HTML (unordered list <ul><li>)
// all values will be stored in cache (if cache enabled) & sorted accordingly
function get_cat_structure($dir_id, $cat_id = 0, $level = 0, $prefix = '', $prefix_id = '')
{
    // global $cat_structure, $cat_structure_top, $cat_structure_id, $cat_structure_html, $cat_name_def, $cat_permalink_def, , $cat_sort, $cat_structure_link;
    global $dir_info, $config, $db_prefix;

    $exists = false;
    $cat_sort = $dir_info[$dir_id]['cat_sort'];
    $res = sql_query("SELECT * FROM ".$db_prefix."listing_cat WHERE parent_id='$cat_id' AND dir_id='$dir_id' ORDER BY FIELD(idx,$cat_sort)");

    if (!isset($dir_info[$dir_id]['cat_structure_html'])) {
        $dir_info[$dir_id]['cat_structure_html'] = '';
    }

    while ($row = sql_fetch_array($res)) {
        if (!$exists) {
            if ($level == 0) {
                $dir_info[$dir_id]['cat_structure_html'] .= str_repeat("\t", $level)."<ul id=\"myID\" class=\"myCLASS\">\n";
            } else {
                $dir_info[$dir_id]['cat_structure_html'] .= str_repeat("\t", $level)."<ul>\n";
            }
            $exists = true;
        }

        $path = (empty($prefix)) ? $row['cat_name'] : strip_tags($prefix).' &raquo; '.$row['cat_name'];
        $path_id = (empty($prefix_id)) ? $row['idx'] : $prefix_id.','.$row['idx'];
        $dir_info[$dir_id]['cat_structure'][$row['idx']] = $path;
        if (!$level) {
            $dir_info[$dir_id]['cat_structure_top'][$row['idx']] = $row['cat_name'];
        }
        if ($config['enable_adp'] && $row['permalink']) {
            $row['url'] = $config['site_url'].'/'.$row['permalink'];
        } else {
            $row['url'] = "$config[site_url]/listing_search.php?cat_id=$row[idx]";
        }
        $path_link = (empty($prefix)) ? "<a href=\"$row[url]\">$row[cat_name]</a>" : $prefix.' &raquo; '."<a href=\"$row[url]\">$row[cat_name]</a>";
        $dir_info[$dir_id]['cat_name_def'][$row['idx']] = $row['cat_name'];
        $dir_info[$dir_id]['cat_url'][$row['idx']] = $row['url'];
        $dir_info[$dir_id]['cat_num_link'][$row['idx']] = $row['cat_num_link'];
        // $dir_info[$dir_id]['cat_image'][$row['idx']] = $row['cat_image'];
        $dir_info[$dir_id]['cat_structure_link'][$row['idx']] = $path_link;
        $dir_info[$dir_id]['cat_structure_id'][$row['idx']] = $path_id;
        // $dir_info[$dir_id]['cat_permalink_def'][$row['idx']] = $row['permalink'];
        $dir_info[$dir_id]['cat_structure_html'] .= str_repeat("\t", $level + 1)."<li><a href=\"$row[url]\">$row[cat_name]</a>\n";
        get_cat_structure($dir_id, $row['idx'], $level+1, $path_link, $path_id);
    }
    if ($exists) {
        $dir_info[$dir_id]['cat_structure_html'] .= str_repeat("\t", $level)."</ul>\n";
    }
}


// just in time dir_info generator, all gathered info will be stored in $dir_info array, so if the script needs info, it won't be rebuilt
// $dir_id = dir id
// return $dir_info (also as global var)
function get_dir_info($dir_id = 0)
{
    global $dir_info, $config, $db_prefix;

    if (!isset($dir_info)) {
        $dir_info = array();
    }

    // get dir summary
    if (empty($dir_info['config']['ok'])) {
        $foo = qcache_get(array('dir.config', 'dir.structure'), false, true);
        if (empty($foo['dir.config']) || empty($foo['dir.structure'])) {
            $c = 0;
            $dirs = array();
            $res = sql_query("SELECT * FROM ".$db_prefix."listing_dir");
            while ($row = sql_fetch_array($res)) {
                $c++;

                // default?
                if ($row['dir_default']) {
                    $dir_info['config']['default'] = $row['idx'];
                    $dir_info['config']['ok'] = true;
                }

                // dir structure
                $dirs[$row['idx']] = $row['dir_title'];
            }

            if ($c == 1) {
                $dir_info['config']['multi'] = false;
                $dir_info['config']['number'] = 1;
            } else {
                $dir_info['config']['multi'] = true;
                $dir_info['config']['number'] = $c;
            }

            $dir_info['structure'] = $dirs;
            qcache_update('dir.config', serialize($dir_info['config']), false, true);
            qcache_update('dir.structure', serialize($dir_info['structure']), false, true);
        } else {
            $dir_info['config'] = unserialize($foo['dir.config']);
            $dir_info['structure'] = unserialize($foo['dir.structure']);
        }
    }
    if (!$dir_id) {
        return $dir_info;
    }

    // get dir specific info
    if (empty($dir_info[$dir_id]['ok'])) {
        // dir info
        $dir_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_dir WHERE idx='$dir_id' LIMIT 1");
        if (!$dir_inf) {
            just_die('Invalid DIR_ID! Please contact web administrator!');
        }
        foreach ($dir_inf as $k => $v) {
            if (is_numeric($k)) {
                unset($dir_inf[$k]);
            }
        }

        $dir_info[$dir_id]['dir_inf'] = $dir_inf;
        $dir_info[$dir_id]['ok'] = false;

        // url
        if ($config['enable_adp'] && $dir_inf['dir_permalink']) {
            $dir_info[$dir_id]['dir_inf']['url'] = $config['site_url'].'/'.$dir_inf['dir_permalink'];
        } else {
            $dir_info[$dir_id]['dir_inf']['url'] = $config['site_url'].'/index.php?dir_id='.$dir_id;
        }

        // get cache for everything
        $cat_cache_def = array('cf_custom_sort', 'cf_define', 'cat_sort', 'cat_structure_top', 'cat_structure', 'cat_structure_id', 'cat_name_def', 'cat_url', 'cat_num_link', 'cat_structure_html', 'cat_structure_link');
        $dir_cache_def = array();
        foreach ($cat_cache_def as $k => $v) {
            $dir_cache_def[] = 'dir_'.$dir_id.'.'.$v;
        }
        $foo = qcache_get($dir_cache_def, false, true);
        $ok = true;
        foreach ($dir_cache_def as $k => $v) {
            if (empty($foo[$v])) {
                $ok = false;
            }
        }
        if (!$ok) {
            // init
            foreach ($cat_cache_def as $k => $v) {
                $dir_info[$dir_id][$v] = array();
            }
            $dir_info[$dir_id]['cat_structure_html'] = '';

            // cat sorting
            $cat_sort = array();
            $res = sql_query("SELECT * FROM ".$db_prefix."menu_item AS t1 JOIN ".$db_prefix."listing_cat AS t2 WHERE t1.idx=t2.menu_item_id AND t2.dir_id='$dir_id' ORDER BY t1.menu_order");
            while ($row = sql_fetch_array($res)) {
                $cat_sort[] = $row['idx'];
            }
            $cat_sort = implode(',', $cat_sort);
            if (empty($cat_sort)) {
                $dir_info[$dir_id]['cat_sort'] = $cat_sort = 0;
            } else {
                $dir_info[$dir_id]['cat_sort'] = $cat_sort;
            }
            get_cat_structure($dir_id);

            // cf sorting
            $cf_custom_sort = array();
            $res = sql_query("SELECT * FROM ".$db_prefix."menu_item AS t1 JOIN ".$db_prefix."listing_cf_define AS t2 WHERE t1.idx=t2.menu_item_id AND t2.dir_id='$dir_id' ORDER BY t1.menu_order");
            while ($row = sql_fetch_array($res)) {
                $cf_custom_sort[] = $row['idx'];
            }
            $cf_custom_sort = implode(',', $cf_custom_sort);
            if (empty($cf_custom_sort)) {
                $dir_info[$dir_id]['cf_custom_sort'] = $cf_custom_sort = 0;
            } else {
                $dir_info[$dir_id]['cf_custom_sort'] = $cf_custom_sort;
            }

            // cf definitions
            $res = sql_query("SELECT * FROM ".$db_prefix."listing_cf_define WHERE dir_id='$dir_id' ORDER BY FIELD(idx,$cf_custom_sort)");
            while ($row = sql_fetch_array($res)) {
                foreach ($row as $k => $v) {
                    if (is_numeric($k)) {
                        unset($row[$k]);
                    }
                }

                // based on item_class
                $row['avail_to_R'] = $row['avail_to_P'] = $row['avail_to_S'] = false;
                $foo = explode(',', $row['avail_to']);
                foreach ($foo as $k => $v) {
                    $row['avail_to_'.$v] = true;
                }

                $dir_info[$dir_id]['cf_define']['cf_'.$row['idx']] = $row;
            }

            foreach ($cat_cache_def as $k => $v) {
                qcache_update('dir_'.$dir_id.'.'.$v, serialize($dir_info[$dir_id][$v]), false, true);
            }
        } else {
            foreach ($cat_cache_def as $k => $v) {
                $dir_info[$dir_id][$v] = unserialize($foo['dir_'.$dir_id.'.'.$v]);
            }
        }


        // all ok -> flag as ok, so we don't need to rebuild everything from scratch
        $dir_info[$dir_id]['ok'] = true;
    }

    return $dir_info;
}


function recount_num_link($dir_id, $cat_id, $mode)
{
    global $dir_info, $db_prefix;
    get_dir_info($dir_id);

    // if cat_id_arr = empty -> do nothing
    if (empty($cat_id)) {
        return;
    }

    $cid_str = explode(',', $dir_info[$dir_id]['cat_structure_id'][$cat_id]);
    foreach ($cid_str as $k => $v) {
        if ($mode == 'inc') {
            sql_query("UPDATE ".$db_prefix."listing_cat SET cat_num_link=cat_num_link+1 WHERE idx='$v' LIMIT 1");
        }
        if ($mode == 'dec') {
            sql_query("UPDATE ".$db_prefix."listing_cat SET cat_num_link=cat_num_link-1 WHERE idx='$v' LIMIT 1");
        }
    }
}


// make sure cats are unique!
function cat_id_unique($cat_id_arr)
{
    $cat_id_arr = array_clean(array_unique($cat_id_arr), true);
    array_unshift($cat_id_arr, 'dummy');
    unset($cat_id_arr[0]);				// make first element starts from 1, because we need to compare current cat_id with old cat_id (which starts from 1)
    for ($i = count($cat_id_arr) + 1; $i <= 6; $i++) {
        $cat_id_arr[$i] = 0;
    }		// populate the rest with 0
    return $cat_id_arr;
}


// create sql for rating (0-5), eg: rating 1.1, will search between 1.0-1.49. Rating 2.5: 2.5-2.99
// $field = field name
// $val = rating value to search
function rating_sql($field, $val)
{
    if (!is_numeric($val) || (($val < 0) || ($val > 5))) {
        return false;
    }
    if (!$val) {
        return false;
    }
    $s = $val - 0.5;
    $e = $val + 0.49;
    if ($s < 0.51) {
        $s = 0.1;
    }
    if ($e > 5) {
        $e = 5;
    }
    return "(($field >= $s) AND ($field <= $e))";
}


// create sql query to cats
function create_cat_where($cat_id, $n = 6, $mode = 'OR')
{
    $where = '';
    for ($i = 1; $i <= $n; $i++) {
        $where .= "(category_$i = '$cat_id') $mode ";
    }
    return '('.substr($where, 0, -4).')';
}


// create thumbnail
// image_id = ITEMID_i, eg 1_1, 1_2, 123_1
// mode = 'list' => medium size, non clickable
//        'detail' => medium size, clickable
//        'feature' => medium size, non clickable
//        'small' => small (50px)
//        'raw' => return actual file name of big image, caution if not exists -> return false
// default_img = return $default_img instead of 'no_image', only for 'list' mode
// alt = image alt text
function make_thumb($image_id, $mode, $default_img = '', $alt = 'image')
{
    global $config, $tpl_section, $in_admin_cp;
    $thumb_size = $config['thumb_size'];
    $quality = $config['thumb_quality'];
    if ($in_admin_cp) {
        $admin = true;
    } else {
        $admin = false;
    }

    $img_fn = "$image_id.jpg";
    ;
    $folder = $config['abs_path'].'/public/listing'; // : './public/products';
    $tolder = $config['abs_path'].'/public/listing_thumb'; // : './public/products_thumbs';
    $img_src_url = $config['site_url']."/public/listing/$img_fn";
    $img_th_url = $config['site_url']."/public/listing_thumb/$img_fn";
    $img_sm_url = $config['site_url']."/public/listing_thumb/small_$img_fn";
    $img_src = "$folder/$img_fn";
    $img_th  = "$tolder/$img_fn";
    $img_sm  = "$tolder/small_$img_fn";

    if (!$default_img) {
        $default_img = "$config[site_url]/$config[skin]/images/nothumb_$mode.png";
    } else {
        $default_img = "$config[site_url]/public/thumb/$default_img";
    }

    if (!file_exists($img_src)) {   // if file not found
        if (($mode == 'feature') || ($mode == 'gallery')) {
            $mode = 'detail';
        }
        if ($mode == 'raw') {
            return false;
        }
        if ($mode == 'newsletter') {
            return '';
        } else {
            return "<img border=\"0\" src=\"$default_img\" title=\"$alt\" alt=\"$alt\" />";
        }
    } else {
        // if thumbnail image not exists -> create it
        if ($mode == 'small') {
            $img_th = $img_sm;
            $size = 50;
        } else {
            $size = 'thumb';
        }
        if (!file_exists($img_th)) {
            image_optimizer($img_src, $img_th, $quality, $size);
        }
    }

    // display it ...
    if (($mode == 'list') || ($mode == 'feature')) {
        $img_txt = "<img border=\"0\" src=\"$img_th_url\" alt=\"$alt\" />";
    } elseif ($mode == 'small') {
        $img_txt = "<img border=\"0\" src=\"$img_sm_url\" alt=\"$alt\" />";
    } elseif ($mode == 'newsletter') {	// for newsletter (need absolute url)
        $img_th = substr($img_th, 5);
        $img_txt = "<img border=\"0\" src=\"$img_th_url\" alt=\"$alt\" />";
    } elseif ($mode == 'detail') {	// for detail.
        $item_id = substr($image_id, 0, (strpos($image_id, '_')));
        $j = strpos($image_id, '_');
        $x = substr($image_id, $j+1);
        $row = array();
        $row['img_txt'] = "<img border=\"0\" src=\"$img_th_url\" alt=\"$alt\" />";
        $row['img_fn'] = $img_src_url;
        $img_txt = quick_tpl($tpl_section['detail_gallery'], $row);
    } else {
        $img_txt = $img_src_url;
    }

    return $img_txt;
}



// get custom field values from DB
// for: list.php & detail.php
function get_custom_field($dir_id, $cf_arr, $item_class, $is_detail = true)
{
    global $db_prefix, $cf_custom_sort, $dir_info, $config;
    $output = $custom = array();
    $site_url = $config['site_url'];
    $ffolder = $site_url.'/public/listing';
    $tfolder = $site_url.'/public/listing_thumb';
    $ifolder = $site_url.'/public/listing';
    $i = 0;

    $cf_custom_sort = $dir_info[$dir_id]['cf_custom_sort'];
    foreach ($dir_info[$dir_id]['cf_define'] as $row) {
        $cid = 'cf_'.$row['idx'];
        $val = $cf_arr[$cid];
        $cf_search = $cid.'='.$val;
        $custom = '';

        switch ($row['cf_type']) {
            case 'textarea':
                if (!empty($val) && $is_detail) {
                    $custom = nl2br($val);
                }
            break;

            case 'file':
                if (!empty($val) && $is_detail) {
                    $custom = "<a href=\"$ffolder/$val\">$val</a>";
                }
            break;

            case 'img':
                if (!empty($val) && $is_detail) {
                    $opt = explode('|', $row['cf_option']);
                    $custom = "<a href=\"$ifolder/$val\" class=\"lightbox\"><img src=\"$tfolder/$val\" alt=\"thumb\" /></a>";
                }
            break;

            case 'rating':
                if (!empty($val)) {
                    if ($is_detail) {
                        $custom = rating_img($val);
                    } else {
                        $custom = rating_img($val, 10);
                    }
                    if ($row['is_searchable']) {
                        $custom = "<a href=\"$site_url/listing_search.php?dir_id=$dir_id&amp;$cf_search\">$custom</a>";
                    }
                }
            break;

            case 'select':
                // $foo = explode ("\r\n", $row['cf_option']);
                // $foo = array_pair ($fii, $foo, 'n/a');
                if ($val) {
                    $fii = safe_send($val, true);
                    $custom = $val;
                    if ($row['is_searchable']) {
                        $custom = "<a href=\"$site_url/listing_search.php?dir_id=$dir_id&amp;$cid=$fii\">$custom</a>";
                    }
                }
            break;

            case 'multi':
                if ($val) {
                    $foo = explode("\r\n", $val);
                    array_shift($foo);
                    array_pop($foo);
                    $custom = implode(', ', $foo);
                    if ($row['is_searchable']) {
                        $fii = array();
                        foreach ($foo as $v) {
                            $k = safe_send($v, true);
                            $fii[] = "<a href=\"$site_url/listing_search.php?dir_id=$dir_id&amp;$cid=$k\">$v</a>";
                        }
                        $custom = implode(', ', $fii);
                    }
                }
            break;

            case 'date':
                if (!empty($val) && $is_detail) {
                    $custom = convert_date($val, 1);
                } elseif (!empty($val) && !$is_detail) {
                    $custom = convert_date($val);
                }
                if ($val && $row['is_searchable']) {
                    $custom = "<a href=\"$site_url/listing_search.php?dir_id=$dir_id&amp;$cf_search\">$custom</a>";
                }
            break;

            case 'time':
                if ($val) {
                    $custom = $val;
                    if ($row['is_searchable']) {
                        $custom = "<a href=\"$site_url/listing_search.php?dir_id=$dir_id&amp;$cf_search\">$custom</a>";
                    }
                }
            break;

            case 'gmap':
                // $custom = "<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$val&amp;zoom=14&amp;size=400x300&amp;sensor=false\" />";
                if (!empty($val) && $is_detail) {
                    $custom = "<iframe width=\"100%\" height=\"350\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" style=\"border:solid 1px #000\" src=\"https://maps.google.com/maps?q=loc:$val&amp;output=embed\"></iframe>";
                }
            break;

            case 'tel':
                if (!empty($val)) {
                    $custom = "<a href=\"tel:$val\">$val</a>";
                }
            break;

            case 'url':
                if (!empty($val)) {
                    $custom = "<a href=\"$val\" target=\"_blank\">$val</a>";
                }
            break;

            case 'video':
                if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $val, $matches)) {
                    $val = '//player.vimeo.com/video/'.$matches[5];
                } elseif (preg_match('~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x', $val, $matches)) {
                    $val = '//www.youtube.com/embed/'.$matches[1];
                } else {
                    $val = '';
                }
                if (!empty($val)) {
                    if ($is_detail) {
                        $custom = "<div class=\"embed-container\"><iframe src=\"$val\" frameborder=\"0\" allowfullscreen></iframe></div>";
                    } else {
                        $custom = "<span class=\"glyphicon glyphicon-film\"></span>";
                    }
                }
            break;

            case 'country':
                if (!empty($val)) {
                    $cty = get_country_list($val);
                    if ($cty) {
                        if ($row['is_searchable']) {
                            $custom = "<a href=\"$site_url/listing_search.php?dir_id=$dir_id&amp;$cf_search\">$cty</a>";
                        } else {
                            $custom = $cty;
                        }
                    }
                }

            break;

            case 'div':
                $val = $row['cf_title'];
                if ($is_detail) {
                    $custom = $val;
                }
            break;

            default:
                if (!empty($val)) {
                    $custom = $val;
                    if ($row['is_searchable']) {
                        $custom = "<a href=\"$site_url/listing_search.php?cfonly=1&amp;dir_id=$dir_id&amp;$cf_search\">$val</a>";
                    }
                }
            break;
        }

        // filter by item class (regular, premium, sponsored)
        if ($row['avail_to_'.$item_class]) {
            // create design (auto)
            $row['value'] = $custom;
            if (empty($row['value'])) {
                unset($custom);
            } else {
                $i++;
                if ($row['is_list'] || $is_detail) {
                    $output[$cid] = array('cf_idx' => $cid, 'cf_title' => $row['cf_title'], 'cf_value' => $custom, 'cf_type' => $row['cf_type'], 'cf_raw' => $val);
                }
            }
        }
    }
    if (!$i) {
        return false;
    } else {
        return $output;
    }
    //echo quick_tpl ($tpl, $output);
}


// process listing info
// $row = the row of listing to be processed (from database)
// $mode = either list or detail
function process_listing_info(&$row, $mode = 'list')
{
    global $config, $lang, $dir_info, $db_prefix, $listing_class_def;

    $item_id = $row['item_id'];
    $dir_id = $row['dir_id'];
    get_dir_info($dir_id);

    // images
    if (file_exists($config['abs_path'].'/public/listing/'.$item_id.'_1.jpg')) {
        $row['image_big'] = '<img src="'.$config['site_url'].'/public/listing/'.$item_id.'_1.jpg'.'" alt="'.$row['item_title'].'" />';
    } else {
        $row['image_big'] = '';
    }
    $row['image'] = make_thumb($item_id.'_1', $mode, '', $row['item_title']);
    $row['image_raw'] = make_thumb($item_id.'_1', 'raw', '', $row['item_title']);
    $row['image_small'] = make_thumb($item_id.'_1', 'small', '', $row['item_title']);

    if ($config['enable_adp'] && $row['item_permalink']) {
        $row['url'] = $row['item_permalink'];
    } else {
        $row['url'] = "detail.php?item_id=$item_id";
    }

    // missing main cat (perhaps because of cat removal?)
    if (empty($row['category_1'])) {
        $dir_info[$dir_id]['cat_structure'][0] = $dir_info[$dir_id]['cat_name_def'][0] = $dir_info[$dir_id]['cat_structure_link'][0] = $dir_info[$dir_id]['cat_url'][0] = '';
        if (!$row['orphaned']) {
            kemana_email($config['site_email'], $item_id, 'orphaned', true, $row);
            sql_query("UPDATE ".$db_prefix."listing SET orphaned='1' WHERE idx='$item_id' LIMIT 1");
        }
    }

    $row['cat_structure'] = $dir_info[$dir_id]['cat_structure'][$row['category_1']];
    $row['dir_name'] = $dir_info[$dir_id]['dir_inf']['dir_title'];
    $row['cat_name'] = $dir_info[$dir_id]['cat_name_def'][$row['category_1']];
    $row['cat_link'] = $dir_info[$dir_id]['cat_structure_link'][$row['category_1']];
    $row['item_details'] = (empty($row['item_details']))? $lang['l_no_description'] : $row['item_details'];
    $row['item_summary'] = $row['item_summary'] ? $row['item_summary'] : line_wrap(strip_tags($row['item_details']), 255);
    $row['item_summary_short'] = line_wrap(strip_tags($row['item_summary']), 80);
    $row['item_details'] = convert_smilies($row['item_details']);
    $row['item_date'] = convert_date($row['item_date']);
    $row['item_details'] = nl2br($row['item_details']);
    $row['item_rating'] = round($row['item_rating'], 1);
    if ($mode == 'list') {
        $row['item_rating_star'] = rating_img($row['item_rating'], 12);
    } else {
        $row['item_rating_star'] = rating_img($row['item_rating'], 20);
    }

    // breadcrumbs
    $breadcat = array();
    $breadcat[] = array('bc_link' => $dir_info[$dir_id]['dir_inf']['url'], 'bc_title' => $dir_info[$dir_id]['dir_inf']['dir_title']);
    $foo = explode(',', $row['category_1']);
    foreach ($foo as $k => $v) {
        $breadcat[] = array('bc_link' => $dir_info[$dir_id]['cat_url'][$v], 'bc_title' => $dir_info[$dir_id]['cat_name_def'][$v]);
    }
    $breaditem = $breadcat;
    $breaditem[] = array('bc_link' => $row['url'], 'bc_title' => $row['item_title']);
    $row['bread_cat'] = $breadcat;
    $row['bread_item'] = $breaditem;

    // item class
    if ($row['item_class'] != 'R') {
        $row['item_valid_date'] = ($row['item_valid_date'] > date('Y-m-d')) ? convert_date($row['item_valid_date']) : '-';
    } else {
        $row['item_valid_date'] = '-';
    }
    return $row;
}


// remove item & its cf & uploaded files
// $item_id = item ID
// $cf_only = true to remove cf only (retaining listing info), false = remove all
// $recount = true to recount number of items per category, false = do not recount (not recommended, unless if you need to remove a directory)
function remove_item($item_id, $cf_only = false, $recount = true, $remove_logo = true, $remove_cf_file = true)
{
    global $config, $db_prefix, $dir_info;

    // delete other attributes
    $old = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$item_id' LIMIT 1");
    if (!$old) {
        return false;
    }

    sql_query("DELETE FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");
    sql_query("DELETE FROM ".$db_prefix."listing WHERE idx='$item_id' LIMIT 1");

    // recount
    if (($old['item_status'] == 'P') && $recount) {
        for ($i = 1; $i <= 6; $i++) {
            recount_num_link($old['dir_id'], $old['category_'.$i], 'dec');
        }
    }

    // permalink
    sql_query("DELETE FROM ".$db_prefix."permalink WHERE target_script='detail.php' AND target_idx='$item_id' LIMIT 1");

    // remove cf files
    if ($remove_cf_file) {
        get_dir_info($old['dir_id']);
        $dir_inf = $dir_info[$old['dir_id']];

        foreach ($dir_inf['cf_define'] as $k => $v) {
            $cf_id = 'cf_'.$v['idx'];
            if (($v['cf_type'] == 'img') && (!empty($old[$cf_id]))) {
                @unlink($config['abs_path'].'/public/listing/'.$old[$cf_id]);
                @unlink($config['abs_path'].'/public/listing_thumb/'.$old[$cf_id]);
            }

            if (($v['cf_type'] == 'file') && (!empty($old[$cf_id]))) {
                @unlink($config['abs_path'].'/public/file/'.$old[$cf_id]);
            }
        }
    }

    // remove images
    if ($remove_logo) {
        $fn = $item_id.'_1.jpg';
        @unlink($config['abs_path'].'/public/listing/'.$fn);
        @unlink($config['abs_path'].'/public/listing_thumb/'.$fn);
        @unlink($config['abs_path'].'/public/listing_thumb/small_'.$fn);
    }

    qcache_clear('dir_'.$old['dir_id'].'.cat_num_link', false);
    return true;
}


// to send email for kemana
// $target = recipient email address
// $item_id = listing item id
// $mode = mode of email template
// $send = true: send the email, false: return body as string
// $data = data, leave empty to fetch from db, or array of data to override fetched db
function kemana_email($target, $item_id, $mode, $send = true, $data = array())
{
    global $db_prefix, $dir_info, $lang, $config, $isLogin;

    $row = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$item_id' LIMIT 1");
    $row = array_merge($row, $data);

    get_dir_info($row['dir_id']);
    $dir_inf = $dir_info[$row['dir_id']];

    // optional fields
    $optional_fields = '';
    if ($dir_inf['dir_inf']['dir_url']) {
        $optional_fields .= "Target URL: $row[item_url]<br />";
    }
    if ($dir_inf['dir_inf']['dir_backlink']) {
        $optional_fields .= "Backlink URL: $row[item_backlink_url]<br />";
    }
    if ($dir_inf['dir_inf']['dir_summary'] && empty($item_summary)) {
        $optional_fields .= "Summary: $row[item_summary]<br />";
    }

    // custom fields
    $cf_email = '';
    foreach ($dir_inf['cf_define'] as $k => $v) {
        $cf_id = 'cf_'.$v['idx'];
        if (!empty($row[$cf_id]) && ($v['cf_type'] != 'div')) {
            $cf_email .= $v['cf_title'].': '.$row[$cf_id].'<br />';
        }
    }
    $row['dir_title'] = $dir_inf['dir_inf']['dir_title'];
    $row['site_url'] = $config['site_url'];
    $row['site_name'] = $config['site_name'];
    $row['optional_fields'] = $optional_fields;
    $row['cf_email'] = $cf_email;

    // send email based on $mode
    switch ($mode) {
        case 'inform_e':
            $mail_body_adm = quick_tpl(load_tpl('mail', 'add_inform_adm'), $row);
            if ($send) {
                email($config['site_email'], '['.$config['site_name'].'] '.sprintf($lang['l_mail_add_subject_adm'], $row['item_title']), $mail_body_adm, true, true);
            }

            $mail_body_usr = quick_tpl(load_tpl('mail', 'add_inform_usr'), $row);
            $subject = '['.$config['site_name'].'] '.sprintf($lang['l_mail_add_subject'], $row['item_title']);
        break;


        case 'lost':
            $mail_body_usr = quick_tpl(load_tpl('mail', 'lost_edit'), $row);
            $subject = '['.$config['site_name'].'] '.sprintf($lang['l_mail_lost_edit_subject'], $row['item_title']);
        break;


        default:
            $etpl_def = array('confirm_t' => 'add_confirm_usr', 'status_p' => 'item_status_p', 'status_e' => 'item_status_e', 'status_x' => 'item_status_x', 'update_e' => 'change_e', 'orphaned' => 'item_orphaned');
            $etpl = $etpl_def[$mode];
            $mail_body_usr = quick_tpl(load_tpl('mail', $etpl), $row);
            $subject = '['.$config['site_name'].'] '.sprintf($lang['l_mail_add_subject'], $row['item_title']);
        break;
    }

    if ($send) {
        email($target, $subject, $mail_body_usr, true);
    } else {
        return $mail_body_usr;
    }

    return true;
}


// verify listing owner & existance
// $item_id = item ID
// $user_id = user id, or empty if if the item should belongs to a guest (no owner id), or '*' for auto (if guest, requires session password, if not guest, owner_id must be matched)
// return $row
function verify_owner($item_id, $user_id = '')
{
    global $db_prefix, $lang, $isLogin, $current_user_id;
    $row = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$item_id' LIMIT 1");
    if (!$row) {
        msg_die($lang['msg']['edit_item_not_found']);
    }
    if ($row['item_status'] == 'T') {
        msg_die($lang['msg']['edit_item_not_found']);
    }
    if (($user_id != '*') && ($row['owner_id'] != $user_id)) {
        msg_die($lang['msg']['edit_item_not_found']);
    }
    if (!$user_id && ($row['owner_id'])) {
        msg_die($lang['msg']['edit_item_not_found']);
    }

    if ($user_id == '*') {
        // auto
        if (!$isLogin) {
            // - when not login, get edit passwd from session, or die!
            $pwd = ip_config_value('edit_passwd');
            if ($pwd) {
                $foo = explode(':', $pwd);
                if (($foo[0] != $item_id) || (qhash($foo[1]) != $row['owner_passwd']) || (!empty($row['owner_id']))) {
                    ip_config_update('edit_passwd', 0);
                    msg_die($lang['msg']['edit_item_not_found']);
                }
            } else {
                msg_die($lang['msg']['edit_item_not_found']);
            }
        } else {
            // - is login, make sure owner_id same with current login id
            if ($row['owner_id'] != $current_user_id) {
                msg_die($lang['msg']['edit_item_not_found']);
            }
        }
    }

    return $row;
}


// function to upgrade an item to selected class (premium or sponsored)
// i made this as a function as it is also used in PP IPN auto upgrade (or may be future payment module)
// $order = array of order
function upgrade_item($order)
{
    global $db_prefix, $sql_today;
    sql_query("UPDATE ".$db_prefix."order SET order_status='C', order_completed='$sql_today', order_cancelled='' WHERE idx='$order[idx]' LIMIT 1");

    //
    $item = sql_qquery("SELECT * FROM ".$db_prefix."listing WHERE idx='$order[item_id]' LIMIT 1");

    // update listing
    if ($item['item_class'] == $order['target_class']) {
        $today = $item['item_valid_date'];
    } else {
        $today = $sql_today;
    }
    $valid_date = convert_date($today, 'sql', $order['item_period'] * 30);
    return sql_query("UPDATE ".$db_prefix."listing SET item_class='$order[target_class]', item_valid_date='$valid_date' WHERE idx='$order[item_id]' LIMIT 1");
}


// get child cats from a cat
// ps. we can use mysql query, but that would be the purpose of caching
// $everything = true to get all descendant (children, grand children, great grand children, ...); false: only get children
// returns array of child (flat array)
function get_cat_child($dir_id, $cat_id, $everything = false)
{
    global $dir_info;
    get_dir_info($dir_id);
    $list = array();

    // get current structure, eg: 1,2,
    $me = $dir_info[$dir_id]['cat_structure_id'][$cat_id].',';
    $l = strlen($me);
    $cc = substr_count($me, ',');

    // find any other structure having: 1,2,nnn; but not 1,2,nnn,3
    foreach ($dir_info[$dir_id]['cat_structure_id'] as $k => $v) {
        if (substr($v, 0, $l) == $me) {
            if ($everything) {
                $list[] = $k;
            } else {
                if (substr_count($v, ',') == $cc) {
                    $list[] = $k;
                }
            }
        }
    }

    return $list;
}


// create list of cats under a specific dir & cat
// $dir_id = dir ID
// $cat_id = cat ID, if empty, returns top cats for dir ID
// returns array of necessary contents for building cat_list (see listing_search.php & index.php)
// *) returns stored in cache
function create_cat_list($dir_id, $cat_id = 0)
{
    global $dir_info, $db_prefix, $config;
    get_dir_info($dir_id);
    $list = array();

    $output = qcache_get("catlist_$dir_id.$cat_id", false);
    if (!$output) {
        if ($cat_id) {
            $list = get_cat_child($dir_id, $cat_id);
        } else {
            // if no 'cat_id', get top cats
            $list = array_keys($dir_info[$dir_id]['cat_structure_top']);
        }

        // default thumb
        $dir_default_img = $dir_info[$dir_id]['dir_inf']['dir_image'];
        if (!$dir_default_img) {
            $dir_default_img = "$config[site_url]/$config[skin]/images/nothumb_small.png";
        } else {
            $dir_default_img = "$config[site_url]/public/thumb/$dir_default_img";
        }

        // get its children & grand children
        foreach ($list as $child) {
            $row = array();
            $img = sql_qquery("SELECT cat_image FROM ".$db_prefix."listing_cat WHERE idx='$child' LIMIT 1");


            $row['cat_image'] = $img['cat_image'] ? $config['site_url'].'/public/image/'.$img['cat_image'] : $dir_default_img;
            $row['cat_url'] = $dir_info[$dir_id]['cat_url'][$child];
            $row['cat_name'] = '<a href="'.$dir_info[$dir_id]['cat_url'][$child].'">'.$dir_info[$dir_id]['cat_name_def'][$child].'</a>';
            $row['cat_num_link'] = num_format($dir_info[$dir_id]['cat_num_link'][$child]);

            // create list of sub cats
            $row['cat_sub_list'] = "<ul>\n";
            $c = 0;

            $grand = get_cat_child($dir_id, $child);
            foreach ($grand as $k => $v) {
                if ($c < 3) {
                    $row['cat_sub_list'] .= '<li><a href="'.$dir_info[$dir_id]['cat_url'][$v].'">'.$dir_info[$dir_id]['cat_name_def'][$v].'</a></li>';
                } elseif ($c == 3) {
                    $row['cat_sub_list'] .= '&hellip;';
                }
                $c++;
            }
            $row['cat_sub_list'] .= "</ul>\n";
            $output[] = $row;
        }

        qcache_update("catlist_$dir_id.$cat_id", serialize($output), false);
    } else {
        $output = unserialize($output);
    }
    if (!is_array($output)) {
        $output = array();
    }
    return $output;
}


// verify selected values for multi select & select CF, it automatically removes invalid options.
// $selected = string or array of selected values (* safe_send'd *)
// $options = string or array of available options. If string, each options must be separated by \r\n (* plain text *)
// returns = string or array of verified value(s). If no valid selected value, returns false. (* plain text *)
function verify_selected($selected, $options)
{
    $is_arr = true;
    if (!is_array($selected)) {
        $selected = array($selected);
        $is_arr = false;
    }
    if (!is_array($options)) {
        $options = explode("\r\n", $options);
    }

    // convert options to safe_send as keys
    $opts = safe_send($options, true);
    $foo = array_pair($opts, $options);

    // eliminate mismatched values
    $verified = array();
    foreach ($selected as $k => $v) {
        $vv = str_replace('=', '%3D', $v);
        if (array_key_exists($vv, $foo)) {
            $verified[] = $foo[$vv];
        }
    }

    if (empty($verified)) {
        return false;
    }

    if (!$is_arr) {
        return $verified[0];
    } else {
        return $verified;
    }
}


// verify a URL for backlink code
// $url = the url containing backlink
// returns true if the code found, otherwise return false
function verify_backlink($url)
{
    global $config;
    if (!$url) {
        return false;
    }

    $foo = @remote_fopen($url);
    if (!$foo) {
        return false;
    }
    $tgt = '.'.preg_replace('/[ \t\s*$^\s*]+/', '', $foo);	// remove spaces & new lines from page & backlink code to simplify comparisons
    $cod = preg_replace('/[ \t\s*$^\s*]+/', '', html_unentities($config['ke']['backlink_code']));
    $x = strpos($cod, '?[user_id]');	// we don't need [user_id] information
    $cod1 = substr($cod, 0, $x);
    $cod2 = substr($cod, $x + 10);
    if (strpos($tgt, $cod1) && strpos($tgt, $cod2)) {
        return true;
    } else {
        return false;
    }
}


function create_search_cache($item_id)
{
    global $db_prefix, $dir_info;
    $row = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE item_id='$item_id' LIMIT 1");

    // cf type to store as smart cache
    $cfsc = array('varchar', 'textarea', 'country', 'select', 'multi');
    $cfsc_str = '';
    get_dir_info($row['dir_id']);
    foreach ($dir_info[$row['dir_id']]['cf_define'] as $k => $v) {
        if (in_array($v['cf_type'], $cfsc) && ($v['is_searchable'])) {
            $cfsc_str .= $row[$k].' ';
        }
    }

    // search_cache
    $string = $row['item_title'].' '.$row['item_summary'].' '.$row['item_details'].' '.$cfsc_str;
    $string = str_replace('&#039;', '\'', $string);
    $string = preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($string));	// remove non alphanumeric, but keep UTF-8 chars
    $string = preg_replace('!\s+!', ' ', $string);								// replace tab & spaces as single space
    $string = addslashes(implode(' ', array_unique(explode(' ', $string))));// unique
    sql_query("UPDATE ".$db_prefix."listing SET smart_search='$string' WHERE idx='$row[item_id]' LIMIT 1");
}




/******************************************************************

 LOCAL DEFINITIONS

******************************************************************/

// local vars
$lang['edit_listing_in_acp'] = "<div class=\"edit_in_acp\"><a href=\"$config[site_url]/$config[admin_folder]/listing.php?cmd=edit&amp;item_id=%s\" target=\"acp\" class=\"btn btn-xs btn-default\">Edit Listing</a></div>";
$lang['edit_listing_cat_in_acp'] = "<div class=\"edit_in_acp\"><a href=\"$config[site_url]/$config[admin_folder]/menu_man.php?cmd=design&amp;midx=%s\" target=\"acp\" class=\"btn btn-xs btn-default\">Edit Category</a></div>";
$lang['S_label'] = $lang['l_sponsored'];
$lang['P_label'] = $lang['l_premium'];
$lang['R_label'] = $lang['l_regular'];

$listing_status_def = array('P' => 'Published',
    'E' => 'Pending',
    'T' => 'Waiting User Confirmation');
$listing_class_def = array('R' => 'Regular',
    'P' => 'Premium',
    'S' => 'Sponsored');
$listing_visible_def = array('A' => 'Normal (Everybody)',
    'M' => 'Members Only',
    'H' => 'Hidden (Requires URL)');

// detail icons
$listing_visible_icon = array('A' => '',
    'M' => '<span class="glyphicon glyphicon-lock"></span>',
    'H' => '<span class="glyphicon glyphicon-eye-close"></span>');

// Payment status
$payment_status_def = array('E' => 'Pending',
    'P' => 'Paid/Approved',
    'X' => 'Failed/Denied');

// Order ststus
$order_status_def = array('E' => 'Order Received',		// queued (order just accepted by server, initial status!)
    'C' => 'Completed',									// all done
    'X' => 'Denied');									// cancelled (e.g fraud)

// product search sort
$search_sort = array(
    'xd' => $lang['l_default'],
    'ta' => $lang['l_title_asc'],
    'td' => $lang['l_title_dsc'],
    'da' => $lang['l_date_asc'],
    'dd' => $lang['l_date_dsc'],
    'ra' => $lang['l_rate_asc'],
    'rd' => $lang['l_rate_dsc']);

// list mode
$list_mode = array('list' => $lang['l_list'],
    'grid' => $lang['l_grid']);

// item class css
$list_class = array('R' => 'list_regular',
    'P' => 'list_premium',
    'S' => 'list_sponsored');


/******************************************************************

 LOCAL INIT

******************************************************************/

get_dir_info();
