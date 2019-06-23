<!-- BEGINSECTION mod_the_title -->
<h1><a href="{$item_url_link}"><span class="glyphicon glyphicon-menu-left"></span></a> Comments on "{$title}"</h1>
<!-- ENDSECTION -->


<!-- BEGINSECTION mod_comment_box -->
<div id="qcomment_form">
	<form method="post" action="{$site_url}/task.php?mod=qcomment" name="qcomment_box" id="qcomment_box" class="form">
	<input type="hidden" name="save" value="1" />
	<input type="hidden" name="m" value="{$mod_id}" />
	<input type="hidden" name="i" value="{$item_id}" />
	<input type="hidden" name="t" value="{$item_title}" />
	<input type="hidden" name="u" value="{$item_url}" />
	<table class="table">
	 <tr><td colspan="2"><b>Your Comment</b></td></tr>
	 <tr><td width="20%">Username</td><td width="80%">{$current_user_id}</td></tr>
	<!-- BEGINIF $rating_box -->
	 <tr><td>Rating</td><td>{$rating_select}</td></tr>
	<!-- ENDIF -->
	 <tr><td>Title</td><td><input type="text" name="comment_title" size="61" maxlength="255" value="{$comment_title}" /></td></tr>
	 <tr><td>&nbsp;</td><td><textarea name="comment_body" style="max-width:500px;max-height:150px;width:100%;height:150px">{$comment_body}</textarea></td></tr>
	<!-- BEGINIF $captcha -->
	 <tr><td>Captcha</td><td><img src="visual.php" alt="captcha" /> <input type="text" name="visual" size="5" maxlength="5" required="required" /></td></tr>
	<!-- ENDIF -->
	 <tr><td colspan="2" align="center"><button type="submit" class="btn btn-primary">Send Comment</button></td></tr>
	</table>
	</form>
</div>
<script type="text/javascript">
	function init_comment_box (mod_id, item_id, item_title, box_title)
	{
		cform = document.forms['qcomment_box'];
		cform.m.value=mod_id;
		cform.i.value=item_id;
		cform.t.value=item_title;
		cform.comment_title.value=box_title;
		// cform.comment_title.setFocus();
	}
</script>
<!-- ENDSECTION -->


<!-- BEGINSECTION mod_no_comment_box -->
<hr />
<a name="qcomment_form"></a>
<p>You must be <a href="profile.php">logged in</a> to post a comment!<br />Or you have posted your comment.</p>

<script type="text/javascript">
function init_comment_box (mod_id, item_id, item_title, box_title)
{ return; }
</script>
<!-- ENDSECTION -->


<!-- BEGINSECTION mod_more_comment -->
<p><a href="task.php?mod=qcomment&amp;m={$mod_id}&amp;i={$item_id}&amp;t={$t}">More Comments/Post Your Own</a></p>
<!-- ENDSECTION -->


<!-- BEGINSECTION mod_helpful_js -->
<script type="text/javascript">
//<![CDATA[
function sendIt(fid, yesno)
{
	if (yesno == "yes")
		document.location = "task.php?mod=qcomment&helpful=1&comment_id="+fid+"&yes=1";
	else
		document.location = "task.php?mod=qcomment&helpful=1&comment_id="+fid+"&no=1";
}
//]]>
</script>
<!-- ENDSECTION -->


<!-- BEGINSECTION conc_item -->
<div class="conc_box" id="comment_{$comment_id}">
	<!-- BEGINIF $current_admin_level -->
	<div class="bg-warning">
		<a href="{$site_url}/{$l_admin_folder}/task.php?mod=qcomment&amp;run=edit.php&amp;id={$comment_id}" class="btn btn-xs" target="acp"><span class="glyphicon glyphicon-pencil" style="padding-left:10px" title="edit"></span></a>
		<a href="{$site_url}/task.php?mod=qcomment&amp;m=pagecomment&amp;trash={$comment_id}" data-ajax-success-callback="hide_comment" data-ajax-success-arg="{$comment_id}" class="simpleAjax btn btn-xs"><span class="glyphicon glyphicon-trash" style="padding-left:10px" title="remove"></span></a>
	</div>
	<!-- ENDIF -->
	<div class="conc_right">
		<div><b>{$comment_title}</b></div>
		<div>{$comment_body}</div>
	</div>
	<div class="conc_left">
		<span class="glyphicon glyphicon-user"></span> {$comment_user}
		<span class="glyphicon glyphicon-time" style="padding-left:10px"></span> {$comment_date}
		<a href="#qcomment_form" onclick="init_comment_box('conc','{$conc_id}','{$conc_title}','re:{$conc_title}')" class="btn btn-xs"><span class="glyphicon glyphicon-comment" style="padding-left:10px" title="reply"></span></a>
	</div>
	<div style="clear:both"></div>
</div>
<!-- ENDSECTION -->