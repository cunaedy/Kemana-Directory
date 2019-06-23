<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$l_lost_passwd}</li>
</ol>

<!-- BEGINIF $tpl_mode == 'lost' -->
<h1>{$l_lost_passwd}</h1>
<form method="post" action="{$site_url}/includes/lost_process.php" style="margin:auto">
	<div class="table_div">
		<div>
			<legend>{$l_username}</legend>
			<div><input type="text" name="user_id" size="50" maxlength="80" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_enter_captcha}</legend>
			<div><img src="visual.php" alt="captcha" /><br /><input type="text" name="qvc" size="5" maxlength="5" required="required" /> <span class="required">&bull;</span></div>
		</div>
	</div>
	<div style="clear:both"></div>
	<p align="center"><button type="submit" class="btn btn-primary">{$l_submit}</button></p>
</form>
<!-- ENDIF -->


<!-- BEGINIF $tpl_mode == 'reset' -->
<h1>{$l_reset_passwd}</h1>
<form method="post" action="{$site_url}/includes/lost_process.php" style="margin:auto">
<input type="hidden" name="do_reset" value="1" />
	<div class="table_div">
		<div>
			<legend>{$l_username}</legend>
			<div><input type="text" name="user_id" value="{$user_id}" size="50" maxlength="80" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_reset_code}</legend>
			<div><input type="text" name="reset" value="{$reset}" size="50" maxlength="80" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_new_password}</legend>
			<div><input type="password" name="user_passwd" id="user_passwd" size="50" maxlength="80" required="required" onkeyup="passwordStrength('user_passwd', this.value)" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_enter_captcha}</legend>
			<div><img src="visual.php" alt="captcha" /><br /><input type="text" name="qvc" size="5" maxlength="5" required="required" /> <span class="required">&bull;</span></div>
		</div>
	</div>
	<div style="clear:both"></div>
	<p align="center"><button type="submit" class="btn btn-primary">{$l_submit}</button></p>
</form>
<!-- ENDIF -->