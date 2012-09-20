<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Cornelius Illi <cornelius.illi@student.hpi.uni-potsdam.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class Tx_CiUniversity_Utility_TsToArray {
	
	const EXTKEY = 'ci_university';
	
	private static function loadTS($pageUid=499) {
       $sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
       $rootLine = $sysPageObj->getRootLine($pageUid);
       $TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
       $TSObj->tt_track = 0;
       $TSObj->init();
       $TSObj->runThroughTemplates($rootLine);
       $TSObj->generateConfig();
       return $TSObj->setup['plugin.']['tx_ciuniversity.'];
	}
	
	public static function tsToArray($name='') {
		if(empty($name)) {
			return array();
		} else {
			$extConf = unserialize(($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][Tx_CiUniversity_Utility_TsToArray::EXTKEY]));
			$settings = self::loadTS($extConf['tspage']);
			$ar = t3lib_div::trimExplode(',', $settings['settings.'][$name]);
			$ret = array();
			foreach($ar as $el) {
				$ret[] = Array($el,$el);
			}
			return $ret;
		}
	}
	
	public static function tsToInt($name='') {
		if(empty($name)) {
			return array();
		} else {
			$extConf = unserialize(($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][Tx_CiUniversity_Utility_TsToArray::EXTKEY]));
			$settings = self::loadTS($extConf['tspage']);
	
			return intval($settings['settings.'][$name]);
		}
	}
	
	public static function tsToStr($name='') {
		if(empty($name)) {
			return array();
		} else {
			$extConf = unserialize(($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][Tx_CiUniversity_Utility_TsToArray::EXTKEY]));
			$settings = self::loadTS($extConf['tspage']);
			return trim($settings['settings.'][$name]);
		}
	}
}

?>