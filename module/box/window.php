<?php
// first line is = mod_pos, unless box module is added manually (but there is no point of using box module manually, isn't it?)
$toOutput = substr($mod_raw, strpos($mod_raw, "\n")+1);
if (empty($toOutput)) {
    $toOutput = $mod_raw;
}
$output = str_replace('__SITE__', $config['site_url'], html_unentities(stripslashes($toOutput)));

// content editor
if (!empty($mod_ini['mod_pos'])) {
    $mod_content_edit_url = $config['site_url'].'/'.$config['admin_folder'].'/manage.php?highlight='.$mod_ini['mod_pos'];
}
