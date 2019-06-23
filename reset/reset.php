<?php
// part of qEngine

// ATTENTION!
// RESET.PHP is used to reset all setting to original setting (first installation), it will remove current product,
// category, review, and other databases. Never call this file if you don't need it! -only works if demo_mode on

if ($config['demo_mode']) {
    // insert data
    $sql = '';
    $cmd_sql = array();
    $zp = gzopen($config['demo_path'].'/reset.sql', "r");
    while ($j = gzgets($zp, 4096)) {
        $sql .= $j;
    }
    gzclose($zp);

    splitSqlFile($cmd_sql, $sql);
    foreach ($cmd_sql as $val) {
        $val = str_replace('__PREFIX__', $db_prefix, $val);
        sql_query($val);
    }

    // delete uploaded files
    $folders = array('file', 'image', 'thumb', 'listing', 'listing_thumb');
    foreach ($folders as $val) {
        $files = get_file_list("./public/$val");
        foreach ($files as $v2) {
            if ($v2 != 'index.html') {
                unlink("./public/$val/$v2");
            }
        }
    }

    // copy demo files
    foreach ($folders as $val) {
        $files = get_file_list($config['demo_path']."/$val");
        foreach ($files as $v2) {
            copy($config['demo_path']."/$val/$v2", "./public/$val/$v2");
        }
        @chmod("./public/$val/$v2", 0644);
    }

    // create user
    $admin_passwd = qhash('admin');
    sql_query("INSERT INTO ".$db_prefix."user (`user_id`, `user_passwd`, `user_email`, `user_level`, `user_since`, `admin_level`) VALUES ('admin', '$admin_passwd', 'demo@c97.net', '5', '$sql_today', '5')");

    // update menu
    sql_query("UPDATE ".$db_prefix."menu_set SET menu_cache=REPLACE(menu_cache, '__SITE__', '$config[site_url]')");

    // update autoexec
    sql_query("UPDATE ".$db_prefix."config SET config_value='$sql_today' WHERE config_id='last_autoexec' LIMIT 1");

    // redirect
    redir($config['site_url']);
}
