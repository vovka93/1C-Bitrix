<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include dirname(__FILE__) . "/portmone.php";
$formValid = Portmone::isPaymentValid($GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID']);
if ($formValid == '') {
    $formFields = array(
        'payee_id'          => CSalePaySystemAction::GetParamValue("PORTMONE_USER_ID"),
        'shop_order_number' => "Order_" . $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID'] . "_" . time(),
        'bill_amount'       => $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['SHOULD_PAY'],
        'description'       => Portmone::getNameSite(),
        'success_url'       => CSalePaySystemAction::GetParamValue("PORTMONE_SUCCESS"),
        'failure_url'       => CSalePaySystemAction::GetParamValue("PORTMONE_CANCEL"),
        'lang'              => Portmone::getLanguage(),
        'encoding'          => 'UTF-8');
    $form = '
        <style>
            .sale-paysystem-wrapper {
                position: relative;
                padding: 24px 38px 24px 38px;
                margin: 0 -15px 0 0;
                border: 1px solid #3bc8f5;
                font: 14px "Helvetica Neue",Arial,Helvetica,sans-serif;
                color: #424956;
            }
            .sale-paysystem-button {
                display: inline-block;
                margin: 26px 10px 26px 0;
            }
        </style>
        <div class="sale-paysystem-wrapper">
            <span class="tablebodytext">
                ' . GetMessage('PORTMONE_PAYSYSTEM_1') . '
                <img src="' . BX_ROOT . '/images/portmone.paysystem/portmone-logo.svg" style="margin-left: 14px;" alt="Portmone logo" />
                <br>
                <br>
                ' . GetMessage('PORTMONE_PAYSYSTEM_2') . '  <b>' . number_format($GLOBALS['SALE_INPUT_PARAMS']['ORDER']['SHOULD_PAY'], 2, '.', '') . '</b> ' . GetMessage('PORTMONE_PAYSYSTEM_3') . '</span>';

    $form .= '<form action="' . Portmone::GATEWAY_URL . '" method="post">';
    foreach ($formFields as $key => $value) {
        $form .= "<input type='hidden' name='$key' value='$value'/>";
    }
    $form .= '
                <div class="sale-paysystem-yandex-button-container">
                    <span class="sale-paysystem-button">
                        <input type="submit" value="' . GetMessage('PORTMONE_PAY_TEXT') . '" class="btn btn-primary" />
                    </span>
                    <span class="sale-paysystem-yandex-button-descrition">' . GetMessage('PORTMONE_PAYSYSTEM_4') . '</span>
                </div>
                <p>
                    <span class="tablebodytext sale-paysystem-description">
                        ' . GetMessage('PORTMONE_PAYSYSTEM_5') . '
                    </span>
                </p>
                </form>
            </span>
        </div>
    ';
} else {
    $form = $formValid ;
}
echo $form;
?>