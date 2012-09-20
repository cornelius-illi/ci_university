<?php
if (!defined ('TYPO3_MODE')){
	die ('Access denied.');
}

/**
 * Registers a Plugin to be listed in the Backend. 
 * You also have to configure the Dispatcher in ext_localconf.php.
 */
Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,		// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	'CiUniversity',			// A unique name of the plugin in UpperCamelCase
	'University-Management'	// A title shown in the backend dropdown field
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Basic Configuration');

#t3lib_extMgm::allowTableOnStandardPages('tx_ciuniversity_domain_model_course');
$TCA['tx_ciuniversity_domain_model_course'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_course',
		'label' 			=> 'title',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/CourseTCA.php',
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'delete'            => 'deleted',
		'cruser_id' 		=> 'cruser_id',
		'dividers2tabs' => TRUE,
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'default_sortby' 	=> 'year DESC, semester DESC, title',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/lv.gif'
	)
);

#t3lib_extMgm::allowTableOnStandardPages('tx_ciuniversity_domain_model_person');
$TCA['tx_ciuniversity_domain_model_person'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_person',
		'label' 			=> 'lastname',
        'label_alt' 		=> 'firstname',
        'label_alt_force' => 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/PersonTCA.php',
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'delete'            => 'deleted',
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'default_sortby' 	=> 'lastname ASC, firstname ASC',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/person.png'
	)
);

#t3lib_extMgm::allowTableOnStandardPages('tx_ciuniversity_domain_model_chair');
$TCA['tx_ciuniversity_domain_model_chair'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_chair',
		'label' 			=> 'title',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/ChairTCA.php',
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'delete'            => 'deleted',
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/chair.png'
	)
);

#t3lib_extMgm::allowTableOnStandardPages('tx_ciuniversity_domain_model_module');
$TCA['tx_ciuniversity_domain_model_module'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:ci_university/Resources/Private/Language/locallang_db.xml:tx_ciuniversity_domain_model_module',
		'label' 			=> 'title',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/ModuleTCA.php',
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'delete'            => 'deleted',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/modul.png'
	)
);

$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_ciuniversity';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/University.xml');

	// adds processing for extra "codes" that have been added to the "what to display" selector in the content element by other extensions
include_once(t3lib_extMgm::extPath($_EXTKEY).'Classes/Utility/ItemsProcFunc.php');
?>