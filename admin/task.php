<?php
// part of qEngine
// just a comment
// you may notice that task.php in admin can accept run=filename.php
// while in user, it can't accept such parameter (unless you made it)
// the reason is to increase security. in admin, we have 'admin_check' so everytime a file called, it will check
// admin status. but in user, anyone can access it, so giving run parameter can be dangerous.... OR IS IT?
// tell us what do you think!

require './../includes/admin_init.php';

admin_check('1');

$mod_id = get_param('mod');
$run = get_param('run');

// find module
$x = array('..', '/');
$fn = './module/'.str_replace($x, '', $mod_id).'/'.str_replace($x, '', $run);
if (!@file_exists($fn)) {
    die("$fn not found!");
}

// load modol
include_once $fn;
flush_tpl('adm');
