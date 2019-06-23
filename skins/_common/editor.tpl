<!-- BEGINIF $rte_mode == 'text' -->
 <textarea name="{$f_textarea}" style="width: {$f_width}px; height: {$f_height}px" wrap="virtual" rows="50" cols="5">{$f_html}</textarea>
<!-- ENDIF -->

<!-- BEGINIF $rte_mode == 'rte_init' -->
<script type="text/javascript" src="{$basedir}/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
// TinyMCE is (c)copyright Moxiecode Systems AB -- http://www.tinymce.com
tinymce.init({
	// General options
	selector : "textarea.mceRTEditor",
	tabfocus_elements : ":prev,:next",
	document_base_url : "{$site_url}/",
	relative_urls : false,
	remove_script_host : false,
	image_advtab: true,
	file_browser_callback: SimpleFileBrowser,
	plugins: "advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen pagebreak insertdatetime media table contextmenu paste emoticons textcolor",
	toolbar: "insertfile undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons"
});

function SimpleFileBrowser(field_name, url, type, win) {
  tinyMCE.activeEditor.windowManager.open({
	 file: '{$site_url}/{$l_admin_folder}/fman/imagelib.php?field=' + field_name + '&value=' + win.document.getElementById(field_name).value,
	 title: 'Simple File Manager',
	 width: 850,
	 height: 650,
	 resizable: "yes",
	 plugins: "media",
	 inline: "yes",
	 close_previous: "no"
  }, { window: win, input: field_name });
  return false;
}
</script>

<textarea name="{$f_textarea}" id="{$f_textarea}" class="mceRTEditor" style="width: {$f_width}px; height: {$f_height}px" rows="50" cols="5">{$f_html}</textarea>
<!-- ENDIF -->

<!-- BEGINIF $rte_mode == 'rte_multi' -->
<textarea name="{$f_textarea}" id="{$f_textarea}" class="mceRTEditor" style="width: {$f_width}px; height: {$f_height}px" rows="50" cols="5">{$f_html}</textarea>
<!-- ENDIF -->

<!-- BEGINIF $rte_mode == 'code_editor_init' -->
<script type="text/javascript" src="{$site_url}/misc/editarea/edit_area_full.js"></script>
<script type="text/javascript">
// Edit Area is (c)copyright Christophe Dolivet -- http://www.cdolivet.com/editarea
editAreaLoader.init({
	id: "{$f_textarea}",	// id of the textarea to transform
	start_highlight: true,	// if start with highlight
	allow_toggle: true,
	word_wrap: true,
	font_family: "Consolas,monospace",
	language: "en",
	syntax: "{$f_syntax}",
	is_editable: {$is_editable}
});
</script>

<textarea name="{$f_textarea}" id="{$f_textarea}" style="width: 100%; height: {$f_height}px" rows="50" cols="5">{$f_html}</textarea>
<!-- ENDIF -->

<!-- BEGINIF $rte_mode == 'code_editor_multi' -->
<script type="text/javascript">
// Edit Area is (c)copyright Christophe Dolivet -- http://www.cdolivet.com/editarea
editAreaLoader.init({
	id: "{$f_textarea}"	// id of the textarea to transform
	,start_highlight: true	// if start with highlight
	,allow_toggle: true
	,word_wrap: true
	,language: "en"
	,syntax: "html"
});
</script>
<textarea name="{$f_textarea}" id="{$f_textarea}" style="width: 100%; height: {$f_height}px" rows="50" cols="5">{$f_html}</textarea>
<!-- ENDIF -->