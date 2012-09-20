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

$TCA['tx_ciuniversity_domain_model_person'] = array(
	'ctrl' => $TCA['tx_ciuniversity_domain_model_person']['ctrl'],
	'interface' => array(
		 # showRecordFieldList wird eine kommaseparierte Liste von Feldnamen erwartet, 
		 # deren Werte im Info-Dialog der Tabelle angezeigt werden sollen
		'showRecordFieldList' => 'firstname,lastname'
	),
	# Der Abschnitt definiert das Aussehen des Formulars fuer das Anlegen bzw. Bearbeiten eines Datensatzes
	'types' => array(  
		'0' => array('showitem' => 'hidden,lastname,firstname,customline,actitle,phone,fax,room,email,sections,chairs,
			customtext;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],
			image,url')
	),
	# Paletten dienen dazu, selten benoetigte Felder zusammenzufassen und diese nur bei Bedarf anzuzeigen
	'palettes' => array(
		'1' => array('showitem' => 'hidden')
	),
	'columns' => array(
		'hidden' => Array (		
			'exclude' => 0,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'pid' => Array (		
			'exclude' => 1,
			'label' => 'PID',
			'config' => Array (
				'type' => 'none',
			)
		),
		'lastname' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.lastname',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'firstname' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.firstname',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'customline' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.customline',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'actitle' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.actitle',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'phone' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.phone',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'fax' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.fax',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'room' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.room',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'email' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.email',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim,tx_ciuniversity_mail_eval',
			)
		),
		
		'customtext' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.customtext',		
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'image' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.image',		
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',	
				'max_size' => 500,	
				'uploadfolder' => 'uploads/tx_ciuniversity',
				'show_thumbs' => 1,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'url' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.url',		
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
		'chairs' => array(		
			'exclude' => 0,		
			'label'   => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.chair',
			'config' => array(
				'type' => 'select',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 3,
				'autoSizeMax' => 3,
				'multiple' => 0,
				'allowed' => 'tx_ciuniversity_domain_model_chair',
				'foreign_table' => 'tx_ciuniversity_domain_model_chair',
				'MM' => 'tx_ciuniversity_person_chair_mm',	
			)
		),
		'sections' => Array (	
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person.sections',		
			'config' => Array (
				'type' => 'select',
				'size' => '3',
				'minitems' => '0',
				'maxitems' => '4',
				'multiple' => '1',
				'items' =>  Tx_CiUniversity_Utility_TsToArray::tsToArray('sections')
			)
		)
	)
);

?>