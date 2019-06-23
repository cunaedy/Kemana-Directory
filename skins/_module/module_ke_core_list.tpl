<!-- by default KEMANA CORE uses /skins/default/style.css (or any other css) with same classes, so you can have consistent results -->

<!-- BEGINIF $tpl_mode == 'list_gridbox' -->
<div class="{$csswrapper}">
	<div class="list_item gridbox {$list_class}">
		<div class="ribbon"><span>{$listing_label}</span></div>
		<div class="gridbox_img"><div><a href="{$site_url}/{$url}">{$image}</a></div></div>
		<div class="gridbox_txt"><a href="{$site_url}/{$url}">{$item_title}</a> <small title="{$visible_help}">{$visible_icon}</small>{$edit_btn}
			<div>{$item_rating_star}</div>
			<p>{$item_summary_short}</p>{$cf_list}</div>
		<div class="gridbox_side clearfix">
			<div class="gridbox_side_left pull-left"></div>
			<div class="gridbox_side_right"></div>
		</div>
	</div>
</div>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list_listbox' -->
<div class="{$csswrapper}">
	<div class="list_item list_listbox {$list_class}">
	<div class="ribbon"><span>{$listing_label}</span></div>
	<div class="listbox_img"><a href="{$site_url}/{$url}">{$image}</a></div>
	<div class="listbox_txt"><a href="{$site_url}/{$url}">{$item_title}</a> {$item_rating_star} <small title="{$visible_help}">{$visible_icon}</small> {$edit_btn}
		<p>{$item_summary}</p> {$cf_list}</div>
	<div style="clear:both"></div>
	</div>
</div>
<!-- ENDIF -->

<!-- BEGINIF $tpl_mode == 'list_list' -->
<div class="{$csswrapper}">
	<div class="ke_list_list clearfix">
		<div class="ke_list_list_img">{$image_small}</div>
		<div class="ke_list_list_txt"><a href="{$site_url}/{$url}">{$item_title}</a></div>
	</div>
</div>
<!-- ENDIF -->

<!-- BEGINSECTION cf_list -->
<span class="label label-default cf_list">{$cf_title}: {$cf_value}</span>
<!-- ENDSECTION -->