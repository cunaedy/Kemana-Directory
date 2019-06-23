<div class="container">
	<div class="row">
		<div id="body_left" class="col-sm-12 col-md-9">
			{$main_body}
		</div>
		<div id="body_right" class="col-sm-12 col-md-3">
			<div class="body_right_content">

				<h3>{$l_search}</h3>
				<form method="get" id="search_filter_main_form" onsubmit="return search_filter_submit()">
					<input type="hidden" name="cmd" value="{$cmd}" />
					<input type="hidden" name="ajax" value="1" />
					<input type="hidden" name="owner_id" value="{$owner_id}" />
					<p><input type="text" name="query" value="{$query}" />
					<button type="button" onclick="search_filter_submit()" class="btn btn-primary"><span class="glyphicon glyphicon-search" style="padding:3px"></span></button></p>
					<p>{$search_sort} {$mode_select}</p>
				</form>

				<h3>{$l_filter_result}</h3>
				<div id="search_filter"></div>
			</div>

			<div class="body_right_content">
			{$module_box_L1}
			</div>

			<div class="body_right_content">
			{$module_box_L2}
			</div>

			<div class="body_right_content">
			{$module_box_R1}
			</div>

			<div class="body_right_content">
			{$module_box_R2}
			</div>
		</div>
	</div>
</div>