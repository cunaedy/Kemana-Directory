<script>
function AddImage(filename) {
	var win = (window.opener?window.opener:window.parent);
	win.document.getElementById('{$field}').value = '{$image_url}/'+filename;
	win.tinyMCE.activeEditor.windowManager.close();
	window.close();
}


function confirm_delete (filename)
{
	c = window.confirm ("Do you wish to delete '"+filename+"'?\nThis process can not be undone!");
	if (!c) return;
	document.location = "imagelib.php?cmd=del&fn="+filename;
}
</script>

<!-- BEGINIF $tpl_mode == 'thumb' -->
<h1><span class="glyphicon glyphicon-picture"></span> Image Library</h1>
<h4>View Mode <a href="imagelib.php?view=thumb&amp;field={$field}"><span class="glyphicon glyphicon-th"></span></a> <a href="imagelib.php?view=list&amp;field={$field}"><span class="glyphicon glyphicon-th-list"></span></a></h4>
<p style="background: #369;padding:2px;color:#fff"><b>Directory of {$real_dir}</b></p>
<div class="row imagelib_wrapper">
<!-- BEGINBLOCK fileman_item -->
<div class="col-xs-4 imagelib_item">
	<div class="imagelib_div">
		<a href="imagelib.php?cmd=preview&amp;field={$field}&amp;fn={$name}"><img src="{$thumb}" alt="{$name}" /></a>
		<div class="imagelib_info">
			<span class="glyphicon glyphicon-time"></span> {$mtime}
			<a href="javascript:confirm_delete ('{$name}');"><span class="glyphicon glyphicon-remove text-danger"></span></a>
		</div>
	</div>

	<div class="text-center">
		<a href="javascript:AddImage('{$name}');">{$name}</a>

		<small class="small">{$size}</small></div>
</div>
<!-- ENDBLOCK -->
</div>
{$upload_form}
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list' -->
<h1><span class="glyphicon glyphicon-picture"></span> Image Library</h1>
<h4>View Mode <a href="imagelib.php?view=thumb&amp;field={$field}"><span class="glyphicon glyphicon-th"></span></a> <a href="imagelib.php?view=list&amp;field={$field}"><span class="glyphicon glyphicon-th-list"></span></a></h4>
<p><b>Directory of {$real_dir}</b></p>
<table class="fman_list table table-condensed">
	<tr>
		<td width="45%" class="fman_list_head">Name</td>
		<td width="12%" class="fman_list_head" align="right">Size</td>
		<td width="20%" class="fman_list_head">Date Modified</td>
		<td width="25%" class="fman_list_head">Tools</td>
	</tr>
	<!-- BEGINBLOCK fileman_item -->
	<tr>
		<td><a href="#" onclick="AddImage('{$name}')">{$name}</a></td>
		<td align="right">{$size}</td>
		<td><small>{$mtime}</small></td>
		<td>
			<a href="imagelib.php?cmd=preview&amp;field={$field}&amp;fn={$name}"><span class="glyphicon glyphicon-search"></span></a>
			<a href="javascript:confirm_delete ('{$name}');"><span class="glyphicon glyphicon-remove text-danger"></span></a>
		</td>
	</tr>
	<!-- ENDBLOCK -->
</table>
{$upload_form}
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'preview' -->
<div class="panel panel-default">
	<div class="panel-body"  style="text-align:center">
		<img src="{$image_url}/{$fn}" alt="preview"/>
		<h3>{$fn}</h3>
		<p>
			<a href="#" onclick="AddImage('{$fn}')" class="btn btn-primary" role="button">Use This Image</a>
			<a href="javascript:window.history.go(-1)" class="btn btn-default" role="button">Cancel</a></p>
	</div>
	<table class="table">
	<tr><td>Name</td><td>{$image_url}/{$fn}</td></tr>
	<tr><td>Dimension</td><td>{$f_dimension}</td></tr>
	<tr><td>Size</td><td>{$f_size}</td></tr>
	<tr><td>Date</td><td>{$f_mtime}</td></tr>
	</table>
</div>
<!-- ENDIF -->

<!-- BEGINSECTION upload -->
<br />
<h4>Upload</h4>
<form method="post" enctype="multipart/form-data" action="imagelib.php">
<input type="hidden" name="cmd" value="upload" />
	<table class="fman_list table">
	  <tr><td colspan="2">{$num_files} file(s) in {$ttl_size} bytes</td></tr>
	  <tr><td>Upload file to current directory:
		   <input type="file" name="upload" size="20" style="display:inline" /><button type="submit">Submit</button><br />
		   <label><input type="checkbox" name="compress" /> Compress (JPEG, GIF &amp; PNG)</label>
		   </td></tr>
	</table>
</form>
<!-- ENDSECTION -->