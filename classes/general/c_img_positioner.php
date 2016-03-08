<?php

/**
 * Класс для определения центра изображения
 * 
 * Поля объекта:
 *	UF_FILE_ID - (int) привязка к файлу из b_file
 *	UF_POS_X - (int) позиция по X в процентах (0-100)
 *	UF_POS_Y - (int) позиция по Y в процентах (0-100)
 * 
 */
class CImgPositioner {
	
	// -------------------------------------------------------------------------
	// --- CRUD ----------------------------------------------------------------
	// -------------------------------------------------------------------------
	
	/**
	 * Функция создает новую запись в базе
	 * @param array $arFields Поля записи
	 * @return boolean|object Объект с результатом добавления элемента или false
	 */
	private static function _add($arFields) {
		$entity = CImgPositioner::_getEntity();
		
		
		if($entity) {
			CModule::IncludeModule('highloadblock');
			$result = $entity::add($arFields);
			
		} else {
			$result = false;
			
		}

		
		return $result;
		
	}
	
	
	/**
	 * Функция ищет запись по файлу с ID = $iFileID
	 * @param int $iFileID ID файла
	 * @return boolean|array Если файл найде, то массив с полями, иначе false
	 */
	private static function _get($iFileID, $arParams=Array()) {
		if(!intval($iFileID)) {return;}
		
		
		// Определение фильтра выборки
		$arQuery = Array(
			'filter' => Array(
				'UF_FILE_ID' => $iFileID,
			),
		);
		// ---------------------------------------------------------------------
		
		
		// Определение полей выборки
		if($arParams['select']) {
			$arQuery['select'] = $arParams['select'];
		}
		// ---------------------------------------------------------------------

		
		// Поиск элемента по базе
		CModule::IncludeModule('highloadblock');
		$entity	= CImgPositioner::_getEntity();
		$rsData	= $entity::getList($arQuery);
		$result = false;
		
		if($arData = $rsData->Fetch()) {
			if($arData['ID']) {
				$result = $arData;
			}
		}
		// ---------------------------------------------------------------------
		
		
		return $result;
	}
	
	
	/**
	 * Функция обновляет запись в базе
	 * @param int $ID ID записи
	 * @param array $arFields Поля записи
	 * @return boolean
	 */
	private static function _update($ID, $arFields) {
		if(!intval($ID))			{return;}
		if(!is_array($arFields))	{return;}
		
		
		$entity = CImgPositioner::_getEntity();
		
		if($entity) {
			CModule::IncludeModule('highloadblock');
			$result = $entity::update($ID, $arFields);
			
		} else {
			$result = false;
			
		}

		
		return $result;
	}
	
	
	/**
	 * Функция удаляет запись о центре изображения по ID файла
	 * @param int $iFileID ID удаляемого файла
	 */
	private static function _delete($iFileID) {
		$arData = CImgPositioner::_get(
			$iFileID,
			Array('select' => Array('ID'))
		);
		
		if($id = $arData['ID']) {
			CModule::IncludeModule('highloadblock');
			$entity = CImgPositioner::_getEntity();
			$entity::delete($id);
		}
		
		return true;
	}
	
	
	// -------------------------------------------------------------------------
	// --- CRUD EX -------------------------------------------------------------
	// -------------------------------------------------------------------------
	
	/**
	 * Сохранение записи о позиции центра в базу или обновление существующей.
	 * Функция проверяет наличие записи по файлу $iFileID в базе
	 * и при необходимости обновляет её
	 * @param int $iFileID привязка к файлу из b_file
	 * @param int $iPosX позиция по X в процентах (0-100)
	 * @param int $iPoxY позиция по Y в процентах (0-100)
	 */
	public static function set($iFileID, $iPosX=50, $iPoxY=50) {
		
		// Проверка входных значений
		if(!intval($iFileID))								{return;}
		if(!intval($iPosX) || $iPosX > 100 || $iPosX < 0)	{return;}
		if(!intval($iPoxY) || $iPoxY > 100 || $iPoxY < 0)	{return;}
		// ---------------------------------------------------------------------
		
		
		// Определим поля новой или обновляемой записи
		$arFields = Array(
			'UF_FILE_ID'	=> $iFileID,
			'UF_POS_X'		=> $iPosX,
			'UF_POS_Y'		=> $iPoxY,
		);
		// ---------------------------------------------------------------------
		
		
		// Добавим или обновим запись в базе
		$arData = CImgPositioner::_get(
			$iFileID,
			Array('select' => Array('ID'))
		);
		
		$result = false;
		if($id = $arData['ID']) {
			$resultUpdate = CImgPositioner::_update($id, $arFields);
			
			if($id = $resultUpdate->getID()) {
				$result = $id;
			}
			
		} else {
			$resultAdd = CImgPositioner::_add($arFields);
			
			if($id = $resultAdd->getID()) {
				$result = $id;
			}
			
		}
		
		// ---------------------------------------------------------------------
		
		
		return $result;
	}
	
	
	/**
	 * Функция возвращает целевые координаты картинки
	 * @param int $iFileID привязка к файлу из b_file
	 * @return array Массив с ключами x и y
	 */
	public static function getPosition($iFileID) {
		if(!intval($iFileID)) {return;}
		
		
		$arData = CImgPositioner::_get($iFileID);
		
		$arResult['x'] = '50';
		$arResult['y'] = '50';
		
		if($arData) {
			$arResult['x'] = $arData['UF_POS_X'];
			$arResult['y'] = $arData['UF_POS_Y'];
			
		}
		
		
		return $arResult;
	}
	

	// -------------------------------------------------------------------------
	// --- SERVICES ------------------------------------------------------------
	// -------------------------------------------------------------------------
	
	/**
	 * Функция возвращает сущность highload-инфоблока для текущего модуля
	 * @return string Сущность модуля, например, «\IMGPositionerTable»
	 */
	private static function _getEntity() {
		CModule::IncludeModule('highloadblock');
		
		$iIblockID = COption::GetOptionString('img.positioner', 'HLBLOCK_IMGPositioner'); 


		if(intval($iIblockID)) {
			$arHLBlock	= Bitrix\Highloadblock\HighloadBlockTable::getById($iIblockID)->fetch();
			$obEntity	= Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
			$strEntity	= $obEntity->getDataClass();

			return $strEntity;

		} else {
			return false;

		}
		
	}
	
	
	/**
	 * Функция-обработчик события OnFileDelete
	 */
	public static function onFileDelete($arEvent) {
		if(!is_array($arEvent)) {return;}
		
		if($iFileID = $arEvent['ID']) {
			CImgPositioner::_delete($iFileID);
		}
	}
	
	
	public static function setAdminConstructor() {
		//if(!$_REQUEST['ID'] || !$_REQUEST['IBLOCK_ID']) {return;}
		
		global $APPLICATION;
		
		CJSCore::Init(array("jquery"));
		$APPLICATION->AddHeadScript('/local/modules/img.positioner/f/js/script.js');
		$APPLICATION->SetAdditionalCSS('/local/modules/img.positioner/f/css/style.css');
	}
}
