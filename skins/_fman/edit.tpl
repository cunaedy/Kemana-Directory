<form id="edit" action="edit.php" method="post">
<input type="hidden" name="chdir" value="{$chdir}" />
<input type="hidden" name="fn" value="{$fn}" />
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> Editing: {$fn}</div>
	<table class="table">
		<!-- BEGINIF $readonly -->
		<tr>
			<td style="background: #f00" align="center" colspan="2">This file is <b>READ ONLY</b>. To make it write-able, please CHMOD it to 777 using an FTP program.</td>
		</tr>
		<!-- ENDIF -->
		<tr>
			<td colspan="2"><textarea name="editArea" id="editArea" style="width: 100%; height: 470px">{$html}</textarea></td>
		</tr>
		<tr>
			<td class="adminbg_c"><label><input type="checkbox" name="wrap" id="wrap" onclick="toggleWordWrap(this)"  checked="checked"/> Word Wrap</label></td>
			<td class="adminbg_c"><input type="submit" name="save" value="Save File" class="btn btn-primary" /></td>
		</tr>
	</table>
</div>
</form>

<script type="text/javascript" src="{$site_url}/misc/editarea/edit_area_full.js"></script>
<script type="text/javascript">
<!-- BEGINIF $editArea -->
// Edit Area is (c)copyright Christophe Dolivet -- http://www.cdolivet.com/editarea
editAreaLoader.init({
	id: 'editArea',	// id of the textarea to transform
	start_highlight: true,	// if start with highlight
	allow_toggle: true,
	word_wrap: true,
	font_family: "Consolas,monospace",
	language: "en",
	syntax: "{$syntax}",
	is_editable: {$is_editable}
});
<!-- ENDIF -->
function toggleWordWrap(elm)
{
	if (elm.checked)
		$('#editArea').attr('wrap','soft');
	else
		$('#editArea').attr('wrap','off');
}

function confirm_exit ()
{
	c = window.confirm ("Exit Now?\nAny changes won't be saved.");
	if (!c) return false;
}
</script>

