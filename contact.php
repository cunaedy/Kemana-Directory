<?php
// part of qEngine
require_once './includes/user_init.php';

$cmd = get_param('cmd'); if (empty($cmd)) {
    $cmd = post_param('cmd');
}
$name = post_param('name');
$email = post_param('email');
$subject = post_param('subject');
$body = post_param('body');
$visual = post_param('visual');
$mail_to = post_param('mail_to');

// send message to admin
switch ($cmd) {
    case 'send':
        // verify entries
        $err = array();
        if (qvc_value() != qhash(strtolower($visual))) {
            msg_die($lang['msg']['captcha_error']);
        }
        if (!validate_email_address($email)) {
            $err[] = "$lang[l_email_error]";
        }
        if (empty($name)) {
            $err[] = $lang['l_name_empty'];
        }
        if (empty($body)) {
            $err[] = $lang['l_message_empty'];
        }

        // any error?
        if (!empty($err)) {
            msg_die(sprintf($lang['msg']['contact_err'], '<ul><li>'.implode('</li><li>', $err).'</li></ul>'));
        }

        // no error
        $row['name'] = $name;
        $row['email'] = $email;
        $row['body'] = $body;
        $row['title'] = $config['site_name'];
        $row['detail_url'] = $config['site_url'];
        $mail_body = quick_tpl(load_tpl('mail', 'contact'), $row);
        $auto_body = quick_tpl(load_tpl('mail', 'contact_reply'), $row);

        // how to send this form?
        email($config['site_email'], '['.$config['site_name'].'] '.$subject, $mail_body, 1, 1);
        email($email, '['.$config['site_name'].'] '.$subject, $auto_body, 1);
        $idx = mysqli_insert_id($dbh);
        create_notification('', "$name sent you an email \"$subject\"", $config['site_url'].'/'.$config['admin_folder'].'/mailog.php?mode=detail&log_id='.$idx, true);
        msg_die($lang['msg']['contact_ok']);
    break;


    default:
        $tpl_mode = 'contact';

        // some info
        qvc_init(3);
        $txt['site_address'] = format_address();
        $txt['site_name'] = $config['site_name'];
        $txt['site_slogan'] = $config['site_slogan'];
        $txt['site_email'] = str_replace('@', '[at]', $config['site_email']);
        $txt['site_address'] = format_address();
        $txt['main_body'] = quick_tpl(load_tpl('contact.tpl'), $txt);
        generate_html_header("$config[site_name] $config[cat_separator] Contact Us");
        flush_tpl();
    break;
}
