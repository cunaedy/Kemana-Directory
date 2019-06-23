<!-- BEGINSECTION qadmin_required -->
 <span style="color:#f00"><b>&bull;</b></span>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_required_js -->
 required="required"
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_help -->
 <span class="glyphicon glyphicon-info-sign help tips" title="{$help}"></span>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_viewfile -->
  <div><span class="glyphicon glyphicon-file"></span> {$value} - {$size} bytes <a href="{$view}" target="_blank"><span class="glyphicon glyphicon-zoom-in"></span> View File</a> <a href="{$remove}"><span class="glyphicon glyphicon-remove"></span> Remove</a></div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_viewimg -->
 <div><span class="glyphicon glyphicon-file"></span> {$value} - {$size} bytes <a href="{$view}" class="lightbox" target="_blank"><span class="glyphicon glyphicon-zoom-in"></span> View Image</a> <a href="{$remove}"><span class="glyphicon glyphicon-remove"></span> Remove</a></div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_viewthumb -->
 <img src="{$thumb}" alt="{$value}" />
 <div><span class="glyphicon glyphicon-file"></span> {$value} - {$size} bytes <a href="{$view}" class="lightbox" target="_blank"><span class="glyphicon glyphicon-zoom-in"></span> View Image</a> <a href="{$remove}"><span class="glyphicon glyphicon-remove"></span> Remove</a></div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_upload -->
<input type="file" name="{$field}" {$required_js} />
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_savenew_button -->
<button type="button" class="btn btn-default" onclick="document.forms['qadmin_form'].qadmin_savenew.value=1;document.forms['qadmin_form'].submit()">Save &amp; New</button>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_head -->
<script type="text/javascript">
<!--
function confirm_remove ()
{
	c = window.confirm ("Do you wish to remove this item?\nThis process can not be un-done!");
	if (!c) return false;
	document.location = "{$action}qadmin_cmd=remove_item&primary_val={$primary_val}";
}
-->
</script>

<form method="post" name="qadmin_form" id="qadmin_form" action="{$action}" enctype="{$enctype}">
	<input type="hidden" name="qadmin_cmd" value="{$cmd}" />
	<input type="hidden" name="qadmin_process" value="1" />
	<input type="hidden" name="qadmin_savenew" value="0" />
	<input type="hidden" name="primary_key" value="{$primary_key}" />
	<input type="hidden" name="primary_val" value="{$primary_val}" />
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_head_inner -->
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-pencil"></span> {$title}</div>
	<div class="panel-body">
		<ul id="qadmin_tab" class="nav nav-pills">
			<li>{$back}</li>
			<li class="active"><a href="#1" data-toggle="tab"><span class="glyphicon glyphicon-home"></span>  Main</a></li>
			{$tab_list}
		</ul>
	</div>
	<div class="tab-content">
		<div class="tab-pane active" id="1">
			<table class="table table-form" id="qadmin_tbl_1">
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_echo -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-book"></span> {$title}</td><td style="width:75%" id="{$thisid}">{$prefix} {$value} {$suffix} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_url -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-link"></span> {$title}</td><td style="width:75%" id="{$thisid}">{$prefix} <a href="{$value}" target="preview">{$value}</a> {$suffix} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_disabled -->
		<tr>
			<td style="width:25%">{$title}</td><td style="width:75%">{$prefix} <input type="text" name="{$field}" value="{$value}" disabled="disabled" {$required_js} size="50" /> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_static -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-book"></span> {$title}</td><td style="width:75%" id="{$thisid}" >{$prefix} <input type="hidden" name="{$field}" value="{$value}" />{$display_value} {$suffix}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_divider -->
			</table>
		</div>

		<div class="tab-pane" id="{$tabindex}">
			<table class="table table-form" id="qadmin_tbl_{$tabindex}">
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_varchar -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-font"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} <input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} /> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_permalink -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-link"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}" >{$prefix} {$permalink_path}<input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} style="width:50%;max-width:50%;min-width:50%"/> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_email -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-envelope"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} <input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} /> <a href="admin_mail.php?mode=mail&amp;email={$value}"><span class="glyphicon glyphicon-send" style="color:inherit" title="send email"></span> </a> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_password -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-lock"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} <input type="password" name="{$field}" id="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} onkeyup="passwordStrength('{$field}', this.value)" /> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_checkbox -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-ok-circle"></span> {$title}</td><td style="width:75%" id="{$thisid}">{$prefix} {$checkbox} {$suffix} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_date -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-calendar"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$date_select}
			<a style="cursor:pointer"><span class="glyphicon glyphicon-calendar calendar" id="date_{$field}" data-date-format="yyyy-mm-dd" data-date="{$value}"></span></a>
			<script type="text/javascript">var cal=$('#date_{$field}').datepicker().on('changeDate',function(ev){update_date_form('{$field}',ev.date);$('#date_{$field}').datepicker('hide')});</script>
			{$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_time -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-time"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$time_select} {$suffix} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_text -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-edit"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} <textarea name="{$field}" style="width:{$x}px; height:{$y}px" {$required_js}>{$value}</textarea> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_code -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-edit"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$code_area} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_wysiwyg -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-edit"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$rte_area} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_select -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-list"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$data_select} {$edit_opt} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_radioh -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-list"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$data_radio} {$edit_opt} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_radiov -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-list"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$data_radio} {$edit_opt} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_multi -->
		<tr>
			<td style="width:25%" valign="top"><span class="glyphicon glyphicon-list"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} <div style="max-height:300px;overflow:auto">{$data_multi}</div> {$edit_opt} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_file -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-upload"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$viewfile} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_img -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-picture"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$viewimg} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_img_set -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-picture"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$viewimg} <input type="file" name="{$field}" {$required_js} /> {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_thumb -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-picture"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$viewthumb} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_img_resize -->
		<tr>
			<td style="width:25%"><span class="glyphicon glyphicon-picture"></span> {$title}</td>
			<td style="width:75%" id="{$thisid}">{$prefix} {$viewimg} {$suffix} {$required} {$help}</td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_edit_opt -->
		<a href="javascript:open_edit('{$editopt}')"><img src="../skins/_admin/images/editopt.gif" alt="edit options" /></a>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_remove -->
		<tr>
			<td style="background-color:#ffe0e0;width:25%"><span class="glyphicon glyphicon-erase"></span> Remove?</td>
			<td style="background-color:#ffe0e0;width:75%"><a href="#" onclick="confirm_remove()" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> Remove entry</a></td>
		</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_foot_inner -->
	</table>
</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_foot -->
		</div>
	</div>
<div class="pull-right" style="margin-bottom:10px">
	<button type="submit" class="btn btn-primary">Save</button> {$savenew_button} <button type="reset" class="btn btn-default">Reset</button>
</div>

<div style="clear:both"></div>

</form>

{$last_update}
<span style="color:#f00"><b>&bull;</b></span> Denotes required information

<script>
var $input = $('#qadmin_form :input[type=text]');
$input.each(function (){s = $(this).attr('size'); if (s < 50) s = s*15; else s = 650; $(this).css ('max-width', s+'px').css ('min-width', '100px')});
$('#qadmin').tab
</script>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search -->
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-search"></span> {$title}</div>
	<div class="panel-body">
		<form method="get" name="qadmin_form" action="{$action}">
		{$hidden_value}
		<input type="hidden" name="qadmin_cmd" value="search" />
		<table class="table table-form" id="qadmin_tbl">
		<tr>
		<td>Keyword <input type="text" name="keyword" value="{$keyword}" size="50" style="max-width:75%;width:75%;min-width:75%"/> {$search_by}</td>
		</tr>
		{$date_form}
		{$filter_form}
		<tr>
		<td><button type="submit" class="btn btn-primary">Search</button> <button type="reset" class="btn btn-danger">Reset</button>
		</tr>
		{$switch_list}
		</table>
		</form>
	</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_date_1 -->
 <tr>
  <td>Date {$start_date}</td>
 </tr>
 <tr>
  <td>Operation for keyword &amp; date {$andor}</td>
 </tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_date_2 -->
 <tr>
  <td>Date from {$start_date} to {$end_date}</td>
 </tr>
 <tr>
  <td>Operation for keyword &amp; date {$andor}</td>
 </tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_filter -->
 <tr>
  <td colspan="2">Filter result by {$filter_by}</td>
 </tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_result -->
<table class="table table-bordered">
 <tr>
  <td colspan="{$colspan}" class="adminbg_h">Information Found</td>
 </tr>
 <tr>
  {$block_title}
 </tr>
  {$block_result}
</table>
</div>

<table>
 <tr>
  <td width="80%" align="right">{$pagination}</td>
  <td width="20%" align="right">{$new_item_form}</td>
 </tr>
</table>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_result_none -->
</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_title_row -->
 <th style="text-align:{$align}" nowrap="nowrap">
  <a href="{$sort_asc}"><span class="glyphicon glyphicon-menu-up"></span></a>
  {$title}
  <a href="{$sort_desc}"><span class="glyphicon glyphicon-menu-down"></span></a>
 </td>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_result_row -->
 <td valign="top" style="text-align:{$align}">{$result}</td>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_edit_title -->
 <th valign="top" style="text-align:center">Edit</th>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_search_edit_result -->
 <td valign="top" width="100" align="center">&nbsp;<a href="{$edit_url}" target="{$edit_target}">Edit</a>&nbsp;</td>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_list -->
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-list"></span> {$title}</div>
	<div class="panel-body">
		<form method="get" name="qadmin_form" action="{$action}">
		{$hidden_value}
		<input type="hidden" name="qadmin_cmd" value="list" />
		<table class="table table-form">
		 {$filter_form}
		 <tr>
		  <td align="center"><button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-repeat"></span> Refresh</button></td><td align="center">{$switch_search}</td>
		 </tr>
		</table>
		</form>
	</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_switch_list -->
<tr>
 <td><a href="{$action}qadmin_cmd=list" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> List All</a></td>
</tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_switch_search -->
<a href="{$action}qadmin_cmd=search" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> Search Form</a>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_new_item -->
<form method="get" name="qadmin_form_new" action="{$action}">
{$hidden_value}
<input type="hidden" name="qadmin_cmd" value="new" />
<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> {$add_button_label}</button>
</form>
<!-- ENDSECTION -->

<!-- BEGINSECTION qadmin_send_email -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="en-us" dir="ltr">
<head>
<meta http-equiv="Content-Language" content="en-us" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Form Result</title>
<base href="{$site_url}" />
<style type="text/css">
body { font: 10pt Tahoma, Arial, Helvetica }
h1, h2 { font-family: Tahoma, Arial, Helvetica }
td { font: 10pt Tahoma, Arial, Helvetica }
td.form_title { font-weight: bold; background: #ccc; padding: 3px 10px 3px 5px }
td.form_value { background: #fff; padding: 3px 5px 3px 10px }
</style>
</head>

<body>
<h1>{$form_name}</h1>
{$header}
<table>
{$form_result}
</table>
{$footer}
<hr />
<p>You can also handle this form in <a href="{$site_url}/admin">ACP</a></p>
</body>
</html>
<!-- ENDSECTION -->


<!-- BEGINSECTION qadmin_tab_list_li -->
<li><a href="#{$i}" data-toggle="tab">{$title}</a></li>
<!-- ENDSECTION -->