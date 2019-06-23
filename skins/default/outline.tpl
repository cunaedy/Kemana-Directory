<!DOCTYPE html>
<html lang="{$l_language_short}" dir="{$l_direction}">
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
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/default/style.css" />
	{$module_css_list}
	<link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon}" />
	<link rel="apple-touch-icon" href="{$favicon}" />
	<script type="text/javascript" src="{$site_url}/misc/js/jquery.min.js"></script>
	<script type="text/javascript" src="{$site_url}/misc/js/jscripts.js"></script>
	{$module_js_list}
</head>

<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.10";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!-- BEGINIF $system_message -->
<div style="display:none">
	<div id="system_msg">
	{$system_message}
	</div>
</div>
<!-- ENDIF -->
<!-- BEGINIF $current_admin_level -->
{$acp_shortcuts}
<!-- ENDIF -->
<nav class="navbar navbar-inverse navbar-static-top" style="margin-bottom:0">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="{$site_url}/index.php" class="navbar-brand"><img src="{$favicon}" alt="{$site_name}"/></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			{qemod:qmenu:main_menu}

			<!-- BEGINIF $isLogin -->
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
					<span class="glyphicon glyphicon-user"></span> {$current_user_id}<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{$site_url}/account.php">{$l_my_account}</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="{$site_url}/profile.php?mode=logout">{$l_logout}</a></li>
					</ul>
				</li>
			</ul>
			<!-- ELSE -->
			<p class="navbar-text  navbar-right"><a href="{$site_url}/profile.php?redir={$current_url}" class="navbar-link">{$l_login_register}</a></p>
			<!-- ENDIF -->

		</div>
	</div>
</nav>

{$main_content}

<div id="footer" style="background:#111">
	<div class="container">
		<div class="row">
			<div id="footer_content">
				<!-- ONLY UP TO 4 (FOUR) MODULES IN B1 HERE!!! -->
				{$module_box_B1}

				<div class="col-sm-6 col-md-6 col-lg-3">
					<h4>{$l_site_name} &bull; {$l_site_slogan}</h4>
					<ul class="list_3">
						<li><a href="{$print_this_page}">{$l_print}</a></li>
						<li>&copy; All Rights Reserved</li>
						<li><!-- BEGINMODULE ztopwatch --><!-- ENDMODULE --></li>
					</ul>
				</div>

				<div class="col-sm-6 col-md-6 col-lg-3">
					<!-- PLEASE DO NOT REMOVE THIS INFORMATION, UNLESS YOU HAVE PURCHASED THE LICENSE -->
					<h4>Powered By</h4>
					<a href="http://www.c97.net"><img src="{$site_url}/skins/_common/images/qe.png" alt="qEngine" /></a>
					<!-- PLEASE DO NOT REMOVE THIS INFORMATION, UNLESS YOU HAVE PURCHASED THE LICENSE -->
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				{$module_box_B2}
			</div>
		</div>
	</div>
</div>

<script>
$(function(){
	$("a.popiframe").colorbox({iframe:true, width:"900px", maxWidth:"95%", height:"500px", maxHeight:"95%"});
	$("a.popiframe_s").colorbox({iframe:true, width:"500px", maxWidth:"95%", height:"300px", maxHeight:"95%"});
	$("a.popiframe_sp").colorbox({iframe:true, width:"600px", maxWidth:"95%", height:"540px", maxHeight:"95%"});
	$("a.lightbox").colorbox({rel:'group', maxWidth:"95%", maxHeight:"95%"});
	$(".tips").tooltip({placement : 'auto right',html:true,container: 'body'});
	$('a.simpleAjax').click(function(event){event.preventDefault();var that=$(this);$.ajax({url:$(this).attr('href'),success:function(result,status,xhr){var res=$.parseJSON(result);var sCallback=$(that).attr('data-ajax-success-callback')==undefined?false:$(that).attr('data-ajax-success-callback');var sArg=$(that).attr('data-ajax-success-arg')==undefined?0:$(that).attr('data-ajax-success-arg');var fCallback=$(that).attr('data-ajax-failed-callback')==undefined?false:$(that).attr('data-ajax-failed-callback');var fArg=$(that).attr('data-ajax-failed-arg')==undefined?0:$(that).attr('data-ajax-failed-arg');if(res[0])alert('Warning!\n'+res[1]);if(!res[0]&&res[2]==1){if(sCallback)window[sCallback](sArg);}else{if(fCallback)window[fCallback](fArg);}},error:function(result,status,xhr){alert('Error '+result.status+' '+result.statusText+'. Please try again later!');res=false;}});return false;});
	var path = '{$request_location}';
	if (path !== undefined) { $('ul.nav,ul.dropdown-menu').find("a[href$='" + path + "']").parents('li').addClass('active'); }

	<!-- BEGINIF $system_message -->
	// system message
	$.colorbox({inline:true,href:'#system_msg',title:'{$site_name}', maxWidth:"95%", maxHeight:"95%"})
	<!-- ENDIF -->
});
</script>
</body>

</html>