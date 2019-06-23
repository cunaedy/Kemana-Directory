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
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/default/layout.css" />
	{$module_css_list}
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon}" />
	<!--[if lt IE 9]><script src="{$site_url}/misc/js/ie8.js"></script><![endif]-->
	<script type="text/javascript" src="{$site_url}/misc/js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="{$site_url}/misc/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{$site_url}/misc/js/jscripts.js"></script>
	{$module_js_list}
	
	<style type="text/css">
		@media print{
			.print {display:none !important;}
		}
	</style>
</head>

<body style="margin:0;padding:0; background:none">
<p class="print"><a href="#" onclick="javascript:window.print()" class="print btn btn-primary">{$l_print}</a></p>
{$company_logo}
{$main_body}
<hr />

<p align="center" class="small">
 Powered by <a href="http://www.c97.net">qEngine</a><br />
<!-- BEGINMODULE ztopwatch --><!-- ENDMODULE -->
</p>
</body>

</html>	