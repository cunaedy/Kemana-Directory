<div class="panel panel-default">
	<div class="panel-heading">Language Editor &mdash; [{$lang_name}]</div>
	<div class="panel-body">
		<ul id="qeconfig" class="nav nav-pills">
			<li {$class0}><a href="lang.php?tab=0&amp;lang_id={$lang_id}">Properties</a></li>
			<li {$class1}><a href="lang.php?tab=1&amp;lang_id={$lang_id}">General</a></li>
			<li {$class2}><a href="lang.php?tab=2&amp;lang_id={$lang_id}">Date &amp; Time</a></li>
			<li {$class3}><a href="lang.php?tab=3&amp;lang_id={$lang_id}">Special</a></li>
			<li {$class4}><a href="lang.php?tab=4&amp;lang_id={$lang_id}">Messages</a></li>
			<li {$class5}><a href="lang.php?tab=5&amp;lang_id={$lang_id}">Emails</a></li>
			<li {$class6}><a href="lang.php?tab=6&amp;lang_id={$lang_id}">Custom</a></li>
		</ul>
	</div>

	<!-- BEGINIF $tpl_mode == 'properties' -->
	<form method="post" action="lang.php">
		<input type="hidden" name="tab" value="{$tab}" />
		<input type="hidden" name="lang_id" value="{$lang_id}" />
		<input type="hidden" name="cmd" value="save_properties" />
		<div class="tab-pane active">
			<table class="table">
				<tr><th width="20%">Key</th><th width="80%">Value</th></tr>
				<tr><td>Language ID</td><td>{$lang_id}</td>
				<tr><td>Language Name</td><td><input type="text" name="lang_name" value="{$lang_name}" style="width:500px" /></td>
				<!-- tr><td>Enabled?</td><td>{$lang_enabled}</td -->
				<tr><td>Language short code</td><td><input type="text" name="l_language_short" value="{$cfg_l_language_short}" style="width:500px" /></td>
				<tr><td>Encoding</td><td><input type="text" name="l_encoding" value="{$cfg_l_encoding}" style="width:500px" />
					 <span class="glyphicon glyphicon-info-sign help tips" title="Enter 'utf-8' if you are not sure."></span></td>
				<tr><td>Text direction</td><td><input type="text" name="l_direction" value="{$cfg_l_direction}" style="width:500px" />
					 <span class="glyphicon glyphicon-info-sign help tips" title="Enter 'ltr' or 'rtl'."></span></td>
				<tr><td>Mysql encoding</td><td><input type="text" name="l_mysql_encoding" value="{$cfg_l_mysql_encoding}" style="width:500px" />
					 <span class="glyphicon glyphicon-info-sign help tips" title="Enter 'utf8' if you are not sure."></span></td>
				<tr><td>Long date format</td><td><input type="text" name="l_long_date_format" value="{$cfg_l_long_date_format}" style="width:500px" />
					 <span class="glyphicon glyphicon-info-sign help tips" title="See www.php.net/manual/en/function.date.php for more information."></span></td>
				<tr><td>Short date format</td><td><input type="text" name="l_short_date_format" value="{$cfg_l_short_date_format}" style="width:500px" />
					 <span class="glyphicon glyphicon-info-sign help tips" title="See www.php.net/manual/en/function.date.php for more information."></span></td>
				<tr><td>Form date format</td><td><input type="text" name="l_select_date_format" value="{$cfg_l_select_date_format}" style="width:500px" />
					 <span class="glyphicon glyphicon-info-sign help tips" title="M = month, D = day, Y = year."></span></td>
				<tr><td colspan="2">

					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						Language Tools <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a class="inline" href="#copy_lang">Copy Language</a></li>
							<li><a class="inline" href="#del_lang">Delete Language</a></li>
							<li class="divider"></li>
							<li><a href="lang.php?cmd=export&amp;lang_id={$lang_id}">Export Language</a></li>
							<li><a class="inline" href="#import_lang">Import Language</a></li>
						</ul>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						Load Another Language <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="lang.php?tab=0&amp;lang_id=en">English (Default)</a></li>
							{$lang_list}
						</ul>
					</div>
					<button type="submit" class="btn btn-primary" style="margin-left:20px">Save</button></td></tr>
			</table>
		</div>
	</form>
</div>
<div style="display:none">
	<div id="copy_lang" style="padding:20px">
		<form method="get" action="lang.php">
		<input type="hidden" name="cmd" value="copy_lang" />
		<input type="hidden" name="lang_id" value="{$lang_id}" />
			<h1>Copy Current Language [{$lang_name}]</h1>
			<table>
				<tr><td>New language ID</td><td><input type="text" name="new_lang_id" required /></td></tr>
				<tr><td>New language name</td><td><input type="text" name="new_lang_name" required /></td></tr>
			</table>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
</div>


<div style="display:none">
	<div id="del_lang" style="padding:20px">
		<form method="get" action="lang.php">
		<input type="hidden" name="cmd" value="del_lang" />
		<input type="hidden" name="lang_id" value="{$lang_id}" />
			<h1>Delete Current Language [{$lang_name}]</h1>
			<p>Are you sure you want to delete this language? This will also <b>reset the default language to English</b>.<br />
			This process can't be undone!</p>
			<button type="submit" class="btn btn-danger">Yes, Remove It</button>
			<button type="submit" class="btn btn-default" onclick="javascript:$.colorbox.close();return false">Cancel</button>
		</form>
	</div>
</div>


<div style="display:none">
	<div id="import_lang" style="padding:20px">
		<form method="post" action="lang.php" enctype="multipart/form-data">
		<input type="hidden" name="cmd" value="import" />
		<input type="hidden" name="lang_id" value="{$lang_id}" />
			<h1>Import New Language</h1>
			<p>Please upload your language pack file (lang_pack.xml): <input type="file" name="xml_file" /></p>
			<p><label><input type="checkbox" name="overwrite" value="1" /> Overwrite old language</label></p>
			<button type="submit" class="btn btn-primary">Import Language</button>
			<button type="submit" class="btn btn-default" onclick="javascript:$.colorbox.close();return false">Cancel</button>
		</form>
	</div>
</div>
<script type="text/javascript">
$(".inline").colorbox({inline:true, width:"50%"});
</script>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'custom' -->
<form method="post" action="lang.php">
	<input type="hidden" name="tab" value="{$tab}" />
	<input type="hidden" name="cmd" value="save_custom" />
	<input type="hidden" name="lang_id" value="{$lang_id}" />
	<div class="tab-pane active" style="margin-top:10px">
		<table class="table">
			<tr><th width="20%">Key</th><th width="80%">Value</th></tr>
		<!-- BEGINBLOCK list -->
			<tr><td>{$lang_key}</td><td><input type="text" name="{$lang_key}" value="{$lang_val}" style="width:500px" />
			<a href="lang.php?cmd=del&amp;lang_id={$lang_id}&amp;id={$lang_key}&amp;AXSRF_token={$axsrf}"><span class="glyphicon glyphicon-remove text-danger"></span></a></td>
		<!-- ENDBLOCK -->
			<tr><td colspan="2"><b>The following empty fields can be used to define your own custom words. You can define up to 10 words at once. Please remember the prefix l_</b></td></tr>
		<!-- BEGINBLOCK empty -->
			<tr><td><input type="text" name="ck_{$i}" style="width:150px" placeholder="l_" /></td>
			<td><input type="text" name="cv_{$i}" style="width:500px" /></td>
		<!-- ENDBLOCK -->
		</table>
	</div>
	<p align="center"><button type="submit" class="btn btn-primary">Save</button></p>
</form>
</div>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'else' -->
<form method="post" action="lang.php">
	<input type="hidden" name="tab" value="{$tab}" />
	<input type="hidden" name="cmd" value="save" />
	<input type="hidden" name="lang_id" value="{$lang_id}" />
	<div class="tab-pane active" style="margin-top:10px">
		<table class="table ">
			<tr><th width="20%">Key</th><th width="80%">Value</th></tr>
		<!-- BEGINBLOCK list -->
			<tr><td>{$lang_key}</td><td>{$lang_val}</td>
		<!-- ENDBLOCK -->
		</table>
	</div>
	<p align="center"><button type="submit" class="btn btn-primary">Save</button></p>
</form>
</div>

<script>
function change (id)
{
	$input = $("#textbox_"+id)
	$link = $("#change_"+id)
	v = $input.val();
	fid = $input.attr('id');
	fname = $input.attr('name');
	textarea = '<textarea style="width:90%;height:50px" id="'+fid+'" name="'+fname+'"></textarea>';
	$input.after(textarea).remove();
	$("#textbox_"+id).val(v);
	$link.remove();
};
</script>
<!-- ENDIF -->