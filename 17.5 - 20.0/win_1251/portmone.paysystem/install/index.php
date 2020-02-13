<?php
/**
 * Portmone.com Payment Module
 *
 * NOTICE OF LICENSE
 *
 * @category        Portmone.com
 * @package         portmone.paysystem
 * @version         1.0.1
 * @author          Portmone.com
 * @copyright       Copyright (c) 2018 Portmone.com
 * @license         Payment Card Industry Data Security Standard (PCI DSS)
 * @license url     https://www.portmone.com.ua/r3/uk/security/
 *
 * EXTENSION INFORMATION
 *
 * 1C-Bitrix        17.0
 */

IncludeModuleLangFile(__FILE__);

class Portmone_paysystem extends CModule
{
    const MODULE_ID     = 'portmone.paysystem';
    const PARTNER_NAME  = 'Portmone.com';
    const URI           = 'https://www.portmone.com.ua/r3/';

    var $MODULE_ID      = self::MODULE_ID;
    var $PARTNER_NAME   = self::PARTNER_NAME;
    var $URI            = self::URI;

    public function __construct() {
        if (file_exists(dirname(__FILE__).'/version.php')) {
            require(dirname(__FILE__).'/version.php');

            $this->MODULE_VERSION       = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE  = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME          = GetMessage('PORTMONE_MODULE_NAME');
            $this->MODULE_DESCRIPTION   = GetMessage('PORTMONE_MODULE_DESC');
            $this->PARTNER_NAME         = self::PARTNER_NAME;
            $this->URI                  = self::URI;
        }
    }

    public function DoInstall() {
        if (IsModuleInstalled('sale')) {
            $this->InstallFiles();
            $this->InstallStatuses();
            RegisterModule($this->MODULE_ID);
            return true;
        }

        $MODULE_ID  = $this->MODULE_ID;
        $TAG        = 'VWS';
        $MESSAGE    = GetMessage('PORTMONE_ERR_MODULE_NOT_FOUND', array('#MODULE#'=>'sale'));
        $intID      = CAdminNotify::Add(compact('MODULE_ID', 'TAG', 'MESSAGE'));

        return false;
    }

    public function DoUninstall() {
        COption::RemoveOption($this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
    }

    public function InstallStatuses() {
        $lang_z = array();
        $db_lang = CLangAdmin::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));

        $texts = [
            [
                'name' => 'Оплачено с помощью Portmone',
                'desc' => 'Оплачено с помощью Portmone',
            ],
            [
                'name' => 'Оплачено с помощью Portmone НО НЕ проверено',
                'desc' => 'Оплачено с помощью Portmone НО НЕ проверено',
            ],
            [
                'name' => 'Оплата заказа через Portmone НЕ удалась',
                'desc' => 'Оплата заказа через Portmone НЕ удалась',
            ],
        ];

        while ($arLang = $db_lang->Fetch()) {
            foreach ($texts as $kay => $text) {
                $lang_z[$kay][] = ['LID' => $arLang["LID"], 'NAME' => $text['name'], 'DESCRIPTION' => $text['desc']];
            }
        }

        $new_statuses = [
            [
                'ID' => 'PP',
                'SORT' => 1000,
                'COLOR' => '#109b00',
                'LANG' => $lang_z[0]
            ],
            [
                'ID' => 'PN',
                'SORT' => 1001,
                'COLOR' => '#0a4e03',
                'LANG' => $lang_z[1]
            ],
            [
                'ID' => 'PE',
                'SORT' => 1002,
                'COLOR' => '#bb0f0f',
                'LANG' => $lang_z[2]
            ]
        ];

        if(CModule::IncludeModule("sale")) {
            foreach ($new_statuses as $new_status) {
                $arStatus = CSaleStatus::GetByID($new_status['ID']);
                 if (!$arStatus) {
                     CSaleStatus::Add($new_status);
                 }
            }
        }
    }

    public function InstallFiles() {
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/sale_payment/portmone_result',
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/portmone_result',
            true, true
        );
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/sale_payment/portmone',
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/portmone',
            true, true
        );
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/images',
            $_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/images/'.$this->MODULE_ID, true, true
        );
    }

    public function UnInstallFiles() {
        DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/portmone");
        return true;
    }
}