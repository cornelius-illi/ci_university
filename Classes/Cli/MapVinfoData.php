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

/**
 *
 * The shell call is
 * /www/typo3/php cli_dispatch.phpsh EXTKEY TASK
 * 
 * @author	Cornelius Illi <Cornelius.Illi@student.hpi.uni-potsdam.de>
 * @package TYPO3
 */

if (!defined('TYPO3_cliMode')) {
	die('Access denied: CLI only.');
}

$_EXTKEY = 'ci_university';

require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/CourseRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/ModuleRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Course.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Module.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Utility/CsvHandler.php');

class Tx_CiUniversity_Cli_MapVinfoData extends t3lib_cli {
	
	protected $persistanceManager;
    protected $course_repo;
    protected $module_repo;
    protected $person_repo;
    protected $csvHandler;
    protected $semesters = array();
    protected $coursePid;
    protected $lecturerPid;
   
	function __construct() {
		$this->persistanceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
		$this->course_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_CourseRepository');
		$this->module_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_ModuleRepository');
		$this->person_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_PersonRepository');
		$this->csvHandler = t3lib_div::makeInstance('Tx_CiUniversity_Utility_CsvHandler');
	}
    
    public function mapVinfoData($filename, $lPid, $cPid, $delimiter=',', $enclosure='', $noHeadersFlag=false) {
    	if(empty($cPid) || empty($lPid) ) {
    		$this->cli_echo("StoragePid for lecturer- and course-records need to be set (using -l and -c)!".LF);
    		exit();
    	} 
    	
    	$lPid = intval($lPid);
    	$cPid = intval($cPid);

    	if($lPid === 0 || $cPid === 0) {
    		$this->cli_echo("StoragePid for lecturer- and module-records need to integer-value!".LF);
    		exit();
    	} else {
    		$this->coursePid = $cPid;
    		$this->lecturerPid = $lPid;
    	}
    	
    	$this->fullTextToCourseTable('add');
    	
    	if( !$this->csvHandler->loadFile($filename,	$delimiter, $enclosure, $noHeadersFlag) ) {
    		$this->cli_echo($this->csvHandler->getErrorMsg() . LF);exit();	
    	}
    	
    	while($row = $this->csvHandler->read() ) {
    		// for the reporting
    		if(!in_array($row['Semester'], $this->semesters)) $this->semesters[] = $row['Semester'];
    		
    		$new = false;
    		// check if already exists
    		$course = $this->course_repo->findOneByUnitIdAndSemester( (int)$row['UnitID'], $row['Semester'] );
    		if(!$course) { // if not, search by title
    			$course = $this->course_repo->findOneByTitleAndSemester( $row['UnitBezeichnung'], $row['Semester']  );
    		}
    		if(!$course) { // if not, do the fuzzy stuff
    			$courses = $this->course_repo->findByTitleAndSemesterWithFullTextSearch( $row['UnitBezeichnung'], $row['Semester'] );
    			if(count($courses) >= 1) {
    				$index = $this->chooseCourseByUserInput($courses, $row['UnitBezeichnung']);
    				if(is_int($index)) {
    					$course = $this->course_repo->findOneByUid($courses[$index]['uid']);
    				} else { // 'n' was entered
    					$course = new Tx_CiUniversity_Domain_Model_Course();
    					$new = true;
    				}
    			} else { // nothing found, create new
    				$course = new Tx_CiUniversity_Domain_Model_Course();
    				$new = true;
    			}
    		}
    		
    		if( $this->updateCourse($course, $row) ) {
	    		if($new === true) {
	    			$this->course_repo->add($course);
	    		} else {
	    			$this->course_repo->update($course);
	    		}
	  		}
    	}
    	
    	if($this->csvHandler->isError()) {
    		$this->cli_echo("Mapping aborded: ".$this->csvHandler->getErrorMsg() );
    	} else {	
		   	// persist all that could be processed successfully
	    	$this->persistanceManager->persistAll();
	    	
	    	
	    	$this->cli_echo(LF."Mapping finished! Creating report...".LF);	
	    	$this->createReport();
    	}
    	
    	// clean-up
    	$this->csvHandler->close();
    	$this->fullTextToCourseTable('remove');
    }
    
    private function createReport() {
    	$courses = $this->course_repo->findNonMapped($this->semesters);
    	if(count($courses) > 0) {
    		$file = fopen('report.txt','w'); 
    		foreach($courses as $course) {
    			fwrite($file, $course->getUid().', '.$course->getTitle().', '.$course->getSemester().chr(10) );
    		}
    		fclose($file);
    		$this->cli_echo(LF.'NOTICE: Some course could not be mapped! See report.txt for more information.'.LF);
    	}
    }
    
    private function chooseCourseByUserInput($courses, $title) {
    	$this->cli_echo('More than one matches for the course "'.$title.'" have been found:'.LF);
    	for($i=0;$i<count($courses);++$i) {
    		$this->cli_echo($i.'. '.$courses[$i]['title'].' ('.($courses[$i]['score']).')'.LF);
    	}
    	$this->cli_echo('n. Or type n to create new course!'.LF);
    	do { 
    		$index = $this->cli_keyboardInput();
    	} while(!intval($index) && !strtolower($index) === 'n');
    		
    	return $index;
    }
    
    private function updateCourse(Tx_CiUniversity_Domain_Model_Course &$course, $row) {
    	// pre-condition: no lecturer, no mapping/ import
    	if($row['Dozent'] === "NULL") {
    		$this->cli_echo("Skipping course: ".$row['UnitBezeichnung']." Lecturer is set NULL".LF);
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
    	$prof = $this->person_repo->findOneByFullnameWithoutPid($fullName);
    	if(!$prof) {
    		//throw new Exception("Person: ".$row['Dozent']." has not been found!");
    		$this->cli_echo("Skipping course:".$row['UnitBezeichnung']." Lecturer '".$row['Dozent']."' not found!".LF);
    		//var_dump($fullName); echo LF;
    		return false;
    	} 
    	
    	$course->setLecturer($prof);
    		
    	if($prof->getChairs()->count() > 0) {
    		$chairs = $prof->getChairs()->toArray();
    		$course->setChair($chairs[0]);
    		$course->setPid($chairs[0]->getPid());
    	} elseif($prof->getPid() === $this->lecturerPid ) {
    		$course->setPid( $this->coursePid );
    	} else {
    		$course->setPid( $prof->getPid() );
    	}
    	
    	$modules = t3lib_div::trimExplode("#", $row['Modul'], true);
    	foreach($modules as $module) {
    		$this->updateCourseModule($course,$module);
    	}
    	
    	return true;
    }
    
    /**
     * OBSOLETE
     * Parses the lecturer-name as given through the vinfo-csv-export
     * The algorithm first creates the title, by trying to find the first word,
     * that doesn't end with a ".". Then uses all the rest, but the last word for the firstnames
     * e.g. "Prof. Dr. Michaela A.C. Schumacher" would return:
     * 		actitle => "Prof. Dr."
     * 		firstname => "Michaela A.C."
     * 		lastname => "Schumacher"
     * 
     * @param string $lecturer
     */
    private function parseLecturerNameOld($lecturer) {	
    	$proAr = explode(' ', $lecturer);
    	$nameAr = array('','','');
    	for($x=0;$x<count($proAr);$x++) {
    		if( empty($nameAr[1]) && substr($proAr[$x], strlen($proAr[$x])-1, strlen($proAr[$x])) === '.') {
    			$nameAr[0] .= $proAr[$x].' ';
    		} elseif( ($x < count($proAr)-1) && ($proAr[$x] !== "v.") ) {
    			$nameAr[1] .= $proAr[$x].' ';
    		} else {
    			$nameAr[2] .= $proAr[$x].' ';
    		}
    	}
    	$nameAr[0] = trim($nameAr[0]);
    	$nameAr[1] = trim($nameAr[1]);
    	$nameAr[2] = trim($nameAr[2]);
    	
    	return array(
    		'firstname' => $nameAr[1],
    		'lastname' => $nameAr[2],
    		'actitle' => $nameAr[0]
    	);
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
    	$module = $this->module_repo->findOrCreateByTitle($mod, $this->coursePid);
    	if( $module->getUid() === NULL ) {
    		$this->cli_echo("New module '".$module->getTitle()."' created!".LF);
    		$this->persistanceManager->persistAll();
    	} 
    	
    	if(!$course->getModules()->contains($module)) {
    		$course->addModule($module);
    	}
    }
    
    private function fullTextToCourseTable($mode='add') {
    	if($mode === 'add') {
    		$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_ciuniversity_domain_model_course ADD FULLTEXT(title)');
    	} elseif($mode === 'remove') {
    		$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_ciuniversity_domain_model_course REMOVE FULLTEXT(title)');
    	}
    }
}