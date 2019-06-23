<?php
$cmd = get_param('cmd');
switch ($cmd) {
    case 'save':
        $display_name = get_param('display_name');
        $be = get_param('be');
        $cc = get_param('cc');
        $ipn = get_param('ipn');
        $sb = get_param('sb');
        $cv = get_param('cv');
        $da = get_param('da');
        if (!empty($display_name)) {
            sql_query("UPDATE ".$db_prefix."module SET mod_name='$display_name' WHERE mod_id='pay_paypal' LIMIT 1");
        }
        update_mod_config('pay_paypal', 'bussiness', $be);
        update_mod_config('pay_paypal', 'currency_code', $cc);
        update_mod_config('pay_paypal', 'ipn', $ipn);
        update_mod_config('pay_paypal', 'sandbox', $sb);
        update_mod_config('pay_paypal', 'conversion_rate', $cv);
        update_mod_config('pay_paypal', 'direct_approval', $da);

        admin_die('admin_ok');
    break;


    default:
        // uses module_ez_config template
        $tpl = load_tpl('var', $tpl_section['module_ez_config']);

        // load the configuration values
        $row = sql_qquery("SELECT * FROM ".$db_prefix."module WHERE mod_id='pay_paypal' LIMIT 1");

        // init some stuffs
        $row['config_title'] = 'Payment Module: Paypal (IPN)';
        $row['mod_id'] = 'pay_paypal';
        $row['hidden_values'] = create_hidden_form('what', 'module').create_hidden_form('mod_id', $row['mod_id']).create_hidden_form('cmd', 'save');

        // 1. configuration items
        $help = ' <span class="glyphicon glyphicon-info-sign help"  rel="#tips" title="If your main currency is not supported by Paypal, you can convert it to a PayPal supported currency by entering the conversion rate here. Eg: INR 0.016/USD. Otherwise, leave it empty or enter 1."></span> ';
        $help2 = ' <span class="glyphicon glyphicon-info-sign help"  rel="#tips" title="Auto upgrade the listing when PayPal approve that payment."></span> ';
        $items = array(
            array('config_label' => 'Display name', 'config_value' => create_varchar_form('display_name', $row['mod_name'])),
            array('config_label' => 'Bussiness email', 'config_value' => create_varchar_form('be', $module_config['pay_paypal']['bussiness'])),
            array('config_label' => 'Currency code', 'config_value' => create_varchar_form('cc', $module_config['pay_paypal']['currency_code'])),
            array('config_label' => 'Currency conversion rate', 'config_value' => $lang['l_cur_name'].' '.create_varchar_form('cv', $module_config['pay_paypal']['conversion_rate'], 5).'/'.$module_config['pay_paypal']['currency_code'].$help),
            array('config_label' => 'Enable IPN?', 'config_value' => create_radio_form('ipn', $yesno, $module_config['pay_paypal']['ipn'])),
            array('config_label' => 'Auto upgrade on IPN Confirmation?', 'config_value' => create_radio_form('da', $yesno, $module_config['pay_paypal']['direct_approval']).$help2),
            array('config_label' => 'Enable Sandbox mode?', 'config_value' => create_radio_form('sb', $yesno, $module_config['pay_paypal']['sandbox']))
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
