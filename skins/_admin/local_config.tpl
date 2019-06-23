<form method="post" action="local_config_process.php" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> Secondary Settings</div>
		<div class="panel-body">
			<ul id="qeconfig" class="nav nav-pills">
				<li class="active"><a href="#1" class="current" data-toggle="tab">Site Layout</a></li>
				<li><a href="#2" data-toggle="tab">Sort Points</a></li>
			</ul>
		</div>
		<div class="tab-content" style="margin-top:5px">
			<div class="tab-pane active" id="1">
			<table class="table table-form">
				<tr>
					<td>Only admin can submit listing?</td>
					<td>{$add_admin_only_radio}
					<span class="glyphicon glyphicon-info-sign help" rel="#tips" title="Enable to close your site from user submission, useful if your site is an online catalog."></span></td>
				</tr>
				<tr>
					<td>Guest can submit listing?</td>
					<td>{$guess_allow_submission_radio}</td>
				</tr>
				<tr>
					<td>Guest must confirm submission?</td>
					<td>{$guess_confirm_submission_radio}
					<span class="glyphicon glyphicon-info-sign help" rel="#tips" title="An email will be sent to guest, in which guest must click a validation link before the submission sent to administrator."></span></td>
				</tr>
				<tr>
					<td>Member must confirm submission?</td>
					<td>{$member_confirm_submission_radio}
					<span class="glyphicon glyphicon-info-sign help" rel="#tips" title="An email will be sent to member, in which member must click a validation link before the submission sent to administrator."></span></td>
				</tr>
				<tr>
					<td>Backlink code</td>
					<td><textarea name="backlink_code" style="width:100%;height:100px">{$backlink_code}</textarea>
					<span class="glyphicon glyphicon-info-sign help" rel="#tips" title="Backlink code should be very simple &amp; easy to implement. If you change the code, your listing owners must also change their codes."></span></td>
				</tr>
				<tr>
					<td>Automatically verify backlink code</td>
					<td>{$backlink_autocheck_radio}
					<span class="glyphicon glyphicon-info-sign help" rel="#tips" title="When enabled, Kemana will automatically verify for backlink code in submitted URL."></span></td>
				</tr>
			</table>
			</div>


			<div class="tab-pane" id="2">
			<table class="table table-form">
				<tr>
					<td colspan="2"><p>In 'Default' sorting, position of a listing is calculated by: last time it is updated (in days), number of referral made by the listing's owner, and ratings. The calcution formula is:</p>
					<p class="text-center">Total Points = <span class="text-danger">Last_update_points</span> - <span class="text-primary">Last Update In Days</span> * <span class="text-danger">Number_of_day_multipier</span> +<br />
					<span class="text-primary">Number Of Referral</span> * <span class="text-danger">Referral_multiplier</span> +<br />
					<span class="text-primary">Rating Value</span> * <span class="text-primary">Number of Votes</span> * <span class="text-danger">Rating_multipier</span></p>
					<p>So, for example, if last update points = 900, number of day multiplier = 10, referral multiplier = 3, rating multiper = 1. And a listing was last updated 30 days ago, and 21 referral, with rating value of 3.9 of 25 votes. Thus Total Points is:</p>

					<p class="text-center">Total Points = <span class="text-danger">900</span> - <span class="text-primary">30</span> * <span class="text-danger">10</span> +<br />
					<span class="text-primary">21</span> * <span class="text-danger">3</span> +<br />
					<span class="text-primary">3.9</span> * <span class="text-primary">25</span> * <span class="text-danger">1</span><br />
					<b>&asymp; 760</b></p>
					<p>The bigger the point, the higher the position of the listing. This doesn't applicable to "Sponsored" listings which always be displayed first.</p>
					</td>
				</tr>
				<tr>
					<td>Last update points</td>
					<td><input type="text" name="update_point" value="{$update_point}" class="width-xs" /></td>
				</tr>
				<tr>
					<td>Number of day multiplier</td>
					<td><input type="text" name="update_multiplier" value="{$update_multiplier}" class="width-xs" /></td>
				</tr>
				<tr>
					<td>Referral multiplier</td>
					<td><input type="text" name="referral_multiplier" value="{$referral_multiplier}" class="width-xs" /></td>
				</tr>
				<tr>
					<td>Rating multiplier</td>
					<td><input type="text" name="rating_multipier" value="{$rating_multipier}" class="width-xs" /></td>
				</tr>
			</table>
			</div>
		</div>
	</div>
	<p class="text-center"><button type="submit" class="btn btn-primary">Submit</button> <button type="reset" class="btn btn-danger">Reset</button></p>
</form>