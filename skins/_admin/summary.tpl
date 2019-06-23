<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-stats"></span> Visitor Statistics</div>
	<div class="panel-body">
		<!-- BEGINIF $qstat_module -->
		<div style="max-width:1900px;height:100%;overflow:hidden">
			<canvas id="canvas" height="300" width="1900"></canvas>
		</div>
		<!-- ELSE -->
		<p><a href="module.php">qStats module is disabled or not installed.</a></p>
		<!-- ENDIF -->
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading"><span class="glyphicon glyphicon-bullhorn"></span> C97.net Updates <a href="https://www.c97.net" target="_blank" title="visit C97.net"><span style="font-size:12pt;color:#999"  class="glyphicon glyphicon-link"></span></a></div>
			<div class="panel-body">
				<iframe src="index.php?cmd=feed" style="border:none;padding:0;margin:0;width:100%;height:100%" name="rssfeed"></iframe>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading"><span class="glyphicon glyphicon-dashboard"></span> Summary</div>
			<div class="panel-body">
				<ul class="list_1" style="margin-bottom: 10px">
					<li>Last login: {$log_user_id} at {$log_time} from {$log_ip_addr} (<a href="iplog.php">{$log_success}</a>)</li>
					<li>Registered users at this site: <a href="user.php">{$total_user}</a></li>
					<li>New members in the last  7 days: {$total_user_7}</li>
					<li>Hard disk space: {$free_space} MB free of {$max_space} MB</li>
					<li>Database space: {$db_size} MB</li>
					<li>Number of entries in <a href="qadmin_log.php">qe_qadmin_log</a>: {$qadmin_log_qty} items, {$qadmin_log_size} KB</li>
					<li>Number of entries in <a href="mailog.php">qe_mailog</a>: {$mailog_qty} items, {$mailog_size} KB</li>
					<li>Number of entries in <a href="iplog.php">qe_ip_log</a>: {$ip_log_qty} items, {$ip_log_size} KB</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script src="{$site_url}/misc/js/chart.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	<!-- BEGINIF $qstat_module -->
	var lineChartData = {
		labels : [{$chart_x}],
		datasets : [
			{
				label: "Hits",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "rgba(220,220,220,1)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : [{$chart_y1}]
			},
			{
				label: "Visits",
				fillColor : "rgba(151,187,205,0.2)",
				strokeColor : "rgba(151,187,205,1)",
				pointColor : "rgba(151,187,205,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(151,187,205,1)",
				data : [{$chart_y2}]
			}
		]
	}
	var ctx = document.getElementById("canvas").getContext("2d");
	window.myLine = new Chart(ctx).Line(lineChartData, { responsive: true });
	<!-- ENDIF -->
});
</script>