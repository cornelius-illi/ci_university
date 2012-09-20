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
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class "tx_ciuniversity_sync_courses_task" provides a task that syncs courses from VInfo
 *
 * @author		Cornelius Illi <cornelius.illi@student.hpi.uni-potsdam.de>
 * @package		TYPO3
 * @subpackage	tx_scheduler
 *
 * $Id: SyncCoursesTask.php $
 */

$_EXTKEY = 'ci_university';

require_once(PATH_typo3.'sysext/scheduler/interfaces/interface.tx_scheduler_additionalfieldprovider.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/CourseRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/ModuleRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/PersonRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Course.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Module.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Person.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Utility/CsvHandler.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Utility/MailHandler.php');

class tx_CiUniversity_Scheduler_SyncCoursesTask 
	extends tx_scheduler_Task implements tx_scheduler_AdditionalFieldProvider {
	
	protected $persistanceManager;
	protected $csvHandler;
	protected $mailHandler;
	protected $courseRepo;
	protected $moduleRepo;
	protected $personRepo;
	protected $modules;
	protected $report;
	protected $errorReport;
	
	// set on task-configuration via backend
	protected $reportEmail;
	protected $vinfofile;
	protected $delimiter;
	protected $enclosure;
	protected $allmailstoadmin;
	protected $dailyreport;
	protected $noheaders;
	protected $lecturerpid;
	protected $coursepid;
	
	private function build() {
		$this->persistanceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
		$this->csvHandler = t3lib_div::makeInstance('Tx_CiUniversity_Utility_CsvHandler');
		$this->mailHandler = t3lib_div::makeInstance('Tx_CiUniversity_Utility_MailHandler');
		$this->courseRepo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_CourseRepository');
		$this->moduleRepo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_ModuleRepository');
		$this->personRepo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_PersonRepository');
		$this->modules = array();
		$this->report = 'New Courses added to Repository: \n';
		$this->errorReport = '';
	}
	
	/**
	 * Function executed from the Scheduler.
	 * Iterates over all user-files and creates new user-accounts!
	 *
	 * @return	void
	 */
	public function execute() {
		$this->build();
		
		if( !$this->csvHandler->loadFile($this->vinfofile, $this->delimiter, $this->enclosure, $this->noheaders) ) {
    		$this->mailHandler->sendMail(
    			$this->reportEmail,
    			'Error on importing course-data via scheduler-task',
    			$this->csvHandler->getErrorMsg()
    		);
    		return false;	
    	}
    	
    	while($row = $this->csvHandler->read() ) {
    		$new = false;
    		$course = $this->courseRepo->findOneByUnitIdAndSemester( (int)$row['UnitID'], $row['Semester'] );
    		if($course) { // course-exists
    			
    		} else { // importing new course
    			$course = new Tx_CiUniversity_Domain_Model_Course;
    			$new = true;
    		}
    		
    		if( !$this->updateCourse($course, $row, $new) ) {
    			/* person-record for lecturer could not be found
    			 * (might happen for new course with new lecturer or
    			 * whenever lecturer-name in TYPO3 is altered and differs from VInfo)
    			 * therefore record may not be persisted
    			 * error-msg will be created
    			 */
    			continue;
    		} 
    		
    		if($new === true) {
    			$this->courseRepo->add($course);
    			
    			// determine which email should be used -
    			$email = $this->reportEmail;
    		
    			if(!$this->allmailstoadmin && (strlen($course->getChair()->getEmail() ) > 0) ) {
    				$email = $course->getChair()->getEmail();
    			} elseif(!$this->allmailstoadmin) {
    				$email = $course->getLecturer()->getEmail();
    			}
    			
    			$this->mailHandler->sendMail(
    				$email,
    				$course->getTitle().' added to the Course-Extension!',
    				$this->createNewCourseMessageFor( $course->getLecturer(), $course->getTitle(), $course->getSemester() )
    			);
    			
    			if($this->dailyreport) {
    				$this->addToDailyReport($course);	
    			}
    		} else {
    			$this->courseRepo->update($course);
    		}
    	}
    	
		if($this->csvHandler->isError()) {
    		$this->mailHandler->sendMail(
    			$this->reportEmail,
    			'Error on importing course-data via scheduler-task',
    			$this->csvHandler->getErrorMsg()
    		);
    		return false;
    	} else {
    		// only persist ANYTHING if file runs through
    		$this->persistanceManager->persistAll();	
    	}
    	
    	// close file
    	$this->csvHandler->close();
    	
    	if($this->dailyreport && count($this->report) > 0) {
    		$this->mailHandler->sendMail(
    			$this->reportEmail,
    			'VInfo-import-report '.strftime("%A."),
    			$this->report
    		);
    	}
    	
    	if( !empty($this->errorReport) ) {
    		$this->mailHandler->sendMail(
    			$this->reportEmail,
    			'[ERROR-Report] VInfo-Import '.strftime("%A."),
    			$this->errorReport
    		);	
    	}
    	
		return true;
	}
	
	private function addToDailyReport(Tx_CiUniversity_Domain_Model_Course &$course) {
		$this->report .= '\t '.$course->getTitle(). '('.$course->getLecturer().')';
	}
	
	private function addToErrorReport($course, $lecturer) {
		$this->errorReport .= $lecturer.' required for course: "'.$course.'" does not exist!';
	}
	
	private function updateCourse(Tx_CiUniversity_Domain_Model_Course &$course, $row, $new) {
    	// pre-condition: no lecturer, no mapping/ import
    	if($row['Dozent'] === "NULL") {
    		$this->addToErrorReport($row['UnitBezeichnung'],$row['Dozent']);
    		return false;
    	}
   
		$course->setUnitId($row['UnitID']);
    	$course->setTitle($row['UnitBezeichnung']);
    	$course->setSemester($row['Semester']);
    	$course->setCredits($row['Credits']);
    	$course->setSws($row['SWS']);
    	$course->setProgram($row['Studiengang']);
    	$course->setTeachingForm($row['Lehrform']);
    	
    	$graded = ($row['Benotet'] === 'Ja') ? 1 : 0;
    	$course->setGraded($graded);
    	
    	$course->setEnrolmentDeadline($row['Enschreibefrist']);
    	$course->setEnrolmentType($row['Belegungsart']);
    	
    	$fullName = $this->parseLecturerName($row['Dozent']);
    	$prof = $this->personRepo->findOneByFullnameWithoutPid($fullName);
    	if(!$prof) {
    		$this->addToErrorReport($row['UnitBezeichnung'],$row['Dozent']);
    		// records for the same course (with different modules) should be skipped
    		return false;
    	}
    	$course->setLecturer($prof); 
    	
    	// if a course get imported to the wrong chair and folder
    	// manual changes should not be overwritten
    	if($new) {	
	    	$chairs = $prof->getChairs()->toArray();
	    	if( count($chairs) > 0) {
	    		$course->setChair( $chairs[0] );
	    		$course->setPid( $chairs[0]->getPid() );
	    	} elseif($prof->getPid() === $this->lecturerpid ) {
	    		$course->setPid( $this->coursepid );
	    	} else {
	    		$course->setPid( $prof->getPid() );
	    	}
    	}
    	
    	$modules = t3lib_div::trimExplode("#", $row['Modul'], true);
    	foreach($modules as $module) {
    		$this->updateCourseModule($course,$module);
    	}
    	
    	return true;
    }
    
	private function parseLecturerName($lecturer) {
    	$nameAr = t3lib_div::trimExplode('#', $lecturer);
    	
    	return array(
    		'actitle' => $nameAr[0],
    		'firstname' => $nameAr[1],
    		'lastname' => $nameAr[2]	
    	);
    }
    
    private function updateCourseModule(&$course, $mod) {
    	$module = $this->module_repo->findOrCreateByTitle($mod, $this->coursepid);
    	
    	# @todo: ->getUid() won't work at the moment (always NULL), but would be the right solution
    	if( !in_array($module->getTitle(), $this->modules) ) {
    		$this->persistanceManager->persistAll();
    		$this->modules[] = $module->getTitle();
    	} 
    	
    	if(!$course->getModules()->contains($module)) {
    		$course->addModule($module);
    	}
    }
    
    private function createNewCourseMessageFor($person, $lvTitle, $semester) {
    	if(empty($person)) {
    		$person = 'geehrte Damen und Herren';
    	}
    	
    	$text = $person->getFullname().','.chr(10).' ihre Lehrveranstaltung "'.$lvTitle.'" für das Semester "'.$semester.'" 
    		wurde der LV-Erweiterung der HPI-Webseite hinzugefügt.'.chr(10).' Bitte ergänzen Sie die fehlenden Informationen!'.chr(10).chr(10);
    	$text .= 'Freundliche Grüße,'.chr(10).'die Admins';
    	$text .= chr(10).chr(10).'-----------------------------'.chr(10).chr(10);
    	$text .= 'Dear '.$person->getFullname().','.chr(10).' your course "'.$lvTitle.'" for the term "'.$semester.'" 
    		has been added to the course-extension of the HPI-Website.'.chr(10).' Please add the missing information!'.chr(10).chr(10);
    	$text .= 'Kind regards,'.chr(10).'The Admins';
    	return $text;
    }
	
	
	/**
	 * This method returns the additional information
	 * @return	string	Information to display
	 */
	public function getAdditionalInformation() {
		return "Iterates over all user-files and creates new user-accounts!"; 
	}
	
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
		if (empty($taskInfo['reportEmail'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['reportEmail'] = $GLOBALS['BE_USER']->user['email'];
			} elseif ($parentObject->CMD == 'edit') {
				$taskInfo['reportEmail'] = $task->reportEmail;
			} else {
				$taskInfo['reportEmail'] = '';
			}
		}
		
		if (empty($taskInfo['vinfofile'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['vinfofile'] = '/export/vinfo/vinfo.csv';
			} elseif ($parentObject->CMD == 'edit') {
				$taskInfo['vinfofile'] = $task->vinfofile;
			} else {
				$taskInfo['vinfofile'] = '';
			}
		}
		
		if (empty($taskInfo['lecturerpid'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['lecturerpid'] = '485';
			} elseif ($parentObject->CMD == 'edit') {
				$taskInfo['lecturerpid'] = $task->lecturerpid;
			} else {
				$taskInfo['lecturerpid'] = '';
			}
		}
		
		if (empty($taskInfo['coursepid'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['coursepid'] = '484';
			} elseif ($parentObject->CMD == 'edit') {
				$taskInfo['coursepid'] = $task->coursepid;
			} else {
				$taskInfo['coursepid'] = '';
			}
		}
		
		if (empty($taskInfo['delimiter'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['delimiter'] = ';';
			} elseif ($parentObject->CMD == 'edit') {
				$taskInfo['delimiter'] = $task->delimiter;
			} else {
				$taskInfo['delimiter'] = ';';
			}
		}
		
		if (empty($taskInfo['enclosure'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['enclosure'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				$taskInfo['enclosure'] = $task->enclosure;
			} else {
				$taskInfo['enclosure'] = '';
			}
		}
		if ($parentObject->CMD == 'edit') {
			$taskInfo['allmailstoadmin'] = $task->allmailstoadmin;
			$taskInfo['dailyreport'] = $task->dailyreport;
			$taskInfo['noheaders'] = $task->noheaders;
		}
		
		$additionalFields = array(
			'task_reportemail' => $this->addInput('reportEmail',$taskInfo['reportEmail'],'Admin-Mail (for errors etc.)'),
			'task_vinfofile' => $this->addInput('vinfofile',$taskInfo['vinfofile'],'Path to Course-File'),
			'task_coursepid' => $this->addInput('coursepid',$taskInfo['coursepid'],'StoragePid for LV-Daten folder'),
			'task_lecturerpid' => $this->addInput('lecturerpid',$taskInfo['lecturerpid'],'StoragePid for Personen-Daten folder'),
			'task_delimiter' => $this->addInput('delimiter',$taskInfo['delimiter'],'CSV-Delimiter (standard: ";")'),
			'task_enclosure' => $this->addInput('enclosure',$taskInfo['enclosure'],'CSV-Enclosure (standard: "")'),
			'task_noheaders' => $this->addCheckbox('noheaders','Use pre-defined headers?', $taskInfo['noheaders']),
			'task_allmailstoadmin' => $this->addCheckbox('allmailstoadmin','All Notification-Mails to Admin? (for test-runs)', $taskInfo['allmailstoadmin']),
			'task_dailyreport' => $this->addCheckbox('dailyreport','Create a daily report? (for test-runs)' ,$taskInfo['dailyreport'])
		);
		 
		return $additionalFields;
	}
	
	private function addInput($name,$value,$label) {
		$id = 'task'.strtolower($name);
		$code = '<input type="text" name="tx_scheduler['.$name.']" id="' . $id . '" value="' . $value . '" size="30" />';
		return array(
			'code'     => $code,
			'label'    => $label
		);
	}
	
	private function addCheckbox($name,$label,$checked) {
		$id = 'task'.strtolower($name);
		$c = ($checked) ? 'checked="checked"' : '';
		$code = '<input type="checkbox" name="tx_scheduler['.$name.']" id="' . $id . '" '.$c.' />';
		return array(
			'code'     => $code,
			'label'    => $label
		);
	}
		
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		$result = true; 
		$submittedData['reportEmail'] = trim($submittedData['reportEmail']);
		$submittedData['vinfofile'] = trim($submittedData['vinfofile']);
		$submittedData['delimiter'] = trim($submittedData['delimiter']);
		$submittedData['enclosure'] = trim($submittedData['enclosure']);
 
		if (empty($submittedData['reportEmail'])) {
			$parentObject->addMessage('No e-mail for error-reporting entered!', t3lib_FlashMessage::ERROR);
			$result = false;
		} elseif(!t3lib_div::validEmail($submittedData['reportEmail'])) {
			$parentObject->addMessage('"'.$submittedData['reportEmail'].'" is not a valid email!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		if (empty($submittedData['vinfofile'])) {
			$parentObject->addMessage('No path to the user-lists entered!', t3lib_FlashMessage::ERROR);
			$result = false;
		} elseif(!t3lib_div::validPathStr($submittedData['vinfofile'])) {
			$parentObject->addMessage($submittedData['vinfofile'].' is no valid Path!', t3lib_FlashMessage::ERROR);
			$result = false;
		} elseif(!file_exists($submittedData['vinfofile'])) {
			$parentObject->addMessage('File: "'.$submittedData['vinfofile'].'" does not exist!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		if (empty($submittedData['delimiter'])) {
			$parentObject->addMessage('Delimiter cannot be empty!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		if(empty($submittedData['coursepid'])) {
			$parentObject->addMessage('StoragePid for LV-Daten cannot be empty!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		if(empty($submittedData['lecturerpid'])) {
			$parentObject->addMessage('StoragePid for Personen-Daten cannot be empty!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		$submittedData['coursepid'] = intval($submittedData['coursepid']);
		$submittedData['lecturerpid'] = intval($submittedData['lecturerpid']);
	
		if($submittedData['lecturerpid'] === 0) {
			$parentObject->addMessage('StoragePid for Personen-Daten could not be found!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		if($submittedData['coursepid'] === 0) {
			$parentObject->addMessage('StoragePid for LV-Daten could not be found!', t3lib_FlashMessage::ERROR);
			$result = false;
		}
		
		return $result;
	}
	
	 public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->reportEmail = $submittedData['reportEmail'];
		$task->vinfofile = $submittedData['vinfofile'];
		$task->delimiter = $submittedData['delimiter'];
		$task->enclosure = $submittedData['enclosure'];
		$task->allmailstoadmin = $submittedData['allmailstoadmin'];
		$task->dailyreport = $submittedData['dailyreport'];
		$task->noheaders = $submittedData['noheaders'];
		$task->coursepid = $submittedData['coursepid'];
		$task->lecturerpid = $submittedData['lecturerpid'];
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['EXT:ci_university/Classes/Scheduler/SyncCoursesTask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['EXT:ci_university/Classes/Scheduler/SyncCoursesTask.php']);
}

?>