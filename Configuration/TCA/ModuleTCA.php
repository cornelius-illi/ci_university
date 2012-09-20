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

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('ci_university') .'Classes/Utility/TsToArray.php');

$TCA['tx_ciuniversity_domain_model_module'] = array(
	'ctrl' => $TCA['tx_ciuniversity_domain_model_module']['ctrl'],
	'interface' => array(
		 # showRecordFieldList wird eine kommaseparierte Liste von Feldnamen erwartet, 
		 # deren Werte im Info-Dialog der Tabelle angezeigt werden sollen
		'showRecordFieldList' => 'title'
	),
	# Der Abschnitt definiert das Aussehen des Formulars fuer das Anlegen bzw. Bearbeiten eines Datensatzes
	'types' => array(  
		'1' => array('showitem' => 'title,modulegroup,identifier')
	),
	# Paletten dienen dazu, selten benoetigte Felder zusammenzufassen und diese nur bei Bedarf anzuzeigen
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(
		'uid' => Array (		
			'exclude' => 1,
			'label' => 'UID',
			'config' => Array (
				'type' => 'none',
			)
		),
		'title' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_module.title',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 255,
				'readOnly' => 1
			)
		),
		'pid' => Array (		
			'exclude' => 1,
			'label' => 'PID',
			'config' => Array (
				'type' => 'none',
			)
		),
		'modulegroup' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_module.modulegroup',
				'config' => Array (
				'type' => 'select',
				'size' => '1',
				'minitems' => '1',
				'maxitems' => '1',
				'items' =>  Tx_CiUniversity_Utility_TsToArray::tsToArray('modulegroups')
			)
		),
		'identifier' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_module.identifier',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 255
			)
		),
	)
);

?>