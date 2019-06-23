<!-- BEGINIF $tpl_mode == 'detail' -->
<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li><a href="{$site_url}/account.php">{$l_my_account}</a></li>
	<li><a href="{$site_url}/trx.php">{$l_my_order}</a></li>
	<li class="active">{$order_id}</li>
</ol>
<h1>{$order_id}</h1>

<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-king" aria-hidden="true"></span> {$l_my_listing}</div>
	<table border="0" width="100%" class="table table-form">
		<tr><th width="33%"></th><td width="67%">{$image}</td></tr>
		<tr><th>{$l_title}</th><td>{$item_title}</td></tr>
		<tr><th>{$l_category}</th><td>{$dir_name}\{$cat_name}</td></tr>
		<tr><th>{$l_description}</th><td>{$item_details}</td></tr>
		<tr><th>{$l_current_class}</th><td>{$item_class} until {$item_valid_date}</td></tr>
	</table>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-king" aria-hidden="true"></span> {$l_my_order}</div>
	<table border="0" width="100%" class="table table-form">
		<tr><th width="33%">Order ID</th><td width="67%">{$order_id}</td></tr>
		<tr><th>{$l_order_date}</th><td>{$order_date}</td></tr>
		<tr><th>{$l_requested_class}</th><td>{$target_class}</td></tr>
		<tr><th>{$l_period}</th><td>{$item_period} month(s)</td></tr>
		<tr><th>{$l_monthly_fee}</th><td>{$order_price}</td></tr>
		<tr><th>{$l_total}</th><td>{$order_total}</td></tr>
		<tr><th>{$l_payment_method}</th><td>{$order_payment}</td></tr>
		<tr><th>{$l_payment_status}</th><td>{$order_paystat}</td></tr>
		<tr><th>{$l_status}</th><td>{$order_status}</td></tr>
	</table>
</div>

<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list' -->
<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li><a href="{$site_url}/account.php">{$l_my_account}</a></li>
	<li class="active">{$l_my_order}</li>
</ol>
<h1>{$l_my_order}</h1>

<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> {$l_my_order}</div>
	<table border="0" width="100%" class="table table-form">
		<tr><th>{$l_order_id}</th><th>{$l_title}</th><th>{$l_requested_class}</th><th>{$l_date}</th><th>{$l_status}</th></tr>
		<!-- BEGINBLOCK list -->
		<tr><td><a href="{$site_url}/trx.php?order_id={$order_id}">{$order_id}</a></td><td>{$item_title}</td><td>{$target_class}</td><td>{$order_date}</td><td>{$order_status}</td></tr>
		<!-- ENDBLOCK -->
	</table>
</div>

{$pagination}
<!-- ENDIF -->