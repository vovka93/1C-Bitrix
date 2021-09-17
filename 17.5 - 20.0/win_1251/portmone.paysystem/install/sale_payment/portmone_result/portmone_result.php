<?
if ($_SERVER["REQUEST_METHOD"] !== "POST") die();
if (!require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php")) die('prolog_before.php not found!');
if (CModule::IncludeModule('sale')) {
    if (empty($_POST)) {
        $callback = json_decode(file_get_contents("php://input"));
        $_POST = array();
        foreach ($callback as $key => $val) {
            $_POST[$key] = $val;
        }
    }
    include $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/portmone.paysystem/install/sale_payment/portmone/portmone.php";
    $ordArray   = explode("_", $_POST['SHOPORDERNUMBER']);
    $ORDER_ID   = $ordArray['1'];
    $params     = Portmone::getPaySystemId($ORDER_ID);
    $arOrder    = CSaleOrder::GetByID($ORDER_ID);

    $data = array(
        "method"            => "result",
        "payee_id"          => $params['PORTMONE_USER_ID'] ,
        "login"             => $params['PORTMONE_USER_LOGIN'] ,
        "password"          => $params['PORTMONE_USER_PASSWORD'] ,
        "shop_order_number" => $_POST['SHOPORDERNUMBER'] ,
    );

    $result_portmone = Portmone::curlRequest(Portmone::GATEWAY_URL, $data);
    $parseXml = Portmone::parseXml($result_portmone);
    $answer = '';
    if ($parseXml === false) {
        if ($_REQUEST['RESULT'] == '0') {
            $status = [
                "STATUS_ID"             => "PN",
                "PAYED"                 => "Y",
                "PS_STATUS"             => "Y",
            ];
            $answer = Portmone::ORDER_PAYED;
        } else {
            $status = [
                "STATUS_ID"             => "PE",
                "PAYED"                 => "N",
                "PS_STATUS"             => "N",
            ];
            $answer = Portmone::ORDER_REJECTED;
        }
    } elseif ($parseXml->orders->order->status == Portmone::ORDER_PAYED) {
        $status = [
            "STATUS_ID"             => "PP",
            "PAYED"                 => "Y",
            "PS_STATUS"             => "Y",
        ];
        $answer = Portmone::ORDER_PAYED;
        CSaleOrder::PayOrder($arOrder['ID'], 'Y');
    } else {
        $status = [
            "STATUS_ID"             => "PE",
            "PAYED"                 => "N",
            "PS_STATUS"             => "N",
        ];
        $answer = Portmone::ORDER_REJECTED;
    }

    if ($arOrder) {
        $arFields = array(
            "PS_STATUS_CODE"        => $answer,
            "PS_STATUS_DESCRIPTION" => ($answer != Portmone::ORDER_PAYED ? $_POST['RESULT'] : ''),
            "PS_STATUS_MESSAGE"     => ' - ',
            "PS_SUM"                => (string) $parseXml->orders->order->bill_amount,
            "PS_CURRENCY"           => 'UAH',
            "PS_RESPONSE_DATE"      => date("d.m.Y H:i:s")
        );
        $arFields = array_merge($status, $arFields);
    }
    CSaleOrder::Update($ORDER_ID, $arFields);
    echo "<script>window.location.replace('/personal/orders/');</script>";
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
?>