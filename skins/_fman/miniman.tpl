<script language="JavaScript" type="text/javascript">
<!--
function DoTool (filename) {
	c = window.confirm ("Do you wish to select '"+filename+"'?");
	if (!c) return false;
	document.location = "{$script}?fn="+filename;
}

//-->
</script>

<h1><img src="./../../skins/_fman/images/fman.png" style="width:64px" border="0" alt="fman" /> MiniMan</h1>
<p><b>Directory of {$abs_dir}</b></p>
    <table border="0" cellpadding="5" cellspacing="1" width="100%" id="fman_list" class="table table-condensed">
      <tr><td width="45%" class="fman_list_head">Name</td>
          <td width="12%" class="fman_list_head" align="right">Size</td>
          <td width="20%" class="fman_list_head">Date Modified</td>
          <td class="fman_list_head">View</td></tr>

      <!-- BEGINBLOCK fileman_item -->
      <tr><td><a href="#" onclick="DoTool('{$name}')">{$name}</a></td>
          <td align="right">{$size}</td>
          <td><font size="1">{$mtime}</font></td>
          <td><a href="{$path}"><img src="../../skins/_fman/images/view.gif" border="0" alt="view" /></a></td></tr>
      <!-- ENDBLOCK -->

    </table>