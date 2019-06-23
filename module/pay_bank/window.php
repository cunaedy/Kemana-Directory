<?php
if (($payment_cmd == 'form') || ($payment_cmd == 'htp')) {
    $txt_howtopay = '<p>Please transfer your funds to:</p>
	<ul><li>Bank Name: '.$module_config['pay_bank']['bankname'].'</li>
	<li>Bank Address: '.$module_config['pay_bank']['bankaddress'].'</li>
	<li>Bank Code: '.$module_config['pay_bank']['bankcode'].'</li>
	<li>Account Number: '.$module_config['pay_bank']['account'].'</li>
	<li>Account Holder: '.$module_config['pay_bank']['holder'].'</li></ul>
	<p>After funds are transferred, please confirm us by email or phone to process your orders.</p>';

    // no hidden field as it is not required
    $form['pay_redirect_to_gateway'] = false;
    $form['txt_howtopay'] = $txt_howtopay;
    $form['hidden'] = '';
}
