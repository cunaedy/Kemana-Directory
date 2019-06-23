<?php
// common functions for admin
// part of qEngine
// copyright (c) C97.net, usage of this script outside C97 is strictly prohibited!
// please write to us for licensing: contact@c97.net

/* ------- ( GENERAL FUNCTIONS ) ------- */

// sending message and just die (for admin)
function admin_die($msg_txt, $url = '')
{
    global $config, $inc_folder, $lang;
    $admin = true;
    if ($msg_txt == 'admin_ok') {
        $mini = true;
        $msg_txt = $lang['msg']['admin_ok'];
    } elseif ($msg_txt == 'demo_mode') {
        $msg_txt = $lang['msg']['demo_mode'];
    }
    require($inc_folder.'/msg.php');
    die();
}


//-- security check for admin
// qE16: just an alias form permission_check function
function admin_check($required = 5, $auto_die = true)
{
    return permission_check($required, $auto_die);
}


/* ------- ( HTML FUNCTIONS ) ------- */


// to strip html tags more cleanly, and replace to $replace
// (c) admin at automapit dot com, contributor: uersoy at tnn dot net
function html2txt($document, $replace = '')
{
    $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
              '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
              '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
              '@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
    );
    $text = preg_replace($search, $replace, $document);
    return $text;
}


/* ------- ( MYSQL FUNCTIONS ) ------- */


// splitSqlFile => taken from phpMyAdmin (c)phpMyAdmin.net
// used in: restore.php, modplug_install.php (uninstall), reset.php
function splitSqlFile(&$ret, $sql, $release = '32270')
{
    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = false;
    $time0        = time();
    $ret          = array();

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


// generate & save permalink URL
// $title = title of article or user input url (eg: 'A Good Book', or 'a-good-book.php')
// $scriptname = the real script to open the url (eg: page.php)
// $targetidx = the real index key to open the url (eg: 1), leave blank or 0 if not needed
// $folder = virtual folder of permalink, leave blank if not needed, when $auto=false, it will ignore $folder
// $auto = set to true so the function will auto generate the url, or false to use user input url (see $title) --> if the url already exists, the function will return false
// $write = set to true so the function will save the generated url to permalink db, false only to get the url
function generate_permalink($title, $scriptname, $targetidx, $targetparam = '', $folder = '', $auto = false, $write = false)
{
    global $db_prefix, $config;
    if ($auto) {
        $t = $j = cleanForShortURL($title);
        $ok = false;
        $i = 1;
        while (!$ok) {
            $i++;
            $jj = "$folder/$j.php";
            if ($jj[0] == '/') {
                $jj = substr($jj, 1);
            }
            $pfoo = sql_qquery("SELECT * FROM ".$db_prefix."permalink WHERE (url='$jj') LIMIT 1");
            if (empty($pfoo['idx'])) {
                $ok = true;
            } else {
                $j = $t.'-'.$i;
            }
        }

        if ($config['adp_extension']) {
            $t = $j.'.'.$config['adp_extension'];
        } else {
            $t = $j;
        }
        $permalink = empty($folder) ? $t : $folder.'/'.$t;
    } else {
        $permalink = cleanForShortURL($title, true);
    }

    // does real url exist in permalink db?
    $foo = sql_qquery("SELECT * FROM ".$db_prefix."permalink WHERE (target_script='$scriptname') AND (target_idx = '$targetidx') AND (target_param = '$targetparam') LIMIT 1");
    if (!$foo) {
        // create new
        $new = true;
        $exists = sql_qquery("SELECT * FROM ".$db_prefix."permalink WHERE (url='$permalink') LIMIT 1");
    } else {
        // update
        $new = false;
        $old_id = $foo['idx'];
        $old_url = $foo['url'];

        // is permalink changed?
        if ($foo['url'] != $permalink) {
            $permalink_changed = true;
        } else {
            $permalink_changed = false;
        }
        $exists = sql_qquery("SELECT * FROM ".$db_prefix."permalink WHERE (url='$permalink') AND (idx!='$foo[idx]') LIMIT 1");
    }

    if (!empty($exists)) {
        return false;
    } else {
        $result = $permalink;
    }	// not unique!

    if ($write) {
        if ($new) {
            sql_query("INSERT INTO ".$db_prefix."permalink SET url='$permalink', target_script='$scriptname', target_idx = '$targetidx', target_param = '$targetparam'");
        } else {
            sql_query("UPDATE ".$db_prefix."menu_set SET menu_cache=REPLACE(menu_cache, '/$old_url\"', '/$permalink\"')");
            sql_query("UPDATE ".$db_prefix."permalink SET url='$permalink' WHERE target_script='$scriptname' AND target_idx = '$targetidx' AND target_param='$targetparam' LIMIT 1");
        }
    }
    return $result;
}


function add_new_language($lang_id, $lang_key, $lang_value)
{
    global $db_prefix;

    // list of lang
    $lang_list = array();
    $res = sql_query("SELECT DISTINCT lang_id, lang_value FROM ".$db_prefix."language WHERE lang_key='_config:lang_name' ORDER BY lang_id");
    while ($row = sql_fetch_array($res)) {
        $lang_list[] = $l_id = $row['lang_id'];
        $l_key = addslashes($lang_key);
        $l_val = addslashes($lang_value);

        // detect previous val
        $foo = sql_qquery("SELECT * FROM ".$db_prefix."language WHERE lang_id='$l_id' AND lang_key='$l_key' LIMIT 1");
        if (empty($foo)) {
            sql_query("INSERT INTO ".$db_prefix."language SET lang_id='$l_id', lang_key='$l_key', lang_value='$l_val'");
        } else {
            if ($lang_id == $l_id) {
                sql_query("UPDATE ".$db_prefix."language SET lang_value='$l_val' WHERE lang_id='$l_id' AND lang_key='$l_key' LIMIT 1");
            }
        }
    }
}


// pid = primary ID of the logged item
// title = title of the logged item
// action = see constants above
// $olds = old values (empty to ignore) ARRAY
// $news = new values (empty to ignore) ARRAY
// $table = table name
function qadmin_log($pid, $title, $action, $old, $new, $table)
{
    global $current_user_id, $db_prefix;
    $fn = basename($_SERVER['SCRIPT_NAME']);

    // serialize old & new values
    if (!empty($old)) {
        $old = base64_encode(gzcompress(serialize($old)));
    }
    if (!empty($new)) {
        $new = base64_encode(gzcompress(serialize($new)));
    }
    if (is_array($new)) {
        $new = '';
    }
    $ip = get_ip_address();

    sql_query("INSERT INTO ".$db_prefix."qadmin_log
        SET log_date=UNIX_TIMESTAMP(), log_file='$fn', log_table='$table', log_user='$current_user_id', log_ip='$ip', log_pid='$pid', log_title='$title', log_action='$action', log_previous='$old', log_now='$new'");
}
