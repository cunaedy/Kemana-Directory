<?php
// part of qEngine
require './../includes/admin_init.php';
admin_check(5);
$table = 'qe_order';
$mode = 'ver'; // sql || hor || ver
$res = sql_query("SELECT * FROM $table");
$fields = mysqli_num_fields($res);
$rows = mysqli_num_rows($res);
echo "Your '".$table."' table has ".$fields." fields and ".$rows." records <br />\n";
echo "Copy & paste the source to create form<hr />\n";

switch ($mode) {
    case 'ver':
        echo "  <table border=\"0\" width=\"100%\" class=\"admin_tbl\">\n";

        for ($i = 0; $i < $fields; $i++) {
            $tbl = mysqli_fetch_field_direct($res, $i);
            $type  = $tbl->type;
            $name  = $tbl->name;
            $len   = $tbl->max_length;
            $flags = $tbl->flags;

            echo "      <tr>\n";
            echo "        <td>".ucwords(strtolower(str_replace('_', ' ', $name)))."</td>\n";

            if (($type == 'blob') || ($type == 'text')) {
                echo "        <td><textarea cols=\"50\" rows=\"3\" name=\"$name\">{\$$name}</textarea></td>\n";
                echo "      </tr>\n";
            } elseif ($type == 'date') {
                echo "        <td><input type=\"text\" size=\"20\" name=\"$name\" class=\"dateBox\" maxlength=\"12\" value=\"{\$$name}\" />\n";
                echo "        <img src=\"../skins/_admin/images/calendar.gif\" alt=\"calendar\" onclick=\"displayCalendar(document.forms['form'].$name,'dd-mm-yyyy',this)\" class=\"calendar\" /></td>\n";
                echo "      </tr>\n";
            } else {
                if ($len > 50) {
                    $l = 50;
                } else {
                    $l = $len;
                }
                echo "        <td><input type=\"text\" size=\"$l\" name=\"$name\" maxlength=\"$len\" value=\"{\$$name}\" /></td>\n";
                echo "      </tr>\n";
            }
        }
        echo "  </table>";
    break;


    case 'hor':
        echo "  <table border=\"0\" width=\"100%\" class=\"admin_tbl\">\n";
        echo "      <tr>\n";

        // create titles
        for ($i = 0; $i < $fields; $i++) {
            $tbl = mysqli_fetch_field_direct($res, $i);
            $name  = $tbl->name;
            echo "        <td class=\"adminbg_c\">".ucwords(strtolower(str_replace('_', ' ', $name)))."</td>\n";
        }

        echo "      </tr>\n";
        echo "      <tr>\n";

        // create form
        for ($i = 0; $i < $fields; $i++) {
            $tbl = mysqli_fetch_field_direct($res, $i);
            $type  = $tbl->type;
            $name  = $tbl->name;
            $len   = $tbl->max_length;
            $flags = $tbl->flags;

            if (($type == 'blob') || ($type == 'text')) {
                echo "        <td valign=\"top\"><textarea cols=\"50\" rows=\"3\" name=\"$name\">{\$$name}</textarea></td>\n";
            } elseif ($type == 'date') {
                echo "        <td valign=\"top\"><input type=\"text\" size=\"12\" name=\"$name\" class=\"dateBox\" maxlength=\"12\" value=\"{\$$name}\" />\n";
                echo "        <img src=\"../skins/_admin/images/calendar.gif\" alt=\"calendar\" onclick=\"displayCalendar(document.forms['form'].$name,'dd-mm-yyyy',this)\" class=\"calendar\" /></td>\n";
            } else {
                if ($len > 50) {
                    $l = 50;
                } else {
                    $l = $len;
                }
                echo "        <td valign=\"top\"><input type=\"text\" size=\"$l\" name=\"$name\" maxlength=\"$len\" value=\"{\$$name}\" /></td>\n";
            }
        }

        echo "      </tr>\n";
        echo "  </table>";
    break;

    case 'get':
    case 'post':
        for ($i = 0; $i < $fields; $i++) {
            $tbl = mysqli_fetch_field_direct($res, $i);
            $type  = $tbl->type;
            $name  = $tbl->name;
            $len   = $tbl->max_length;
            $flags = $tbl->flags;
            echo "\$$name = ".$mode."_param ('$name');<BR />";
        }
    break;

    case 'sql':
        echo "INSERT INTO $table SET<BR />";
        for ($i = 0; $i < $fields; $i++) {
            $tbl = mysqli_fetch_field_direct($res, $i);
            $type  = $tbl->type;
            $name  = $tbl->name;
            $len   = $tbl->max_length;
            $flags = $tbl->flags;
            echo "$name = '\$$name', ";
        }
        echo '"';
    break;

    case 'modcfg':
        $a = $b = $c = '';
        $mod_id = 'ship_pickup';
        $res = sql_query("SELECT * FROM ".$db_prefix."module_config WHERE mod_id='$mod_id'");
        while ($row = sql_fetch_array($res)) {
            $a .= "\$$row[config_id] = get_param ('$row[config_id]');<br />";
            $b .= "sql_query (\"UPDATE \".\$db_prefix.\"module_config SET config_value='\$$row[config_id]' WHERE mod_id='\$mod_id' AND config_id='$row[config_id]' LIMIT 1\");<br />";
            $c .= "&lt;tr&gt;&lt;td class=\"adminbg_c\"&gt;Sometxt&lt;/th&gt;&lt;td&gt;'.create_varchar_form ('$row[config_id]', \$module_config[\$mod_id]['$row[config_id]']).'&lt;/td&gt;&lt;/tr&gt;<br />";
        }
        echo "<pre>$a$b$c</pre>";
    break;
}

/* REVERT BACK FROM qadmin_def TO MySQL table
print_r ($qadmin_def);
echo '<hr />';

echo "CREATE TABLE `$qadmin_cfg[table]` (<br />";
foreach ($qadmin_def as $k => $v)
{
    if ($v['field'] == 'idx')
        echo "`idx` int(10) unsigned NOT NULL AUTO_INCREMENT,<br/>";
    elseif ($v['type'] != 'div')
    {
        echo "`$v[field]` ";
        if (($v['type'] == 'varchar') || ($v['type'] == 'echo') || ($v['type'] == 'hidden') || ($v['type'] == 'thumb'))
        {
            $v['size'] = empty ($v['size']) ? 255 : $v['size'];
            echo "varchar($v[size]) NOT NULL";
        }
        elseif ($v['type'] == 'select') echo "int(10) unsigned NOT NULL";
        elseif ($v['type'] == 'date') echo "date NOT NULL";
        elseif ($v['type'] == 'text') echo "text NOT NULL";
        else echo "???";
        echo ",<br />";
    }

}
echo "PRIMARY KEY (`idx`)<br />
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
echo '<hr />';


*/
