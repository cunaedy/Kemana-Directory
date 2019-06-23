<!-- BEGINSECTION pagination -->
<div style="float:left;margin-right:5px">
	<ul class="pagination">
		<li><span style="background:#ccc">{$pg_total_items} {$l_items}/{$pg_total_pages} {$l_pages}</span></li>
	</ul>
</div>
<div style="float:left">
	<ul class="pagination">
		<li class="{$pg_top_class}">{$pg_top}</li>
		<li class="{$pg_prev_class}">{$pg_prev}</li>
		<!-- BEGINBLOCK pagelist -->
		<li class="{$class}">{$pp}</li>
		<!-- ENDBLOCK -->
		<li class="{$pg_next_class}">{$pg_next}</li>
		<li class="{$pg_last_class}">{$pg_last}</li>
		<li class="normal"><a onclick="promptPage()" style="cursor:pointer"><span class="glyphicon glyphicon-share-alt"></span></a></li>
	</ul>
</div>
<div style="clear:both"></div>
<script>
function promptPage()
{
	var page = prompt ('{$l_enter_page_number}: 1-{$pg_total_pages}', '{$pg_current_page}');
	var pageInt = parseInt (page);
	if (isNaN (pageInt)) return false;
	if ((pageInt > {$pg_total_pages}) || (pageInt < 1) || (pageInt == {$pg_current_page})) return false;
	var url = "{$base_url}&p="+pageInt;
	// alert (url);
	window.location.href = url;
	return false;
}
</script>
<!-- ENDSECTION -->

<!-- BEGINSECTION fullpage_msg -->
<div class="well" id="msgalert">
{$message}
<p>Please use your browser <a href="javascript:history.go(-1)">&lt;back&gt;</a> button to return to previous page!</p>
</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION popup_msg -->
<div class="well" id="msgalert">
{$message}
<p align="center"><button type="button" onclick="javascript:document.getElementById('msgalert').style.display='none'">Ok</button></p>
</div>
<!-- ENDSECTION -->

<!-- BEGINSECTION normal_msg -->
{$message}
<!-- ENDSECTION -->

<!-- BEGINSECTION module_ez_config -->
<form method="get" action="modplug_config.php?what=module&mod_id={$mod_id}">
	{$hidden_values}
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> {$config_title}</div>
		<table class="table table-form">
			<!-- BEGINBLOCK configuration -->
			<tr><th width="25%">{$config_label}</th><td width="75%">{$config_value}</td></tr>
			<!-- ENDBLOCK -->
			<tr><td colspan="2"><button type="submit" class="btn btn-primary">Save</button></td></tr>
		</table>
	</div>
</form>
<!-- ENDSECTION -->


<!-- BEGINSECTION rssfeed -->
	<!-- BEGINBLOCK rssfeed -->
	<small><span class="glyphicon glyphicon-bullhorn"></span></small> <a href="{$link}" target="_blank">{$title}</a><br />
	<small class="text-muted"><span class="glyphicon glyphicon-time"></span> {$shortDate}</small>
	<p>{$shortDesc}</p>

	<!-- ENDBLOCK -->
<!-- ENDSECTION -->


<!-- BEGINSECTION listing_feat -->
<h1>Featured Listing</h1>
<p>To promote listings for specific directory or category, please use <a href="listing_dir.php">Listing &gt; Multiple Directories</a> or <a href="listing_dir_select.php?what=cat&amp;qadmin_cmd=list">Listing &gt; Manage Categories</a>.</p>
<!-- ENDSECTION -->