<!-- BEGINIF $tpl_mode == 'list' -->
<h1>Demo</h1>
<p>This is demo mode in full screen (exclusive).</p>

<table class="table_2" border="0" width="100%">
	<tr><th>Name</th><th>Address</th><th>Detail</th></tr>
<!-- BEGINBLOCK list -->
	<tr><td>{$dname}</td><td>{$daddress}</td><td><a href="task.php?mod=demo&amp;cmd=view&amp;idx={$idx}">Detail</a></tr>
<!-- ENDBLOCK -->
</table>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'view' -->
<h1>View {$dname}</h1>
<p>This is demo mode in full screen (exclusive).</p>

<table class="table_2" border="0" width="100%">
	<tr><th colspan="2">Details</th></tr>
	<tr><td class="label">Name</td><td>{$dname}</td></tr>
	<tr><td class="label">Address</td><td>{$daddress}</td></tr>
	<tr><td class="label">D.O.B</td><td>{$ddate}</td></tr>
	<tr><td class="label">Sex</td><td>{$dsex}</td></tr>
	<tr><td class="label">Notes</td><td>{$dnotes}</td></tr>
</table>

<p><a href="task.php?mod=demo">&laquo; Back</a></p>
<!-- ENDIF -->
