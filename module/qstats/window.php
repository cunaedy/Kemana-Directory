<?php
// update hits
$sql_today = date('Y-m-d');
if ($module_config['qstats']['stats_last_update'] != $sql_today) {
    sql_query("INSERT IGNORE INTO ".$db_prefix."stats_daily SET stats_date='$sql_today'");
    update_mod_config('qstats', 'stats_last_update', $sql_today);
}
sql_query("UPDATE ".$db_prefix."stats_daily SET stats_hit=stats_hit+1 WHERE stats_date='$sql_today' LIMIT 1");

// update visits
// -- (1) delete last 30 minutes
$last30 = time() - 1800;
sql_query("DELETE FROM ".$db_prefix."stats_ip WHERE last_update < $last30");

// -- (2) find ip address
$ip = get_ip_address();
$row = sql_qquery("SELECT * FROM ".$db_prefix."stats_ip WHERE ip_address='$ip' LIMIT 1");
if (empty($row)) {
    sql_query("UPDATE ".$db_prefix."stats_daily SET stats_visit=stats_visit+1 WHERE stats_date='$sql_today' LIMIT 1");
    sql_query("INSERT IGNORE INTO ".$db_prefix."stats_ip SET ip_address='$ip', last_update=UNIX_TIMESTAMP()");
}

// online users -- we don't use ip_config_count, as it takes too long to refresh (2 hours), instead we use stats_ip which is updated every 30 minutes
$row = sql_qquery("SELECT COUNT(*) FROM ".$db_prefix."stats_ip LIMIT 1");
if ($row[0] == 1) {
    $output = 'There is '.num_format($row[0]).' user online.';
} else {
    $output = 'There are '.num_format($row[0]).' users online.';
}
