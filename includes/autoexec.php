<?php
// an autoexec for daily call

// remove unconfirmed listing after 24 hours
$twodays = convert_date('now', 'sql', -2);
$res = sql_query("SELECT idx FROM ".$db_prefix."listing WHERE item_status='T' AND item_date < '$twodays'");
while ($row = sql_fetch_array($res)) {
    remove_item($row['idx']);
}

// set expired S/P listings to R
sql_query("UPDATE ".$db_prefix."listing SET item_class='R' WHERE ((item_class='S') OR (item_class='P')) AND (item_valid_date < '$sql_today')");

// send email to soon-to-be expired S/P listing, limited to only 3 everytime
$sevendays = convert_date('now', 'sql', 7);
$res = sql_query("SELECT *, idx AS item_id FROM ".$db_prefix."listing WHERE ((item_class='S') OR (item_class='P')) AND (item_valid_date < '$sevendays') AND (expired_email='0') LIMIT 3");
while ($row = sql_fetch_array($res)) {
    $row = process_listing_info($row);
    $row['item_class'] = $listing_class_def[$row['item_class']];
    $blah = quick_tpl(load_tpl('mail', 'soon_expired'), $row);
    email($row['owner_email'], sprintf($lang['l_mail_expired_subject'], $row['item_title']), $blah, 1, 1, 1);
    sql_query("UPDATE ".$db_prefix."listing SET expired_email='1' WHERE idx='$row[item_id]' LIMIT 1");
}

// anymore soon-to-be expired? if yes, don't stop autoexec now
$foo = sql_qquery("SELECT COUNT(*) AS c FROM ".$db_prefix."listing WHERE ((item_class='S') OR (item_class='P')) AND (item_valid_date < '$sevendays') AND (expired_email='0') LIMIT 1");

if ($foo['c']) {
    $ok = false;
}
