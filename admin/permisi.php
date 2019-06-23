<?php
// part of qEngine
/* CUSTOM PERMISSIONS
   To add your own permissions, follow these:
   1. Assuming your permission identifier is: my_own_permission
   2. By using a MySQL manager (eg PhpMyAdmin, or Adminer), add the key to 'qe_config' table, with the following settings:
      a. group_id = 'permisi'
      b. config_id = 'my_own_permission'
      c. config_value = leave it empty
   3. Open /includes/vars.php, add your identifier to $acp_permission_def for back end permission, or $user_permission_Def for front end permission.
      The format should be: array ('my_own_permission' => 'The description');
   4. You are done.

   To use the permission:
   1. In .php files, use permission_check ('my_own_permission');
   2. In .tpl files, use <!-- BEGINIF $is_allowed_my_own_permission -->Some HTML<!-- ENDIF -->

*/
require './../includes/admin_init.php';
admin_check('permisi');
$cmd = post_param('cmd');
switch ($cmd) {
    case 'save':
        $res = sql_query("SELECT * FROM ".$db_prefix."config WHERE group_id='permisi'");
        while ($row = sql_fetch_array($res)) {
            $foo = array();
            for ($i = 0; $i <= 5; $i++) {
                $foo[$i] = post_param($row['config_id'].'_'.$i);
            }
            $s = serialize($foo);
            sql_query("UPDATE ".$db_prefix."config SET config_value='$s' WHERE config_id='$row[config_id]' AND group_id='permisi' LIMIT 1");
        }

        admin_die('admin_ok');
    break;


    default:
        $tpl = load_tpl('adm', 'permisi.tpl');

        // title
        $txt['block_title'] = '';
        for ($i = 1; $i <= 5; $i++) {
            $row = array();
            $row['level'] = $i;
            $row['user_title'] = $lang['l_user_level_'.$i];
            $row['admin_title'] = $lang['l_admin_level_'.$i];
            $txt['block_title'] .= quick_tpl($tpl_block['title'], $row);
        }

        // get permissions from db
        $txt['block_admin_permisi'] = $txt['block_user_permisi'] = '';
        $res = sql_query("SELECT * FROM ".$db_prefix."config WHERE group_id='permisi' ORDER BY config_id");
        while ($row = sql_fetch_array($res)) {
            $val = unserialize($row['config_value']);

            if (array_key_exists($row['config_id'], $acp_permission_def)) {
                for ($i = 1; $i <= 5; $i++) {
                    $row['check'.$i] = create_tickbox_form($row['config_id'].'_'.$i, '', $val[$i]);
                }
                $row['permisi'] = $acp_permission_def[$row['config_id']];
                $txt['block_admin_permisi'] .= quick_tpl($tpl_block['admin_permisi'], $row);
            } elseif (array_key_exists($row['config_id'], $user_permission_def)) {
                for ($i = 0; $i <= 5; $i++) {
                    $row['check'.$i] = create_tickbox_form($row['config_id'].'_'.$i, '', $val[$i]);
                }
                $row['permisi'] = $user_permission_def[$row['config_id']];
                $txt['block_user_permisi'] .= quick_tpl($tpl_block['user_permisi'], $row);
            }
        }
        $txt['main_body'] = quick_tpl($tpl, $txt);
        flush_tpl('adm');
    break;
}
