<?php
// create custom SQL
// for: admin/product_process.php
// item_id = 0 => add; > 0 => edit
function do_custom_sql($dir_id, $item_id)
{
    global $db_prefix, $tmp_spec, $lang, $config;
    $output = $err = array();

    $ffolder = './../public/listing';
    $ifolder = './../public/listing';
    $tfolder = './../public/listing_thumb';

    $output = array();
    $old_val = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");
    if (!$old_val) {
        sql_query("INSERT INTO ".$db_prefix."listing_cf_value SET item_id='$item_id'");
    }

    $res = sql_query("SELECT * FROM ".$db_prefix."listing_cf_define WHERE dir_id='$dir_id'");
    while ($row = sql_fetch_array($res)) {
        $key = 'cf_'.$row['idx'];

        if (empty($old_val[$key])) {
            $old = false;
        } else {
            $old = $old_val[$key];
        }

        $val = post_param($key);

        switch ($row['cf_type']) {
            case 'varchar':
            case 'rating':
            case 'select':
            case 'gmap':
            case 'textarea':
            case 'tel':
            case 'email':
            case 'country':
            case 'url':
            break;

            case 'time':
                $val = time_param('time_'.$key, 'post');
            break;

            case 'date':
                $val = date_param('date_'.$key, 'post');
            break;

            case 'img':
                if (!empty($_FILES[$key]['name'])  && (!$config['demo_mode'])) {
                    // upload
                    image_optimizer($_FILES[$key]['tmp_name'], "$ifolder/".$_FILES[$key]['name'], $config['optimizer']);
                    if (!empty($config['watermark_file'])) {
                        image_watermark("$ifolder/".$_FILES[$key]['name'], './../public/image/'.$config['watermark_file']);
                    }

                    // create thumb
                    image_optimizer($_FILES[$key]['tmp_name'], "$tfolder/".$_FILES[$key]['name'], $config['thumb_quality'], 'thumb');

                    unlink($_FILES[$key]['tmp_name']);
                    $val = $_FILES[$key]['name'];
                } else {
                    $val = $old;
                }	// if no file uploaded, populate current $val with $old value. Otherwise, empty $val will be deleted!
            break;

            case 'file':
                if (!empty($_FILES[$key]['name']) && (!$config['demo_mode'])) {
                    $s = upload_file($key, "$ffolder/".$_FILES[$key]['name'], true);
                    if ($s['success']) {
                        $val = $s[0]['filename'];
                    } else {
                        $val = $old;
                    }
                } else {
                    $val = $old;
                }
            break;

            case 'multi':
                $val = checkbox_param($key, 'post', true);

                if ($val) {
                    // verify selection
                    $foo = explode("\r\n", $row['cf_option']);
                    $fii = safe_send($foo, true);
                    $foo = array_pair($fii, $foo);

                    $selected = array();
                    foreach ($val as $k => $v) {
                        if (array_key_exists($v, $foo)) {
                            $selected[] = $foo[$v];
                        }
                    }

                    $val = "\r\n".implode("\r\n", $selected)."\r\n";
                }
            break;

            case 'video':
                // unfortunately, we can not store 'cleaned' youtube/vimeo URL, as cleaned URL will be marked as invalid by the following checker
                if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $val, $matches)) {
                    $video = true;
                } elseif (preg_match('~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x', $val, $matches)) {
                    $video = true;
                } else {
                    $video = false;
                }
                if (!$video) {
                    $val = '';
                }
            break;

            case 'div':
                $val = 'foo';
            break;

            default:
                die("Unknown custom field type: $row[cf_type]");
            break;
        }

        // add/edit cf val
        if (!empty($val)) {
            $output[] = "$key='$val'";
        /* if ($old_idx)
            sql_query ("UPDATE ".$db_prefix."listing_cf_value SET cf_value='$val', cf_value_text='$val_text' WHERE idx='$old_idx' LIMIT 1");
        else
            sql_query ("INSERT INTO ".$db_prefix."listing_cf_value SET item_id='$item_id', cf_id='$row[idx]', cf_value='$val', cf_value_text='$val_text'"); */
        } else {	// remove cf val
            $output[] = "$key=''";
        }
    }

    $sql = implode(', ', $output);
    if ($sql) {
        sql_query("UPDATE ".$db_prefix."listing_cf_value SET $sql WHERE item_id='$item_id' LIMIT 1");
    }
    return;
}


function delete_feat($item_id)
{
    global $config, $db_prefix;

    $search[] = ";$item_id;";
    $replace[] = ';';
    $search[] = ";$item_id";
    $replace[] = '';
    $search[] = "$item_id;";
    $replace[] = '';
    $search[] = $item_id;
    $replace[] = '';

    // top category
    $t = str_replace($search, $replace, $config['featured_product']);
    sql_query("UPDATE ".$db_prefix."config SET config_value='$t' WHERE config_id='featured_product' LIMIT 1");

    // other categories
    $res = sql_query("SELECT idx, cat_featured FROM ".$db_prefix."product_cat WHERE cat_featured LIKE '%$item_id%'");
    while ($row = sql_fetch_array($res)) {
        $t = str_replace($search, $replace, $row['cat_featured']);
        sql_query("UPDATE ".$db_prefix."product_cat SET cat_featured = '$t' WHERE idx = '$row[cat_id]' LIMIT 1");
    }
}



require './../includes/admin_init.php';
admin_check(4);
AXSRF_check();

$item_id = post_param('item_id');
$dir_id = post_param('dir_id');
$owner_id = post_param('owner_id');
$owner_email = post_param('owner_email');
$owner_passwd = post_param('owner_passwd');
$item_permalink = post_param('item_permalink');
$item_title = post_param('item_title');
$item_url = post_param('item_url');
$item_url_mask = post_param('item_url_mask');
$item_summary = post_param('item_summary');
$item_details = post_param('item_details');
$item_status = post_param('item_status');
$item_sort_point = post_param('item_sort_point');
$item_class = post_param('item_class');
$item_date = date_param('item_date', 'post');
$item_valid_date = date_param('item_valid_date', 'post');
$item_backlink_url = post_param('item_backlink_url');
$item_visibility = post_param('item_visibility');
$item_keyword = post_param('item_keyword');
$see_also = post_param('see_also');
$send_email = post_param('send_email');

$copy_item = post_param('copy_item');
$copy_cf = post_param('copy_cf');
$copy_switch = post_param('copy_switch');
$copy_img = post_param('copy_img');

// mode
save_form('listing');
AXSRF_check();
if (!$item_id) {
    $mode = 'new';
} else {
    $mode = 'edit';
}

// dir info
$dir_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_dir WHERE idx='$dir_id' LIMIT 1");
if (!$dir_inf) {
    admin_die(sprintf($lang['msg']['echo'], 'Invalid Directory ID!'));
}

// cats
$sql_cat_arr = $cat_id_arr = array(); $sql_cat_str = '';
for ($i = 1; $i <= $dir_inf['dir_multi_cat']; $i++) {
    $cat_id_arr[$i] = post_param('category_'.$i);
}
$cat_id_arr = cat_id_unique($cat_id_arr);
foreach ($cat_id_arr as $k => $v) {
    $sql_cat_arr[$k] = "category_$k='".$v."'";
}
$sql_cat_str = implode(', ', $sql_cat_arr);

// valid backlink?
if (!empty($item_backlink_url)) {
    $item_backlink_ok = verify_backlink($item_backlink_url) ? 1 : 0;
} else {
    $item_backlink_ok = '';
}

// create sql
$sql = "dir_id = '$dir_id', owner_id = '$owner_id', owner_email = '$owner_email', $sql_cat_str, item_url='$item_url', item_url_mask='$item_url_mask',
item_permalink = '$item_permalink', item_title = '$item_title', item_summary = '$item_summary', item_details = '$item_details', item_status = '$item_status', item_sort_point = '$item_sort_point', item_class = '$item_class',
item_date = '$item_date', item_update='$sql_today', item_valid_date = '$item_valid_date', item_backlink_url = '$item_backlink_url', item_backlink_ok='$item_backlink_ok', item_visibility = '$item_visibility', item_keyword = '$item_keyword', see_also = '$see_also', orphaned='0'";

// execute sql
$change_request = false;

if ($mode == 'new') {
    sql_query("INSERT INTO ".$db_prefix."listing SET $sql");
    $item_id = mysqli_insert_id($dbh);
    if ($item_status == 'P') {
        foreach ($cat_id_arr as $k => $v) {
            recount_num_link($dir_id, $cat_id_arr[$k], 'inc');
        }
    }
} else {
    // get old vals
    $old = sql_qquery("SELECT * FROM ".$db_prefix."listing WHERE idx = '$item_id' LIMIT 1");

    // update
    sql_query("UPDATE ".$db_prefix."listing SET $sql WHERE idx = '$item_id' LIMIT 1");

    // approve change request?
    if ($old['original_idx'] && $item_status == 'P') {
        $change_request = true;

        // - remove old item
        sql_query("DELETE FROM ".$db_prefix."listing_cf_value WHERE item_id='$old[original_idx]' LIMIT 1");
        sql_query("DELETE FROM ".$db_prefix."listing WHERE idx='$old[original_idx]' LIMIT 1");
        if (file_exists('./../public/listing/'.$old['original_idx'].'_1.jpg')) {
            unlink('./../public/listing/'.$old['original_idx'].'_1.jpg');
            unlink('./../public/listing_thumb/'.$old['original_idx'].'_1.jpg');
            unlink('./../public/listing_thumb/small_'.$old['original_idx'].'_1.jpg');
        }

        // - rename index
        sql_query("UPDATE ".$db_prefix."listing SET idx='$old[original_idx]', original_idx=0 WHERE idx='$item_id' LIMIT 1");
        sql_query("UPDATE ".$db_prefix."listing_cf_value SET item_id='$old[original_idx]' WHERE item_id='$item_id' LIMIT 1");

        // - rename logo
        if (file_exists('./../public/listing/'.$item_id.'_1.jpg')) {
            rename('./../public/listing/'.$item_id.'_1.jpg', './../public/listing/'.$old['original_idx'].'_1.jpg');
            rename('./../public/listing_thumb/'.$item_id.'_1.jpg', './../public/listing_thumb/'.$old['original_idx'].'_1.jpg');
            rename('./../public/listing_thumb/small_'.$item_id.'_1.jpg', './../public/listing_thumb/small_'.$old['original_idx'].'_1.jpg');
        }

        // - remove counter (it should be re-added later, not very efficient but it works)
        foreach ($cat_id_arr as $k => $v) {
            recount_num_link($dir_id, $v, 'dec');
        }

        $item_id = $old['original_idx'];
    }

    // if cats changed, recount the num_link (only if listing is published)
    // - if new status = 'Published'
    if ($item_status == 'P') {
        // -- but old status was also 'Published' => decrease counter
        if ($old['item_status'] == 'P') {
            for ($i = 1; $i <= $dir_inf['dir_multi_cat']; $i++) {
                recount_num_link($dir_id, $old['category_'.$i], 'dec');
            }
        }

        // -- and increase counter
        // -- these must be done, in case admin change the cats, if cats not changed => it simply restore the counter
        foreach ($cat_id_arr as $k => $v) {
            recount_num_link($dir_id, $v, 'inc');
        }
    }

    // - if new status = 'Pending', decrease the counter, simple
    if (($old['item_status'] == 'P') && ($item_status != 'P')) {
        foreach ($cat_id_arr as $k => $v) {
            recount_num_link($dir_id, $v, 'dec');
        }
    }
}

// reset password?
if ($owner_passwd) {
    sql_query("UPDATE ".$db_prefix."listing SET owner_passwd='$owner_passwd' WHERE idx='$item_id' LIMIT 1");
}

// permalink
if (($mode == 'new') || (($mode == 'edit') && ($old['item_permalink'] != $item_permalink)) || (empty($item_permalink))) {
    if (!empty($item_permalink)) {
        $item_permalink = generate_permalink($item_permalink, 'detail.php', $item_id, '', '', false, true);
    } else {
        $item_permalink = generate_permalink($item_title, 'detail.php', $item_id, '', '', true, true);
    }
    if (!$item_permalink) {
        $item_permalink = generate_permalink($item_title, 'detail.php', $item_id, '', '', true, true);
    }
    sql_query("UPDATE ".$db_prefix."listing SET item_permalink='$item_permalink' WHERE idx='$item_id' LIMIT 1");
}

// upload images
// -- default image
if (!empty($_FILES['logo']['name'])) {
    // find folder
    $folder = './../public/listing';
    $tolder = './../public/listing_thumb';

    // create image
    $image_id = $item_id.'_1';
    $target = "$folder/$image_id.jpg";

    // optimize image
    if ($config['optimizer'] || $dir_inf['dir_logo_size']) {
        $img = getimagesize($_FILES['logo']['tmp_name']);
        image_optimizer($_FILES['logo']['tmp_name'], $target, $config['optimizer'], $dir_inf['dir_logo_size']);
        if (!empty($config['watermark_file'])) {
            image_watermark($target, './../public/image/'.$config['watermark_file']);
        }
        if (!file_exists($target)) {
            admin_die($lang['msg']['can_not_upload']);
        }
        @chmod($target, 0644);
    } else {
        if (!$config['demo_mode']) {
            if (!@upload_file('logo', $target)) {
                admin_die($lang['msg']['can_not_upload']);
            }
            if (!empty($config['watermark_file'])) {
                image_watermark($target, './../public/image/'.$config['watermark_file']);
            }
            @chmod($target, 0644);
        }
    }
    @unlink("$tolder/$image_id.jpg");
    @unlink("$tolder/small_$fn.jpg");
    make_thumb($image_id, 'detail', true);
    make_thumb($image_id, 'small', true);
}

// custom fields
do_custom_sql($dir_id, $item_id);

// create smart search cache
create_search_cache($item_id);

// force clear cache
qcache_clear();
qcache_clear('dir_'.$dir_id.'.cat_num_link', false);

// send email
if ($send_email == 1) {
    if (($old['item_status'] != 'P') && ($item_status == 'P')) {
        kemana_email($owner_email, $item_id, 'status_p');
    }
    if (($old['item_status'] != 'E') && ($item_status == 'E')) {
        kemana_email($owner_email, $item_id, 'status_e');
    }
} elseif ($send_email == 2) {
    $subject = '['.$config['site_name'].'] '.sprintf($lang['l_mail_add_subject'], $old['item_title']);
    $url = $config['site_url'].'/'.$config['admin_folder'].'/admin_mail.php?mode=status_p&item_id='.$item_id.'&email='.$owner_email.'&subject='.$subject;
    if (($old['item_status'] != 'P') && ($item_status == 'P')) {
        redir($url.'&mode=status_p');
    }
    if (($old['item_status'] != 'E') && ($item_status == 'E')) {
        redir($url.'&mode=status_e');
    }
}

// copy?
if ($copy_item) {
    // copy product db
    sql_query("INSERT INTO ".$db_prefix."listing SET $sql");
    $new_item = mysqli_insert_id($dbh);

    // fix several things
    $item_permalink = generate_permalink($item_title, 'detail.php', $new_item, '', '', true, true);
    sql_query("UPDATE ".$db_prefix."listing SET item_status='E', item_permalink='$item_permalink' WHERE idx='$new_item' LIMIT 1");

    // copy cf?
    if ($copy_cf) {
        $cf_sql = array("item_id='$new_item'");
        $cfsrc = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");
        foreach ($cfsrc as $k => $v) {
            if (!is_numeric($k) && ($k != 'idx') && ($k != 'item_id')) {
                $cf_sql[] = "$k='".addslashes($v)."'";
            }
        }
        $cf_sql = implode(', ', $cf_sql);
        sql_query("INSERT INTO ".$db_prefix."listing_cf_value SET $cf_sql");
    }

    // copy images?
    if ($copy_img) {
        // find folder
        $sfolder = '../public/listing';
        $tfolder = '../public/listing_thumb';
        $sf = $sfolder.'/'.$item_id.'_1.jpg';
        $st = $tfolder.'/'.$item_id.'_1.jpg';
        $tf = $sfolder.'/'.$new_item.'_1.jpg';
        $tt = $tfolder.'/'.$new_item.'_1.jpg';
        if (file_exists($sf)) {
            copy($sf, $tf);
            copy($st, $tt);
            chmod($tf, 0644);
            chmod($tt, 0644);
        }
    }

    // redir?
    if ($copy_switch) {
        admin_die(sprintf($lang['msg']['echo'], 'Item has been copied succesfully. You are now editing <b>the copied</b> item.'), $config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&item_id='.$new_item);
    }
}

if ($copy_item) {
    admin_die(sprintf($lang['msg']['echo'], 'Item has been copied succesfully. You are now editing <b>the original</b> item.'), $config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&item_id='.$item_id);
} else {
    admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&item_id='.$item_id);
}
