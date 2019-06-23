<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li><a href="{$site_url}/add.php?cmd=edit&amp;item_id={$item_id}">{$l_edit_listing}</a></li>
	<li class="active">{$l_upgrade_listing}</li>
</ol>
<!-- BEGINIF $tpl_mode == 'payment' -->
	<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> {$l_upgrade_listing}</div>
	<table border="0" width="100%" class="table table-form">
		<tr><th width="20%"></th><td width="80%">{$image}</td></tr>
		<tr><th>{$l_title}</th><td>{$item_title}</td></tr>
		<tr><th>{$l_category}</th><td>{$dir_name}\{$cat_name}</td></tr>
		<tr><th>{$l_description}</th><td>{$item_details}</td></tr>
		<tr><th>{$l_requested_class}</th><td><b>{$upgrade_to}</b></td></tr>
		<tr><th>{$l_period}</th><td>{$period} {$l_month}(s) &times; {$price}</td></tr>
		<tr><th>{$l_valid_until}</th><td>{$valid_until}</td></tr>
		<tr><th>{$l_total}</th><td>{$total}</td></tr>
		{$warning_class}
	</table>
	</div>

<form method="get" action="add_upgrade.php">
	<input type="hidden" name="item_id" value="{$item_id}" />
	<input type="hidden" name="target_class" value="{$target_class}" />
	<input type="hidden" name="Speriod" value="{$speriod}" />
	<input type="hidden" name="Pperiod" value="{$pperiod}" />
	<input type="hidden" name="cmd" value="confirm" />
	<div class="panel panel-default">
	<div class="panel-heading"><span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span> {$l_payment_method}</div>
		<table border="0" width="100%" class="table table-form">
		<tr>
			<td width="20%" align="center">&nbsp;</td>
			<th width="80%" style="text-align:left">{$l_payment_method}</th>
		</tr>
		<!-- BEGINBLOCK pay_item -->
		<tr>
			<td align="center"><input type="radio" name="payment" value="{$method}" id="payment_{$method}" required="required" /></td>
			<td><label for="payment_{$method}">{$name}</label></td>
		</tr>
		<!-- ENDBLOCK -->
		<tr><td></td><td><label><input type="checkbox" name="read" value="1" required="required" /> {$l_i_have_confirm}</label></td><td></td></tr>
		<tr><td></td><td><button type="submit" class="btn btn-primary">{$l_upgrade_now}</button></td><td></td></tr>
		</table>
	</table>
	</div>
</form>
<!-- ENDIF -->


<!-- BEGINIF $tpl_mode == 'form' -->
<form method="get" action="add_upgrade.php">
	<input type="hidden" name="item_id" value="{$item_id}" />
	<input type="hidden" name="cmd" value="payment" />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> {$l_upgrade_listing}</div>
		<div class="panel-body">{$l_upgrade_why}</div>
		<table border="0" width="100%" class="table table-form">
			<tr><th width="20%"></th><td width="80%">{$image}</td></tr>
			<tr><th>{$l_title}</th><td>{$item_title}</td></tr>
			<tr><th>{$l_description}</th><td>{$item_details}</td></tr>
			<tr><th>{$l_current_class}</th><td>{$item_class}</td></tr>
			<tr><th>{$l_valid_until}</th><td>{$item_valid_date}</td></tr>
			<tr><th>{$l_requested_class}</th>
				<td>
					<div class="btn-group" data-toggle="buttons" id="target_class">
  					<label class="btn btn-default active"><input type="radio" name="target_class" value="P" id="pradio" checked="checked"> Premium</label>
  					<label class="btn btn-default"><input type="radio" name="target_class" value="S" id="sradio"> Sponsored</label></div>
  				</td>
			</tr>
			<tr><th>{$l_period}</th>
				<td>
					<div class="btn-group" data-toggle="buttons" id="pfee">
  					<label class="btn btn-default active"><input type="radio" name="Pperiod" value="1" checked="checked"> 1 {$l_month} ({$pfee1})</label>
  					<label class="btn btn-default"><input type="radio" name="Pperiod" value="3"> 3 {$l_months} ({$pfee3})</label>
  					<label class="btn btn-default"><input type="radio" name="Pperiod" value="6"> 6 {$l_months} ({$pfee6})</label>
  					<label class="btn btn-default"><input type="radio" name="Pperiod" value="12"> 12 {$l_months} ({$pfee12})</label>
					</div>

					<div class="btn-group" data-toggle="buttons" id="sfee" style="display:none">
  					<label class="btn btn-default active"><input type="radio" name="Speriod" value="1" checked="checked"> 1 {$l_month} ({$sfee1})</label>
  					<label class="btn btn-default"><input type="radio" name="Speriod" value="3"> 3 {$l_months} ({$sfee3})</label>
  					<label class="btn btn-default"><input type="radio" name="Speriod" value="6"> 6 {$l_months} ({$sfee6})</label>
  					<label class="btn btn-default"><input type="radio" name="Speriod" value="12"> 12 {$l_months} ({$sfee12})</label>
					</div>
				</td>
			</tr>
			<tr><td></td><td><button type="submit" class="btn btn-primary">{$l_next}</button></td></tr></table>
		</table>
	</div>
</form>

<script>
$('#target_class').on('click', function (){
if ($('#pradio').is(':checked')) {$('#pfee').hide(); $('#sfee').show()};
if ($('#sradio').is(':checked')) {$('#sfee').hide(); $('#pfee').show()};
})
</script>
<!-- ENDIF -->

<!-- BEGINSECTION warning_class -->
<tr class="danger"><th><span class="glyphicon glyphicon-exclamation-sign"></span> {$l_warning}</th><td>{$l_upgrade_warning}</td></tr>
<!-- ENDSECTION -->