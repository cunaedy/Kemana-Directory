<script>
function confirm_delete ()
{
	c = window.confirm ("Do you wish to clear all logs?\nThis process can not be undone!");
	if (!c) return false;
	document.location = "mailog.php?mode=delall&AXSRF_token={$axsrf}";
}
</script>

<div class="panel panel-default">
	<div class="panel-heading">Email Log</div>
<!-- BEGINIF $tpl_mode == 'list' -->
   <table class="table table-bordered">
      <tr><th width="5%">ID</th>
          <th width="20%">Sent</th>
          <th width="25%">Recipient</th>
          <th width="30%">Subject</th>
          <th width="5%">Remove</th></tr>

      <!-- BEGINBLOCK log_item -->
      <tr>
       <td><a href="mailog.php?mode=detail&amp;log_id={$log_id}">{$log_id}</a></td>
       <td>{$log_time}</td>
       <td>{$log_address}</td>
       <td><a href="mailog.php?mode=detail&amp;log_id={$log_id}">{$log_subject}</a></td>
       <td align="center"><a href="mailog.php?mode=del&amp;log_id={$log_id}&amp;AXSRF_token={$axsrf}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>
      <!-- ENDBLOCK -->

    </table>
</div>
    {$pagination}

<p><a href="#page" onclick="confirm_delete()" class="btn btn-danger alert-link"><span class="glyphicon glyphicon-remove"></span> Remove all logs</a></p>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'detail' -->
   <table border="0" width="100%" cellpadding="3" cellspacing="1" class="table table-bordered" id="result">
      <tr><th colspan="2"><a href="mailog.php"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></th></tr>
      <tr><td>Log ID/Sent Date</td><td>{$log_id} / {$log_time}</td></tr>
      <tr><td>Recipient</td><td>{$log_address}</td></tr>
      <tr><td>Subject</td><td>{$log_subject}</td></tr>
      <tr><td valign="top">Message</td><td>{$log_body}</td></tr>
	  <tr><td>Remove</td><td><a href="mailog.php?mode=del&amp;log_id={$log_id}&AXSRF_token={$axsrf}"><span class="glyphicon glyphicon-remove"></span> Click Here to Remove this Log</a></td></tr>
   </table>
</div>
<!-- ENDIF -->

<form method="get" action="mailog.php" style="margin-top:5px">
	<input type="hidden" name="mode" value="search" />
	Search for: <input type="text" name="keyword" size="20" value="{$keyword}" style="width:80%;max-width:80%;min-width:80%"/>
	<button type="submit" class="btn btn-primary">Search</button>
</form>