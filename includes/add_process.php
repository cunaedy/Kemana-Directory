<?php
// create & execute SQL for custom field
// inputs:
// $dir_id = dir_id
// $item_id = item_id
// $item_class = item_class (regular, sponsored, premium)
// returns true if CF updated/saved
function do_custom_sql($dir_id, $item_id, $item_class, $old_id)
{
    global $db_prefix, $tmp_spec, $lang, $config, $dir_info;
    $output = $err = array();

    $ffolder = './../public/listing';
    $ifolder = './../public/listing';
    $tfolder = './../public/listing_thumb';

    $output = array();
    if ($old_id) {
        $old_val = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_value WHERE item_id='$old_id' LIMIT 1");
    }
    sql_query("INSERT INTO ".$db_prefix."listing_cf_value SET item_id='$item_id'");

    foreach ($dir_info[$dir_id]['cf_define'] as $row) {
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
            case 'gmap':
            case 'textarea':
            case 'country':
            case 'url':
            case 'tel':
            break;

            case 'date':
                $val = date_param('date_'.$key, 'post');
            break;

            case 'time':
                $val = time_param('time_'.$key, 'post');
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

            case 'select':
                if ($val) {
                    $selected = verify_selected($val, $row['cf_option']);
                    if (!$selected) {
                        $val = false;
                    } else {
                        $val = $selected;
                    }
                }
            break;

            case 'multi':
                $val = checkbox_param($key, 'post', true);

                if ($val) {
                    $selected = verify_selected($val, $row['cf_option']);
                    $val = "\r\n".implode("\r\n", $selected)."\r\n";
                }
            break;

            case 'div':
                $val = 'foo';
            break;

            default:
                die("Unknown custom field type: $row[cf_type]");
            break;
        }

        if ($row['avail_to_'.$item_class]) {
            // add/edit cf val
            if (!empty($val)) {
                $output[] = "$key='$val'";
            } else {
                // remove cf val
                $output[] = "$key=''";
                if ($row['is_required']) {
                    msg_die(sprintf($lang['msg']['add_error'], "<ul><li>$row[cf_title] must be filled!</li></ul>"));
                }
            }
        }
    }

    $sql = implode(', ', $output);

    if ($sql) {
        sql_query("UPDATE ".$db_prefix."listing_cf_value SET $sql WHERE item_id='$item_id' LIMIT 1");
    }
    return true;
}


require './user_init.php';
require './admin_func.php';
if (!$isLogin && !$config['ke']['guess_allow_submission']) {
    msg_die($lang['msg']['not_member']);
}
if ($isLogin) {
    AXSRF_check();
}

$visual = post_param('visual');
$item_id = post_param('item_id');
$dir_id = post_param('dir_id');
$owner_email = post_param('owner_email');
$item_title = post_param('item_title');
$item_url = post_param('item_url');
$item_url_mask = post_param('item_url_mask');
$item_summary = post_param('item_summary');
$item_details = post_param('item_details');
$item_backlink_url = post_param('item_backlink_url');

// error checks
$is_error = false;
$err_msg = $optional_fields = '';
save_form('listing');

// - visual confirmation
if (empty($visual) || qhash(strtolower($visual)) != qvc_value()) {
    msg_die(sprintf($lang['msg']['add_error'], $lang['l_captcha_error']));
}

// - dir exists?
if (empty($dir_id)) {
    msg_die(sprintf($lang['msg']['add_error'], 'FATAL ERROR! Dir_id not defined!'));
}
if (!array_key_exists($dir_id, $dir_info['structure'])) {
    msg_die(sprintf($lang['msg']['add_error'], 'FATAL ERROR! Dir_id not defined!'));
}

// - dir rules
get_dir_info($dir_id);
$dir_inf = $dir_info[$dir_id]['dir_inf'];
$dir_name = $dir_inf['dir_title'];

// - validate entries
if (empty($item_title) || empty($item_details)) {
    $err_msg .= '<li>'.$lang['l_title_details_err'].'</li>';
}
if ($dir_inf['dir_url'] && empty($item_url)) {
    $err_msg .= '<li>'.$lang['l_url_err'].'</li>';
}
if ($dir_inf['dir_backlink'] && empty($item_backlink_url)) {
    $err_msg .= '<li>'.$lang['l_backlink_err'].'</li>';
}
if ($dir_inf['dir_summary'] && empty($item_summary)) {
    $err_msg .= '<li>'.$lang['l_summary_err'].'</li>';
}
if (!empty($err_msg)) {
    msg_die(sprintf($lang['msg']['add_error'], '<ul>'.$err_msg.'</ul>'));
}

// def
$folder = $config['abs_path'].'/public/listing';
$tolder = $config['abs_path'].'/public/listing_thumb';

// mode
if (!$item_id) {
    $mode = 'new';
} else {
    $mode = 'edit';
}

// dir info
$dir_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_dir WHERE idx='$dir_id' LIMIT 1");
if (!$dir_inf) {
    msg_die(sprintf($lang['msg']['echo'], 'Invalid Directory ID!'));
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

// owner id
if (!$isLogin) {
    $owner_id = '';
} else {
    $owner_email = $current_user_info['user_email'];
    $owner_id = $current_user_id;
}

// item status
$item_status = 'T';
if (!$isLogin && !$config['ke']['guess_confirm_submission']) {
    $item_status = 'E';
}
if ($isLogin && !$config['ke']['member_confirm_submission']) {
    $item_status = 'E';
}

// create sql
$sql = "dir_id = '$dir_id', owner_id = '$owner_id', owner_email = '$owner_email', $sql_cat_str, item_url='$item_url', item_url_mask='$item_url_mask',
item_title = '$item_title', item_summary = '$item_summary', item_details = '$item_details', item_status = '$item_status', item_backlink_url = '$item_backlink_url'";

// execute sql
if ($mode == 'new') {
    // email (for guest)
    if (!$isLogin && !validate_email_address($owner_email)) {
        msg_die(sprintf($lang['msg']['add_error'], $lang['l_url_err']));
    }

    // url exists?
    if ($dir_inf['dir_url']) {
        $foo = sql_qquery("SELECT idx FROM ".$db_prefix."listing WHERE item_url='$item_url' LIMIT 1");
        if ($foo) {
            msg_die(sprintf($lang['msg']['add_error'], $lang['l_url_err']));
        }
    }

    $item_date = convert_date('now', 'sql');
    $sql .= ", item_date='$item_date', item_valid_date='$item_date'";
    $edit_passwd = random_str(16, false, 2);
    $edit_passwd_hash = qhash($edit_passwd);
    $sql .= ", owner_passwd='$edit_passwd_hash'";
    sql_query("INSERT INTO ".$db_prefix."listing SET $sql");
    $item_id = mysqli_insert_id($dbh);
    $item_class = 'R';
    $old_id = false;

    // permalink
    $item_permalink = generate_permalink($item_title, 'detail.php', $item_id, '', '', true, true);
    sql_query("UPDATE ".$db_prefix."listing SET item_permalink='$item_permalink' WHERE idx='$item_id' LIMIT 1");
} else {
    // url exists?
    if ($dir_inf['dir_url']) {
        $foo = sql_qquery("SELECT idx FROM ".$db_prefix."listing WHERE item_url='$item_url' AND (idx != '$item_id' AND original_idx != '$item_id') LIMIT 1");
        if ($foo) {
            msg_die(sprintf($lang['msg']['add_error'], $lang['l_url_err']));
        }
    }

    // get old vals
    // $old_id = original item (could be master); $item_id = current item (could be a copy)
    $old = verify_owner($item_id, '*');
    $item_class = $old['item_class'];

    // by default old_id = item_id
    $old_id = $item_id;

    // -- when the user edit a [not yet approved] change request, remove the request, as we will copy from master item (original_idx)
    if ($old['original_idx']) {
        remove_item($item_id, false, true, true, false);
        $old_id = $old['original_idx'];
    }

    // -- when the user have already submit another change request, but edit the master, remove that request
    $prev = sql_qquery("SELECT idx FROM ".$db_prefix."listing WHERE original_idx='$item_id' LIMIT 1");
    if ($prev) {
        remove_item($prev['idx'], false, true, true, false);
    }

    // copy old vals to tmp item, and new changes are written to tmp. this way, we can both old (original) & new (temp) items. so if admin doesn't approve the changes, we don't lose anything.
    $tmp_item_id = sql_copy_row($db_prefix.'listing', 'idx', $old_id);

    // update
    sql_query("UPDATE ".$db_prefix."listing SET $sql WHERE idx = '$tmp_item_id' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."listing SET item_status='E', original_idx='$old_id' WHERE idx = '$tmp_item_id' LIMIT 1");

    // replace item_id with new item_id
    $item_id = $tmp_item_id;
    $old_fn = $old_id.'_1.jpg';
    $new_fn = $item_id.'_1.jpg';

    // copy logo
    if (file_exists($folder.'/'.$old_fn)) {
        copy($folder.'/'.$old_fn, $folder.'/'.$new_fn);
        copy($tolder.'/'.$old_fn, $tolder.'/'.$new_fn);
        copy($tolder.'/small_'.$old_fn, $tolder.'/small_'.$new_fn);
    }
}

// upload images
// -- default image
if (!empty($_FILES['logo']['name'])) {
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
    make_thumb($image_id, 'detail');
    make_thumb($image_id, 'small');
}

// custom fields
do_custom_sql($dir_id, $item_id, $item_class, $old_id);

// create smart search cache
create_search_cache($item_id);

// compare rows
if ($old_id) {
    $foo = sql_query("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$old_id' LIMIT 1");
    $c1 = sql_fetch_assoc($foo);
    $foo = sql_query("SELECT *, t1.idx AS item_id FROM ".$db_prefix."listing AS t1 LEFT JOIN ".$db_prefix."listing_cf_value AS t2 ON (t1.idx=t2.item_id) WHERE t1.idx='$item_id' LIMIT 1");
    $c2 = sql_fetch_assoc($foo);
    foreach (array('idx', 'item_id', 'original_idx', 'item_status') as $v) {
        unset($c1[$v], $c2[$v]);
    }

    // - if both rows are the same -> no actual change! don't send notification, but first check the logo
    if ($c1 == $c2) {
        // -- is the logo changed?
        $ologo = file_exists($folder.'/'.$old_id.'_1.jpg');
        $nlogo = file_exists($folder.'/'.$item_id.'_1.jpg');
        if ($ologo && $nlogo) {
            if (filesize($folder.'/'.$old_id.'_1.jpg') == filesize($folder.'/'.$item_id.'_1.jpg')) {
                remove_item($item_id, false, true, true, false);
                msg_die($lang['msg']['no_change']);
            }
        }
        // -- or both don't have logo.
        elseif (!$ologo && !$nlogo) {
            remove_item($item_id, false, true, true, false);
            msg_die($lang['msg']['no_change']);
        }
    }
}

// send email
reset_form();
ip_config_update('edit_passwd', 0);
if ($mode == 'new') {
    if ($item_status == 'E') {
        create_notification('', 'New Submission: '.$item_title, $config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&item_id='.$item_id, true);
        kemana_email($owner_email, $item_id, 'inform_e', true, array('owner_passwd' => $owner_passwd));
        msg_die($lang['msg']['add_thanks'], $config['site_url']);
    } else {
        kemana_email($owner_email, $item_id, 'confirm_t', true, array('owner_passwd' => $edit_passwd));
        msg_die($lang['msg']['add_temp'], $config['site_url']);
    }
} else {
    create_notification('', 'Change Request: '.$item_title, $config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&item_id='.$item_id, true);
    kemana_email($owner_email, $item_id, 'update_e');

    msg_die($lang['msg']['update_ok'], $config['site_url']);
}
