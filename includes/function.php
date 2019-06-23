<?php
// part of qEngine
// copyright (c) C97.net, usage of this script outside C97 is strictly prohibited!
// please write to us for licensing: contact@c97.net

/* Strucure:
// Description
// Parameters - if no info, means the parameter is self explanatory
// Sample of usage - if no sample, means it's quite easy (title is self explanatory)
// Used in (this function is used by ??) - if no info, means used by many ---> used_by only indicates that the function is used within qEngine, so it may be used by other script not listed here
*/

/* ------- ( GENERAL FUNCTIONS ) ------- */


// sending message and just die
function msg_die($msg_txt, $url = '')
{
    global $config, $inc_folder, $lang;
    if ($msg_txt == 'ok') {
        $msg_txt = $lang['msg']['ok'];
    }
    require($inc_folder.'/msg.php');
    die();
}


// like msg_die, but in popup template
function popup_die($msg_txt, $url = '')
{
    global $config, $inc_folder;
    $popup = true;
    require($inc_folder.'/msg.php');
    die();
}

// sending message and just die (no url required, user must press <back> instead)
function fullpage_die($msg_txt, $admin = false)
{
    global $config, $inc_folder;
    $full = true;
    require($inc_folder.'/msg.php');
    die();
}


function just_die($title, $msg_txt = '')
{
    html_header();
    if (!empty($msg_txt)) {
        echo "<div class=\"well\"><h1>$title</h1><p>$msg_txt</p></div>";
    } else {
        echo "<div class=\"well\"><p>$title</p></div>";
    }
    html_footer();
    die;
}



// generate random string
// param: $l = string length
//        $lower = 1 -> lower case only (i.e: abcdef). use $lower = 0 for mixed case (i.e: AbCdEf)
//        $mode = 1 -> 0-9, A-F; $mode = 0 or 2 -> 0-9, A-Z; $mode 3 => 0-9, A-Z, symbols
function random_str($len, $lower = 1, $mode = 1)
{
    if ($mode == 1) {
        $ch = "ABCDEF1234567890";
    } elseif (($mode == 0) || ($mode == 2)) {
        $ch = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrtuvwxyz1234567890";
    } else {
        $ch = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrtuvwxyz1234567890`~!@#$%^*()_+-{}[]:;?,./";
    }

    $l = strlen($ch) - 1;
    $str = "";
    for ($i=0; $i < $len; $i++) {
        $x = rand(0, $l);
        $str .= $ch[$x];
    }

    if ($lower) {
        $str = strtolower($str);
    }
    return $str;
}


// get paramenter from GET, POST or COOKIE ... >>> see also date_param()
// $mode = 'noslash' = ' & " will not be slashed !!!!
//         'nohtml' = remove all HTML tags, ' & " -> \' & \"
//         'filterhtml' = remove selected HTML tags (defined in config), '
//         'html' = allow html (be ware of XSS)
// DEFAULT = add slash, convert html, sql ready
function filter_param($param, $mode = '')
{
    global $config;
    $html = 0;
    if (!$config['gpc_quotes']) {
        $param = addslashes($param);
    }

    $param = trim($param);
    $cmd = explode(" ", $mode);
    reset($cmd);
    while (list($key, $cm) = each($cmd)) {
        if ($cm == 'noslash') {
            $param = stripslashes($param);
        }
        if ($cm == 'nohtml') {
            $param = strip_tags($param);
        }
        if ($cm == 'filterhtml') {
            $param = strip_tags($param, $config['allowed_html_tags']);
            $html = 1;
        }
        if ($cm == 'html') {
            $html = 1;
        }
        if (($cm == 'rte') && ($config['wysiwyg'])) {
            $html = 1;
        }
        if (($cm == 'rte') && (!$config['wysiwyg'])) {
            $html = 0;
        }
    }

    if ($html) {
        return $param;
    } else {
        return htmlspecialchars($param, ENT_QUOTES);
    }
}


// get_param, post_param & cookie_param will extract vars => WE CAN'T TRY EXTERNAL INPUT (incl. COOKIE's)
// $var_name -> if integer will extract in this fashion: index.php?var1,var2,var3 (0: var1,var2,var3; 1: var1; 2: var2...)
// $mode -> see filter_param ()
// default: in sql ready (' -> \'), convert all HTML tags (" -> &quot;)!
function get_param($var_name, $default = '', $mode = '')
{
    if (is_integer($var_name)) {
        if (!isset($_SERVER['QUERY_STRING'])) {
            return $default;
        }
        $p = $_SERVER['QUERY_STRING'];
        $g = explode(',', $p);
        array_unshift($g, $p);

        if (empty($g[$var_name])) {
            return $default;
        }
        $v = $g[$var_name];
    } else {
        if (!isset($_GET[$var_name])) {
            return $default;
        }
        $v = $_GET[$var_name];
    }

    $v = filter_param($v, $mode);
    return trim($v);
}


// like get_param but for POST method
function post_param($var_name, $default = '', $mode = '')
{
    if (!isset($_POST[$var_name])) {
        return $default;
    } else {
        $v = $_POST[$var_name];
        $v = filter_param($v, $mode);
        return trim($v);
    }
}


// like get_param but for COOKIE
function cookie_param($var_name)
{
    if (!isset($_COOKIE[$var_name])) {
        return '';
    } else {
        $v = $_COOKIE[$var_name];
        $v = filter_param($v, '');
        return trim($v);
    }
}


// like get_param but for SESSION
function session_param($var_name)
{
    if (!isset($_SESSION[$var_name])) {
        return false;
    } else {
        $v = $_SESSION[$var_name];
        return trim($v);
    }
}


// like get_param but to get MODULE PARAMETER (eg: <!-- BEGINMODULE xyz --> param1 = value1 <!-- ENDMODULE -->)
// used in: mostly by modules
function mod_param($var_name, $default = '')
{
    global $mod_ini;
    if (empty($mod_ini[$var_name])) {
        return $default;
    } else {
        return $mod_ini[$var_name];
    }
}


function update_mod_config($mod_id, $config_id, $config_value)
{
    global $db_prefix;
    return sql_query("UPDATE ".$db_prefix."config SET config_value='$config_value' WHERE group_id='mod_".$mod_id."' AND config_id='$config_id' LIMIT 1");
}

// cut long line to short line, but cut it nicely!
// regular cut: "this is a very bor..."
// line_wrap: "this is a very ..."
function line_wrap($txt, $l = 200)
{
    if ($l < 1) {
        return $txt;
    }
    $ori = strlen($txt);
    $txt = str_replace("\n", " ", $txt);
    $txt = wordwrap($txt, $l, "\n", 1);
    $i = strpos($txt, "\n");
    if (empty($i)) {
        $i = $l;
    }
    $foo = substr($txt, 0, $i);
    if (strlen($foo) < $ori) {
        $foo .= '&hellip;';
    }
    return $foo;
}


// get microtime (ie. milisecond)
function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


// format num using predefined $config
// $currency = 1 : display currency formatting (ie. $config['num_*'])
// $currency = 2 : display currency formatting without symbol
// $zeros = display 0 as 0 or other character (eg: -)
function num_format($number, $comma = 0, $currency = 0, $zeros = 0)
{
    global $config;

    if ($currency) {
        if (empty($config['num_thousands_sep'])) {
            $config['num_thousands_sep'] = ' ';
        }
        if ($number < 0) {
            $neg = true;
        } else {
            $neg = false;
        }
        if ($zeros && (empty($number) || $number == 0)) {
            return $zeros;
        }
        if (!$comma) {
            $comma = $config['num_decimals'];
        }
        $val = number_format(abs($number), $comma, $config['num_dec_point'], $config['num_thousands_sep']);
        if ($currency == 1) {
            if ($config['num_curr_pos']) {
                $val .= $config['num_currency'];
            } else {
                $val = $config['num_currency'].$val;
            }
        }
        if ($neg) {
            $val = '-'.$val;
        }
    } else {
        if ($zeros && empty($number)) {
            return $zeros;
        }
        $val = number_format($number, $comma, $config['num_dec_point'], $config['num_thousands_sep']);
    }

    return $val;
}


// split string or array.... eg. string '1;abc;2;def' -> array (array ('1', 'abc'), array ('2', 'def'))
// $as_key: use odd value as key (array[1] = 'abc'; array[2] = 'def')
// used in: load_form() & form.php
function array_split($source, $divider = ';', $as_key = 0)
{
    $ok = true;
    $i = 0;
    $output = array();

    if (!is_array($source)) {
        if (substr($source, -1) != $divider) {
            $source .= $divider;
        }
        $source = explode($divider, $source);
    }

    reset($source);
    while ($ok) {
        $i++;
        if ($as_key) {
            $k = current($source);
            $v = next($source);
            $output[$k] = $v;
        } else {
            $output[1][$i] = current($source);
            $output[2][$i] = next($source);
        }
        if (!next($source)) {
            $ok = false;
        }
    }
    return $output;
}


// clean an array (single or multi dimension) from empty (0, '') values
// (c) alessandronunes at gmail dot com, based on Nimja's func (php.net)
// used in: real_url ()
function array_clean($array, $reset_key = false)
{
    $multi_array = false;
    foreach ($array as $index => $value) {
        if (is_array($array[$index])) {
            $array[$index] = array_clean($array[$index]);
            $multi_array = true;
        }
        if (empty($value)) {
            unset($array[$index]);
        }
    }
    if ($reset_key && $multi_array) {
        return array_map('array_values', $array);
    } elseif ($reset_key && !$multi_array) {
        return array_values($array);
    } else {
        return $array;
    }
}


// prepare random seed
// used in: usually in init.php
function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}


// merge 2 arrays to 1 array, using array1 as key, array2 as val => new_array[array1] = array2
// used in: mostly by qadmin
function array_pair($array1, $array2, $first = '')
{
    $t1 = array_values($array1);
    $t2 = array_values($array2);
    $t = array();
    $s = max(count($t1), count($t2));
    if (!empty($first)) {
        $t[0] = $first;
    }
    for ($i = 0; $i <= $s - 1; $i++) {
        $k = empty($t1[$i]) ? 0 : $t1[$i];
        $v = empty($t2[$i]) ? '' : $t2[$i];
        if (empty($k)) {
            $t[] = $v;
        } else {
            $t[$k] = $v;
        }
    }
    return $t;
}


// clean a url from some GET queries (eg. a.php?o=1&p=2&q=3 => clean 'o', 'p' => a.php?q=3)
// $needle = can be array ('o', 'p') or string
// used in: mostly by qadmin
function clean_get_query($needle, $include_script_name = true, $script_name = '')
{
    $foo = '';

    // get active php
    if (empty($script_name)) {
        $t = parse_url(urldecode(cur_url()));
    } else {
        $t = parse_url($script_name);
    }
    if (empty($t['scheme'])) {
        $t['scheme'] = 'http';
    }
    if (empty($t['host'])) {
        $t['host'] = '';
    }
    $script = $t['scheme'].'://'.$t['host'].$t['path']; //dirname ($t['path']).'/'.basename ($t['path']);
    if (!is_array($needle)) {
        $needle = array($needle);
    }
    $needle[] = 'permalink_request';

    // filter
    if (empty($script_name)) {
        foreach ($_GET as $key => $val) {
            if (!is_array($val)) {
                $val = urlencode($val);
            }
            if (!in_array($key, $needle)) {
                $foo .= "&amp;$key=$val";
            }
        }
    } else {
        if (!empty($t['query'])) {
            parse_str($t['query'], $qArr);
        } else ($qArr = array());
        foreach ($qArr as $key => $val) {
            if (!is_array($val)) {
                $val = urlencode($val);
            }
            if (!in_array($key, $needle)) {
                $foo .= "&amp;$key=$val";
            }
        }
    }

    if ($include_script_name) {
        return $script.'?'.substr($foo, 5);
    } else {
        return substr($foo, 5);
    }
}


// get abs url from rel url
function real_url($absolute, $relative)
{
    $absolute = str_replace('%20', ' ', $absolute);
    $relative = str_replace('%20', ' ', $relative);
    $relative = str_replace('\\', '/', $relative);
    $dir = parse_url($absolute);	// explode abs url to path info

    $aparts = array_clean(explode("/", $dir['path']));	// split each path level (level1/level2/level3) to array
    $rparts = array_clean(explode("/", $relative));

    if ($relative[0] == '/') {
        $aparts = array();			// if $relavtive => absolute => remove aparts array
    } else {
        $foo = $rparts;				// otherwise, if current level = . => remove level; if .. => remove level and remove bottom level of abs path
        foreach ($foo as $i => $part) {
            if ($part == '.') {
                array_shift($rparts);
            }
            if ($part == '..') {
                array_pop($aparts);
                array_shift($rparts);
            }
        }
    }

    $apath = implode("/", $aparts);
    $path = implode("/", $rparts);

    $url = "";
    $url .= (!empty($dir['scheme'])) ? $dir['scheme'].'://' : '';
    $url .= (!empty($dir['user'])) ? $dir['user'] : '';
    $url .= (!empty($dir['pass'])) ? ':'.$dir['pass'] : '';
    $url .= (!empty($dir['user'])) ? '@' : '';
    $url .= (!empty($dir['host'])) ? $dir['host'].'/' : '';
    $url .= $apath;
    if (substr($url, -1, 1) != '/') {
        $url .= '/'.$path;
    } else {
        $url .= $path;
    }
    return $url;
}


// return current url (url encoded)
function cur_url($encode = true)
{
    global $config, $isPermalink;
    $g = get_param('permalink_request');

    $script = '';
    if ($isPermalink || !empty($g)) {
        $script = get_param('permalink_request');
        $isPermalink = true;
    }
    if (empty($script) && !empty($_SERVER['PHP_SELF'])) {
        $script = $_SERVER['PHP_SELF'];
    }					// should be working..
    if (empty($script) && !empty($_SERVER['SCRIPT_NAME'])) {
        $script = $_SERVER['SCRIPT_NAME'];
    }	// ..unless in command line
    if (empty($script) && !empty($_SERVER['SCRIPT_FILENAME'])) {
        $script = $_SERVER['SCRIPT_FILENAME'];
    }	// IIS & PHP 4.3.0
    if (empty($script)) {
        die('Your server is very weird! Contact us now!');
    }				// what the f__?!
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    if (empty($_SERVER["QUERY_STRING"])) {
        $_SERVER["QUERY_STRING"] = '';
    }
    if ($isPermalink) {
        $url = $script.'?'.$_SERVER['QUERY_STRING'];
    } else {
        $url = $protocol.$_SERVER['SERVER_NAME'].$script.'?'.$_SERVER['QUERY_STRING'];
    }
    if ($encode) {
        $url = urlencode($url);
    }
    return $url;
}


// mobile version
function check_mobile()
{
    // detect for mobile device (c)Russel Beattie -- http://www.russellbeattie.com/blog/mobile-browser-detection-in-php
    global $config;
    if (!$config['mobile_version']) {
        return false;
    }
    $isMobile = false;

    $op = empty($_SERVER['HTTP_X_OPERAMINI_PHONE']) ? '' : strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE']);
    $ua = empty($_SERVER['HTTP_USER_AGENT']) ? '' : strtolower($_SERVER['HTTP_USER_AGENT']);
    $ac = empty($_SERVER['HTTP_ACCEPT']) ? '' : strtolower($_SERVER['HTTP_ACCEPT']);
    $ip = get_ip_address();

    $mobile_def = array('sony', 'symbian', 'nokia', 'samsung', 'mobile', 'windows ce', 'epoc', 'opera mini', 'nitro', 'j2me', 'midp-', 'cldc-',
    'netfront', 'mot', 'up.browser', 'audiovox', 'blackberry', 'ericsson', 'panasonic', 'philips', 'sanyo', 'sharp', 'sie-', 'portalmmm', 'blazer',
    'avantgo', 'danger', 'palm', 'series60', 'palmsource', 'pocketpc', 'smartphone', 'rover', 'ipaq', 'au-mic', 'alcatel', 'ericy', 'up.link',
    'vodafone/', 'wap1.', 'wap2.');

    $bot_def = array('googlebot', 'mediapartners', 'yahooysmcm', 'baiduspider', 'msnbot', 'slurp', 'ask', 'teoma', 'spider', 'heritrix',
    'attentio',	'twiceler', 'irlbot', 'fast crawler', 'fastmobilecrawl', 'jumpbot', 'googlebot-mobile', 'yahooseeker', 'motionbot',
    'mediobot', 'chtml generic', 'nokia6230i/. fast crawler');

    if (strpos($ac, 'application/vnd.wap.xhtml+xml') !== false || $op != '') {
        $isMobile = true;
    }
    foreach ($mobile_def as $val) {
        if (strpos($ua, $val) !== false) {
            $isMobile = true;
        }
    }

    return $isMobile;
}


// Create a web friendly URL slug from a string.
// (c) copyright Copyright 2012 Sean Murphy. All rights reserved.
// http://iamseanmurphy.com/creating-seo-friendly-urls-in-php-with-url-slug/
function cleanForShortURL($toClean, $allowSpc = false)
{
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    if (function_exists('mb_convert_encoding')) {
        $toClean = str_replace('&#039;', "'", stripslashes(mb_convert_encoding((string)$toClean, 'UTF-8', mb_list_encodings())));
    } else {
        $toClean = str_replace('&#039;', "'", stripslashes($toClean));
    }

    // Replace non-alphanumeric characters with our delimiter
    if ($allowSpc) {
        $toClean = preg_replace('/[^\p{L}\p{Nd}\/\.]+/u', '-', $toClean);
    } else {
        $toClean = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $toClean);
    }

    // Remove duplicate delimiters
    $toClean = preg_replace('/(' . preg_quote('-', '/') . '){2,}/', '$1', $toClean);

    // Truncate slug to max. characters
    if (function_exists('mb_convert_encoding')) {
        $toClean = mb_substr($toClean, 0, 255, 'UTF-8');
    } else {
        $toClean = substr($toClean, 0, 255);
    }

    // Remove delimiter from ends
    $toClean = trim($toClean, '-');
    if (function_exists('mb_convert_encoding')) {
        return mb_strtolower($toClean, 'UTF-8');
    } else {
        return strtolower($toClean);
    }
}


// clean a csv string from excessive spaces & duplicates, eg: this, book,is ,book => this,book,is
// return cleaned string
function clean_csv($string)
{
    $str = array_clean(explode(',', strtolower($string)));
    foreach ($str as $k => $v) {
        $str[$k] = trim($v);
    }
    $str = implode(',', array_unique($str));
    return $str;
}


/* ------- ( MYSQL FUNCTIONS ) ------- */


// sending query to MySQL
function sql_query($sql, $debug = false)
{
    global $dbh, $config, $db_prefix, $debug_info;

    $config['total_mysql_query']++;

    if ($debug && $config['debug_mode']) {
        echo $sql.'<br />';
    }
    $debug_info['sql'][] = nl2br(chunk_split($sql, 120));
    if (!$result = mysqli_query($dbh, $sql)) {
        if ($config['debug_mode']) {
            echo '<h1>MySQL Error</h1>'.$sql;
            getCallingFunction();
        }
        die(mysqli_error($dbh));
    }
    return $result;
}


// get one line quickly
function sql_qquery($sql, $debug = 0)
{
    $res = sql_query($sql, $debug);
    return sql_fetch_array($res);
}


// input: $slash => add (keep) slash (\) for special chars, such as ' -> \'
//                  0 equal to stripslashes
//        $html  => allow html
//                  0 equal to strip_tags
function sql_fetch_array($res_id, $allow_html = 1, $slash = 1)
{
    global $config;
    $row = mysqli_fetch_array($res_id);

    if (!$slash && is_array($row)) {
        reset($row);
        while (list($key, $val) = each($row)) {
            $row[$key] = stripslashes($val);
        }
    }

    if (!$allow_html && is_array($row)) {
        reset($row);
        while (list($key, $val) = each($row)) {
            $row[$key] = strip_tags($val);
        }
    }
    return $row;
}


function sql_fetch_assoc($res_id, $allow_html = 1, $slash = 1)
{
    // we can use mysqli_fetch_assoc, but i need to reuse the codes in sql_fetch_array, so...
    $foo = sql_fetch_array($res_id, $allow_html = 1, $slash = 1);
    foreach ($foo as $k => $v) {
        if (is_int($k)) {
            unset($foo[$k]);
        }
    }
    return $foo;
}

// generate blank vars from a table (useful for creating empty fields in form)
function create_blank_tbl($tbl)
{
    global $db_name, $db_prefix;
    $row = array();
    $res = sql_query("SHOW COLUMNS FROM $tbl");
    while ($foo = sql_fetch_array($res)) {
        $row[$foo['Field']] = '';
    }
    return $row;
}


// create multipage sql result ... only display item #1 to #X in page Y (see $config['list_ipp'] & $config['list_ppp'])
// $table = table name | $columns = columns to select (use * or col_name, col_name, col_name) | $where = where query
// $order_by = order by query | $cur_page = current page | $script_name = script to handle list (not used?)
// $per_page = number of item per page (if empty -> $config['list_ipp'])
function sql_multipage($table, $columns, $where, $order_by, $cur_page, $script_name = '', $per_page = '', $debug = false)
{
    global $config, $txt, $lang, $tpl_block;

    $tmp = array();
    $i = 0;
    $p = $cur_page;

    if (empty($per_page)) {
        $ipp = $config['list_ipp'];
    } else {
        $ipp = $per_page;
    } // ipp = items per page
    if (!empty($where)) {
        $where = 'WHERE '.$where;
    }
    if (!empty($order_by)) {
        $order_by = 'ORDER BY '.$order_by;
    }

    // get total pages
    $res = sql_query("SELECT COUNT(*) AS total FROM $table $where");
    $row = sql_fetch_array($res);
    $total = $row['total'];
    $pages = ceil($total / $ipp); // number of pages of list

    // verify vars
    // if $p is not defined or $p > number_of_pages
    if (empty($p) or ($p > $pages) or ($p < 1)) {
        $p = "1";
    }
    $start = ($p-1) * $ipp;

    $sql = "SELECT $columns FROM $table $where $order_by LIMIT $start, $ipp";
    if ($debug && $config['debug_mode']) {
        echo $sql;
    }
    $res = sql_query($sql);
    while ($row = sql_fetch_array($res)) {
        $i++;
        $tmp[$i] = $row;
    }

    // generate page list
    $txt['pagination'] = generate_pagination($script_name, $pages, $p, $total);

    // done
    return $tmp;
}


// generate pagination
// $script_name = url link to use. this func will add '&p=xx' at the end of $base_url
function generate_pagination($script_name, $total_pages, $cur_page, $num_item)
{
    global $config, $tpl_block, $lang, $tpl_section;

    // clear $script_name from &p param
    $base_url = clean_get_query('p', true, $script_name);
    if (!strpos($base_url, '?') && !strpos($base_url, '&amp;')) {
        $base_url .= '?';
    }

    // template
    $tpl = load_tpl('var', $tpl_section['pagination']);
    $row['block_pagelist'] = '';

    $per_page = $config['list_ppp'];
    $mid = floor($per_page / 2);
    $page_string = $class_string = array();

    if ($total_pages <= 1) {
        return '';
    }

    if ($cur_page <= $mid) {
        $start = 1;
    } else {
        if ($cur_page + $mid > $total_pages) {
            $start = $total_pages - $per_page + 1;
        } else {
            $start = $cur_page - $mid;
        }
    }

    $finish = $start + $per_page - 1;
    if ($finish > $total_pages) {
        $finish = $total_pages;
    }
    if ($start < 1) {
        $start = 1;
    }
    for ($i = $start; $i <= $finish; $i++) {
        if ($i == $cur_page) {
            $page_string[] = "<a href=\"#\">$i</a>";
            $class_string[] = 'active';
        } else {
            $page_string[] = "<a href=\"$base_url&amp;p=$i\">$i</a>";
            $class_string[] = 'normal';
        }
    }

    // previous & next page link
    $pr = $cur_page - 1;
    $nx = $cur_page + 1;
    $row['pg_prev_class'] = $row['pg_next_class'] = $row['pg_top_class'] = $row['pg_last_class'] = 'normal';

    if ($cur_page > 1) {
        $row['pg_prev'] = "<a href=\"$base_url&amp;p=$pr\">".$lang['l_pp_prev']."</a>";
    } else {
        $row['pg_prev'] = "<a href=\"#\">".$lang['l_pp_prev']."</a>";
        $row['pg_prev_class'] = 'active';
    }

    if ($cur_page < $total_pages) {
        $row['pg_next'] = "<a href=\"$base_url&amp;p=$nx\">".$lang['l_pp_next']."</a>";
    } else {
        $row['pg_next'] = "<a href=\"#\">".$lang['l_pp_next']."</a>";
        $row['pg_next_class'] = 'active';
    }

    // first & last pages
    if ($cur_page == 1) {
        $row['pg_top'] = "<a href=\"#\">".$lang['l_pp_top']."</a>";
        $row['pg_top_class'] = 'active';
    } else {
        $row['pg_top'] = "<a href=\"$base_url&amp;p=1\">".$lang['l_pp_top']."</a>";
    }

    if ($cur_page == $total_pages) {
        $row['pg_last'] = "<a href=\"#\">".$lang['l_pp_last']."</a>";
        $row['pg_last_class'] = 'active';
    } else {
        $row['pg_last'] = "<a href=\"$base_url&amp;p=$total_pages\">".$lang['l_pp_last']."</a>";
    }

    // generate pagenumber
    foreach ($page_string as $k => $val) {
        $row['pp'] = $val;
        $row['class'] = $class_string[$k];
        $row['block_pagelist'] .= quick_tpl($tpl_block['pagelist'], $row);
    }

    // generate list content
    $row['base_url'] = html_unentities($base_url);
    $row['pg_current_page'] = $cur_page;
    $row['pg_total_pages'] = $total_pages;
    $row['pg_total_items'] = num_format($num_item);
    $pagelist = str_replace('?&amp;', '?', quick_tpl($tpl, $row));

    // return $pagelist;
    $pagelist .= '<!-- q E  i s  c r e at e d  b y  C 9 7 . n e t ( h t t p : / / w w w . c 9 7 . n e t ) --><!-- Contact us for more information -->';
    return $pagelist;
}


// create SQL's where query
function create_where($row, $query, $mode = 'AND')
{
    $where = array();
    $keyword = strtok($query, ' ');
    while ($keyword) {
        $where[] = "$row LIKE '%".$keyword."%'";
        $keyword = strtok(' ');
    }

    $where = implode(" $mode ", $where);
    return "($where)";
}


// from php.net by: php@dogpoop.cjb.net
// $line: the csv line to be split
// $delim: the delimiter to split by
// $removeQuotes: if this is false, the quotation marks won't be removed from the fields
// used in: load_form (), form.php
function csv_split($line, $delim=',', $removeQuotes=true)
{
    $fields = array();
    $fldCount = 0;
    $inQuotes = false;
    for ($i = 0; $i < strlen($line); $i++) {
        if (!isset($fields[$fldCount])) {
            $fields[$fldCount] = "";
        }
        $tmp = substr($line, $i, strlen($delim));
        if ($tmp === $delim && !$inQuotes) {
            $fldCount++;
            $i += strlen($delim)-1;
        } elseif ($fields[$fldCount] == "" && $line[$i] == '"' && !$inQuotes) {
            if (!$removeQuotes) {
                $fields[$fldCount] .= $line[$i];
            }
            $inQuotes = true;
        } elseif ($line[$i] == '"') {
            if ($i+1 < strlen($line) && $line[$i+1] == '"') {
                $i++;
                $fields[$fldCount] .= $line[$i];
            } else {
                if (!$removeQuotes) {
                    $fields[$fldCount] .= $line[$i];
                }
                $inQuotes = false;
            }
        } else {
            $fields[$fldCount] .= $line[$i];
        }
    }
    return $fields;
}

// page status sql
// create an sql for page_status based on user status (logged in, admin, etc)
function create_page_status_sql()
{
    global $config, $isLogin, $current_admin_level;
    $foo = array("page_status != 'D'");
    if (!$isLogin && !$config['allow_locked_page_list']) {
        $foo[] = "page_status != 'M'";
    }
    if (!$current_admin_level) {
        $foo[] = "page_status != 'A'";
    }
    $foo[] = "page_status != 'H'";
    return "(". implode(') AND (', $foo).")";
}


// to copy a row from & to same/different table
// $table_src = source table
// $table_tgt = target table
// $key = name of primary key field (eg page_id, item_id, idx, etc)
// $idx = the value of primary key (eg 1, 2, 3, ...)
// returns auto increment of new value
function sql_copy_row($table_src, $key, $idx, $table_tgt = '')
{
    global $dbh;
    $tmp = 'tmptable_'.random_str(3);
    if (!$table_tgt) {
        $table_tgt = $table_src;
    }
    sql_query("CREATE TEMPORARY TABLE $tmp SELECT * FROM $table_src WHERE $key = '$idx' LIMIT 1");
    sql_query("UPDATE $tmp SET $key = NULL");
    sql_query("INSERT INTO $table_tgt SELECT * FROM $tmp");
    $ret = mysqli_insert_id($dbh);
    sql_query("DROP TEMPORARY TABLE IF EXISTS $tmp");
    return $ret;
}


/* ------- ( DATE & TIME FUNCTIONS ) ------- */


// converting SQL Formatted date to HUMAN UNDERSTANDABLE & READABLE DATE (HURT)
// $sql_date = date in sql format (yyyy-mm-dd)
// $mode = [sql = sql formatted; 1 = dayname, monthname dd, yyyy; 0/else = mm/dd/yyyy]
// $days = [0 / blank = return specified date; X = return X days after specified date]
// and translate it to other language (if configured) ... still, much more easier than i though! REALLY!
function convert_date($sql_date, $mode = '0', $days = 0)
{
    global $config, $lang;
    if (($sql_date == '0000-00-00') && ($mode != 'int' && $mode != 'unix')) {
        return 'Invalid Date';
    }
    if (($sql_date == '0000-00-00') && ($mode == 'int' || $mode == 'unix')) {
        return false;
    }
    if (empty($sql_date)) {
        return false;
    }
    if (($sql_date == 'today') || ($sql_date == 'now')) {
        $sql_date = date('Y-m-d');
    }
    $thn = substr($sql_date, 0, 4);
    $bln = substr($sql_date, 5, 2);
    $tgl = substr($sql_date, 8, 2);
    $tglbener = mktime(0, 0, 0, $bln, $tgl, $thn);
    if (!checkdate($bln, $tgl, $thn)) {
        return false;
    }
    if ($days != 0) {
        $tglbener = $tglbener + ($days * 24 * 3600);
    }

    switch ((string) $mode) {
        case '0':
        case 'short':
         $tanggal = date($lang['l_short_date_format'], $tglbener);
        break;

        case '1':
        case 'long':
         $tanggal = date($lang['l_long_date_format'], $tglbener);
        break;

        case '2':
        case 'mini':
         $tanggal = date('d/m', $tglbener);
        break;

        case '3':
        case 'form':
         $tanggal = date($lang['l_form_date_format'], $tglbener);
        break;

        case 'sql':
         $tanggal = date('Y-m-d', $tglbener);
         return $tanggal;
        break;

        case 'int':
        case 'unix':
         return $tglbener;
        break;

        default:
         $tanggal = date($mode, $tglbener);
        break;
    }

    // translate (if configured)
    if ($config['multi_lang']) {
        $search = array_keys($lang['datetime']);
        $replace = array_values($lang['datetime']);
        return strtr($tanggal, array_combine($search, $replace));
    // return str_replace ($search, $replace, $tanggal);
    } else {
        return $tanggal;
    }
}


// verify SQL formatted date (yyyy-mm-dd)
function verify_date($sql_date)
{
    $thn = substr($sql_date, 0, 4);
    $bln = substr($sql_date, 5, 2);
    $tgl = substr($sql_date, 8, 2);
    $bener = checkdate($bln, $tgl, $thn);
    return $bener;
}


// verify time
function verify_time($time)
{
    return preg_match("#([0-1]{1}[0-9]{1}|[2]{1}[0-3]{1}):[0-5]{1}[0-9]{1}#", $time);
}


// calculate 'how many days have passed since...' (different between 2 dates)
// parameters are in SQL formatted date
function diff_date($sql_date1, $sql_date2 = 'now')
{
    if ($sql_date1 == "now") {
        $sql_date1 = date("Y/m/d", time());
    }
    if ($sql_date2 == "now") {
        $sql_date2 = date("Y/m/d", time());
    }

    $thn1 = substr($sql_date1, 0, 4);
    $bln1 = substr($sql_date1, 5, 2);
    $tgl1 = substr($sql_date1, 8, 2);

    $thn2 = substr($sql_date2, 0, 4);
    $bln2 = substr($sql_date2, 5, 2);
    $tgl2 = substr($sql_date2, 8, 2);

    $tanggal1 = mktime(0, 0, 0, $bln1, $tgl1, $thn1);
    $tanggal2 = mktime(0, 0, 0, $bln2, $tgl2, $thn2);

    $tanggal = ($tanggal2 - $tanggal1) / 86400;
    return (round($tanggal));
}


/* ------- ( USER AUTHENTICATION ) ------- */


// authorize user login
function authorize_user($user_id, $user_passwd)
{
    global $db_prefix, $current_user_id, $isLogin;

    // create security hash
    session_regenerate_id(true);
    $key = hash_hmac('sha256', random_str(32, 0, 0), time());
    $hash = hash_hmac('sha512', ~$user_passwd, $key);
    setcookie($db_prefix.'hash', $hash, 0, '/', cookie_domain());

    $_SESSION[$db_prefix.'isLogin'] = true;
    $_SESSION[$db_prefix.'user_id'] = $user_id;
    $_SESSION[$db_prefix.'password'] = $user_passwd;
    $_SESSION[$db_prefix.'key'] = $key;
    $_SESSION[$db_prefix.'auth'] = hash_hmac('sha512', $key, hash_hmac('sha512', $_SESSION[$db_prefix.'user_id'].get_ip_address(), $hash));

    // AXSRF
    $value = random_str(32);
    sql_query("UPDATE ".$db_prefix."user SET axsrf_token='$value' WHERE user_id='$user_id' LIMIT 1");
    $_SESSION[$db_prefix.'axsrf'] = $value;

    $current_user_id = $user_id;
    $isLogin = true;
}


// check if he's member
// return 1 = member; 0 = guest
function isMember()
{
    global $config, $db_prefix;
    $user_id = session_param($db_prefix.'user_id');
    $user_passwd = session_param($db_prefix.'password');
    $current_user_info = array();

    if (session_param($db_prefix.'isLogin')) {
        // 1. verify hash
        $hash = cookie_param($db_prefix.'hash');
        $check = hash_hmac('sha512', session_param($db_prefix.'key'), hash_hmac('sha512', session_param($db_prefix.'user_id').get_ip_address(), $hash));

        if (session_param($db_prefix.'auth') == $check) {
            // 2. verify user id & passwd SET -> member
            $row = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE user_id='$user_id' AND user_passwd = '$user_passwd' AND user_activation = '' LIMIT 1");
            if (!empty($row['user_id'])) {
                // save all user info in $current_user_info
                foreach ($row as $k => $v) {
                    if (!is_numeric($k)) {
                        $current_user_info[$k] = $v;
                    }
                }
                $current_user_info['ip'] = get_ip_address();
            } else {
                // invalid uid & pwd -> forced to logout
                kick_user();
            }
        } else {
            // invalid session -> forced to logout
            kick_user();
        }
    } else {
        // user not login
        if (empty($user_id) && empty($user_passwd)) { // new guest
            // create temporary username (session id)
            // guest temporary ID ... using '*' star, as star is not allowed for user ID
            $_SESSION[$db_prefix.'user_id'] = 'guest*'.random_str(32);
            $_SESSION[$db_prefix.'password'] = '';
        }
    }
    return $current_user_info;
}


// kick user (aka logout) by cleaning session
function kick_user($mode = 'user')
{
    global $config, $lang;

    // remove session
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(), '', 0, '/');
    session_regenerate_id(true);
    if ($mode == 'adm') {
        redir($config['site_url'].'/'.$config['admin_folder'].'/login.php');
    } elseif ($mode == 'pwd') {
        msg_die($lang['msg']['passwd_changed'], $config['site_url'].'/profile.php');
    } else {
        redir($config['site_url']);
    }
}


//-- get user detail
function get_user_info($usrnm = '')
{
    global $current_user_id, $isLogin, $db_prefix, $current_user_info;

    // if $usrnm not specified, get $usrnm from cookie ($username)
    if (empty($usrnm) && $isLogin) {
        return $current_user_info;
    } elseif (empty($usrnm) && !$isLogin) {
        $user = create_blank_tbl($db_prefix.'user');
    } else {
        $user = sql_qquery("SELECT * FROM ".$db_prefix."user WHERE user_id='$usrnm' LIMIT 1");
    }
    $user['ip'] = get_ip_address();
    return $user;
}


/* ------- ( HTML FUNCTIONS ) ------- */


// to generate <select> for a <form>, and automatically select the 'selected' value.
// $source = array of data for <select>
// $select_name = name for <select>
// $selected_value = selected value
// $first_line = should be NOT SELECTABLE option, such as '--PLEASE SELECT--'
// $addtl_option = can be javascript, like: onchange=document.forms[0].submit()
// special array key: --- to disable, *** to add <optgroup>, /// to add </optgroup>; don't forget to add an
//  index, eg: ---1, or ***a, or ///whatever.
function create_select_form($select_name, $source, $selected_value = '', $first_line = '', $disabled = 0, $addtl_option = '')
{
    if ($disabled) {
        $disabled = 'disabled="disabled"';
    } else {
        $disabled = '';
    }
    $t = "<select size=\"1\" name=\"$select_name\" $disabled $addtl_option>\n";
    if (!empty($first_line)) {
        $t .= "<option value=\"\">$first_line</option>\n";
    }
    if (!empty($source)) {
        foreach ($source as $key=>$val) {
            $foo = '';
            $doh = substr($key, 0, 3);
            if ($doh == '***') {
                $t .= "<optgroup label=\"$val\">";
            } elseif ($doh == '///') {
                $t .= "</optgroup>";
            } else {
                if ($doh == '---') {
                    $foo .= " disabled=\"disabled\"";
                } elseif ($key == $selected_value) {
                    $foo .=  " selected=\"selected\"";
                }
                if (substr($val, 0, 2) == '//') {
                    $t .= "<option value=\"$key\" style=\"color:#22f\" $foo>".substr($val, 2)."</option>\n";
                } else {
                    $t .= "<option value=\"$key\" $foo>$val</option>\n";
                }
            }
        }
    }
    $t .= "</select>\n";
    return $t;
}


// create checkbox form, useful for multiple box ("i need boxes, lots of boxes" - Neo)
// $box_name = field name
// $source = array[$key] = $val, $key = field value, $val = text to display
// $selected = string: 1,5,8,9; or use array[] = $selected
// $col = number of colums to display
// $form_id = form name -- when defined, will display "Select/Deselect All" button
function create_checkbox_form($box_name, $source, $selected = '', $col = 3, $form_id = '')
{
    $t = array();
    for ($i = 1; $i <= $col; $i++) {
        $t[$i] = '';
    }
    $i = 0;

    // selected vals
    if (!is_array($selected)) {
        $select = explode(',', $selected);
    } else {
        $select = $selected;
    }
    if ($selected == '') {
        $select = array();
    }

    if (!empty($source)) {
        foreach ($source as $key => $val) {
            $j = ($i % $col) + 1;
            $k = $i + 1;
            if (in_array($key, $select)) {
                $t[$j] .= "<label><input type=\"checkbox\" name=\"{$box_name}_{$k}\" value=\"$key\" checked=\"checked\" /> $val</label><br />\n";
            } else {
                $t[$j] .= "<label><input type=\"checkbox\" name=\"{$box_name}_{$k}\" value=\"$key\" /> $val</label><br />\n";
            }
            $i++;
        }
    }
    for ($i = 1; $i <= $col; $i++) {
        $t[$i] = substr($t[$i], 0, -7);
    }

    if ($form_id) {
        $out = "<label><input name=\"checkall\" type=\"checkbox\" onclick=\"SetAllCheckBoxes('$form_id','div_$box_name',this.checked);\" /> Select/Deselect All</label><div id=\"div_$box_name\">";
    } else {
        $out = '<div>';
    }
    if ($col > 1) {
        $w = round(100 / $col);
        $out .= "<table border=\"0\" width=\"100%\"><tr>\n";
        for ($i = 1; $i <= $col; $i++) {
            $out .= "<td valign=\"top\" width=\"$w%\">$t[$i]</td>\n";
        }
        $out .= "</tr></table>";
        return $out."</div>";
    } else {
        return $out.$t[1]."</div>";
    }
}



// to generate <radio> for a <form>, and automatically select the 'selected' value.
// $source = array of data for <select>
// $radio_name = name for <select>
// $selected_value = selected value
// $mode = 'h' - horizontal, 'v' - vertical (only if col = 1)
// $col = number of column
function create_radio_form($radio_name, $source, $selected_value = '', $mode = 'h', $col = 1, $addtl_option = '')
{
    $t = array();
    for ($i = 1; $i <= $col; $i++) {
        $t[$i] = '';
    }
    $i = 0;

    foreach ($source as $key => $val) {
        $j = ($i % $col) + 1;
        if ($key == $selected_value) {
            $t[$j] .= "<label><input type=\"radio\" name=\"$radio_name\" value=\"$key\" checked=\"checked\" $addtl_option /> $val</label>\n";
        } else {
            $t[$j] .= "<label><input type=\"radio\" name=\"$radio_name\" value=\"$key\" $addtl_option /> $val</label>\n";
        }

        // if col == 1, then can have h or v formatting
        if ($col == 1) {
            if ($mode == 'h') {
                $t[$j] .= '&nbsp;';
            } else {
                $t[$j] .= '<br />';
            }
        }
        // if col > 1, only vertical formatting
        else {
            $t[$j] .= "<br />\n";
        }
        $i++;
    }

    if ($col > 1) {
        for ($i = 1; $i <= $col; $i++) {
            $t[$i] = substr($t[$i], 0, -7);
        }
        $w = round(100 / $col);
        $out = "<table border=\"0\" width=\"100%\"><tr>\n";
        for ($i = 1; $i <= $col; $i++) {
            $out .= "<td valign=\"top\" width=\"$w%\">$t[$i]</td>\n";
        }
        $out .= "</tr></table>";
        return $out;
    } else {
        return $t[1];
    }
}


// create a simple tickbox, it's different from create_checkbox_form, as this is easier
function create_tickbox_form($name, $string = '', $toggle = 0, $addtl_option = '')
{
    $check = empty($toggle) ? '' : 'checked="checked"';
    return "<label><input type=\"checkbox\" name=\"$name\" value=\"1\" $check $addtl_option /> $string</label>";
}


// create date (dd-mmm-yyyy) select form
// - return: a form field to select date-month-year
// - input:
//   $prefix = add prefix to form field (prefix_dd, prefix_mm, prefix_yy)
//   $show_year = self explanatory, also to indicate start year
//   $show_date, $show_month = self explanatory
//   $select = default $select date (format: Y-m-d), or use 'now' or 'today'
//   $yyrange = if today is 2010, then $yyrange = 10 will display year form: 2005-2015;
// - example: date_form ('mydate', 2005, 1, 1, 'today');
//   example: date_form ('mydate') create form for today with Y-m-d
function date_form($prefix, $show_year = '', $show_month = '', $show_date = '', $select = '', $yyrange = 0)
{
    global $lang;
    $ok = false;
    $mi = array('January', 'February', 'March', 'April', 'May', 'June',
                 'July', 'August', 'September', 'October', 'November', 'December');	// define month index

    // get default date
    if (empty($show_year) && empty($show_month) && empty($show_date)) {
        $show_date = 1;
        $show_month = 1;
        $show_year = date('Y');
        $select = 'now';
    }
    if (($select == 'now') || ($select == 'today')) {
        $select = date('Y-m-d');
    }
    if (empty($select)) {
        $select = '0000-00-00';
    }
    $f = explode('-', $select);
    if (empty($f)) {
        $f = explode('/', $select);
    }
    if (empty($f)) {
        $f = explode('.', $select);
    }
    if (empty($f[1])) {
        $f[1] = '00';
    }
    if (empty($f[2])) {
        $f[2] = '00';
    }

    $thn = $f[0];
    $bln = strlen($f[1]) > 1 ? $f[1] : '0'.$f[1];
    $tgl = strlen($f[2]) > 1 ? $f[2] : '0'.$f[2];

    if ($show_date) {
        for ($d = 1; $d <= 31; $d++) {
            $dt[$d] = $d;
        }
        $tmp['D'] = create_select_form($prefix.'_dd', $dt, $tgl, '[Date]');
    } else {
        $tmp['D'] = '';
    }

    if ($show_month) {
        for ($m = 1; $m <= 12; $m++) {
            $i = current($mi);
            $mt[$m] = $lang['datetime'][$i];
            next($mi);
        }
        $tmp['M'] = create_select_form($prefix.'_mm', $mt, $bln, '[Month]');
    } else {
        $tmp['M'] = '';
    }

    if ($show_year) {
        if ($show_year < 1901) {
            $show_year = date('Y');
        }
        $ye = $show_year + $yyrange;
        for ($y = $show_year; $y <= $ye; $y++) {
            $yt[$y] = $y;
        }
        if ($yyrange) {
            $tmp['Y'] = create_select_form($prefix.'_yy', $yt, $thn, '[Year]');
        } else {
            $tmp['Y'] = "<input type=\"text\" name=\"{$prefix}_yy\" value=\"$thn\" size=\"4\" style=\"width:80px;min-width:80px;max-width:80px\" />";
        }
    } else {
        $tmp['Y'] = '';
    }

    $output = '';
    for ($i=0; $i<3; $i++) {
        $j = $lang['l_select_date_format'][$i];
        $output .= $tmp[$j];
    }

    return $output;
}


// time form
// $prefix = for field id
// $select = selected value (00:00 ~ 23:59)
// $interval = show minutes field with this interval, eg. interval 5 = show minute form: 00, 05, 10, 15, etc. interval 1 = 00, 01, 02, 03...
// $empty_lines = true to show empty field as hour & minutes
function time_form($prefix, $select = '', $interval = 5, $disabled = false)
{
    global $lang;
    $ok = false;

    // get default date
    if (($select == 'now') || ($select == 'today')) {
        $select = date('H:i');
    }
    if (empty($select) && !verify_time($select)) {
        $select = '00:00';
    }
    $hou = substr($select, 0, 2);
    $min = substr($select, 3, 2);
    $min = round($min / $interval) * $interval;

    // hour: 00-23
    $hh = array();
    for ($h = 0; $h <= 23; $h++) {
        if (strlen($h) <= 1) {
            $hi = '0'.$h;
        } else {
            $hi = $h;
        }
        $hh[$hi] = $h;
    }
    $tmp['H'] = create_select_form($prefix.'_hou', $hh, $hou, '', $disabled);

    // minute: 00-59
    $mm = array();
    for ($m = 0; $m <= 59; $m=$m+$interval) {
        if (strlen($m) <= 1) {
            $mi = '0'.$m;
        } else {
            $mi = $m;
        }
        $mm[$mi] = $mi;
    }
    $tmp['M'] = create_select_form($prefix.'_min', $mm, $min, '', $disabled);

    $output = $tmp['H'].':'.$tmp['M'];

    return $output;
}


// create a simple <input type="text">, it doesn't use <id="id">, if it's required, insert it in $addtl_option with other js
function create_varchar_form($field_name, $value='', $size = 30, $max = 255, $disabled = false, $addtl_option = '')
{
    if ($disabled) {
        $dis = 'disabled="disabled"';
    } else {
        $dis = '';
    }
    $s = ($size * 15).'px';
    $sm = ($size * 5).'px';
    $sizepx = "style=\"max-width:$s;min-width:$sm;width:100%\"";
    return "<input type=\"text\" name=\"$field_name\" value=\"$value\" size=\"$size\" $sizepx \"maxlength=\"$max\" $dis $addtl_option />";
}


// create a simple <input type="hidden">, it doesn't use <id="id">, if it's required, insert it in $addtl_option with other js
function create_hidden_form($field_name, $value='', $addtl_option = '')
{
    return "<input type=\"hidden\" name=\"$field_name\" value=\"$value\" />";
}


// get date input from date_form() function
// - return: date in Y-m-d format (sql ready)
//           false if invalid date (2005-02-31)
//           die() if out-of-bound date (2005-13-32)
// - note: if all field exists (complete YYYY-MM-DD) also check date validity (2005-02-29 will return FALSE); otherwise
//         (only YYYY-MM or YYYY) return as-is.
function date_param($prefix, $method = 'get')
{
    $tmp = '';
    $method = strtolower($method);
    if ($method == 'get') {
        $yy = get_param($prefix.'_yy');
        $mm = get_param($prefix.'_mm');
        $dd = get_param($prefix.'_dd');
    } else {
        $yy = post_param($prefix.'_yy');
        $mm = post_param($prefix.'_mm');
        $dd = post_param($prefix.'_dd');
    }

    $d = "$yy$mm$dd";
    if (($d == "") || ($d == '0000')) {
        return false;
    }

    if (strlen($yy) != 4) {
        return false;
    } else {
        $tmp = $yy;
    }

    if (($yy < 1901) || ($yy > 2038)) {
        return false;
    }

    if ($mm) {
        if (strlen($mm) < 2) {
            $mm = "0$mm";
        }
        if (strlen($mm) > 2) {
            return false;
        }
        if (($mm > 12) || ($mm < 1)) {
            return false;
        }
        $tmp .= "-$mm";
    }

    if ($dd) {
        if (strlen($dd) < 2) {
            $dd = "0$dd";
        }
        if (strlen($dd) > 2) {
            return false;
        }
        if (($dd > 31) || ($dd < 1)) {
            return false;
        }
        $tmp .= "-$dd";

        if (!verify_date($tmp)) {
            return false;
        }
    }

    return $tmp;
}


// get time
function time_param($prefix, $method = 'get')
{
    $tmp = '';
    $method = strtolower($method);
    if ($method == 'get') {
        $hh = get_param($prefix.'_hou');
        (int)$mm = get_param($prefix.'_min');
    } else {
        $hh = post_param($prefix.'_hou');
        (int)$mm = post_param($prefix.'_min');
    }

    if ("$hh$mm" == "") {
        return false;
    }

    if (($hh > 23) || ($hh < 0)) {
        return false;
    } else {
        $tmp = $hh;
    }

    if (($mm > 59) || ($mm < 0)) {
        return false;
    } else {
        if ($mm < 10) {
            $mm = '0'.substr($mm, 1, 1);
        }
        $tmp .= ":$mm";
    }

    return $tmp;
}


// get checkbox value (built by create_checkbox_form)
// $as_array = 1 : return values in an array; 0 : return as string (1,2,3,4,5...)
function checkbox_param($box_name, $method = 'get', $as_array = 0)
{
    $method = strtolower($method);
    $foo = array();
    $l = strlen($box_name) + 1;

    if ($method == 'get') {
        foreach ($_GET as $key => $val) {
            if (substr($key, 0, $l) == $box_name.'_') {
                $foo[] = get_param($key);
            }
        }
    } else {
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, $l) == $box_name.'_') {
                $foo[] = post_param($key);
            }
        }
    }


    if ($as_array) {
        return $foo;
    } else {
        return implode(',', $foo);
    }
}


// this function will transform (convert?) a url query (eg: abc.php?var=val&yes=no) to array ('var' => 'val', 'yes' => 'no');
function url_query_to_array($query)
{
    $result = array();
    $x = strpos($query, '?');
    $xx = substr($query, $x + 1, strlen($query) - $x);
    $tok = strtok($xx, "&");
    while ($tok) {
        $y = 0;
        $y = strpos($tok, '=', $y);
        $key = substr($tok, 0, $y);
        $val = substr($tok, $y + 1, strlen($tok) - $y);
        $result[$key] = $val;
        $tok = strtok('&');
    }
    return $result;
}


// send html header to browser
function html_header()
{
    global $config;
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"en-us\" dir=\"ltr\">\n";
    echo "<head>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
    echo "<title>$config[site_name]</title>\n";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$config[site_url]/skins/_common/default.css\" />\n";
    echo "</head>\n";
    echo "<body style=\"margin:20px\">\n";
}


// send html footer to browser
function html_footer()
{
    echo "</body>\n";
    echo "</html>\n";
}


// create HTML header (title, description, keywords, etc)
// return nothing
// output directly into global $txt
function generate_html_header($title = '', $description = '', $keywords = '')
{
    global $txt, $lang, $config;
    $txt['site_name'] = $config['site_name'];
    $txt['site_slogan'] = $config['site_slogan'];
    $txt['site_email'] = $config['site_email'];
    if (empty($title)) {
        $title = $config['site_name'];
    }
    if (empty($description)) {
        $description = $config['site_description'];
    }
    if (empty($keywords)) {
        $keywords = $config['site_keywords'];
    }
    $title = addslashes(str_replace(array('qemod:', "\n", "\r", '"', "'"), '', strip_tags($title)));
    $description = addslashes(str_replace(array('qemod:', "\n", "\r", '"', "'"), '', strip_tags($description)));
    $keywords = addslashes(str_replace(array('qemod:', "\n", "\r", '"', "'"), '', strip_tags($keywords)));
    if ($title == 'adm') {
        $title = 'Administration Panel :: '.$config['site_name'];
    }
    $txt['head_title'] = $title;
    $txt['site_description'] = str_replace('&hellip;', '...', line_wrap($description, 140, false));
    $txt['site_keywords'] = $keywords;
}


// create rating image (star)
// show 5 stars, input: 0-5
function rating_img($rating, $fontsize = 20)
{
    global $config, $inc_folder;
    $img = '<span class="rating">';
    $s_rating = floor($rating);
    // full star
    for ($i=1; $i<=$s_rating; $i++) {
        $img .= '<i class="glyphicon glyphicon-star" style="font-size:'.$fontsize.'px"></i>';
    }

    // half star
    if ($s_rating < $rating) {
        $h_rating = $rating * 10;
        if (($h_rating / 5) % 2) {
            $img .= '<i class="glyphicon glyphicon-star half" style="font-size:'.$fontsize.'px"></i>';
            $s_rating++;
        }
    }

    // empty star
    for ($i=$s_rating+1; $i<=5;$i++) {
        $img .= '<i class="glyphicon glyphicon-star empty" style="font-size:'.$fontsize.'px"></i>';
    }
    return $img.'</span>';
}


// convert string from html_entities () into original html (esp. values from <form>)
// we don't use html_entity_decode (which is only available from php 4.3.0), because it wrongly convert a double
// html_entities, e.g <img => &lt;img => &amp;lt;img, which converted to <img directly, when it suppose to be &lt;img.
// used by: email(), user_init.php
function html_unentities($text, $quote_style = ENT_QUOTES)
{
    $trans_table = array_reverse(array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    $trans_table = array('&amp;' => '&tmp;', '&#039;' => '\'', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>');
    $s = array_keys($trans_table);
    $r = array_values($trans_table);
    $text = str_replace($s, $r, $text);
    $text = str_replace('&tmp;', '&', $text);
    // $text = preg_replace ('/&#(\d+);/me', "chr(\\1)", $text);		#decimal notation without zero (123, 234, etc)
    // $text = preg_replace ('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $text);#hex notation
    return $text;
}


// convert smilies code to smilies images (see lang.php for list)
function convert_smilies($text)
{
    global $smilies;
    $parent_dir = '.';

    foreach ($smilies as $key => $val) {
        $smile_key[] = $key;
        $smile_val[] = "<img src=\"$parent_dir/$val\" alt=\"$key\" />";
    }

    return str_replace($smile_key, $smile_val, $text);
}


// display smilies list
// f_id = form name, i_id = input text -or- textarea name
function get_smilies($f_id, $i_id)
{
    global $smilies;
    $parent_dir = '.';

    $tmp  = "<script type=\"text/javascript\">\nfunction insert_smilies (id)\n{ document.forms['$f_id'].$i_id.value+=' '+id+' ' }\n</script>\n";
    $tmp .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\"><tr>\n";

    foreach ($smilies as $key => $val) {
        $tmp .= "<td width=\"17\"><img src=\"$parent_dir/$val\" alt=\"$key\" title=\"$key\" onclick=\"insert_smilies ('$key')\" /></td>\n";
    }

    $tmp .= '</tr></table>';
    return $tmp;
}


// censor some words (see lang.php for list)
function word_censor($text)
{
    global $censor;

    foreach ($censor as $key => $val) {
        $censor_key[] = "/$key/i";
        $censor_val[] = $val;
    }

    return preg_replace($censor_key, $censor_val, $text);
}


// $mode = 0 (default) : use header then html. 1 : html only
function redir($url = '', $mode = 0)
{
    global $config;
    if (empty($url)) {
        $url = empty($_SERVER['HTTP_REFERER']) ? $config['site_url'] : $_SERVER['HTTP_REFERER'];
        if (strpos('.'.$url, $config['site_url']) != 1) {
            $url = $config['site_url'];
        }
    }
    if (empty($url)) {
        $url = $config['site_url'];
    }

    if ((headers_sent() && $config['force_redir']) || ($mode)) {
        if ($config['debug_mode']) {
            $timer = 10;
        } else {
            $timer = 0;
        }
        echo "<html>\n<head>\n";
        echo "  <meta http-equiv=\"refresh\" content=\"$timer;url=$url\">\n";
        echo " </head>\n<body>Redirecting to <a href=\"$url\">$url</a></body><html>";
    } else {
        header("Location: $url");
    }
    die; // <<- IMPORTANT!
}


function br2nl($html)
{
    return preg_replace('#<br\s*/?>#i', "\n", $html);
}


// return formatted ajax result in json
// @param err_no = error number, 0 if no error
// @param err_msg = error message, optional
// @param result = the result, can be string or array, optional
// if err_msg & result are empty, will override err_no = 0 & err_msg = empty (aka. success); and result = err_no
// -- eg: flush_json (1) => err_no = 0, err_msg = 0 <ajax success>, result = 1 <script dependent>
function flush_json($err_no, $err_msg = '', $result = '')
{
    if (empty($err_msg) && empty($result)) {
        $result = $err_no;
        $err_no = 0;
        $err_msg = '';
    }
    echo json_encode(array($err_no, $err_msg, $result));
    die;
}


// (c) http://stackoverflow.com/questions/1996122/how-to-prevent-xss-with-html-php
// clean any potential xss attack from html rich text editor form (if any)
function xss_clean($data)
{
    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);

    // we are done...
    return $data;
}


/* ------- ( FILE FUNCTIONS ) ------- */


// get file list from a path
function get_file_list($path)
{
    $list = array();
    if (substr($path, -1)  != '/') {
        $path .= '/';
    }
    $handle=opendir($path);
    if (!$handle) {
        return false;
    }

    while (false !== ($file = readdir($handle))) {
        if (is_file($path.$file)) {
            $list[] = $file;
        }
    }

    closedir($handle);

    return $list;
}


// convert a string to a filename, removing invalid characters
// used by: create_filename ()
function safe_filename($fn, $len = 50)
{
    $fn = strtolower($fn);
    $foo = pathinfo($fn);

    // get only filename
    if (empty($foo['extension'])) {
        $foo['extension'] = '';
    }
    $foo['filename'] = substr($foo['basename'], 0, -1 * strlen($foo['extension']) -1);

    $fn = preg_replace("/[^a-zA-Z0-9]/", "_", substr($foo['filename'], 0, $len)).'.'.preg_replace("/[^a-zA-Z0-9]/", "_", $foo['extension']);
    return $fn;
}


// create a file name that doesn't exist in $folder folder
// $folder = folder location
// $fn = original filename (required even if random filename to determine extension)
// $rnd = TRUE: create random name; FALSE: create safe filename
function create_filename($folder, $fn, $rnd = true)
{
    $ok = false;
    $foo = pathinfo($fn);
    $ext = $foo['extension'];
    $add = '';	// addtional random string(for safe filename), eg: 3c1_somename.ext, '3c1_' is additional string
    while (!$ok) {
        if ($rnd) {
            $tmp_name = random_str(16).'.'.$ext;
        } else {
            $tmp_name = $add.safe_filename($fn);
        }

        if (!file_exists($folder.'/'.$tmp_name)) {
            $ok = true;
        }
        $add = random_str(3).'_';
    }
    return $tmp_name;
}


// safe file upload: 1. limit uploadable files (by extension); 2. disabled in demo mode
// $field = form field name; can be array
// $target = target folder OR target folder and name (= needs file extension); can be array
// $overwrite = TRUE to overwrite same filename; FALSE to save as different name
// return = true if uploaded, false if (any) failed; if inputs are array, it will return array corresponding with each upload status (eg: $return = array (1 => TRUE, 2 => FALSE, 3 => TRUE, 'summary' => 'T')
// --- where $return['success'] is TRUE if all uploaded, FALSE if any failure
function upload_file($field, $target, $allow_overwrite = false)
{
    global $config;
    $overwrite = false;
    $count = $size = 0;
    $result = array();
    $result['success'] = true;
    $result['demo_mode'] = false;
    $result['count'] = $result['size'] = 0;

    // for demo mode, return FALSE, explaining, it's in demo mode
    if ($config['demo_mode']) {
        $result['success'] = false;
        $result['demo_mode'] = true;
        return $result;
    }

    // if not array, create as array
    if (!is_array($field)) {
        $field = array($field);
        $target = array($target);
        $array = false;
    } else {
        // check if target also an array
        if (!is_array($target)) {
            $foo = array();
            foreach ($field as $k => $v) {
                $foo[$k] = $target;
            }
            $target = $foo;
        }
        $array = true;
    }

    // process
    foreach ($field as $k => $v) {
        if (!empty($_FILES[$v]['tmp_name']) && !$_FILES[$v]['error']) {
            $result[$k]['source'] = $_FILES[$v]['name'];
            $src = $_FILES[$v]['tmp_name'];
            $tgt = $target[$k];
            $err = false;

            if (is_dir($tgt)) { // if folder, use it
                $tgt_folder = $tgt;
                $tgt_name = $_FILES[$v]['name'];
            // echo $tgt;
            } else { // if not folder, is it an existing file, a new file, or invalid path?
                $foo = pathinfo($tgt);
                $tgt_folder = $foo['dirname'];
                $tgt_name = $foo['basename'];

                // not existing file & invalid path => die!
                if (is_file($tgt)) {
                    $overwrite = true;
                }
                if (!is_file($tgt) && !is_dir($tgt_folder)) {
                    $result[$k]['err'] = 'NoTarget';
                    $result['success'] = false;
                }
            }

            // fix file name
            if (substr($tgt_folder, -1, 1) == '/') {
                $tgt_folder = substr($tgt_folder, 0, -1);
            }
            if (!$allow_overwrite) {
                $tgt_name = create_filename($tgt_folder, $tgt_name, false);
            }	// if not allow overwrite = create a safe & non-existing name
            if ($allow_overwrite) {
                $tgt_name = safe_filename($tgt_name);
            } // if allow overwrite = only make sure it's a safe name
            $path = $tgt_folder.'/'.$tgt_name;
            $result[$k]['path'] = $path;
            $result[$k]['filename'] = $tgt_name;

            // is it allowable files?
            $allowed = explode(',', $config['allowed_file']);
            foreach ($allowed as $dv) {
                $foo = pathinfo($tgt_name);
                $ext = empty($foo['extension']) ? '' : $foo['extension'];
                if (!in_array($ext, $allowed)) {
                    $err = true;
                }
            }

            // upload!
            if (!$err) {
                move_uploaded_file($src, $path);
                @chmod($path, 0644);
                if (file_exists($path)) {
                    $size = $size + $_FILES[$v]['size'];
                    $count++;
                    $result[$k]['err'] = '';
                } else {
                    $result[$k]['err'] = 'NotWriteable';
                }
            } else {
                $result['success'] = false;
                $result[$k]['err'] = 'Disallow';
            }
        }
    }

    // if $field is not array, return simpler result
    $result['count'] = $count;
    $result['size'] = $size;
    return $result;
}


// to open a remote file (file in different host or url)
// generally we can use fopen ($url), but in some host, it's not allowed
// NOTES: remote_fopen skip SSL certificate, THUS DO NOT USE this function IF YOU REALLY NEED A SECURE CONNECTION!
function remote_fopen($site_url)
{
    // ignore SSL certificate
    $arrContextOptions = array("ssl" => array("verify_peer" => false, "verify_peer_name" => false));

    // if remote fopen allow, use it (it's faster)
    if (ini_get('allow_url_fopen')) {
        return file_get_contents($site_url, false, stream_context_create($arrContextOptions));
    }

    // if not allowed, use curl
    $ch = curl_init();
    $timeout = 5; // set to zero for no timeout
    curl_setopt($ch, CURLOPT_URL, $site_url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    ob_start();
    curl_exec($ch);
    curl_close($ch);
    $file_contents = ob_get_contents();
    ob_end_clean();

    return $file_contents;
}


/* ------- ( EMAIL FUNCTIONS ) ------- */

function smtp_get_lines($conn, $cmd = '')
{
    global $config;

    $data = "";
    while ($str = @fgets($conn, 515)) {
        $data .= $str;
        if (substr($str, 3, 1) == " ") {
            break;
        }
    }
    if ($config['debug_mode']) {
        echo 'SMTP > '.$cmd.'<br />';
        echo $data.'<hr />';
    }
    return $data;
}

function smtp_send_lines($conn, $cmd)
{
    fputs($conn, $cmd);
    smtp_get_lines($conn, $cmd);
}

function smtp_email($to, $subject, $body, $html = 0)
{
    global $config;
    $crlf = $config['smtp_crlf'];
    $SmtpServer = $config['smtp_server'];
    $SmtpUser = base64_encode($config['smtp_user']);
    $SmtpPass = base64_encode($config['smtp_passwd']);

    // take email address from "NAME <em@il>" ==> em@il
    $tom = $to;
    if ($x = strpos($to, '<')) {
        $to = substr($to, $x + 1, -1);
    }
    if ($config['debug_mode']) {
        echo '<h1>Debug Result for SMTP Email</h1>';
    }

    // server name
    if (isset($_SERVER['SERVER_NAME'])) {
        $http_host = $_SERVER['SERVER_NAME'];
    } else {
        $http_host = 'localhost.localdomain';
    }

    // if ssl
    if ($config['smtp_secure'] == 'ssl') {
        $SmtpServer = 'ssl://'.$SmtpServer;
    }

    // default port
    if (empty($config['smtp_port'])) {
        $PortSMTP = 25;
    } else {
        $PortSMTP = $config['smtp_port'];
    }

    // sender
    if (empty($config['smtp_sender'])) {
        $config['smtp_sender'] = $config['site_email'];
    }

    // html?
    $header = $crlf;
    if ($html) {
        $header = "Content-Type: text/html; charset=\"UTF-8\"".$crlf."Content-Transfer-Encoding: 8bit";
    }

    if ($conn = fsockopen($SmtpServer, $PortSMTP)) {
        smtp_get_lines($conn);
        smtp_send_lines($conn, "EHLO ".$http_host.$crlf);
        if ($config['smtp_secure'] == 'tls') {
            smtp_send_lines($conn, "STARTTLS".$crlf);
            if (!stream_socket_enable_crypto($conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                return false;
            }
            smtp_send_lines($conn, "EHLO ".$http_host.$crlf);
        }
        smtp_send_lines($conn, "AUTH LOGIN".$crlf);
        smtp_send_lines($conn, $SmtpUser.$crlf);
        smtp_send_lines($conn, $SmtpPass.$crlf);
        smtp_send_lines($conn, "MAIL FROM: <".$config['smtp_sender'].">".$crlf);
        smtp_send_lines($conn, "RCPT TO: <$to>".$crlf);
        smtp_send_lines($conn, "DATA".$crlf);
        smtp_send_lines($conn, "To: $tom".$crlf."From: $config[site_name] <".$config['smtp_sender'].">".$crlf."Subject: ".$subject.$crlf.$header.$crlf.$body.$crlf.'.'.$crlf);
        smtp_send_lines($conn, "QUIT".$crlf);
        fclose($conn);
    }
}


// send email (advanced feature)
// body : string. To send an attachemtn, body : array ('message' => message, 'attachment' => file name to be attached)
// $html : 0 : send only plain text | 1 : send HTML email
// $log; log this email?
// $debug : 0 : display nothing | 1 : display email information (NOT SENDING EMAIL!)
function email($to, $subject, $body, $html = 0, $log = 0, $debug = 0)
{
    global $config, $db_prefix, $dbh;

    // attachment?
    if (is_array($body)) {
        $att = true;
    } else {
        $att = false;
    }
    $uid = md5(uniqid(time()));


    if ($html) {
        $content_type = 'text/html';
    } else {
        $content_type = 'text/plain';
    }

    // prepare headers
    $headers  = "From: $config[site_email]\r\n";
    $headers .= "MIME-Version: 1.0\r\n";

    if ($att) {
        $headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    } else {
        $headers .= "Content-type: $content_type; charset=utf-8\r\n";
    }

    // prepare message
    if ($att) {
        $file = $body['attachment'];
        $body['message'] = str_replace("\r", '', stripslashes($body['message']));
        $filename = basename($file);
        $content = file_get_contents($file);
        $content = chunk_split(base64_encode($content));
        $nmessage = "--".$uid."\r\n";
        $nmessage .= "Content-type: $content_type; charset=utf-8\r\n";
        $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $nmessage .= $body['message']."\r\n\r\n";
        $nmessage .= "--".$uid."\r\n";
        $nmessage .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
        $nmessage .= "Content-Transfer-Encoding: base64\r\n";
        $nmessage .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
        $nmessage .= $content."\r\n\r\n";
        $nmessage .= "--".$uid."--";
    } else {
        // remove double new line under Windows
        $nmessage = str_replace("\r", '', stripslashes($body));
    }

    // if debug, show email contents
    if ($debug && $config['debug_mode']) {
        if (!$html) {
            $body = html_entity_decode($body);
        }
        $body = str_replace('&#039;', "'", $body);
        if (!$html) {
            $body = '<font face="Courier New" size="2">'.nl2br($body).'</font>';
        }
        if ($config['debug_mode']) {
            echo '<h1>Debug Mode</h1><p>Cookie may not be stored.</p>';
        }
        echo "<table width='100%' border='1' style='border-collapse: collapse;' bgcolor='lightblue'>\n";
        echo "<tr><td colspan='2' width='100%' align='center'><b>Email Debug</b></td></tr>\n";
        echo "<tr><td nowrap><b>Email server</b></td><td width='90%'>".($config['smtp_email'] ? 'SMTP' : 'PHP\'s mail ()')."</td></tr>\n";
        echo "<tr><td nowrap><b>Email type</b></td><td width='90%'>$content_type</td></tr>\n";
        echo "<tr><td nowrap><b>Send to</b></td><td width='90%'>$to</td></tr>\n";
        echo "<tr><td nowrap><b>Subject</b></td><td width='90%'>$subject</td></tr>\n";
        echo "<tr><td nowrap><b>Additional Headers</b></td><td width='90%'>".nl2br($headers)."</td></tr>\n";
        echo "<tr><td nowrap><b>Message</b></td><td width='90%'>$nmessage</td></tr>\n";
        echo "</table>\n";
    }

    // and now mail it
    if (!$html) {
        // reverse &amp -> &; &quot; -> ", etc
        $b = html_entity_decode($nmessage);
        $b = str_replace('&#039;', "'", $b);
        if ($config['smtp_email']) {
            smtp_email($to, $subject, html_unentities($b));
        } else {
            mail($to, $subject, html_unentities($b), $headers);
        }
    } else {
        if ($config['smtp_email']) {
            smtp_email($to, $subject, $nmessage, true);
        } else {
            mail($to, $subject, $nmessage, $headers);
        }
    }

    // and finally, log it
    $subject = addslashes($subject);
    $body = addslashes($nmessage);
    $to = addslashes($to);
    if ($log) {
        sql_query("INSERT INTO ".$db_prefix."mailog VALUES ('', '$to', '$subject', '$body', UNIX_TIMESTAMP(), '$html')");
        return mysqli_insert_id($dbh);
    } else {
        return true;
    }
}


// validate email address (eg: need to be xxxx@yyyy.tld
function validate_email_address($address)
{
    if (empty($address)) {
        return false;
    }

    // check address format
    $pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
    '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
    return preg_match($pattern, $address);
}


/* ------- ( RTE - WYSIWYG & CODE EDITOR) ------- */
// We are using TinyMCE as wysiwyg editor
// You can obtain full TinyMCE source in http://tinymce.moxiecode.com/
// TinyMCE is (c)copyright Moxiecode


// display wysiwyg editor
// $id = ID of form field
// $text = initial value
// $pagebreak = display pagebreak button ( <!-- pagebreak --> )
function rte_area($id, $text = '', $width = 400, $height = 350, $pagebreak = false)
{
    global $config, $txt, $rte_mode;
    if ($config['wysiwyg']) {
        $rte_mode = 'rte_init';
        if ($config['multi_rte']) {
            $rte_mode = 'rte_multi';
        } else {
            $config['multi_rte'] = 1;
        }
    } else {
        $rte_mode = 'text';
    }

    if (strpos(Cur_Url(), 'includes%2F') or strpos(Cur_Url(), $config['admin_folder'].'%2F')) {
        $rte['basedir'] = '../misc';
    } else {
        $rte['basedir'] = './misc';
    }

    if ($pagebreak) {
        $rte['pagebreak'] = 'pagebreak,';
    } else {
        $rte['pagebreak'] = '';
    }
    $rte['f_textarea'] = $id;
    $rte['f_width'] = $width;
    $rte['f_height'] = $height;
    $rte['f_html'] = htmlentities($text, ENT_COMPAT, 'UTF-8');
    $rte['site_url'] = $config['site_url'];
    return quick_tpl(load_tpl('etc', 'skins/_common/editor.tpl'), $rte);
}


// We are using Edit Area as code editor
// You can obtain full Edit Area source in http://www.cdolivet.com/editarea/
// Edit Area is (c)copyright Christophe Dolivet

// display code editor
// $id = ID of form field
// $text = initial value
// $lang = default 'html', but can also accept: js, css & php
function code_editor_area($id, $text = '', $lang = 'html', $width = 400, $height = 350, $is_editable = true)
{
    global $config, $txt, $rte_mode;
    if ($config['wysiwyg']) {
        $rte_mode = 'code_editor_init';
        if ($config['multi_code_editor']) {
            $code_editor_mode = 'code_editor_multi';
        } else {
            $config['multi_code_editor'] = 1;
        }
    } else {
        $rte_mode = 'code_editor_multi';
    }

    $rte['is_editable'] = $is_editable ? 'true' : 'false';
    $rte['f_syntax'] = $lang;
    $rte['f_textarea'] = $id;
    $rte['f_width'] = $width;
    $rte['f_height'] = $height;
    $rte['f_html'] = htmlentities($text, ENT_COMPAT, 'UTF-8');
    $rte['site_url'] = $config['site_url'];
    // print_R ($rte);
    return quick_tpl(load_tpl('editor.tpl'), $rte);
    die;
}

/* ------- ( IMAGE FUNCTIONS ) ------- */
// require GD version 2.0.28 or later


// optimize image without sacrificing image quality (only in GD 2)
// $source = file source; $target = file output;
// $target_x = output x size; $target_y = output y size; $target_q = output quality
// $target_x can be 'thumb' to create thumbnail, or empty to smart-resize
// $screendump = true to save result to file and output result to screen, false to save only
function image_optimizer($source, $target, $target_q = 0, $target_x = 0, $target_y = 0, $screendump = false)
{
    global $config;
    $gd_version = $config['gd_library'];
    $thumb_size = $config['thumb_size'];

    // quality
    if (empty($target_q)) {
        $target_q = $config['optimizer'];
    }

    // works only on JPEG
    $inf = $img_size = getimagesize($source);
    $format = $inf[2];

    if (($format >= 1) && ($format <= 3) && (!empty($target_q))) {
        if ($format == 1) {
            $img_in = ImageCreateFromGIF($source);
        } elseif ($format == 2) {
            $img_in = ImageCreateFromJPEG($source);
        } elseif ($format == 3) {
            $img_in = ImageCreateFromPNG($source);
        } else {
            return false;
        }

        // if $x & $y empty -> original size
        if (empty($target_x) && empty($target_y)) {
            $target_x = $inf[0];
            $target_y = $inf[1];
        }

        // if $x = any (not 'thumb') & y = empty -> resize
        if (!empty($target_x) && empty($target_y)) {
            if ($target_x != 'thumb') {
                $thumb_size = $target_x;
                $target_x = 'thumb';
            }
        }

        // if $x thumb -> create thumb
        if ($target_x == 'thumb') {
            if (empty($target_q)) {
                $target_q = $config['thumb_quality'];
            }

            if ($img_size[0] > $img_size[1]) {
                $thumb_on = 'x';
            } else {
                $thumb_on = 'y';
            }

            if ($thumb_on == 'y') {
                $target_x = ($thumb_size/$img_size[1]) * $img_size[0];
                $target_y = $thumb_size;
            } else {
                $target_y = ($thumb_size/$img_size[0]) * $img_size[1];
                $target_x = $thumb_size;
            }
        }

        // create optimized version
        if ($gd_version == '1') {
            $img_out = ImageCreate($target_x, $target_y);
            ImageCopyResized($img_out, $img_in, 0, 0, 0, 0, $target_x, $target_y, $img_size[0], $img_size[1]);
        } elseif ($gd_version == '2') {
            $img_out = ImageCreateTrueColor($target_x, $target_y);
            if ($format == 3) {
                // transparent PNG
                imagealphablending($img_out, false);
                imagesavealpha($img_out, true);
                $transparent = imagecolorallocatealpha($img_out, 255, 255, 255, 127);
                imagefilledrectangle($img_out, 0, 0, $target_x, $target_y, $transparent);
            }
            ImageCopyResampled($img_out, $img_in, 0, 0, 0, 0, $target_x, $target_y, $img_size[0], $img_size[1]);
        }

        // optimized (output)
        if ($format == 1) {
            ImageGIF($img_out, $target, $target_q);
            if ($screendump) {
                header('Content-Type: image/gif');
                ImageGIF($img_out);
            }
        } elseif ($format == 2) {
            ImageJPEG($img_out, $target, $target_q);
            if ($screendump) {
                header('Content-Type: image/jpg');
                ImageJPEG($img_out);
            }
        } elseif ($format == 3) {
            ImagePNG($img_out, $target, round($target_q / 10));
            if ($screendump) {
                header('Content-Type: image/png');
                ImagePNG($img_out);
            }
        } else {
            return false;
        }

        ImageDestroy($img_out);
        ImageDestroy($img_in);
        @chmod($target, 0644);

        return true;
    } else {
        return false;
    }
}


// place a watermark to an image
// $input: file to be marked, could be jpg, gif & png
// $watermark: the stamp, must be PNG
// $position: location of stamp: TL, TR, BL, BR, CC (top left, top right, bottom left, bottom right, center)
// $output: name of file for output, otherwise, it will overwrite the $input file.
function image_watermark($input, $watermark = '', $position = '', $output = '')
{
    global $config;

    // default values
    if (!$watermark) {
        $watermark = $config['watermark_file'];
    }
    if (!$position) {
        $position = $config['watermark_position'];
    }
    if (!$watermark) {
        return false;
    }

    // Set the margins for the stamp and get the height/width of the stamp image
    $marginX = 10;
    $marginY = 10;
    $target_q = $config['optimizer'];
    if (empty($output)) {
        $output = $input;
    }

    $inf = getimagesize($input);
    $format = $inf[2];

    if (($format < 1) || ($format > 3)) {
        return false;
    } // unsupported image type

    // load images
    $stamp = imagecreatefrompng($watermark);

    if ($format == 1) {
        $im = ImageCreateFromGIF($input);
    } elseif ($format == 2) {
        $im = ImageCreateFromJPEG($input);
    } elseif ($format == 3) {
        $im = ImageCreateFromPNG($input);
    } else {
        return false;
    }

    $ix = $inf[0];
    $iy = $inf[1];
    $sx = imagesx($stamp);
    $sy = imagesy($stamp);

    switch (strtoupper($position)) {
        case 'TL':
            $posX = $marginX;
            $posY = $marginY;
        break;

        case 'TR':
            $posX = $ix - $sx - $marginX;
            $posY = $marginY;
        break;

        case 'CC':
            $posX = ($ix - $sx) / 2;
            $posY = ($iy - $sy) / 2;
        break;

        case 'BL':
            $posX = $marginX;
            $posY = $iy - $sy - $marginY;
        break;

        default:
            $posX = $ix - $sx - $marginX;
            $posY = $iy - $sy - $marginY;
        break;
    }

    // Copy the stamp image onto our photo using the margin offsets and the photo
    // width to calculate positioning of the stamp.
    imagecopy($im, $stamp, $posX, $posY, 0, 0, $sx, $sy);

    // Output and free memory
    if ($format == 1) {
        ImageGIF($im, $output, $target_q);
    } elseif ($format == 2) {
        ImageJPEG($im, $output, $target_q);
    } elseif ($format == 3) {
        ImagePNG($im, $output, round($target_q / 10));
    }
    imagedestroy($im);
    imagedestroy($stamp);
}


/* ------- ( IP CONFIG ) ------- */


// a shortcut to SESSION.

// update config value, or create a new one
// $what = field name in ip_config table (optional)
function ip_config_update($what = '', $value = '')
{
    global $current_user_id, $db_prefix;
    $_SESSION[$db_prefix.'_'.$what] = $value;
}


// get ip_config value of $what from ip_config table
function ip_config_value($what)
{
    global $db_prefix;
    return session_param($db_prefix.'_'.$what);
}


// count numbers of stored ip_config ==> can be used as 'Number of online users' (but time range too big, 60 minutes)!
function ip_config_count()
{
    $res = sql_query("SELECT SUM(username!='') AS member, SUM(username='') AS guest FROM ".$db_prefix."ip_config LIMIT 1");
    $row = sql_fetch_array($res);
    $row['total'] = $row['member'] + $row['guest'];
    return $row;
}


/* ------- ( QVC - VISUAL CONFIRMATION FUNCTIONS aka CAPTCHA ) ------- */


// qVC - the simplest visual confirmation engine yet
// use qvc_init() --> <img src="visual.php"> --> compare qvc_value() == qhash (strtolower($user_input) )?
// qVC uses db to communicate with visual.php, then set user cookie using qhash, then db not used!
// $num = either 3 or 5, 3 => only 0-9, 5 => 0-F
function qvc_init($num = 5)
{
    if ($num == 3) {
        $value = mt_rand(100, 999);
    } else {
        $value = random_str(5);
    }
    ip_config_update('visual', $value);
    $_SESSION['qvc_value'] = qhash($value);
    // print_r ($_SESSION);
}


// return qvc value (it's qhash'd, so be sure to compare with qhash'd value)
function qvc_value()
{
    $correct_val = session_param('qvc_value');

    // block browser BACK
    qvc_init();
    return trim($correct_val);
}


/* ------- ( AXSRF - Anti Cross Site Request Forgery & OTHER SECURITIES) ------- */
// Search the web for XSRF
// So, to fight XSRF, we have to generate random token for each form & user, and compare it with stored token in db
// qEngine automatically initialize AXSRF token upon user login (see authorize_user function)
// qTPL automatically add hidden field to all forms, but only if you use flush_tpl()


// get AXSRF token value
function AXSRF_value()
{
    global $isLogin, $current_user_info;
    if ($isLogin) {
        return $current_user_info['axsrf_token'];
    } else {
        return false;
    }
}


// get & compare token ID automatically
// it automatically get token and do comparison, if failed => die
// $field = hidden form field name (default: AXSRF_token) - both get & post method
// call this function before processing any forms
function AXSRF_check($field = 'AXSRF_token')
{
    global $config;

    $db = AXSRF_value();
    $msg = "<h1>Invalid AXSRF token ID.</h1>\n
	<p>Please return to <a href=\"javascript:history.back(-1)\">previous page</a>, refresh, and try again.<br />If this problem occurs, please <a href=\"mailto:contact@c97.net\">contact C97net.</a></p>\n
	<p>For more information on XSRF, <a href=\"http://en.wikipedia.org/wiki/Cross-site_request_forgery\">click here</a>.</p>";
    if (empty($db)) {
        die($msg);
    }

    $foo = post_param($field);
    if (empty($foo)) {
        $foo = get_param($field);
    }

    // AXSRF in DB doesn't match with hidden field => die
    if ($foo != $db) {
        die($msg);
    }

    // safe
    return true;
}


// qhash, actually just a wrapper for hashing using pre-defined hash key (see config table)
function qhash($string, $mode = 'sha512', $advanced = true)
{
    global $qe_hash_key;
    settype($string, 'string');
    if ($advanced) {
        $str = ~$string;
    } else {
        $str = $string;
    }
    if ($mode == 'md5') {
        return trim(md5($str));
    }
    if ($mode == 'sha1') {
        return trim(sha1($str));
    } else {
        return trim(hash_hmac('sha512', $str, $qe_hash_key));
    }
}


// verify user's permissions.
// $permisi_id = permission id (eg: 'page_editor'), or required_level (1-5)
// $auto_die = true to show 'not allowed' message automatically, false to return 'false' to caller
// *) auto_die is overriden to true in ACP
// returns = user/admin level or auto die if level is not enough. If you need to compare the return value, be sure to use if (permission_check ('something') !== false) to avoid confusion with '0' (guest)
function permission_check($permisi_id, $auto_die = true)
{
    global $current_user_info, $config, $lang, $in_admin_cp;
    $allowed = false;

    if (is_numeric($permisi_id)) {
        if ($in_admin_cp) {
            if (($current_user_info['admin_level'] < $permisi_id)) {
                // admin && ajax -> simply show a message to re-login
                if (strpos(cur_url(false), '/admin_ajax.php')) {
                    die('Please re-login');
                } else {
                    admin_die($lang['msg']['no_level']);
                }
            }
            $allowed = true;
        } else {
            if (($current_user_info['admin_level'] >= $permisi_id)) {
                $allowed = true;
            }
        }
    } else {
        $permisi = unserialize($config['permisi'][$permisi_id]);

        if ($in_admin_cp) {
            if (!$permisi[$current_user_info['admin_level']]) {
                admin_die($lang['msg']['no_level']);
            } else {
                $allowed = true;
            }
        } else {
            if ($permisi[$current_user_info['user_level']]) {
                $allowed = true;
            }
        }
    }

    // return
    if ($auto_die && !$allowed) {
        msg_die($lang['msg']['no_level']);
    }
    if (!$auto_die && !$allowed) {
        return false;
    }
    if ($in_admin_cp) {
        return ($current_user_info['admin_level']);
    } else {
        return ($current_user_info['user_level']);
    }
}



/* ------- ( FORM LOADER ) ------- */


// save_form saves user input values for form, so it can be used in form loader
// ONLY 1 FORM can be saved at a time, new save will overwrite old one
function save_form($form_id, $method = 'post')
{
    $tmp = '"form","'.$form_id.'",';
    if ($method == 'post') {
        foreach ($_POST as $key => $val) {
            $tmp .= '"'.$key.'","'.post_param($key).'",';
        }
    } else {
        foreach ($_GET as $key => $val) {
            $tmp .= '"'.$key.'","'.get_param($key).'",';
        }
    }

    // $tmp = addslashes (substr ($tmp, 0, -1));
    $tmp = substr($tmp, 0, -1);
    ip_config_update('saved_form', $tmp);
    load_form($form_id);
}


// load_form loads user input values from db, so user doesn't need to re-input values
// return array to be used in tpl
function load_form($form_id)
{
    $foo = ip_config_value('saved_form');
    $tmp = csv_split($foo);
    if (empty($tmp)) {
        return false;
    }
    if ($tmp[1] != $form_id) {
        return false;
    }
    foreach ($tmp as $var) {
        $doh[] = stripslashes($var);
    }
    return array_split($doh, '', true);
}


// empty saved values
function reset_form()
{
    ip_config_update('saved_form', '');
}


/* ------- ( SEO URL ) ------- */

// create the URL for SEO
// $item_id = real item_id
// $label = title of article, eg "Welcome To Our Forum" -> will become "welcome-to-our-forum"
// used by: qadmin.php (and by other scripts that doesn't use qadmin)
function create_seo_url($item_id, $label)
{
    // create new item_id
    return $item_id.'-'.preg_replace("/[^a-zA-Z0-9]/", "-", substr(strtolower(html_unentities($label)), 0, 255));
}

// get param for SEO URL; eg. page.php?pid=welcome --> page,pid,1-welcome.php (original item_id is 1) from table qe_page
// $item_id = the item_id from URL ('welcome')
function seo_param($item_id)
{
    global $config;

    // seo url only for ADP 3
    if ($config['enable_adp'] != 3) {
        return $item_id;
    }
    $foo = explode('-', $item_id);
    if (!empty($foo[0])) {
        return $foo[0];
    } else {
        return $item_id;
    }
}


/* ------- ( qCACHE ) ------- */
// the easiest cache ever


// get cache from database
// $id = identifier or array
// $include_skin = include skin name in cache id, default true
function qcache_get($id, $include_skin = true)
{
    global $config, $db_prefix;

    // check if array, if not, create as a single array
    if (is_array($id)) {
        $cid = '';
        foreach ($id as $v) {
            $cid .= $include_skin ? "'$v:$config[skin]'," : "'$v',";
        }
        $cid = substr($cid, 0, -1);
        $num = count($id);
    } else {
        $cid = $include_skin ? "'$id:$config[skin]'" : "'$id'";
        $num = 1;
        $oid = $id;
        $id = array($id);
    }

    // if cache disabled -> return false
    if (!$config['cache']) {
        $output = array();
        $num = 0;
        foreach ($id as $v) {
            $num++;
            $output[$v] = false;
        }
        if ($num > 1) {
            return $output;
        } else {
            return false;
        }
    }

    // get the cache
    $cached = array();
    $res = sql_query("SELECT * FROM ".$db_prefix."cache WHERE cache_id IN ($cid) LIMIT $num");
    while ($row = sql_fetch_array($res)) {
        $cached[$row['cache_id']] = $row['cache_value'];
    }

    // verify the cache result
    foreach ($id as $v) {
        $cid = $include_skin ? "$v:$config[skin]" : $v;
        if (empty($cached[$cid])) {
            $cached[$cid] = '0|foo';
        }	// if cache is empty, return empty cache (aka false or failed)
        $foo = explode('|', $cached[$cid]);
        $lu = $foo[0];	// first section = time in unix stamp
        $cc = $foo[1];	// second section = the real cache contents
        if (time() - $lu > $config['cache']) {
            $output[$v] = false;
        } else {
            $output[$v] = (!empty($cc)) ? gzuncompress(base64_decode($cc)) : $cc;
        }
    }

    if ($num > 1) {
        return $output;
    } else {
        return $output[$oid];
    }
}


// update cache to db
// $id = identifier
// $content = cached content to save (must be clean from slashes)
// $include_skin = include skin name in cache id, default true
function qcache_update($id, $content, $include_skin = true, $cache_manual = false)
{
    global $config, $db_prefix, $dbh;
    if (!$config['cache']) {
        return false;
    }
    $id = $include_skin ? $id.':'.$config['skin'] : $id;
    $cc = time().'|'.base64_encode(gzcompress($content)) ;
    $cm = $cache_manual ? 1 : 0;
    sql_query("UPDATE ".$db_prefix."cache SET cache_value='$cc' WHERE cache_id='$id' LIMIT 1");
    if (!mysqli_affected_rows($dbh)) {
        sql_query("INSERT IGNORE INTO ".$db_prefix."cache SET cache_id='$id', cache_value='$cc', cache_manual='$cm'");
    }
}


// clear cache
// $what = name of cache_id, when omitted it will clear all 'automatic' cache (cache_manual=0). $what can be:
//         - string of qcache_id (eg: detail_1, page_5)
//         - string with %, eg: page_%
//         - empty string, which means remove all automatic cache
//         - or a string of 'everything', which means EVERYTHING (truncate)
// $include_skin = (needs $what), true: remove cache thats related to skins
function qcache_clear($what = false, $include_skin = true)
{
    global $config, $db_prefix, $dbh;
    if (!$what) {
        sql_query("DELETE FROM ".$db_prefix."cache WHERE cache_manual='0'");
    } elseif ($what == 'everything') {
        sql_query("TRUNCATE TABLE ".$db_prefix."cache");
    } else {
        if ($include_skin) {
            sql_query("DELETE FROM ".$db_prefix."cache WHERE cache_id LIKE '$what:%'");
        } else {
            if (substr($what, -1) == '%') {
                sql_query("DELETE FROM ".$db_prefix."cache WHERE cache_id LIKE '$what'");
            } else {
                sql_query("DELETE FROM ".$db_prefix."cache WHERE cache_id = '$what' LIMIT 1");
            }
        }
    }
    return mysqli_affected_rows($dbh);
}


/* ------- ( MISC ) ------- */


// (c) forceone at justduck.net [ php.net ]
// get varname & value from .ini formatted vars, eg:
// [Section]
// ; remark
// var = value
// var2 = value2
// used by: tpl.php for modules
function parse_ini_str($Str, $ProcessSections = true)
{
    $Section = null;
    $Data = array();
    if ($Temp = strtok($Str, "\r\n")) {
        do {
            $Temp = trim($Temp);
            if (empty($Temp)) {
                $Temp = ';dummy';
            }
            switch ($Temp[0]) {
                case ';':
                case '#':
                break;

                case '[':
                 if (!$ProcessSections) {
                     break;
                 }

                 $Pos = strpos($Temp, '[');
                 $Section = substr($Temp, $Pos+1, strpos($Temp, ']', $Pos)-1);
                 $Data[$Section] = array();
                break;

                default:
                 $Pos = strpos($Temp, '=');
                 if ($Pos === false) {
                     break;
                 }
                 $Value = array();
                 $name = trim(substr($Temp, 0, $Pos));
                 $val = trim(substr($Temp, $Pos+1), ' "');
                 if ($ProcessSections) {
                     if (empty($Section)) {
                         $Data[$name] = $val;
                     } else {
                         $Data[$Section][$name] = $val;
                     }
                 } else {
                     $Data[$name] = $val;
                 }

                break;
            }
        } while ($Temp = strtok("\r\n"));
    }

    return $Data;
}


// safely send string via url (without the risk of 'simple injection' or 'lost in translation').... IT'S NOT A SECURE METHOD!
// $string = can be a string or array
// $trim = trim string first
function safe_send($string, $trim = false)
{
    if (empty($string)) {
        return false;
    }

    // convert to array first
    if (!is_array($string)) {
        $string = array($string);
        $is_array = false;
    } else {
        $is_array = true;
    }

    // trim?
    if ($trim) {
        foreach ($string as $k => $v) {
            $string[$k] = trim($v);
        }
    }

    // zip the values if supported
    if (function_exists('gzinflate') && function_exists('gzdeflate')) {
        foreach ($string as $k => $v) {
            $string[$k] = gzdeflate($v, 9);
        }
    }
    foreach ($string as $k => $v) {
        $string[$k] = urlencode(base64_encode($v));
    }

    if (!$is_array) {
        return $string[0];
    } else {
        return $string;
    }
}


// safely send string via url (without the risk of 'simple injection' or 'lost in translation').... IT'S NOT A SECURE METHOD!
function safe_receive($string)
{
    if (empty($string)) {
        return false;
    }

    // first try (for non URL)
    $tmp = urldecode($string);
    $tmp = base64_decode($tmp);
    if (function_exists('gzinflate') && function_exists('gzdeflate')) {
        @$tmp = gzinflate($tmp);
    }

    // second try (for URL)
    if (empty($tmp)) {
        $string = base64_decode($string);
        if (function_exists('gzinflate') && function_exists('gzdeflate')) {
            @$string = gzinflate($string);
        }
        return $string;
    }

    return $tmp;
}


// return formatted address
// $addr = array -> return customer address
//       = non array/empty -> return shop address
// used by: contact.php
function format_address($addr = '')
{
    global $address_format, $config;

    if (is_array($addr)) {
        if ($addr['address2']) {
            $addr['address'] = $addr['address'].'<br />'.$addr['address2'];
        }
        return sprintf($address_format['member'], $addr['fullname'], $addr['address'], $addr['city'], $addr['state'], $addr['zip'], $addr['country'], $addr['phone'], $addr['district']);
    } else {
        $foo = $config;
        if ($foo['site_address2']) {
            $foo['site_address'] = $foo['site_address'].'<br />'.$foo['site_address2'];
        }
        return sprintf($address_format['site'], $foo['site_name'], $foo['site_address'], $foo['site_city'], $foo['site_state'], $foo['site_zip'], $foo['site_country'], $foo['site_phone'], '', $foo['site_fax'], $foo['site_mobile'], '1');
    }
}


// return byte value of G, M or K (so, 1K returned as 1024)
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
            // no break
        case 'm':
            $val *= 1024;
            // no break
        case 'k':
            $val *= 1024;
    }
    return $val;
}


// upload max size
function get_upload_max_size()
{
    $m1 = ini_get('post_max_size');
    $m2 = ini_get('upload_max_filesize');
    return return_bytes(min($m1, $m2));
}


// get options from db -- use EDIT_OPT.php to display edit window: edit_opt.php?fid=FIELD_ID&sublist=SUBFIELD, eg. FIELD_ID = 'city', SUBFIELD (optional) = '1' (for TRUE)
// this is useful if you need to create a simple option list (eg. drop down, etc for dynamic list, eg. city list --where you don't need further processing--)
// ps: values saved in `option` table, in format: idx (auto) | option_name ($fid) | option_value (user input)
// $fid = ID
// $sort = sort by value
// $db_index = use table index (auto number) as array key, or restart from 1
function get_editable_option($fid, $sort = true, $db_index = true)
{
    global $db_prefix;

    $foo = array();
    $i = 0;

    if ($sort) {
        $res = sql_query("SELECT idx, option_value FROM ".$db_prefix."option WHERE option_name='$fid' ORDER BY option_value");
    } else {
        $res = sql_query("SELECT idx, option_value FROM ".$db_prefix."option WHERE option_name='$fid' ORDER BY idx");
    }
    while ($row = sql_fetch_array($res)) {
        $i++;
        if ($db_index) {
            $foo[$row['idx']] = $row['option_value'];
        } else {
            $foo[$i] = $row['option_value'];
        }
    }
    return $foo;
}

// get real domain name for set_cookies (), eg: http://www.example.com -> .example.com; http://qe.example.com -> qe.example.com
function cookie_domain()
{
    global $config;
    $r = array('http://www', 'https://www', 'http://', 'https://');
    $d = str_replace($r, '', $config['site_url']);
    $x = strpos($d, '/');
    $dom = $x ? substr($d, 0, $x) : $d;

    // except for localhost, return nothing (IE & Chrome doesn't accept 'localhost')
    if (strpos($config['site_url'], '//localhost')) {
        return '';
    } else {
        return $dom;
    }
}


// output a variable on screen (for testing)
function test_var($var, $fn = '')
{
    global $$var;
    ob_start();
    var_dump($$var);
    $v = ob_get_contents();
    ob_end_clean();

    echo "<p>".(($fn) ? "<b>Caller:</b> $fn " : '')."<b>Variable Name:</b> $var &rarr; <b>Content:</b> ".$v."</p>";
}


function getCallingFunction($summary_only = false)
{
    global $config;
    if (!$config['debug_mode']) {
        return false;
    }
    $trace = debug_backtrace();
    if (!$summary_only) {
        echo '<h1>Error Source</h1>';
        foreach ($trace as $k => $v) {
            echo '<p>File <b>'.$v['file'].'</b><br />Function <b>'.$v['function'].'</b>:'.$v['line'].'</b></p>';
        }
    } else {
        $err = array_values(array_slice($trace, -1))[0];
        echo '<p>Error in <b>'.$err['file'].'</b>, function <b>'.$err['function'].'()</b>, line <b>'.$err['line'].'</b>.</p>';
    }
}


// notification
// $to = recipient user name, can be empty is $isAdmin = true;
// $subject = the subject
// $url = action url
// $isAdmin = true to create alert for whichever admin (not specific admin id)
// $send_email = true to also send email to admin (site email) only
function create_notification($to, $subject, $url = '', $isAdmin = false, $send_email = false)
{
    global $db_prefix, $isLogin, $current_user_id, $config;
    $subject = addslashes($subject);
    $uid = $isLogin ? $current_user_id : '';
    $isAdmin = $isAdmin ? 1 : 0;
    sql_query("INSERT INTO ".$db_prefix."notification SET notify_time=UNIX_TIMESTAMP(), notify_from='$uid', notify_to='$to', notify_admin='$isAdmin', notify_subject='$subject', notify_url='$url'");
    if ($send_email) {
        $email_txt = "You have a new notification from $config[site_url]\n\n$subject\nRelated URL: $url";
        email($config['site_email'], '['.$config['site_name'].'] '.$subject, $email_txt);
    }
}


// get a specific line from language db
function get_lang_line($lang_id, $lang_key)
{
    global $lang, $db_prefix;

    // if requested lang_id _same_with_ loaded lang = EN, return the line immidiatelly
    if ($lang['l_lang_id'] == $lang_id) {
        if (!empty($lang[$lang_key])) {
            return $lang[$lang_key];
        } else {
            return false;
        }
    }

    // if requested lang != EN, or loaded lang != EN, get it from db
    $row = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$lang_id' AND lang_key='$lang_key' LIMIT 1");
    if (empty($row)) {
        if (!empty($lang[$lang_key])) {
            return $lang[$lang_key];
        } else {
            return false;
        }
    } else {
        return $row['lang_value'];
    }
}


// create a unique id from a given $string, for example: $string = 'general', unique id is = 'gen'. Then your script will verify if 'gen' is really unique, otherwise,
// .. recall the script, and it will return 'gne', 'ger', 'gal', 'ene', 'eer', and so on
// $string = the string
// $length = lenght of returned id
// $iteration = number of step, eg: for 'general', iteration 0 returns 'gen', iteration 1 returns 'gne', iteration 5 returns 'eer'
// $auto_random = will create a random string if the function can't create a unique id
function create_unique_id($string, $length, $iteration = 0, $auto_random = false)
{
    $string = strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $string));
    $l = strlen($string);
    $i = $c = 0;
    while ($i < $l) {
        $j = 1;
        while ($j + $length - 2 < $l - $i) {
            $str = $string[$i].substr(substr($string, $i), $j, $length - 1);
            $j++;
            $c++;
            if ($c == $iteration + 1) {
                return $str;
            }
        }
        $i++;
    }

    if ($auto_random) {
        return substr($string, 0, 1).mt_rand(10 * ($l - 1), 10 * $l - 1);
    }
    return false;	// couldn't create unique id
}


function get_ip_address()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


// get list of countries
// $cid = empty to return array of countries
// $cid = <defined> to return string of the country name (verify country name)
function get_country_list($cid = '')
{
    global $country_list_def, $config;
    if (!count($country_list_def)) {
        $foo = file_get_contents($config['abs_path'].'/includes/country.def');
        $cfoo = unserialize($foo);
        foreach ($cfoo as $k => $v) {
            $country_list_def[$v] = $v;
        }
    }

    if ($cid) {
        $cid = array_search(strtolower($cid), array_map('strtolower', $country_list_def));
        if ($cid) {
            return $country_list_def[$cid];
        } else {
            return false;
        }
    } else {
        return $country_list_def;
    }
}
