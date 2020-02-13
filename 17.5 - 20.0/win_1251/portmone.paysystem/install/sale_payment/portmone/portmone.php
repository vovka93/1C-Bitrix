<?php

class Portmone
{
    const ORDER_PAYED           = 'PAYED';
    const ORDER_CREATED         = 'CREATED';
    const ORDER_REJECTED        = 'REJECTED';
    const GATEWAY_URL           = 'https://www.portmone.com.ua/gateway/';

    public static function getPaySystemId($ORDER_ID) {
        $arOrder    = CSaleOrder::GetByID($ORDER_ID);
        $payID      = $arOrder['PAY_SYSTEM_ID'];
        $temp       = CSalePaySystemAction::GetList(array(), array("PAY_SYSTEM_ID" => $payID));
        $payData    = $temp->Fetch();
        $parametrs  = array();

        $b = unserialize($payData['PARAMS']);
        foreach ($b as $k => $v) $parametrs[$k] = $v['VALUE'];

        return $parametrs;
    }

    public static function isPaymentValid($ORDER_ID) {
        $parametrs = self::getPaySystemId($ORDER_ID);
        $message    = '';
        if (empty($parametrs['PORTMONE_USER_ID'])) {
            $message .= GetMessage('PORTMONE_PAYSYSTEM_ERROR_1').'<br>';
        }

        if (empty($parametrs['PORTMONE_USER_LOGIN'])) {
            $message .= GetMessage('PORTMONE_PAYSYSTEM_ERROR_2').'<br>';
        }

        if (empty($parametrs['PORTMONE_USER_PASSWORD'])) {
            $message .= GetMessage('PORTMONE_PAYSYSTEM_ERROR_3').'<br>';
        }
        return $message;
    }


    /**
     * A request to verify the validity of payment in Portmone
     **/
    public static function curlRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (200 !== intval($httpCode)) {
            return false;
        }
        return $response;
    }

    /**
     * Parsing XML response from Portmone
     **/
    public static function parseXml($string) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (false !== $xml) {
            return $xml;
        } else {
            return false;
        }
    }

    public static function getLanguage() {
        $lang = LANGUAGE_ID;
        if ($lang == 'ru' || $lang == 'en' || $lang == 'uk') {
            return  $lang;
        } else {
            return  'en';
        }
    }

    public static function getNameSite() {
        $rsSites    = CSite::GetByID(SITE_ID);
        $arSite     = $rsSites->Fetch();
        return $arSite['SITE_NAME'];
    }
}