<form method="post" action="listing_process.php" enctype="multipart/form-data">
<input type="hidden" name="item_id" value="{$item_id}" />
<input type="hidden" name="dir_id" value="{$dir_id}" />

	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> Item Editor</div>
		<div class="panel-body">
			<ul id="tabs" class="nav nav-pills">
				<li class="active"><a href="#1" class="current" data-toggle="tab">General</a></li>
				<li><a href="#2" data-toggle="tab">Custom Fields</a></li>
				<li><a href="#3" data-toggle="tab">Advanced  <span class="badge" id="advbadge"></span></a></li>
				<!-- BEGINIF $change_request -->
				<li><a href="#4" data-toggle="tab">Compare</a></li>
				<!-- ENDIF -->
			</ul>
		</div>

		<div class="tab-content" style="margin-top:5px">
			<div class="tab-pane active" id="1">
				<table border="0" width="100%" class="table table-form" id="result1">
					<tr>
						<td width="20%">Item ID</td>
						<td width="80%">{$item_id} {$change}</td>
					</tr>
					<tr>
						<td width="20%">Directory</td>
						<td width="80%">{$dir_title}</td>
					</tr>
					<tr {$category_1_class}>
						<td>Main Category {$category_1_mark}</td>
						<td>{$category_form} {$l_required_symbol} <a href="listing_dir_select.php?what=cat&amp;qadmin_cmd=list" target="_blank" class="btn btn-default">Edit</a></td>
					</tr>
					<!-- BEGINBLOCK multi_cat -->
					<tr {$category_class}>
						<td>Category {$i}  {$category_mark}</td>
						<td>{$category_form}</td>
					</tr>
					<!-- ENDBLOCK -->
					<!-- BEGINIF $allow_logo_empty -->
					<tr {$item_logo_class}>
						<td style="vertical-align: top">Logo Image {$item_logo_mark}</td>
						<td><img border="0" src="../skins/_common/images/noimage.gif" alt="No thumbnail" /><div><input type="file" name="logo" /></div></td>
					</tr>
					<!-- ENDIF -->
					<!-- BEGINIF $allow_logo_exists -->
					<tr {$item_logo_class}>
						<td style="vertical-align: top">Logo Image {$item_logo_mark}</td>
						<td>{$logo} <div><a href="listing.php?cmd=del_img&amp;item_id={$item_id}&amp;AXSRF_token={$axsrf}"><span class="glyphicon glyphicon-remove"></span> Remove</a></div></td>
					</tr>
					<!-- ENDIF -->
					<tr {$item_title_class}>
						<td>Title {$item_title_mark}</td>
						<td><input type="text" size="50" name="item_title" value="{$item_title}" required="required" /> {$l_required_symbol}</td>
					</tr>
					<!-- BEGINIF $cmd == 'edit' -->
					<tr>
						<td>Preview</td>
						<td><a href="{$preview_url}" target="_blank">Click to view this item in your browser (save first)</a></td>
					</tr>
					<!-- ENDIF -->
					<!-- BEGINIF $require_summary -->
					<tr {$item_summary_class}>
						<td>Summary  {$item_summary_mark}</td>
						<td><input type="text" size="50" name="item_summary" value="{$item_summary}" required="required" /> {$l_required_symbol}</td>
					</tr>
					<!-- ENDIF -->
					<!-- BEGINIF $require_url -->
					<tr {$item_url_class}>
						<td>Target URL  {$item_url_mark}</td>
						<td><input type="text" size="50" name="item_url" value="{$item_url}" required="required" /> {$l_required_symbol}</td>
					</tr>
					<!-- ENDIF -->
					<!-- BEGINIF $allow_url_mask -->
					<tr {$item_url_mask_class}>
						<td>URL Masking  {$item_url_mask_mark}</td>
						<td><input type="text" size="50" name="item_url_mask" value="{$item_url_mask}" /></td>
					</tr>
					<!-- ENDIF -->
					<!-- BEGINIF $duplicate_url -->
					<tr>
						<td></td>
						<td><div class="alert alert-danger alert-dismissible" style="margin:0;padding:3px;text-align:center">
						<b><span class="glyphicon glyphicon-warning-sign"></span> WARNING!</b> {$url_status}</div></td>
					</tr>
					<!-- ENDIF -->
					<tr {$item_details_class}>
						<td style="vertical-align: top">Description {$item_details_mark}</td>
						<td><textarea name="item_details" style="width:70%;height:200px" required="required">{$item_details}</textarea>  {$l_required_symbol}</td>
					</tr>
					<tr>
						<td>Owner ID (User ID)</td>
						<td><input type="text" size="50" name="owner_id" id="owner_id" value="{$owner_id}" class="width-md" /> {$l_required_symbol}</td>
					</tr>
					<tr>
						<td>Owner Email</td>
						<td><input type="text" size="50" name="owner_email" id="owner_email" value="{$owner_email}" class="width-md" /></td>
					</tr>
					<tr>
						<td>Edit Password</td>
						<td><input type="text" size="50" name="owner_passwd" value="{$owner_passwd}" class="width-md" />
						<span class="glyphicon glyphicon-info-sign help tips" title="Fill if you need to change current edit password."></span></td>
					</tr>
					<tr>
						<td>Type</td>
						<td>{$class_select}</td>
					</tr>
					<tr>
						<td>Sponsor Period</td>
						<td>{$sp_date} <a style="cursor:pointer"><span class="glyphicon glyphicon-calendar" id="sp_date" class="calendar" data-date-format="yyyy-mm-dd" data-date=""></span></a></td>
					</tr>
					<!-- BEGINIF $cmd == 'edit' -->
					<tr>
						<td>Send Email on Status Change</td>
						<td>{$email_select}
						<span class="glyphicon glyphicon-info-sign help tips" title="'Manual' allow you to edit the message before sending."></span></td>
					</tr>
					<!-- ENDIF -->
					<!-- BEGINIF $change_request -->
					<tr class="danger">
						<td>Status</td>
						<td>{$status_select}</td>
					</tr>
					<!-- ELSE -->
					<tr>
						<td>Status</td>
						<td>{$status_select}
						<span class="glyphicon glyphicon-info-sign help tips" title="'Waiting User Confirmation' will be removed automatically in 24 hours if user doesn't confirm it."></span></td>
					</tr>
					<!-- ENDIF -->
				</table>
			</div>


			<div class="tab-pane" id="2">
				<table border="0" width="100%" class="table table-form" id="result6">
					<tr>
						<td style="vertical-align:top" width="20%"><p><b>Note</b></p></td>
						<td width="80%"><p>Add more custom fields <a href="listing_cf.php?qadmin_cmd=list">here</a>.</p></td>
					</tr>
					{$cf_form}
				</table>
			</div>


			<div class="tab-pane" id="3">
				<table border="0" width="100%" class="table table-form" id="result4">
					<tr>
						<td>Sort Point</td>
						<td>{$item_sort_point}
						<span class="glyphicon glyphicon-info-sign help tips" title="This is automatically calculated. See more info in Tools &gt; Configurations &gt; Directory Settings."></span></td>
					</tr>
					<tr>
						<td>Visibility</td>
						<td>{$visibility_select}
						<span class="glyphicon glyphicon-info-sign help tips" title="Who can see this item?"></span></td>
					</tr>
					<tr>
						<td>Entry Date</td>
						<td>{$list_date} <a style="cursor:pointer"><span class="glyphicon glyphicon-calendar" id="list_date" class="calendar" data-date-format="yyyy-mm-dd" data-date=""></span></a></td>
					</tr>
					<!-- BEGINIF $require_backlink -->
					<tr>
						<td>Backlink URL</td>
						<td nowrap><input type="text" size="50" class="width-xl" name="item_backlink_url" id="item_backlink_url" value="{$item_backlink_url}" required="required" />
						<span id="backlink_ok"></span> <a href="javascript:verify_backlink()"><span class="glyphicon glyphicon-refresh tips" title="Refresh verification."></span></a>
						<a href="javascript:;" onclick="window.open($('#item_backlink_url').val(), '_blank')"><span class="glyphicon glyphicon-link tips" title="Open the URL in new window."></span></a>
						{$l_required_symbol}
						<span class="glyphicon glyphicon-info-sign help tips" title="Get the backlink code in Settings."></span></td>
					</tr>
					<!-- ENDIF -->
					<tr>
						<td>Permalink</td>
						<td><input type="text" size="50" name="item_permalink" value="{$item_permalink}" /></td>
					</tr>
					<tr>
						<td>Keywords</td>
						<td><input type="text" size="50" name="item_keyword" value="{$item_keyword}" />
						<span class="glyphicon glyphicon-info-sign help tips" title="For search engine optimization."></span></td>
					</tr>
					<tr>
						<td>See Also</td>
						<td><input type="text" size="40" name="see_also" value="{$see_also}" id="see_also" /></td>
					</tr>
					<!-- BEGINIF $cmd == 'edit' -->
					<tr>
						<td>Hits</td><td bgcolor="white">{$stat_hits} hits</td>
					</tr>
					<tr>
						<td>Copy This Item</td>
						<td>
							<label><input type="checkbox" name="copy_item" value="1" onclick="copydiv()" id="copy_check" /> Copy this item</label>
							<div style="margin-left:20px; display:none" id="copydiv">
							<label><input type="checkbox" name="copy_cf" value="1" checked="checked" /> Along with its custom fields</label><br />
							<label><input type="checkbox" name="copy_img" value="1" checked="checked" /> Along with its images</label><br />
							<label><input type="checkbox" name="copy_switch" value="1" checked="checked" /> Switch to copied item</label>
							</div>
						</td>
					</tr>
					<!-- ENDIF -->
				</table>
			</div>

			<!-- BEGINIF $change_request -->
			<div class="tab-pane" id="4">
				<table border="0" width="100%" class="table table-form" id="result6">
					<tr><th>Title</th><th>Original Value</th><th>Requested Value</th></tr>
					{$compare}
					<tr><td colspan="3">PS: Unchanged values not listed</td></tr>
				</table>
			</div>
			<!-- ENDIF -->


		</div>
	</div>
	<div class="clearfix">
		<div class="pull-left">
			<div class="btn-group">
				<button type="button" class="btn btn-danger" onclick="confirm_delete(1)">Delete &amp; Send Email</button>
				<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a onclick="confirm_delete(0)">Delete &amp; Suppress Email</a></li>
				</ul>
			</div>
		</div>
		<div class="pull-right"><button type="submit" class="btn btn-primary">Save Listing Information</button></div>
	</div>
</form>

<script>
$(function(){
	$('#tabs').tab
	$("#see_also").tokenInput("admin_ajax.php?cmd=item", { queryParam:"query", preventDuplicates:true, prePopulate:{$see_also_preset}});
	$('#list_date').datepicker().on('changeDate',function(ev){update_date_form('item_date',ev.date);
	$('#list_date').datepicker('hide')});
	$('#sp_date').datepicker().on('changeDate',function(ev){update_date_form('item_valid_date',ev.date);
	$('#sp_date').datepicker('hide')});
	$('#owner_id').autocomplete({serviceUrl: 'admin_ajax.php?cmd=user',onSelect: function (suggestion) {$('#owner_id').val(suggestion.value)}})
	$('#owner_email').autocomplete({serviceUrl: 'admin_ajax.php?cmd=email',onSelect: function (suggestion) {$('#owner_email').val(suggestion.value)}})
	<!-- BEGINIF $require_backlink -->
	verify_backlink ();
	<!-- ENDIF -->
})

function copydiv ()
{
	c = $('#copy_check:checked').val();
	cd = $('#copydiv');
	if (c != null) cd.css('display','block'); else cd.css('display','none');
}

function confirm_delete (email)
{
	c = confirm ('Are you sure to remove this item? This process can not be undone!');
	if (!c) return false;
	document.location = "listing.php?cmd=del_item&email="+email+"&item_id={$item_id}&AXSRF_token={$axsrf}";
}

function verify_backlink ()
{
	isBacklinkOk = validateByAjax ('#item_backlink_url', '{$site_url}/ajax.php?cmd=backlink', '#backlink_ok');
	if (!isBacklinkOk) $('#advbadge').text(1); else $('#advbadge').text('');
}

var isEmailOk = false;

<!-- BEGINIF $require_backlink -->
var isBacklinkOk;
$('#item_backlink_url').blur (function () { verify_backlink () });
<!-- ENDIF -->
</script>

<!-- BEGINSECTION cf_list -->
	<tr {$cf_class}><td>{$cf_title} {$cf_mark} {$cf_help}</span>
	</td><td>{$cf_field}</td></tr>
<!-- ENDSECTION -->