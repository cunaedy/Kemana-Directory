<?php
// get param
$type = strtoupper(mod_param('banner_type'));

$row = sql_qquery("SELECT * FROM ".$db_prefix."page WHERE group_id='QBANR' ORDER BY RAND() LIMIT 1");
if (!empty($row)) {
    $output  = "<div style=\"text-align:center\">";
    $output .= "<a href=\"$row[page_keyword]\"><img src=\"$config[site_url]/public/image/$row[page_image]\" alt=\"$row[page_title]\" /></a>";
    $output .= "</div>";
} else {
    $output = '<!-- no banner found -->';
}

$mod_content_edit_url = $config['site_url'].'/'.$config['admin_folder'].'/task.php?mod=qbanner&amp;run=edit.php';
