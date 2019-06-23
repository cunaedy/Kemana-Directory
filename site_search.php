<?php
// since qEngine 12, qSearch has been removed. If you modules need qSearch, you have to do it manually.
require_once './includes/user_init.php';
$mod_id = get_param('mod_id');
$p = get_param('p', 1);
$query = get_param('query');

switch ($mod_id) {
    case 'mycustomsearch':
        // insert your custom search code below!
    break;


    default:
        // simple page search
        if (empty($query)) {
            msg_die(sprintf($lang['msg']['echo'], $lang['l_search_no_result']));
        }
        // load tpl
        $tpl = load_tpl('site_search.tpl');
        $txt['block_page'] = '';

        $w = array();
        $w[] = create_where('page_title', $query);
        $w[] = create_where('page_body', $query);
        $w = implode(' OR ', $w);
        $page_status_sql = create_page_status_sql();

        // perform search
        $i = 0;
        $foo = sql_multipage($db_prefix.'page', 'permalink, page_id, page_title, page_body', "($page_status_sql) AND ($w) AND (page_list=1)", '', $p);
        foreach ($foo as $row) {
            $i++;
            if ($config['enable_adp'] && $row['permalink']) {
                $row['page_url'] = $row['permalink'];
            } else {
                $row['page_url'] = "page.php?pid=$row[page_id]";
            }
            $row['title'] = strip_tags($row['page_title']);
            $row['body'] = line_wrap(strip_tags($row['page_body']));
            $txt['block_page'] .= quick_tpl($tpl_block['page'], $row);
        }
        if (!$i) {
            $no_result = true;
        } else {
            $no_result = false;
        }

        $txt['mod_id'] = $mod_id;
        $txt['query'] = stripslashes($query);
        $txt['main_body'] = quick_tpl(load_tpl('site_search.tpl'), $txt);
        generate_html_header("$config[site_name] $config[cat_separator] Site Search");
        flush_tpl();
    break;
}
