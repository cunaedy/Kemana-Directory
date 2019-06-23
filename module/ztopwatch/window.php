<?php
// end stopwatch
$time_end = getmicrotime();
$content = $enc = '';
$total_mysql_query = num_format($config['total_mysql_query']);
$process_time = num_format($time_end - $config['time_start'], 3);

// we don't need no tpl!
$output = "Generated in $process_time second | $total_mysql_query queries";

// output must be contained in $output
