<!-- BEGINSECTION ezform_required -->
 <span style="color:#f00"><b>&bull;</b></span>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_required_js -->
 required="required"
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_help -->
 <span class="glyphicon glyphicon-info-sign help tips" title="{$help}"></span>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_viewfile -->
 {$value} - {$size} bytes [ <a href="{$view}" target="_blank">view file</a> ] [ <a href="{$remove}">remove</a> ]<br />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_viewimg -->
 {$value} - {$size} bytes [ <a href="{$view}" target="_blank">view image</a> ] [ <a href="{$remove}">remove</a> ]<br />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_viewthumb -->
 <img src="{$thumb}" alt="{$value}" /><br />
 {$value} - {$size} bytes [ <a href="{$view}" target="_blank">view image</a> ] [ <a href="{$remove}">remove</a> ]<br />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_head -->
<script type="text/javascript">
<!--
function confirm_remove ()
{
	c = window.confirm ("Do you wish to remove this item?\nThis process can not be undone!");
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
<h2>{$back} {$title}</h2>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_echo -->
{$value}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_url -->
<a href="{$value}">{$value}</a>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_static -->
<input type="hidden" name="{$field}" value="{$value}" />
{$value}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_disabled -->
 <input type="text" name="{$field}" value="{$value}" disabled="disabled" {$required_js} size="50" /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_divider -->
<div style="background:url('../skins/_admin/images/menu-bar-right-arrow.png') 0 10px no-repeat #999; color:#fff">{$title}</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_varchar -->
<input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_permalink -->
{$permalink_path}/<input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_email -->
<input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} /> [ <a href="admin_mail.php?mode=mail&amp;email={$value}">send email</a> ]
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_password -->
<input type="password" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} class="password" onkeyup="passwordStrength(this.value)" /> {$required} {$help}<br />
<div id="passwordStrength" class="strength0">Password not entered</div>
<div style="clear:both"></div>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_checkbox -->
{$checkbox}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_date -->
{$date_select}
<a style="cursor:pointer"><span class="glyphicon glyphicon-calendar" id="date_{$field}" class="calendar" data-date-format="yyyy-mm-dd" data-date="{$value}"></span></a>
<script type="text/javascript">$("#date_{$field}").dateinput({selectors:true,value:new Date({$js_value}),change:function(event,date){update_date_form('{$field}',date);}});</script>
{$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_time -->
{$time_select}<br />{$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_text -->
<textarea name="{$field}" style="width:{$x}px; height:{$y}px" {$required_js}>{$value}</textarea> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_code -->
{$code_area} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_wysiwyg -->
{$rte_area} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_select -->
{$data_select} {$edit_opt} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_radioh -->
{$data_radio} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_radiov -->
{$data_radio} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_multi -->
 <tr>
  <td valign="top">{$title}</td>
  <td>{$data_multi} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_file -->
{$viewfile} <input type="file" name="{$field}" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_img -->
{$viewimg} <input type="file" name="{$field}" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_thumb -->
{$viewthumb} <input type="file" name="{$field}" {$required_js} />{$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_img_resize -->
{$viewimg} <input type="file" name="{$field}" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_edit_opt -->
<a href="javascript:open_edit('{$editopt}')"><img src="../skins/_admin/images/editopt.gif" alt="edit options" /></a>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_remove -->
<a href="#" onclick="confirm_remove()">Remove this item</a>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_foot -->
 <button type="submit">Submit</button> <button type="reset">Reset</button>
</form>
<span style="color:#f00"><b>&bull;</b></span> Required
<!-- ENDSECTION -->