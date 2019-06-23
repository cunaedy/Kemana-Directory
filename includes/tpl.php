<?php
// qTPL - the simplest template engine yet
// part of qEngine
// copyright (c) C97.net, usage of this script outside C97 is strictly prohibited!
// please write to us for licensing: contact@c97.net


/* ------- ( GENERAL FUNCTIONS ) ------- */


function compile_tpl($args)
{
    global $tpl_block, $tpl_subblock, $tpl_section;
    $args[1] = strtolower($args[1]);

    switch ($args[1]) {
        case 'section':
         $tpl_section[$args[2]] = $args[3];
         //return TRUE;
        break;

        case 'subblock':
         $tpl_subblock[$args[2]] = $args[3];
         return '{$subblock_'.$args[2].'}';
        break;

        case 'block':
         $tpl_block[$args[2]] = $args[3];
         return '{$block_'.$args[2].'}';
        break;

        case 'if':
         if ($p = strpos($args[3], '<!-- ELSE -->')) {
             $args[4] = substr($args[3], $p+13);
             $args[3] = substr($args[3], 0, $p);
         }

         // useful for BEGINIF - ENDIF & BEGINIF - ELSE - ENDIF
         if (empty($args[4])) {
             $args[4] = '';
         }

         $vars = (explode(' ', $args[2]));
         $vars[0] = substr($vars[0], 1);

         global ${$vars[0]};

         if (empty(${$vars[0]})) {
             $cmd = 0;
         } else {
             $cmd = eval("if ($args[2]) return TRUE; else return FALSE;");
         }

         if ($cmd) {
             return $args[3];
         } else {
             return $args[4];
         }
        break;
    }
}


// see load_tpl & load_section
function get_skin_path($mode, $skinfile, $required = true)
{
    global $config, $txt;
    $notFound = false;
    $isMobileSkin = strpos($config['skin'], '_mobile') ? true : false;

    switch ($mode) {
        case 'adm':
        case 'admin':
        case 'mod_admin':
         $path = $config['abs_path'].'/skins/_admin/'.$skinfile;
         if (!file_exists($path)) {
             $notFound = true;
         }
        break;

        // mod skin: look for mod_skin in skins/[current_skin]/[filename.tpl]
        // if not found -> find in skins/_common/[filename.tpl], cool eh!
        case 'mod':
         $path = $config['abs_path'].'/'.$config['skin'].'/'.$skinfile;
         if (!file_exists($path)) {
             $def_path = $config['abs_path'].'/skins/_module/'.$skinfile;
             if (!file_exists($def_path)) {
                 $notFound = true;
             }
             $path = $def_path;
         }
        break;

        case 'email':
        case 'mail':
         $path = $config['abs_path'].'/skins/_mail/'.$skinfile;
         if (!file_exists($path)) {
             $notFound = true;
         }
        break;

        case 'etc':
        case 'force':
         $path = $config['abs_path'].'/'.$skinfile;
         if (!file_exists($path)) {
             $notFound = true;
         }
        break;

        // user skin: skins/[skin_name]/[filename.tpl]
        default:
         $path = $config['abs_path'].'/'.$config['skin'].'/'.$skinfile;
         if (!file_exists($path)) {
             $def_path = $config['abs_path'].'/skins/_common/'.$skinfile;
             if (!file_exists($def_path)) {
                 $notFound = true;
             }
             $path = $def_path;
         }
        break;
    }

    if ($notFound && !$isMobileSkin && $required) {
        just_die("<p><b>Template $path not found! Contact webmaster.</b></p>");
    } elseif ($notFound && $isMobileSkin && $required) {
        $txt['main_body'] = "<p>This feature is not supported for mobile browsers. Please use your desktop computer to access this page.</p>";
        flush_tpl();
        die;
    } elseif ($notFound && !$required) {
        return false;
    } else {
        return $path;
    }
}


// loding a template file into a varible.
// use quick_tpl to display template
// $mode =  'adm', 'admin' = admin skin
//          'mod' = module skin
//          'usr', 'user', blank (or ignore) = user skin
//			'etc', 'force' = load a skin from a specific path (relates to installation path)
//			'var' = load from a var (eg: tpl_section)
// 			you can skip $mode, and put skin_name instead, load_tpl ('some_file.tpl'), to load 'some_file.tpl' from UI skin
// $skinfile = file to load
// $required = true :: script will be halted if the skin not found, false :: only return 'false';
// $raw = true : to return raw template without pre-compiled, false : return compiled template
function load_tpl($mode, $skinfile = '', $required = true, $raw = false)
{
    $tpl = '';
    global $tpl_block, $tpl_subblock, $tpl_section, $lang, $txt, $config, $debug_info;

    // get skin path
    if (empty($skinfile)) {
        $skinfile = $mode;
    }
    if ($mode == 'var') {
        $tpl = $skinfile;
    } elseif ($mode == 'mail') {
        if (isset($lang['mail'][$skinfile])) {
            $config['company_logo'] = $txt['company_logo'];
            $t = quick_tpl($lang['mail']['outline'], $config);
            $tpl = str_replace('<<main_body>>', $lang['mail'][$skinfile], $t);
        } else {
            die("<p><b>Template $skinfile not found! Contact webmaster.</b></p>");
        }
    } else {
        // open skin
        $path = get_skin_path($mode, $skinfile, $required);
        if ($path === false) {
            $debug_info['tpl'][] = $skinfile.' -&gt; <font color="#ff0000">Load failed!</font>';
            return false;
        }
        $debug_info['tpl'][] = $path.' -&gt; Loaded';
        $fp = fopen($path, 'r');
        while (!feof($fp)) {
            $tpl .= fgets($fp, 4096);
        }
        fclose($fp);
    }

    // return raw?
    if ($raw) {
        return $tpl;
    }

    // compile first
    $escape[] = '{{{';
    $esc[] = ' __l__ ';
    $escape[] = '}}}';
    $esc[] = ' __r__ ';
    $tpl = str_replace($escape, $esc, $tpl);
    $tpl = preg_replace_callback("/<!-- BEGIN(IF) (.*?) -->(.*?)<!-- ENDIF -->/is", "compile_tpl", $tpl);
    $tpl = preg_replace_callback("/<!-- BEGIN(SUBBLOCK) (.*?) -->(.*?)<!-- ENDSUBBLOCK -->/is", "compile_tpl", $tpl);
    $tpl = preg_replace_callback("/<!-- BEGIN(BLOCK) (.*?) -->(.*?)<!-- ENDBLOCK -->/is", "compile_tpl", $tpl);
    $tpl = preg_replace_callback("/<!-- BEGIN(SECTION) (.*?) -->(.*?)<!-- ENDSECTION -->/is", "compile_tpl", $tpl);
    return $tpl;
}


// load sections from a template file
// section is simply a part of template. it is still in raw. it's like loading some small templates in one file.
// section is very useful if you want to use multiple classes/instances in looping.
// BEGINIF & BEGINBLOCK won't work in SECTION.
function load_section($mode, $skinfile = '')
{
    global $tpl_section;
    $tpl = '';

    // get skin path
    if (empty($skinfile)) {
        $skinfile = $mode;
    }
    $path = get_skin_path($mode, $skinfile);

    // open skin
    $fp = fopen($path, 'r');
    while (!feof($fp)) {
        $tpl .= fgets($fp, 4096);
    }
    fclose($fp);
    $tpl = preg_replace_callback("/<!-- BEGIN(SECTION) (.*?) -->(.*?)<!-- ENDSECTION -->/is", "compile_tpl", $tpl);
    return true;
}


// quick_template
// param: $tpl = variable containing template structure
//        $txt = array to be displayed in the template.
//
function quick_tpl($tpl, $text)
{
    global $lang, $link, $config;
    if (!is_array($text)) {
        $text = array();
    }
    $text['site_url'] = $config['site_url'];

    $search = preg_match_all("/{\\$(l_[a-zA-Z0-9\-_]+)}/", $tpl, $matches);
    foreach ($matches[1] as $val) {
        $tpl = str_replace('{$'.$val.'}', $lang[$val], $tpl);
    }

    $search = preg_match_all("/{\\$([a-zA-Z0-9\-_]+)}/", $tpl, $matches);
    foreach ($matches[1] as $val) {
        if (!isset($text[$val])) {
            if ($config['debug_mode']) {
                echo '<b>Warning!</b> Template variable <b><i>{$'.$val.'}</i></b> is not defined! Assumed <b><i>\''.$val.'\'</i></b> string instead.';
                echo getCallingFunction(true);
            }
            $text[$val] = $val;
        }
        $tpl = str_replace('{$'.$val.'}', $text[$val], $tpl);
    }
    return $tpl;
}

/* ------- ( FLUSH FUNCTIONS ) ------- */

// to stop XSRF :: this basically add <input type="hidden" name="token" value="{$token}" /> to all <form>
function AXSRF_token($args)
{
    global $AXSRF_tokenID;
    $hidden = "<input type=\"hidden\" name=\"AXSRF_token\" value=\"$AXSRF_tokenID\" />";
    return "$args[0]\n$hidden\n";
}


function init_module($args)
{
    global $config, $module_config, $module_enabled, $lang, $txt, $tpl_section, $tpl_block, $tpl_subblock, $dbh;
    global $db_prefix, $module_mode, $mod_ini, $debug_info, $current_user_id, $isLogin, $current_admin_level, $isPermalink, $original_idx;
    if (!$config['enable_module_engine']) {
        return ('<!-- MODULE_ENGINE IS DISABLED -->');
    }

    // is it inline mod?
    $inline = false;
    $f = explode(':', $args[0]);
    if ($f[0] == '{qemod') {
        $inline = true;
        $args[1] = $f[1];
        $args[2] = substr($f[2], 0, -1);
    }
    $mod_id = strtolower($args[1]);
    $mod_ini = parse_ini_str($args[2]);
    $mod_raw = $args[2];

    // is it active? -- somehow simply empty ($module_enabled[$mod_id]['mod_enabled']) doesn't return correctly..... any idea?
    @$x = $module_enabled[$mod_id]['mod_enabled'];
    if (empty($x)) {
        $debug_info['mod'][] = $mod_id.' -&gt; <style="color:#f00">Not Enabled</style>';
        return "<!-- module $mod_id is disabled or not installed! -->";
    }

    // find module
    if (!@file_exists('./module/'.$mod_id.'/window.php')) {
        $debug_info['mod'][] = $mod_id.' -&gt; <style="color:#f00">Not Found</style>';
        return "<!-- module $mod_id is not available -->";
    }

    // load module
    $output = $mod_content_edit_url = '';
    include './module/'.$mod_id.'/window.php';
    $debug_info['mod'][] = $mod_id.' -&gt; Loaded';
    if ($current_admin_level) {
        if (!empty($mod_ini['mod_pos'])) {
            $pos_url = $config['site_url'].'/'.$config['admin_folder'].'/manage.php?highlight='.$mod_ini['mod_pos'];
        } else {
            $pos_url = "javascript:alert('This module was added manually without using module manager!')";
        }
        if (!empty($mod_content_edit_url)) {
            $content_url = $mod_content_edit_url;
        } else {
            $content_url = "javascript:alert('This module doesn\'t support such feature!')";
        }
        $output = sprintf($lang['edit_in_acp_module'], $content_url, $pos_url, $config['site_url'].'/'.$config['admin_folder'].'/modplug_doku.php?what=module&amp;mod_id='.$mod_id).$output;
    }
    return $output;
}


// to display/flush a template file
// this is different than quick_tpl, because it has default file & variables
// after flush -> always die
// $mode = user, popup, admin (adm), ajax
// $subtpl = name of sub template to load instead of body_default.tpl
//			 use '_blank' to load empty sub template.
// LZ ONLY = main_popup, adm_main_popup
function flush_tpl($mode = 'user', $subtpl = '')
{
    global $txt, $lang, $config, $time_start, $user_logged, $tpl_block, $tpl_subblock, $tpl_section, $AXSRF_tokenID, $db_prefix, $debug_info, $current_admin_level, $pleasePurchaseTheLicense;
    $print = get_param('print_version');
    $adm_popup = get_param('adm_popup');

    if ($print) {
        $mode = 'print';
    }
    if (empty($adm_popup)) {
        $adm_popup = post_param('adm_popup');
    }
    if ($adm_popup) {
        $mode = 'adm_popup';
    }

    // for stopwatch (if you are a dedicated user of qEngine 1), see ztopwatch module
    if (empty($txt['head_title'])) {
        if ($mode == 'adm') {
            generate_html_header('adm');
        } else {
            generate_html_header();
        }
    }

    // load tpl
    if (($mode == 'admin') || ($mode == 'adm')) {
        $path = load_tpl('adm', 'outline.tpl');
    } elseif ($mode == 'adm_popup') {
        $path = load_tpl('adm', 'popup.tpl');
    } elseif ($mode == 'adm_print') {
        $path = load_tpl('adm', 'admin_print.tpl');
    } elseif ($mode == 'popup') {
        $path = load_tpl('popup.tpl');
    } elseif ($mode == 'print') {
        $path = load_tpl('print_version.tpl');
    } elseif ($mode == 'ajax') {
        $path = '<!-- AJAX MODE -->{$main_body}';
    } else {
        // load sub tpl
        if (empty($subtpl) && ($mode != 'user')) {
            $subtpl = $mode;
        }
        if (empty($subtpl)) {
            $subtpl = 'body_default.tpl';
        }

        if ($subtpl != '_blank') {
            $subtpl = load_tpl('user', $subtpl, false);
        } else {
            $subtpl = '{$main_body}';
        }

        if (!$subtpl) {
            $subtpl = load_tpl('body_default.tpl');
        }

        $path = load_tpl('outline.tpl');
        $path = str_replace('{$main_content}', $subtpl, $path);
        $mode = 'user';
    }

    // add DEMO COMMENT for demo mode
    if ($config['demo_mode']) {
        $txt['main_body'] = '<div style="background:yellow; color:red; text-align:center; padding:3px; margin:3px">'.
        '<b>Demo Mode</b>, please don\'t purchase anything from this site.<br />Some features, including file upload, '.
        'are disabled for security reason.</div>'.$txt['main_body'];
        $txt['head_title'] .= ' [ DEMO MODE ]';
    }

    // show closed message instead?
    if ($config['close_site'] && $current_admin_level) {
        $txt['main_body'] = '<div style="background:yellow; color:red; text-align:center; padding:3px; margin:3px">'.
        'Your web site is closed. You can access this because you are logged in as administrator. To open your web site,
		<a href="'.$config['site_url'].'/'.$config['admin_folder'].'/qe_config.php">click here</a>.</div>'.$txt['main_body'];
    }

    // show acp shortcuts
    if ($current_admin_level) {
        $txt['acp_shortcuts'] = empty($tpl_section['acp_shortcuts']) ? '' : quick_tpl($tpl_section['acp_shortcuts'], $txt);
    } else {
        $txt['acp_shortcuts'] = '';
    }

    if ($mode == 'user') {
        // if module man enable -> handle module by qE
        if ($config['enable_module_man']) {
            // add modules
            $long = array();
            $short = array('T1', 'T2', 'L1', 'L2', 'R1', 'R2', 'B1', 'B2');	// list of positions
            foreach ($short as $val) {
                $long[] = 'module_box_'.$val;
            }
            $qc = qcache_get($long);

            // cached? (qE only stores modules design & position to cache, not the module output itself)
            $cache_avail = true;
            foreach ($qc as $k => $v) {
                if ($qc[$k] === false) {
                    $cache_avail = false;
                }
                $txt[$k] = $v;
            }
            if (!$cache_avail) {
                // init pos
                foreach ($short as $k => $v) {
                    if (empty($tpl_section['module_design_'.$v])) {
                        $tpl_section['module_design_'.$v] = (substr($v, 0, 1) == 'L') || (substr($v, 0, 1) == 'R') ? $tpl_section['module_design_LR'] : $tpl_section['module_design_TB'];
                    }
                    $txt['module_box_'.$v] = '';
                }

                // load modules
                $res = sql_query("SELECT * FROM ".$db_prefix."module_pos ORDER BY idx");
                while ($row = sql_fetch_array($res)) {
                    $row['mod_content'] = "<!-- BEGINMODULE $row[mod_id] -->mod_pos=$row[idx]\n$row[mod_config]<!-- ENDMODULE -->";
                    $txt['module_box_'.$row['mod_pos']] .= quick_tpl($tpl_section['module_design_'.$row['mod_pos']], $row);
                }

                foreach ($short as $k => $v) {
                    qcache_update('module_box_'.$v, $txt['module_box_'.$v]);
                }
            }
        } else {
            // if module man disabled -> simply create dummy output!
            // you can still add module manually by using <!-- BEGINMODULE -->
            $short = array('T1', 'T2', 'L1', 'L2', 'R1', 'R2', 'B1', 'B2');	// list of positions
            foreach ($short as $val) {
                $txt['module_box_'.$val] = '<!-- MODULE_MAN is disabled -->';
            }
        }
    } elseif (($mode == 'adm') || ($mode == 'admin')) {
        $txt['please_purchase_license_to_support_us'] = '';

        // Please purchase the license to support us. It's only a couple of bucks, but it means a lot to us. Visit http://www.c97.net to purchase your license.
        // After purchasing the license, you can remove both lines below. Thanks for your supports.
        $txt['please_purchase_license_to_support_us'] = base64_decode('WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgS2VtYW5hLiBQbGVhc2Ugc3VwcG9ydCB1cyBieSA8YSBocmVmPSJodHRwOi8vd3d3LmM5Ny5uZXQiPnB1cmNoYXNpbmcgdGhlIGxpY2Vuc2U8L2E+Lg==');
        $txt['head_title'] .= base64_decode('IC0gUGxlYXNlIFB1cmNoYXNlIExpY2Vuc2Uh=');
    }

    // stats
    $time_end = getmicrotime();
    $txt['stat_time_required'] = num_format($time_end - $config['time_start'], 5);
    $txt['stat_sql_required'] = num_format($config['total_mysql_query']);

    // create output
    $content = quick_tpl($path, $txt);

    // run modules
    // ... but inline module {qemod} doesn't run in ACP, so it doesn't interfere with page editor!
    if (($mode != 'admin') && ($mode != 'adm') && ($mode != 'adm_popup')) {
        $content = preg_replace_callback("/{qemod:(.*?)}/is", 'init_module', $content);
    }
    $content = preg_replace_callback("/<!-- BEGINMODULE (.*?) -->(.*?)<!-- ENDMODULE -->/is", "init_module", $content);

    // unique toket ID to fight XSRF --google it-- (i'm lazy! so instead of mod each form, i create this 'bot') == Auto AXSRF do NOT work in popups or print -- obviously
    $AXSRF_tokenID = axsrf_value();
    $foo = preg_replace_callback("/<form (.*?)>/is", "AXSRF_token", $content);
    if (!empty($foo)) {
        $content = $foo;
    }

    // put back escape
    $esc[] = ' __l__ ';
    $es[] = '{';
    $esc[] = ' __r__ ';
    $es[] = '}';
    $content = str_replace($esc, $es, $content);

    $enc = '';
    reset_form();
    if (empty($_SERVER["HTTP_ACCEPT_ENCODING"])) {
        $_SERVER["HTTP_ACCEPT_ENCODING"] = '';
    }
    if (is_integer(strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip'))) {
        $enc = 'x-gzip';
    }
    if (is_integer(strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip'))) {
        $enc = 'gzip';
    }

    // gzip header is disabled in ACP in case server doesn't allow GZip
    if ($enc && $config['enable_gzip'] && ($mode != 'adm') && ($mode != 'admin') && ($mode != 'adm_popup') && !headers_sent()) {
        $ori = strlen($content);
        $content = gzencode($content."<!-- GZIP enabled, originally $ori bytes-->");
        header("Content-Type: text/html; charset=".$lang['l_encoding']);
        header("Content-Encoding: ".$enc);
        echo $content;
    } else {
        if (!headers_sent()) {
            header("Content-Type: text/html; charset=".$lang['l_encoding']);
        }
        echo $content;
    }

    // add debug info
    if ($config['debug_mode']) {
        global $memory_when_start;

        $time_end = getmicrotime();
        if (function_exists('memory_get_usage')) {
            $mfinish = memory_get_usage();
        } else {
            $mfinish = 0;
        }
        if (function_exists('memory_get_peak_usage')) {
            $mpeak = memory_get_peak_usage();
        } else {
            $mpeak = 0;
        }
        $cache = $config['cache'] ? 'Refreshed every '.$config['cache'].' seconds' : 'disabled';

        echo "<hr />\n<h1>Debug Information</h1>\n<table border=\"1\" style=\"border-collapse:collapse\" width=\"100%\" cellpadding=\"3\">\n";
        echo "<tr><td colspan=\"2\"><b>Memory Usage Information</b></td></tr>\n";
        echo "<tr><td></td><td><b>Peak</b>: ".num_format($mpeak)." bytes</td></tr>\n";
        echo "<tr><td></td><td><b>On Init</b>: ".num_format($memory_when_start)." bytes</td></tr>\n";
        echo "<tr><td></td><td><b>On Finish</b>: ".num_format($mfinish)." bytes</td></tr>\n";
        echo "<tr><td colspan=\"2\"><b>Performance Information</b></td></tr>\n";
        echo "<tr><td></td><td><b>Cache</b>: ".$cache."</td></tr>\n";
        echo "<tr><td></td><td><b>Start</b>: ".date('d M Y, H:i:s', $config['time_start'])."</td></tr>\n";
        echo "<tr><td></td><td><b>Finish</b>: ".date('d M Y, H:i:s', $time_end)."</td></tr>\n";
        echo "<tr><td></td><td><b>Time Needed</b>: ".num_format($time_end - $config['time_start'], 5)." seconds</td></tr>\n";
        echo "<tr><td></td><td><b>SQL Queries</b>: ".num_format($config['total_mysql_query'])." queries</td></tr>\n";
        echo "<tr><td colspan=\"2\"><b>SQL Information</b></td></tr>\n";
        foreach ($debug_info['sql'] as $k => $v) {
            echo "<tr><td width=\"5%\" valign=\"top\">$k.</td><td width=\"95%\">$v</td></tr>\n";
        }

        echo "<tr><td colspan=\"2\"><b>Module Information</b></td></tr>\n";
        foreach ($debug_info['mod'] as $k => $v) {
            echo "<tr><td width=\"5%\">$k.</td><td width=\"95%\">$v</td></tr>\n";
        }

        echo "<tr><td colspan=\"2\"><b>Template Information</b></td></tr>\n";
        foreach ($debug_info['tpl'] as $k => $v) {
            echo "<tr><td width=\"5%\">$k.</td><td width=\"95%\">$v</td></tr>\n";
        }

        echo "</table>";
    }
    die;
}
