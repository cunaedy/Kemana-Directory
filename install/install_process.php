<?php
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
        $ch = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrtuvwxyz1234567890`~!@#$%^&*()_+-={}[]:\";<>?,./";
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

// sending query to MySQL
function sql_query($sql, $debug = 0)
{
    global $dbh;
    if ($debug) {
        echo $sql.'<br />';
    }
    if (!$result = mysqli_query($dbh, $sql)) {
        echo '<p>',$sql,'<br />';
        echo mysqli_error($dbh).'</p>';
        echo '<p>Please contact us about this problem.</p>';
        die;
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

// splitSqlFile => taken from phpMyAdmin (c)phpMyAdmin.net
function splitSqlFile(&$ret, $sql, $release = '32270')
{
    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = false;
    $time0        = time();

    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for
        // backquotes that can't be escaped
        if ($in_string) {
            for (;;) {
                $i         = strpos($sql, $string_start, $i);
                // No end of string found -> add the current substring to the
                // returned array
                if (!$i) {
                    $ret[] = $sql;
                    return true;
                }
                // Backquotes or no backslashes before quotes: it's indeed the
                // end of the string -> exit the loop
                elseif ($string_start == '`' || $sql[$i-1] != '\\') {
                    $string_start      = '';
                    $in_string         = false;
                    break;
                }
                // one or more Backslashes before the presumed end of string...
                else {
                    // ... first checks for escaped backslashes
                    $j                     = 2;
                    $escaped_backslash     = false;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }
                    // ... if escaped backslashes: it's really the end of the
                    // string -> exit the loop
                    if ($escaped_backslash) {
                        $string_start  = '';
                        $in_string     = false;
                        break;
                    }
                    // ... else loop
                    else {
                        $i++;
                    }
                } // end if...elseif...else
            } // end for
        } // end if (in string)

            // We are not in a string, first check for delimiter...
        elseif ($char == ';') {
            // if delimiter found, add the parsed part to the returned array
            $ret[]      = substr($sql, 0, $i);
            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len    = strlen($sql);
            if ($sql_len) {
                $i      = -1;
            } else {
                // The submited statement(s) end(s) here
                return true;
            }
        } // end else if (is delimiter)

        // ... then check for start of a string,...
        elseif (($char == '"') || ($char == '\'') || ($char == '`')) {
            $in_string    = true;
            $string_start = $char;
        } // end else if (is start of string)

        // ... for start of a comment (and remove this comment if found)...
        elseif ($char == '#'
                     || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
            // starting position of the comment depends on the comment type
            $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
            // if no "\n" exits in the remaining string, checks for "\r"
            // (Mac eol style)
            $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                                  ? strpos(' ' . $sql, "\012", $i+2)
                                  : strpos(' ' . $sql, "\015", $i+2);
            if (!$end_of_comment) {
                // no eol found after '#', add the parsed part to the returned
                // array if required and exit
                if ($start_of_comment > 0) {
                    $ret[]    = trim(substr($sql, 0, $start_of_comment));
                }
                return true;
            } else {
                $sql          = substr($sql, 0, $start_of_comment)
                                  . ltrim(substr($sql, $end_of_comment));
                $sql_len      = strlen($sql);
                $i--;
            } // end if...else
        } // end else if (is comment)

            // ... and finally disactivate the "/*!...*/" syntax if MySQL < 3.22.07
        elseif ($release < 32270
                     && ($char == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*')) {
            $sql[$i] = ' ';
        } // end else if

        // loic1: send a fake header each 30 sec. to bypass browser timeout
        $time1     = time();
        if ($time1 >= $time0 + 30) {
            $time0 = $time1;
            header('X-pmaPing: Pong');
        } // end if
    } // end for

        // add any rest to the returned array
    if (!empty($sql) && preg_match('/[^[:space:]]+/', $sql)) {
        $ret[] = $sql;
    }

    return true;
} // end of the 'splitSqlFile()' function


// list files
function get_file($path)
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


// simple version of post_param
function post_param($var_name)
{
    if (!isset($_POST[$var_name])) {
        return '';
    }
    $v = $_POST[$var_name];
    return $v;
}

// get vars
$cmd = post_param('cmd');
$db_hostname = post_param('db_hostname');
$db_name = post_param('db_name');
$db_username = post_param('db_username');
$db_passwd = post_param('db_passwd');
$db_prefix = post_param('db_prefix');

$admin_email = post_param('admin_email');
$admin_username = post_param('admin_username');
$admin_passwd = post_param('admin_passwd');
$admin_passwd_confirm = post_param('admin_passwd_confirm');
$abs_url = post_param('abs_url');
$abs_path = post_param('abs_path');
$sample_data = post_param('sample_data');
$gd_ver = post_param('gd_ver');
$foo = parse_url($abs_url);
$path = $foo['path'];
if ($path != '/') {
    $path .= '/';
}

$sample_data = false;	// always install demo!

$err = '';
$today = date('Y-m-d');

// verify input
if (empty($db_hostname)) {
    $err .= '<li>Database server hostname is empty!</li>';
}
if (empty($db_name)) {
    $err .= '<li>Database name is empty!</li>';
}
if (empty($db_username)) {
    $err .= '<li>Database username is empty!</li>';
}
if (empty($db_passwd)) {
    $err .= '<li>Database password is empty!</li>';
}

if ($cmd == 'new') {
    if (empty($admin_email)) {
        $err .= '<li>Administrator email is empty!</li>';
    }
    if (empty($admin_username)) {
        $err .= '<li>Administrator username is empty!</li>';
    }
    if (empty($admin_passwd)) {
        $err .= '<li>Administrator password is empty!</li>';
    }
    if (empty($admin_passwd_confirm)) {
        $err .= '<li>Administrator password (confirm) is empty!</li>';
    }
    if ($admin_passwd != $admin_passwd_confirm) {
        $err .= '<li>Administrator password doesn\'t match!</li>';
    }
}

// if no form error, continue to db connection
if (empty($err)) {
    if (!$dbh = mysqli_connect($db_hostname, $db_username, $db_passwd)) {
        $err .= '<li>Can not connect to database. Check db configuration & password!</li>';
    }
}

// connect to db
if (empty($err)) {
    if (!$dbt = mysqli_select_db($dbh, $db_name)) {
        $err .= '<li>Can not connect to database. Check db configuration & password!</li>';
    }

    // compatibility with MySQL 5 Strict Mode
    $mysql_ver = substr(mysqli_get_server_info($dbh), 0, 1);
    if ($mysql_ver > 4) {
        mysqli_query($dbh, "SET @@global.sql_mode=''");
    }
}

// upgrade?
if (empty($err) && substr($cmd, 0, 2) == 'up') {
    $res = sql_query("SELECT * FROM ".$db_prefix."qe_config WHERE config_id='qe_version' LIMIT 1");
    $row = sql_fetch_array($res);
    $foo = explode('/', $row[1]);
    $cur_version = $foo[0];

    die('Sorry! You can\'t use this installer to upgrade. Please contact us for more information.');
}

// if no db error, create DB
if (empty($err)) {
    $sql = '';
    if ($cmd == 'new') {
        $fn = 'install.sql';
    } else {
        $fn = 'upgrade.sql';
    }

    $cmd_sql = array();
    $zp = gzopen($fn, "r");
    while ($j = gzgets($zp, 4096)) {
        $sql .= $j;
    }
    gzclose($zp);
    splitSqlFile($cmd_sql, $sql);
    foreach ($cmd_sql as $val) {
        $val = str_replace('__PREFIX__', $db_prefix, $val);
        sql_query($val);
    }
}

// if no db[2] error, update configuration
if (empty($err)) {
    // create admin user
    $qe_hash_key = random_str(32, false, 3);
    $today = date('Y-m-d');
    $admin_passwd = qhash($admin_passwd);
    $abs_path = str_replace('\\', '/', $abs_path);	// windows patch

    // empty fields => reset_passwd, user_activation_code, axsrf_toket, notes
    $backlink = '&lt;a href="'.$abs_url.'/index.php?[user_id]"&gt;Demo Site - Be Different, Be Unique&lt;/a&gt';
    sql_query("INSERT INTO ".$db_prefix."user (`user_id`, `user_passwd`, `user_email`, `user_level`, `user_since`, `admin_level`) VALUES ('$admin_username', '$admin_passwd', '$admin_email', '5', '$today', '5')");
    sql_query("UPDATE ".$db_prefix."config SET config_value='$abs_url' WHERE config_id='site_url' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."config SET config_value='$abs_path' WHERE config_id='abs_path' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."config SET config_value='$admin_email' WHERE config_id='site_email' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."config SET config_value='$today' WHERE config_id='site_start' LIMIT 1");
    sql_query("UPDATE ".$db_prefix."config SET config_value='$backlink' WHERE config_id='backlink_code' AND group_id='ke' LIMIT 1");

    // update menu
    sql_query("UPDATE ".$db_prefix."menu_set SET menu_cache=REPLACE(menu_cache, '__SITE__', '$abs_url')");
}

// install sample data?
if (empty($err) && !empty($sample_data)) {
    $sql = '';
    $cmd_sql = array();
    $zp = gzopen('demo.sql', "r");
    while ($j = gzgets($zp, 4096)) {
        $sql .= $j;
    }
    gzclose($zp);

    splitSqlFile($cmd_sql, $sql);
    foreach ($cmd_sql as $val) {
        $val = str_replace('__PREFIX__', $db_prefix, $val);
        sql_query($val);
    }
}

// ... and create db_config.php
if (empty($err)) {
    $tpl = '';
    $fp = fopen('db_config.tpl', 'r');
    while (!feof($fp)) {
        $tpl .= @fgets($fp, 4096);
    }
    $search = preg_match_all("/{\\$([a-zA-Z0-9\-_]+)}/", $tpl, $matches);
    foreach ($matches[1] as $val) {
        $tpl = str_replace('{$'.$val.'}', $$val, $tpl);
    }
    fclose($fp);

    $fp = @fopen('../includes/db_config.php', 'w-');
    @fwrite($fp, $tpl);
    @fclose($fp);

    // and create htaccess
    $tpl = '';
    $fp = fopen('_htaccess', 'r');
    while (!feof($fp)) {
        $tpl .= @fgets($fp, 4096);
    }
    $search = preg_match_all("/{\\$([a-zA-Z0-9\-_]+)}/", $tpl, $matches);
    foreach ($matches[1] as $val) {
        $tpl = str_replace('{$'.$val.'}', $$val, $tpl);
    }
    fclose($fp);

    $fp = @fopen('../_htaccess', 'w-');
    @fwrite($fp, $tpl);
    @fclose($fp);


    if (!file_exists('../includes/db_config.php')) {
        $err_cfg = 1;
    } else {
        $err_cfg = 0;
    }
}

// create output
if (!empty($err)) {
    $msg = '<font color="#FF0000"><ul>'.$err.'</ul>Press &lt;back&gt; on your browser and correct it!</font>';
} else {
    $msg = '<p><b>SUCCESS!</b> qEngine has been installed on your server.</p>'
          .'<p>qEngine has also created a user & administrator under the name you filled earlier. Use that username & password to manage '
          .'your directory.<br /> If there is any problem, do not hestitate to contact us. Thank you for choosing qEngine!</p>'
          .'<p>Don\'t forget to remove <b>install/</b> directory before using qEngine.</p>'
          .'<p>Now would be a good time to manage your website. <a href="../admin/login.php">Click to continue to administration panel</a></p>';
}

if (!empty($err_cfg)) {
    $msg = '<p><b>FAILED!</b> qEngine has reached the final step succesfully, but can not write configuration '
          .'file into <b>includes/db_config.php</b>. The good news is you can manually finish the final step by '
          .'following the following instruction:'
          .'<ol><li>Copy and paste the following text into a text editor.</li>'
          .'<li>Save it as <b>db_config.php</b>.</li>'
          .'<li>Upload the file using your FTP program onto your server in <b>includes</b> directory.</li>'
          .'<li>And go to <a href="../admin/login.php">admin panel</a>.'
          .'<li><b>Important!</b> Make sure there are no extra lines before and after the codes.</li></ol>'
          .'<p align="center"><textarea cols="80" rows="9">'.$tpl.'</textarea></p>';
}
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../skins/_common/default.css"/>
<title>Kemana: Installation</title>
</head>

<body>
	<div style="background:#000; text-align:center; padding:20px"><img src="../skins/_admin/images/qe.png" alt="logo" /></div>
	<div class="container">
		<h1>Installation Results</h1>
		<div class="well">
			<div><?php echo $msg ?></div>
		</div>
		<p class="alert alert-danger">Don't forget to remove <b>install/</b> folder before using qEngine.</p>
		<p><b>To increase security</b>, you may want to rename /admin folder, follow instruction in /includes/db_config.php for more information.</p>
	</div>
	</body>
</html>