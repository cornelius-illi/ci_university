<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
 * Configure the Plugin to call the
 * right combination of Controller and Action according to
 * the user input (default settings, FlexForm, URL etc.)
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,						// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	'CiUniversity',					// A unique name of the plugin in UpperCamelCase
	array(							// An array holding the controller-action-combinations that are accessible 
		'Course' => 'index,show,modules',
		'Person' => 'index,show'	// The first controller and its first action will be the default 
	),
	array(							// An array of non-cachable controller-action-combinations (they must already be enabled)
		'Person' => 'index'
	)
);

if (TYPO3_MODE === 'BE') {
	$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array(
		'EXT:' . $_EXTKEY . '/Classes/Cli/Factory.php',
		'_CLI_lowlevel'
	);
}


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_CiUniversity_Scheduler_SyncCoursesTask'] = array (
		'extension' => $_EXTKEY,
		'title' => 'Syncronizes courses from the VInfo System',
		'description' => 'Imports data from a csv-file',
		'additionalFields' => 'tx_CiUniversity_Scheduler_SyncCoursesTask'
);

$rteConf = ' {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}';

t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_course.description'.$rteConf);
t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_course.requirements'.$rteConf);
t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_course.examination'.$rteConf);
t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_course.literature'.$rteConf);
t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_course.learning'.$rteConf);
t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_course.dates'.$rteConf);
t3lib_extMgm::addPageTSConfig('RTE.config.tx_ciuniversity_domain_model_persons.customtext'.$rteConf);
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_ciuniversity_mail_eval'] = 'EXT:ci_university/Classes/Utility/EmailValidator.php';
$TYPO3_CONF_VARS['FE']['eID_include']['ciuniversityAjaxDispatcher'] = t3lib_extMgm::extPath($_EXTKEY).'Classes/Utility/ajaxDispatcher.php';
?>