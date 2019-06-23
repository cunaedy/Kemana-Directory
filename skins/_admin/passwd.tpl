<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-off" aria-hidden="true"></span> Change Admin Password</div>
	<form method="post" action="passwd.php" target="_parent">
		<input type="hidden" name="cmd" value="change" />
		<table class="table table-form" id="passwd">
			<tr>
				<td width="30%">Current Password</td><td width="70%"><input type="password" name="curr_passwd" required /></td>
			</tr>
			<tr>
				<td width="30%">New Password</td><td width="70%"><input type="password" name="new_passwd" id="new_passwd" maxlength="255" onkeyup="passwordStrength('new_passwd',this.value)" required /></td>
			</tr>
			<tr><td colspan="2" align="center"><button type="submit" class="btn btn-primary">Submit</button></td></tr>
		</table>
	</form>
</div>