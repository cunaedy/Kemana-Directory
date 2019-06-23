<?php
$tpl = load_tpl('mod', 'module_slideshow.tpl');

// themes?
$theme = mod_param('theme');
if (empty($theme)) {
    $theme = "theme-default";
}

// create slideshow / featured content
$output = array('block_content' => '');
$res = sql_query("SELECT * FROM ".$db_prefix."page WHERE group_id='SSHOW' ORDER BY page_id");
while ($row = sql_fetch_array($res)) {
    $output['block_content'] .= quick_tpl($tpl_block['content'], $row);
}

$output['id'] = random_str(3);
$output['theme'] = $theme;
$output = quick_tpl($tpl, $output);

$mod_content_edit_url = $config['site_url'].'/'.$config['admin_folder'].'/task.php?mod=slideshow&amp;run=edit.php';
