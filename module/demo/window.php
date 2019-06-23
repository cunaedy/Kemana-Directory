<?php
# window.php is called when a module is used inside another page (such as inside outline.tpl, or welcome.tpl) -not an independent or stand alone module-
# you can use this method to display, for example, lastest news, random image, etc.

// get module's inline config, which is contained in $mod_ini
if (!empty($mod_ini)) {
    $txt['mod_option'] = "<ul>\n";
    foreach ($mod_ini as $k => $v) {
        $txt['mod_option'] .= "<li>$k => $v</li>\n";
    }
    $txt['mod_option'] .= "</ul>\n";
} else {
    $txt['mod_option'] =  'None given';
}

// there's also a module_config db:
// print_r ($module_config);
// to access module_config for mod_id demo, use $module_config['demo']
$txt['mod_config'] = "<ul>\n";
foreach ($module_config['demo'] as $k => $v) {
    $txt['mod_config'] .= "<li>$k => $v</li>\n";
}
$txt['mod_config'] .= "</ul>\n";

// load mod's template
// if need to use BEGINIF $tp_mode => use BEGINIF $module_mode
$tpl = load_tpl('mod', 'module_demo_window.tpl');

// output must be contained in $output
$output = quick_tpl($tpl, $txt);

// specify a url to edit the content of this module (eg, page editor, menu editor, etc) -- optional
$mod_content_edit_url = '#';
