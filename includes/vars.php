<?php
// predefined variables
$acp_permission_def = array(
    'site_config' => 'Site Settings',
    'site_setting' => 'Site Other Settings',
    'site_file' => 'File Manager<br /><small>(incl. file manager, backup &amp; restore)</small>',
    'site_log' => 'Logs',
    'permisi' => 'Permissions Settings',
    'page_manager' => 'Page Management',
    'page_editor' => 'Page Editor',
    'manage_menu' => 'Menu Management',
    'manage_user' => 'User Management',
    'manage_module' => 'Module Management'
);

$user_permission_def = array(
    'page_download' => 'Download File',
    'something' => 'Something'
);

$lang['l_admin_folder'] = $config['admin_folder'] = $qe_admin_folder;

// permission
// if not login -> user_level = 0
if (empty($current_user_info)) {
    $current_user_info['user_level'] = $current_user_info['admin_level'] = 0;
}
if ($current_user_info['user_level'] > 5) {
    $current_user_info['user_level'] = 5;
}
if ($current_user_info['admin_level'] > 5) {
    $current_user_info['admin_level'] = 5;
}
if ($current_user_info['user_level'] < 1) {
    $current_user_info['user_level'] = 0;
}
if ($current_user_info['admin_level'] < 1) {
    $current_user_info['user_level'] = 0;
}

$permission = array();
foreach ($config['permisi'] as $k => $v) {
    $permisi = unserialize($v);
    if ($in_admin_cp) {
        $permission[0] = false;	// guest may not enter acp
        $permission[$k] = $permisi[$current_user_info['admin_level']] ? true : false;
    } else {
        $permission[$k] = $permisi[$current_user_info['user_level']] ? true : false;
    }

    $n = 'is_allowed_'.$k;
    ${$n} = $permission[$k];
}

// user & admin def
$user_level_def = $admin_level_def = array();
for ($i = 1; $i <= 5; $i++) {
    $user_level_def[$i] = $lang['l_user_level_'.$i];
    $admin_level_def[$i] = $lang['l_admin_level_'.$i];
}

// value definition
$yesno = array(0 => 'No', 1 => 'Yes');
$hideshow = array(0 => 'Hide', 1 => 'Show');
$enabledisable = array(0 => 'Disable', 1 => 'Enable');

// edit in acp
$lang['edit_in_acp'] = "<div class=\"edit_in_acp\"><a href=\"$config[site_url]/$config[admin_folder]/page.php?id=%s\" target=\"acp\" class=\"btn btn-xs btn-default\">Edit Page</a></div>";
$lang['edit_pcat_in_acp'] = "<div class=\"edit_in_acp\"><a href=\"$config[site_url]/$config[admin_folder]/page_cat.php?id=%s\" target=\"acp\" class=\"btn btn-xs btn-default\">Edit in ACP</a></div>";
$lang['edit_in_acp_module'] = "<div class=\"edit_in_acp\"><div class=\"btn-group\"><button type=\"button\" class=\"btn btn-default btn-xs dropdown-toggle\" data-toggle=\"dropdown\">Edit Module <span class=\"caret\"></span></button><ul class=\"dropdown-menu\">
      <li><a href=\"%1\$s\" target=\"acp\"><span class=\"glyphicon glyphicon-pencil\"></span> Contents</a></li>
	  <li><a href=\"%2\$s\" target=\"acp\"><span class=\"glyphicon glyphicon-cog\"></span> Properties</a></li>
	  <li><a href=\"%3\$s\" target=\"acp\"><span class=\"glyphicon glyphicon-book\"></span> Documentation</a></li>
      </ul></div></div>";
$lang['page_pinned'] = "<span class=\"glyphicon glyphicon-pushpin\"></span>";
$lang['page_locked'] = "<span class=\"glyphicon glyphicon-eye-close\"></span>";
$lang['page_unlocked'] = "<span class=\"glyphicon glyphicon-eye-open\"></span>";
$lang['page_attachment'] = "<span class=\"glyphicon glyphicon-paperclip\"></span>";
$lang['l_required_symbol'] = '<span style="color:#f00"><b>&bull;</b></span>';

// format
// %1 = name; %2 = address; %3 = city; %4 = state; %5 = country; %6 = zip code; %7 = phone; %9 = fax; %10 = mobile; %8 = district
$address_format = array();
$address_format['member'] = "<div>%1\$s</div><div>%2\$s</div><div>%8\$s</div><div>%3\$s</div><div>%4\$s, %5\$s %6\$s</div><div>Phone: %7\$s</div>";
$address_format['site']   = "<div>%2\$s</div><div>%8\$s</div><div>%3\$s</div><div>%4\$s, %5\$s %6\$s</div><div>Phone: %7\$s</div><div>Fax: %9\$s</div><div>Mobile: %10\$s</div>";

// rating
$rating_def = array();
$rating_def[0] = $lang['l_na_select'];
$rating_def[1] = $lang['l_rating_1'];
$rating_def[2] = $lang['l_rating_2'];
$rating_def[3] = $lang['l_rating_3'];
$rating_def[4] = $lang['l_rating_4'];
$rating_def[5] = $lang['l_rating_5'];

// page list sort
$page_sort = array();
$page_sort['t'] = $lang['l_title'];
$page_sort['d'] = $lang['l_date'];

// menu manager -- menu type
$menu_man_preset = array();
$menu_man_preset['bsnav'] = 'Horizontal Drop Down';
$menu_man_preset['--'] = 'Other (please specify below)';

// others
$lang['l_cur_name'] = $config['num_currency'];
$lang['l_weight_name'] = $config['weight_name'];
$lang['l_site_name'] = $config['site_name'];
$lang['l_site_slogan'] = $config['site_slogan'];
$txt['company_logo'] = empty($config['company_logo']) ? $config['site_name'] : "<img src=\"$config[site_url]/public/image/$config[company_logo]\" alt=\"$config[site_name]\" />";
$txt['favicon'] = "$config[site_url]/public/image/$config[favicon]";
$txt['current_url'] = cur_url();				// current url
$txt['site_url'] = $config['site_url'];
$txt['news_url'] = $config['enable_adp'] ? 'news.php' : 'page.php?cmd=list&amp;cid=2';

// print page url
$base_url = clean_get_query('p');
if (substr($base_url, -1) == '?') {
    $base_url = substr($base_url, 0, -1);
}
if (empty($base_url)) {
    $txt['request_location'] = 'index.php';
} else {
    $txt['request_location'] = html_unentities($base_url);
}
if (strpos($base_url, '/admin')) {
    $txt['request_location'] = str_replace($config['site_url'].'/'.$qe_admin_folder.'/', '', $base_url);
}
if (!strpos($base_url, '?') && !strpos($base_url, '&amp;')) {
    $base_url .= '?';
}
$txt['print_this_page'] = str_replace('?&amp;', '?', $base_url.'&amp;print_version=1');

###
### the following words are rarely changed...
###

// smilies
$smilies = array();
$smilies[':clap:'] = 'misc/smilies/eusa_clap.gif';
$smilies[':clap:'] = 'misc/smilies/eusa_dance.gif';
$smilies[':doh:'] = 'misc/smilies/eusa_doh.gif';
$smilies[':hand:'] = 'misc/smilies/eusa_hand.gif';
$smilies[':pray:'] = 'misc/smilies/eusa_pray.gif';
$smilies[':think:'] = 'misc/smilies/eusa_think.gif';
$smilies[':wall:'] = 'misc/smilies/eusa_wall.gif';
$smilies[':D'] = 'misc/smilies/icon_biggrin.gif';
$smilies[':-/'] = 'misc/smilies/icon_confused.gif';
$smilies[':cool:'] = 'misc/smilies/icon_cool.gif';
$smilies[':cry:'] = 'misc/smilies/icon_cry.gif';
$smilies[':o'] = 'misc/smilies/icon_eek.gif';
$smilies[':evil:'] = 'misc/smilies/icon_evil.gif';
$smilies[':mad:'] = 'misc/smilies/icon_mad.gif';
$smilies[':|'] = 'misc/smilies/icon_neutral.gif';
$smilies[':red:'] = 'misc/smilies/icon_redface.gif';
$smilies[':roll:'] = 'misc/smilies/icon_rolleyes.gif';
$smilies[':('] = 'misc/smilies/icon_sad.gif';
$smilies[':)'] = 'misc/smilies/icon_smile.gif';
$smilies[':siht:'] = 'misc/smilies/icon_siht.gif';
$smilies[':miaw:'] = 'misc/smilies/icon_miaw.gif';
$smilies[':fcuk:'] = 'misc/smilies/icon_fcuk.gif';

// word censor
$cencor = array();
$censor['fuck'] = 'f***';
$censor['shit'] = 's***';
$censor['cunt'] = 'c***';
$censor['piss'] = 'p***';
$censor['wank'] = 'w***';
$censor['spunk'] = 's****';
$censor['bollocks'] = 'b******';
$censor['bollox'] = 'b******';
$censor['balls'] = 'b****';
$censor['cock'] = 'c***';
$censor['boob'] = 'b***';
$censor['tosser'] = 't*****';
$censor['prick'] = 'p****';
$censor['arse'] = 'a***';
$censor['bitch'] = 'b***';
$censor['bastard'] = 'b******';

// fman related
$fman_lang = array();
$fman_lang['fman_rename'] = '<img src="./../../skins/_fman/images/ren.gif" alt="rename" border="0" />';
$fman_lang['fman_delete'] = '<img src="./../../skins/_fman/images/del.png" alt="delete" border="0" />';
$fman_lang['fman_view'] = '<img src="./../../skins/_fman/images/view.gif" alt="view" border="0" />';
$fman_lang['fman_move'] = '<img src="./../../skins/_fman/images/move.gif" alt="move" border="0" />';
$fman_lang['fman_copy'] = '<img src="./../../skins/_fman/images/copy.gif" alt="copy" border="0" />';
$fman_lang['fman_edit'] = '<img src="./../../skins/_fman/images/edit.gif" alt="edit" border="0" />';
$fman_lang['fman_move_info'] = 'Select a folder to move file to.';
$fman_lang['fman_browse_info'] = 'Select a folder to change to.';
$fman_lang['fman_folder_open'] = "<img src=\"./../../skins/_fman/images/opened.gif\" alt=\"open\" />";
$fman_lang['fman_folder_close'] = "<img src=\"./../../skins/_fman/images/closed.gif\" alt=\"close\" />";
$fman_lang['fman_folder_space'] = '<span style="padding-left:16px"></span>';
