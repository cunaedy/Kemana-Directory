<!-- BEGINIF $module_engine -->
<!-- ELSE -->
<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> Module Engine is disabled in Primary Config. All modules will be disabled!</div>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list' -->
<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Module Library</div>
	<table border="0" width="100%" class="table">
		<tr>
			<td class="adminbg_c">Name</td>
			<td class="adminbg_c" align="center">Version</td>
			<td class="adminbg_c">Description</td>
			<td class="adminbg_c" align="center">Enabled</td>
			<td class="adminbg_c" align="center">Configure</td>
			<td class="adminbg_c" align="center">Document</td>
			<td class="adminbg_c" align="center">Uninstall</td>
		</tr>

	<!-- BEGINBLOCK list -->
	<tr>
		<td align="center" valign="top">{$icon}<br />{$mod_name}</td>
		<td align="center" valign="top">{$mod_version}</td>
		<td valign="top"><div>{$mod_desc}</div>
						<p><span class="glyphicon glyphicon-user"></span> Author: {$mod_author}
						<span class="glyphicon glyphicon-info-sign"></span> License: {$mod_license}<br />
						<span class="glyphicon glyphicon-copyright-mark"></span> {$mod_copyright}<br />
						<span class="glyphicon glyphicon-link"></span> <a href="http://{$mod_authorUrl}">{$mod_authorUrl}</a>
						<span class="glyphicon glyphicon-envelope"></span> <a href="mailto:{$mod_authorEmail}">{$mod_authorEmail}</a></p></td>
		<td align="center">{$mod_enabled}</td>
		<td align="center">
		<p><a href="modplug_config.php?what=module&amp;mod_id={$mod_id}" class="module_setup">
		<span class="glyphicon glyphicon-wrench icon-l"></span></p></td>

		<td align="center"><p><a href="modplug_doku.php?what=module&amp;mod_id={$mod_id}" class="module_setup">
		<span class="glyphicon glyphicon-file icon-l"></span></p></td>

		<td align="center"><p><a href="modplug_install.php?cmd=ask_uninstall&amp;what=module&amp;mod_id={$mod_id}&amp;AXSRF_token={$axsrf}" class="module_setup">
		<span class="glyphicon glyphicon-trash icon-l text-danger"></span></td>
	</tr>
	<!-- ENDBLOCK -->
	</table>
</div>
<a href="module.php?cmd=scan" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Scan For New Modules</a>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'scan' -->
<div class="panel panel-default">
	<div class="panel-heading">Available Modules</div>
	<table class="table table-bordered">
		<tr>
			<td colspan="5"><a href="module.php"><span class="glyphicon glyphicon-chevron-left"></span> Installed Modules</a></td>
		</tr>
		<tr>
			<th>Name</th>
			<th align="center">Version</th>
			<th>Description</th>
			<th align="center">Install</th>
			<th align="center">Documentation</th>
		</tr>

		<!-- BEGINBLOCK avail -->
		<tr>
			<td valign="top" align="center" valign="top">{$icon}<br />{$mod_name}</td>
			<td valign="top" align="center" valign="top">{$mod_version}</td>
			<td valign="top"><div>{$mod_desc}</div>
							<p><span class="glyphicon glyphicon-user"></span> Author: {$mod_author}
							<span class="glyphicon glyphicon-info-sign"></span> License: {$mod_license}<br />
							<span class="glyphicon glyphicon-copyright-mark"></span> {$mod_copyright}<br />
							<span class="glyphicon glyphicon-link"></span> <a href="http://{$mod_authorUrl}">{$mod_authorUrl}</a>
							<span class="glyphicon glyphicon-envelope"></span> <a href="mailto:{$mod_authorEmail}">{$mod_authorEmail}</a></p></td>
			<td align="center"><a href="modplug_install.php?cmd=install&amp;what=module&amp;mod_id={$mod_id}&amp;AXSRF_token={$axsrf}" class="module_setup">
			<span class="glyphicon glyphicon-ok icon-l"></span><br />
			{$l_install}</a></td>

			<td align="center"><a href="modplug_doku.php?what=module&amp;mod_id={$mod_id}" class="module_setup">
			<span class="glyphicon glyphicon-file icon-l"></span><br />Documentation</a></td>
		</tr>
		<!-- ENDBLOCK -->
	</table>
</div>
<!-- ENDIF -->