<?php
// get module's inline config, which is contained in $mod_ini
global $rating_def;
$mode = mod_param('mode');			// mode = rate or comment
$mod_id = mod_param('mod_id');		// identifier, eg news, link, list, etc (aka GROUP)
$item_id = mod_param('item_id');	// item_id of $mod_id (GROUP)
$title = mod_param('title');		// title (safe_send)
$sort = mod_param('sort', 'latest');	// sorting
$items = mod_param('items', 5);	// number of comment per page on window mode
if ($mod_id == 'conc') {
    $mode = 'conc';
}

// get rules
$row = sql_qquery("SELECT * FROM ".$db_prefix."qcomment_set WHERE mod_id = '$mod_id' LIMIT 1");

if (empty($row)) {
    $mode = 'invalid';
} else {
    $comment_mode = $row['comment_mode'];
    if ($comment_mode == 2) {
        $rating_box = false;
    } else {
        $rating_box = true;
    }
    if ($row['comment_on_comment']) {
        $conc = true;
    } else {
        $conc = false;
    }
}

// just do it!!
switch ($mode) {
    case 'rate':
        $module_mode = 'rate';

        // check cookies
        $cookies = empty($_COOKIE['rating']) ? '' : $_COOKIE['rating'];
        $ident = $mod_id.'_'.$item_id;
        if (!empty($cookies[$ident])) {
            $disabled = true;
        } else {
            $disabled = false;
        }

        // load mod's template
        // for 'rate', get from db with mod name [mod]*rate, and item_id=9999999
        $tpl = load_tpl('mod', 'module_qcomment_window.tpl');
        $row = sql_qquery("SELECT * FROM ".$db_prefix."qcomment WHERE mod_id='$mod_id*rate' AND item_id='$item_id' LIMIT 1");
        if (empty($row)) {
            $row['comment_title'] = '0|0';
        }

        // rate
        $f = explode('|', $row['comment_title']);
        $row['avg_star'] = rating_img($f[0]);
        $row['avg_rate'] = num_format($f[0], 2);
        $row['freq_rate'] = num_format($f[1]);
        $row['rate_select'] = create_select_form('rate', $rating_def, '', '', $disabled);
        $row['mod_id'] = $mod_id;
        $row['item_id'] = $item_id;
        $row['item_title'] = safe_send($title);

        // output must be contained in $output
        $output = quick_tpl($tpl, $row);
    break;


    case 'comment':
        $output = qcache_get('qcomment_window_'.$mod_id.'_'.$item_id);
        if (empty($output)) {
            $module_mode = 'comment';

            // load mod's template
            $tpl = load_tpl('mod', 'module_qcomment_window.tpl');

            // num of comment
            $f = sql_qquery("SELECT COUNT(*) AS total FROM ".$db_prefix."qcomment WHERE mod_id='$mod_id' AND item_id='$item_id' AND comment_approve='1'");
            $num = $f[0];

            // get comments
            $foo = array('block_comment' => '');
            $res = sql_query("SELECT * FROM ".$db_prefix."qcomment WHERE mod_id='$mod_id' AND item_id='$item_id' AND comment_approve='1' ORDER BY comment_id LIMIT 3");
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

                // helpful?
                $f = explode('|', $row['comment_helpful']);
                if (empty($f[1])) {
                    $row['comment_helpful'] = '';
                } else {
                    $row['comment_helpful'] = sprintf($lang['l_comment_helpful'], $f[0], $f[1]);
                }

                // conc?
                $row['conc_num'] = '';
                if ($conc) {
                    $res2 = sql_qquery("SELECT COUNT(*) AS ctotal FROM ".$db_prefix."qcomment WHERE mod_id='conc' AND item_id='$row[comment_id]' AND comment_approve='1' LIMIT 1");
                    if ($res2['ctotal']) {
                        $row['conc_num'] = sprintf($lang['l_conc_num'], num_format($res2['ctotal']));
                    }
                }

                $row['comment_date'] = convert_date($row['comment_date']);
                $foo['block_comment'] .= quick_tpl($tpl_block['comment'], $row);
            }

            // rating
            $foo['rating_avg'] = '';
            if ($rating_box) {
                $avg = sql_qquery("SELECT AVG(comment_rate) FROM ".$db_prefix."qcomment WHERE mod_id='$mod_id' AND item_id='$item_id' AND comment_approve='1' AND comment_rate>0");
                $foo['rating_avg'] = 'Average Ratings: '.rating_img($avg[0]);
            }

            // output must be contained in $output
            if (!$num) {
                $foo['nl'] = $lang['l_no_comment'];
            } elseif ($num == 1) {
                $foo['nl'] = $lang['l_one_comment'];
            } else {
                $foo['nl'] = sprintf($lang['l_more_comment'], $num);
            }

            $foo['n'] = num_format($num);
            $foo['safetitle'] = safe_send($title);
            $foo['safeurl'] = safe_send(cur_url(false));
            $foo['mod_id'] = $mod_id;
            $foo['item_id'] = $item_id;
            $output = quick_tpl($tpl, $foo);
            qcache_update('qcomment_window_'.$mod_id.'_'.$item_id, $output);
        }

        // num of comment to approve -- this should not be cached!
        $f = sql_qquery("SELECT COUNT(*) AS total FROM ".$db_prefix."qcomment WHERE mod_id='$mod_id' AND item_id='$item_id' AND comment_approve='0'");
        $num_to_approve = $f[0];
        if ($current_admin_level) {
            $output .= '<div class="bg-danger">There are '.num_format($num_to_approve).' comments to approve!</div>';
        }
    break;


    case 'invalid':
        $output = '<!-- invalid mod_id, please define it in acp! -->';
    break;


    default:
        $output = '<!-- qcomment error: invalid mode -->';
    break;
}

// due to old codes, qcomment modify mod_id (which should not be modified, but you know... old codes)
$mod_id = 'qcomment';
$mod_content_edit_url = $config['site_url'].'/'.$config['admin_folder'].'/task.php?mod=qcomment&amp;run=edit.php&amp;qadmin_cmd=list&amp;filter_by=2';
