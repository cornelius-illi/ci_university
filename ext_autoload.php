<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php 6536 2009-11-25 14:07:18Z stucki $
 */

$extensionPath = t3lib_extMgm::extPath('ci_university');
return array(
	'tx_ciuniversity_scheduler_synccoursestask' => $extensionPath.'Classes/Scheduler/SyncCoursesTask.php',
);
?>
