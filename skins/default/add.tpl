<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<!-- BEGINIF $tpl_mode == 'add' -->
	<li class="active">{$l_add_listing}</li>
	<!-- ELSE -->
	<li class="active">{$l_edit_listing}</li>
	<!-- ENDIF -->
</ol>
<!-- BEGINIF $allow_upgrade -->
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> {$l_upgrade_listing}</div>
		<div class="panel-body">
    		{$l_upgrade_why}
    		<p><a href="{$site_url}/add_upgrade.php?item_id={$item_id}" class="btn btn-primary">{$l_upgrade_now}</a></p>
  		</div>
	</div>
<!-- ENDIF -->

<form method="post" action="{$site_url}/includes/add_process.php" enctype="multipart/form-data">
<input type="hidden" name="item_id" value="{$item_id}" />
<input type="hidden" name="dir_id" value="{$dir_id}" />

<!-- BEGINIF $tpl_mode == 'add' -->
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> {$l_add_listing}</div>
<!-- ELSE -->
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> {$l_edit_listing}</div>
<!-- ENDIF -->
		<table border="0" width="100%" class="table table-form">
			<tr>
				<td width="20%">{$l_directory}</td>
				<td width="80%">{$dir_title}</td>
			</tr>
			<tr>
				<td>{$l_main_category}</td>
				<td><div id="SearchDiv_1" class="SearchDiv"><input type="search" id="SearchBox_1" placeholder="{$l_search}" /></div><div>{$category_form} <a href="javascript:display_search(1)"><span class="glyphicon glyphicon-search"></span></a> {$l_required_symbol}</div>
				</td>
			</tr>
			<!-- BEGINBLOCK multi_cat -->
			<tr>
				<td>{$l_category} {$i}</td>
				<td><div id="SearchDiv_{$i}" class="SearchDiv"><input type="search" id="SearchBox_{$i}" placeholder="{$l_search}" /></div><div>{$category_form} <a href="javascript:display_search({$i})"><span class="glyphicon glyphicon-search"></span></a></div></td>
			</tr>
			<!-- ENDBLOCK -->
			<!-- BEGINIF $allow_logo_empty -->
			<tr>
				<td style="vertical-align: top">{$l_logo}</td>
				<td><img border="0" src="{$site_url}/skins/_common/images/noimage.gif" alt="No thumbnail" /><div><input type="file" name="logo" /></div></td>
			</tr>
			<!-- ENDIF -->
			<!-- BEGINIF $allow_logo_exists -->
			<tr>
				<td style="vertical-align: top">{$l_logo}</td>
				<td>{$logo} <div><a href="add.php?cmd=del_img&amp;item_id={$item_id}&amp;AXSRF_token={$axsrf}"><span class="glyphicon glyphicon-remove"></span> Remove</a></div></td>
			</tr>
			<!-- ENDIF -->
			<!-- BEGINIF $require_url -->
			<tr>
				<td>{$l_target_url}</td>
				<td><input type="url" size="50" name="item_url" value="{$item_url}" required="required" /> {$l_required_symbol}</td>
			</tr>
			<!-- ENDIF -->
			<!-- BEGINIF $allow_url_mask -->
			<tr>
				<td>{$l_url_mask}</td>
				<td><input type="text" size="50" name="item_url_mask" value="{$item_url_mask}" /></td>
			</tr>
			<!-- ENDIF -->
			<tr>
				<td>{$l_title}</td>
				<td><input type="text" size="50" name="item_title" value="{$item_title}" required="required" /> {$l_required_symbol}</td>
			</tr>
			<!-- BEGINIF $require_summary -->
			<tr>
				<td>{$l_summary}</td>
				<td><input type="text" size="50" name="item_summary" value="{$item_summary}" required="required" /> {$l_required_symbol}</td>
			</tr>
			<!-- ENDIF -->
			<tr>
				<td style="vertical-align: top">{$l_description}</td>
				<td><textarea name="item_details" style="width:70%;height:200px" required="required">{$item_details}</textarea>  {$l_required_symbol}</td>
			</tr>
			<!-- BEGINIF $require_backlink -->
			<tr>
				<td>{$l_backlink_url}</td>
				<td><input type="url" size="50" name="item_backlink_url" id="item_backlink_url" value="{$item_backlink_url}" required="required" />
				<span id="backlink_ok"></span> {$l_required_symbol}</td>
			</tr>
			<!-- ENDIF -->
			<!-- BEGINIF $require_email -->
			<tr>
				<td>{$l_email_address}</td>
				<td><input type="email" size="50" name="owner_email" id="owner_email" value="{$owner_email}" class="width-md" required="required" /> {$l_required_symbol}</td>
			</tr>
			<!-- ENDIF -->
		</table>
	</div>

	<!-- BEGINIF $cf_form -->
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> {$l_more_details}</div>
		<table border="0" width="100%" class="table table-form">
			{$cf_form}
		</table>
	</div>
	<!-- ENDIF -->

	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {$l_captcha}</div>
		<table border="0" width="100%" class="table table-form">
			<tr>
				<td width="20%">{$l_enter_captcha}</td>
				<td width="80%"><img src="{$site_url}/visual.php" alt="captcha" /><div style="margin-top:3px"><input type="text" name="visual" required="required" /></div></td>
			</tr>
		</table>
	</div>

	<div style="text-align:right; padding:10px">
	<!-- BEGINIF $allow_remove -->
	<button type="button" class="btn btn-danger" style="margin-right: 30px" onclick="confirm_remove()">{$l_remove}</button>
	<!-- ENDIF -->
	<button type="submit" class="btn btn-primary">{$l_submit}</button></div>
</form>

<!-- BEGINSECTION cf_list -->
	<tr><td width="20%">{$cf_title}</td><td width="80%">{$field} {$cf_help}</td></tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION cf_list_div -->
	<tr><td colspan="2"><h3 class="cf_div">{$field}</h3></td></tr>
<!-- ENDSECTION -->

<script>
function confirm_remove ()
{
	var c = confirm ('{$l_remove_confirm}');
	if (c) document.location = "{$site_url}/add.php?cmd=del&item_id={$item_id}&AXSRF_token={$axsrf}";
}

// showOnlyOptionsSimilarToText
// (c) Larry Battle <bateru.com/news>
var filterSelect = function (selectionEl, str) {
	str = str.toLowerCase();
	var $el = $(selectionEl);
	if (!$el.data("options")) $el.data("options", $el.find("option").clone());
	var newOptions = $el.data("options").filter(function () {
			var text = $(this).text();
			text = text.toLowerCase();
			return text.match(str);
		});
	$el.empty().append(newOptions);
};

function display_search (n)
{
	var f = 'select[name=category_'+n+']';
	var s = '#SearchBox_'+n;
	var d = '#SearchDiv_'+n;

	$('.SearchDiv').hide();
	$('select[name^=category]').attr('size','1');

	$(d).show();
	$(s).on("keyup", function () { var userInput = $(s).val(); filterSelect($(f), userInput); }).focus();
	$(f).attr('size','5');
	$(f).on('change',function(){ $(d).hide(); $(f).attr('size','1'); $(f).trigger('blur'); });
}

$('#item_backlink_url').blur (function () { isBacklinkOk = validateByAjax ('#item_backlink_url', '{$site_url}/ajax.php?cmd=backlink', '#backlink_ok') });
</script>