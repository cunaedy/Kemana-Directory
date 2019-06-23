<h1>File Manager</h1>
<p><b>Directory of {$where}</b></p>

<form enctype="multipart/form-data" action="upload_process.php" method="post">
<input type="hidden" name="chdir" value="{$chdir}" />
<input type="hidden" name="n" value="{$n}" />
<table border="0" cellpadding="5" cellspacing="1" width="100%" id="fman_list" class="table table-condensed">
 <tr>
  <td colspan="2" class="fman_list_head">Upload Files</td>
 </tr>
 <!-- BEGINBLOCK upload_item -->
 <tr>
  <td bgcolor="#EEEEEE" width="5%" class="admin_head" align="center">{$n}</td>
  <td bgcolor="#EEEEEE"><input type="file" name="userfile_{$n}" size="50" /></td>
 </tr>
 <!-- ENDBLOCK -->

 <tr>
  <td bgcolor="#EEEEEE" colspan="2" align="center"><input type="submit" value="Upload Files" />
 </tr>
</table>
</form>

<hr class="divider" />

<form action="upload.php" method="get">
<input type="hidden" name="chdir" value="{$chdir}" />
<div id="fman_tool">
 <div class="fman_tool_head">Tools</div>
 <div class="fman_tool_content">
  <a href="fileman.php?chdir={$chdir}">Return to FMan</a> |
  Show {$n_select} Fields <input type="submit" value="Show" /></div>
 <div class="fman_tool_content">Total space: {$max_space} bytes | Used {$used_space} bytes | Free: {$free_space} bytes</div>
</div>
</form>