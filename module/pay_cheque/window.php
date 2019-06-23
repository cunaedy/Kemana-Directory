<?php
if (($payment_cmd == 'form') || ($payment_cmd == 'htp')) {
    $txt_howtopay = '<p>&bull; Please make sure that your check is payable to '.$module_config['pay_cheque']['recipient'].'</p>';
    
    // no hidden field as it is not required
    $form['pay_redirect_to_gateway'] = false;
    $form['txt_howtopay'] = $txt_howtopay;
    $form['hidden'] = '';
}
