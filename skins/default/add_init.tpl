<!-- BEGINIF $tpl_mode == 'dir_select' -->
<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$l_add_listing}</li>
</ol>
<h1>{$l_select_dir}</h1>
<div class="row">
<!-- BEGINBLOCK list -->
<div class="col-sm-6">
	<div class="dir_select thumbnail">
		<a href="add.php?cmd=add&amp;dir_id={$idx}"><img src="{$dir_image}" alt="{$dir_title}" /></a>
		<div class="caption">
			<h3>{$dir_title}</h3>
			<div class="dir_desc">{$dir_body}</div>
			<p><a href="add.php?cmd=add&amp;dir_id={$idx}" class="btn btn-primary" role="button">{$l_select}</a></p>
		</div>
	</div>
</div>
<!-- ENDBLOCK -->
</div>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'form' -->
<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$l_edit_listing}</li>
</ol>
<form method="post" action="{$site_url}/add.php" enctype="multipart/form-data">
<input type="hidden" name="cmd" value="edit_guest" />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> {$l_edit_item}</div>
		 <div class="panel-body">
    		<p>{$l_edit_guest_why}</p>
  		</div>
		<table border="0" width="100%" class="table table-form" id="result1">
			<tr>
				<td width="20%">{$l_item_id}</td>
				<td width="80%"><input type="text" name="item_id" required="required" /></td>
			</tr>
			<tr>
				<td>{$l_password}</td>
				<td><input type="password" name="edit_passwd" required="required" /> <a href="{$site_url}/add.php?cmd=lost"><span class="glyphicon glyphicon-question-sign"></span> {$l_lost_passwd}</a></td>
			</tr>
			<tr>
				<td width="20%">{$l_enter_captcha}</td>
				<td width="80%"><img src="{$site_url}/visual.php" alt="captcha" /><div style="margin-top:3px"><input type="text" name="visual" required="required" /></div></td>
			</tr>
		</table>
	</div>
	<div style="text-align:right; padding:10px"><button type="submit" class="btn btn-primary">{$l_submit}</button></div>
</form>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'lost' -->
<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active"><a href="{$site_url}/add.php?cmd=edit_guest">{$l_edit_listing}</a></li>
	<li class="active">{$l_lost_passwd}</li>
</ol>
<form method="post" action="{$site_url}/add.php" enctype="multipart/form-data">
<input type="hidden" name="cmd" value="lost" />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> {$l_lost_passwd}</div>
		 <div class="panel-body">
    		<p>{$l_edit_lost_why}</p>
  		</div>
		<table border="0" width="100%" class="table table-form" id="result1">
			<tr>
				<td width="20%">{$l_item_id}</td>
				<td width="80%"><input type="text" name="item_id" required="required" /></td>
			</tr>
			<tr>
				<td width="20%">{$l_enter_captcha}</td>
				<td width="80%"><img src="{$site_url}/visual.php" alt="captcha" /><div style="margin-top:3px"><input type="text" name="visual" required="required" /></div></td>
			</tr>
		</table>
	</div>
	<div style="text-align:right; padding:10px"><button type="submit" class="btn btn-primary">{$l_submit}</button></div>
</form>
<!-- ENDIF -->