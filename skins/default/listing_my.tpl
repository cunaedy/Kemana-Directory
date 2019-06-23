<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li><a href="{$site_url}/account.php">{$l_my_account}</a></li>
	<li class="active">{$l_my_listing}</li>
</ol>
<h1>{$l_my_listing}</h1>

<h2>{$l_active_listing}</h2>
<div class="row">
<!-- BEGINMODULE ke_core -->
mode = item_list
items = user_id
user_id = {$current_user_id}
item_status = P
item_visibility = ALL
limit = 9999
display = list
csswrapper = col-sm-12
<!-- ENDMODULE -->
</div>


<h2>{$l_pending_listing}</h2>
<div class="row">
<!-- BEGINMODULE ke_core -->
mode = item_list
items = user_id
user_id = {$current_user_id}
item_status = E
item_visibility = ALL
limit = 9999
display = list
csswrapper = col-sm-12
<!-- ENDMODULE -->
</div>