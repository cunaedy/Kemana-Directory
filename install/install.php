<?php
$cmd = empty($_GET['cmd']) ? 'new' : $_GET['cmd'];
if ($cmd == 'new') {
    $ctext = 'Installation';
} else {
    $ctext = 'Upgrade';
}

// find error!
$warn = '';

// search for existing installation
if (($cmd == 'new') && (file_exists('../includes/db_config.php'))) {
    $warn .= "<li>qEngine may have already been installed in this server. If you continue installation, all contents will be reset!</li>\n";
}

// trying to write to public directory
$folder = array('.', 'file', 'image', 'thumb');
foreach ($folder as $val) {
    @fopen("../public/$val/temp.tmp", 'w-');

    if (!file_exists("../public/$val/temp.tmp")) {
        $warn .= "<li>Directory <b>public/$val/</b> can not be written. Make sure that you have set the directory permission into 777/755 using your FTP Client.</li>\n";
    } else {
        unlink("../public/$val/temp.tmp");
    }
}

// detect GD version
$gd_funcs = get_extension_funcs("gd");
if (empty($gd_funcs)) {
    $gd_ver = 0;
    $warn .= '<li>GD Library is required by this program. You can\'t continue without GD Library. Contact your server administrator to enable it.</li>';
} elseif (in_array('imagecreatetruecolor', $gd_funcs)) {
    $gd_ver = '2';
} else {
    $gd_ver = '1';
}

if (!$gd_ver) {
    $gd_text = '<b>None</b>';
} else {
    $gd_text = "v$gd_ver.x.x";
}

// detect location
$abs_path = str_replace('\\', '/', realpath('./..'));
$abs_url = substr($_SERVER["HTTP_REFERER"], 0, strpos($_SERVER["HTTP_REFERER"], '/install/'));

// any error?
if (!empty($_GET['err'])) {
    $warn = "<li>$_GET[err]</li>\n".$warn;
}
if (empty($warn)) {
    $warn = 'No error so far';
} else {
    $warn = "<font color=\"#FF0000\"><b>WARNING!</b></font><ul>$warn</ul> <b>If you continue installation, qEngine may not work correctly!</b>";
}
?>

<html>

<head>
<meta http-equiv="Content-Language" content="en-us" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../skins/_common/default.css"/>
<title>Kemana: Installation</title>
</head>

<body>
	<div style="background:#000; text-align:center; padding:20px"><img src="../skins/_admin/images/qe.png" alt="logo" /></div>
	<div class="container">
		<h1><?php echo $ctext; ?></h1>
		<p class="lead">Step 2: In order to complete this installation please fill out the details
		requested below. Please note that the database you install into should already exist.</p>
		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="panel-title">Server Configuration</h3></div>
			<form method="post" action="install_process.php">
				<input type="hidden" name="cmd" value="<?php echo $cmd; ?>" />
				<input type="hidden" name="gd_ver" value="<?php echo $gd_ver; ?>" />
				<table class="table">
					<tr><td width="100%" colspan="2"><h4>Database Configuration</h4></td></tr>
					<tr>
						<td width="37%">Database Server Hostname<br /><font size="1"><i>Default: &#39;localhost&#39;</i></font></td>
						<td width="63%"><input type="text" name="db_hostname" size="60" value="localhost" /></td>
					</tr>
					<tr>
						<td width="37%">Database Name</td>
						<td width="63%"><input type="text" name="db_name" size="60" /></td>
					</tr>
					<tr>
						<td width="37%">Database Username</td>
						<td width="63%"><input type="text" name="db_username" size="27" /></td>
					</tr>
					<tr>
						<td width="37%">Database Password</td>
						<td width="63%"><input type="text" name="db_passwd" size="17" /></td>
					</tr>
					<tr>
						<td width="37%">Table Prefix</td>
						<td width="63%"><input type="text" name="db_prefix" size="17" value="qe_" /></td>
					</tr>
					<?php if ($cmd == 'new'): ?>
					<tr><td width="100%" colspan="2"><h4>Site Configuration</h4></td></tr>
					<tr>
						<td width="37%">Absolute URL (e.g http://www.yourdomain/site)</td>
						<td width="63%"><input type="text" name="abs_url" size="60" value="<?php echo $abs_url; ?>" /></td>
					</tr>
					<tr>
						<td width="37%">Absolute Path (e.g /home/www/mysite/site)</td>
						<td width="63%"><input type="text" name="abs_path" size="60" value="<?php echo $abs_path; ?>" /></td>
					</tr>
					<tr><td width="100%" colspan="2"><h4>Admin Configuration</h4></td></tr>
					<tr>
						<td width="37%">Site/Administrator&#39;s Email</td>
						<td width="63%"><input type="text" name="admin_email" size="60" /></td>
					</tr>
					<tr>
						<td width="37%">Administrator Username</td>
						<td width="63%"><input type="text" name="admin_username" size="60" /></td>
					</tr>
					<tr>
						<td width="37%">Administrator Password</td>
						<td width="63%"><input type="password" name="admin_passwd" size="17" /></td>
					</tr>
					<tr>
						<td width="37%">Administrator Password Confirm</td>
						<td width="63%"><input type="password" name="admin_passwd_confirm" size="17" /></td>
					</tr>
					<tr><td width="100%" colspan="2"><h4>Miscellaneous Configuration</h4></td></tr>
					<tr>
						<td width="37%">Detected GD library version</td>
						<td width="63%"><?php echo $gd_text; ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td width="100%" colspan="2"><h4>Notice! <span style="color:red">Please Read!</span></h4></td>
					</tr>
					<tr>
						<td width="100%" colspan="2">
							If you want to install/uninstall modules in the future, please make sure the following files are writable:
							<ul>
								<li>/admin/module/admin_menu.xml</li>
							</ul>
						</td>
					</tr>
					<tr><td width="100%" colspan="2"><h4>Error Messages</h4></td></tr>
					<tr><td width="100%" colspan="2"><?php echo $warn ?></td></tr>
					<tr>
						<td width="100%" colspan="2" class="section"><button type="submit" class="btn btn-primary">Continue</button></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>

</html>