<?php
if (empty($mod_ini['page_id']) && $inline) {
    $mod_ini['page_id'] = $mod_raw;
    $mod_ini['body'] = 1;
}

// get param
// $var = mod_param (id, 'default value')
$gid = mod_param('group_id');
$cid = mod_param('cat_id');
$page_id = mod_param('page_id');
$title = mod_param('title');
$body = mod_param('body');
$img = mod_param('img');
$thumb = mod_param('thumb');
$link = mod_param('link');
$date = mod_param('date');
$time = mod_param('time');
$summary = mod_param('summary');
$more = mod_param('more');
$orderby = mod_param('orderby', 'page_title');
$sort = mod_param('sort', 'ASC');
$style = mod_param('style', 'default');
$limit = mod_param('limit', 10);
$class = mod_param('cssclass');
$sectiontpl = mod_param('sectiontpl');
$offset = mod_param('offset', 0);
$hr = mod_param('hr');

// get page
if ($page_id) {
    if (!$limit) {
        $limit = substr_count($page_id, ',') + 1;
    }
    $sql = "page_id IN ($page_id)";
} elseif ($cid) {
    $sql = "cat_id IN ($cid)";
} elseif ($gid) {
    $sql = "group_id = '$gid'";
} elseif (empty($page_id) && empty($gid) && empty($cid)) {
    $sql = '1=1';
}

// output header
$output = '';

// only display listed_page in list mode
if (empty($page_id)) {
    $sql .= " AND page_list != 0 AND page_status = 'P'";
}

// custom block?
$tpl = '';
if ($sectiontpl) {
    $tpl = $tpl_section[$sectiontpl];
} else {
    $tpl = '<div class={$class}>';
    if ($title) {
        $tpl .= '<h1>{$page_title}</h1>';
    }
    if ($img) {
        $tpl .= '<div>{$page_image}</div>';
    }
    if ($thumb) {
        $tpl .= '<div style="float:left; margin-right:5px; margin-bottom:5px">{$page_thumb}</div><div style="float:left; text-align:left">';
    }
    if ($body) {
        $tpl .= '<div>{$page_body}</div>';
    }
    if ($summary) {
        $tpl .= '<div>{$page_summary}</div>';
    }
    if ($date) {
        $tpl .= '<p>Posted on: {$page_date}</p>';
    }
    if ($more) {
        $tpl .= '<span class="glyphicon glyphicon-chevron-right"></span> <a href="{$url}">More</a>';
    }
    if ($thumb) {
        $tpl .= '</div><div style="clear:both"></div>';
    }
    if (($limit == 1) && (!$hr)) {
        $tpl .= '<hr />';
    }
    $tpl .= '</div>';
}

// output body
$output = '';
if ($limit) {
    $res = sql_query("SELECT * FROM ".$db_prefix."page WHERE $sql ORDER BY $orderby $sort LIMIT $offset, $limit");
} else {
    $res = sql_query("SELECT * FROM ".$db_prefix."page WHERE $sql");
}
while ($row = sql_fetch_array($res)) {
    switch ($style) {
        // simple list (not all features are supported!)
        case 'list':
            $row['page_thumb'] = $row['page_image'] ? "<img src=\"$config[site_url]/public/image/$row[page_image]\" width=\"50\" alt=\"$row[page_title]\" /> " : '';

            if ($config['enable_adp'] && $row['permalink']) {
                $url = $config['site_url'].'/'.$row['permalink'];
            } else {
                $url = $config['site_url']."/page.php?pid=$row[page_id]";
            }
            $output .= "<li>";
            if ($thumb) {
                $output .= $row['page_thumb'].' ';
            }
            if ($title) {
                $output .= "<a href=\"$url\">$row[page_title]</a>";
            }
            $output .= "</li>\n";
        break;

        default:
            $row['class'] = $class ? $class : 'page_gallery';
            $row['page_thumb'] = $row['page_image'] ? "<img src=\"$config[site_url]/public/image/$row[page_image]\" width=\"100\" alt=\"$row[page_title]\" />" : '';
            $row['page_image'] = $row['page_image'] ? "<img src=\"$config[site_url]/public/image/$row[page_image]\" alt=\"$row[page_title]\">\n" : '';
            $row['page_summary'] = line_wrap(strip_tags($row['page_body']));
            $row['page_date'] = convert_date($row['page_date'], 1);
            $row['url'] = ($config['enable_adp'] && $row['permalink']) ? $config['site_url'].'/'.$row['permalink'] : $config['site_url']."/page.php?pid=$row[page_id]";
            $row['page_title'] = $link ? "<a href=\"$row[url]\">$row[page_title]</a>" : $row['page_title'];
            $row['page_image'] = $link ? "<a href=\"$row[url]\">$row[page_image]</a>" : $row['page_image'];
            $row['page_thumb'] = $link ? "<a href=\"$row[url]\">$row[page_thumb]</a>" : $row['page_thumb'];
            $output .= quick_tpl($tpl, $row);
        break;
    }
}

// output
if ($style == 'list') {
    $output = "<ul class=\"list_1\">\n$output</ul>";
} else {
    // run inline module
    $output = preg_replace_callback("/{qemod:(.*?)}/is", 'init_module', $output);
}

$output .= "<div style=\"clear:both\"></div>";

// module content editor
$mod_content_edit_url = $config['site_url'].'/'.$config['admin_folder'].'/page.php?id='.$page_id;
