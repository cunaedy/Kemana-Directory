<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="{$l_encoding}" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="{$site_description}" />
	<meta name="keywords" content="{$site_keywords}" />
	<meta name="author" content="{$site_email}" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>{$head_title}</title>
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_common/default.css"/>
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_common/jscripts.css"/>
	{$module_css_list}
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon}" />
	<!--[if lt IE 9]><script src="{$site_url}/misc/js/ie8.js"></script><![endif]-->
	<script type="text/javascript" src="{$site_url}/misc/js/jquery.min.js"></script>
	<script type="text/javascript" src="{$site_url}/misc/js/jscripts.js"></script>
	{$module_js_list}
</head>

<body style="background-image:none;background:#fff;margin: 10px">
<!-- BEGINIF $system_message -->
{$system_message}
<!-- ENDIF -->
{$main_body}
</body>

</html>