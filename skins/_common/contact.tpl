<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$l_contact_us}</li>
</ol>

<h1>{$l_contact_us}</h1>
<!-- BEGINMODULE page_gallery -->
// Contact text
page_id = 6
body = 1
<!-- ENDMODULE -->

<p><b>{$site_name}</b></p>
{$site_address}

<hr />
<p>{$l_contact_us_form}
<form method="post" action="{$site_url}/contact.php">
<input type="hidden" name="cmd" value="send" />
	<div class="table_div">
		<div>
			<legend>{$l_your_name}</legend>
			<div><input type="text" name="name" style="max-width:240px" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_email_address}</legend>
			<div><input type="email" name="email" style="max-width:240px" required="required" /> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_subject}</legend>
			<div><input type="text" name="subject" style="max-width:240px" required="required" /></div>
		</div>
		<div>
			<legend>{$l_message}</legend>
			<div><textarea name="body" rows="10" cols="20" style="max-width:240px" required="required"></textarea> <span class="required">&bull;</span></div>
		</div>
		<div>
			<legend>{$l_enter_captcha}</legend>
			<div><img src="visual.php" alt="captcha" /><br /><input type="text" name="visual" size="3" maxlength="3" required="required" /> <span class="required">&bull;</span></div>
		</div>
		</div>
	<div style="clear:both"></div>
	<p align="center"><button type="submit" class="btn btn-primary">{$l_submit}</button></p>
</form>