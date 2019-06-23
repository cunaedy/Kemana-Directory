<?php
function get_cf($dir_id, $item_id, $saved_form, $item_class)
{
    global $db_prefix, $tpl_section, $rating_def, $cf_custom_sort, $lang, $dir_info, $config, $sql_today;

    $ffolder = $config['site_url'].'/public/file';
    $ifolder = $config['site_url'].'/public/listing';
    $tfolder = $config['site_url'].'/public/listing_thumb';
    $axsrf = AXSRF_value();

    $output = array();
    $cf_val = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");		// cf value
    //$res = sql_query ("SELECT * FROM ".$db_prefix."listing_cf_define WHERE dir_id='$dir_id' ORDER BY FIELD(idx,$cf_custom_sort)");		// cf definition

    foreach ($dir_info[$dir_id]['cf_define'] as $row) {
        $key = 'cf_'.$row['idx'];
        if (!empty($saved_form[$key])) {
            $val = $saved_form[$key];
        } else {
            $val = $cf_val[$key];
        }
        if ($row['is_required']) {
            $required = 'required="required"';
        } else {
            $required = '';
        }
        if ($row['cf_help']) {
            $row['cf_help'] = '<span class="glyphicon glyphicon-info-sign help tips" title="'.$row['cf_help'].'"></span>';
        }
        $field = $row['cf_type'].' type is not defined!';

        switch ($row['cf_type']) {
            case 'time':
                $fn = 'time_'.$key;
                $field = time_form($fn, $val);
            break;

            case 'date':
                $fn = 'date_'.$key;
                if (empty($val)) {
                    $val = $sql_today;
                }
                $field = date_form($fn, 1, 1, 1, $val)." <a style=\"cursor:pointer\"><span class=\"glyphicon glyphicon-calendar calendar\" id=\"cal_$fn\" data-date-format=\"yyyy-mm-dd\" data-date=\"$val\"></span></a>
				<script>var cal=$('#cal_$fn').datepicker().on('changeDate',function(ev){update_date_form('$fn',ev.date);$('#cal_$fn').datepicker('hide')});</script>";
            break;

            case 'country':
                $field = create_select_form($key, get_country_list(), $val, '&nbsp;');
            break;

            case 'varchar':
            case 'tel':
                $field = "<input type=\"text\" name=\"$key\" value=\"$val\" $required />";
            break;

            case 'url':
                $field = "<input type=\"url\" size=\"50\"name=\"$key\" value=\"$val\" $required />";
            break;

            case 'video':
                $field = "<input type=\"text\" name=\"$key\" size=\"50\" value=\"$val\" placeholder=\"$lang[l_video_help]\" />";
            break;

            case 'textarea':
                $field = "<textarea name=\"$key\" cols=\"50\" rows=\"5\" $required>$val</textarea>";
            break;

            case 'file':
                if (empty($val)) {
                    $field = "<input type=\"file\" name=\"$key\" $required />";
                } else {
                    $fs = num_format(filesize("./public/file/$val") / 1024);
                    $field = "<a href=\"$ffolder/$val\">$val</a> ($fs KB)<br /><a href=\"add.php?cmd=del_cf&amp;cf_id=$key&amp;item_id=$item_id&amp;AXSRF_token=$axsrf\"><span class=\"glyphicon glyphicon-remove\"></span> Remove</a>";
                }
            break;

            case 'img':
                if (empty($val)) {
                    $field = "<input type=\"file\" name=\"$key\" $required />";
                } else {
                    $field = "<a href=\"$ifolder/$val\" class=\"lightbox\"><img src=\"$tfolder/$val\" alt=\"thumb\" /></a><br /><a href=\"add.php?cmd=del_cf&amp;cf_id=$key&amp;item_id=$item_id&amp;AXSRF_token=$axsrf\"><span class=\"glyphicon glyphicon-remove\"></span> Remove</a>";
                }
            break;

            case 'multi':
                if ($val) {
                    // safe send selected
                    $foo = explode("\r\n", $val);
                    $selected = safe_send($foo, true);

                    // verify selection
                    $val = verify_selected($selected, $row['cf_option']);
                    $val = safe_send($val, true);
                }

                $foo = explode("\r\n", $row['cf_option']);
                $fii = safe_send($foo, true);
                $foo = array_pair($fii, $foo);
                $field = create_checkbox_form($key, $foo, $val, 3);
            break;

            case 'select':
                $foo = explode("\r\n", $row['cf_option']);
                $fii = safe_send($foo, true);
                $foo = array_pair($fii, $foo);
                $val = safe_send($val, true);
                $field = create_select_form($key, $foo, $val, '-', false, $required);
            break;

            case 'rating':
                unset($rating_def[0]);
                $field = create_select_form($key, $rating_def, $val, '-', false, $required);
            break;

            case 'gmap':
                $field = "<input type=\"text\" name=\"$key\" id=\"$key\" size=\"50\" value=\"$val\" class=\"width-md\" $required /> <a href=\"$config[site_url]/gmap_picker.php?cmd=picker&amp;mode=latlon1&amp;fid=$key&amp;latlon=$val\" class=\"popiframe_sp\"><span class=\"glyphicon glyphicon-map-marker\"></span> Locate</a>";
            break;

            case 'div':
                $field = $row['cf_title'];
            break;
        }

        if ($row['avail_to_'.$item_class]) {
            $row['field'] = $field;
            if ($row['is_required']) {
                $row['field'] .= ' '.$lang['l_required_symbol'];
            }
            if ($row['cf_type'] == 'div') {
                $output[] = quick_tpl($tpl_section['cf_list_div'], $row);
            } else {
                $output[] = quick_tpl($tpl_section['cf_list'], $row);
            }
        }
    }

    return implode($output, "\n");
}

require_once './includes/user_init.php';

if (!$isLogin && !$config['ke']['guess_allow_submission']) {
    msg_die($lang['msg']['not_member']);
}
if ($config['ke']['add_admin_only']) {
    fullpage_die($lang['l_page_admin_only']);
}

$cmd = get_param('cmd');
$item_id = get_param('item_id');
$secret_key = get_param('secret_key');
if (empty($cmd)) {
    $cmd = post_param('cmd');
}

switch ($cmd) {
    case 'del':
        if ($isLogin) {
            AXSRF_check();
        }

        // verify link's owner & status (must be P[E]NDING)
        $row = verify_owner($item_id, '*');
        if ($row['item_status'] != 'E') {
            redir();
        }

        remove_item($item_id);
        if (!$isLogin) {
            msg_die($lang['msg']['ok']);
        }
        redir($config['site_url'].'/account.php?cmd=listing');
    break;


    case 'confirm':
        $row = sql_qquery("SELECT * FROM ".$db_prefix."listing WHERE idx='$item_id' LIMIT 1");
        if ($row['owner_passwd'] != qhash($secret_key)) {
            msg_die($lang['msg']['invalid_key']);
        }
        if ($row['item_status'] !='T') {
            msg_die($lang['msg']['item_status_not_t']);
        }
        sql_query("UPDATE ".$db_prefix."listing SET item_status='E' WHERE idx='$item_id' LIMIT 1");

        // send email to admin
        create_notification('', 'New Submission: '.$row['item_title'], $config['site_url'].'/'.$config['admin_folder'].'/listing.php?cmd=edit&item_id='.$item_id, true);
        kemana_email($row['owner_email'], $item_id, 'inform_e', true, array('owner_passwd' => $secret_key));
        msg_die($lang['msg']['item_status_set_e']);
    break;


    case 'del_cf':
        if ($isLogin) {
            AXSRF_check();
        }
        $row = verify_owner($item_id, '*');

        $cf_id = substr(get_param('cf_id'), 3);
        $field = 'cf_'.$cf_id;

        $cf_inf = sql_qquery("SELECT * FROM ".$db_prefix."listing_cf_define WHERE idx='$cf_id' LIMIT 1");
        if (!$cf_inf) {
            die('Invalid CF ID!');
        }

        $cf_val = sql_qquery("SELECT $field FROM ".$db_prefix."listing_cf_value WHERE item_id='$item_id' LIMIT 1");
        $val = $cf_val[$field];

        // delete file
        if (($cf_inf['cf_type'] == 'img') && (!empty($val))) {
            unlink('./public/image/'.$val);
        } elseif (($cf_inf['cf_type'] == 'file') && (!empty($val))) {
            unlink('./public/file/'.$val);
        }

        // update db
        $cf_val = sql_qquery("UPDATE ".$db_prefix."listing_cf_value SET $field='' WHERE item_id='$item_id' LIMIT 1");
        msg_die('ok');
    break;


    case 'del_img':
        if ($isLogin) {
            AXSRF_check();
        }
        $row = verify_owner($item_id, '*');

        $fn = $item_id.'_1.jpg';
        $folder = $config['abs_path'].'/public/listing';
        $tolder = $config['abs_path'].'/public/listing_thumb';
        @unlink("$folder/$fn");
        @unlink("$tolder/$fn");
        @unlink("$tolder/small_{$fn}_1.jpg");
        msg_die('ok');
    break;


    case 'lost':
        $item_id = post_param('item_id');
        $visual = post_param('visual');

        $tpl_mode = 'lost';
        if (empty($item_id)) {
            qvc_init(3);
            $txt['main_body'] = quick_tpl(load_tpl('add_init.tpl'), $txt);
            flush_tpl();
        } else {
            if (empty($visual) || qhash(strtolower($visual)) != qvc_value()) {
                msg_die(sprintf($lang['msg']['add_error'], $lang['l_captcha_error']));
            }
            $row = verify_owner($item_id);
            $new_passwd = random_str(16, false, 2);
            sql_query("UPDATE ".$db_prefix."listing SET owner_passwd='".qhash($new_passwd)."' WHERE idx='$item_id' LIMIT 1");

            kemana_email($row['owner_email'], $item_id, 'lost', true, array('owner_passwd' => $new_passwd));
        }
    break;


    case 'edit_guest':
        $item_id = post_param('item_id');
        $edit_passwd = post_param('edit_passwd');
        $visual = post_param('visual');

        if (empty($item_id) || empty($edit_passwd)) {
            qvc_init(3);
            $tpl_mode = 'form';
            $txt['main_body'] = quick_tpl(load_tpl('add_init.tpl'), $txt);
            flush_tpl();
        } else {
            if (empty($visual) || qhash(strtolower($visual)) != qvc_value()) {
                msg_die(sprintf($lang['msg']['add_error'], $lang['l_captcha_error']));
            }
            $row = verify_owner($item_id);
            if (qhash($edit_passwd) != $row['owner_passwd']) {
                msg_die($lang['msg']['edit_item_not_found']);
            }
            ip_config_update('edit_passwd', $item_id.':'.$edit_passwd);
            redir($config['site_url'].'/add.php?cmd=edit&item_id='.$item_id);
        }
    break;


    case 'edit':
        $row = verify_owner($item_id, '*');

        // get dir info
        $dir_id = $row['dir_id'];
        get_dir_info($dir_id);
        $dir_inf = $dir_info[$dir_id]['dir_inf'];

        // template
        $tpl_mode = 'edit';
        $cf_form = true;
        $allow_logo_empty = $allow_logo_exists = $duplicate_url = $require_email = $allow_upgrade = $allow_remove = $allow_url_mask = false;
        $allow_logo = $dir_inf['dir_logo'];
        $require_url = $dir_inf['dir_url'];
        $allow_url_mask = $dir_inf['dir_url_mask'];
        $require_backlink = $dir_inf['dir_backlink'];
        $require_summary = $dir_inf['dir_summary'];

        // allow upgrade?
        if ($dir_info[$dir_id]['dir_inf']['dir_pre_allow'] || $dir_info[$dir_id]['dir_inf']['dir_spo_allow']) {
            $allow_upgrade = true;
        }

        // allow remove (must be PENDING)
        if ($row['item_status'] == 'E') {
            $allow_remove = true;
        }

        if ($allow_logo) {
            if (file_exists($config['abs_path'].'/public/listing/'.$item_id.'_1.jpg')) {
                $row['logo'] = "<a href=\"$config[site_url]/public/listing/".$item_id."_1.jpg\" class=\"lightbox\"><img border=\"0\" src=\"$config[site_url]/public/listing_thumb/".$item_id."_1.jpg\" /></a>";
                $allow_logo_exists = true;
            } else {
                $row['logo'] = "<img border=\"0\" src=\"../skins/_common/images/noimage.gif\" alt=\"No thumbnail\" />";
                $allow_logo_empty = true;
            }
        }
        $tpl = load_tpl('add.tpl');

        // cats
        $row['category_form'] = create_select_form('category_1', $dir_info[$row['dir_id']]['cat_structure'], $row['category_1']);
        $row['block_multi_cat'] = '';
        for ($i = 2; $i <= $dir_inf['dir_multi_cat']; $i++) {
            $foo['i'] = $i;
            $foo['category_form'] = create_select_form('category_'.$i, $dir_info[$row['dir_id']]['cat_structure'], $row['category_'.$i], '&nbsp;');
            $row['block_multi_cat'] .= quick_tpl($tpl_block['multi_cat'], $foo);
        }

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
            $dup = sql_qquery("SELECT idx, item_title FROM ".$db_prefix."listing WHERE item_url='$row[item_url]' AND idx != '$item_id' LIMIT 1");
            if ($dup) {
                $row['url_status'] = '<a href="listing.php?cmd=edit&amp;item_id='.$dup['idx'].'" class="text-danger">Duplicate URL of item ID '.$dup['idx'].' - '.$dup['item_title'].'</a>';
                $duplicate_url = true;
            }
        }

        $row['dir_id'] = $dir_id;
        $row['dir_title'] = $dir_inf['dir_title'];
        $row['item_id'] = $item_id;
        $row['list_date'] = date_form('item_date', date('Y'), 1, 1, $row['item_date']);
        $row['sp_date'] = date_form('item_valid_date', date('Y'), 1, 1, $row['item_valid_date']);
        $row['block_thumb'] = $row['digi_check'] = $row['digital'] = $row['critical'] = '';
        $row['cf_form'] = get_cf($dir_id, $item_id, array(), $row['item_class']);
        $row['owner_passwd'] = '';
        $row['preview_url'] = $config['site_url'].'/detail.php?item_id='.$item_id;
        $row['status_select'] = create_select_form('item_status', $listing_status_def, $row['item_status']);
        $row['class_select'] = create_select_form('item_class', $listing_class_def, $row['item_class']);
        $row['visibility_select'] = create_select_form('item_visibility', $listing_visible_def, $row['item_visibility']);
        $row['axsrf'] = AXSRF_value();

        if (!$row['cf_form']) {
            $cf_form = false;
        }

        qvc_init(3);
        $txt['main_body'] = quick_tpl(load_tpl('add.tpl'), $row);
        flush_tpl();
    break;


    case 'add':
        // dir_id must be selected!
        $dir_id = get_param('dir_id');
        if (empty($dir_id)) {
            $dir_id = $dir_info['config']['default'];
        }
        if (!array_key_exists($dir_id, $dir_info['structure'])) {
            $dir_id = $dir_info['config']['default'];
        }

        // get dir info
        get_dir_info($dir_id);
        $dir_inf = $dir_info[$dir_id]['dir_inf'];

        // template
        $tpl_mode = 'add';
        $cf_form = true;
        $allow_logo_empty = $allow_logo_exists = $duplicate_url = $allow_upgrade = $allow_url_mask = false;
        $allow_logo = $dir_inf['dir_logo'];
        $require_url = $dir_inf['dir_url'];
        $require_backlink = $dir_inf['dir_backlink'];
        $require_summary = $dir_inf['dir_summary'];
        $allow_url_mask = $dir_inf['dir_url_mask'];
        $require_email = !$isLogin;
        if ($allow_logo) {
            $allow_logo_empty = true;
        }
        $tpl = load_tpl('add.tpl');
        $foo = load_form('listing');
        if (!empty($foo)) {
            $row = $foo;
        } else {
            $row = create_blank_tbl($db_prefix.'listing');
        }

        // cats
        $row['category_form'] = create_select_form('category_1', $dir_info[$dir_id]['cat_structure'], $row['category_1']);
        $row['block_multi_cat'] = '';
        for ($i = 2; $i <= $dir_inf['dir_multi_cat']; $i++) {
            $foo['i'] = $i;
            $foo['category_form'] = create_select_form('category_'.$i, $dir_info[$dir_id]['cat_structure'], $row['category_'.$i], '&nbsp;');
            $row['block_multi_cat'] .= quick_tpl($tpl_block['multi_cat'], $foo);
        }

        if (empty($row['owner_id'])) {
            $row['owner_id'] = $current_user_id;
        }
        if (empty($row['owner_email'])) {
            $row['owner_email'] = empty($current_user_info['user_email']) ? '' : $current_user_info['user_email'];
        }
        $row['idx'] = '';
        $row['dir_id'] = $dir_id;
        $row['dir_title'] = $dir_inf['dir_title'];
        $row['logo'] = '';
        $row['item_id'] = '';
        $row['list_date'] = date_form('item_date', date('Y'), 1, 1, $sql_today);
        $row['sp_date'] = date_form('item_valid_date', date('Y'), 1, 1, $sql_today);
        $row['block_thumb'] = $row['digi_check'] = $row['digital'] = $row['critical'] = '';
        $row['see_also_preset'] = 'null';
        $row['cf_form'] = get_cf($dir_id, 0, $row, 'R');
        $row['preview_url'] = '#';
        $row['status_select'] = create_select_form('item_status', $listing_status_def, 'E');
        $row['class_select'] = create_select_form('item_class', $listing_class_def);
        $row['visibility_select'] = create_select_form('item_visibility', $listing_visible_def);
        $row['axsrf'] = AXSRF_value();
        if (!$row['cf_form']) {
            $cf_form = false;
        }

        qvc_init(3);
        $txt['main_body'] = quick_tpl(load_tpl('add.tpl'), $row);
        flush_tpl();
    break;


    default:
        if (!$dir_info['config']['multi']) {
            redir($config['site_url'].'/add.php?cmd=add&dir_id='.$dir_info['config']['default']);
        }

        $tpl_mode = 'dir_select';
        $tpl = load_tpl('add_init.tpl');
        $txt['block_list'] = '';
        foreach ($dir_info['structure'] as $k => $v) {
            get_dir_info($k);
            $dir_info[$k]['dir_inf']['dir_image'] = empty($dir_info[$k]['dir_inf']['dir_image']) ? $config['site_url'].'/skins/default/images/nothumb_list.png' : 'public/image/'.$dir_info[$k]['dir_inf']['dir_image'];
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $dir_info[$k]['dir_inf']);
        }

        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl();
    break;
}
