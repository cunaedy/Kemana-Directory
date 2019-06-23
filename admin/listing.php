<?php
// compare difference between old & new values & cf values
// $old_value = array of original item & cf values
// $new_value = array of new (request) item & cf values
// $dir_id
// returns array of <tr><td>{title}</td><td>{old val}</td><td>{new_val}</td></tr> & class & label for different values only
function mark_fields($old_value, $new_value, $dir_id)
{
    global $dir_info;
    $ifolder = './../public/listing/';
    $tfolder = './../public/listing_thumb/';
    $cifolder = './../public/listing/';
    $ctfolder = './../public/listing_thumb/';
    $cffolder = './../public/listing/';

    $tocompare = array('category_1', 'category_2', 'category_3', 'category_4', 'category_5', 'category_6', 'item_title', 'item_logo', 'item_summary', 'item_url', 'item_url_mask', 'item_details');
    $tolabel   = array('Category 1', 'Category 2', 'Category 3', 'Category 4', 'Category 5', 'Category 6', 'Title', 'Logo', 'Summary', 'Target URL', 'URL Masking', 'Details');
    foreach ($old_value as $k => $v) {
        if (substr($k, 0, 3) == 'cf_') {
            $tocompare[] = $k;
            if (!empty($dir_info[$dir_id]['cf_define'][$k]['cf_title'])) {
                $tolabel[] = $dir_info[$dir_id]['cf_define'][$k]['cf_title'];
            } else {
                $tolabel[] = 'Removed CF';
            }
        }
    }

    // return dummy
    if (empty($new_value)) {
        foreach ($tocompare as $k => $v) {
            $result[$v.'_mark'] = $result[$v.'_class'] = '';
        }
        return $result;
    }

    // logo comparisons
    $old_value['item_logo'] = 0;
    $new_value['item_logo'] = 0;
    $ologo = file_exists($ifolder.'/'.$old_value['item_id'].'_1.jpg');
    $nlogo = file_exists($ifolder.'/'.$new_value['item_id'].'_1.jpg');

    if (($ologo && !$nlogo) || (!$ologo && $nlogo)) {
        $old_value['item_logo'] = $old_value['item_id'];
        $new_value['item_logo'] = $new_value['item_id'];
    }

    if ($ologo && $nlogo) {
        $c1 = filesize($ifolder.'/'.$old_value['item_id'].'_1.jpg');
        $c2 = filesize($ifolder.'/'.$new_value['item_id'].'_1.jpg');
        if ($c1 != $c2) {
            $old_value['item_logo'] = $old_value['item_id'];
            $new_value['item_logo'] = $new_value['item_id'];
        }
    }

    // compare
    $result = array();
    $result['compare'] = '';
    foreach ($tocompare as $k => $v) {
        $ori = $old_value[$v];
        $cur = $new_value[$v];
        if ($ori != $cur) {
            $result[$v.'_mark'] = "<span class=\"helpred tips\" title=\"Value changed, see 'Compare' tab\"><span class=\"glyphicon glyphicon-exclamation-sign help\"></span></span>";
            $result[$v.'_class'] = 'class="danger"';

            // logo
            if ($v == 'item_logo') {
                if ($ologo) {
                    $ori = "<a href=\"$ifolder/{$ori}_1.jpg\" class=\"lightbox\"><img src=\"$tfolder/{$ori}_1.jpg\" alt=\"thumb\" /></a>";
                } else {
                    $ori = '';
                }

                if ($nlogo) {
                    $cur = "<a href=\"$ifolder/{$cur}_1.jpg\" class=\"lightbox\"><img src=\"$tfolder/{$cur}_1.jpg\" alt=\"thumb\" /></a>";
                } else {
                    $cur = '';
                }
            }

            // cf
            if (substr($v, 0, 3) == 'cf_') {
                // - cf image
                if ($dir_info[$dir_id]['cf_define'][$v]['cf_type'] == 'img') {
                    if ($ori) {
                        $ori = "<a href=\"$cifolder/$ori\" class=\"lightbox\"><img src=\"$ctfolder/$ori\" alt=\"thumb\" /></a>";
                    } else {
                        $ori = '';
                    }

                    if ($cur) {
                        $cur = "<a href=\"$cifolder/$cur\" class=\"lightbox\"><img src=\"$ctfolder/$cur\" alt=\"thumb\" /></a>";
                    } else {
                        $cur = '';
                    }
                }

                // - cf file
                if ($dir_info[$dir_id]['cf_define'][$v]['cf_type'] == 'file') {
                    if ($ori) {
                        $ori = "<a href=\"$cffolder/$ori\">$ori</a>";
                    } else {
                        $ori = '';
                    }

                    if ($cur) {
                        $cur = "<a href=\"$cffolder/$cur\">$cur</a>";
                    } else {
                        $cur = '';
                    }
                }
            }
            $result['compare'] .= "<tr><td>$tolabel[$k]</td><td>$ori</td><td>$cur</td>\n";
        } else {
            $result[$v.'_mark'] = $result[$v.'_class'] = '';
        }
    }
    return $result;
}


// format cf value
// $dir_id = dir_id
// $cf_val = array of item_info & raw cf_value
function get_cf($dir_id, $cf_val)
{
    global $db_prefix, $tpl_section, $rating_def, $cf_custom_sort, $lang, $dir_info, $sql_today;

    $ffolder = './../public/listing';
    $ifolder = './../public/listing';
    $tfolder = './../public/listing_thumb';
    $axsrf = AXSRF_value();

    $output = array();

    foreach ($dir_info[$dir_id]['cf_define'] as $row) {
        if ($row['cf_help']) {
            $row['cf_help'] = '<span class="glyphicon glyphicon-info-sign help tips" title="'.$row['cf_help'].'"></span>';
        }
        $key = 'cf_'.$row['idx'];
        $val = isset($cf_val[$key]) ? $cf_val[$key] : '';

        switch ($row['cf_type']) {
            case 'varchar':
            case 'tel':
            case 'email':
            case 'url':
                $field = "<input type=\"text\" name=\"$key\" size=\"50\" value=\"$val\" />";
            break;

            case 'video':
                $field = "<input type=\"text\" name=\"$key\" size=\"50\" value=\"$val\" placeholder=\"Paste a video URL from Youtube or Vimeo\" /> ";
                if ($val) {
                    $field .= "<a href=\"$val\" target=\"_blank\"><span class=\"glyphicon glyphicon-film\"></span></a>";
                }
            break;

            case 'textarea':
                $field = "<textarea name=\"$key\" cols=\"50\" rows=\"5\">$val</textarea>";
            break;

            case 'file':
                if (empty($val)) {
                    $field = "<input type=\"file\" name=\"$key\" size=\"37\" />";
                } else {
                    $fs = num_format(filesize("$ffolder/$val") / 1024);
                    $field = "<a href=\"$ffolder/$val\">$val</a> ($fs KB)<br /><a href=\"listing.php?cmd=del_cf&amp;cf_id=$key&amp;item_id=$cf_val[item_id]&amp;AXSRF_token=$axsrf\"><span class=\"glyphicon glyphicon-remove\"></span> Remove</a>";
                }
            break;

            case 'img':
                if (empty($val)) {
                    $field = "<input type=\"file\" name=\"$key\" />";
                } else {
                    $field = "<a href=\"$ifolder/$val\" class=\"lightbox\"><img src=\"$tfolder/$val\" alt=\"thumb\" /></a><br /><a href=\"listing.php?cmd=del_cf&amp;cf_id=$key&amp;item_id=$cf_val[item_id]&amp;AXSRF_token=$axsrf\"><span class=\"glyphicon glyphicon-remove\"></span> Remove</a>";
                }
            break;

            case 'select':
                $foo = explode("\r\n", $row['cf_option']);
                $foo = array_pair($foo, $foo, 'n/a');
                $field = create_select_form($key, $foo, $val);
            break;

            case 'multi':
                // selected vals
                $val = explode("\r\n", $val);
                $val = safe_send($val, true);

                $foo = explode("\r\n", $row['cf_option']);
                $fii = safe_send($foo, true);
                $foo = array_pair($fii, $foo);
                $field = create_checkbox_form($key, $foo, $val, 3);
            break;

            case 'country':
                $clist = array();
                $field = create_select_form($key, get_country_list(), $val, '&nbsp;');
            break;

            case 'date':
                $fn = 'date_'.$key;
                if (empty($val)) {
                    $val = $sql_today;
                }
                $field = date_form($fn, 1, 1, 1, $val)." <a style=\"cursor:pointer\"><span class=\"glyphicon glyphicon-calendar calendar\" id=\"cal_$fn\" data-date-format=\"yyyy-mm-dd\" data-date=\"$val\"></span></a>
				<script>var cal=$('#cal_$fn').datepicker().on('changeDate',function(ev){update_date_form('$fn',ev.date);$('#cal_$fn').datepicker('hide')});</script>";
            break;

            case 'time':
                $fn = 'time_'.$key;
                $field = time_form($fn, $val);
            break;

            case 'rating':
                $field = create_select_form($key, $rating_def, $val);
            break;

            case 'gmap':
                $field = "<input type=\"text\" name=\"$key\" id=\"$key\" size=\"50\" value=\"$val\" class=\"width-md\"/> <a href=\"../gmap_picker.php?cmd=picker&amp;mode=latlon1&amp;fid=$key&amp;latlon=$val\" class=\"popiframe_sp\">Locate</a>";
            break;

            case 'div':
                $field = '<b>'.$row['cf_title'].'</b>';
            break;
        }

        $row['cf_field'] = $field;
        if ($row['is_required']) {
            $row['cf_field'] .= ' '.$lang['l_required_symbol'];
        }

        $row['cf_class'] = isset($cf_val[$key.'_class']) ? $cf_val[$key.'_class'] : '';
        $row['cf_mark'] = isset($cf_val[$key.'_mark']) ? $cf_val[$key.'_mark'] : '';
        $output[] = quick_tpl($tpl_section['cf_list'], $row);
    }

    return implode($output, "\n");
}


require './../includes/admin_init.php';
admin_check(4);

$cmd = get_param('cmd');
$item_id = get_param('item_id');

// def
$send_email_def = array(1 => 'Yes, send automatically', 0 => 'No, don\'t send any emails', 2 => 'Send manually');

switch ($cmd) {
    case 'del_cf':
        AXSRF_check();

        $cf_id = substr(get_param('cf_id'), 3);
        $field = 'cf_'.$cf_id;

        $cf_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_define WHERE idx='$cf_id' LIMIT 1");
        if (!$cf_inf) {
            die('Invalid CF ID!');
        }

        $cf_val = sql_qquery("SELECT $field FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");
        $val = $cf_val[$field];

        // delete file
        if (($cf_inf['cf_type'] == 'img') || ($cf_inf['cf_type'] == 'file') && (!empty($val))) {
            unlink('./../public/listing/'.$val);
        }
        if (($cf_inf['cf_type'] == 'img') && (!empty($val))) {
            unlink('./../public/listing_thumb/'.$val);
        }

        // update db
        $cf_val = sql_qquery("UPDATE ".$db_prefix."listing_cf_value SET $field='' WHERE item_id='$item_id' LIMIT 1");

        // clear cache
        qcache_clear();
        redir();
    break;


    case 'del_item':
        AXSRF_check();
        $email = get_param('email');
        $row = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$item_id' LIMIT 1");
        if ($email) {
            kemana_email($row['owner_email'], $item_id, 'status_x', true, $row);
        }

        remove_item($item_id);
        admin_die('admin_ok', $config['site_url'].'/{$l_admin_folder}/listing.php');
    break;


    case 'del_img':
        AXSRF_check();
        $fn = $item_id.'_1.jpg';
        $folder = $config['abs_path'].'/public/listing';
        $tolder = $config['abs_path'].'/public/listing_thumb';
        @unlink("$folder/$fn");
        @unlink("$tolder/$fn");
        @unlink("$tolder/small_$fn.jpg");
        admin_die('admin_ok');
    break;


    case 'edit':
        $row = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$item_id' LIMIT 1");
        if (!$row) {
            admin_die('<h1>Not Found</h1><p>Item not found!</p>');
        }
        $dir_id = $row['dir_id'];

        // get dir info
        get_dir_info($dir_id);
        $dir_inf = $dir_info[$dir_id]['dir_inf'];

        // template
        $allow_logo_empty = $allow_logo_exists = $duplicate_url = $change_request = $allow_url_mask = false;
        $allow_logo = $dir_inf['dir_logo'];
        $require_url = $dir_inf['dir_url'];
        $require_backlink = $dir_inf['dir_backlink'];
        $require_summary = $dir_inf['dir_summary'];
        $allow_url_mask = $dir_inf['dir_url_mask'];

        if ($allow_logo) {
            if (file_exists($config['abs_path'].'/public/listing/'.$item_id.'_1.jpg')) {
                $row['logo'] = "<a href=\"$config[site_url]/public/listing/".$item_id."_1.jpg\" class=\"lightbox\"><img border=\"0\" src=\"$config[site_url]/public/listing_thumb/".$item_id."_1.jpg\" /></a>";
                $allow_logo_exists = true;
            } else {
                $row['logo'] = "<img border=\"0\" src=\"../skins/_common/images/noimage.gif\" alt=\"No thumbnail\" />";
                $allow_logo_empty = true;
            }
        }
        $tpl = load_tpl('adm', 'listing.tpl');

        // get see also
        $i = 0;
        $foo = explode(',', $row['see_also']);
        $mm = array();
        if ($row['see_also']) {
            foreach ($foo as $k => $v) {
                $i++;
                $mem = sql_qquery("SELECT idx, item_title FROM ".$db_prefix."listing WHERE idx='$v' LIMIT 1");
                $mm[] = array('id' => $mem['idx'], 'name' => $mem['item_title']);
            }
        }
        $row['see_also_preset'] = $i ? json_encode($mm) : 'null';

        // duplicate url?
        $row['url_status'] = '';
        if ($require_url) {
            $dup = sql_qquery("SELECT idx, item_title FROM ".$db_prefix."listing WHERE (item_url='$row[item_url]') AND (idx != '$item_id') AND (idx != '$row[original_idx]') LIMIT 1");
            if ($dup) {
                $row['url_status'] = '<a href="listing.php?cmd=edit&amp;item_id='.$dup['idx'].'" class="text-danger">Possible duplicate URL of item ID '.$dup['idx'].' - '.$dup['item_title'].'</a>';
                $duplicate_url = true;
            }
        }

        // change request?
        if ($row['original_idx']) {
            $ori = sql_qquery("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$row[original_idx]' LIMIT 1");
            $row['change'] = "<span class=\"bg-info small\"><span class=\"glyphicon glyphicon-transfer\"></span> <a href=\"listing.php?cmd=edit&amp;item_id=$row[original_idx]\">Original item is $row[original_idx] - $ori[item_title]</a></span>";

            // - translate cats to string
            $new = $row;
            for ($i = 1; $i <= 6; $i++) {
                if ($new['category_'.$i]) {
                    $new['category_'.$i] = $dir_info[$row['dir_id']]['cat_structure'][$new['category_'.$i]];
                }
                if ($ori['category_'.$i]) {
                    $ori['category_'.$i] = $dir_info[$row['dir_id']]['cat_structure'][$ori['category_'.$i]];
                }
            }
            $chg = mark_fields($ori, $new, $dir_id);
            $row = array_merge($row, $chg);

            // - rename status option
            $change_request = true;
            $listing_status_def['P'] = 'Approve &amp; Publish';
            unset($listing_status_def['T']);
        } else {
            $row['change'] = $row['compare'] = '';
            $req = sql_qquery("SELECT idx, item_title FROM ".$db_prefix."listing WHERE original_idx='$item_id' LIMIT 1");
            if ($req) {
                $row['change'] = "<span class=\"bg-info small\"><span class=\"glyphicon glyphicon-transfer\"></span> <a href=\"listing.php?cmd=edit&amp;item_id=$req[idx]\">An update request has been made in $req[idx] - $req[item_title]</a></span>";
            }
            $chg = mark_fields($row, array(), $dir_id);
            $row = array_merge($row, $chg);

            // - remove 'T'
            if ($row['item_status'] != 'T') {
                unset($listing_status_def['T']);
            }
        }

        // cats
        if ($row['orphaned'] || !$row['category_1']) {
            $row['category_1_class'] = 'class="danger"';
            $row['category_form'] = create_select_form('category_1', $dir_info[$row['dir_id']]['cat_structure'], $row['category_1'], '(Orphaned)');
        } else {
            $row['category_form'] = create_select_form('category_1', $dir_info[$row['dir_id']]['cat_structure'], $row['category_1']);
        }
        $row['block_multi_cat'] = '';
        for ($i = 2; $i <= $dir_inf['dir_multi_cat']; $i++) {
            $foo['i'] = $i;
            $foo['category_class'] = $row['category_'.$i.'_class'];
            $foo['category_mark'] = $row['category_'.$i.'_mark'];
            $foo['category_form'] = create_select_form('category_'.$i, $dir_info[$row['dir_id']]['cat_structure'], $row['category_'.$i], '&nbsp;');
            $row['block_multi_cat'] .= quick_tpl($tpl_block['multi_cat'], $foo);
        }

        // send email option
        if ($row['owner_id'] == $current_user_id) {
            $row['email_select'] = create_select_form('send_email', $send_email_def, 0);
        } else {
            $row['email_select'] = create_select_form('send_email', $send_email_def, 1);
        }

        $row['dir_id'] = $dir_id;
        $row['dir_title'] = $dir_inf['dir_title'];
        $row['item_id'] = $item_id;
        $row['list_date'] = date_form('item_date', date('Y'), 1, 1, $row['item_date']);
        $row['sp_date'] = date_form('item_valid_date', date('Y'), 1, 1, $row['item_valid_date']);
        $row['block_thumb'] = $row['digi_check'] = $row['digital'] = $row['critical'] = '';
        $row['cf_form'] = get_cf($dir_id, $row);
        $row['owner_passwd'] = '';
        $row['preview_url'] = $config['site_url'].'/detail.php?item_id='.$item_id;
        $row['status_select'] = create_select_form('item_status', $listing_status_def, $row['item_status']);
        $row['class_select'] = create_select_form('item_class', $listing_class_def, $row['item_class']);
        $row['visibility_select'] = create_select_form('item_visibility', $listing_visible_def, $row['item_visibility']);
        $row['axsrf'] = AXSRF_value();
        $txt['main_body'] = quick_tpl(load_tpl('adm', 'listing.tpl'), $row);
        flush_tpl('adm');
    break;


    default:
        // dir_id must be selected!
        $dir_id = get_param('dir_id');
        if (!$dir_id) {
            redir($config['site_url'].'/{$l_admin_folder}/listing_dir_select.php?what=item');
        }

        // get dir info
        get_dir_info($dir_id);
        $dir_inf = $dir_info[$dir_id]['dir_inf'];

        // template
        $allow_logo_empty = $allow_logo_exists = $duplicate_url = $change_request = $allow_url_mask = false;
        $allow_logo = $dir_inf['dir_logo'];
        $require_url = $dir_inf['dir_url'];
        $allow_url_mask = $dir_inf['dir_url_mask'];
        $require_backlink = $dir_inf['dir_backlink'];
        $require_summary = $dir_inf['dir_summary'];
        if ($allow_logo) {
            $allow_logo_empty = true;
        }
        $tpl = load_tpl('adm', 'listing.tpl');
        $foo = load_form('listing');
        if (!empty($foo)) {
            $row = $foo;
        } else {
            $row = create_blank_tbl($db_prefix.'listing');
        }

        // empties
        unset($listing_status_def['T']);
        $chg = mark_fields($row, array(), $dir_id);
        $row = array_merge($row, $chg);
        $row['change'] = $row['compare'] = '';

        // cats
        $row['category_form'] = create_select_form('category_1', $dir_info[$dir_id]['cat_structure'], $row['category_1']);
        $row['block_multi_cat'] = '';
        for ($i = 2; $i <= $dir_inf['dir_multi_cat']; $i++) {
            $foo['i'] = $i;
            $foo['category_class'] = $row['category_'.$i.'_class'];
            $foo['category_mark'] = $row['category_'.$i.'_mark'];
            $foo['category_form'] = create_select_form('category_'.$i, $dir_info[$dir_id]['cat_structure'], $row['category_'.$i], '&nbsp;');
            $row['block_multi_cat'] .= quick_tpl($tpl_block['multi_cat'], $foo);
        }

        if (empty($row['owner_id'])) {
            $row['owner_id'] = $current_user_id;
        }
        if (empty($row['owner_email'])) {
            $row['owner_email'] = $current_user_info['user_email'];
        }
        $row['dir_id'] = $dir_id;
        $row['dir_title'] = $dir_inf['dir_title'];
        $row['logo'] = $row['email_select'] = $row['item_id'] = '';
        $row['list_date'] = date_form('item_date', date('Y'), 1, 1, $sql_today);
        $row['sp_date'] = date_form('item_valid_date', date('Y'), 1, 1, $sql_today);
        $row['block_thumb'] = $row['digi_check'] = $row['digital'] = $row['critical'] = '';
        $row['see_also_preset'] = 'null';
        $row['cf_form'] = get_cf($dir_id, array());
        $row['preview_url'] = '#';
        $row['status_select'] = create_select_form('item_status', $listing_status_def, 'E');
        $row['class_select'] = create_select_form('item_class', $listing_class_def);
        $row['visibility_select'] = create_select_form('item_visibility', $listing_visible_def);
        $row['axsrf'] = AXSRF_value();
        $txt['main_body'] = quick_tpl($tpl, $row);
        flush_tpl('adm');
    break;
}
