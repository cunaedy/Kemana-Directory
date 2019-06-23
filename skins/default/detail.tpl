<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<!-- BEGINBLOCK cat_bread_crumb -->
 	<li><a href="{$bc_link}">{$bc_title}</a></li>
	<!-- ENDBLOCK -->
	<li class="active">{$item_title}</li>
</ol>

<h1>{$item_title} <small><button class="btn btn-xs btn-default" title="{$visible_help}">{$visible_icon}</button></small></h1>
<!-- BEGINIF $enable_twitter_share -->
<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" style="vertical-align:text-bottom !important;margin-top:5px !important">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<!-- ENDIF -->

<!-- BEGINIF $enable_facebook_like -->
<div class="fb-like" data-href="{$current_url}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true" style="vertical-align:top !important"></div>
<!-- ENDIF -->

<!-- BEGINIF $require_logo --><div style="margin-top:5px">{$image}</div><!-- ENDIF -->
<!-- BEGINIF $require_url --><h3><span class="glyphicon glyphicon-link"></span> URL: <a href="{$item_url}" target="_blank">{$item_url_string}</a></h3><!-- ENDIF -->

<h2>{$l_description}</h2>
<!-- BEGINIF $require_summary --><p>{$item_summary}</p><!-- ENDIF -->
<p>{$item_details}</p>

<!-- BEGINIF $also_by -->
<p>{$l_also_by}:</p>
{$also_by}
<!-- ENDIF -->

<h2>{$l_specification}</h2>
<div class="panel panel-default">

<table border="1" class="table table-bordered" width="100%">
	{$cf_list}
</table>
</div>

<h2>{$l_listing_info}</h2>

<div class="list-group">
	<!-- BEGINIF $edit_btn -->
	<a class="list-group-item list-group-item-info"><span class="glyphicon glyphicon-certificate"></span> {$item_class} until {$item_valid_date}</a>
	<a class="list-group-item list-group-item-info" href="{$site_url}/add.php?cmd=edit&amp;item_id={$item_id}"><span class="glyphicon glyphicon-pencil"></span> {$l_edit_item}</a>
	<!-- ENDIF -->
	<a class="list-group-item" href="{$site_url}/tell.php?who=friend&amp;item_id={$item_id}"><span class="glyphicon glyphicon-share-alt"></span> {$l_share_item}</a>
	<a class="list-group-item" href="{$site_url}/tell.php?who=owner&amp;item_id={$item_id}"><span class="glyphicon glyphicon-envelope"></span> {$l_contact_owner}</a>
	<!-- BEGINIF $add_fave -->
	<a class="list-group-item" href="{$site_url}/account.php?cmd=fave_add&amp;item_id={$item_id}"><span class="glyphicon glyphicon-heart-empty"></span> {$l_add_favorite}</a>
	<!-- ELSE -->
	<a class="list-group-item" href="{$site_url}/account.php?cmd=fave_del&amp;item_id={$item_id}"><span class="glyphicon glyphicon-heart"></span> {$l_remove_favorite}</a>
	<!-- ENDIF -->
	<a class="list-group-item" href="{$site_url}/tell.php?who=us&amp;item_id={$item_id}"><span class="glyphicon glyphicon-flag"></span> {$l_notify_us}</a>
	<div class="list-group-item" href="{$site_url}/listing_search.php?owner_id={$owner_id}"><span class="glyphicon glyphicon-user"></span> Submitted by {$owner_id} on {$item_date}</div>
	<div class="list-group-item"><span class="glyphicon glyphicon-eye-open"></span> Hits: {$stat_hits}&times;</div>
	<div class="list-group-item"><span class="glyphicon glyphicon-th-list"></span> Listed on: {$listed_cat}</div>
</div>

<!-- BEGINIF $enable_comment -->
<h2>{$l_review}</h2>
<!-- BEGINMODULE qcomment -->
mode = comment
mod_id = listing
item_id = {$item_id}
sort = latest
title = {$item_title}
<!-- ENDMODULE -->
<!-- ENDIF -->

<!-- BEGINIF $enable_facebook_comment -->
<h2>{$l_facebook_comment}</h2>
<div class="fb-comments" data-href="{$current_url}" data-numposts="5" data-colorscheme="light"></div>
<div style="clear:both"></div>
<!-- ENDIF -->

<!-- BEGINSECTION cf_list -->
	<tr><th id="cf_title_{$cf_idx}" valign="top" width="35%">{$cf_title}</th><td id="cf_value_{$cf_idx}" valign="top" width="65%">{$cf_value}</td></tr>
<!-- ENDSECTION -->

<!-- BEGINSECTION cf_list_div -->
	<tr><td colspan="2"><h3 class="cf_div">{$cf_value}</h3></td></tr>
<!-- ENDSECTION -->