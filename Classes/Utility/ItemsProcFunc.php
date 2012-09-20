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

require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/CourseRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/ChairRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Course.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Chair.php');


class tx_CiUniversity_Utility_ItemsProcFunc {
	
	/**
 	* returns an array of all semesters
 	*
 	* @param	array		$config: extension configuration array
 	* @return	array		$config array with extra codes merged in
 	*/
	public function user_getAllSemesters($config) {
		$courseRepository = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_CourseRepository');
		$config['items'] = array_merge($config['items'] ,$courseRepository->findAllSemestersToArray());
		return $config;
	}
	
	/**
 	* returns an array of all chairs
 	*
 	* @param	array		$config: extension configuration array
 	* @return	array		$config array with extra codes merged in
 	*/
	public function user_getAllChairs($config) {
		$chairRepository = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_ChairRepository');
		$config['items'] = array_merge($config['items'] ,$chairRepository->findAllChairsToArray());	
		return $config;
	}
	
}

?>