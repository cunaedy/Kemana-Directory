<?php
# main.php will be called if you need to display a full page module (eg. detailed news, full story, blog, etc)
# call this using: task.php?mod=qnews or task.php?mod=qnews&param=value&param=value

$p = get_param('p');
$cmd = get_param('cmd');
$idx = get_param('idx');
if (empty($cmd)) {
    $tpl_mode = 'list';
} else {
    $tpl_mode = 'view';
}

/* load tpl
notice that we're using 'mod', this tell qE to search for module_demo.tpl in the current skin
if not found -> use module_demo.tpl in common skin
if still not found -> exit (die) */

$tpl = load_tpl('mod', 'module_demo.tpl');

/* if you are creating a complex module, ie. requires lots of files, you can use main.php as module manager, eg:
task.php?mod=demo&amp;cmd=read => to read an article
task.php?mod=demo&amp;cmd=list => to list articles
task.php?mod=demo&amp;cmd=cat => to list categories, etc
and later in the main.php:

$cmd = get_param ('cmd');
switch ($cmd)
{
    case 'read':
        require ('./module/demo/read.php');
    break;

    case 'cat':
        require ('./module/demo/cat.php');
    break;

    case 'list':
        require ('./module/demo/cat.php');
    break;
} */

switch ($cmd) {
    case 'view':
        $sex_def = array('m' => 'Male', 'f' => 'Female');
        $row = sql_qquery("SELECT * FROM ".$db_prefix."demo WHERE idx='$idx'");
        $row['ddate'] = convert_date($row['ddate']);
        $row['dsex'] = $sex_def[$row['dsex']];
        $txt['main_body'] = quick_tpl($tpl, $row);
    break;
    
    default:
        $txt['block_list'] = '';
        $foo = sql_multipage($db_prefix.'demo', '*', '', 'dname', $p);
        foreach ($foo as $row) {
            $row['ddate'] = convert_date($row['ddate']);
            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        $txt['main_body'] = quick_tpl($tpl, $txt);
    break;
}
