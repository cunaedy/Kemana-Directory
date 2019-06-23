<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check('site_setting');

// demo mode?
if ($config['demo_mode']) {
    admin_die('demo_mode');
}

// get phpinfo into vars
ob_start();
phpinfo();

preg_match('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);

# $matches [1]; # Style information
# $matches [2]; # Body information
ob_start();
echo "<div class=\"panel panel-default\"><div class=\"panel-heading\">PHP Info</div><div class='phpinfodisplay panel-body'><style type='text/css'>\n",
    join(
        "\n",
        array_map(
            create_function(
                '$i',
                'return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );'
                ),
            preg_split('/\n/', $matches[1])
            )
        ),
    "{} \n.phpinfodisplay h2 {font-size: 125%; background:none; color: #000}
	  </style>\n",
    $matches[2],
    "\n</div></div>\n";
$txt['main_body'] = ob_get_contents();
ob_end_clean();
flush_tpl('adm');
