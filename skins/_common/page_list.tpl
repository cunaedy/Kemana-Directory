<ol class="breadcrumb">
	<li><a href="{$site_url}"><span class="glyphicon glyphicon-home"></span></a></li>
	<li class="active">{$cat_name}</li>
</ol>
<!-- BEGINIF $tpl_mode == 'cat' -->
<div class="panel panel-default">
	<div class="panel-heading"><h1 style="margin:0">{$cat_name}</h1></div>
	<div class="panel-body">
		<div style="margin:0 10px 0 0; float:left;"><img src="{$site_url}/{$cat_image}" alt="{$cat_name}" /></div>
		<div>{$cat_details}</div>
		<div style="clear:both"></div>
	</div>
<!-- ELSE -->
<h1>{$l_article_by} {$page_author}</h1>
<!-- ENDIF -->

<!-- BEGINIF $all_cat_list -->
	<table class="table" width="100%">
	<tr>
		<th colspan="2">{$l_other_cat}</th>
	</tr>
	<tr>
		<td colspan="2">
	<!-- BEGINBLOCK cat_list -->
			<a href="{$site_url}/{$cat_url}" style="padding-right:20px"><img src="{$cat_image}" alt="{$cat_name}" width="30" /> <b>{$cat_name}</b></a>
	<!-- ENDBLOCK -->
		</ul></td>
	</tr>
	<tr>
		<th colspan="2">{$l_contents}</th>
	</tr>
	</table>
<!-- ENDIF -->

	<div class="table-responsive">
	<table class="table" width="100%" border="0">
	<tr>
		<th width="15%"></th>
		<th width="70%"><a href="javascript:sortby('t')">{$l_title}</a></th>
		<th style="text-align:right" width="15%"><a href="javascript:sortby('d')">{$l_date}</a></th>
	</tr>
	<!-- BEGINBLOCK list -->
	<tr>
		<td align="center"><a href="{$site_url}/{$page_url}"><img src="{$page_image_thumb}" width="50" alt="{$page_title}" /></a></td>
		<td><a href="{$site_url}/{$page_url}">{$page_title}</a> {$page_pinned} {$page_attachment} {$page_locked}<div class="small"><span class="glyphicon glyphicon-user"></span> <a href="{$site_url}/page.php?author={$page_author}">{$page_author}</a></div></td>
		<td align="right">{$page_date}</td>

	</tr>
	<!-- ENDBLOCK -->
	</table>
	</div>

<!-- BEGINIF $tpl_mode == 'cat' -->
</div>
<!-- ENDIF -->
{$pagination}

<form method="get" action="{$site_url}/page.php" id="sortby">
	<input type="hidden" name="cmd" value="list" />
	<input type="hidden" name="cid" value="{$cid}" />
	<input type="hidden" name="author" value="{$page_author}" />
	<input type="hidden" name="sort" value="t" id="sortby_value" />
</form>


<script>
function sortby (w)
{
	$('#sortby_value').val(w);
	$('#sortby').submit();
}
</script>