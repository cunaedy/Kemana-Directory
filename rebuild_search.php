<h1>Rebuilding Smart Search Database</h1>
<p>If this process timed out, please reload this page to continue.</p>

<?php
// very important file
require_once "./includes/user_init.php";

// remove remark to clean old database
sql_query("UPDATE ".$db_prefix."listing SET smart_search = ''");

$res = sql_query("SELECT idx FROM ".$db_prefix."listing WHERE smart_search = ''");
while ($row = sql_fetch_array($res)) {
    create_search_cache($row['idx']);
}
?>

<h1>DONE!</h1>