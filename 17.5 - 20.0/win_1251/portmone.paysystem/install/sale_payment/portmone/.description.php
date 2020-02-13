<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php include(GetLangFileName(dirname(__FILE__) . "/", "/lang.php")); ?>
<?php
$psTitle = "Portmone.com";
$psDescription = "<a href=\"https://www.portmone.com.ua/r3/\" target=\"_blank\">Portmone.com</a>";

$arPSCorrespondence = array(
    "PORTMONE_USER_ID"  => array(
        "NAME"  => GetMessage("PORTMONE_PAYSYSTEM_USER_ID"),
        "DESCR" => GetMessage("PORTMONE_PAYSYSTEM_USER_ID_DESC"),
        'GROUP' => 'PS_OTHER',
        'SORT'  => 100,
    ),
    "PORTMONE_USER_LOGIN"   => array(
        "NAME"  => GetMessage("PORTMONE_PAYSYSTEM_USER_LOGIN"),
        "DESCR" => GetMessage("PORTMONE_PAYSYSTEM_USER_LOGIN_DESC"),
        'GROUP' => 'PS_OTHER',
        'SORT'  => 200,
    ),
    "PORTMONE_USER_PASSWORD"    => array(
        "NAME"  => GetMessage("PORTMONE_PAYSYSTEM_USER_PASSWORD"),
        "DESCR" => GetMessage("PORTMONE_PAYSYSTEM_USER_PASSWORD_DESC"),
        'GROUP' => 'PS_OTHER',
        'SORT'  => 300,
    ),
    "PORTMONE_SUCCESS"  => array(
        "NAME"  => GetMessage("PORTMONE_PAYSYSTEM_SUCCESS"),
        "DESCR" => GetMessage("PORTMONE_PAYSYSTEM_SUCCESS_DESC"),
        'GROUP' => 'PS_OTHER',
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "http://".$_SERVER["HTTP_HOST"]."/bitrix/tools/portmone_result/portmone_result.php",
            "PROVIDER_KEY" => "VALUE"
        ),
        'SORT' => 400,
    ),
    "PORTMONE_CANCEL"   => array(
        "NAME"  => GetMessage("PORTMONE_PAYSYSTEM_CANCEL"),
        "DESCR" => GetMessage("PORTMONE_PAYSYSTEM_CANCEL_DESC"),
        'GROUP' => 'PS_OTHER',
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "http://".$_SERVER["HTTP_HOST"]."/bitrix/tools/portmone_result/portmone_result.php",
            "PROVIDER_KEY" => "VALUE"
        ),
        'SORT' => 500,
    )
);
?>