<!-- BEGINIF $module_engine -->
<!-- ELSE -->
<div class="warning">
Warning! Module Engine is disabled in Primary Config. Menu Manager needs qMenu module to work.
</div>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list' -->
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Menu Manager</div>
	<div class="panel-body">Use Menu Manager to create your own menu. Later you can add your menu on your site using several method: manually, by module, or included in pages.</div>
	<table class="table table-bordered">
		<tr>
			<th width="15%">Menu ID</td>
			<th width="65%">Title</td>
			<th width="20%">Tools</td>
		</tr>
		<!-- BEGINBLOCK list -->
		<tr>
			<td>{$menu_id}</td><td>{$menu_title}</td>
			<td><a href="menu_set.php?id={$idx}"><span class="glyphicon glyphicon-list-alt tips" title="Edit properties"></span></a> &nbsp;
				<a href="menu_man.php?cmd=design&amp;midx={$idx}"><span class="glyphicon glyphicon-pencil tips" title="Reorder menu"></span></a> &nbsp;
				<a href="menu_man.php?cmd=reorder3&amp;midx={$idx}"><span class="glyphicon glyphicon-refresh tips" title="Refresh cache"></span></a> &nbsp;
				<a href="menu_man.php?cmd=guide&amp;midx={$idx}"><span class="glyphicon glyphicon-info-sign tips" title="Help on integration"></span></a> &nbsp;
				<a href="javascript:confirm_remove({$idx})"><span class="glyphicon glyphicon-remove text-danger tips" title="Remove menu"></span></a>
			</td>
		</tr>
		<!-- ENDBLOCK -->
	</table>
</div>

<p><a href="menu_set.php?qadmin_cmd=new"><span class="glyphicon glyphicon-plus"></span> Add New Menu</a></p>
<p><a href="menu_man.php?cmd=reorder_all"><span class="glyphicon glyphicon-refresh"></span> Refresh Cache for All Menu</a></p>

<script>
function confirm_remove (id)
{
	c = window.confirm ("Do you wish to remove this menu and all its items?\nThis process can not be un-done!");
	if (!c) return;
	document.location = "menu_man.php?cmd=del_menu&midx="+id;
}
</script>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'preview' -->
<h2>Preview</h2>
<div style="margin-bottom:10px">{$menu_cache}<div style="clear:both"></div></div>
<h3>Important!</h3>
<ul class="list_1">
	<li>To add this menu to your web site, please see <a href="menu_man.php?cmd=guide&amp;midx={$midx}">the guide here</a>.</li>
</ul>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'design_content' -->
<!-- ENDIF -->


<!-- BEGINIF $tpl_mode == 'design' -->
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Menu Designer</div>
	<div class="panel-body">Here you can design your menu. To add new item, simply click [New Item].</div>
	<table class="table table-bordered table-hover">
		<tr>
			<th colspan="3"><a href="menu_man.php"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></th>
		</tr>
		<!-- BEGINBLOCK itemlist -->
		<tr>
			<td width="70%">
				<span style="padding-left:{$pad}px"></span>
				{$dn} {$up}	{$title} {$show_url}
			</td>
			<td width="30%">
				{$edit_button} &nbsp; {$add_child_button} &nbsp; {$del_button}

			</td>
		</tr>
		<!-- ENDBLOCK -->
		<tr>
			<td colspan="3">{$add_button}</td>
		</tr>
	</table>
</div>

{$preview}

<script>
function confirm_remove (id, ref)
{
	c = window.confirm ("Do you wish to remove this menu and all its sub menu?\nThis process can not be un-done!");
	if (!c) return;
	document.location = "menu_man.php?cmd=del_item&midx={$midx}&iidx="+id+"&ref_idx="+ref;
}
</script>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'guide' -->
<div class="panel panel-default">
	<div class="panel-heading"><a href="menu_man.php"><span class="glyphicon glyphicon-chevron-left"></span></a> Legends</h2></div>
	<table width="100%" class="table table-bordered">
		<tr><th width="12%" valign="top">Icons</th>
			<th>Functions</th></tr>
		<tr><td width="12%" valign="top"><span class="glyphicon glyphicon-list-alt icon tips" title="Edit properties"></span> Properties</td>
			<td>Use this to edit menu properties, such as id, title, style, etc.</td></tr>
		<tr><td width="12%" valign="top"><span class="glyphicon glyphicon-pencil icon tips" title="Reorder menu"></span> Design</td>
			<td>Use this to design your menu: adding items, removing items, rearrange, etc.</td></tr>
		<tr><td width="12%" valign="top"><span class="glyphicon glyphicon-refresh icon tips" title="Refresh cache"></span> Refresh</td>
			<td>Use this to refresh menu cache. You should do this if the menu doesn't appear correctly, or if you are changing
			'Enable Search Engine Friendly URLS' value in Primary Config.</td></tr>
		<tr><td width="12%" valign="top"><span class="glyphicon glyphicon-remove text-danger icon tips" title="Remove menu"></span> Delete</td>
			<td>Use this to remove a menu and its items.</td></tr>
	</table>
	<div class="panel-heading"><a href="menu_man.php"><span class="glyphicon glyphicon-chevron-left"></span></a> Integration Guide</div>
	<div class="panel-body">
		<p>There are three ways to integrate your menu to qEngine design:</p>
		<ul>
			<li>Module method</li>
			<li>Page method</li>
			<li>Manual method</li>
		</ul>

		<h3>Module Method</h3>
		<p>This is the most practical way to add a menu to your site.</p>
		<ul>
			<li>Open Modules &gt; Layout.</li>
			<li>At Top 1 position, pick qMenu module. Click Save Changes</li>
			<li>The page will refresh, now click Edit for qMenu. Type: <span class="code">menu={$mymenu}</span> as Configuration. Save. Done.</li>
		</ul>
		<p>Notes: in <b>default</b> skin, qE already has two menu: main_menu for Top 1 & footer_menu for Bottom 1.</p>

		<h3>Page Method</h3>
		<p>If you need to put your menu inside a page, this method is the easiest.</p>
		<ul>
			<li>Edit a page using: Contents &gt; Manage Contents. Pick any page.</li>
			<li>Inside the body, type: <span class="code">{{{qemod:qmenu:{$mymenu}}</span></li>
			<li>Save. Done.</li>
		</ul>

		<h3>Manual Method</h3>
		<p>If you need total a freedom to place &amp; display your menu, you should use this method. It's actually very easy!</p>
		<p>This method requires you to understand: FTP &amp; html tags.</p>
		<ol>
			<li>With any FTP program, download the <b>[any file].tpl</b> file from /skins/default folder.</li>
			<li>Open the file with your text editor.</li>
			<li>Place this tag: <span class="code">{{{qemod:qmenu:{$mymenu}}</span>. Save. Upload. Done!</li>
		</ol>

		<h3>CSS ID</h3>
		<p>The HTML tag for menu is: <span class="code">&lt;ul id="qmenu_{$mymenu}" class="<i>[menu_class]</i>"&gt;</span>.</p>
	</div>
</div>
<!-- ENDIF -->