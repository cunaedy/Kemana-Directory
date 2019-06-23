<script type="text/javascript">
function update_opt(idx)
{
	document.location = "edit_opt.php?fid={$fid}&show={$show}&sublist="+idx;
}
</script>

<form method="post" action="edit_opt.php">
<input type="hidden" name="fid" value="{$fid}" />
<input type="hidden" name="show" value="{$show}" />
<input type="hidden" name="sublist" value="{$sublist}" />
<input type="hidden" name="cmd" value="save" />

<table border="0" align="center">
<!-- BEGINIF $sublist_avail -->
 <tr><td colspan="2" align="center">{$sublist_select}</td></tr>
<!-- ENDIF -->
 <tr><td></td><td>Remove</td></tr>
 
<!-- BEGINBLOCK list -->
 <tr>
  <td><input type="text" name="value_{$idx}" value="{$option_value}" size="40" tabindex="{$i}" /></td>
  <td align="center"><a href="edit_opt.php?fid={$fid}&amp;sublist={$sublist}&amp;cmd=del&amp;show={$show}&amp;oid={$idx}&amp;AXSRF_token={$axsrf}" style="font-weight:bold; color:#f00">x</a></td>
 </tr>
<!-- ENDBLOCK -->
 <tr><td colspan="2" align="center">Show {$show_select} fields</td></tr>
</table>
<center><button type="submit">Save</button>
<button type="reset">Reset</button></center>
</form>

<hr />
<b>Note:</b>
<ul class="list_1">
 <li>Closing this window, will refresh your form.</li>
 <li>Save before closing this window.</li>
</ul>