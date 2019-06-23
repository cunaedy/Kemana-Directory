<script>
function confirm_delete ()
{
	c = window.confirm ("Do you wish to clear all logs?\nThis process can not be undone!");
	if (!c) return false;
	document.location = "qadmin_log.php?mode=delall&AXSRF_token={$axsrf}";
}


function confirm_restore (id)
{
	c = window.confirm ("Do you wish to restore this values?\n\nNOTICE! Some changes may not be able to be restored, including: file/image changes.\nWARNING! If you have changed permalink, you may need to refresh it manually.");
	if (!c) return false;
	document.location = "qadmin_log.php?mode=restore&log_id="+id+"&AXSRF_token={$axsrf}";
}
</script>
<!-- BEGINIF $enable_detailed_log -->
<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Detailed log is enabled. You can restore previous changes. Click <span class="glyphicon glyphicon-search"></span> to restore an item.</div>
<!-- ELSE -->
<div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span> Restore function doesn't work when detailed log is disabled. Open <a href="qe_config.php" class="alert-link">Settings</a> to enable detailed log.</div>
<!-- ENDIF -->

<div class="panel panel-default">
	<div class="panel-heading">qAdmin Log</div>
<!-- BEGINIF $tpl_mode == 'list' -->
	<table class="table table-bordered">
		<tr><th colspan="8"><a href="qadmin_log.php"><span class="glyphicon glyphicon-repeat"></span> Reset Filters</a></th></tr>
		<tr><th width="5%">ID</th>
			<th width="10%">Date/Time</th>
			<th width="8%">Admin File</th>
			<th width="35%">Item Title</th>
			<th width="10%">Action</th>
			<th width="15%">User</th>
			<th width="7%" nowrap="nowrap"></th>
		</tr>

		<!-- BEGINBLOCK log_item -->
		<tr>
			<td><a href="qadmin_log.php?mode=detail&amp;log_id={$log_id}">{$log_id}</a></td>
			<td class="small"><a href="qadmin_log.php?w=date&amp;h={$log_date}">{$log_date}</a><br /><small>{$log_time}</small></td>
			<td><a href="qadmin_log.php?w=file&amp;h={$log_file}">{$log_file}</a></td>
			<td><small><a href="qadmin_log.php?w=pid&amp;h={$log_file}&amp;pid={$log_pid}"><span class="glyphicon glyphicon-filter"></span></a> <a href="{$log_file}?id={$log_pid}"><span class="glyphicon glyphicon-pencil"></span></a></small> {$log_title}</td>
			<td><a href="qadmin_log.php?w=action&amp;h={$log_action}">{$log_action_def}</a></td>
			<td><small><a href="qadmin_log.php?w=user&amp;h={$log_user}"><span class="glyphicon glyphicon-filter"></span></a> <a href="user.php?id={$log_user}"><span class="glyphicon glyphicon-user"></span></a></small> {$log_user}<br />
				<small><a href="qadmin_log.php?w=ip&amp;h={$log_ip}"><span class="glyphicon glyphicon-filter"></span></a> <a href="iplog.php?w=ip&h={$log_ip}"><span class="glyphicon glyphicon-globe"></span></a></small> {$log_ip}</td>
			<td align="center" nowrap="nowrap"><a href="qadmin_log.php?mode=detail&amp;log_id={$log_id}"><span class="glyphicon glyphicon-search"></span></a>
			<a href="qadmin_log.php?mode=del&amp;log_id={$log_id}&amp;AXSRF_token={$axsrf}"><span class="glyphicon glyphicon-remove text-danger"></span></a></td>
		</tr>
		<!-- ENDBLOCK -->

	</table>
</div>
{$pagination}
<p><a href="#page" onclick="confirm_delete()" class="btn btn-danger alert-link"><span class="glyphicon glyphicon-remove"></span> Remove all logs</a></p>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'detail' -->
<table class="table table-bordered">
	<tr><th colspan="2"><a href="qadmin_log.php"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></th></tr>
	<tr><td width="25%">Log ID/Date</td><td width="75%">{$log_id} / {$log_time}</td></tr>
	<tr><td>qAdmin File</td><td><a href="qadmin_log.php?w=file&amp;h={$log_file}">{$log_file}</a></td></tr>
	<tr><td>User</td><td>{$log_user} <a href="qadmin_log.php?w=user&amp;h={$log_user}"><span class="glyphicon glyphicon-filter"></span></a> <a href="user.php?id={$log_user}"><span class="glyphicon glyphicon-user"></span></a></td></tr>
	<tr><td>IP Address</td><td>{$log_ip} <a href="qadmin_log.php?w=ip&amp;h={$log_ip}"><span class="glyphicon glyphicon-filter"></span></a> <a href="iplog.php?w=ip&h={$log_ip}"><span class="glyphicon glyphicon-globe"></span></a></td></tr>
	<tr><td>Title</td><td>{$log_title} <a href="qadmin_log.php?w=pid&amp;h={$log_file}&amp;pid={$log_pid}"><span class="glyphicon glyphicon-filter"></span></a> <a href="{$log_file}?id={$log_pid}"><span class="glyphicon glyphicon-pencil"></span></td></tr>
	<tr><td>Action</td><td><a href="qadmin_log.php?w=action&amp;h={$log_action}">{$log_action_def}</a></td></tr>
	<tr><td>Restore</td><td><a href="#" onclick="confirm_restore({$log_id})"><span class="glyphicon glyphicon-cloud-download"></span> Restore this Entry</a></td></tr>
	<tr><td>Remove</td><td><a href="qadmin_log.php?mode=del&amp;log_id={$log_id}&amp;AXSRF_token={$axsrf}" class="text-danger"><span class="glyphicon glyphicon-trash"></span> Remove this Log</a></td></tr>
	<tr class="nohover"><td colspan="2">{$values}</td></tr>
</table>
</div>
<!-- ENDIF -->