<?php
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen('/install/index.php'));
include(GetLangFileName($strPath2Lang.'/lang/', '/install/index.php'));

Class img_positioner extends CModule {
	var $MODULE_ID = 'img.positioner';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function img_positioner() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		$this->MODULE_VERSION		= $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE	= $arModuleVersion['VERSION_DATE'];

		$this->MODULE_NAME			= GetMessage('IMG_POSITIONER_INSTALL_NAME');
		$this->MODULE_DESCRIPTION	= GetMessage('IMG_POSITIONER_INSTALL_DESCRIPTION');

		$this->PARTNER_NAME			= GetMessage('IMG_POSITIONER_PARTNER');
		$this->PARTNER_URI			= 'http://dev.1c-bitrix.ru/community/webdev/user/94272/blog/';
	}

	function InstallDB($install_wizard = true) {
		RegisterModule($this->MODULE_ID);
		
		RegisterModuleDependences(
			'main', 'OnFileDelete',
			$this->MODULE_ID, 'CImgPositioner', 'onFileDelete'
		);
		
		RegisterModuleDependences(
			'main', 'OnEpilog', 
			$this->MODULE_ID, 'CImgPositioner', 'setAdminConstructor'
		);

		
		return true;
	}

	function UnInstallDB($arParams = Array()) {
		UnRegisterModule($this->MODULE_ID);
		
		UnRegisterModuleDependences(
			'main', 'OnFileDelete',
			$this->MODULE_ID, 'CImgPositioner', 'onFileDelete'
		);
		
		UnRegisterModuleDependences(
			'main', 'OnEpilog', 
			$this->MODULE_ID, 'CImgPositioner', 'setAdminConstructor'
		);

		
		return true;
	}

	function InstallFiles() {
		return true;
	}

	function UnInstallFiles() {
		return true;
	}

	function DoInstall() {
		$this->InstallDB(false);
	}

	function DoUninstall() {
		$this->UnInstallDB();
	}
}
