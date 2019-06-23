<!-- BEGINSECTION ezform_required -->
 <span style="color:#f00"><b>&bull;</b></span>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_required_js -->
 required="required"
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_help -->
 <span class="glyphicon glyphicon-info-sign help tips" title="{$help}"></span>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_viewfile -->
 {$value} - {$size} bytes [ <a href="{$site_url}/{$view}">view file</a> ] [ <a href="{$site_url}/{$remove}">remove</a> ]<br />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_viewimg -->
 {$value} - {$size} bytes [ <a href="{$site_url}/{$view}">view image</a> ] [ <a href="{$site_url}/{$remove}">remove</a> ]<br />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_viewthumb -->
 <img src="{$thumb}" alt="{$value}" /><br />
 {$value} - {$size} bytes [ <a href="{$site_url}/{$view}" class="lightbox">view image</a> ] [ <a href="{$site_url}/{$remove}">remove</a> ]<br />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_head -->
<form method="post" name="ezform_form" action="{$site_url}/{$action}" enctype="{$enctype}">
<input type="hidden" name="ezform_cmd" value="{$cmd}" />
<input type="hidden" name="ezform_process" value="1" />
<input type="hidden" name="primary_key" value="{$primary_key}" />
<input type="hidden" name="primary_val" value="{$primary_val}" />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_captcha -->
<img src="visual.php" alt="captchai" /><br /><input type="text" name="visual" size="5" maxlength="5" required="required" />
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_varchar -->
<input type="text" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_password -->
<input type="password" name="{$field}" size="{$size}" value="{$value}" maxlength="{$maxlength}" {$required_js} class="password" onkeyup="passwordStrength(this.value)" /> {$required} {$help}
<br />
<div id="passwordStrength" class="strength0">Password not entered</div>
<div style="clear:both"></div>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_date -->
{$date_select}
<a style="cursor:pointer"><span class="glyphicon glyphicon-calendar" id="date_{$field}" class="calendar" data-date-format="yyyy-mm-dd" data-date="{$value}"></span></a>
<script type="text/javascript">var cal=$('#date_{$field}').datepicker().on('changeDate',function(ev){update_date_form('{$field}',ev.date);$('#date_{$field}').datepicker('hide')});</script>
{$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_time -->
{$time_select} {$required}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_text -->
<textarea name="{$field}" style="width:{$x}px; height:{$y}px" rows="5" cols="50" class="ezf" id="{$field}" {$required_js}>{$value}</textarea> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_bbcode -->
{$bbc_area} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_wysiwyg -->
{$rte_area} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_select -->
{$data_select} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_radioh -->
{$data_radio} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_radiov -->
{$data_radio} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_checkbox -->
{$data_checkbox} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_multi -->
{$data_multi} {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_file -->
{$viewfile} <input type="file" name="{$field}" class="ezf" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_img -->
{$viewimg} <input type="file" name="{$field}" class="ezf" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_thumb -->
{$viewthumb} <input type="file" name="{$field}" class="ezf" {$required_js} /> {$required} {$help}
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_foot -->
<div class="ezform_foot"><button type="submit" class="btn btn-primary">Submit</button> <button type="reset" class="btn btn-danger">Reset</button></div>
</form>
<!-- ENDSECTION -->

<!-- BEGINSECTION ezform_send_email -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="en-us" dir="ltr">
<head>
<meta http-equiv="Content-Language" content="en-us" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Form Results</title>
<base href="{$site_url}" />
<style type="text/css">
body { font: 10pt Tahoma, Arial, Helvetica }
h1, h2 { font-family: Tahoma, Arial, Helvetica }
td { font: 10pt Tahoma, Arial, Helvetica }
td.form_title { font-weight: bold; background: #ccc; padding: 3px 10px 3px 5px }
td.form_value { background: #fff; padding: 3px 5px 3px 10px }
</style>
</head>

<body>
<h1>{$form_name}</h1>
<table border="1" style="border-collapse:collapse">
{$form_result}
</table>
<hr />
<p>You can also manage this form in <a href="{$site_url}/{$admin_url}">{$admin_url}</a></p>
</body>
</html>
<!-- ENDSECTION -->