<div id="ss_wrapper_{$id}">
	<div class="slider-wrapper {$theme}">
		<div id="slider_{$id}" class="nivoSlider">
		<!-- BEGINBLOCK content -->
		<a href="{$page_keyword}"><img src="{$site_url}/public/image/{$page_image}" alt="{$page_title}" title="{$page_title}" /></a>
		<!-- ENDBLOCK -->
		</div>
	</div>
</div>
<script type="text/javascript" src="{$site_url}/skins/_module/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript">
$(window).on('load', function() {
	$('#slider_{$id}').nivoSlider({randomStart:true,slices:8,boxCols:4,boxRows:2});
});
</script>