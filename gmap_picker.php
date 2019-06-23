<?php
// part of qEngine
require './includes/user_init.php';

// Show google map location picker popup
// $cmd = for now  only 'picker'
// $mode = 	'latlon1' to returns 'lat,lon' as a single value
//			'latlon2' to returns 'lat' & 'lon' as seperate values
// $fid = id of input form field the picker will write to, for $mode = 'latlon1' -> name of the id; for $mode = 'latlon2' -> name of the id, the script will write to '[id]_lat' & '[id]_lon' fields.
// $lat & $lon = default lat & lon
$cmd = get_param('cmd');
$mode = get_param('mode', 'latlon1');
$fid = get_param('fid', 'latlon');
$latlon = get_param('latlon');
$lat = get_param('lat');
$lon = get_param('lon');

// lat lon, if not defined, zoom = 0;
$zoom = 0;
if ($latlon) {
    $foo = explode(',', $latlon);
    $lat = $foo[0];
    $lon = $foo[1];
}

if ($lat || $lon) {
    $zoom = 15;
}

$txt['fid'] = $fid;
$txt['lat'] = $lat;
$txt['lon'] = $lon;
$txt['zoom'] = $zoom;
$txt['gmap_api'] = $config['gmap_api'];
$txt['main_body'] = quick_tpl(load_tpl('gmap_picker.tpl'), $txt);
generate_html_header();
flush_tpl('popup');
