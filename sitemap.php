<?php
require_once "./includes/user_init.php";
$output = "<ul class=\"list_2\">\n";

// pages
$gres = sql_query("SELECT * FROM ".$db_prefix."page_group WHERE cat_list='1' ORDER BY group_title");
while ($grow = sql_fetch_array($gres)) {
    $output .= "<li><a href=\"#\">$grow[group_title]</a>\n<ul>\n";
    $res = sql_query("SELECT * FROM ".$db_prefix."page WHERE page_list='1' AND group_id='$grow[group_id]' ORDER BY page_title");
    while ($row = sql_fetch_array($res)) {
        if ($config['enable_adp'] && $row['permalink']) {
            $url = $row['permalink'];
        } else {
            $url = "page.php?pid=$row[page_id]";
        }
        $output .= "<li><a href=\"$url\">$row[page_title]</a>\n<ul>\n";
        $output .= "</ul>\n</li>\n";
    }
    $output .= "</ul>\n</li>\n";
}

// directory & cats
foreach ($dir_info['structure'] as $k => $v) {
    get_dir_info($k);
    // print_r ($dir_info[$k]);
    $output .= '<li><a href="'.$dir_info[$k]['dir_inf']['url'].'">'.$dir_info[$k]['dir_inf']['dir_title'].'</a></li>';
    $output .= $dir_info[$k]['cat_structure_html'];
    $output .= '</li>';
}

// end
$output .= "</ul>\n";
$txt['the_sitemap'] = str_replace("<ul>\n</ul>", '', $output);
$txt['main_body'] = quick_tpl(load_tpl('sitemap.tpl'), $txt);
generate_html_header("$config[site_name] $config[cat_separator] Site Map");
flush_tpl('site');
