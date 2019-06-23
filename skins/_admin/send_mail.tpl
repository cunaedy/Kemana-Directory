<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-send" aria-hidden="true"></span> Send Email</div>
	<div class="panel-body">
		<form method="post" action="sendmail.php">
			<input type="hidden" name="mode" value="{$mode}" />
			<table border="0" width="100%" class="table table-form">
				<tr>
					<td width="25%">Send Email to</td>
					<td width="75%"><input type="text" size="50" name="name" value="{$user_id}" /></td>
				</tr>
				<tr>
					<td>Email address</td>
					<td><input type="text" size="50" name="email" value="{$user_email}" /></td>
				</tr>
				<tr>
					<td>Subject</td>
					<td><input type="text" size="50" name="subject" value="{$subject}" /></td>
				</tr>
				<tr>
					<td>Message</td>
					<td>{$email_body}</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<button type="submit" class="btn btn-primary">Submit</button>
						<button type="reset" class="btn btn-danger">Reset</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>