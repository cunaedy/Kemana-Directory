<form method="post" action="qe_config_process.php" enctype="multipart/form-data" name="qe_config">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> Primary Settings</div>
		<div class="panel-body">
			<ul id="qeconfig" class="nav nav-pills">
				<li class="active"><a href="#1" class="current" data-toggle="tab"><span class="glyphicon glyphicon-home"></span> Site Info</a></li>
				<li><a href="#2" data-toggle="tab">Engine Settings</a></li>
				<li><a href="#3" data-toggle="tab">Look &amp; Feel</a></li>
				<li><a href="#4" data-toggle="tab">API</a></li>
				<li><a href="#5" data-toggle="tab">Advanced</a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="tab-pane active" id="1">
			   <table width="100%" border="0" cellpadding="3" cellspacing="0" class="table table-form" id="result">
					<tr>
						<td class="adminbg_h" colspan="2">Site Configuration</td>
					</tr>
					<tr>
						<td class="adminbg_c" colspan="2">Site Information</td>
					</tr>
					<tr>
						<td width="40%">Site URL / Domain Name</td>
						<td width="60%"><input type="text" name="site_url" size="35" maxlength="255" value="{$site_url}" />
							<span class="glyphicon glyphicon-info-sign help tips" title="End without '/' Don't change if you aren't sure. Don't forget the www prefix (if needed)! HTTPS recommended."></span></td>
					</tr>
					<tr>
						<td>Absolute Path</td>
						<td><input type="text" name="abs_path" size="35" maxlength="255" value="{$abs_path}" />
							<span class="glyphicon glyphicon-info-sign help tips" title="End without &#39;/&#39;. Use / instead of \. Don't change if you aren't sure."></span></td>
					</tr>
					<tr>
						<td>Site Name</td>
						<td><input type="text" name="site_name" size="35" maxlength="255" value="{$site_name}" /></td>
					</tr>
					<tr>
						<td>Site Slogan</td>
						<td><input type="text" name="site_slogan" size="35" maxlength="255" value="{$site_slogan}" /></td>
					</tr>
					<tr>
						<td>Site Description</td>
						<td><input type="text" name="site_description" size="35" maxlength="255" value="{$site_description}" /></td>
					</tr>
					<tr>
						<td>Site Keywords</td>
						<td><input type="text" name="site_keywords" size="35" maxlength="255" value="{$site_keywords}" />
						<span class="glyphicon glyphicon-info-sign help tips" title="Useful for Search Engine Optimization."></span></td>
					</tr>
					<tr>
						<td>Site Email Address </td>
						<td><input type="text" name="site_email" size="35" maxlength="255" value="{$site_email}" /></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="adminbg_c" colspan="2">Site Address</td>
					</tr>
					<tr>
						<td>Address (line 1)</td>
						<td><input type="text" name="site_address" size="50" maxlength="255" value="{$site_address}" /></td>
					</tr>
					<tr>
						<td>Address (line 2)</td>
						<td><input type="text" name="site_address2" size="50" maxlength="255" value="{$site_address2}" /></td>
					</tr>
					<tr>
						<td>City</td>
						<td><input type="text" name="site_city" size="15" maxlength="255" value="{$site_city}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>Post / Zip Code </td>
						<td><input type="text" name="site_zip" size="9" maxlength="255" value="{$site_zip}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>County / State</td>
						<td><input type="text" name="site_state" size="35" maxlength="255" value="{$site_state}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>Country</td>
						<td>{$country_select}</td>
					</tr>
					<tr>
						<td>Telephone</td>
						<td><input type="text" name="site_phone" size="12" maxlength="255" value="{$site_phone}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>Fax</td>
						<td><input type="text" name="site_fax" size="12" maxlength="255" value="{$site_fax}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>Mobile / Cell Phone Number </td>
						<td><input type="text" name="site_mobile" size="12" maxlength="255" value="{$site_mobile}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>Close this Site</td>
						<td>{$close_select}
						<span class="glyphicon glyphicon-info-sign help tips" title="Temporarily close your site. A message will be displayed to any visitors suggesting that they should try visiting again soon."></span></td>
					</tr>
					<tr>
						<td>Debug Mode</td>
						<td>{$debug_select}
						<span class="glyphicon glyphicon-info-sign help tips" title="Enable debug mode to show errors, and see developer oriented information."></span></td>
					</tr>
			   </table>
			</div>

			<div class="tab-pane" id="2">
			   <table width="100%" cellpadding="3" cellspacing="0" class="table table-form" id="result1">
					<tr>
						<td class="adminbg_h" colspan="2">Engine Settings </td>
					</tr>
					<tr>
						<td class="adminbg_c" colspan="2">Locale</td>
					</tr>
					<tr>
						<td width="40%">Default Language</td>
						<td width="60%">{$default_lang_select}
							<span class="glyphicon glyphicon-info-sign help tips" title="You can add more language in Tools &gt; Language Editor."></span></td>
					</tr>
					<tr>
						<td>National Weight Name</td>
						<td><input type="text" name="weight_name" size="2" value="{$weight_name}" class="narrow_input" />
							<span class="glyphicon glyphicon-info-sign help tips" title="Enter the units you use to measure Weight. For example: kgs, lbs, units, sets, items, etc."></span></td>
					</tr>
					<tr>
						<td>National Currency Name</td>
						<td><input type="text" name="num_curr_name" size="20" value="{$num_curr_name}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>National Currency Symbol</td>
						<td><input type="text" name="num_currency" size="5" value="{$num_currency}" class="narrow_input" />
							<span class="glyphicon glyphicon-info-sign help tips" title="Use html escaped symbols.<br />Eg, for &pound; use &amp;&#173;pound;. For &euro; use &amp;&#173;euro;"></span></td>
					</tr>
					<tr>
						<td>Decimal Digits</td>
						<td>{$num_decimals_select}
							<span class="glyphicon glyphicon-info-sign help tips" title="The number of decimal places used after the decimal seperator in your currency. For example, a number of 12.3456, will be displayed 12.35 if you enter 2."></span></td>
					</tr>
					<tr>
						<td>Decimal Pointer</td>
						<td>{$num_dec_point_select}
							<span class="glyphicon glyphicon-info-sign help tips" title="The pointer used to seperate the full units and the decimal digits."></span></td>
					</tr>
					<tr>
						<td>Thousands Separator</td>
						<td>{$num_thousands_sep_select}
							<span class="glyphicon glyphicon-info-sign help tips" title="The pointer used to seperate every three figures. For example, a number of 1234, will be displayed 1 234 if you use blank space."></span></td>
					</tr>
					<tr>
						<td>Location of Currency Symbol</td>
						<td>{$curr_pos_select}</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="adminbg_h" colspan="2">User Management</td>
					</tr>
					<tr>
						<td>Registration Requires Email Confirmation</td>
						<td>{$active_radio}
							<span class="glyphicon glyphicon-info-sign help tips" title="Determine if you want newly registered users to confirm their email address by sending an activation email."></span></td>
					</tr>
					<tr>
						<td>Log All Outgoing Emails?</td>
						<td>{$mailog_radio}
							<span class="glyphicon glyphicon-info-sign help tips" title="This will log all outgoing email sent via qEngine, Some private emails will not be logged."></span></td>
					</tr>
					<tr>
						<td>Log All Modifications?</td>
						<td>{$qadmin_log_radio}
							<span class="glyphicon glyphicon-info-sign help tips" title="This will log all modification made via qEngine ACP (script dependent)."></span></td>
					</tr>
					<tr>
						<td>Use Detailed Logs?</td>
						<td>{$qadmin_detail_log_radio}
							<span class="glyphicon glyphicon-info-sign help tips" title="Enable detailed log to store all previous values so you can compare &amp; restore them easily. May produce a very big log table!"></span></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="adminbg_h" colspan="2">Site Optimisation </td>
					</tr>
					<tr>
						<td>Enable Gzip Header</td>
						<td>{$enable_gzip_select}
							<span class="glyphicon glyphicon-info-sign help tips" title="A GZIP header will speed up the downloads of your site by compressing the whole page before having it sent to the users."></span></td>
					</tr>
					<tr>
						<td>Disable Browser Cache?</td>
						<td>{$disable_browser_cache}
						<span class="glyphicon glyphicon-info-sign help tips" title="Enabling browser cache may increase site performance."></span></td>
					</tr>
					<tr>
						<td>Enable Search Engine Friendly URLS (SEF URL)</td>
						<td>{$enable_adp_select}
							<span class="glyphicon glyphicon-info-sign help tips" title="Enabling ADP will make your URL be search engine friendly. Remember to rename /_htaccess file to /.htaccess."></span>
					  </td>
					</tr>
					<tr>
						<td>Default file extension for SEF URL</td>
						<td><input type="text" name="adp_extension" size="5" value="{$adp_extension}" class="narrow_input" />
							<span class="glyphicon glyphicon-info-sign help tips" title="SEF URL will generate virtual files. Set your desired file extension. This only affect new pages, existing pages will not be affected."></span>
					  </td>
					</tr>
					<tr>
						<td>Regenerate Script Cache</td>
						<td>Every <input type="text" name="cache" size="5" value="{$cache}" class="narrow_input" /> seconds
							<span class="glyphicon glyphicon-info-sign help tips" title="qEngine stores cache to improve performance. Enter 0 to disable or when developing your site."></span></td>
					</tr>
					<tr>
						<td>Allow these files to be uploaded</td>
						<td><input type="text" name="allowed_file" size="50"  maxlength="255" value="{$allowed_file}" />
							<span class="glyphicon glyphicon-info-sign help tips" title="To improve site security, your users should not be able to upload some file types for custom field."></span></td>
					</tr>
				</table>
			</div>

			<div class="tab-pane" id="3">
			   <table width="100%" border="0" cellpadding="3" cellspacing="0" class="table table-form" id="result2">
					<tr>
						<td class="adminbg_h" colspan="2">Look &amp; Feel </td>
					</tr>
					<tr>
						<td class="adminbg_c" colspan="2">Company Logo</td>
					</tr>
					<tr>
						<td width="40%">Logo</td>
						<td width="60%">{$company_logo}<br /><input type="file" name="company_logo" /></td>
					</tr>
					<tr>
						<td>Small Logo (recommended 152x152 pixels, 24-bit transparent PNG)</td>
						<td>{$favicon}<br /><input type="file" name="favicon" /></td>
					</tr>
					<tr>
						<td>Watermark</td>
						<td>{$watermark}
							<!-- BEGINIF $isWatermark -->
							<div><a href="qe_config.php?cmd=del_watermark"><span class="glyphicon glyphicon-remove"></span> Remove File</a></div>
							<!-- ELSE -->
							<div><input type="file" name="watermark_file" /></div>
							<!-- ENDIF -->
							<span class="glyphicon glyphicon-info-sign help tips" title='All uploaded images will be watermarked with this image. Recommended watermark file: 24-bit transparent PNG @ 100x100 px'></span>
						</td>

					</tr>
					<tr>
						<td>Watermark Position</td>
						<td>{$watermark_pos_select}</td>
					</tr>

					<tr>
						<td class="adminbg_c" colspan="2">Layout</td>
					</tr>
					<tr>
						<td>Template</td>
						<td>{$skin_select}</td>
					</tr>
					<tr>
						<td>Use WYSIWYG Editor in Admin Control Panel</td>
						<td>{$wysiwyg_select}</td>
					</tr>
					<tr>
						<td>Enable Mobile Version Skin?</td>
						<td>{$mobile_version}
							<span class="glyphicon glyphicon-info-sign help tips" title="Default skin of qEngine uses responsive layout that adapt to any devices (PCs, tablets, phones). But if you prefer to use different skin for mobile, please enable this setting, and put your mobile skin files in /skins/_mobile folder."></span></td>
					</tr>
					<tr>
						<td>Display member only content in page list?</td>
						<td>{$allow_locked_page_radio}
							<span class="glyphicon glyphicon-info-sign help tips" title="Choose Yes to display the content title in page list. Contents still not visible to guests."></span></td>
					</tr>
					<tr>
						<td>Breadcrumbs Separator</td>
						<td><input type="text" name="cat_separator" size="3" value="{$cat_separator}" class="narrow_input" /></td>
					</tr>
					<tr>
						<td>Items per page</td>
						<td><input type="text" name="list_ipp" size="3" value="{$list_ipp}" class="narrow_input" /> items
							<span class="glyphicon glyphicon-info-sign help tips" title="The number of items that can be displayed on every page."></span></td>
					</tr>
					<tr>
						<td class="adminbg_c" colspan="2">Graphics</td>
					</tr>
					<tr>
						<td>GD version</td>
						<td>{$gd_select} <span class="glyphicon glyphicon-info-sign help tips" title="Confirm which version of GD Library you have installed. Be sure to know which version of GD you have. See your PHP info for more information"></span></td>
					</tr>
					<tr>
						<td>Image Optimizer Quality</td>
						<td>{$optimizer_select} <span class="glyphicon glyphicon-info-sign help tips" title="Requires GD version 2 or higher."></span></td>
					</tr>
					<tr>
						<td>Thumbnail Quality</td>
						<td>{$thumb_quality_select} <span class="glyphicon glyphicon-info-sign help tips" title="Use this to set the quality of your product images. High quality images are larger in size."></span></td>
				    </tr>
					<tr>
						<td>Thumbnail Size</td>
						<td><input type="text" name="thumb_size" size="6" value="{$thumb_size}" class="narrow_input" /> pixels</td>
					</tr>
				  </table>
			</div>

			<div class="tab-pane" id="4">
				<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table table-form" id="result3">
					<tr>
						<td class="adminbg_h" colspan="2">Email Server Configuration</td>
					</tr>
					<tr>
						<td width="40%">Send Emails Using SMTP Server?</td>
						<td width="60%">{$smtp_email}
							<span class="glyphicon glyphicon-info-sign help tips" title="Choose Yes to send email using your own SMTP server. Choose No to use internal PHP mailer service."></span></td>
					</tr>
					<tr>
						<td>SMTP Authentication Type</td>
						<td>{$smtp_secure}</td>
					</tr>
					<tr>
						<td>SMTP Server</td>
						<td><input type="text" size="20" name="smtp_server" value="{$smtp_server}" /></td>
					</tr>
					<tr>
						<td>SMTP Port</td>
						<td><input type="text" size="5" name="smtp_port" value="{$smtp_port}" class="narrow_input" />
							<span class="glyphicon glyphicon-info-sign help tips" title="Usually: 25 - for No Authentication, 465 - for SSL, 587 or 995 - for TLS."></span></td>
					</tr>
					<tr>
						<td>SMTP Username</td>
						<td><input type="text" size="20" name="smtp_user" value="{$smtp_user}" /></td>
					</tr>
					<tr>
						<td>SMTP Password</td>
						<td><input type="text" size="20" name="smtp_passwd" value="{$smtp_passwd}" /></td>
					</tr>
					<tr>
						<td>SMTP Sender Email</td>
					<td><input type="text" size="20" name="smtp_sender" value="{$smtp_sender}" /></td>
					</tr>
					<tr>
						<td class="adminbg_h" colspan="2">Social Media Integration</td>
					</tr>
					<tr>
						<td width="40%">Enable Facebook Like &amp; Share?</td>
						<td width="60%">{$facebook_like}</td>
					</tr>
					<tr>
						<td width="40%">Replace qEngine Comments with Facebook Comments?</td>
						<td width="60%">{$facebook_comment}
							<span class="glyphicon glyphicon-info-sign help tips" title="Some features may rely on qEngine comments." ></span></td>
					</tr>
					<tr>
						<td width="40%">Enable Twitter Share (tweet)?</td>
						<td width="60%">{$twitter_share}</td>
					</tr>
					<tr>
						<td class="adminbg_h" colspan="2">Other Integration</td>
					</tr>
					<tr>
						<td width="40%">Google Maps API Key</td>
						<td width="60%"><input type="text" size="5" name="gmap_api" value="{$gmap_api}" />
							<span class="glyphicon glyphicon-info-sign help tips" title="You only need this if you need to enable Google Map Picker function. Get your key from https://developers.google.com"></span></td>
					</tr>

				</table>
			</div>

			<div class="tab-pane" id="5">
				<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table table-form" id="result5">
					<tr>
						<td class="adminbg_h" colspan="2">Advanced Settings</td>
					</tr>
					<tr>
						<td colspan="2">Disabling these settings will increase your site performance by sacrificing some conveniences.</td>
					</tr>
					<tr>
						<td width="40%"><b>Module Manager</b></td>
						<td width="60%">
							<p>You can disable module manager to increase site performance. Module Manager is a function accessed from:
							ACP &gt; Modules &gt; Layout. If you disable this, you can't add/modify/delete any modules from ACP &gt; Modules &gt; Layout</p>
							<p>You can still add modules manually by using &lt;-- BEGINMODULE --&gt;&lt;-- ENDMODULE --&gt;. See module documentation for more
							information.</p>
							<p>Enable module manager? {$module_man_radio}</p>
						</td>
					</tr>
					<tr>
						<td><b>Module Engine</b></td>
						<td>
							<p>You can disable module engine to increase site performance. If you don't need any modules, you can disable module engine altogether.</p>
							<p>Disabling module engine will remove module supports from your site, and you can't use modules anywhere.</p>
							<p>Enable module engine? {$module_engine_radio}</p>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<p align="center"><button type="submit" class="btn btn-primary">Submit</button> <button type="reset" class="btn btn-danger">Reset</button></p>
</form>

<script type="text/javascript">
$('#qeconfig').tab
</script>