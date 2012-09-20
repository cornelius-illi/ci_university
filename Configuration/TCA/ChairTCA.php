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

$TCA['tx_ciuniversity_domain_model_chair'] = array(
	'ctrl' => $TCA['tx_ciuniversity_domain_model_chair']['ctrl'],
	'interface' => array(
		 # showRecordFieldList wird eine kommaseparierte Liste von Feldnamen erwartet, 
		 # deren Werte im Info-Dialog der Tabelle angezeigt werden sollen
		'showRecordFieldList' => 'title'
	),
	# Der Abschnitt definiert das Aussehen des Formulars fuer das Anlegen bzw. Bearbeiten eines Datensatzes
	'types' => array(  
		'1' => array('showitem' => 'title,url,email,head')
	),
	# Paletten dienen dazu, selten benoetigte Felder zusammenzufassen und diese nur bei Bedarf anzuzeigen
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(
		'title' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_chair.title',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 255,
			)
		),
		'pid' => Array (		
			'exclude' => 1,
			'label' => 'PID',
			'config' => Array (
				'type' => 'none',
			)
		),
		'url' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_chair.url',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'checkbox' => '',
				'eval' => 'trim',
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'email' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_chair.email',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,tx_ciuniversity_mail_eval',
				'max'  => 255,
			)
		),
		'head' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_chair.head',
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ciuniversity_domain_model_person',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	)
);

?>