<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_config');

// remove files
$cmd = get_param('cmd');
switch ($cmd) {
    case 'del_watermark':
        $fn = sql_qquery("SELECT config_value FROM ".$db_prefix."config WHERE config_id='watermark_file' LIMIT 1");
        print_r($fn);
        if (!empty($fn)) {
            unlink('../public/image/'.$fn[0]);
            sql_query("UPDATE ".$db_prefix."config SET config_value = '' WHERE config_id='watermark_file' LIMIT 1");
        }
        admin_die('admin_ok');
    break;
}

//
$curr_loc = array(0 => 'Prefix (eg. $100)', 1 => 'Suffix (eg. 100$)');
$adpselect = array(0 => 'Disable - use standard URL', 3 => 'Enable SEF URL');
$gd = array(1 => 'GD v1.x (poor thumb quality)', 2 => 'GD v2.x (hi-quality thumb)');
$optimizer_def = array(0 => 'Disable', 30 => '30 - Low Quality', 50 => '50 - Medium Quality', 80 => '80 - High Quality (Recommended)', 100 => '100 - Best Quality');
$thumb_quality_def = array(0 => 'Disable', 30 => '30 - Low Quality', 50 => '50 - Medium Quality', 80 => '80 - High Quality (Recommended)', 100 => '100 - Best Quality');
$num_format = array('.' => '. (dot)', ',' => ', (comma)');
$thousand_format = array('.' => '. (dot)', ',' => ', (comma)', ' ' => '  (empty space)');
$num_digit = array(0 => 'None', 1 => '1', 2 => '2');
$smtp_secure_def = array('none' => 'None', 'ssl' => 'SSL', 'tls' => 'TLS');
$watermark_pos = array('TL' => 'Top Left', 'TR' => 'Top Right', 'BL' => 'Bottom Left', 'BR' => 'Bottom Right', 'CC' => 'Center');

// tpl
$tpl = load_tpl('adm', 'qe_config.tpl');

// get [ORIGINAL] config values
$res = sql_query("SELECT * FROM ".$db_prefix."config WHERE group_id=''");
while ($row = sql_fetch_array($res, 1, 1)) {
    $cfg[$row['config_id']] = $row['config_value'];
}

// list of lang
$lang_list = array('en' => 'English');
$res = sql_query("SELECT DISTINCT lang_id, lang_value FROM ".$db_prefix."language WHERE lang_id != 'en' AND lang_key='_config:lang_name' ORDER BY lang_id");
while ($row = sql_fetch_array($res)) {
    $lang_list[$row['lang_id']] = $row['lang_value'];
}

// get skin folders
$handle = opendir('../skins');
$c_skin = array();
while (false !== ($file = readdir($handle))) {
    if ($file != "." && $file != ".." && substr($file, 0, 1) != '_' && is_dir("../skins/$file")) {
        $c_skin["skins/$file"] = $file;
    }
}
closedir($handle);
$cfg['skin_select'] = create_select_form('skin', $c_skin, $cfg['skin']);

// logo
$cfg['company_logo'] = empty($cfg['company_logo']) ? '' : "<img src=\"./../public/image/$cfg[company_logo]\" alt=\"logo\" />";
$cfg['favicon'] = "<img src=\"./../public/image/$cfg[favicon]\" alt=\"favicon\" />";
if (empty($cfg['watermark_file'])) {
    $cfg['watermark'] = '';
    $isWatermark = false;
} else {
    $cfg['watermark'] = "<img src=\"./../public/image/$cfg[watermark_file]\" alt=\"watermark\" />";
    $isWatermark = true;
}

$cfg['country_select'] = create_select_form('site_country', get_country_list(), $cfg['site_country']);
$cfg['curr_pos_select'] = create_select_form('num_curr_pos', $curr_loc, $cfg['num_curr_pos']);
$cfg['enable_gzip_select'] = create_radio_form('enable_gzip', $enabledisable, $cfg['enable_gzip']);
$cfg['enable_adp_select'] = create_radio_form('enable_adp', $adpselect, $cfg['enable_adp']);
$cfg['wysiwyg_select'] = create_radio_form('wysiwyg', $yesno, $cfg['wysiwyg']);
$cfg['gd_select']  = create_select_form('gd_library', $gd, $cfg['gd_library']);
$cfg['english_select'] = create_radio_form('multi_lang', $yesno, !$cfg['multi_lang']);
$cfg['num_decimals_select'] = create_select_form('num_decimals', $num_digit, $cfg['num_decimals']);
$cfg['num_dec_point_select'] = create_select_form('num_dec_point', $num_format, $cfg['num_dec_point']);
$cfg['num_thousands_sep_select'] = create_select_form('num_thousands_sep', $thousand_format, $cfg['num_thousands_sep']);
$cfg['close_select'] = create_radio_form('close_site', $yesno, $cfg['close_site']);
$cfg['optimizer_select'] = create_select_form('optimizer', $optimizer_def, $cfg['optimizer']);
$cfg['thumb_quality_select'] = create_select_form('thumb_quality', $thumb_quality_def, $cfg['thumb_quality']);
$cfg['active_radio'] = create_radio_form('user_activation', $yesno, $cfg['user_activation']);
$cfg['mailog_radio'] = create_radio_form('mailog', $yesno, $cfg['mailog']);
$cfg['debug_select'] = create_radio_form('debug_mode', $yesno, $cfg['debug_mode']);
$cfg['allow_locked_page_radio'] = create_radio_form('allow_locked_page_list', $yesno, $cfg['allow_locked_page_list']);
$cfg['mobile_version'] = create_radio_form('mobile_version', $yesno, $cfg['mobile_version']);
$cfg['disable_browser_cache'] = create_radio_form('disable_browser_cache', $yesno, $cfg['disable_browser_cache']);
$cfg['smtp_email'] = create_radio_form('smtp_email', $yesno, $cfg['smtp_email']);
$cfg['smtp_secure'] = create_select_form('smtp_secure', $smtp_secure_def, $cfg['smtp_secure']);
$cfg['module_man_radio'] = create_radio_form('enable_module_man', $yesno, $cfg['enable_module_man']);
$cfg['module_engine_radio'] = create_radio_form('enable_module_engine', $yesno, $cfg['enable_module_engine']);
$cfg['qadmin_log_radio'] = create_radio_form('enable_qadmin_log', $yesno, $cfg['enable_qadmin_log']);
$cfg['qadmin_detail_log_radio'] = create_radio_form('enable_detailed_log', $yesno, $cfg['enable_detailed_log']);
$cfg['default_lang_select'] = create_select_form('default_lang', $lang_list, $cfg['default_lang']);
$cfg['facebook_like'] = create_radio_form('facebook_like', $yesno, $cfg['facebook_like']);
$cfg['facebook_comment'] = create_radio_form('facebook_comment', $yesno, $cfg['facebook_comment']);
$cfg['twitter_share'] = create_radio_form('twitter_share', $yesno, $cfg['twitter_share']);
$cfg['watermark_pos_select'] = create_select_form('watermark_position', $watermark_pos, $cfg['watermark_position']);

$txt['main_body'] = quick_tpl(load_tpl('adm', 'qe_config.tpl'), $cfg);
flush_tpl('adm');
