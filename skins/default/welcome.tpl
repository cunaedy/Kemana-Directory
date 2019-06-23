<div>
	<!-- BEGINMODULE slideshow -->theme=theme-welcome<!-- ENDMODULE -->
</div>

<div id="welcome" class="container"">
	<form method="get" action="listing_search.php">
		<input type="hidden" name="cmd" value="search" />
		{$dir_select}
		<input type="text" name="query" placeholder="{$l_search}" autofocus="autofocus" />
		<button type="submit" class="btn btn-primary">{$l_search}</button>
	</form>

	<h1>{$l_browse}</h1>
	<div class="row cat_list">
	<!-- BEGINBLOCK cat_list -->
	<div class="col-sm-4 col-md-3">
		<div><a href="{$cat_url}"><img src="{$cat_image}" class="pull-left"></a> {$cat_name} <i>({$cat_num_link} items)</i><div>{$cat_sub_list}</div></div>
	</div>
	<!-- ENDBLOCK -->
	</div>

	<!-- BEGINIF $featured_listing -->
	<h1>{$l_featured_listing}</h1>
	<div class="row" id="welcome_feat">
	<!-- BEGINMODULE ke_core -->
	mode = item_list
	items = dir_featured
	dir_id = {$dir_id}
	display = grid
	div_id = welcome_feat
	csswrapper_grid = col-sm-4 col-md-3
	csswrapper_list = col-sm-12
	<!-- ENDMODULE -->
	</div>
	<!-- ENDIF -->


	<h1>{$l_newest_listing}</h1>
	<div class="row" id="welcome_new">
	<!-- BEGINMODULE ke_core -->
	mode = item_list
	items = newest
	dir_id = {$dir_id}
	display = grid
	limit = 6
	div_id = welcome_new
	csswrapper_grid = col-sm-4 col-md-3
	csswrapper_list = col-sm-12
	<!-- ENDMODULE -->
	</div>

	<h1>{$l_welcome}</h1>
	<!-- BEGINMODULE page_gallery -->
	// Welcome text
	page_id = 1
	body = 1
	<!-- ENDMODULE -->

	<h3 style="padding-top:10px">{$l_site_news}</h3>
	<!-- BEGINMODULE page_gallery -->
	// Display list of 5 pages from group 2 (news), all categories
	group_id = news
	title = 1
	style = list
	orderby = page_date
	sort = desc
	<!-- ENDMODULE -->

	<ul class="list_1">
		<li><a href="{$site_url}/{$news_url}">{$l_all_news}</a></li>
	</ul>
</div>