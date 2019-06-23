<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$l_account_act}</li>
</ol>

<h1>{$l_account_act}</h1>
<p>{$l_account_act_why}</p>
<form method="post" action="{$site_url}/includes/act_process.php">
	<div class="table_div">
		<div>
			<legend>{$l_username}</legend>
			<div><input type="text" name="user_id" value="{$user_id}" size="50" maxlength="255" required="required" class="username" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_account_act_key}</legend>
			<div><input type="text" name="act" value="{$act}" size="50" maxlength="16" required="required" class="password"/> <span class="required">&bull;</span><br />
			<span class="small">{$l_account_act_key_why}</span></div>
		</div>
	</div>
	<div style="clear:both"></div>
	<p align="center"><button type="submit" class="btn btn-primary">{$l_submit}</button></p>
</form>