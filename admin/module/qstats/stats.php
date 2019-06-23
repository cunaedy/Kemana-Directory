<?php
// part of qEngine
admin_check(3);

$cmd = get_param('cmd');
$p = get_param('p');
$start = date_param('start', 'get');
$rnd = random_str(16);

switch ($cmd) {
    case 'detail_year':
        $tpl_mode = 'detail_year';
        $txt['block_list'] = ''; $total = $tpv = $tv = 0;
        $tpl = load_tpl('adm', 'qstats.tpl');
        $chart_x = $chart_y1 = $chart_y2 = array();

        // date
        $res = sql_query("SELECT MAX(stats_date) AS max_year, MIN(stats_date) AS min_year FROM ".$db_prefix."stats_daily LIMIT 1");
        $row = sql_fetch_array($res);
        $min = substr($row['min_year'], 0, 4);
        $max = substr($row['max_year'], 0, 4);
        if ($min < 2013) {
            $min = date('Y');
        }
        if (empty($max) || ($max < 2013)) {
            $max = date('Y');
        }

        for ($ye = $min; $ye <= $max; $ye++) {
            $ye_end = "$ye-12-31";
            $ye_start = "$ye-1-1";
            $res2 = sql_query("SELECT SUM(stats_hit) AS hits, SUM(stats_visit) AS visits FROM ".$db_prefix."stats_daily WHERE 
							   stats_date >= '$ye_start' AND stats_date <= '$ye_end' LIMIT 1");
            $row2 = sql_fetch_array($res2);
            $tpv = $tpv + $row2['hits'];
            $tv = $tv + $row2['visits'];

            $row['date'] = $ye;
            $row['pageview'] = empty($row2['hits']) ? 0 : num_format($row2['hits']);
            $row['visit'] = empty($row2['visits']) ? 0 : num_format($row2['visits']);
            $row['tpv'] = num_format($tpv);
            $row['tv'] = num_format($tv);

            $chart_x[] = $ye;
            $chart_y1[] = empty($row2['hits']) ? 0 : $row2['hits'];
            $chart_y2[] = empty($row2['visits']) ? 0 : $row2['visits'];

            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        $txt['chart_x'] = implode(',', $chart_x);
        $txt['chart_y1'] = implode(',', $chart_y1);
        $txt['chart_y2'] = implode(',', $chart_y2);

        $txt['start_date'] = date_form('start', date('Y'), 0, 0, $start);
        $txt['tpv'] = num_format($tpv);
        $txt['tv'] = num_format($tv);
        $txt['total'] = num_format($total, 0, 1);
        
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
    
    
    case 'detail_month':
        $tpl_mode = 'detail_month';
        $txt['block_list'] = ''; $total = $tpv = $tv = 0;
        $tpl = load_tpl('adm', 'qstats.tpl');

        // date
        if (empty($start)) {
            $start = date('Y');
        }
        $chart_x = $chart_y1 = $chart_y2 = array();

        for ($mo = 1; $mo <= 12; $mo++) {
            $mo_end = "$start-$mo-31";
            $mo_start = "$start-$mo-1";
            
            $res2 = sql_query("SELECT SUM(stats_hit) AS hits, SUM(stats_visit) AS visits FROM ".$db_prefix."stats_daily WHERE 
								stats_date >= '$mo_start' AND stats_date <= '$mo_end' LIMIT 1");
            $row2 = sql_fetch_array($res2);
            $tpv = $tpv + $row2['hits'];
            $tv = $tv + $row2['visits'];

            $row['mo'] = $mo;
            $row['ye'] = substr($mo_start, 0, 4);
            $row['date'] = date('F', mktime(0, 0, 0, $mo, 1, 2000));
            $row['pageview'] = empty($row2['hits']) ? 0 : num_format($row2['hits']);
            $row['visit'] = empty($row2['visits']) ? 0 : num_format($row2['visits']);
            
            $chart_x[] = '"'.date('M', strtotime($mo_start)).'"';
            $chart_y1[] = empty($row2['hits']) ? 0 : $row2['hits'];
            $chart_y2[] = empty($row2['visits']) ? 0 : $row2['visits'];

            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        $txt['chart_x'] = implode(',', $chart_x);
        $txt['chart_y1'] = implode(',', $chart_y1);
        $txt['chart_y2'] = implode(',', $chart_y2);
        $txt['total'] = num_format($total, 0, 1);
        $txt['tpv'] = num_format($tpv);
        $txt['tv'] = num_format($tv);
        $txt['start_date'] = date_form('start', date('Y'), 0, 0, $start);
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
    
    
    default:
        $tpl_mode = 'detail_day';
        $txt['block_list'] = ''; $total = $tpv = $tv = 0;
        $tpl = load_tpl('adm', 'qstats.tpl');

        // date
        if (empty($start)) {
            $start = date('Y-m');
        }
        $ed = date('t', mktime(0, 0, 0, substr($start, 5, 2), 1, substr($start, 0, 4)));
        $chart_x = $chart_y1 = $chart_y2 = array();

        for ($dd = 1; $dd <= $ed; $dd++) {
            $date = $start.'-'.$dd;
            $row['ye'] = substr($date, 0, 4);
            $row['mo'] = substr($date, 5, 2);
            $row['da'] = substr($date, 8, 2);

            $res2 = sql_query("SELECT * FROM ".$db_prefix."stats_daily WHERE stats_date = '$date' LIMIT 1");
            $row2 = sql_fetch_array($res2);
            $tpv = $tpv + $row2['stats_hit'];
            $tv = $tv + $row2['stats_visit'];
            $row['date'] = convert_date($date);
            $row['pageview'] = empty($row2['stats_hit']) ? 0 : num_format($row2['stats_hit']);
            $row['visit'] = empty($row2['stats_visit']) ? 0 : num_format($row2['stats_visit']);

            $chart_x[] = "\"$dd\"";
            $chart_y1[] = empty($row2['stats_hit']) ? 0 : $row2['stats_hit'];
            $chart_y2[] = empty($row2['stats_visit']) ? 0 : $row2['stats_visit'];

            $txt['block_list'] .= quick_tpl($tpl_block['list'], $row);
        }

        $txt['chart_x'] = implode(',', $chart_x);
        $txt['chart_y1'] = implode(',', $chart_y1);
        $txt['chart_y2'] = implode(',', $chart_y2);
        $txt['start_date'] = date_form('start', date('Y'), 1, 0, $start);
        $txt['tpv'] = num_format($tpv);
        $txt['tv'] = num_format($tv);
        $txt['total'] = num_format($total, 0, 1);
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
