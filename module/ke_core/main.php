<?php
$mode = get_param('mode');
if ($mode != 'list') {
    $mode = 'grid';
}

$m = get_param('mod_ini');
$mod_ini_str = safe_receive($m);
$txt['main_body'] = "<!-- BEGINMODULE ke_core -->
$mod_ini_str
display_overwrite = $mode
<!-- ENDMODULE -->";
flush_tpl('ajax');
