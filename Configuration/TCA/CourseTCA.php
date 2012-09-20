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

$TCA['tx_ciuniversity_domain_model_course'] = array(
	'ctrl' => $TCA['tx_ciuniversity_domain_model_course']['ctrl'],
	'interface' => array(
		 # showRecordFieldList wird eine kommaseparierte Liste von Feldnamen erwartet, 
		 # deren Werte im Info-Dialog der Tabelle angezeigt werden sollen
		'showRecordFieldList' => 'title'
	),
	# Der Abschnitt definiert das Aussehen des Formulars fuer das Anlegen bzw. Bearbeiten eines Datensatzes
	'types' => array(  
		'1' => array(
			'showitem' => '
			--div--;LLL:EXT:ci_university/Resources/Private/Language/locallang_tca.xml:ci_university.tca.course.basic,unit_id,title,semester,credits,sws,program,teaching_form,graded,enrolment_deadline,enrolment_type,lecturer,modules,
			--div--;LLL:EXT:ci_university/Resources/Private/Language/locallang_tca.xml:ci_university.tca.course.additional,max_participants,other_lecturers,chair,tutors,url,only_use_course_page_contents,
			--div--;LLL:EXT:ci_university/Resources/Private/Language/locallang_tca.xml:ci_university.tca.course.texts,description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],
			requirements;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],
			examination;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],
			literature;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],
			learning;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],
			dates;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode],'
		 )
	),
	# Paletten dienen dazu, selten benoetigte Felder zusammenzufassen und diese nur bei Bedarf anzuzeigen
	'palettes' => array(
		'1' => array('showitem' => 'hidden'),
	),
	'columns' => array(
		'hidden' => Array (		
			'exclude' => 0,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
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
		'year' => Array (		
			'exclude' => 1,
			'label' => 'Year',
			'config' => Array (
				'type' => 'none',
			)
		),
		'unit_id' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.unitid',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
				'readOnly' => 1
			)
		),
		'title' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.title',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
				'readOnly' => 1
			)
		),
		'semester' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.semester',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
				'readOnly' => 1
			)
		),
		'credits' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.credits',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',
				'readOnly' => 1
			)
		),
		'sws' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.sws',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',
				'readOnly' => 1
			)
		),
		'program' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.program',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
				'readOnly' => 1
			)
		),
		'lecturer' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.lecturer',		
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ciuniversity_domain_model_person',	
				'size' => 1,
				'autoSizeMax' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
				'readOnly' => 1		
			)
		),
		'other_lecturers' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.other_lecturers',		
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ciuniversity_domain_model_person',	
				'foreign_table' => 'tx_ciuniversity_domain_model_person',
				'MM' => 'tx_ciuniversity_course_person_mm',
				'MM_match_fields' => array('type' => 'other_lecturer'),
				'MM_insert_fields' => array('type' => 'other_lecturer'),
				'size' => 4,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'chair' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_chair',		
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ciuniversity_domain_model_chair',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'tutors' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.tutors',		
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ciuniversity_domain_model_person',	
				'foreign_table' => 'tx_ciuniversity_domain_model_person',
				'MM' => 'tx_ciuniversity_course_person_mm',
				'MM_match_fields' => array('type' => 'tutor'),
				'MM_insert_fields' => array('type' => 'tutor'),
				'size' => 4,	
				'minitems' => 0,
				'maxitems' => 10,	
			)
		),
		'modules' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_module',		
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ciuniversity_domain_model_module',	
				'foreign_table' => 'tx_ciuniversity_domain_model_module',
				'MM' => 'tx_ciuniversity_course_module_mm',
				'size' => 4,	
				'minitems' => 0,
				'maxitems' => 15,
				'readOnly' => 1
			)
		),
		'teaching_form' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.teaching_form',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
				'readOnly' => 1
			)
		),
		'description' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.description',		
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
		'requirements' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.requirements',		
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
		'examination' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.examination',		
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
		'literature' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.literature',		
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
		'learning' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.learning',		
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
		'dates' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.dates',		
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
		'graded' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.graded',		
			'config' => Array (
				'type' => 'check',
				'default' => 1,
				'readOnly' => 1
			)
		),
		'enrolment_deadline' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.enrolment_deadline',		
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
				'readOnly' => 1
			)
		),
		'enrolment_type' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.enrolment_type',		
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
				'readOnly' => 1
			)
		),
		'max_participants' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.max_participants',		
			'config' => Array (
				'type' => 'input',
				'size' => '2',
				'max' => '3',
				'eval' => 'int'
			)
		),
		'url' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.url',		
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
		'only_use_course_page_contents' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course.only_use_course_page_contents',		
			'config' => Array (
				'type' => 'check',
				'default' => 0
			)
		),
	)
);

?>