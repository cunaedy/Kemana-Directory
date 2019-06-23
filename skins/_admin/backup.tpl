<div class="panel panel-default">
	<div class="panel-heading">Database Backup</div>
	<div class="panel-body">
		<p>Here you can keep a backup of your entire database, including products, users, transactions, staff, amongst other data. The Backup file will be
		put in the <b>/{$l_admin_folder}/backup</b> directory, where you can download/delete it using built in file manager..</p>

		<p>Don't forget to backup your files in the <b>/public</b> folder using your FTP program.</p>

		<p><i>Technical information: this will backup all tables in the database which are prefixed with {$db_prefix}.</i></p>
	</div>
	<div class="panel-footer">
		<a href="backup.php?cmd=do_backup" class="popiframe_s btn btn-primary">Backup to plain text</a>
		<a href="backup.php?cmd=do_backup&amp;gzip=1" class="popiframe_s btn btn-default">Backup to compressed gzip</a>
	</div>
</div>