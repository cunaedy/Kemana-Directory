<?php
// handle file/image upload, form $field name must be the same with config_id value
function upload_image($field)
{
    global $db_prefix;

    if (!empty($_FILES[$field]['tmp_name'])) {
        $src = $_FILES[$field]['tmp_name'];
        $tgt = create_filename('./../public/image/', $_FILES[$field]['name'], false);
        $x = upload_file($field, './../public/image/'.$tgt);
        if (!$x['success']) {
            admin_die($lang['msg']['can_not_upload']);
        }
        @chmod('./../public/image/'.$tgt, 0644);
        return $tgt;
    } else {
        $res = sql_query("SELECT config_value FROM ".$db_prefix."config WHERE config_id='$field' LIMIT 1");
        $row = sql_fetch_array($res);
        return $row[0];
    }
}
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_config');
AXSRF_check();

// demo mode?
if ($config['demo_mode']) {
    admin_die('demo_mode');
}

// exclusion
$excluded = array('qe_version', 'site_start', 'last_autoexec', 'stats_lastdate', 'qe_hash_key');

// get param
$old = array();
$res = sql_query("SELECT * FROM ".$db_prefix."config WHERE group_id=''");
while ($row = sql_fetch_array($res)) {
    ${$row['config_id']} = post_param($row['config_id']);
    $old[$row['config_id']] = $row['config_value'];
}

// change sef url?
$change_sef = false;
if ($enable_adp != $old['enable_adp']) {
    $change_sef = true;
}

// upload images
$company_logo = upload_image('company_logo');
$favicon = upload_image('favicon');
$watermark_file = upload_image('watermark_file');

// fix some entries
$abs_path = str_replace('\\\\', '/', $abs_path);
$header_adsense_code = post_param('header_adsense_code', '', 'html');
$footer_adsense_code = post_param('footer_adsense_code', '', 'html');
if ($adp_extension[0] == '.') {
    $adp_extension = substr($adp_extension, 1);
}
if ($list_ipp < 5) {
    $list_ipp = 5;
}

// escape some html
$num_currency = html_unentities($num_currency);

// multi lang
if ($multi_lang) {
    $multi_lang = 0;
} else {
    $multi_lang = 1;
}

// update db
$res = sql_query("SELECT config_id FROM ".$db_prefix."config WHERE group_id=''");
while ($row = sql_fetch_array($res)) {
    if (!in_array($row['config_id'], $excluded)) {
        $v = ${$row['config_id']};
        sql_query("UPDATE ".$db_prefix."config SET config_value='$v' WHERE config_id='$row[config_id]' LIMIT 1");
    }
}

// rebuild cache!
qcache_clear('everything');
sql_query("UPDATE ".$db_prefix."language SET lang_value='' WHERE lang_key='_config:cache' LIMIT 1");

redir($config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=reorder_all');
