<form method="post" action="permisi.php">
	<input type="hidden" name="cmd" value="save" />

	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-lock"></span> User Level &amp; Permission</div>
		<div class="panel-body">
			<p>Here you can define users' &amp; administrators' permissions to access several area of your web sites.
			If you need custom privileges, please refer to /{$l_admin_folder}/permisi.php file for information how to create them.</p>

		<table class="table table-bordered">
			<tr>
				<th width="20%">User/Admin Level</th>
				<th width="40%">User Title</th>
				<th width="40%">Admin Title</th>
			</tr>
			<!-- BEGINBLOCK title -->
			<tr>
				<td>{$level}</td>
				<td>{$user_title}</td>
				<td>{$admin_title}</td>
			</tr>
			<!-- ENDBLOCK -->
		</table>
		<p><span class="glyphicon glyphicon-pencil"></span> <a href="lang.php?tab=1">To modify user &amp; admin titles, use Language Editor.</a></p>

		<h3>Back End Permissions</h3>

		<table class="table table-bordered">
			<tr>
				<th width="20%">Permissions</th>
				<th width="16%" class="text-center">Level 1</th>
				<th width="16%" class="text-center">Level 2</th>
				<th width="16%" class="text-center">Level 3</th>
				<th width="16%" class="text-center">Level 4</th>
				<th width="16%" class="text-center">Level 5</th>
			</tr>
			<!-- BEGINBLOCK admin_permisi -->
			<tr>
				<td>{$permisi}</td>
				<td class="text-center">{$check1}</td>
				<td class="text-center">{$check2}</td>
				<td class="text-center">{$check3}</td>
				<td class="text-center">{$check4}</td>
				<td class="text-center">{$check5}</td>
			</tr>
			<!-- ENDBLOCK -->
		</table>

		<h3>Front End Permissions</h3>
		<table class="table table-bordered">
			<tr>
				<th width="20%">Permissions</th>
				<th width="13%" class="text-center">Guests</th>
				<th width="13%" class="text-center">Level 1</th>
				<th width="13%" class="text-center">Level 2</th>
				<th width="13%" class="text-center">Level 3</th>
				<th width="13%" class="text-center">Level 4</th>
				<th width="13%" class="text-center">Level 5</th>
			</tr>
			<!-- BEGINBLOCK user_permisi -->
			<tr>
				<td>{$permisi}</td>
				<td class="text-center">{$check0}</td>
				<td class="text-center">{$check1}</td>
				<td class="text-center">{$check2}</td>
				<td class="text-center">{$check3}</td>
				<td class="text-center">{$check4}</td>
				<td class="text-center">{$check5}</td>
			</tr>
			<!-- ENDBLOCK -->
		</table>
		</div>
	</div>
	<button type="reset" class="btn btn-default">Reset</button>
	<button type="submit" class="btn btn-primary">Save Changes</button>

</form>