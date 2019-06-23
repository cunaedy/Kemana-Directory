<form method="get" action="listing_list.php" name="listing_list" id="listing_list">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-th"></span> Listing</div>
		<table class="table">
			<tr><td colspan="2">Keywords</td><td colspan="5"><input type="text" name="keyword" value="{$keyword}" /> <span class="glyphicon glyphicon-info-sign help tips" title="This will search in ID, title, URL, summary &amp; details."></td></tr>
			<tr><td colspan="2">Entry Date</td>
				<td colspan="5">{$start_date} <a style="cursor:pointer"><span class="glyphicon glyphicon-calendar" id="start_date" class="calendar" data-date-format="yyyy-mm-dd" data-date=""></span></a>
								to {$end_date} <a style="cursor:pointer"><span class="glyphicon glyphicon-calendar" id="end_date" class="calendar" data-date-format="yyyy-mm-dd" data-date=""></span></a></td></tr>
			<tr><td colspan="2">Directory</td><td colspan="5">{$dir_select} &bull; Category <span id="cat_select"></span></td></tr>
			<tr><td colspan="2">Owner</td><td colspan="5"><input type="text" name="owner_id" id="owner_id" value="{$owner_id}" class="width-sm" /></td></tr>
			<tr><td colspan="2">Status</td><td colspan="5">{$status_select}</td></tr>
			<tr><td colspan="2">Class</td><td colspan="5">{$class_select}</td></tr>
			<tr><td colspan="2">Search Operation</td><td colspan="5">{$mode_select} &bull; Sort Results By {$sort_select} &bull; <button type="submit" class="btn btn-primary" name="cmd" value="search">Search Now</button></td></tr>

			<tr><td colspan="2">Short Cuts</td><td colspan="5">
				<ul class="quick_info">
					<li><a href="listing_list.php">Show All</a></li>
					<li><a href="listing_list.php?cmd=search&amp;item_status=E&amp;sort=dd">All New (Pending)</a></li>
					<li><a href="listing_list.php?cmd=search&amp;item_status=E">All Pending</a></li>
					<li><a href="listing_list.php?cmd=search&amp;item_status=P">All Approved</a></li>
					<li><a href="listing.php"><span class="glyphicon glyphicon-plus"></span> Add New Item</a></li>
				</ul></td></tr>
		</table>

		<table class="table">
			<tr><td colspan="7" class="adminbg_h">Search Results</td></tr>
			<tr>
				<th style="text-align:center" width="10%"><input type="checkbox" onclick="SetAllCheckBoxes ('listing_list', 'listing_list', this.checked)" /></th>
				<th width="5%">ID /<br />Hits</th>
				<th width="25%">Directory /<br />Category <span class="glyphicon glyphicon-info-sign help tips" title="Hover mouse on category name to see category structure."></span></th>
				<th width="35%">Title /<br />Summary</th>
				<th width="10%">Owner /<br />Entry Date</th>
				<th width="10%">Status /<br />Class</th>
				<th width="5%">Edit</th>
			</tr>
			<!-- BEGINBLOCK list -->
			<tr>
				<td style="text-align:center"><label for="select_{$idx}"><img src="{$image_small}" style="width:50px" alt="{$item_title}" /></label> <br /><input type="checkbox" name="select_{$idx}" id="select_{$idx}" value="1" /></td>
				<td>{$idx}<div class="small">{$stat_hits}</div></td>
				<td>{$dir_title}<div class="small">{$category}</div></td>
				<td>{$change} {$item_title}<div class="small">{$item_summary}</div></td>
				<td>{$owner_id}<div class="small">{$item_date}</div></td>
				<td>{$item_status}<div class="small">{$item_class}</div></td>
				<td><a href="listing.php?cmd=edit&amp;item_id={$idx}">Edit</a></td>
			</tr>
			<!-- ENDBLOCK -->
			<tr>
				<td style="text-align:center"><span class="glyphicon glyphicon-arrow-up"></span></td>
				<td colspan="6">
					<div class="pull-left">With selected:&nbsp;</div>
					<div class="pull-left">
						<ul class="quick_info">
							<li><button type="submit" name="cmd" value="setE" onclick="return askconfirm('P')">Status as Pending</button></li>
							<li><button type="submit" name="cmd" value="setP" onclick="return askconfirm('E')">Status as Approved</button></li>
							<li><button type="submit" name="cmd" value="delAll" onclick="return askconfirm('X')">Delete</button></li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</div>
</form>
{$pagination}
<script>
$(function(){
	$('#start_date').datepicker().on('changeDate',function(ev){update_date_form('start_date',ev.date);
	$('#start_date').datepicker('hide')});
	$('#end_date').datepicker().on('changeDate',function(ev){update_date_form('end_date',ev.date);
	$('#end_date').datepicker('hide')});
	$('#owner_id').autocomplete({serviceUrl: 'admin_ajax.php?cmd=user',onSelect: function (suggestion) {$('#owner_id').val(suggestion.value)}})
	$('#cat_select').load('admin_ajax.php?cmd=cat_form&query=listing_list&dir_id={$dir_id}&cat_id={$cat_id}');
})


function update_cat (d)
{
	$('#cat_select').load('admin_ajax.php?cmd=cat_form&query=listing_list&dir_id='+d+'&cat_id={$cat_id}');
}

function askconfirm (w)
{
	if (w == 'P')
		c = confirm ('Are you sure to mark the selected items as Pending?');
	else if (w == 'E')
		c = confirm ('Are you sure to mark the selected items as Approved?');
	else
		c = confirm ('Are you sure to remove selected items?\nWARNING: This action can not be undone!');
	if (c) return true; else return false;
}
</script>