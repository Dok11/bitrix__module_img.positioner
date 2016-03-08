<?php

/**
 * Скрип ссохраняет координаты центра меню
 *
 * Входные параметры:
 *	ID - ID файла в системе
 *	X - позиция по X
 *	Y - позиция по Y
 *
 * Пример запроса:
 *	/local/modules/img.positioner/ajax/save.php?ID=15&X=20&Y=55
 *
 * Ключи результата:
 *	success - (true|false) Сообщение об успехе
 *
 */


// Инициализация скрипта
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
// -----------------------------------------------------------------------------


// Обновление/добавление информации о центре картинки
$arResult['success'] = false;

if(CModule::IncludeModule('img.positioner')) {
	$arMenu = CImgPositioner::set(
		intval($_REQUEST['ID']),
		intval($_REQUEST['X']),
		intval($_REQUEST['Y'])
	);
	$arResult['success'] = true;
}
// -----------------------------------------------------------------------------


// Вывод результата
header('Content-Type: application/json; charset=utf-8');
$sJson = json_encode($arResult);
echo $sJson;
