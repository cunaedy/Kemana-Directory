<!-- BEGINIF $tpl_mode == 'list' -->
<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<!-- BEGINBLOCK cat_bread_crumb -->
 	<li><a href="{$bc_link}">{$bc_title}</a></li>
	<!-- ENDBLOCK -->
	<li class="active">{$cat_name}</li>
</ol>

<h1 style="margin-top:0">{$cat_name}</h1>
{$cat_details}
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list' -->
<div class="row cat_list">
	<!-- BEGINBLOCK cat_list -->
	<div class="col-sm-4">
		<div><img src="{$cat_image}" class="pull-left"> {$cat_name} <i>({$cat_num_link} items)</i><div>{$cat_sub_list}</div></div>
	</div>
	<!-- ENDBLOCK -->
</div>
<!-- ENDIF -->

<!-- BEGINIF $featured_listing -->
<h2>{$l_featured_listing}</h2>
<div class="row" style="margin-bottom:10px">
	<!-- BEGINMODULE ke_core -->
	mode = item_list
	items = cat_featured
	display = grid
	csswrapper = col-sm-4
	<!-- ENDMODULE -->
</div>
<!-- ENDIF -->

<h2>{$l_listing}</h2>

<!-- BEGINIF $no_search_result -->
<div>{$l_search_no_result}</div>
<!-- ELSE -->
<div class="row">
<!-- BEGINBLOCK search_item -->
<div class="col-sm-4">
	<div class="list_item gridbox {$list_class}">
		<div class="ribbon"><span>{$listing_label}</span></div>
		<div class="gridbox_img"><div><a href="{$site_url}/{$url}">{$image}</a></div></div>
		<div class="gridbox_txt"><a href="{$site_url}/{$url}">{$item_title}</a> <small title="{$visible_help}">{$visible_icon}</small>{$edit_btn}
			<div>{$item_rating_star}</div>
			<p>{$item_summary_short}</p>{$cf_list}
		</div>
	</div>
</div>
<!-- ENDBLOCK -->
</div>
{$pagination}
<!-- ENDIF -->

<script>
function search_filter_submit ()
{
	var f = $('#search_filter_form').serialize();
	var g = $('#search_filter_main_form').serialize();
    $('#body_left').load('{$site_url}/listing_search.php?search_mode={$cmd}&'+g+'&'+f);
	return false;
}

$(function(){
$('#search_filter_main_form select').change(function () { search_filter_submit() });
$('#search_filter_main_form input:radio').change(function () { search_filter_submit() });
$('#search_filter_main_form input:checkbox').change(function () { search_filter_submit() });
$('#search_filter_main_form').submit(function () { search_filter_submit() });
$('#search_filter').load('{$site_url}/ajax.php?cmd=search_filter&search_mode={$cmd}&query=foo&dir_id={$dir_id}&{$query_url}');
});
</script>

<!-- BEGINSECTION cf_list -->
<span class="label label-default cf_list">{$cf_title}: {$cf_value}</span>
<!-- ENDSECTION -->