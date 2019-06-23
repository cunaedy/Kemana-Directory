<div class="panel panel-default">
	<div class="panel-heading">Uninstall {$name}?</div>
	<form method="get" action="modplug_install.php">
		<input type="hidden" name="cmd" value="uninstall" />
		<input type="hidden" name="what" value="{$what}" />
		<input type="hidden" name="id" value="{$id}" />
		<table border="0" width="100%" class="table">
			<tr>
				<td align="center" height="100">Are you sure you want to remove this "<b>{$name}</b>" {$l_modplug}?
				<p><button type="submit" class="btn btn-danger">Uninstall Now!</button></p>
				<div class="text-danger"><b><span class="glyphicon glyphicon-warning-sign"></span> This process can not be undone!</b></div>
				</td>
			</tr>
			<tr>
				<td>
					<p><b>Additional Parameters</b></p>
					<input type="checkbox" name="remove_db" checked="checked" />  Also remove database (you may not be able to re-install without first removing doing this)<br />
					<input type="checkbox" name="remove_file" checked="checked" /> Also remove all created &amp; upload folders &amp; files
				</td>
			</tr>
		</table>
	</form>
</div>