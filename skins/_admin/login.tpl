<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="{$l_encoding}" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Administration Panel</title>
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_common/default.css" />
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_common/jscripts.css"/>
	<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_admin/style.css" />
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon}" />
	<script type="text/javascript" src="{$site_url}/misc/js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="{$site_url}/misc/js/jscripts.js"></script>
	<style type="text/css">
		body{margin:50px; background-color: #eee;}
		.form-signin{max-width:330px;margin:0 auto;padding:15px;}
		.form-signin .form-signin-heading,.form-signin .checkbox{margin-bottom:10px;}
		.form-signin .checkbox{font-weight:400;}
		.form-signin .form-control{position:relative;height:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;font-size:16px;padding:10px;}
		.form-signin .form-control:focus{z-index:2;}
		.form-signin .userid{margin-bottom:-1px;border-bottom-right-radius:0;border-bottom-left-radius:0;width:100%}
		.form-signin .passwd{margin-bottom:-1px;border-radius:0;width:100%}
		.form-signin .qvc{margin-bottom:10px;border-top-left-radius:0;border-top-right-radius:0;width:100%}
	</style>
</head>

<body>
	<div class="login_box">
		<form class="form-signin" method="post" action="login.php">
			<h2 class="form-signin-heading">Please sign in</h2>
			<input type="text" name="user_id" class="form-control userid" placeholder="User ID" required autofocus />
			<input type="password" name="user_passwd" class="form-control passwd" placeholder="Password" required />
			<input type="text" class="form-control qvc" name="visual" placeholder="Enter the number below" autocomplete="off" required />
			<div style="text-align:center"><img src="../visual.php" alt="Are you a robot?" style="text-align:center"/></div>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
			<p align="center"><a href="../profile.php?mode=lost">Lost Password?</a></p>
		</form>
	</div>
</html>