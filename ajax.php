<?php
require './includes/user_init.php';

$cmd = get_param('cmd');
$q = get_param('query');
$limit = 20;

switch ($cmd) {
    case 'userOk':
        if (!empty($q)) {
            $foo = sql_qquery("SELECT user_id FROM ".$db_prefix."user WHERE user_id='$q' LIMIT 1");
            if (!empty($foo) || !preg_match("/^[[:alnum:]]+$/", $q)) {
                flush_json(0) ;
            } else {
                flush_json(1);
            }	// 1 = username is ok
        }
    break;


    case 'emailOk':
        if (!empty($q)) {
            if ($isLogin) {
                $foo = sql_qquery("SELECT user_email FROM ".$db_prefix."user WHERE (user_email='$q') AND (user_id!='$current_user_id') LIMIT 1");
            } else {
                $foo = sql_qquery("SELECT user_email FROM ".$db_prefix."user WHERE user_email='$q' LIMIT 1");
            }
            if (!empty($foo) || !validate_email_address($q)) {
                flush_json(0);
            } else {
                flush_json(1);
            }	// 1 = email is ok
        }
    break;


    case 'search_filter':
        $dir_id = get_param('dir_id');
        $cat_id = get_param('cat_id');
        $rating = get_param('rating');
        $search_mode = get_param('search_mode');

        $output = $row = $txt = array();
        get_dir_info($dir_id);

        // directory
        if (($search_mode == 'list') || (!$dir_info['config']['multi'])) {
            $dir_list = false;
            $txt['dir_select'] = $dir_info[$dir_id]['dir_inf']['dir_title'];
        } else {
            $dir_list = true;
            $txt['dir_select'] = create_select_form('dir_id', $dir_info['structure'], $dir_id);
        }

        // category
        if ($search_mode == 'list') {
            $cat_list = false;
            $txt['cat_select'] = $dir_info[$dir_id]['cat_structure'][$cat_id];
        } else {
            $cat_list = true;
            $txt['cat_select'] = create_select_form('cat_id', $dir_info[$dir_id]['cat_structure_top'], $cat_id, '('.$lang['l_all'].')');
        }

        // rating
        $rating_def[0] = '('.$lang['l_all'].')';
        $txt['rating_select'] = create_select_form('rating', $rating_def, $rating);

        // cf
        foreach ($dir_info[$dir_id]['cf_define'] as $row) {
            if ($row['is_searchable']) {
                $key = 'cf_'.$row['idx'];
                $val = stripslashes(get_param($key));
                switch ($row['cf_type']) {
                case 'select':
                    $foo = explode("\r\n", $row['cf_option']);
                    $fii = safe_send($foo, true);
                    $val = str_replace('=', '%3D', $val);	// as browser replace = with %3D, we need to restore the value
                    $foo = array_pair($fii, $foo, '('.$lang['l_all'].')');
                    $field = create_select_form($key, $foo, $val);
                break;

                case 'multi':
                    // definition
                    $foo = explode("\r\n", $row['cf_option']);
                    $foo = array_pair(safe_send($foo, true), $foo);

                    // value
                    if (empty($val)) {
                        $fii = checkbox_param($key, 'get', true);
                        if (!empty($fii)) {
                            $val = implode("\r\n", $fii);
                        }
                    } else {
                        $fii = array(str_replace('=', '%3D', $val));
                    }

                    // form
                    $field = create_checkbox_form($key, $foo, $fii, 1);
                break;

                case 'rating':
                    $field = create_select_form($key, $rating_def, $val);
                break;

                case 'country':
                    $clist = array();
                    $field = create_select_form($key, get_country_list(), $val, '('.$lang['l_all'].')');
                break;

                case 'date':
                    if (!empty($val) && !verify_date($val)) {
                        $val = $sql_today;
                    }
                    $field = "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"$val\" /> <span class=\"glyphicon glyphicon-remove\" style=\"cursor:pointer\" onclick=\"cleardate('$key')\"></span>
					<script>$('#$key').datepicker({format: 'yyyy-mm-dd'});</script>";
                break;

                case 'time':
                    // try to get 'time' value from time_form (marked by keyID_hou form field)
                    if (get_param($key.'_hou')) {
                        $val = time_param($key);
                    }
                    $disabled = get_param($key.'_disabled');
                    if (($disabled == 'true') || !$val) {
                        $dis = true;
                    } else {
                        $dis = false;
                    }
                    if (!empty($val) && !verify_time($val)) {
                        $val = '00:00';
                    }
                    $field = time_form($key, $val, 5, $dis)." <input type=\"hidden\" name=\"{$key}_disabled\" value=\"$disabled\" /><span class=\"glyphicon glyphicon-remove\" style=\"cursor:pointer\" onclick=\"cleartime('$key')\"></span>";
                break;


                default:
                    $field = false;
                break;
            }

                if ($field) {
                    $row['field'] = $field;
                    $output[] = quick_tpl($tpl_section['cf_list'], $row);
                }
            }
        }

        $txt['cat_id'] = $cat_id;
        $txt['dir_id'] = $dir_id;
        $txt['cf_list'] = implode($output, "\n");
        echo quick_tpl(load_tpl('var', $tpl_section['cf_form']), $txt);
    break;


    case 'backlink':
        if (!empty($q)) {
            if (verify_backlink($q)) {
                flush_json(1);
            } else {
                flush_json(0);
            }
        }
    break;


    default:
        flush_json(9999, 'Undefined ajax mode '.$cmd);
    break;
}
