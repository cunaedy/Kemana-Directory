<?php
/* this is a paypal payment module
   the expected returned variables from a payment module depends on the "$payment_cmd"

   if the $payment_cmd == init, then the module need to return additional fee (if any). For compatibility, you module may ignore such request, and do nothing.
   Returned variable should be contained in $payment_extra_fee.

   if the $payment_cmd == form, then the module have to returns the hidden form fields required by the payment gateway, if payment gateway doesn't need
   a payment gateway, the module should return an empty field.

   if the $payment_cmd == htp, then the module have to returns "how to pay" information, eg for Bank Transfer module, it returns bank name, bank account, etc. */


switch ($payment_cmd) {
    case 'form':
        // return the form's hidden fields to be submitted to payment gateway
        $form = array();

        // pay_redirect_to_gateway = true -> if the payment step should be made in another site (eg paypal.com),
        // false -> if the payment step can be made in the web site, or no payment step
        $form['pay_redirect_to_gateway'] = true;

        // the form action
        $form['action'] = 'https://www.paypal.com/cgi-bin/webscr';

        // the form method: get or post
        $form['method'] = 'post';
        if ($module_config['pay_paypal']['sandbox']) {
            $form['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

        // you need also to return how to pay information (if any)
        $form['txt_howtopay'] = '';

        // conversion rate
        if (empty($module_config['pay_paypal']['conversion_rate'])) {
            $cv = 1;
        } else {
            $cv = 1 / $module_config['pay_paypal']['conversion_rate'];
        }

        // the hidden fields, you are free to create the generator, as long as the returned $form['hidden'] filled with <input type="hidden" name="xxx" value="yyy" /> etc
        $hidden_val = array('rm' => 2,
                             'cmd' => '_xclick',
                             'business' => $module_config['pay_paypal']['bussiness'],
                             'item_name' => 'Your purchase at '.$config['site_name'].' - '.$summary['order_id'],
                             'currency_code' => $module_config['pay_paypal']['currency_code'],
                             'amount' => number_format(($summary['order_total']) * $cv, 2),
                             'tax' => 0,
                             'quantity' => '1',
                             'return' => $config['site_url'],
                             'cbt' => 'Click here to return to '.$config['site_name'],
                             'invoice' => $summary['order_id'],
                             'add' => '1');

        if ($module_config['pay_paypal']['ipn']) {
            $hidden_val['notify_url'] = $config['site_url'].'/module/pay_paypal/pp_ipn.php';
        }

        $form['hidden'] = '';
        foreach ($hidden_val as $key => $val) {
            $form['hidden'] .= "<input type=\"hidden\" name=\"$key\" value=\"$val\" />\n";
        }
    break;


    default:
        // htp (in this case as default case)
        // htp or how to pay information, eg for bank transfer, this may contain info on bank name, bank account, etc
        // not all payment module needs this info
        $txt_howtopay = '';
    break;
}
