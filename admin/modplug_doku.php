<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(4);

$what = get_param('what');
$mod_id = get_param('mod_id');
$plug_id = get_param('plug_id');

// read info.xml
$fn = './'.$config['admin_folder'].'/module/'.$mod_id.'/info.html';
$doc = load_tpl('etc', $fn);

$txt['main_body'] = quick_tpl($doc, 0);
flush_tpl('adm');
