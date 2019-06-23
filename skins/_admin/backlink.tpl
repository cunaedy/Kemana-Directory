<div class="panel panel-default">
	<div class="panel-heading">Backlink URL Status</div>
	<div class="panel-body">
	<p>Kemana regularly verify backlink URL for submitted listings. After whole URL's been verified, it will automatically reset the results, and start over from beginning. You can disable automatic Backlink Checker
	by visiting <a href="{$site_url}/{$l_admin_folder}/local_config.php">Settings page</a>.</p>
	</div>
	<table border="0" width="100%" cellpadding="3" cellspacing="1" class="table table-bordered" id="result">
		<tr>
			<th colspan="6">
				<a href="backlink.php?w=valid"><span class="glyphicon glyphicon-ok text-success"></span> All Valids</a> &nbsp;
				<a href="backlink.php?w=invalid"><span class="glyphicon glyphicon-remove text-danger"></span> All Invalids</a> &nbsp;
				<a href="backlink.php?w=notyet"><span class="text-warning"><b>?</b></span> Not Verified Yet</a> &nbsp;
				<a href="backlink.php"><span class="glyphicon glyphicon-repeat"></span> Show All</a>
			</th>
		</tr>
		<tr><th width="10%"></th><th width="60%">Listing</th><th width="10%" style="text-align:center">Status</th><th width="20%">Tools</th></tr>
		<!-- BEGINBLOCK list -->
		<tr>
			<td style="text-align:center"><img src="{$image_small}" style="width:50px" alt="{$item_title}" /></td>
			<td>
				<div class="small">{$dir_title} \ {$category}</div>
				<div>{$item_title}</div>
				<div><span class="glyphicon glyphicon-link" title="Backlink URL"></span> <input type="hidden" id="item_backlink_url_{$idx}" value="{$item_backlink_url}" /> {$item_backlink_url}</div>
			</td>
			<td style="text-align:center"><span id="backlink_ok_{$idx}">{$status}</span></td>
			<td>
				<a href="javascript:verify_backlink('{$idx}')"><span class="glyphicon glyphicon-refresh tips" title="Refresh verification."></span></a>&nbsp;
				<a href="{$item_backlink_url}" target="_blank tips" title="Open URL in new window"><span class="glyphicon glyphicon-link"></span></a>&nbsp;
				<a href="listing.php?cmd=edit&amp;item_id={$idx} tips" title="Edit this item"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;
			</td>
		</tr>
		<!-- ENDBLOCK -->
	</table>
</div>
<p><a href="{$site_url}/{$l_admin_folder}/listing_list.php?cmd=search&amp;items={$items}" class="btn btn-default alert-link"><span class="glyphicon glyphicon-pencil"></span> Edit Displayed Items</a>
<a href="javascript:;" onclick="confirm_delete()" class="btn btn-danger alert-link"><span class="glyphicon glyphicon-remove"></span> Reset Verifications</a></p>

{$pagination}

<script>
function verify_backlink (item_id)
{
	isBacklinkOk = validateByAjax ('#item_backlink_url_'+item_id, '{$site_url}/{$l_admin_folder}/admin_ajax.php?cmd=backlink_update&item_id='+item_id, '#backlink_ok_'+item_id);
}

function confirm_delete ()
{
	c = window.confirm ("Do you wish to reset all verification results?\nThis process can not be undone!");
	if (!c) return false;
	document.location = "backlink.php?cmd=reset&AXSRF_token={$axsrf}";
}
</script>