<?php
require './../includes/admin_init.php';
admin_check('site_setting');

//
$res = sql_query("SELECT * FROM ".$db_prefix."config WHERE group_id='ke'");
while ($row = sql_fetch_array($res)) {
    $cfg[$row['config_id']] = $row['config_value'];
}
$txt = array_merge($txt, $cfg);

$tpl = load_tpl('adm', 'local_config.tpl');

// create backlink code
if (empty($cfg['backlink_code'])) {
    $cfg['backlink_code'] = '&lt;a href="'.$config['site_url'].'/index.php?[user_id]"&gt;'.$config['site_name'].' - '.$config['site_slogan'].'&lt;/a&gt';
}
$txt['backlink_code'] = $cfg['backlink_code'];

$txt['add_admin_only_radio'] = create_radio_form('add_admin_only', $yesno, $cfg['add_admin_only']);
$txt['guess_allow_submission_radio'] = create_radio_form('guess_allow_submission', $yesno, $cfg['guess_allow_submission']);
$txt['guess_confirm_submission_radio'] = create_radio_form('guess_confirm_submission', $yesno, $cfg['guess_confirm_submission']);
$txt['member_confirm_submission_radio'] = create_radio_form('member_confirm_submission', $yesno, $cfg['member_confirm_submission']);
$txt['backlink_autocheck_radio'] = create_radio_form('backlink_autocheck', $yesno, $cfg['backlink_autocheck']);
$txt['main_body'] = quick_tpl($tpl, $txt);
flush_tpl('adm');
