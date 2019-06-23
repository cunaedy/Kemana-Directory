<?php
function format_comment(&$row, $rating_box, $conc)
{
    global $conc_txt;
    $conc_txt = '';
    if (empty($row['comment_user'])) {
        $row['comment_user'] = $lang['l_guest'];
    }

    // rating?
    if (($rating_box) && ($row['comment_rate'])) {
        $row['rating'] = rating_img($row['comment_rate'], 12);
    } else {
        $row['rating'] = '';
    }

    // helpful?
    $f = explode('|', $row['comment_helpful']);
    if (empty($f[1])) {
        $row['comment_helpful'] = '';
    } else {
        $row['comment_helpful'] = sprintf("%s of %s people find this comment useful", $f[0], $f[1]);
    }

    // conc?
    if ($conc) {
        get_conc($row['comment_id']);
        $row['conc_id'] = $row['comment_id'];
        $row['conc_title'] = $row['comment_title'];
        $row['conc_title_encrypted'] = safe_send('[CONC] Comment on '.$row['comment_title']);
        $row['conc_msg'] = $conc_txt;
    }

    $row['t'] = safe_send($row['comment_title']);
    $row['comment_date'] = convert_date($row['comment_date']);
    $row['comment_body'] = word_censor($row['comment_body']);
}


// comment on comment
function get_conc($i)
{
    global $conc_txt, $db_prefix, $tpl_section, $lang;
    $tpl_section['conc_item'] = load_tpl('var', $tpl_section['conc_item']);
    $cres = sql_query("SELECT * FROM ".$db_prefix."qcomment WHERE mod_id='conc' AND item_id='$i' ORDER BY comment_id");
    while ($crow = sql_fetch_array($cres)) {
        if (empty($crow['comment_user'])) {
            $crow['comment_user'] = $lang['l_guest'];
        }
        $crow['conc_id'] = $crow['comment_id'];
        $crow['conc_title'] = $crow['comment_title'];
        $crow['conc_title_encrypted'] = safe_send('[CONC] Comment on '.$crow['comment_title']);
        $crow['comment_date'] = convert_date($crow['comment_date']);
        $conc_txt .= quick_tpl($tpl_section['conc_item'], $crow);
        get_conc($crow['comment_id']);
    }
}


$m = get_param('m');			// identifier (eg, page, comment, review, portal, etc), also used to get rules
$i = get_param('i');			// index, or item_id
$p = get_param('p');			// page
$t = get_param('t');			// title (safe_send)
$c = get_param('comment_id');	// comment id
$box = get_param('box');		// show/hide comment form
$rate = get_param('rate');		//
$save = post_param('save');	// save comment?
$approve = get_param('approve');	// approve comment?
$hold = get_param('hold');	// hold comment?
$trash = get_param('trash');	// trash comment?
$key = get_param('key');		// secret key
$title = safe_receive($t);		// title
$item_url = safe_receive(get_param('u'));
$helpful = get_param('helpful');	// helpful mode
$window = get_param('window');	// windowed mode
$ajax = get_param('ajax');

if (empty($m)) {
    $m = post_param('m');
}
if (empty($i)) {
    $i = post_param('i');
}

// determine mode
$mode = 'blank';
if ($t && !$box) {
    $mode = 'show_comment';
}	// if title defined but not show comment form => show comment only
if ($t && $box) {
    $mode = 'show_box';
}		// if title defined but not show comment form => show comment & form
if ($save) {
    $mode = 'save_comment';
}		// if save mode, save comment
if ($rate) {
    $mode = 'save_rate';
}			// if save mode, save rating
if ($helpful) {
    $mode = 'helpful';
}		// if helpful mode, save helpful info

// quick approval -> if login as admin, or has secret key
if ($hold && $current_admin_level) {
    sql_query("UPDATE ".$db_prefix."qcomment SET comment_approve='0' WHERE comment_id='$hold' LIMIT 1");
    $success = mysqli_affected_rows($dbh);
    qcache_clear();

    if (empty($key) && $success) {
        flush_json(1);
    }		// if success (and removed from UI) => return 1
    elseif (empty($key) && !$success) {
        flush_json(9999, 'Could not hold comment right now. Please try again!');
    }	// if failed (and removed from UI) => return json false
    else {
        redir();
    }
}
if ($approve && ($current_admin_level || (qhash('qcomment_ok_'.$approve) == $key))) {
    sql_query("UPDATE ".$db_prefix."qcomment SET comment_approve='1' WHERE comment_id='$approve' LIMIT 1");
    $success = mysqli_affected_rows($dbh);
    qcache_clear();

    if (empty($key) && $success) {
        flush_json(1);
    }		// if success (and removed from UI) => return 1
    elseif (empty($key) && !$success) {
        flush_json(9999, 'Could not approve comment right now. Please try again!');
    }	// if failed (and removed from UI) => return json false
    else {
        redir();
    }
}
if ($trash && ($current_admin_level || (qhash('qcomment_no_'.$trash) == $key))) {
    sql_query("DELETE FROM ".$db_prefix."qcomment WHERE comment_id='$trash' LIMIT 1");
    $success = mysqli_affected_rows($dbh);
    qcache_clear();

    if (empty($key) && $success) {
        flush_json(1);
    }		// if success (and removed from UI) => return 1
    elseif (empty($key) && !$success) {
        flush_json(9999, 'Could not remove comment right now. Please try again!');
    }	// if failed (and removed from UI) => return json false
    else {
        redir();
    }
}

// get rules
$row = sql_qquery("SELECT * FROM ".$db_prefix."qcomment_set WHERE mod_id = '$m' LIMIT 1");

// define rules from database
if (!empty($row)) {
    $comment_mode = $row['comment_mode'];
    $approval = $row['comment_approval'];
    $member_only = $row['member_only'];
    $unique = $row['unique_comment'];
    $conc = $row['comment_on_comment'];
    $captcha = $row['captcha'];
    $helpful = $row['comment_helpful'];
    $detail = $row['detail'];
} else {
    if ($helpful) {
        $unique = $comment_mode = $conc = false;
    } else {
        msg_die($lang['msg']['qcomment_err_1']);
    }
}


if (!$isLogin) {
    $captcha = true;
}												// guest must enter captcha
if ($unique && !$member_only) {
    $member_only = true;
}							// member only for unique
if ($comment_mode == 2) {
    $rating_box = false;
} else {
    $rating_box = true;
}		// rating box only in rating and comment & rating mode
if ($window) {
    $ipp = 5;
}
if ($helpful && $window) {
    $helpful_js = false;
} 								// helpful form only avail in full screen
elseif ($helpful && !$window) {
    $helpful_js = true;
} elseif (!$helpful && !$window) {
    $helpful_js = false;
}

// comment mode
switch ($mode) {
    // show comment with box OR/AND show input box only OR/AND windowed mode (aka shown with inside another page, but without comment box & pagination)
    // as input box depends on several factors (like login, unique, etc) AND windowed mode basically the same as show comment;
    // so, it would be wasting my time to recreate separate functions to do the same job
    case 'show_comment':
    case 'show_box':
     if (empty($title)) {
         msg_die($lang['msg']['qcomment_err_2']);
     }

     // open tpl
     if ($member_only && !$isLogin) {
         $show_box = false;
     } else {
         $show_box = true;
     }
     if ($unique) {
         $foo = sql_qquery("SELECT comment_user FROM ".$db_prefix."qcomment WHERE mod_id='$m' AND item_id='$i' AND comment_user='$current_user_id' LIMIT 1");
         if (!empty($foo)) {
             $show_box = false;
         }
     }

     $tpl = load_tpl('mod', 'module_qcomment.tpl');
     $foo = load_section('mod', 'module_qcomment_section.tpl');
     $txt['block_comment'] = '';

     // get num of comments
     $num = 0;
     if ($mode == 'show_comment') {
         // quick approval
         $txt['block_comment_quick_approval'] = '';
         if ($current_admin_level && $mode == 'show_comment') {
             $res = sql_query("SELECT * FROM ".$db_prefix."qcomment WHERE mod_id='$m' AND item_id='$i' AND comment_approve='0' ORDER BY comment_approve, comment_id");
             while ($row = sql_fetch_array($res)) {
                 if (empty($row['comment_user'])) {
                     $row['comment_user'] = $lang['l_guest'];
                 }

                 // rating?
                 if (($rating_box) && ($row['comment_rate'])) {
                     $row['rating'] = rating_img($row['comment_rate'], 12);
                 } else {
                     $row['rating'] = '';
                 }

                 $row['comment_date'] = convert_date($row['comment_date']);
                 $row['comment_body'] = word_censor($row['comment_body']);
                 $txt['block_comment_quick_approval'] .= quick_tpl($tpl_block['comment_quick_approval'], $row);
             }
         }

         // show approved comments
        if (!$current_admin_level) {
            $txt['block_comment'] = qcache_get('qcomment_main_'.$m.'_'.$i);
        } else {
            $txt['block_comment'] = '';
        }	// disable cache for admin, thus it will display admin tools
        if (empty($txt['block_comment'])) {
            // get comments
            $res = sql_query("SELECT * FROM ".$db_prefix."qcomment WHERE mod_id='$m' AND item_id='$i' AND comment_approve='1' ORDER BY comment_id");
            while ($row = sql_fetch_array($res)) {
                $num++;
                format_comment($row, $rating_box, $conc);
                $txt['block_comment'] .= quick_tpl($tpl_block['comment'], $row);
            }
            if (!$current_admin_level) {
                qcache_update('qcomment_main_'.$m.'_'.$i, $num.'|'.$txt['block_comment']);
            }
        } else {
            $fii = strpos($txt['block_comment'], '|');
            $num = substr($txt['block_comment'], 0, $fii);
            if (!is_numeric($num)) {
                $num = '~';
            }
            $txt['block_comment'] = substr($txt['block_comment'], $fii + 1);
        }
     }

     // show avg rating
     $txt['rating_avg'] = '';
     if ($rating_box) {
         $avg = sql_qquery("SELECT AVG(comment_rate) FROM ".$db_prefix."qcomment WHERE mod_id='$m' AND item_id='$i' AND comment_approve='1' AND comment_rate>0");
         $txt['rating_avg'] = rating_img($avg[0]);
     }

     // the rest
     qvc_init(3);

     if (!$row = load_form('comment')) {
         $row = create_blank_tbl($db_prefix.'qcomment');
     }
     if ($rating_box) {
         $txt['rating_select'] = create_select_form('comment_rate', $rating_def);
     }

     $txt = array_merge($txt, $row);
     $txt['t'] = safe_send($title);
     $txt['num'] = ($mode == 'show_box') ? 0 : num_format($num);
     $txt['jtitle'] = str_replace(array("'", '&#039;'), "\'", $title);
     $txt['title'] = $title;
     $txt['item_title'] = safe_send($title);
     $txt['item_url'] = safe_send($item_url);
     $txt['item_url_link'] = $item_url;
     $txt['item_id'] = $i;
     $txt['mod_id'] = $m;
     $txt['current_user_id'] = $isLogin ? $current_user_id : $lang['l_guest'];
     $txt['the_title'] = ($window) ? '' : quick_tpl($tpl_section['mod_the_title'], $txt);
     $txt['comment_body'] = $row['comment_body'];
     // $txt['comment_area'] = bbc_area ('comment_body', $row['comment_body'], 500, 150);

     // output
     if ($show_box) {
         $txt['comment_box'] = quick_tpl(load_tpl('var', $tpl_section['mod_comment_box']), $txt);
     } else {
         $txt['comment_box'] = quick_tpl($tpl_section['mod_no_comment_box'], $txt);
     }

     if ($helpful_js) {
         $txt['helpful_js'] = quick_tpl($tpl_section['mod_helpful_js'], $txt);
     } else {
         $txt['helpful_js'] = '';
     }

     if ($window) {	// windowed mode (as i'm too lazy to repeat the script in window.php)
         $popup = true;
         $show_box = false;
         $txt['pagination'] = '';
         $txt['comment_box'] = quick_tpl($tpl_section['mod_more_comment'], $txt);
     }

     if ($m == 'conc' && !$box) {
         $popup = true;
         ;
         $txt['comment_box'] = '';
     }
     if ($m == 'conc' && $box) {
         $popup = true;
         ;
     }

     // flush
     if ($mode == 'show_comment') {
         $txt['main_body'] = quick_tpl($tpl, $txt);
     } else {
         $txt['main_body'] = $txt['comment_box'];
     }
    break;


    case 'save_comment':
     // member only?
     if ($member_only && !$isLogin) {
         msg_die($lang['msg']['not_member']);
     }

     // unique? (NEED MEMBER ONLY RULE)
     if ($unique) {
         $foo = sql_qquery("SELECT comment_user FROM ".$db_prefix."qcomment WHERE mod_id='$m' AND item_id='$i' AND comment_user='$current_user_id' LIMIT 1");
         if (!empty($foo)) {
             msg_die($lang['msg']['qcomment_err_3']);
         }
     }

     // save comment
     $item_title = safe_receive(post_param('t'));
     $item_url = safe_receive(post_param('u'));
     $comment_title = post_param('comment_title');
     $comment_body = post_param('comment_body');
     $comment_rate = post_param('comment_rate');
     $visual = post_param('visual');
     save_form('comment');

     // verify entries
     if (($captcha) && (qhash($visual) != qvc_value())) {
         msg_die($lang['msg']['captcha_error']);
     }
     if (!$rating_box) {
         $comment_rate = 0;
     }
     if (($comment_rate < 0) || ($comment_rate > 5) || (!is_numeric($comment_rate))) {
         $comment_rate = 0;
     }
     if (empty($comment_body) || (strlen($comment_body) < 1)) {
         msg_die($lang['msg']['qcomment_err_4']);
     }
     if (empty($comment_title)) {
         $comment_title = $lang['l_untitled'];
     }
     if (!$isLogin) {
         $comment_user = $lang['l_guest'];
     } else {
         $comment_user = $current_user_id;
     }

     // set status to?
     reset_form();
     if (!$approval) {
         $approved = 1;
     } else {
         $approved = 0;
     }

     // reset cache
     qcache_clear();

     // insert into db
     sql_query("INSERT INTO ".$db_prefix."qcomment VALUES ('', '$m', '$i', '$item_title', '$item_url', '$comment_user', '$comment_title', '$comment_body', '$sql_today', '$comment_rate', '0|0', $approved)");
     $idx = mysqli_insert_id($dbh);
     create_notification('', "$comment_user posted a new comment on <a href=\"$item_url\">$item_title</a>", $config['site_url'].'/'.$config['admin_folder'].'/task.php?mod=qcomment&run=edit.php&id='.$idx, true);

     // send notification via email (more detailed)
     $quick_ok = $config['site_url'].'/task.php?mod=qcomment&amp;approve='.$idx.'&amp;key='.qhash('qcomment_ok_'.$idx);
     $quick_no = $config['site_url'].'/task.php?mod=qcomment&amp;trash='.$idx.'&amp;key='.qhash('qcomment_no_'.$idx);
     $email_body = "<p>$comment_user posted a new comment on <a href=\"$item_url\">$item_title</a>:</p><p><b>$comment_title</b></p><p>$comment_body</p><hr /><p>To quickly approve this comment, <a href=\"$quick_ok\">click here</a>.</p><p>To remove this comment, <a href=\"$quick_no\">click here</a>.</p>";
     email($config['site_email'], "[$config[site_name]] $comment_user posted a new comment on \"$item_title\"", $email_body, true);

     if ($approval) {
         msg_die($lang['msg']['qcomment_ok_1']);
     } else {
         msg_die($lang['msg']['ok']);
     }
    break;


    case 'save_rate':
     // check cookies
     $ident = $m.'_'.$i;
     $cookies = empty($_COOKIE['rating']) ? '' : $_COOKIE['rating'];
     if (!empty($cookies[$ident])) {
         msg_die($lang['msg']['qcomment_err_5']);
     }
     if (($rate > 5) || ($rate < 1)) {
         msg_die($lang['msg']['qcomment_err_6']);
     }

     // check in db
     $item_title = safe_receive($t);
     $row = sql_qquery("SELECT * FROM ".$db_prefix."qcomment WHERE mod_id='$m*rate' AND item_id='$i' LIMIT 1");
     if (empty($row)) {
         $row['comment_title'] = '0|0';
     }

     // rate
     $f = explode('|', $row['comment_title']);
     $avg = ($f[0] * $f[1] + $rate) / (++$f[1]);
     $t = "$avg|$f[1]";
     if (!empty($row['item_id'])) {
         sql_query("UPDATE ".$db_prefix."qcomment
		           SET comment_title = '$t'
				   WHERE mod_id='$m*rate' AND item_id='9999999' LIMIT 1");
     } else {
         sql_query("INSERT INTO ".$db_prefix."qcomment VALUES ('', '$m*rate', '$i', '$item_title', '$item_url', 'rate', '$t', '', '$sql_today', 0, '0|0', 1)");
     }

     // set cookies for 1 year
     $exp = time() + 31536000;
     setcookie("rating[$ident]", 1, $exp);

     // reset cache
     qcache_clear();

     // done
     msg_die($lang['msg']['qcomment_ok_2']);
    break;


    case 'helpful':
     $yes = get_param('yes');
     $no = get_param('no');

     $cookies = empty($_COOKIE['helpful']) ? '' : $_COOKIE['helpful'];
     if (!empty($cookies[$c])) {
         msg_die($lang['msg']['qcomment_err_5']);
     }

     // get help
     $res = sql_query("SELECT comment_helpful FROM ".$db_prefix."qcomment WHERE comment_id='$c' LIMIT 1");
     $row = sql_fetch_array($res);
     if (empty($row['comment_helpful'])) {
         $row['comment_helpful'] = '0|0';
     }
     $f = explode('|', $row['comment_helpful']);
     if ($yes) {
         $f[0]++;
         $f[1]++;
     } elseif ($no) {
         $f[1]++;
     }

     // set cookies for 1 year
     $exp = time() + 31536000;
     setcookie("helpful[$c]", 1, $exp);

     // reset cache
     qcache_clear();

     $h = implode('|', $f);
     sql_query("UPDATE ".$db_prefix."qcomment SET comment_helpful='$h' WHERE comment_id='$c' LIMIT 1");
     msg_die('ok');
    break;


    default:
     redir();
    break;
}
