<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$l_language_short}" lang="{$l_language_short}" dir="{$l_direction}">
<head>
<meta http-equiv="Content-Language" content="{$l_language_short}" />
<meta http-equiv="Content-Type" content="text/html; charset={$l_encoding}" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Folder Tree</title>

<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_common/default.css" />
<link rel="stylesheet" type="text/css" href="{$site_url}/skins/_admin/style.css" />
<style type="text/css">
 body	{ margin: 10px; background:#fff }
</style>

<!-- BEGINIF $show_tree -->

<script type="text/javascript">
<!--
 function jumpto (dir)
 {
	parent.par_jumpto (dir);
 }

 function moveto (dir)
 {
 	parent.par_moveto (dir);
 }
-->
</script>
</head>

<body style="background-color:white">

 <!-- BEGINBLOCK tree -->
  <div>{$spacer}{$folder} {$name}</div>
 <!-- ENDBLOCK -->

<!-- ELSE -->

<script type="text/javascript">
<!--
 function par_jumpto (dir)
 {
	window.opener.location = "fileman.php?chdir="+dir;
	window.close();
 }

 function par_moveto (dir)
 {
	window.opener.location = "fileman.php?cmd=move&chdir={$chdir}&fn={$fn}&target="+dir;
	window.close();
 }
-->
</script>
</head>

<body style="background-color:ButtonFace">
 <small>{$message}<br />
 <b>{$where}</b></small>
 <center>
  <iframe src="tree.php?cmd={$cmd}&amp;chdir={$chdir}&amp;fn={$fn}&amp;show_tree=1" frameborder="0"
   style="padding:0; width:330px; height:390px; margin: 3px; border:solid 1px gray">
  </iframe>
 </center>
<!-- ENDIF -->

</body>

</html>