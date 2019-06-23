<?php
// qAdmin, the fastest (laziest) way to create admin interface ... ADMIN ONLY!
// part of qEngine
// copyright (c) C97.net, usage of this script outside C97 is strictly prohibited!
// please write to us for licensing: contact@c97.net

define('LOG_ADD', 1);
define('LOG_EDIT', 2);
define('LOG_DEL', 3);
define('LOG_UPLOAD', 4);
define('LOG_DEL_FILE', 5);

###############
###
### Support functions
###
###############


function get_filename($table, $field, $fn)
{
    $ok = false;
    $foo = pathinfo($fn);
    $ext = $foo['extension'];
    while (!$ok) {
        $tmp_name = random_str(16).'.'.$ext;	// it's 18,446,744,073,709,551,616 possibilities
        // it's like having all people in the world submitting 3 billion files... EACH!
        $res = sql_query("SELECT * FROM $table WHERE $field='$tmp_name' LIMIT 1");
        $row = sql_fetch_array($res);
        if (empty($row)) {
            $ok = true;
        }
    }
    return $tmp_name;
}


###############
###
### Compile (display) qadmin
###
###############


// $def = data definition
// $cfg = configuration
function qadmin_compile($def, $cfg)
{
    global $config, $tpl_section, $tpl_block, $db_prefix, $lang;

    // init
    $tmp = '';
    $tab_list = $ezd = $ezf = $row = array();
    $file = false;
    if (empty($cfg['action'])) {
        $t = parse_url(urldecode(cur_url()));
        $cfg['action'] = basename($t['path']);
    }

    if (strpos($cfg['action'], '?')) {
        $cfg['action'] .= '&';
    } else {
        $cfg['action'] .= '?';
    }

    // template
    if (empty($cfg['template'])) {
        $cfg['template'] = 'default';
    }
    if ($cfg['template'] == 'default') {
        load_section('adm', 'qadmin_ezf_section.tpl');
        load_section('adm', 'qadmin_section.tpl');
    } else {
        load_section('adm', $cfg['template'].'_ezf_section.tpl');
        load_section('adm', $cfg['template'].'_section.tpl');
    }

    // buttons
    $cfg['tab_list'] = '';
    if (empty($cfg['back'])) {
        $cfg['back'] = "<a href=\"$cfg[action]\" style=\"background:#eee\"><span class=\"glyphicon glyphicon-chevron-left\"></span> <small>Back</small></a>";
    } else {
        $cfg['back'] = "<a href=\"$cfg[back]\" style=\"background:#eee\"><span class=\"glyphicon glyphicon-chevron-left\"></span> <small>Back</small></a>";
    }

    if ($cfg['cmd_new_enable']) {
        $cfg['savenew_button'] = $tpl_section['qadmin_savenew_button'];
    } else {
        $cfg['savenew_button'] = '';
    }

    // value (sql_select) for $def_val (default value)
    if (($cfg['cmd'] == 'update') && (!isset($cfg['sql_select']))) {
        $cfg['sql_select'] = "SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$cfg[primary_val]' LIMIT 1";
    } elseif ($cfg['cmd'] == 'new') {
        $def_val = create_blank_tbl($cfg['table']);
        $cfg['sql_select'] = '';
    }

    // execute sql
    if (!empty($cfg['sql_select'])) {
        $res = sql_query($cfg['sql_select']);
        $def_val = sql_fetch_array($res);
        if (($cfg['cmd'] == 'update') && empty($def_val)) {
            admin_die($lang['msg']['qadmin_item_not_found']);
        }
    }

    // form already filled?
    $f = load_form($cfg['table']);
    if (!empty($f)) {
        $def_val = $form_val = $f;
    }	// $def_val = we replace values from sql; but some field needs further processing (eg date/time), so we use $form_val

    // field
    foreach ($def as $key=>$val) {
        $val['value'] = empty($val['value']) ? '' : $val['value'];
        $val['size'] = empty($val['size']) ? 0 : $val['size'];
        $val['help'] = empty($val['help']) ? '' : addslashes($val['help']);
        $val['prefix'] = !empty($val['prefix']) ? $val['prefix'] : '';
        $val['suffix'] = !empty($val['suffix']) ? $val['suffix'] : '';
        $val['thisid'] = $cfg['table'].'-'.$val['field'];

        if ($val['value'] == 'sql') {
            $val['value'] = !isset($def_val[$val['field']]) ? '' : $def_val[$val['field']];
        }
        if (empty($val['help'])) {
            $val['help'] = '';
        } else {
            $val['help'] = quick_tpl($tpl_section['qadmin_help'], $val);
        }
        if (empty($val['required'])) {
            $val['required'] = '';
            $val['required_js'] = '';
        } else {
            $val['required'] = quick_tpl($tpl_section['ezform_required'], 0);
            $val['required_js'] = quick_tpl($tpl_section['ezform_required_js'], 0);
        }

        if (empty($val['type'])) {
            die("<b>Error!</b> $val[field] doesn\'t have type!");
        }
        switch ($val['type']) {
            // plain text
            case 'echo':
                if (empty($val['value'])) {
                    $val['value'] = '&nbsp;';
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_echo'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_echo'], $val);
            break;


            // link
            case 'url':
                if (empty($val['value'])) {
                    $val['value'] = '#';
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_url'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_url'], $val);
            break;

            // disabled
            case 'disabled':
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_disabled'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_disabled'], $val);
            break;

            // hidden value
            case 'hidden':
                $tmp .=  $ezd['ezd_'.$val['field']] = "<tr><td colspan=\"2\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border:none\"><input type=\"hidden\" name=\"$val[field]\" value=\"$val[value]\" /></td></tr>";
                $ezf['ezf_'.$val['field']] = "<input type=\"hidden\" name=\"$val[field]\" value=\"$val[value]\" />";
            break;

            // static (a combination of echo & hidden)
            case 'static':
                if (!empty($val['option'])) {
                    $val['display_value'] = empty($val['option'][$val['value']]) ? '-' : $val['option'][$val['value']];
                } else {
                    $val['display_value'] = $val['value'];
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_static'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_static'], $val);
            break;

            // email
            case 'email':
                $ezd['ezd_'.$val['field']] = $val['maxlength'] = $val['size'];
                $val['size'] = $val['size'] > 50 ? 50 : $val['size'];

                if (!empty($val['value'])) {
                    $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_email'], $val);
                    $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_email'], $val);
                } else {
                    $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_varchar'], $val);
                    $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_varchar'], $val);
                }
            break;

            // divider
            case 'divider':
            case 'div':
                $tab_list[] = $val['title'];
                $val['tabindex'] = count($tab_list) + 1;
                $tmp .= quick_tpl($tpl_section['qadmin_divider'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_divider'], $val);
            break;

            // mask
            case 'mask':
                $val['value'] = empty($val['option'][$val['value']]) ? '-' : $val['option'][$val['value']];
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_echo'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_echo'], $val);
            break;

            // short text
            case 'varchar':
            case 'permalink':
            case 'password':
                $val['maxlength'] = $val['size'];
                $val['size'] = $val['size'] > 50 ? 50 : $val['size'];

                if ($val['type'] == 'varchar') {
                    $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_varchar'], $val);
                    $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_varchar'], $val);
                } elseif ($val['type'] == 'permalink') {
                    if (empty($val['value'])) {
                        $val['permalink_path'] = empty($cfg['permalink_folder']) ? $config['site_url'].'/' : $config['site_url'].'/'.$cfg['permalink_folder'].'/';
                    } else {
                        $val['permalink_path'] = $config['site_url'].'/';
                    }

                    if (empty($val['help'])) {
                        $val['help'] = 'Create a search engine friendly URL. Leave empty to auto generate, or enter your own url.';
                        $val['help'] = quick_tpl($tpl_section['qadmin_help'], $val);
                    }
                    $val['size'] = $val['size'] > 32 ? 32 : $val['size'];
                    $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_permalink'], $val);
                    $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_permalink'], $val);
                } else {
                    $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_password'], $val);
                    $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_password'], $val);
                }
            break;

            // date
            case 'date':
                if (empty($val['value'])) {
                    $val['value'] = 'today';
                    $y = date('Y');
                    $m = date('m') - 1;
                    $d = date('d');
                    $val['js_value'] = "$y,$m,$d";
                } else {
                    $y = substr($val['value'], 0, 4);
                    $m = substr($val['value'], 5, 2) - 1;
                    $d = substr($val['value'], -2);
                    $val['js_value'] = "$y,$m,$d";
                }

                // user_val = user entered val in a form (some fields don't need more processing, but others ~like this one~ need more processing)
                if (!empty($form_val[$key.'_dd'])) {
                    $val['value'] = $form_val[$key.'_yy'].'-'.$form_val[$key.'_mm'].'-'.$form_val[$key.'_dd'];
                    $val['js_value'] = $form_val[$key.'_yy'].','.$form_val[$key.'_mm'].','.$form_val[$key.'_dd'];
                }

                $val['date_select'] = date_form($val['field'], date('Y'), 1, 1, $val['value']);

                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_date'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_date'], $val);
            break;

            // time
            case 'time':
                if (empty($val['value'])) {
                    $val['value'] = 'now';
                }
                if (!empty($form_val[$key.'_hou'])) {
                    $val['value'] = $form_val[$key.'_hou'].':'.$form_val[$key.'_min'];
                }
                $val['time_select'] = time_form($val['field'], $val['value']);
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_time'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_time'], $val);
            break;

            // text - $row['size'] is in "x,y"
            case 'text':
            case 'textarea':
                if (empty($val['size'])) {
                    $val['size'] = '500,200';
                }
                $s = explode(',', $val['size']);

                $val['x'] = $s[0]; $val['y'] = $s[1];
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_text'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_text'], $val);
            break;

            // code - $row['size'] is in "x,y"
            case 'code':
                if (empty($val['lang'])) {
                    $val['lang'] = 'html';
                }
                $s = explode(',', $val['size']);
                if (empty($val['size'])) {
                    $val['code_area'] = code_editor_area($val['field'], $val['value'], $val['lang']);
                } else {
                    $val['code_area'] = code_editor_area($val['field'], $val['value'], $val['lang'], $s[0], $s[1]);
                }

                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_code'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_code'], $val);
            break;

            // rte - $row['size'] is in "x,y"
            case 'wysiwyg':
                $s = explode(',', $val['size']);
                if (!empty($form_val[$key])) {
                    $val['value'] = html_entity_decode($val['value']);
                }
                if (!empty($val['wysiwyg_pagebreak'])) {
                    $pagebreak = true;
                } else {
                    $pagebreak = false;
                }
                if (empty($val['size'])) {
                    $val['rte_area'] = rte_area($val['field'], $val['value'], 0, 0, $pagebreak);
                } else {
                    $val['rte_area'] = rte_area($val['field'], $val['value'], $s[0], $s[1], $pagebreak);
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_wysiwyg'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_wysiwyg'], $val);
            break;

            // select
            case 'select':
                $val['edit_opt'] = '';
                if (!empty($val['editopt'])) {
                    $val['edit_opt'] = quick_tpl($tpl_section['qadmin_edit_opt'], $val);
                    $val['option'] = get_editable_option($val['editopt']);
                }
                if (!empty($val['required'])) {
                    $val['data_select'] = create_select_form($val['field'], $val['option'], $val['value'], '[ Please Select ]', 0, 'required="required"');
                } else {
                    $val['data_select'] = create_select_form($val['field'], $val['option'], $val['value'], '[ Please Select ]');
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_select'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_select'], $val);
            break;

            // radio
            case 'radio':
            case 'radioh':
                $val['edit_opt'] = '';
                if (!empty($val['editopt'])) {
                    $val['edit_opt'] = quick_tpl($tpl_section['qadmin_edit_opt'], $val);
                    $val['option'] = get_editable_option($val['editopt']);
                }

                foreach ($val['option'] as $k => $v) {
                    $val['option'][$k] = str_replace('\n', '<br />', $v);
                }
                $val['data_radio'] = create_radio_form($val['field'], $val['option'], $val['value']);
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_radioh'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_radioh'], $val);
            break;

            // radio
            case 'radiov':
                $val['edit_opt'] = '';
                if (!empty($val['editopt'])) {
                    $val['edit_opt'] = quick_tpl($tpl_section['qadmin_edit_opt'], $val);
                    $val['option'] = get_editable_option($val['editopt']);
                }

                foreach ($val['option'] as $k => $v) {
                    $val['option'][$k] = str_replace('\n', '<br />', $v);
                }
                $val['data_radio'] = create_radio_form($val['field'], $val['option'], $val['value'], 'v');
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_radiov'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_radiov'], $val);
            break;

            // file
            case 'file':
                $file = true;
                if (!empty($val['value'])) {
                    $val['view'] = $cfg['file_folder'].'/'.$val['value'];
                    @$val['size'] = num_format(filesize($val['view']));
                    $val['remove'] = $cfg['action']."qadmin_cmd=remove_file&amp;field=$val[field]&amp;primary_val=$cfg[primary_val]";
                    $val['viewfile'] = quick_tpl($tpl_section['qadmin_viewfile'], $val);
                } else {
                    $val['viewfile'] = quick_tpl($tpl_section['qadmin_upload'], $val);
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_file'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_file'], $val);
            break;

            // image
            case 'img':
            case 'image':
                $file = true;
                if (!empty($val['value'])) {
                    $val['view'] = $cfg['img_folder'].'/'.$val['value'];
                    @$val['size'] = num_format(filesize($val['view']));
                    $val['remove'] = $cfg['action']."qadmin_cmd=remove_file&amp;field=$val[field]&amp;primary_val=$cfg[primary_val]";
                    $val['viewimg'] = quick_tpl($tpl_section['qadmin_viewimg'], $val);
                } else {
                    $val['viewimg'] = quick_tpl($tpl_section['qadmin_upload'], $val);
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_img'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_img'], $val);
            break;

            // image series
            case 'img_set':
            case 'img_series':
                $file = true;
                $ok = false; $i = 0;
                $val['viewimg'] = '';
                if ($cfg['cmd'] == 'update') {
                    while (!$ok) {
                        $i++;
                        $fn = $val['prefix'].'_'.$cfg['primary_val'].'_'.$i;
                        $img_th = "$cfg[thumb_folder]/$fn.jpg";
                        $img_src = "$cfg[img_folder]/$fn.jpg";
                        if (file_exists($img_src)) {   // if thumbs avail
                            $val['view'] = $img_src;
                            @$val['size'] = num_format(filesize($img_src));
                            $val['remove'] = "<a href=\"".$cfg['action']."qadmin_cmd=remove_file&amp;field=$val[field]&amp;primary_val=$cfg[primary_val]&amp;idx=$i\"><span class=\"glyphicon glyphicon-remove\"></span> Delete</a>";
                            $val['viewimg'] .= "<p><a href=\"$img_src\" class=\"lightbox\"><img src=\"$img_th\" alt=\"image\" /></a><br /><span class=\"glyphicon glyphicon-file\"></span> $val[size] bytes $val[remove]</p>";
                        } else {
                            $ok = true;
                        }
                    }
                }
                $tmp .= $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_img_set'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_img'], $val);
            break;

            // image + thumb
            case 'thumb':
                $file = true;
                if (!empty($val['value'])) {
                    $val['view'] = $cfg['img_folder'].'/'.$val['value'];
                    $val['thumb'] = $cfg['thumb_folder'].'/'.$val['value'];
                    $val['size'] = num_format(filesize($val['view']));
                    $val['remove'] = $cfg['action']."qadmin_cmd=remove_file&amp;field=$val[field]&amp;primary_val=$cfg[primary_val]";
                    $val['viewthumb'] = quick_tpl($tpl_section['qadmin_viewthumb'], $val);
                } else {
                    $val['viewthumb'] = quick_tpl($tpl_section['qadmin_upload'], $val);
                }
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_thumb'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_thumb'], $val);
            break;

            // image resize
            case 'img_resize':
            case 'image_resize':
                $file = true;
                if (!empty($val['value'])) {
                    $val['view'] = $cfg['img_folder'].'/'.$val['value'];
                    $val['size'] = num_format(filesize($val['view']));
                    $val['remove'] = $cfg['action']."qadmin_cmd=remove_file&amp;field=$val[field]&amp;primary_val=$cfg[primary_val]";
                    $val['viewimg'] = quick_tpl($tpl_section['qadmin_viewimg'], $val);
                } else {
                    $val['viewimg'] = quick_tpl($tpl_section['qadmin_upload'], $val);
                };
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_img_resize'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_img_resize'], $val);
            break;

            // multi = stored as: <opt>\r\n<opt>\r\n<opt>
            // multi, = stored as: ,opt,opt,opt,	(make searching easier with LIKE %,opt,%)
            // multicsv = stored as: opt,opt,opt
            case 'multi':
            case 'multi,':
            case 'multicsv':
                $val['edit_opt'] = '';
                if (!empty($val['editopt'])) {
                    $val['edit_opt'] = quick_tpl($tpl_section['qadmin_edit_opt'], $val);
                    $val['option'] = get_editable_option($val['editopt']);
                }

                if (empty($val['size'])) {
                    $val['size'] = 3;
                }

                if ($val['type'] == 'multi') {
                    $val['value'] = explode("\r\n", $val['value']);
                } elseif ($val['type'] == 'multi,') {
                    $val['value'] = explode(',', substr($val['value'], 1, -1));
                } else {
                    $val['value'] = explode(',', $val['value']);
                }

                $fuu = array();
                if (!empty($form_val)) {
                    foreach ($form_val as $fk => $fv) {
                        if (strpos('*'.$fk, $key) == 1) {
                            $fuu[] = $fv;
                        }
                    }
                }	// to make things easier, find '*[field_name]' in form_val, not '[field_name]' :-)
                if (!empty($fuu)) {
                    $val['value'] = $fuu;
                }
                foreach ($val['option'] as $k => $v) {
                    $val['option'][$k] = str_replace('\n', '<br />', $v);
                }

                $val['data_multi'] = create_checkbox_form($val['field'], $val['option'], $val['value'], $val['size'], 'qadmin_form');
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_multi'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_multi'], $val);
            break;

            //
            case 'rating':
                global $rating_def;
                $val['edit_opt'] = '';
                $val['data_select'] = create_select_form($val['field'], $rating_def, $val['value']);
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_select'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_select'], $val);
            break;

            //
            case 'checkbox':
                $val['checkbox'] = create_tickbox_form($val['field'], $val['option'], $val['value']);
                $tmp .=  $ezd['ezd_'.$val['field']] = quick_tpl($tpl_section['qadmin_checkbox'], $val);
                $ezf['ezf_'.$val['field']] = quick_tpl($tpl_section['ezform_checkbox'], $val);
            break;

            // what?
            default:
                die("<b>Error!</b> $val[field] has unrecognized type of $val[type]!");
            break;
        }
    }

    // tabs
    $i = 1;
    foreach ($tab_list as $k => $v) {
        $i++;
        $tb = array('i' => $i, 'title' => $v);
        $cfg['tab_list'] .= quick_tpl($tpl_section['qadmin_tab_list_li'], $tb);
    }

    // show captchai
    if (!empty($cfg['captcha'])) {
        qvc_init();
        $ezd['ezd_captcha'] = $tmp .= quick_tpl($tpl_section['qadmin_captcha'], $val);
        $ezf['ezf_captcha'] = quick_tpl($tpl_section['ezform_captcha'], $val);
    }

    // show remove item (on UPDATE mode)
    if (($cfg['cmd'] == 'update') && ($cfg['cmd_remove_enable'])) {
        $ezd['ezd_remove'] = $tmp .= quick_tpl($tpl_section['qadmin_remove'], $cfg);
        $ezf['ezf_remove'] = quick_tpl($tpl_section['ezform_remove'], $cfg);
    }

    // last update
    if ($cfg['cmd'] == 'new') {
        $cfg['last_update'] = '';
    } elseif ($cfg['cmd'] == 'update') {
        if ($cfg['enable_log']) {
            $fn = basename($_SERVER['SCRIPT_NAME']);
            $tlog = sql_qquery("SELECT COUNT(*) AS count FROM ".$db_prefix."qadmin_log WHERE log_file='$fn' AND log_pid='$cfg[primary_val]' LIMIT 1");
            $rlog = sql_qquery("SELECT log_date, log_user FROM ".$db_prefix."qadmin_log WHERE log_file='$fn' AND log_pid='$cfg[primary_val]' ORDER BY log_id DESC LIMIT 1");
            $last_update = date('Y-m-d H:i:s', $rlog['log_date']);
            $cfg['last_update'] = "<p class=\"small\">This entry has been updated $tlog[count]&times;, last updated at $last_update by $rlog[log_user] <a href=\"qadmin_log.php?w=pid&amp;h=$fn&amp;pid=$cfg[primary_val]\" class=\"btn btn-default btn-xs\"><span class=\"glyphicon glyphicon-zoom-in\"></span> See details or restore changes</a></p>";
        } else {
            $cfg['last_update'] = '(Log has been disabled)';
        }
    }


    // table header & footer
    if ($file) {
        $cfg['enctype'] = 'multipart/form-data';
    } else {
        $cfg['enctype'] = 'application/x-www-form-urlencoded';
    }
    $row['title'] = $cfg['title'];
    $head = quick_tpl($tpl_section['qadmin_head'], $cfg);
    $head_inner = quick_tpl($tpl_section['qadmin_head_inner'], $cfg);
    $foot_inner = quick_tpl($tpl_section['qadmin_foot_inner'], $cfg);
    $foot = quick_tpl($tpl_section['qadmin_foot'], $cfg);
    $ezf['ezf_head'] = quick_tpl($tpl_section['ezform_head'], $cfg);
    $ezf['ezf_foot'] = quick_tpl($tpl_section['ezform_foot'], $cfg);

    // additional header & footer
    if (!empty($cfg['header'])) {
        $head = $cfg['header'].$head;
    }
    if (!empty($cfg['footer'])) {
        $foot = $foot.$cfg['footer'];
    }

    if (!empty($cfg['ezf_mode'])) {
        return $ezf;
    } elseif (!empty($cfg['ezd_mode'])) {
        return $head.quick_tpl($cfg['ezd_template'], $ezd).$foot;
    } else {
        return $head.$head_inner.$tmp.$foot_inner.$foot;
    }
}


###############
###
### Form processor (add / edit item)
###
###############


function qadmin_process($def, $cfg)
{
    global $config, $db_prefix, $dbh, $lang;

    // AXSRF
    if (empty($cfg['admin_level'])) {
        die('<b>Fatal Error!</b> Admin level is not defined!');
    }
    if ($cfg['admin_level']) {
        AXSRF_check();
    }

    // init (sql = sql query, eml = emailed string, err = required error, fs_query = do fast search for xxx
    $sql = $eml = $err = $fs_query= array();
    $index_first = true;
    $permalink_changed = false;
    $permalink = '';
    $cfg['cmd'] = post_param('qadmin_cmd');
    $primary_val = post_param('primary_val');
    $savenew = post_param('qadmin_savenew');

    // save form
    save_form($cfg['table']);

    // captchai
    if (!empty($cfg['captcha'])) {
        $visual = qhash(post_param('visual'));
        if ($visual != qvc_value()) {
            admin_die($lang['msg']['captcha_error']);
        }
    }

    // field
    foreach ($def as $key=>$val) {
        unset($tt, $et);
        switch ($val['type']) {
            // short text
            case 'varchar':
            case 'email':
                $t = post_param($val['field']);

                if (!empty($val['unique'])) {
                    if ($cfg['cmd'] == 'new') {
                        $foo = sql_qquery("SELECT * FROM $cfg[table] WHERE ($val[field]='$t') LIMIT 1");
                    } else {
                        $foo = sql_qquery("SELECT * FROM $cfg[table] WHERE ($val[field]='$t') AND ($cfg[primary_key] != '$primary_val') LIMIT 1");
                    }
                    if (!empty($foo)) {
                        $err[] = $val['title'].' must be unique!';
                    }
                }
                if (isset($t)) {
                    $et = $tt = $t;
                }	// $tt = for sql queries, $et = for email report
            break;


            case 'permalink':
                if (empty($cfg['permalink_source']) || empty($cfg['permalink_script'])) {
                    admin_die($lang['msg']['permalink_error']);
                }
                if (empty($cfg['permalink_param'])) {
                    $cfg['permalink_param'] = '';
                }
                if (empty($cfg['permalink_folder'])) {
                    $cfg['permalink_folder'] = '';
                }
                $t = post_param($val['field']);
                if (empty($t)) {
                    $t = post_param($cfg['permalink_source']);
                    $auto = true;
                } else {
                    $auto = false;
                }

                $permalink_cfg = array();	// so we can reuse it later
                $permalink_cfg['source'] = $cfg['permalink_source'];
                $permalink_cfg['script'] = $cfg['permalink_script'];
                $permalink_cfg['param'] = $cfg['permalink_param'];
                $permalink_cfg['folder'] = $cfg['permalink_folder'];
                $permalink_cfg['title'] = $t;
                $permalink_cfg['auto'] = $auto;
                $permalink = generate_permalink($permalink_cfg['title'], $permalink_cfg['script'], $primary_val, $permalink_cfg['param'], $permalink_cfg['folder'], $permalink_cfg['auto']);
                if (!$permalink) {
                    $err[] = $val['title'].' must be unique!';
                }
                if (isset($t)) {
                    $et = $tt = $permalink;
                }	// $tt = for sql queries, $et = for email report

                // permalink db must be updated/inserted to permalink db after qadmin finish (see below)
            break;

            case 'text':
            case 'textarea':
            case 'hidden':
            case 'static':
            case 'disabled':
            case 'checkbox':
                $t = post_param($val['field']);
                if (isset($t)) {
                    $et = $tt = $t;
                }	// $tt = for sql queries, $et = for email report
            break;

            case 'select':
            case 'radio':
            case 'radioh':
            case 'radiov':
                $val['edit_opt'] = '';
                if (!empty($val['editopt'])) {
                    $val['option'] = get_editable_option($val['editopt']);
                }

                $t = post_param($val['field']);
                if (isset($t)) {
                    $tt = $t;	// $tt = for sql queries, $et = for email report
                    @$et = $val['option'][$t];
                    if (empty($t)) {
                        $et = '(None selected)';
                    }
                }
            break;

            // multi = stored as: <opt>\r\n<opt>\r\n<opt>
            // multi, = stored as: ,opt,opt,opt,	(make searching easier with LIKE %,opt,%)
            // multicsv = stored as: opt,opt,opt
            case 'multi':
            case 'multi,':
            case 'multicsv':
                $val['edit_opt'] = '';
                if (!empty($val['editopt'])) {
                    $val['option'] = get_editable_option($val['editopt']);
                }
                $t = checkbox_param($val['field'], 'post', true);

                if ($val['type'] == 'multi') {
                    $tt = implode("\r\n", $t);
                } elseif ($val['type'] == 'multi,') {
                    $tt = ','.implode(',', $t).',';
                } else {
                    $tt = implode(',', $t);
                }

                // email values
                $foo = array();
                // foreach ($t as $k => $v) $foo[] = $val['option'][$v];
                $et = implode("<br />", $foo);
            break;

            // password
            case 'password':
                $t = post_param($val['field']);
                if (!empty($t)) {
                    $tt = qhash($t);
                }
                $et = $t;
            break;

            // date
            case 'date':
                $t = date_param($val['field'], 'post', true);
                if (!empty($t)) {
                    $tt = $et = $t;
                }
            break;

            // time
            case 'time':
                $t = time_param($val['field'], 'post', true);
                if (!empty($t)) {
                    $tt = $et = $t;
                }
            break;

            // ode
            case 'code':
                $t = post_param($val['field'], '', 'rte');
                if (isset($t)) {
                    $tt = $et = $t;
                }
            break;

            // rte
            case 'wysiwyg':
                $t = post_param($val['field'], '', 'rte');
                if (isset($t)) {
                    $tt = $et = $t;
                }
            break;

            // file
            case 'file':
            case 'img':
            case 'image':
                if (!empty($_FILES[$val['field']]['name']) && (!$config['demo_mode'])) {
                    $fm = $_FILES[$val['field']]['tmp_name'];
                    $fn = $_FILES[$val['field']]['name'];
                    if (!empty($val['rename'])) {
                        $rnd = true;
                    } else {
                        $rnd = false;
                    }
                    if ($val['type'] == 'file') {
                        $fn = create_filename($cfg['file_folder'], $fn, $rnd);
                        $fl = $cfg['file_folder'].'/'.$fn;
                    } else {
                        $fn = create_filename($cfg['img_folder'], $fn, $rnd);
                        $fl = $cfg['img_folder'].'/'.$fn;
                    }
                    $x = upload_file($val['field'], $fl);
                    if (!$x['success']) {
                        admin_die($lang['msg']['can_not_upload']);
                    }

                    // get new name
                    $fn = $x[0]['filename'];
                    $fl = $cfg['img_folder'].'/'.$fn;
                    $ft = $cfg['thumb_folder'].'/'.$fn;
                    @chmod($fl, 0644);

                    $tt = $fn;
                    if ($val['type'] == 'file') {
                        $et = "<a href=\"$config[site_url]/$fl\">$config[site_url]/$fl</a>";
                    } else {
                        if (!empty($config['watermark_file'])) {
                            image_watermark($fl, './../public/image/'.$config['watermark_file']);
                        }
                        $et = "<img src=\"$config[site_url]/$fl\"><br /><a href=\"$config[site_url]/$fl\">$config[site_url]/$fl</a>";
                    }
                }
            break;

            // thumb
            case 'thumb':
                if (!empty($_FILES[$val['field']]['name']) && !$config['demo_mode']) {
                    $fn = $_FILES[$val['field']]['name'];
                    $fm = $_FILES[$val['field']]['tmp_name'];
                    if (!empty($val['rename'])) {
                        $fn = get_filename($cfg['table'], $val['field'], $fn);
                    }

                    $fl = $cfg['img_folder'].'/'.$fn;
                    $ft = $cfg['thumb_folder'].'/'.$fn;
                    $x = upload_file($val['field'], $fl);
                    if (!$x['success']) {
                        admin_die($lang['msg']['can_not_upload']);
                    }

                    // get new name
                    $fn = $x[0]['filename'];
                    $fl = $cfg['img_folder'].'/'.$fn;
                    $ft = $cfg['thumb_folder'].'/'.$fn;
                    @chmod($fl, 0644);

                    if (!empty($config['watermark_file'])) {
                        image_watermark($fl, './../public/image/'.$config['watermark_file']);
                    }

                    // create thumb
                    $size = empty($val['size']) ? 'thumb' : $val['size'];
                    image_optimizer($fl, $ft, $config['thumb_quality'], $size);

                    $tt = $fn;
                    $et = "<a href=\"$config[site_url]/$fl\"><img src=\"$config[site_url]/$ft\" alt=\"image\"></a><br />"
                         ."<a href=\"$config[site_url]/$fl\">$config[site_url]/$fl</a>";
                }
            break;

            // image resizer
            case 'img_resize':
                if (!empty($_FILES[$val['field']]['name'])) {
                    $fn = $_FILES[$val['field']]['name'];
                    $fm = $_FILES[$val['field']]['tmp_name'];
                    if (!empty($val['rename'])) {
                        $fn = get_filename($cfg['table'], $val['field'], $fn);
                    }
                    if (empty($val['size'])) {
                        $val['size'] = $config['thumb_size'];
                    }
                    if (!empty($config['watermark_file'])) {
                        image_watermark($fl, './../public/image/'.$config['watermark_file']);
                    }

                    // create thumb
                    $fl = $cfg['img_folder'].'/'.$fn;
                    $img_size = GetImageSize($fm);
                    image_optimizer($fm, $fl, $config['thumb_quality'], $val['size']);

                    $tt = $fn;
                    $et = "<img src=\"$config[site_url]/$fl\"><br /><a href=\"$config[site_url]/$fl\">$config[site_url]/$fl</a>";
                }
            break;

            // image series
            case 'img_set':
            case 'img_series':
                if (!empty($_FILES[$val['field']]['tmp_name']) && !$config['demo_mode']) {
                    if ($cfg['cmd'] != 'update') {
                        $nid = sql_qquery("SHOW TABLE STATUS LIKE '$cfg[table]'");
                        $next = $nid['Auto_increment'];
                        $primary_val = $next;
                    }

                    $fm = $_FILES[$val['field']]['tmp_name'];

                    // search lastest index file for image
                    $ok = false;
                    $i = 0;
                    while (!$ok) {
                        $i++;
                        $fn = $val['prefix'].'_'.$primary_val.'_'.$i;
                        if (!file_exists("$cfg[img_folder]/$fn.jpg")) {
                            $ok = true;
                        }
                    }
                    $fl = "$cfg[img_folder]/$fn.jpg";

                    if (!empty($val['resize'])) {
                        image_optimizer($fm, $fl, $config['thumb_quality'], $val['resize']);
                    } else {
                        $x = upload_file($val['field'], $fl);
                        if (!$x['success']) {
                            admin_die($lang['msg']['can_not_upload']);
                        }
                    }
                    @chmod($fl, 0644);

                    if (!empty($config['watermark_file'])) {
                        image_watermark($fl, './../public/image/'.$config['watermark_file']);
                    }

                    // thumb
                    $ft = $cfg['thumb_folder'].'/'.$fn;
                    $size = empty($val['thumb_size']) ? 'thumb' : $val['thumb_size'];
                    image_optimizer($fl, $ft.'.jpg', $config['thumb_quality'], $size);

                    $tt = $fn;
                    $et = "<img src=\"$config[site_url]/$fl\"><br /><a href=\"$config[site_url]/$fl\">$config[site_url]/$fl</a>";
                }
            break;
        }

        // required?
        if (!empty($val['required']) && empty($tt)) {
            $err[] = $val['title'].' is required!';
        }

        // sql query (only do !empty field)
        if (isset($tt)) {
            $sql[] = "$val[field] = '$tt'";
        }
        if (isset($et)) {
            $eml[] = "<tr><td valign=\"top\" class=\"form_title\"><b>$val[title]</b></td><td class=\"form_value\">$et</td></tr>";
        }

        // fast search query
        if (!empty($cfg['fastsearch']) && !empty($val['index'])) {
            $fs_query[] = $tt;
        }
    }

    // any 'required' error?
    if (!empty($err)) {
        $err = implode('-_', $err);
        admin_die(sprintf($lang['msg']['qadmin_required_err'], $err));
    }

    reset_form();

    // do sql
    $sql = implode(', ', $sql);
    if ($cfg['cmd'] == 'update') {
        // create log - get previous values
        $old = sql_qquery("SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$primary_val' LIMIT 1");
        $old_values = $old;

        // update db
        if (!empty($sql)) {
            sql_query("UPDATE $cfg[table] SET $sql WHERE $cfg[primary_key]='$primary_val' LIMIT 1");
        }
        $id = $primary_val;

        // create log - get new values
        $new = sql_qquery("SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$primary_val' LIMIT 1");
        $new_values = $new;
        if (($old != $new) && !empty($cfg['enable_log'])) {
            if (empty($cfg['detailed_log'])) {
                $foo = $old[$cfg['log_title']];
                $old = $new = '';
            }
            qadmin_log($primary_val, $old[$cfg['log_title']], LOG_EDIT, $old, $new, $cfg['table']);
        }
    } elseif ($cfg['cmd'] == 'new') {
        if (!empty($sql)) {
            sql_query("INSERT INTO $cfg[table] SET $sql");
        }
        $id = mysqli_insert_id($dbh);

        // create log - get previous values
        $old = sql_qquery("SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$id' LIMIT 1");
        $old_values = $new_values = $old;
        $new = array();
        if (!empty($cfg['enable_log'])) {
            qadmin_log($id, $old[$cfg['log_title']], LOG_ADD, $old, $new, $cfg['table']);
        }

        // if $id = 0, it means the primary key is not auto-increment, is it... varchar?
        if (empty($id)) {
            $id = post_param($cfg['primary_key']);
        }

        // send email?
        if (!empty($cfg['send_to'])) {
            global $title, $tpl_section;
            if (empty($cfg['send_subject'])) {
                $cfg['send_subject'] = 'Form Result';
            }

            load_section('adm', 'qadmin_section.tpl');
            $eml = implode("\n", $eml);
            $snt['form_result'] = $eml;
            $snt['form_name'] = empty($title['new']) ? 'Form' : $title['new'];
            $snt['header'] = empty($cfg['header']) ? '' : $cfg['header'];
            $snt['footer'] = empty($cfg['footer']) ? '' : $cfg['footer'];
            $snt['site_url'] = $config['site_url'];
            $body = quick_tpl($tpl_section['qadmin_send_email'], $snt);
            email($cfg['send_to'], $cfg['send_subject'], $body, 1, 1);
        }
    }

    // permalink
    if ($permalink) {
        $permalink = generate_permalink($permalink_cfg['title'], $permalink_cfg['script'], $id, $permalink_cfg['param'], $permalink_cfg['folder'], $permalink_cfg['auto'], true);
    }

    // cache
    if (!empty($cfg['auto_recache']) || !empty($cfg['recache']) || !empty($cfg['rebuild_cache'])) {
        qcache_clear();
    }

    // hurray! done!
    if ($savenew) {
        $redir = cur_url(false)."qadmin_cmd=new";
    } else {
        $redir = cur_url(false)."id=$id";
    }

    if (empty($cfg['post_process'])) {
        if (!empty($cfg['send_to'])) {
            admin_die($lang['msg']['qadmin_email_ok'], $redir);
        } else {
            admin_die('admin_ok', $redir);
        }
    } else {
        if (function_exists($cfg['post_process'])) {
            call_user_func($cfg['post_process'], $cfg['cmd'], $id, $savenew, $old_values, $new_values);
        } else {
            redir($cfg['post_process']."&qadmin_cmd=$cfg[cmd]&qadmin_id=$id&qadmin_savenew=$savenew");
        }
    }
}


###############
###
### Search form
###
###############


function qadmin_search($def, $cfg)
{
    global $config, $tpl_section, $tpl_block;

    // init
    $tmp = '';
    $row = array();
    $file = false;
    $keyword = get_param('keyword');
    $search_by = get_param('search_by');
    $start = date_param('start_date');
    $end = date_param('end_date');
    $andor = get_param('andor');

    $filter_by = get_param('filter_by');

    // andor data def
    $andor_def['or'] = 'or';
    $andor_def['and'] = 'and';

    if (empty($cfg['action'])) {
        $t = parse_url(urldecode(cur_url()));
        $cfg['action'] = basename($t['path']);
    }

    if (strpos($cfg['action'], '?')) {
        $cfg['action'] .= '&amp;';
    } else {
        $cfg['action'] .= '?';
    }

    // add some hidden values
    $cfg['hidden_value'] = '';
    $foo = url_query_to_array($cfg['action']);
    foreach ($foo as $hkey => $hval) {
        $cfg['hidden_value'] .= "<input type=\"hidden\" name=\"$hkey\" value=\"$hval\" />\n";
    }

    // template
    if (empty($cfg['template'])) {
        $cfg['template'] = 'default';
    }

    if ($cfg['template'] == 'default') {
        load_section('adm', 'qadmin_section.tpl');
    } else {
        load_section('adm', $cfg['template']);
    }

    // search by
    $j = explode(',', $cfg['search_key']);
    $k = explode(',', $cfg['search_key_mask']);
    $t = array_pair($j, $k);
    $cfg['search_by'] = create_select_form('search_by', $t, $search_by);

    // filter by
    if (!empty($cfg['search_filterby'])) {
        $film = explode(',', $cfg['search_filtermask']);
        array_unshift($film, 'None');
        $val['filter_by'] = create_select_form('filter_by', $film, $filter_by);
        $cfg['filter_form'] = quick_tpl($tpl_section['qadmin_search_filter'], $val);
    } else {
        $cfg['filter_form'] = '';
    }

    // date
    if (!empty($cfg['search_start_date']) && !empty($cfg['search_end_date'])) {
        $start = date_param('start_date', 'get');
        if (empty($start)) {
            $start = 'today';
        }
        $val['start_date'] = date_form('start_date', date('Y'), 1, 1, $start);

        $end = date_param('end_date', 'get');
        if (empty($end)) {
            $end = 'today';
        }
        $val['end_date'] = date_form('end_date', date('Y'), 1, 1, $end);
        $val['andor'] = create_select_form('andor', $andor_def, $andor);

        $cfg['date_form'] = quick_tpl($tpl_section['qadmin_search_date_2'], $val);
    } elseif (!empty($cfg['search_start_date']) && empty($cfg['search_end_date'])) {
        $start = date_param('start_date', 'get');
        if (empty($start)) {
            $start = 'today';
        }
        $val['start_date'] = date_form('start_date', date('Y'), 1, 1, $start);
        $val['andor'] = create_select_form('andor', $andor_def, $andor);

        $cfg['date_form'] = quick_tpl($tpl_section['qadmin_search_date_1'], $val);
    } else {
        $cfg['date_form'] = '';
    }

    // other
    if ($cfg['cmd_list_enable']) {
        $cfg['switch_list'] = quick_tpl($tpl_section['qadmin_switch_list'], $cfg);
    } else {
        $cfg['switch_list'] = '';
    }

    // get result (if keyword !empty)
    if (!empty($keyword)) {
        $cfg['keyword'] = $keyword;
        $result = qadmin_search_result($def, $cfg);
    } else {
        $cfg['keyword'] = '';
        $result = quick_tpl($tpl_section['qadmin_search_result_none'], $cfg);
    }

    $head = quick_tpl($tpl_section['qadmin_search'], $cfg);
    if (!empty($cfg['header'])) {
        $head = $cfg['header'].$head;
    }
    return $head.$result;
}


###############
###
### Execute search form and display results
###
###############


function qadmin_search_result($def, $cfg, $list_mode = false)
{
    global $config, $lang, $tpl_section, $tpl_block, $txt;

    // init
    $keyword = get_param('keyword');
    $search_by = get_param('search_by');
    $start = date_param('start_date');
    $end = date_param('end_date');
    $andor = get_param('andor');
    $filter_by = get_param('filter_by');
    $orderby = get_param('orderby');
    $sortby = get_param('sortby');
    $p = get_param('p');
    $script = str_replace('&', '&amp;', $_SERVER['REQUEST_URI']);

    // remove + from search_by
    $d = explode('+', $search_by);
    $search_by = $d[0];

    // explode search_key to array
    $j = $jj = array();
    $i = 0;
    $a = explode(',', $cfg['search_key']);
    foreach ($a as $b => $c) {
        $d = explode('+', $c);
        if (!empty($d[1])) {
            $e = $d;
            array_shift($e);
            foreach ($e as $f => $g) {
                $jj[$i] = $g;
            }
            $j[$i] = $d[0];
        } else {
            $j[$i] = $c;
        }
        $i++;
    }
    $cfg['search_key'] = implode(',', $j);
    if (!empty($jj)) {
        $cfg['search_key'] = $cfg['search_key'].','.implode(',', $jj);
    }

    // result masking
    if (!empty($cfg['search_result_mask'])) {
        $k = explode(',', $cfg['search_result_mask']);
        $result_mask = array_pair($j, $k);
    }

    // result url masking
    if (!empty($cfg['search_result_url'])) {
        $k = explode(',', $cfg['search_result_url']);
        $result_url = array_pair($j, $k);
    }

    // Prepare layout
    if (empty($cfg['action'])) {
        $t = parse_url(urldecode(cur_url()));
        $cfg['action'] = basename($t['path']);
    }

    // create title
    $cfg['block_title'] = $cfg['block_result'] = '';
    $k = explode(',', $cfg['search_key_mask']);
    $cfg['colspan'] = count($k) + 1;
    $foo = 0;

    foreach ($k as $val) {
        $fiel = $j[$foo];
        $sortscript = clean_get_query(array('sortby', 'orderby'));			// remove sortby & orderby queries
        $t['align'] = 'left';
        $t['title'] = $val;
        $t['sort_asc'] = $sortscript."&amp;sortby=$fiel&amp;orderby=a";			// sortby [asc]
        $t['sort_desc'] = $sortscript."&amp;sortby=$fiel&amp;orderby=d";			// sortby [desc]

        // cell title formatting
        if (!empty($def[$fiel]['format'])) {
            $format = $def[$fiel]['format'];
        } else {
            $format = 'default';
        }
        if (substr($format, 0, 7) == 'numeric') {
            $t['align'] = 'right';
        } elseif ($format == 'currency') {
            $t['align'] = 'right';
        }
        $cfg['block_title'] .= quick_tpl($tpl_section['qadmin_search_title_row'], $t);
        $foo++;
    }

    // 'edit' label
    if ($cfg['cmd_update_enable']) {
        $cfg['block_title'] .= quick_tpl($tpl_section['qadmin_search_edit_title'], $t);
    }

    // build search query
    $cfg['keyword'] = $keyword;
    $sql_where = '';
    $key = strtok($keyword, " ");
    while ($key) {
        $sql_where .= "$search_by LIKE '%".$key."%' AND ";
        $key = strtok(" ");
    }
    $sql_where = '('.substr($sql_where, 0, -5).')';

    // date param
    if ($andor == 'and') {
        $andor = 'AND';
    } else {
        $andor = 'OR';
    }
    if (!empty($start) && empty($end)) {
        $sql_where .= " $andor ($cfg[search_date_field] >= '$start')";
    }
    if (!empty($start) && !empty($end)) {
        $sql_where .= " $andor ($cfg[search_date_field] >= '$start' AND $cfg[search_date_field] <= '$end')";
    }

    // apply filter
    $filq = '';
    if ($filter_by) {
        $filb = explode(',', $cfg['search_filterby']);
        array_unshift($filb, 'Dummy');
        $filq = str_replace('|', ',', $filb[$filter_by]);
        $sql_where = "($sql_where) AND ($filq)";
    }

    // search!
    if ($orderby == 'd') {
        $orderby = 'DESC';
    } else {
        $orderby = 'ASC';
    }
    if (empty($sortby)) {
        $sss = '';
    } else {
        $sss = "$sortby $orderby";
    }				// sql sort method

    // if list_mode, simply replace sql_where with empty string (damn, I'm good!)
    if ($list_mode) {
        $result = sql_multipage($cfg['table'], $cfg['search_key'], $filq, $sss, $p);
    } else {
        $result = sql_multipage($cfg['table'], $cfg['search_key'], $sql_where, $sss, $p);
    }

    // create result
    foreach ($result as $val) {
        $tmp = '';

        foreach ($val as $key => $value) {
            if (in_array($key, $j) && !empty($key)) {
                // get field type
                foreach ($def as $t) {
                    if ($t['field'] == $key) {
                        $type = $t['type'];
                        if (!empty($t['format'])) {
                            $format = $t['format'];
                        } else {
                            $format = 'default';
                        }
                    }
                }

                // filter output
                $t['align'] = 'left';
                $t['result'] = $value;

                if (empty($type)) {
                    $type = '';
                }
                if (($type == 'wysiwyg') || ($type == 'text') || ($type == 'code')) {
                    $t['result'] = line_wrap(strip_tags($t['result']), 200);
                }
                if ($type == 'date') {
                    $format = 'date';
                }

                // cell content formatting
                if (substr($format, 0, 7) == 'numeric') {
                    $digit = substr($format, 8);
                    $t['result'] = num_format($t['result'], $digit);
                    $t['align'] = 'right';
                }
                if ($format == 'date') {
                    $t['result'] = convert_date($t['result']);
                }
                if ($format == 'currency') {
                    $t['result'] = num_format($t['result'], 0, 1);
                    $t['align'] = 'right';
                }


                // apply mask
                if (!empty($result_mask[$key])) {
                    $qw = $result_mask[$key];
                    global $$qw;
                    if (isset(${$qw}[$t['result']])) {
                        $t['result'] = ${$qw}[$t['result']];
                    } else {
                        $t['result'] = '';
                    }
                }

                // apply url mask
                if (!empty($result_url[$key])) {
                    $t['result'] = sprintf($lang['l_open_url'], str_replace('__KEY__', $t['result'], $result_url[$key]), $t['result']);
                }

                // output
                if (substr($t['result'], 0, 6) == 'guest*') {
                    $t['result'] = '(Guest)';
                }

                // see if current result row has sub_result
                $j_idx = array_search($key, $j);
                if (!empty($jj[$j_idx])) {
                    $t['result'] = '<b>'.$t['result'].'</b><br />'.$val[$jj[$j_idx]];
                }

                $tmp .= quick_tpl($tpl_section['qadmin_search_result_row'], $t);
            }
        }

        $t['primary_val'] = $val[$cfg['primary_key']];
        $t['action'] = $cfg['action'];
        $t['edit_target'] = '_self';

        if ($cfg['cmd_update_enable']) {
            if (empty($cfg['search_edit'])) {
                $t['edit_url'] = "$cfg[action]id=$t[primary_val]";
            } else {
                $t['edit_url'] = str_replace('__KEY__', $t['primary_val'], $cfg['search_edit']);
                if (!empty($cfg['search_edit_target'])) {
                    $t['edit_target'] = $cfg['search_edit_target'];
                }
            }
            $tmp .= quick_tpl($tpl_section['qadmin_search_edit_result'], $t);
        }
        $cfg['block_result'] .= '<tr>'.$tmp.'</tr>';
    }

    // pagination
    $cfg['pagination'] = $txt['pagination'];

    // new item
    $cfg['new_item_form'] = '';
    $cfg['add_button_label'] = empty($cfg['add_button_label']) ? 'Add New Entry' : $cfg['add_button_label'];

    if (!empty($cfg['cmd_new_enable'])) {
        $cfg['new_item_form'] = quick_tpl($tpl_section['qadmin_new_item'], $cfg);
    }

    $result = quick_tpl($tpl_section['qadmin_search_result'], $cfg);

    // additional header & footer
    if (!empty($cfg['footer'])) {
        $result = $result.$cfg['footer'];
    }

    return $result;
}


###############
###
### Display (list) all item in a table
###
###############


// instead of creating a whole new list function, we simply hack into search_result.
function qadmin_list($def, $cfg)
{
    global $config, $tpl_section, $lang;

    $filter_by = get_param('filter_by');

    // action
    if (empty($cfg['action'])) {
        $t = parse_url(urldecode(cur_url()));
        $cfg['action'] = basename($t['path']);
    }

    if (strpos($cfg['action'], '?')) {
        $cfg['action'] .= '&';
    } else {
        $cfg['action'] .= '?';
    }

    // add some hidden values
    $cfg['hidden_value'] = '';
    $foo = url_query_to_array($cfg['action']);
    foreach ($foo as $hkey => $hval) {
        $cfg['hidden_value'] .= "<input type=\"hidden\" name=\"$hkey\" value=\"$hval\" />\n";
    }

    // template
    if (empty($cfg['template'])) {
        $cfg['template'] = 'default';
    }
    if ($cfg['template'] == 'default') {
        load_section('adm', 'qadmin_ezf_section.tpl');
        load_section('adm', 'qadmin_section.tpl');
    } else {
        load_section('adm', $cfg['template'].'_ezf_section.tpl');
        load_section('adm', $cfg['template'].'_section.tpl');
    }

    // filter by
    if (!empty($cfg['search_filterby'])) {
        $film = explode(',', $cfg['search_filtermask']);
        array_unshift($film, 'None');
        $val['filter_by'] = create_select_form('filter_by', $film, $filter_by);
        $cfg['filter_form'] = quick_tpl($tpl_section['qadmin_search_filter'], $val);
    } else {
        $cfg['filter_form'] = '';
    }

    // date (if seach_by_date is defined, then show date)
    if (!empty($cfg['search_date_field'])) {
        $cfg['search_key'] .= ','.$cfg['search_date_field'];
        $cfg['search_key_mask'] .= ','.$lang['l_date'];
    }

    // other
    if ($cfg['cmd_search_enable']) {
        $cfg['switch_search'] = quick_tpl($tpl_section['qadmin_switch_search'], $cfg);
    } else {
        $cfg['switch_search'] = '';
    }

    $head = quick_tpl($tpl_section['qadmin_list'], $cfg);
    $result = qadmin_search_result($def, $cfg, true);

    if (!empty($cfg['header'])) {
        $head = $cfg['header'].$head;
    }

    return $head.$result;
}


###############
###
### Remove an uploaded file from an item, and update as necessary.
###
###############


// remove uploaded files
function qadmin_remove_file($def, $cfg)
{
    global $config;
    if ($config['demo_mode']) {
        admin_die('admin_ok');
    }

    $field = get_param('field');
    $primary_val = get_param('primary_val');
    $idx = get_param('idx');

    // get field type
    foreach ($def as $t) {
        if ($t['field'] == $field) {
            $type = $t['type'];
            $prefix = (!empty($t['prefix'])) ? $t['prefix'] : '';
        }
    }

    // get filename
    $res = sql_query("SELECT $field FROM $cfg[table] WHERE $cfg[primary_key] = '$primary_val' LIMIT 1");
    $row = sql_fetch_array($res);

    if (!empty($row[$field]) || ($type == 'img_series') || ($type == 'img_set')) {
        $fn = $row[$field];

        // remove file
        $fn = $row[$field];
        if ($type == 'file') {
            @unlink($cfg['file_folder'].'/'.$fn);
        }
        if (($type == 'img') || ($type == 'image') || ($type == 'img_resize') || ($type == 'image_resize')) {
            @unlink($cfg['img_folder'].'/'.$fn);
        }
        if ($type == 'thumb') {
            @unlink($cfg['img_folder'].'/'.$fn);
            @unlink($cfg['thumb_folder'].'/'.$fn);
        }
        if (($type == 'img_series') || ($type == 'img_set')) {
            $x = get_param('idx');
            $ok = false;
            $i = $x;
            $fn = $prefix.'_'.$primary_val.'_'.$i;
            unlink("$cfg[img_folder]/$fn.jpg");
            unlink("$cfg[thumb_folder]/$fn.jpg");

            while (!$ok) {
                $i++;
                $fn_old = $prefix.'_'.$primary_val.'_'.$i;
                $fn_new = $prefix.'_'.$primary_val.'_'.($i - 1);

                if (file_exists("$cfg[img_folder]/$fn_old.jpg")) {
                    rename("$cfg[img_folder]/$fn_old.jpg", "$cfg[img_folder]/$fn_new.jpg");
                    rename("$cfg[thumb_folder]/$fn_old.jpg", "$cfg[thumb_folder]/$fn_new.jpg");
                } else {
                    $ok = true;
                }
            }
        }

        // update db
        $old = sql_qquery("SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$primary_val' LIMIT 1");
        sql_query("UPDATE $cfg[table] SET $field='' WHERE $cfg[primary_key] = '$primary_val' LIMIT 1");
        if (!empty($cfg['enable_log'])) {
            // create log - get new values
            $new = sql_qquery("SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$primary_val' LIMIT 1");
            if ($old != $new) {
                if (empty($cfg['detailed_log'])) {
                    $foo = $old[$cfg['log_title']];
                    $old = $new = '';
                    $old[$cfg['log_title']] = $foo;
                }
                qadmin_log($primary_val, $old[$cfg['log_title']], LOG_DEL_FILE, $old, $new, $cfg['table']);
            }
        }
    }

    admin_die('admin_ok');
}


###############
###
### Remove an item, along with its uploaded files
###
###############


// remove item from db
function qadmin_remove_item($def, $cfg)
{
    global $config, $db_prefix;

    // do
    $primary_val = get_param('primary_val');
    $idx = get_param('idx');

    // remove_item can also remove multiple items, simple use: $_GET['primary_val'] = '1,2,3,4,5,6...';
    $pv_arr = explode(',', $primary_val);
    if (empty($pv_arr)) {
        $pv_arr = array($primary_val);
    }

    // get data
    foreach ($pv_arr as $primary_val) {
        $res = sql_query("SELECT * FROM $cfg[table] WHERE $cfg[primary_key] = '$primary_val' LIMIT 1");
        $old_values = $row = sql_fetch_array($res);

        // remove files
        foreach ($def as $key=>$val) {
            @$fn = $row[$val['field']];
            if ($val['type'] == 'file') {
                @unlink($cfg['file_folder'].'/'.$fn);
            }
            if (($val['type'] == 'img') || ($val['type'] == 'image') || ($val['type'] == 'img_resize') || ($val['type'] == 'image_resize')) {
                @unlink($cfg['img_folder'].'/'.$fn);
            }
            if ($val['type'] == 'thumb') {
                @unlink($cfg['img_folder'].'/'.$fn);
                @unlink($cfg['thumb_folder'].'/'.$fn);
            }
            if (($val['type'] == 'img_series') || ($val['type'] == 'img_set')) {
                $ok = false;
                $i = 0;
                while (!$ok) {
                    $i++;
                    $fn = $val['prefix'].'_'.$primary_val.'_'.$i;
                    if (!file_exists("$cfg[img_folder]/$fn.jpg")) {
                        $ok = true;
                    } else {
                        unlink("$cfg[img_folder]/$fn.jpg");
                        unlink("$cfg[thumb_folder]/$fn.jpg");
                    }
                }
            }
        }

        // remove from table...
        $old = sql_qquery("SELECT * FROM $cfg[table] WHERE $cfg[primary_key]='$primary_val' LIMIT 1");

        if (!empty($cfg['enable_log'])) {
            $log_info = sql_qquery("SELECT $cfg[log_title] FROM $cfg[table] WHERE $cfg[primary_key] = '$primary_val' LIMIT 1");
        }
        sql_query("DELETE FROM $cfg[table] WHERE $cfg[primary_key] = '$primary_val' LIMIT 1");
        if (!empty($cfg['enable_log'])) {
            $new = array();
            qadmin_log($primary_val, $log_info[0], LOG_DEL, $old, $new, $cfg['table']);
        }

        // remove permalink
        if (!empty($cfg['permalink_script'])) {
            sql_query("DELETE FROM ".$db_prefix."permalink WHERE target_script='$cfg[permalink_script]' AND target_idx='$primary_val' LIMIT 1");
        }
    }

    // cache
    if (!empty($cfg['auto_recache']) || !empty($cfg['recache']) || !empty($cfg['rebuild_cache'])) {
        qcache_clear();
    }

    // hurray! done!
    $strip = array('qadmin_cmd', 'id', 'primary_val');
    $url = str_replace('&amp;', '&', urldecode(clean_get_query($strip)));

    if (empty($cfg['post_process'])) {
        admin_die('admin_ok', $url);
    } else {
        if (function_exists($cfg['post_process'])) {
            call_user_func($cfg['post_process'], $cfg['cmd'], $primary_val, false, $old_values, $old_values);
        } else {
            redir($cfg['post_process']."&qadmin_cmd=$cfg[cmd]&qadmin_id=$primary_val");
        }
    }
}


###############
###
### Frontend, control central, HQ, base, whatever (something like that) to give command to qadmin.
###
###############


// manage commands
// $tpl_mode = full or popup
function qadmin_manage($def, $cfg, $title = array(), $tpl_mode = 'full')
{
    global $config, $txt;

    // security
    if ($cfg['admin_level']) {
        admin_check($cfg['admin_level']);
    }

    $cmd = get_param('qadmin_cmd');
    $id = get_param('id');

    //  get cmd from POST
    if (empty($cmd)) {
        $cmd = post_param('qadmin_cmd');
    }

    // manage cmd
    if (empty($cmd)) {
        $cmd = $cfg['cmd_default'];
    }
    if (!empty($id)) {
        $cmd = 'update';
    }
    if (post_param('qadmin_process')) {
        $cmd = 'process';
    }

    // logging
    if (!isset($cfg['enable_log'])) {
        $cfg['enable_log'] = $config['enable_qadmin_log'];
    }
    if (!isset($cfg['detailed_log'])) {
        $cfg['detailed_log'] = $config['enable_detailed_log'];
    }
    if (empty($cfg['log_title'])) {
        $cfg['enable_log'] = $cfg['detailed_log'] = '';
    }

    // tpl mode
    if ($tpl_mode == 'popup') {
        $tpl_mode = 'adm_popup';
    } else {
        $tpl_mode = 'adm';
    }

    switch ($cmd) {
        case 'search':
            $cfg['title'] = empty($title['search']) ? 'Search' : $title['search'];
            if ($cfg['cmd_search_enable']) {
                $txt['main_body'] = qadmin_search($def, $cfg);
                flush_tpl($tpl_mode);
            } else {
                echo 'Search disabled!';
            }
        break;


        case 'list':
            $cfg['title'] = empty($title['list']) ? 'List' : $title['list'];
            if ($cfg['cmd_list_enable']) {
                $txt['main_body'] = qadmin_list($def, $cfg);
                flush_tpl($tpl_mode);
            } else {
                echo 'List disabled!';
            }
        break;


        case 'new':
            $cfg['title'] = empty($title['new']) ? 'New Item' : $title['new'];
            if ($cfg['cmd_new_enable']) {
                $cfg['cmd'] = 'new';					// cmd: new, update, search
                if (!empty($cfg['ezf_mode'])) {
                    return qadmin_compile($def, $cfg);
                } else {
                    $txt['main_body'] = qadmin_compile($def, $cfg);
                    flush_tpl($tpl_mode);
                }
            } else {
                echo 'New item not allowed.';
            }
        break;


        case 'update':
            $cfg['title'] = empty($title['update']) ? 'Update Item' : $title['update'];
            if ($cfg['cmd_update_enable']) {
                $cfg['cmd'] = 'update';
                $cfg['primary_val'] = get_param('id');

                if (!empty($cfg['ezf_mode'])) {
                    return qadmin_compile($def, $cfg);
                } else {
                    $txt['main_body'] = qadmin_compile($def, $cfg);
                    flush_tpl($tpl_mode);
                }
            } else {
                echo 'Item update not allowed.';
            }
        break;


        case 'process':
            qadmin_process($def, $cfg);
        break;


        case 'remove_item':
            if ($cfg['cmd_remove_enable']) {
                $cfg['cmd'] = 'remove_item';
                qadmin_remove_item($def, $cfg);
            } else {
                echo 'Item removal not allowed.';
            }
        break;


        case 'remove_file':
            $cfg['cmd'] = 'remove_file';
            qadmin_remove_file($def, $cfg);
        break;
    }
}


// to build data definition for qadmin ($qadmin_def)
// $table => table name to build
// $ezf => TRUE to build a ezf style definition
function qadmin_build($table, $ezf = false)
{
    global $dbh;
    $res = sql_query("SELECT * FROM $table");
    $fields = mysqli_num_fields($res);
    $rows = mysqli_num_rows($res);
    $i = 0;
    echo "Your '".$table."' table has ".$fields." fields and ".$rows." records <br />";
    echo "Copy & paste the following to create qadmin_def: <br /><hr />";

    $mysql_data_type_hash = array(1=>'tinyint',2=>'smallint',3=>'int',4=>'float',5=>'double',7=>'timestamp',8=>'bigint',9=>'mediumint',10=>'date',
    11=>'time',12=>'datetime',13=>'year',16=>'bit',252=>'text',253=>'varchar',254=>'char',246=>'decimal');
    while ($i < $fields) {
        $tbl = mysqli_fetch_field_direct($res, $i);
        $type  = $mysql_data_type_hash[$tbl->type];
        $name  = $tbl->name;
        $len   = $tbl->length;
        $flags = $tbl->flags;

        if ($ezf) {
            echo "\$qadmin_def[$i] = '".ucwords(strtolower(str_replace('_', ' ', $name))).",$name,";
        } else {
            echo "<p>// $name :: $type :: $len<br />";
            echo "\$qadmin_def['$name']['title'] = '".ucwords(strtolower(str_replace('_', ' ', $name)))."';<br />";
            echo "\$qadmin_def['$name']['field'] = '$name';<br />";
        }

        if (($type == 'string') || ($type == 'int')) {
            if ($ezf) {
                echo "varchar,$len";
            } else {
                echo "\$qadmin_def['$name']['type'] = 'varchar';<br />";
                echo "\$qadmin_def['$name']['size'] = $len;<br />";
            }
        } elseif (($type == 'blob') || ($type == 'text')) {
            if ($ezf) {
                echo "text,500*200";
            } else {
                echo "\$qadmin_def['$name']['type'] = 'text';<br />";
                echo "\$qadmin_def['$name']['size'] = '500,200';<br />";
            }
        } elseif ($type == 'date') {
            if ($ezf) {
                echo "date";
            } else {
                echo "\$qadmin_def['$name']['type'] = 'date';<br />";
            }
        } elseif ($type == 'time') {
            if ($ezf) {
                echo "time";
            } else {
                echo "\$qadmin_def['$name']['type'] = 'time';<br />";
            }
        } else {
            if ($ezf) {
                echo "varchar,$len";
            } else {
                echo "\$qadmin_def['$name']['type'] = 'varchar';<br />";
                echo "\$qadmin_def['$name']['size'] = $len;<br />";
            }
        }

        if ($ezf) {
            echo "';<br />";
        } else {
            echo "\$qadmin_def['$name']['value'] = 'sql';<br />";
            echo '</p>';
        }
        $i++;
    }
    echo '<hr />';
}


// just like qadmin_build, but doesn't need to already have the table
// $def is an array, using this format: 'title,field_id,type,size', eg: $foo[] = 'Name,uname,varchar,80';
// $as_array returns result in array that can be used with ezform_compile, kewl eh
function qadmin_qbuild($def, $as_array = false)
{
    $qadmin_def = array();

    foreach ($def as $val) {
        $foo = explode(',', $val);
        $name = $foo[0];
        $field = $foo[1];
        $type = $foo[2];
        $len = (!empty($foo[3])) ? $foo[3] : 0;
        $required = (!empty($foo[4])) ? true : false;
        $option = (!empty($foo[5])) ? $foo[5] : '';

        if ($as_array) {
            $qadmin_def[$field]['title'] = $name;
            $qadmin_def[$field]['field'] = $field;
            $qadmin_def[$field]['type'] = $type;
            $qadmin_def[$field]['size'] = str_replace('*', ',', $len);
            $qadmin_def[$field]['value'] = 'sql';
            $qadmin_def[$field]['required'] = $required;

            // option
            if (!empty($option)) {
                if ($option[0] == '*') {
                    $qadmin_def[$field]['editopt'] = substr($option, 1);
                } else {
                    global $$option;
                    $qadmin_def[$field]['option'] = $$option;
                }
            }
        } else {
            echo "<p>// $field :: $type :: $len<br />";
            echo "\$ezform_def['$field']['title'] = '$name';<br />";
            echo "\$ezform_def['$field']['field'] = '$field';<br />";
            echo "\$ezform_def['$field']['type'] = '$type';<br />";
            echo "\$ezform_def['$field']['size'] = '$len';<br />";
            echo "\$ezform_def['$field']['value'] = 'sql';<br />";
            echo "\$ezform_def['$field']['required'] = '$required';<br />";
            echo '</p>';
        }
    }

    if ($as_array) {
        return $qadmin_def;
    }
}
