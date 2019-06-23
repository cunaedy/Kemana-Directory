<script type="text/javascript">
//<![CDATA[
function confirm_delete ()
{
	c = window.confirm ("Do you wish to clear all logs?\nThis process can not be undone!");
	if (!c) return false;
	document.location = "paylog.php?mode=delall";
}
//]]>
</script>

<!-- BEGINIF $tpl_mode == 'list' -->
<div class="panel panel-default">
   <div class="panel-heading">Payment Log</div>
   <table class="table table-bordered">
      <tr><td class="adminbg_c" width="5%">ID</td>
          <td class="adminbg_c" width="5%">Payment Method</td>
		  <td class="adminbg_c" width="10%">Order ID</td>
		  <td class="adminbg_c" width="10%">TXN ID</td>
		  <td class="adminbg_c" width="10%">Order Total</td>
		  <td class="adminbg_c" width="10%">Paid</td>
          <td class="adminbg_c" width="10%">Payment Status</td>
		  <td class="adminbg_c" width="35%">Notes</td>
          <td class="adminbg_c" width="5%">Remove</td></tr>

      <!-- BEGINBLOCK log_item -->
      <tr>
       <td><a href="paylog.php?mode=detail&amp;log_id={$log_id}">{$log_id}</a></td>
       <td>{$payment_method}</td>
       <td><a href="trx.php?order_id={$order_id}">{$order_id}</a></td>
	   <td><a href="paylog.php?mode=detail&amp;log_id={$log_id}">{$txn_id}</a></td>
	   <td>{$order_total}</td>
	   <td>{$order_paid}</td>
	   <td>{$payment_status}</td>
	   <td style="color:#f00">{$notes}</td>
       <td align="center"><a href="paylog.php?mode=del&amp;log_id={$log_id}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>
      <!-- ENDBLOCK -->

    </table>
</div>
{$pagination}
<p><a href="#page" onclick="confirm_delete()" class="btn btn-danger alert-link"><span class="glyphicon glyphicon-remove"></span> Remove all logs</a></p>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'detail' -->
<div class="panel panel-default">
   <div class="panel-heading">Payment Log</div>
   <table class="table table-bordered">
    <tr><th colspan="2"><a href="paylog.php"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></th></tr>
    <tr><td>Log ID/Log Time</td><td>{$log_id} / {$log_time}</td></tr>
    <tr><td>Payment Method / Status</td><td>{$payment_method}/ {$payment_status}</td></tr>
    <tr><td>Order ID/TXN ID</td><td><a href="trx.php?order_id={$order_id}">{$order_id}</a> / {$txn_id}</td></tr>
	  <tr><td>Order Total/Paid</td><td>{$order_total} / {$order_paid}</td></tr>
    <tr><td valign="top">Notes</td><td style="color:#f00">{$notes}</td></tr>
	  <tr><td valign="top">Technical Request</td><td><div style="width:100%; height:300px; overflow:auto; font:9pt Courier">{$sent_request}</div></td></tr>
	  <tr><td valign="top">Technical Response</td><td><div style="width:100%; height:300px; overflow:auto; font:9pt Courier">{$response}</div></td></tr>
	  <tr><td>Remove</td><td><a href="paylog.php?mode=del&amp;log_id={$log_id}">Click Here to Remove this Log</a></td></tr>
   </table>
</div>
<!-- ENDIF -->

<div style="padding:10px"><b>Notes:</b> this only logs all payments made by PayPal IPN.<br />
<b>Missing ID:</b> on a very rare occassion, PayPAL IPN testing may fail (server down, etc), resulting in missing order IDs. If this happen,
you have to manually look for PayPal email notifications.</div>
<script type="text/javascript">
stripeTable ('result');
</script>