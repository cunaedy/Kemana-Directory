<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(4);
AXSRF_check();

// demo mode?
if ($config['demo_mode']) {
    admin_die('demo_mode');
}

$name = post_param('name');
$email = post_param('email');
$subject = post_param('subject');
$body = post_param('email_body', '', 'rte');
$mode = post_param('mode');

// log
email("$name <$email>", $subject, $body, true, true);

// custom redir if needed
$redir = '';
if (($mode == 'xxx')) {
    $redir = 'yyy';
}
admin_die($lang['msg']['email_ok'], $redir);
