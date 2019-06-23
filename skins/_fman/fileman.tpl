<script>
function confirm_delete (filename)
{
	c = window.confirm ("Do you wish to delete '"+filename+"'?\nThis process can not be undone!");
	if (!c) return false;
	document.location = "fileman.php?cmd=del&fn="+filename+"&chdir={$cur_path}";
}

function confirm_rename (filename)
{
	newname = filename;
	c = window.prompt ("Enter a new name for '"+filename+"'?", newname);
	if (!c) return false;
	document.location = "fileman.php?cmd=ren&fn="+filename+"&newfn="+c+"&chdir={$cur_path}";
}

function confirm_copy (filename)
{
	newname = 'copy_of_'+filename;
	c = window.confirm ("Copy '"+filename+"' to '"+ newname +"'?\nYou can rename later.");
	if (!c) return false;
	document.location = "fileman.php?cmd=copy&fn="+filename+"&chdir={$cur_path}";
}

function confirm_mkdir ()
{
	c = window.prompt ("Enter a new directory name", "new folder");
	if (!c) return false;
	document.location = "fileman.php?cmd=mkdir&fn="+c+"&chdir={$cur_path}";
}

function confirm_new ()
{
	c = window.prompt ("Enter a new file name", "newfile.ext");
	if (!c) return false;
	document.location = "fileman.php?cmd=new&fn="+c+"&chdir={$cur_path}";
}

function confirm_rmdir (filename)
{
	c = window.confirm ("Do you wish to delete '"+filename+"'?\nThis process can not be undone!");
	if (!c) return false;
	document.location = "fileman.php?cmd=rmdir&fn="+filename+"&chdir={$cur_path}";
}

function confirm_rendir (filename)
{
	newname = filename;
	c = window.prompt ("Enter a new name for '"+filename+"'?", newname);
	if (!c) return false;
	document.location = "fileman.php?cmd=rendir&fn="+filename+"&newfn="+c+"&chdir={$cur_path}";
}

function confirm_move (filename)
{
	window.open("tree.php?cmd=move&chdir={$cur_path}&fn="+filename, "tree",
	            "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=350,height=450");
}

function browse ()
{
	window.open("tree.php?cmd=browse&chdir={$cur_path}&fn=", "tree",
	            "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=350,height=450");
}
</script>

<h1><img src="./../../skins/_fman/images/fman.png" style="width:64px" border="0" alt="fman" /> File Manager</h1>
<p><b>Directory of {$where}</b></p>
<table id="fman_list" class="table table-condensed table-bordered">
 <tr>
  <th width="45%">Name</th>
  <th width="12%" style="text-align:right">Size</th>
  <th width="20%" style="text-align:right">Date Modified</th>
  <th width="25%" style="text-align:right">Tools</th>
 </tr>

 <!-- BEGINBLOCK fileman_item -->
 <tr>
  <td>{$name}</td>
  <td style="text-align:right">{$size}</td>
  <td style="text-align:right"><font size="1">{$mtime}</font></td>
  <td style="text-align:right">{$tools}</td>
 </tr>
 <!-- ENDBLOCK -->

 <tr>
  <td colspan="4" align="center">{$num_files} file(s) and {$num_dirs} directory(s) in {$ttl_size} bytes</td>
 </tr>
 <tr>
  <td colspan="4" align="center" class="small">
   <img src="./../../skins/_fman/images/view.gif" border="0" alt="View" /> view file &nbsp;&nbsp;
   <img src="./../../skins/_fman/images/ren.gif" border="0" alt="Rename" /> rename file/folder &nbsp;&nbsp;
   <img src="./../../skins/_fman/images/copy.gif" border="0" alt="Copy" /> copy file &nbsp;&nbsp;
   <img src="./../../skins/_fman/images/del.png" border="0" alt="Delete" /> delete file/folder &nbsp;&nbsp;
   <img src="./../../skins/_fman/images/edit.gif" border="0" alt="Edit" /> edit html/text file &nbsp;&nbsp;
   <img src="./../../skins/_fman/images/move.gif" border="0" alt="Move" /> move file
  </td>
 </tr>
</table>

<hr />
<div id="fman_tool">
 <div class="fman_tool_head">Tools</div>
 <div class="fman_tool_content">
   [ <a href="#" onClick="browse()">Dir Tree</a> ]
   [ <a href="upload.php?chdir={$cur_path}">Upload</a> ]
   [ <a href="#" onClick="confirm_mkdir ()">New Folder</a> ]
   [ <a href="#" onClick="confirm_new ()">New File</a> ]
 </div>
 <div class="fman_tool_content">
   Total space: {$max_space} bytes | Used {$used_space} bytes | Free: {$free_space} bytes
 </div>
</div>