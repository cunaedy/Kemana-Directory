<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$l_tell_friend}</li>
</ol>

<h1>{$l_tell_friend}</h1>
<form method="post" action="{$site_url}/tell.php">
<input type="hidden" name="item_id" value="0" />
<input type="hidden" name="contact_us" value="0" />
<input type="hidden" name="cmd" value="send" />
	<div class="table_div">
		<div>
			<legend>{$l_your_name}</legend>
			<div><input type="text" name="name" size="30" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_your_email}</legend>
			<div><input type="text" name="email" size="30" value="{$user_email}" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_friend_name}</legend>
			<div><input type="text" name="friend_name" size="30" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_friend_email}</legend>
			<div><input type="text" name="friend_email" size="30" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_message}</legend>
			<div><textarea name="tell_body" style="height:80px"></textarea></div>
		</div>
		<div>
			<legend>{$l_enter_captcha}</legend>
			<div><img src="visual.php" alt="captcha" /><br /><input type="text" name="visual" size="3" maxlength="3" required="required" /> <span class="required">&bull;</span></div>
		</div>
	</div>
	<div style="clear:both"></div>
	<p align="center"><button type="submit" class="btn btn-primary">{$l_submit}</button></p>
</form>