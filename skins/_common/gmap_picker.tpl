<script src="http://maps.googleapis.com/maps/api/js?key={$gmap_api}"></script>

<form style="max-width:550px">
	<fieldset class="gllpLatlonPicker">
	<input type="hidden" class="gllpZoom" id="gllpZoom" value="{$zoom}"/>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-globe"></span> Pick Your Location</div>
		<table class="table">
			<tr><td><div class="gllpMap" style="width:550px;height:250px;border:solid 1px #000">Google Maps</div></td></tr>
			<tr><td>Please pick your location from the map above. You can either move the marker, or double click the location.</td></tr>
			<tr><td>
				<div class="pull-left">Search <input type="text" class="gllpSearchField"> <button type="button" class="gllpSearchButton btn btn-default input-sm"><span class="glyphicon glyphicon-search"></span></button></div>
				<div class="pull-right"><button type="button" class="gllpUpdateButton btn btn-primary" onclick="tada()">Confirm Location</button></div>
			</td></tr>
			<tr><td>
				<div>Lat/Lon <input type="text" class="gllpLatitude" id="gllpLatitude" value="{$lat}" placeholder="&phi;"/>&deg; <input type="text" class="gllpLongitude" id="gllpLongitude" value="{$lon}" placeholder="&lambda;" />&deg;
				<button type="button" class="gllpUpdateButton btn btn-default" id="gllpUpdateButton"><span class="glyphicon glyphicon-refresh"></span></button></div>
			</td></tr>
		</table>
	</div>
	</fieldset>
</form>

<script>
function tada ()
{
	var lat=$('#gllpLatitude').val();
	var lon=$('#gllpLongitude').val();
	var win = (window.opener?window.opener:window.parent);

	<!-- BEGINIF $mode == 'latlon1' -->
	win.document.getElementById('{$fid}').value = lat+','+lon;
	win.$.colorbox.close();
	<!-- ENDIF -->

	<!-- BEGINIF $mode == 'latlon2' -->
	win.document.getElementById('{$fid}_lat').value = lat;
	win.document.getElementById('{$fid}_lon').value = lon;
	win.$.colorbox.close();
	<!-- ENDIF -->
}

$(function() {
if (navigator && navigator.geolocation)
{
	navigator.geolocation.getCurrentPosition (
		function (position) {
			$("#gllpLatitude").val(position.coords.latitude);
			$("#gllpLongitude").val(position.coords.longitude);
			$("#gllpZoom").val(12);
			$("#gllpUpdateButton").trigger('click');
		},
		function(error){
			// alert(error.message);
		},
		{
			enableHighAccuracy: true,
			timeout: 5000
		})
}
});
</script>