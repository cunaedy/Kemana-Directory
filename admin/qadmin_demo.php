<?php
echo 'to see this, please remove the following command. (see source)';
die;		// <-- remove this line -->

/*
Before you start using this, please create a database:

CREATE TABLE `<ADD_YOUR_DB_PREFIX_HERE>_news` (
  `news_id` int(10) unsigned NOT NULL auto_increment,
  `news_category` int(10) unsigned NOT NULL default '0',
  `news_date` date NOT NULL default '0000-00-00',
  `news_title` varchar(255) NOT NULL default '',
  `news_summary` varchar(255) NOT NULL default '',
  `news_body` text NOT NULL,
  `news_publish` char(1) NOT NULL default '',
  `news_file` varchar(255) NOT NULL default '',
  `news_img` varchar(255) NOT NULL default '',
  `news_thumb` varchar(255) NOT NULL default '',
  `news_display` char(1) NOT NULL default '',
  PRIMARY KEY  (`news_id`)
) TYPE=MyISAM

And flood with some junks:

for ($foo = 1; $foo <= 100; $foo++)
{
    $x = time () - 2592000;
    $y = time ();
    $c = mt_rand (1, 3);
    $d = date ('Y-m-d', mt_rand ($x, $y));
    $t = random_str (20, 0, 0);
    $s = random_str (50, 0, 0);
    $b = random_str (200, 0, 0);
    $p = mt_rand (0, 1);
    $i = mt_rand (0, 1);
    sql_query ("INSERT INTO ".$db_prefix."news VALUES ('', '$c', '$d', '$t', '$s', '$b', '$p', '', '', '', '$i')");
}
*/

// part of qEngine
require './../includes/admin_init.php';

for ($foo = 1; $foo <= 100; $foo++) {
    $x = time() - 2592000;
    $y = time();
    $c = mt_rand(1, 3);
    $d = date('Y-m-d', mt_rand($x, $y));
    $t = random_str(20, 0, 0);
    $s = random_str(50, 0, 0);
    $b = random_str(200, 0, 0);
    $p = mt_rand(0, 1);
    $i = mt_rand(0, 1);
    sql_query("INSERT INTO tmp_news VALUES ('', '$c', '$d', '$t', '$s', '$b', '$p', '', '', '', '$i')");
}
// some options
$opt = array(1=>'Option #1', 2=>'Option #2', 3=>'Option #3');

// data definitions
//
// IMPORTANT: use qadmin_build ('table_name') function to quickly generate data def
//
$qadmin_def['date']['title'] = 'News Date';					// title
$qadmin_def['date']['help'] = 'Enter news date';		// help info (optional)
$qadmin_def['date']['field'] = 'news_date';				// field name in table (also as field name in form)
$qadmin_def['date']['type'] = 'date';					// input type: varchar, date, text, wysiwyg, select, radio, radioh, radiov, file, img
$qadmin_def['date']['value'] = 'sql';					// value: could be anything or use 'sql' to extract from table (cmd = update only)
$qadmin_def['date']['format'] = 'date';					// search result formatting, you can assign: (empty) or not set / 'date' / 'numeric[,digit]' (eg: 'numeric,2' / 'currency'

$qadmin_def['id']['title'] = 'News ID';
$qadmin_def['id']['field'] = 'news_id';
$qadmin_def['id']['type'] = 'echo';
$qadmin_def['id']['value'] = 'sql';

// permalink :: string :: 255
$qadmin_def['permalink']['title'] = 'Permalink';		// a special data def, use permalink to display a text input to accept a permalink URL
$qadmin_def['permalink']['field'] = 'permalink';		// this def needs permalink config (see qadmin_cfg below)
$qadmin_def['permalink']['type'] = 'permalink';			// permalink must be unique (handled by system)
$qadmin_def['permalink']['size'] = 255;					// if user leave permalink empty, it will be determined by system
$qadmin_def['permalink']['value'] = 'sql';

$qadmin_def['title']['title'] = 'News Title';
$qadmin_def['title']['field'] = 'news_title';
$qadmin_def['title']['type'] = 'varchar';
$qadmin_def['title']['size'] = '255';					// field size, for varchar = width of field; for text & wysiwyg is in 'x,y'
$qadmin_def['title']['value'] = 'sql';
$qadmin_def['title']['required'] = true;				// must be filled!
$qadmin_def['title']['unique'] = true;					// entry must be unique (only available for varchar type)

$qadmin_def['sum']['title'] = 'Summary';
$qadmin_def['sum']['field'] = 'news_summary';
$qadmin_def['sum']['type'] = 'text';
$qadmin_def['sum']['value'] = 'sql';
$qadmin_def['sum']['index'] = true;
$qadmin_def['sum']['format'] = true;					// you can also define formatting for search result: default (optional), numeric, currency

$qadmin_def['file']['title'] = 'File';					// for file, img & thumb -> define folder location for upload in $qadmin_cfg
$qadmin_def['file']['help'] = 'Support file?';
$qadmin_def['file']['field'] = 'news_file';
$qadmin_def['file']['type'] = 'file';
$qadmin_def['file']['value'] = 'sql';

$qadmin_def['img']['title'] = 'Image';
$qadmin_def['img']['help'] = 'Where is the image?';
$qadmin_def['img']['field'] = 'news_img';
$qadmin_def['img']['type'] = 'img';
$qadmin_def['img']['rename'] = 'img';					// rename files to random char (to avoid duplicate name) -- also for thumb & img_resize
$qadmin_def['img']['value'] = 'sql';					// .. field size = min 20 chars. for: file, image & thumb type

$qadmin_def['thumb']['title'] = 'Thumbnail';
$qadmin_def['thumb']['help'] = '--dude, this is automatic thumbnail generator--';
$qadmin_def['thumb']['field'] = 'news_thumb';
$qadmin_def['thumb']['type'] = 'thumb';					// upload image & automatically create thumbnail
$qadmin_def['thumb']['value'] = 'sql';
$qadmin_def['thumb']['size'] = 200;						// optional, size of thumbnail

/* alternative for thumbnail -- if you don't need thumbnail feature, you can use RESIZE feature
$qadmin_def['thumb']['title'] = 'Image';
$qadmin_def['thumb']['help'] = '--dude, this is automatic image resizer --';
$qadmin_def['thumb']['field'] = 'news_thumb';
$qadmin_def['thumb']['type'] = 'img_resize';
$qadmin_def['thumb']['size'] = '100';	// output size (smart resize) -- empty = thumbnail size
$qadmin_def['thumb']['value'] = 'sql'; */

$qadmin_def['rte']['title'] = 'News Body';
$qadmin_def['rte']['field'] = 'news_body';
$qadmin_def['rte']['type'] = 'wysiwyg';
$qadmin_def['rte']['wysiwyg_pagebreak'] = true;			// enable pagebreak module for TinyMCE (ie: <!-- pagebreak -->)
$qadmin_def['rte']['size'] = '200,200';					// field size for text & wysiwyg is in 'x,y' (px)
$qadmin_def['rte']['value'] = 'sql';
$qadmin_def['rte']['index'] = true;

$qadmin_def['cat']['title'] = 'Category';
$qadmin_def['cat']['field'] = 'news_category';
$qadmin_def['cat']['type'] = 'select';
$qadmin_def['cat']['option'] = $opt;					// for multi, select & radio, $option is array();
$qadmin_def['cat']['value'] = 'sql';
/* YOU CAN also use editopt to use USER EDITABLE OPTIONS
replace: $qadmin_def['cat']['option'] = $opt;
with: $qadmin_def['cat']['editopt'] = 'SOME_ID'
then in USER script, use $opt = get_option ('SOME_ID') */

$qadmin_def['cat']['title'] = 'Category (another example)';
$qadmin_def['cat']['field'] = 'news_category';
$qadmin_def['cat']['type'] = 'multi';
$qadmin_def['cat']['option'] = $opt;					// you can also use edit_opt, similar to select
$qadmin_def['cat']['value'] = 'sql';

$qadmin_def['rad']['title'] = 'Display at home?';
$qadmin_def['rad']['field'] = 'news_display';
$qadmin_def['rad']['type'] = 'radio';
$qadmin_def['rad']['option'] = $yesno;					// you can also use edit_opt, similar to select
$qadmin_def['rad']['value'] = 'sql';

/* another type: mask
// with 'mask', we can use array (eg. $username_def) to mask result, eg. displaying user's real name instead of
// username.
// array $username_def[KEY] = VALUE;
// where KEY = username, VALUE = user real name
$qadmin_def['rad']['title'] = 'Username';
$qadmin_def['rad']['field'] = 'news_username';
$qadmin_def['rad']['type'] = 'mask';
$qadmin_def['rad']['option'] = $username_def;
$qadmin_def['rad']['value'] = 'sql';
*/

/* another type: img_set, image_set, img_series, image_series
// it needs a temporary varchar(255) field in table
// page_img_tmp :: string :: 255
$qadmin_def['page_img_tmp']['title'] = 'Additional Images';
$qadmin_def['page_img_tmp']['field'] = 'page_img_tmp';
$qadmin_def['page_img_tmp']['type'] = 'img_set';
$qadmin_def['page_img_tmp']['prefix'] = 'page_img';
$qadmin_def['page_img_tmp']['resize'] = 500;			// resize image to; or 0 = not resized
$qadmin_def['page_img_tmp']['thumb_size'] = 100;	// create thumbszie; 0 = thumb default
$qadmin_def['page_img_tmp']['value'] = 'sql';
*/

/* another type: hidden & static
hidden = form's hidden value
static = similar to echo + hidden, you can use ['option'] field to mask real value, or ignore ['option'] to display raw value
*/


/* ALTERNATE DATA DEF USING EZF SHORT METHOD
// using this format: 'title,field_id,type,size,required', eg: $foo[] = 'Name,uname,varchar,80,1';
// for "select" and "radio" use: 'title,field_id,type,size,required,option', eg: $foo[] = 'Name,uname,varchar,80,1,option_array';
// For explation, see /ezf_sample.php

$foo = array ();
$foo[] = 'Name,uname,varchar,80';
$foo[] = 'Company Name,cname,varchar,80';
$foo[] = 'Address,address,varchar,255';
$foo[] = 'Notes,notes,text,300*100';
$qadmin_def = qadmin_qbuild ($foo, TRUE);
*/

// general configuration ( * = optional )
$qadmin_cfg['table'] = 'tmp_news';					// table name
$qadmin_cfg['primary_key'] = 'news_id';						// table's primary key
$qadmin_cfg['primary_val'] = 'dummy';						// primary key value
$qadmin_cfg['ezf_mode'] = false;							// TRUE to use EZF mode (see ./_qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['ezd_mode'] = false;							// TRUE to use ezDesign mode (see ./qadmin_ez_mode.txt for more info), FALSE to use QADMIN *
$qadmin_cfg['template'] = 'default';						// template to use
# $qadmin_cfg['back'] = 'url.php';					// back button link, if omitted will use $qadmin_cfg['action'] value *
# $qadmin_cfg['header'] = 'Additional Header';				// additional header *
# $qadmin_cfg['footer'] = 'Additional Footer';				// additional footer *
# $qadmin_cfg['action'] = 'task.php?mod=media&run=cat.php';	// form action *
# $qadmin_cfg['post_process'] = '';							// file or function to open (redir) after processing: add, modify & remove *
                                                            // FOR FILE: must be ended with query string, eg: process.php?, process.php?cmd=do
                                                            // -- qadmin will add its vars: qadmin_cmd & qadmin_id
                                                            // -- so, process.php?cmd=do, will be called as process.php?cmd=do&qadmin_cmd=xyz&qadmin_id=pqr&qadmin_savenew=[0/1]
                                                            // FOR FUNC: it will call your function with 2 arguments: cmd & id
                                                            // -- eg: 'myfunc' will be called as 'myfunc ($cmd, $id, $qadmin_savenew, $old_values, $new_values)'
$qadmin_cfg['send_to'] = '';								// enter an email @ddress, so script will also send an email containing the result. (only on ADD NEW)
$qadmin_cfg['send_subject'] = 'Email subject for send_to';

// permalink (hooray, finally qE has a proper SEF URL) -- see also permalink qadmin_def above
$qadmin_cfg['permalink_folder'] = '';						// virtual folder for permalink, eg: www.c97.net/virtual_folder/mypage.html, empty = no folder (eg: www.c97.net/mypage.html), end without / (optional)
$qadmin_cfg['permalink_script'] = 'page.php';				// script name for permalink to open, eg: page.php
$qadmin_cfg['permalink_source'] = 'page_title';				// the source field for permalink if user doesn't enter any permalink value, eg: page_title
$qadmin_cfg['permalink_param'] = 'cat';						// you can use this as extra field, it will be sent to your script AS-IS, eg: cat (optional)
// eg: using above sample vars, when your visitor enter a url: /example.php; then /permalink.php will be called, and it will search permalink db to find & open
// the real script: page.php, and send vars: $original_idx = 5 (your real page_id); $permalink_param = 'cat' (identifier);

// logging
$qadmin_cfg['enable_log'] = true;			// log all changes (add/edit/remove), default = from qe_config
$qadmin_cfg['detailed_log'] = true;			// store modification values (may be big!), default = from qe_config
$qadmin_cfg['log_title'] = 'page_title';	// qadmin field to be used as log title (empty = disable log, no matter other cfg's)

// folder configuration (qAdmin only stores filename.ext without folder location), ends without slash '/' - optional
$qadmin_cfg['file_folder'] = './../public/file';						// folder to place file upload (relative to /admin folder)
$qadmin_cfg['img_folder'] = './../public/image';						// folder to place image upload
$qadmin_cfg['thumb_folder'] = './../public/thumb';						// folder to place thumb (auto generated)

// search configuration
$qadmin_cfg['search_key'] =      'news_id,news_title,news_summary';		// list other key to search
// you can use + for sub result: 'news_id,news_title+date,news_summary';		// this will display news_title<br />date
$qadmin_cfg['search_key_mask'] = 'News ID,News Title,News Summary';		// mask other key

$qadmin_cfg['search_date_field'] = 'news_date';							// search by date field name *
$qadmin_cfg['search_start_date'] = true;								// show start date *
$qadmin_cfg['search_end_date'] = true;									// show end date *

# $qadmin_cfg['search_result_url'] = ",link.php?item_id=__KEY__,,";		// mask result by url *
# $qadmin_cfg['search_filterby'] = "news_publish='1',news_publish='0'";	// filter by sql_query (use , to separate queries) *
# $qadmin_cfg['search_filtermask'] = 'Published,Unpublished';				// mask filter *
# $qadmin_cfg['search_edit'] = 'external_editor.php?id=__KEY__';		// edit using qadmin or other external editor
                                                                        // __KEY__ will be replaced auto *
# $qadmin_cfg['search_edit_target'] = '_self';							// open external editor in [target] window: _self, _blank, _paret, custom, etc
# $qadmin_cfg['search_result_mask'] = ",title_def,,";					// mask result by array (title_def must be array,
                                                                        // with KEY corresponded to result *

// enable qadmin functions, which are: search, list, new, update & remove
$qadmin_cfg['cmd_default'] = 'list';							// if this script called without ANY parameter
$qadmin_cfg['cmd_search_enable'] = true;
$qadmin_cfg['cmd_list_enable'] = true;
$qadmin_cfg['cmd_new_enable'] = true;
$qadmin_cfg['cmd_update_enable'] = true;
$qadmin_cfg['cmd_remove_enable'] = true;						// PRO TIP: you can also remove several items by using a direct url: qadmin_demo.php?qadmin_cmd=remove_item&primary_val=1,2,3,4,5,6

// security *** qADMIN CAN NOT RUN IF admin_level NOT DEFINED ***
// Highest to lowest: 5, 4, 3, 2, 1
// higher level can access lower level features [ 4 should be good, 5 is too high and should be used in a very important area ]
$qadmin_cfg['admin_level'] = '4';

// form title
$qadmin_title['new'] = 'Add News';
$qadmin_title['update'] = 'Update News';
$qadmin_title['search'] = 'Search News';
$qadmin_title['list'] = 'News List';

// auto sql query generated by qAdmin: "SELECT * FROM table WHERE primary_key='primary_val' LIMIT 1"
// to overwrite >> $qadmin_cfg['sql_select'] = "SELECT * FROM ".$db_prefix."news WHERE news_id = '2' LIMIT 1"; <<
// auto sql & manual sql used only for cmd = 'update'

qadmin_manage($qadmin_def, $qadmin_cfg, $qadmin_title);


// NOTES:
// qadmin will create fields with unique ID of: [table_name]-[field_name], eg: qe_page-page_title. By default this ID is assigned to
// .. field's container instead of the field. EG: <td id="[field_id]"><input type="text" ..... ></td>
// .. to access the field's properties use jquery: $('#[field_id]>input'); eg: $('#qe_page-page_author>input').click(function () { alert ('Aaa!') });
