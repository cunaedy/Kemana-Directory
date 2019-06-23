<?php
// either from mod_ini or mod_raw (inline mod)
if (empty($mod_ini['menu'])) {
    $mod_ini['menu'] = $mod_raw;
}
$m = empty($mod_ini['menu']) ? '' : $mod_ini['menu'];
global $qmenu_cache;
if (!isset($qmenu_cache)) {
    $qmenu_cache = array();
    $res = sql_query("SELECT idx, menu_id, menu_cache FROM ".$db_prefix."menu_set");
    while ($row = sql_fetch_array($res)) {
        $qmenu_cache[$row['menu_id']]['content'] = trim($row['menu_cache']);
        $qmenu_cache[$row['menu_id']]['idx'] = $row['idx'];
    }
}

if (isset($qmenu_cache[$m])) {
    $output = $qmenu_cache[$m]['content'];

    // sub menu?
    $search = preg_match_all("/\[\[sm:([a-zA-Z0-9_]+)\]\]/", $qmenu_cache[$m]['content'], $matches);
    foreach ($matches[1] as $val) {
        $f1 = substr($qmenu_cache[$val]['content'], strpos($qmenu_cache[$val]['content'], "\n") + 6);
        $f = substr($f1, 0, strrpos($f1, "</ul>") - 6);
        $output = str_replace('<a href="[[sm:'.$val.']]">#</a>', $f, $output);
    }
} else {
    $output = "<!-- menu_id $m not found! -->";
}

$mod_content_edit_url = $config['site_url'].'/'.$config['admin_folder'].'/menu_man.php?cmd=design&amp;midx='.$qmenu_cache[$m]['idx'];
