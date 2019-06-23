<?php
$cmd = get_param('cmd');
switch ($cmd) {
    case 'save':
        $display_name = get_param('display_name');
        $bn = get_param('bn');
        $ba = get_param('ba');
        $bc = get_param('bc');
        $ac = get_param('ac');
        $ah = get_param('ah');
        if (!empty($display_name)) {
            sql_query("UPDATE ".$db_prefix."module SET mod_name='$display_name' WHERE mod_id='pay_bank' LIMIT 1");
        }
        update_mod_config('pay_bank', 'bankname', $bn);
        update_mod_config('pay_bank', 'bankaddress', $ba);
        update_mod_config('pay_bank', 'bankcode', $bc);
        update_mod_config('pay_bank', 'account', $ac);
        update_mod_config('pay_bank', 'holder', $ah);
        /* sql_query ("UPDATE ".$db_prefix."module_config SET config_value='$bn' WHERE mod_id='pay_bank' AND config_id='bankname' LIMIT 1");
        sql_query ("UPDATE ".$db_prefix."module_config SET config_value='$ba' WHERE mod_id='pay_bank' AND config_id='bankaddress' LIMIT 1");
        sql_query ("UPDATE ".$db_prefix."module_config SET config_value='$bc' WHERE mod_id='pay_bank' AND config_id='bankcode' LIMIT 1");
        sql_query ("UPDATE ".$db_prefix."module_config SET config_value='$ac' WHERE mod_id='pay_bank' AND config_id='account' LIMIT 1");
        sql_query ("UPDATE ".$db_prefix."module_config SET config_value='$ah' WHERE mod_id='pay_bank' AND config_id='holder' LIMIT 1"); */
        admin_die('admin_ok');
    break;


    default:
        // uses module_ez_config template
        $tpl = load_tpl('var', $tpl_section['module_ez_config']);

        // load the configuration values
        $row = sql_qquery("SELECT * FROM ".$db_prefix."module WHERE mod_id='pay_bank' LIMIT 1");

        // init some stuffs
        $row['config_title'] = 'Payment Module: Bank Wire Transfer';
        $row['mod_id'] = 'pay_bank';
        $row['hidden_values'] = create_hidden_form('what', 'module').create_hidden_form('mod_id', $row['mod_id']).create_hidden_form('cmd', 'save');

        // 1. configuration items
        $items = array(
            array('config_label' => 'Display name', 'config_value' => create_varchar_form('display_name', $row['mod_name'])),
            array('config_label' => 'Bank name', 'config_value' => create_varchar_form('bn', $module_config['pay_bank']['bankname'])),
            array('config_label' => 'Bank address', 'config_value' => create_varchar_form('ba', $module_config['pay_bank']['bankaddress'])),
            array('config_label' => 'Bank code', 'config_value' => create_varchar_form('bc', $module_config['pay_bank']['bankcode'])),
            array('config_label' => 'Account number', 'config_value' => create_varchar_form('ac', $module_config['pay_bank']['account'])),
            array('config_label' => 'Account holder', 'config_value' => create_varchar_form('ah', $module_config['pay_bank']['holder']))
        );

        // 2. create block of items
        $row['block_configuration'] = '';
        foreach ($items as $k => $v) {
            $row['block_configuration'] .= quick_tpl($tpl_block['configuration'], $v);
        }

        // output
        $txt['main_body'] = quick_tpl($tpl, $row);
    break;
}
