<?php
// qVC - quick visual confirmation
// part of qEngine
require_once './includes/user_init.php';
$font = array();
$font[1]['name'] = 'parallello.ttf';
$font[1]['offset_y'] = 40;
$font[1]['space_x'] = 28;

$bg = array();
$bg[1] = 'bg1.png';
$bg[2] = 'bg2.png';
$bg[3] = 'bg3.png';
$bg[4] = 'bg4.png';
$bg[5] = 'bg5.png';

// randomize font & bg
$max_f = count($font);
$ran_f = mt_rand(1, $max_f);

$max_b = count($bg);
$ran_b = mt_rand(1, $max_b);

$offset_y = $font[$ran_f]['offset_y'];
$space_x = $font[$ran_f]['space_x'];
$rand_fo = './misc/captcha/'.$font[$ran_f]['name'];
$rand_bg = './misc/captcha/'.$bg[$ran_b];

// imageTTFtext supported?
if (function_exists('imagettftext')) {
    $ttf = true;
} else {
    $ttf = false;
}

// get value
$t = ip_config_value('visual');

// output
if ($ttf) {
    $gb = imagecreatefrompng($rand_bg);
} else {
    $gb = imagecreate(150, 70);
}
$gry = mt_rand(150, 255);
$bgc = imagecolorallocate($gb, $gry, $gry, $gry);
$grc = imagecolorallocate($gb, 200, 200, 200);
$out = imagecolorallocate($gb, 255, 255, 255);
$hin = imagecolorallocate($gb, 0, 0, 0);
$l = strlen($t);

// write hint
imagefilledrectangle($gb, 0, 60, 149, 69, $hin);
imagestring($gb, 3, 5, 58, 'Valid char: 0-9, a-f', $bgc);

for ($i = 0; $i < $l; $i++) {
    $fgc = imagecolorallocate($gb, mt_rand(0, 128), mt_rand(0, 128), mt_rand(0, 128));
    $s = mt_rand(3, 5);			// font size (for internal font)
    $ss = mt_rand(30, 35);			// font size (for TTF font)
    $y = $offset_y - mt_rand(0, 5);	// y offset
    $x = $i * $space_x;			// letter spacing
    $d = (mt_rand(0, 10)) * 2;		// angle
    $q = (mt_rand(0, 1));			// + / - angle
    if ($q) {
        $d = $d * -1;
    }
    if ($ttf) {
        imageTTFtext($gb, $ss, $d, $x + 12, $y+2, $out, $rand_fo, $t[$i]);
        imageTTFtext($gb, $ss, $d, $x + 10, $y, $fgc, $rand_fo, $t[$i]);
    } else {
        imagechar($gb, $s, $x + 16, $y-16, $t[$i], $out);
        imagechar($gb, $s, $x + 15, $y-17, $t[$i], $fgc);
    }
}

header('Content-Type: image/png');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
imagePNG($gb);
imagedestroy($gb);
