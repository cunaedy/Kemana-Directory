<?php
require './../includes/admin_init.php';
admin_check('site_setting');
AXSRF_check();

// demo mode?
if ($config['demo_mode']) {
    admin_die('demo_mode');
}

// exclusion - config_id here won't be overwritten
$excluded = array('ke_version');

// get param
$res = sql_query("SELECT config_id FROM ".$db_prefix."config WHERE group_id='ke' AND LEFT (config_id, 1) != '_'");
while ($row = sql_fetch_array($res)) {
    ${$row['config_id']} = post_param($row['config_id'], '', 'html');
}

// update db
$res = sql_query("SELECT config_id FROM ".$db_prefix."config WHERE group_id='ke' AND LEFT (config_id, 1) != '_'");
while ($row = sql_fetch_array($res)) {
    if (!in_array($row['config_id'], $excluded)) {
        sql_query("UPDATE ".$db_prefix."config SET config_value='{${$row['config_id']}}' WHERE config_id='$row[config_id]' LIMIT 1");
    }
}

// rebuild cache!
qcache_clear('everything');
sql_query("UPDATE ".$db_prefix."language SET lang_value='' WHERE lang_key='_config:cache' LIMIT 1");

admin_die('admin_ok');
