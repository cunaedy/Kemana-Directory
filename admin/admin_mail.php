<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(4);

$mode = get_param('mode');
$item_id = get_param('item_id');
$user_id = get_param('user_id');
$email = get_param('email');
$txt['mode'] = $mode;
$txt['company_logo'] = '<img src="'.$config['site_url'].'/public/image/'.$config['company_logo'].'" alt="logo" />';
$tpl_mode = '';

switch ($mode) {
    case 'mail':
        if ($email) {
            $id['user_id'] = '';
            $id['user_email'] = $email;
        } else {
            $id = get_user_info($user_id);
        }
        $txt = array_merge($txt, $id);
        $txt['today'] = convert_date($sql_today);
        $txt['subject'] = '';
        $txt['email_body'] = rte_area('email_body', quick_tpl(load_tpl('mail', 'admin_mail'), $txt));
        $txt['main_body'] = quick_tpl(load_tpl('adm', 'send_mail.tpl'), $txt);
        flush_tpl('adm');
    break;


    case 'status_p':
    case 'status_e':
        $subject = get_param('subject');
        if ($mode == 'status_p') {
            $txt['email_body'] = rte_area('email_body', kemana_email($email, $item_id, 'status_p', false));
        } else {
            $txt['email_body'] = rte_area('email_body', kemana_email($email, $item_id, 'status_e', false));
        }
        $txt['user_id'] = '';
        $txt['user_email'] = $email;
        $txt['today'] = convert_date($sql_today);
        $txt['subject'] = $subject;
        $txt['main_body'] = quick_tpl(load_tpl('adm', 'send_mail.tpl'), $txt);
        flush_tpl('adm');
    break;
}
