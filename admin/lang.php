<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_setting');

$tab = get_param('tab');
if (empty($tab)) {
    $tab = post_param('tab', 0);
}

$cmd = get_param('cmd');
if (empty($cmd)) {
    $cmd = post_param('cmd');
}

$lang_id = get_param('lang_id');
if (empty($lang_id)) {
    $lang_id = post_param('lang_id', 'en');
}

// init vars
$txt['class0'] = $txt['class1'] = $txt['class2'] = $txt['class3'] = $txt['class4'] = $txt['class5'] = $txt['class6'] = '';
$txt['class'.$tab] = 'class="active"';
$langsection = array('properties', 'general', 'datetime', 'special', 'msg', 'mail', 'custom');
$tpl_def = array('properties', 'else', 'else', 'else', 'else', 'else', 'custom');

$langkeys = array();
$langkeys['properties'] = array('l_long_date_format', 'l_short_date_format', 'l_select_date_format', 'l_language_short', 'l_encoding', 'l_mysql_encoding', 'l_direction');

// general words
$langkeys['general'] = array('l_ok', 'l_submit', 'l_enter_captcha', 'l_captcha_error', 'l_guest', 'l_date', 'l_title', 'l_yes', 'l_no', 'l_none', 'l_up', 'l_down', 'l_left', 'l_right',
'l_top', 'l_bottom', 'l_never', 'l_na_select', 'l_all', 'l_all_cat', 'l_untitled', 'l_no_description', 'l_pp_top', 'l_pp_last', 'l_pp_next', 'l_pp_prev', 'l_item', 'l_items',
'l_back', 'l_page', 'l_pages', 'l_print', 'l_downloading', 'l_download_click', 'l_browser_back_button', 'l_open_url', 'l_demo_mode', 'l_login', 'l_login_register',
'l_logout', 'l_login_why', 'l_you_are_not_login', 'l_you_are_login', 'l_username', 'l_password', 'l_lost_passwd', 'l_reset_passwd', 'l_reset_code',
'l_account_act', 'l_account_act_why', 'l_account_act_key', 'l_account_act_key_why', 'l_contact_us', 'l_contact_us_form', 'l_register', 'l_register_now',
'l_email_address', 'l_current_password', 'l_new_password', 'l_confirm_password', 'l_subscribe_newsletter', 'l_subscribe', 'l_unsubscribe', 'l_username_used',
'l_email_used', 'l_name_empty', 'l_message_empty', 'l_username_error', 'l_email_error', 'l_password_empty', 'l_password_error', 'l_account', 'l_my_account',
'l_manage_profile', 'l_manage_profile_why', 'l_manage_subscription', 'l_manage_subscription_why', 'l_manage_faq', 'l_manage_faq_why', 'l_manage_logout_why',
'l_profile_enter_passwd', 'l_passwd_changed_subject', 'l_newsletter_subscribed', 'l_newsletter_unsubscribed', 'l_passwd_not_entered', 'l_tell_friend',
'l_your_name', 'l_your_email', 'l_subject', 'l_message', 'l_friend_name', 'l_friend_email', 'l_newsletter', 'l_search', 'l_site_search', 'l_content_search',
'l_quick_search', 'l_search_result', 'l_contents', 'l_more_result', 'l_search_no_result', 'l_you_are_here', 'l_downloaded', 'l_page_gallery', 'l_posted_by',
'l_posted_on', 'l_last_updated', 'l_related_page', 'l_article_by', 'l_other_cat', 'l_page_author', 'l_comment', 'l_facebook_comment', 'l_comment_helpful', 'l_conc_num', 'l_conc_non',
'l_no_comment', 'l_one_comment', 'l_more_comment', 'l_site_closed_info', 'l_mail_us_subject', 'l_mail_register', 'l_mail_friend_subject', 'l_mail_lost_subject', 'l_all_news', 'l_site_news',
'l_edit', 'l_default', 'l_rating_1', 'l_rating_2', 'l_rating_3', 'l_rating_4', 'l_rating_5', 'l_enter_page_number', 'l_page_member_only', 'l_page_admin_only', 'l_page_not_found', 'l_page_draft', 'l_sitemap',
'l_guest', 'l_user_level_1', 'l_user_level_2', 'l_user_level_3', 'l_user_level_4', 'l_user_level_5', 'l_admin_level_1', 'l_admin_level_2', 'l_admin_level_3', 'l_admin_level_4', 'l_admin_level_5');

// date time words
$langkeys['datetime'] = array('datetime.Sunday', 'datetime.Monday', 'datetime.Tuesday', 'datetime.Wednesday', 'datetime.Thursday', 'datetime.Friday', 'datetime.Saturday',
'datetime.Sun', 'datetime.Mon', 'datetime.Tue', 'datetime.Wed', 'datetime.Thu', 'datetime.Fri', 'datetime.Sat', 'datetime.January', 'datetime.February',
'datetime.March', 'datetime.April', 'datetime.May', 'datetime.June', 'datetime.July', 'datetime.August', 'datetime.September', 'datetime.October',
'datetime.November', 'datetime.December', 'datetime.Jan', 'datetime.Feb', 'datetime.Mar', 'datetime.Apr', 'datetime.Jun', 'datetime.Jul', 'datetime.Aug',
'datetime.Sep', 'datetime.Oct', 'datetime.Nov', 'datetime.Dec');

// special words (script specific, aka local)
$langkeys['special'] = array('l_enter_page_number','l_page_member_only','l_page_admin_only','l_page_not_found','l_page_draft','l_review','l_toolbox','l_add_favorite','l_see_also','l_description','l_contact_owner','l_specification','l_also_by',
'l_share_item','l_notify_us','l_tell_owner_subject','l_tell_us_subject','l_title_asc','l_title_dsc','l_date_asc','l_date_dsc','l_list','l_grid','l_sort_by','l_list_style','l_category','l_directory','l_sponsored',
'l_premium','l_regular','l_rate_asc','l_rate_dsc','l_rating','l_title_details_err','l_url_err','l_backlink_err','l_summary_err','l_mail_add_subject','l_mail_add_subject_adm','l_edit_item','l_mail_change_subject_adm',
'l_mail_lost_edit_subject','l_select','l_can_not_upgrade','l_method','l_fee','l_mail_expired_subject','l_pay_redir','l_pay_redir_3s','l_mail_order_admin_subject','l_mail_order_subject','l_how_to_pay',
'l_all_news','l_site_news','l_enter_page_number','l_page_member_only','l_page_admin_only','l_page_not_found','l_page_draft','l_review','l_toolbox','l_add_favorite','l_see_also','l_description','l_contact_owner',
'l_specification','l_also_by','l_share_item','l_notify_us','l_tell_owner_subject','l_tell_us_subject','l_title_asc','l_title_dsc','l_date_asc','l_date_dsc','l_list','l_grid','l_sort_by','l_list_style','l_category',
'l_directory','l_sponsored','l_premium','l_regular','l_rate_asc','l_rate_dsc','l_rating','l_title_details_err','l_url_err','l_backlink_err','l_summary_err','l_mail_add_subject','l_mail_add_subject_adm','l_edit_item',
'l_mail_change_subject_adm','l_mail_lost_edit_subject','l_select','l_can_not_upgrade','l_method','l_fee','l_mail_expired_subject','l_pay_redir','l_pay_redir_3s','l_mail_order_admin_subject','l_mail_order_subject',
'l_how_to_pay','l_remove_favorite','l_featured_listing','l_newest_listing','l_browse','l_welcome','l_my_listing','l_my_listing_why','l_my_order','l_my_order_why','l_my_favorite','l_my_favorite_why','l_active_listing',
'l_pending_listing','l_order_id','l_requested_class','l_status','l_current_class','l_period','l_monthly_fee','l_total','l_payment_method','l_payment_status','l_order_date','l_sitemap','l_add_listing','l_edit_listing',
'l_upgrade_listing','l_listing','l_select_dir','l_edit_guest_why','l_item_id','l_edit_lost_why','l_upgrade_why','l_main_category','l_upgrade_now','l_target_url','l_backlink_url','l_logo','l_summary','l_more_details',
'l_captcha','l_month','l_months','l_next','l_i_have_confirm','l_valid_until', 'l_clear', 'l_remove', 'l_remove_confirm', 'l_url_mask', 'l_filter_result', 'l_listing_info', 'l_warning', 'l_upgrade_warning', 'l_video_help');

// message words also local msg
$langkeys['msg'] = array('msg.can_not_upload', 'msg.no_cat_list', 'msg.mod_install_ok', 'msg.mod_uninstall_ok', 'msg.no_config', 'msg.cache', 'msg.tpl_only',
'msg.sa_only', 'msg.no_level', 'msg.qadmin_email_ok', 'msg.qadmin_required_err', 'msg.admin_err', 'msg.admin_error', 'msg.ok', 'msg.admin_ok', 'msg.email_ok',
'msg.reset_error', 'msg.act_error', 'msg.user_act', 'msg.contact_err', 'msg.contact_ok', 'msg.passwd_changed', 'msg.register_ok', 'msg.register_error',
'msg.update_error', 'msg.lost_error', 'msg.lost_ok', 'msg.bad_login', 'msg.ezform_ok', 'msg.ezform_required_err', 'msg.email_err', 'msg.unsub_ok',
'msg.sub_ok', 'msg.site_closed', 'msg.tell_error', 'msg.tell_ok', 'msg.demo_mode', 'msg.not_member', 'msg.username_not_found', 'msg.captcha_error',
'msg.sql_error', 'msg.echo', 'msg.msg_about', 'msg.default', 'msg.fman_error', 'msg.module_engine_disabled', 'msg.module_engine_error',
'msg.page_attachment_error', 'msg.ssl_error', 'msg.qcomment_err_1', 'msg.qcomment_err_2', 'msg.qcomment_err_3', 'msg.qcomment_err_4',
'msg.qcomment_err_5', 'msg.qcomment_err_6', 'msg.qcomment_ok_1', 'msg.qcomment_ok_2', 'msg.permalink_error', 'msg.fman_copy_err', 'msg.fman_move_err',
'msg.fman_ren_err', 'msg.fman_del_err', 'msg.fman_new_err', 'msg.fman_mkdir_err', 'msg.fman_rendir_err', 'msg.fman_rmdir_err', 'msg.fman_not_allowed',
'msg.fman_not_found', 'msg.menuman_locked_err', 'msg.mod_not_installed', 'msg.mod_installed', 'msg.qadmin_item_not_found', 'msg.permalink_cfg_error',
'msg.tellus_ok','msg.tellowner_ok','msg.add_error','msg.invalid_key','msg.item_status_not_t','msg.item_status_set_e','msg.add_thanks','msg.change_ok','msg.no_change','msg.item_not_found','msg.edit_item_not_found',
'msg.add_temp', 'msg.update_ok', 'msg.cat_error');

// mail words also local mail
$langkeys['mail'] = array('mail.outline', 'mail.admin_mail', 'mail.change_pwd', 'mail.contact', 'mail.contact_reply', 'mail.lost', 'mail.newsletter', 'mail.register', 'mail.tell',
'mail.tell_friend', 'mail.tell_us', 'mail.tell_owner', 'mail.add_inform_adm','mail.add_inform_usr','mail.add_confirm_usr','mail.item_status_p','mail.item_status_e','mail.change_e','mail.item_status_x','mail.lost_edit','mail.checkout_admin','mail.checkout','mail.soon_expired','mail.item_orphaned');

$langkeys['custom'] = array();
if (($tab == 0) && !$cmd) {
    $cmd = 'properties';
} elseif (($tab == 6) && !$cmd) {
    $cmd = 'custom';
}

switch ($cmd) {
    case 'import':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }

        // lang list
        $lang_list = array();
        $res = sql_query("SELECT DISTINCT lang_id, lang_value FROM ".$db_prefix."language WHERE lang_key='_config:lang_name' ORDER BY lang_id");
        while ($row = sql_fetch_array($res)) {
            $lang_list[] = $row['lang_id'];
        }

        $overwrite = post_param('overwrite');
        if (!is_uploaded_file($_FILES['xml_file']['tmp_name'])) {
            admin_die('admin_err');
        }

        $xml = read_xml($_FILES['xml_file']['tmp_name']);
        $new_lang_id = $xml['qmodule']['#']['id']['0']['#'];
        if ($xml['qmodule']['@']['type'] != 'langpack') {
            admin_die('<h1>Failed</h1><p>Not a language pack!</p>');
        }
        if ($xml['qmodule']['@']['version'] != '1.0.0') {
            admin_die('<h1>Failed</h1><p>Incompatible version!</p>');
        }
        if (in_array($new_lang_id, $lang_list) && !$overwrite) {
            admin_die('<h1>Failed</h1><p>Language has been existed in the database. Operation aborted.</p>');
        }

        // remove old lang if necessary
        if ($overwrite) {
            sql_query("DELETE FROM ".$db_prefix."language WHERE lang_id='$new_lang_id'");
        }

        $keys = $xml['qmodule']['#']['language']['0']['#']['lang_key'];
        $vals = $xml['qmodule']['#']['language']['0']['#']['lang_value'];
        if (count($vals) != count($keys)) {
            admin_die('Number of language keys &amp; language values are not the same. File may be corrupted?');
        }
        foreach ($keys as $k => $v) {
            $kk = $v['#'];
            $vv = addslashes($vals[$k]['#']);
            sql_query("INSERT INTO ".$db_prefix."language SET lang_id='$new_lang_id', lang_key='$kk', lang_value='$vv'");
        }

        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/lang.php?tab=0&lang_id='.$new_lang_id);
    break;


    case 'export':
        $langcfg = array();
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id' AND LEFT(lang_key, 8) = '_config:'");
        while ($row = sql_fetch_array($res)) {
            $langcfg[$row['lang_key']] = $row['lang_value'];
        }
        $lang_name = $langcfg['_config:lang_name'];

        // actually just dump mysql to xml
        $output  = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<qmodule type=\"langpack\" version=\"1.0.0\">";
        $output .= "<name>Language Pack [$lang_name]</name>\n<type>langpack</type>\n<id>$lang_id</id>\n<author>$config[site_email]</author>";
        $output .= "<copyright>$config[site_name]</copyright>\n<license>Freeware</license>\n<authorEmail>$config[site_email]</authorEmail>\n";
        $output .= "<authorUrl>$config[site_url]</authorUrl>\n<version>1.0.0</version>\n<description>This is a language pack for $lang_name.</description>\n";
        $output .= "<language>\n";
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id'");
        while ($row = sql_fetch_array($res)) {
            if ($row['lang_key'] == '_config:cache') {
                $row['lang_value'] = '';
            }
            $output .= " <lang_key>$row[lang_key]</lang_key>\n <lang_value>".htmlspecialchars($row['lang_value'])."</lang_value>\n";
        }
        $output .= "</language>\n</qmodule>";

        $content_len = strlen($output);
        @ini_set('zlib.output_compression', 'Off');
        header('ETag: '.md5($output));
        header('Pragma: public', false);
        header('Expires: 0', false);
        header('Cache-Control: private', false);
        header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT', false);
        header('Content-Type: application/octet-stream', false);
        header('Content-Disposition: attachment; filename="lang_pack_'.$lang_id.'.xml"', false);
        header('Accept-Ranges: bytes', false);
        header('Content-Length: '.$content_len, false);
        echo $output;
    break;


    case 'del_lang':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }

        AXSRF_check();
        if ($lang_id == 'en') {
            admin_die('<h1>Failed!</h1><p>You can not remove default [English] language!</p>');
        }
        sql_query("DELETE FROM ".$db_prefix."language WHERE lang_id='$lang_id'");
        sql_query("UPDATE ".$db_prefix."language SET lang_value='1' WHERE lang_key='_config:enabled' AND lang_id='en' LIMIT 1");
        sql_query("UPDATE ".$db_prefix."config SET config_value='en' WHERE config_id='default_lang' AND group_id='' LIMIT 1");
        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/lang.php?tab=0&lang_id=en');
    break;


    case 'copy_lang':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }

        $new_lang_id = get_param('new_lang_id');
        $new_lang_name = get_param('new_lang_name');
        if (empty($new_lang_id) || empty($new_lang_name)) {
            admin_die($lang['msg']['admin_err']);
        }
        $foo = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$new_lang_id' LIMIT 1");
        if (!empty($foo)) {
            admin_die('admin_err');
        }

        sql_query("INSERT INTO ".$db_prefix."language (lang_id, lang_key, lang_value) SELECT '$new_lang_id', lang_key, lang_value FROM ".$db_prefix."language WHERE lang_id='$lang_id'");
        sql_query("UPDATE ".$db_prefix."language SET lang_value='$new_lang_name' WHERE lang_key='_config:lang_name' AND lang_id='$new_lang_id' LIMIT 1");

        admin_die('admin_ok', $config['site_url'].'/'.$config['admin_folder'].'/lang.php?tab=0&lang_id='.$new_lang_id);
    break;


    case 'del':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }

        AXSRF_check();
        $lang_key = get_param('id');
        // make sure lang_key is not fixed
        $fixed = array_merge($langkeys['properties'], $langkeys['general'], $langkeys['datetime'], $langkeys['special'], $langkeys['msg'], $langkeys['mail']);
        if (!in_array($lang_key, $fixed)) {
            sql_query("DELETE FROM ".$db_prefix."language WHERE lang_key='$lang_key'");
            // sql_query ("DELETE FROM ".$db_prefix."language WHERE lang_id='en' AND lang_key='$lang_key' LIMIT 1");
        }
        redir();
    break;


    case 'save_custom':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }
        $lang_list = $new = $old = array();

        // get lang list
        $res = sql_query("SELECT DISTINCT lang_id, lang_value FROM ".$db_prefix."language WHERE lang_key='_config:lang_name' ORDER BY lang_id");
        while ($row = sql_fetch_array($res)) {
            $lang_list[] = $row['lang_id'];
        }

        foreach ($_POST as $k => $v) {
            // old lang == update
            if (substr($k, 0, 2) == 'l_') {
                $vv = post_param($k);
                $vv = html_unentities($vv);
                if (!empty($vv)) {
                    $old[$k] = $vv;
                    sql_query("UPDATE ".$db_prefix."language SET lang_value='$vv' WHERE lang_id='$lang_id' AND lang_key='$k' LIMIT 1");
                }
            }

            // new custom lang == insert
            if (substr($k, 0, 3) == 'ck_') {
                $kk = post_param($k);
                $vv = post_param('cv_'.substr($k, 3));
                $vv = html_unentities($vv);
                if (!empty($vv)) {
                    if (empty($kk)) {
                        $kk = strtolower(preg_replace("/[^a-zA-Z0-9]/", "_", $vv));
                    }
                    if (substr($kk, 0, 2) != 'l_') {
                        $kk = 'l_'.$kk;
                    }
                    $new[$kk] = $vv;
                    $foo = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id' AND lang_key='$kk' LIMIT 1");
                    if (empty($foo)) {
                        foreach ($lang_list as $k => $v) {
                            sql_query("INSERT INTO ".$db_prefix."language SET lang_id='$v', lang_key='$kk', lang_value='$vv'");
                        }
                    }
                }
            }
        }
        sql_query("UPDATE ".$db_prefix."language SET lang_value='' WHERE lang_id='$lang_id' AND lang_key='_config:cache' LIMIT 1");
        redir();
    break;


    case 'save_properties':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }
        $lang_name = post_param('lang_name');
        $enabled = post_param('enabled');
        if (!empty($lang_name)) {
            sql_query("UPDATE ".$db_prefix."language SET lang_value='$lang_name' WHERE lang_id='$lang_id' AND lang_key='_config:lang_name' LIMIT 1");
        }
        // sql_query ("UPDATE ".$db_prefix."language SET lang_value='$enabled' WHERE lang_id='$lang_id' AND lang_key='_config:enabled' LIMIT 1");
    // no break;

    case 'save':
        if ($config['demo_mode']) {
            admin_die('demo_mode');
        }
        $k = $langsection[$tab];
        foreach ($langkeys[$k] as $k2 => $v2) {
            $v2 = str_replace('.', '|', $v2);
            $vv = html_unentities(post_param($v2));
            $v2 = str_replace('|', '.', $v2);
            if (!empty($vv)) {
                sql_query("UPDATE ".$db_prefix."language SET lang_value='$vv' WHERE lang_id='$lang_id' AND lang_key='$v2' LIMIT 1");
            }
        }
        sql_query("UPDATE ".$db_prefix."language SET lang_value='' WHERE lang_id='$lang_id' AND lang_key='_config:cache' LIMIT 1");
        admin_die('admin_ok');
    break;


    case 'custom':
        $tpl_mode = $tpl_def[$tab];
        $tpl = load_tpl('adm', 'lang.tpl');

        // get all pre-defined lang keys
        $fixed = array_merge($langkeys['properties'], $langkeys['general'], $langkeys['datetime'], $langkeys['special'], $langkeys['msg'], $langkeys['mail']);

        $langkey = $langrow = array();
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id'");
        while ($row = sql_fetch_array($res)) {
            if (substr($row['lang_key'], 0, 8) != '_config:') {
                $langrow[$row['lang_key']] = $row['lang_value'];
                $langkey[] = $row['lang_key'];
            }
            if ($row['lang_key'] == '_config:lang_name') {
                $lang_name = $row['lang_value'];
            }
        }

        // then get all non-preset lang keys == custom keys
        $axsrf = axsrf_value();
        $langcust = array_diff($langkey, $fixed);
        $txt['block_list'] = '';
        foreach ($langcust as $k => $v) {
            $row = array();
            $val = htmlentities($langrow[$v]);
            $val = str_replace(array('{', '}'), array('&#123;', '&#125;'), $val);
            $row['axsrf'] = $axsrf;
            $row['lang_id'] = $lang_id;
            $row['lang_key'] = $v;
            $row['lang_val'] = $val;
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        // 10 new custom
        $txt['block_empty'] = '';
        for ($i = 1; $i <= 10; $i++) {
            $row = array();
            $row['i'] = $i;
            $txt['block_empty'] .= quick_tpl($tpl_block['empty'], $row);
        }

        $txt['lang_id'] = $lang_id;
        $txt['lang_name'] = $lang_name;
        $txt['tab'] = $tab;
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;


    case 'properties':
        $tpl_mode = $tpl_def[$tab];
        $tpl = load_tpl('adm', 'lang.tpl');

        // list of lang
        $txt['lang_list'] = '';
        $res = sql_query("SELECT DISTINCT lang_id, lang_value FROM ".$db_prefix."language WHERE lang_id != 'en' AND lang_key='_config:lang_name' ORDER BY lang_id");
        while ($row = sql_fetch_array($res)) {
            $txt['lang_list'] .= "<li><a href=\"lang.php?tab=0&amp;lang_id=$row[lang_id]\">$row[lang_value]</a></li>";
        }

        $langcfg = array();
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id' AND LEFT(lang_key, 8) = '_config:'");
        while ($row = sql_fetch_array($res)) {
            $langcfg[$row['lang_key']] = $row['lang_value'];
        }

        $prop_keys = "'".implode("', '", $langkeys['properties'])."'";
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id' AND lang_key IN ($prop_keys)");
        while ($row = sql_fetch_array($res)) {
            $txt['cfg_'.$row['lang_key']] = $row['lang_value'];
        }

        $txt['lang_id'] = $lang_id;
        $txt['lang_name'] = $langcfg['_config:lang_name'];
        $txt['lang_enabled'] = create_radio_form('enabled', $yesno, $langcfg['_config:enabled']);
        $txt['tab'] = $tab;
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;


    default:
        // get the original lang
        $langedit = array();
        $res = sql_query("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id'");
        while ($row = sql_fetch_array($res)) {
            if (substr($row['lang_key'], 0, 8) != '_config:') {
                $langedit[$row['lang_key']] = $row['lang_value'];
            }
            if ($row['lang_key'] == '_config:lang_name') {
                $lang_name = $row['lang_value'];
            }
        }

        /* To add new lang vars from db, uncomment these lines
        $new_dt = $new_msg = $new_mail = $new_lang = array ();
        foreach ($langedit as $k => $v)
        {
            $foo = explode ('.', $k);

            // seperate new keys to each group
            if (($foo[0] == 'datetime') && (!in_array ($k, $langkeys['datetime']))) $new_dt[] = "'$k'";
            elseif (($foo[0] == 'msg') && (!in_array ($k, $langkeys['msg']))) $new_msg[] = "'$k'";
            elseif (($foo[0] == 'mail') && (!in_array ($k, $langkeys['mail']))) $new_mail[] = "'$k'";
            elseif ((substr ($k, 0, 2) == 'l_') && (!in_array ($k, $langkeys['general'])) && (!in_array ($k, $langkeys['properties']))) $new_lang[] = "'$k'";
        }

        // dump! so you can copy paste to above arrays
        echo implode (',', $new_dt).'<br />'; echo implode (',', $new_msg).'<br />'; echo implode (',', $new_mail).'<br />'; echo implode (',', $new_lang).'<br />';
        die;
        // */

        // tpl
        $tpl_mode = $tpl_def[$tab];
        $tpl = load_tpl('adm', 'lang.tpl');
        $txt['block_list'] = '';
        $k = $langsection[$tab];
        foreach ($langkeys[$k] as $k2 => $v2) {
            $row = array();
            $row['lang_key'] = $v2;

            // auto insert into table if doesn't exist
            if (!isset($langedit[$v2])) {
                sql_query("INSERT INTO ".$db_prefix."language SET lang_id='$lang_id', lang_key='$v2'");
                $val = '';
            } else {
                $val = htmlentities($langedit[$v2]);
            }
            $val = str_replace(array('{', '}'), array('&#123;', '&#125;'), $val);

            // create the form
            $v2 = str_replace('.', '|', $v2);
            if ($k == 'msg') {
                $row['lang_val'] = "<textarea name=\"$v2\" style=\"width:500px;height:50px\">$val</textarea>";
            } elseif ($k == 'mail') {
                $row['lang_val'] = "<textarea name=\"$v2\" style=\"width:500px;height:100px\">$val</textarea>";
            } else {
                $row['lang_val'] = "<input type=\"text\" name=\"$v2\" value=\"$val\" style=\"width:500px\" id=\"textbox_$k2\"/> <a href=\"javascript:change($k2)\" id=\"change_$k2\"><span class=\"glyphicon glyphicon-resize-full\" title=\"expand\"></span></a>";
            }
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }
        $txt['tab'] = $tab;
        $txt['lang_id'] = $lang_id;
        $txt['lang_name'] = $lang_name;
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
