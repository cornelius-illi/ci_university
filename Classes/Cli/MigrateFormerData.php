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

require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/PersonRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Person.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/ChairRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Chair.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/CourseRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Course.php');



class Tx_CiUniversity_Cli_MigrateFormerData extends t3lib_cli {

	protected $person_table_old = 'tx_jshuniversity_persons';
    protected $person_table_new = 'tx_ciuniversity_domain_model_course';
    protected $person_repo;
    
    protected $person_chair_mm_old = 'tx_jshuniversity_persons_chair_mm';
    
  	protected $chair_table_old = 'tx_jshuniversity_chairs';
    protected $chair_table_new = 'tx_ciuniversity_domain_model_chair';
    protected $chair_repo;
    
    protected $course_table_old = 'tx_jshuniversity_courses';
    protected $course_table_new = 'tx_ciuniversity_domain_model_course';
    protected $course_repo;
    protected $programMap;
    protected $skipped;
    
    
    # protected $dispatcher;
    protected $persistanceManager;
    
	
	function __construct() {
		// Running parent class constructor
        parent::t3lib_cli();
		$this->cli_options = array_merge($this->cli_options, array());
		$this->cli_help = array_merge(
			$this->cli_help,
			array(
				'name' => 'Import Former University Data',
				'synopsis' => $this->extKey . ' command [batchImport/ reverseImport] ###OPTIONS###',
				'description' => 'Imports the course- and person-data from the former university extension jsh_university',
				'examples' => 'typo3/cli_dispatch.phpsh',
				'author' => '(c) 2011 - Cornelius Illi'
			)
		);
		# $this->dispatcher = new Tx_Extbase_Dispatcher(); // only needed for core < 4.5.x 
		$this->persistanceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
		$this->chair_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_ChairRepository');
    	$this->person_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_PersonRepository');
    	$this->course_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_CourseRepository'); 
    	$this->programMap = $this->getProgramMap();
    	$this->skipped = array();
     }
    
    /**
     * Imports the complete jsh_university data within a batch
     * @return string
     */
    public function batchImport() {
    	$go = $this->cli_keyboardInput_yes('All previously migrated records will be lost. Continue?');
    	if(!$go) {
    		$this->cli_echo("Migration aborded by user!".LF);
    		exit(0);
    	}
    	
    	$this->checkPersonDuplicates();
    	
    	$this->reverseImport();
    	$this->emptyUploadsFolder();
    	
    	$this->importChairs();
    	$this->importPersons();
    	$this->migrateChairsHeads();
    	$this->migratePersonPictures();
    	$this->importCourses();
    }
    
    private function checkPersonDuplicates() {
    	$res = $GLOBALS['TYPO3_DB']->admin_query('SELECT actitle, lastname,firstname, GROUP_CONCAT(pid) as pid FROM tx_jshuniversity_persons WHERE deleted=0 and hidden=0 GROUP BY lastname,firstname HAVING COUNT(uid) > 1');
    	while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$question = $row['actitle']." ".$row['firstname']." ".$row['lastname']." has been found on multiple times: ".$row['pid'].LF."Person-Record on which page should be kept:  ";
    		do { 
    			$this->cli_echo($question);
    			$pid = $this->cli_keyboardInput();
    		} while(!intval($pid));
    		
    		$goodUser = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, pid','tx_jshuniversity_persons',
    		'pid='.$pid." AND lastname='".$row['lastname']."' AND firstname='".$row['firstname']."'",
    		'',
    		'',
    		'1');
    		
    		$goodUser = $goodUser[0];
    		
    		$badUser = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid','tx_jshuniversity_persons',
    		'uid!='.$goodUser['uid']." AND lastname='".$row['lastname']."' AND firstname='".$row['firstname']."'",
    		'',
    		'',
    		'1');
    		$badUser = $badUser[0];
    		
    		// the actual action
    		$update = array( 'uid_foreign' => $goodUser['uid']);
    		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_jshuniversity_persons',"uid=".$badUser['uid']);
    		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_jshuniversity_courses_lecturer_mm',"uid_foreign='".$badUser['uid'], $update);
    		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_jshuniversity_courses_tutor_mm',"uid_foreign='".$badUser['uid'], $update);
    	}
    	$GLOBALS['TYPO3_DB']->sql_free_result($res);
    }
    
    /**
     * Imports the jsh_university_person table incl. dependencies
     * @return string
     */
    private function importPersons() {
    	if($this->chair_repo->countAll() === 0) {
    		$this->cli_echo("Migration aborded: no chairs could be found in DB (required).".LF);
    		exit(0);	
    	}
    	
    	$count = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', $this->person_table_old,'deleted=0 AND hidden=0 AND migrated=0');
    	$i = 1;
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->person_table_old, 'deleted=0 AND hidden=0 AND migrated=0');
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$newPerson = new Tx_CiUniversity_Domain_Model_Person;
    		$newPerson->setLastname($row['lastname']);
    		$newPerson->setFirstname($row['firstname']);
    		$newPerson->setCustomline($row['customline']);
    		$newPerson->setActitle($row['actitle']);
    		$newPerson->setPhone($row['phone']);
   			$newPerson->setFax($row['fax']);
   			$newPerson->setRoom($row['room']);
   			$newPerson->setEmail($row['email']);
   			$newPerson->setCustomtext($row['customtext']);
   			$newPerson->setImage($row['image']);
   			$newPerson->setUrl($row['url']);
   			$newPerson->setPid($row['pid']);
   			$newPerson->setHidden($row['hidden']);
   			
   			$sections = $this->getSectionsByUid($row['uid']);
   			$newPerson->setSections($sections);
   			
   			$chairs = $this->getChairsByUid($row['uid']);
   			foreach($chairs as $chair)
   				$newPerson->addChair($chair);
   			
   			$this->person_repo->add($newPerson);
   			
   			# update migrated record
   			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->person_table_old, 'uid='.$row['uid'], array("migrated" => 1) );
   			$this->cli_echo(CR."Person ".$row['firstname'].' '.$row['lastname'].' migrated ('.$i.'/'.$count.")            ");
   			++$i;
   		}
   		$this->persistanceManager->persistAll();
   		$this->cli_echo(LF."Migration of person-records successfully completed!".LF);
    }
    
     /**
     * Imports the jsh_university_chairs table incl. dependencies
     * @return string
     */
    
    private function importChairs() {
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title,head,pid,hidden',$this->chair_table_old,'deleted=0 AND migrated=0');
    	
    	if($er = $GLOBALS['TYPO3_DB']->sql_error()) {
    		$this->cli_echo($er.LF); exit();
    	}	
    	
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$chair = new Tx_CiUniversity_Domain_Model_Chair;
    		$chair->setTitle($row['title']);
    		$chair->setPid($row['pid']);
    		$chair->setHidden($row['hidden']);
    		# head cannot be set hear, as person might not exist by now 
    		# $chair->setHead($row['head']);
    		$this->chair_repo->add($chair);
    		
    		# update migrated record
   			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->chair_table_old, 'uid='.$row['uid'], array("migrated" => 1) );
    	}
    		
    	$this->persistanceManager->persistAll();
   		$this->cli_echo("All Chairs migrated successfully!".LF);
    }
    
     /**
     * Imports the jsh_university_courses table incl. dependencies
     * @return string
     */
    private function importCourses() {
    	if($this->chair_repo->countAll() === 0) {
    		$this->cli_echo("Migration aborded: no chairs could be found in DB (required).".LF);
    		exit(0);	
    	}
    	if($this->person_repo->countAll() === 0) {
    		$this->cli_echo("Migration aborded: no persons could be found in DB (required).".LF);
    		exit(0);	
    	}

    	$count = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', $this->course_table_old,'deleted=0 AND migrated=0');
    	$i = 1;
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->course_table_old, 'deleted=0 AND migrated=0');
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$newCourse = new Tx_CiUniversity_Domain_Model_Course;
    		$newCourse->setTitle($row['title']);
    		$newCourse->setSemester($this->semesterMap[ $row['semester'] ]);
			$newCourse->setCredits($row['ects']);
    		$newCourse->setSws($row['sws']);
    		
    		// new: IT-Systems Engineering BA/MA, old: program + degree (but never worked)
    		$newCourse->setProgram($this->programMap[ $row['degree'] ]);
    		
    		// by convention: course may only have one head-lecturer
    		$lecturers = $this->getLecturersForChairByUid($row['uid']);
    		if($lecturers["head"]) {
    			$newCourse->setLecturer($lecturers["head"]); 
    		}
    		foreach($lecturers["other"] as $lec) {
    			$newCourse->addOtherLecturer($lec);	
    		}
    		
    		$newCourse->setTeachingForm($this->mapTeachingForm((string)$row['teaching_form']));
    		$newCourse->setGraded($row['graded']);
    		$newCourse->setEnrolmentDeadline( strftime("%d.%m.%Y", $row['enrolment']));
    		$newCourse->setEnrolmentType($this->mapEnrolmentType( $row['enrolment_type'] ) );
    		$newCourse->setMaxParticipants($row['max_participants']);
    		if($chair = $this->getChairByUid($row['uid'])) {
    			$newCourse->setChair( $chair );
    		}
    		$tutors = $this->getTutorsByUid($row['uid']);
    		foreach($tutors as $tutor) {
    			$newCourse->addTutor($tutor);	
    		}
    		
    		$newCourse->setDescription($row['description']);
    		$newCourse->setRequirements($row['requirements']);
    		$newCourse->setExamination($row['examination']);
    		$newCourse->setLiterature($row['literature']);
    		$newCourse->setLearning($row['learning']);
    		$newCourse->setDates($row['dates']);
    		$newCourse->setUrl($row['url']);
    		$semester = $this->mapSemester($row['uid']);
    		if(is_null($semester)) {
    			// if no semester set, then course never appeared on site
    			// therefore migration can be skipped
    			$this->skipped[] = $row['title'];
    			continue; 
    		}
    		
    		$newCourse->setSemester($semester['title']);
    		$newCourse->setYear($semester['year']);
    		$newCourse->setPid($row['pid']);
    		
    		$this->course_repo->add($newCourse);
   			
    		# update migrated record
    		$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->person_table_old, 'uid='.$row['uid'], array("migrated" => 1) );
   			$this->cli_echo(CR."Course ".$row['title'].' migrated ('.$i.'/'.$count.")            ");
   			++$i;
    	}
    	$this->persistanceManager->persistAll();
   		$this->cli_echo(LF."Migration of course-records successfully completed!".LF);
   		if(!empty($this->skipped)) {
	   		$this->cli_echo("The following courses have been skipped: ".LF);
	   		foreach($this->skipped as $course) {
	   			$this->cli_echo(TAB.$course.LF);
	   		}
   		}
    }
    
    private function reverseImport() {
    	$this->truncateTables();
    	$this->addMigratedColumnToTable($this->person_table_old);
    	$fields_values = array( 'migrated' => 0, 'deleted' => 0 );
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			$this->person_table_old,
			'migrated=1',
			$fields_values
		);
		$this->cli_echo("Table 'persons' has been restored!".LF);
		$this->addMigratedColumnToTable($this->course_table_old);
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			$this->course_table_old,
			'migrated=1',
			$fields_values
		);
		$this->cli_echo("Table 'courses' has been restored!".LF);
		$this->addMigratedColumnToTable($this->chair_table_old);
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			$this->chair_table_old,
			'migrated=1',
			$fields_values
		);
		$this->cli_echo("Table 'chair' has been restored!".LF);
		$this->cli_echo("All data has successfully been restored!".LF);
    }
    
    private function getLecturersForChairByUid($uid) {
    	$ret = array();
		// $select, $local_table, $mm_table, $foreign_table, $whereClause
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
    		'tx_jshuniversity_persons.actitle AS actitle, tx_jshuniversity_persons.lastname AS lastname, tx_jshuniversity_persons.firstname AS firstname',
    		'',
    		'tx_jshuniversity_courses_lecturer_mm',
    		'tx_jshuniversity_persons',
    		'AND tx_jshuniversity_courses_lecturer_mm.uid_local='.$uid,
    		'',
    		'tx_jshuniversity_courses_lecturer_mm.sorting'
    	);
    	
    	if($er = $GLOBALS['TYPO3_DB']->sql_error()) {
    		$this->cli_echo($er.LF); exit();
    	}
    	
    	$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    	$ret["head"] = $this->person_repo->findOneByFullnameWithoutPid($row);
    	
    	$names = array();
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$names[] = $row;
    	}
    	
    	$GLOBALS['TYPO3_DB']->sql_free_result($res);
    	$ret["other"] = $this->person_repo->findAllByFullnameWithoutPid($names);	
    	
    	return $ret;
    }
    
   	private function mapTeachingForm($teachingForm) {
   		/*
    	 * 	"tx_jshuniversity_courses.teaching_form.I.0" => "Vorlesung",
		 *	"tx_jshuniversity_courses.teaching_form.I.1" => "Projekt",
		 *	"tx_jshuniversity_courses.teaching_form.I.2" => "Seminar",
		 *	"tx_jshuniversity_courses.teaching_form.I.3" => "�bung",
		 *	"tx_jshuniversity_courses.teaching_form.I.4" => "Forschungsseminar",
		 *  new: 
		 *  BP = Bachelor-Projekt
		 *  P = Projekt
		 *  PS = Projekt-Seminar (not used)
		 *  S = Seminar
		 *  SP = Projekt-Seminar (used)
		 *  V = Vorlesung
		 *  VP = Vorlesung/ Projekt
		 *  VU = Vorlesung mit �bung
		 *  
		 *  0 => V
		 *  "0,3"  => VU
		 *  "0,1" => VP
		 *  "0,3,1" => VU
		 *  2 => S
		 *  "2,1" => SP
		 *  "1,2 => PS
		 *  1 => P
    	 */
   		switch ($teachingForm) {
   			case  "0":
   				$res = "V";
   			break;
   			
   			case "0,3":
   				$res = "VU";
   			break;
   			
   			case "0,1":
   				$res = "VP";
   			break;
   			
   			case "0,3,1":
   				$res = "VU";
   			break;
   			
   			case "2":
   				$res = "S";
   			break;
   			
   			case "2,1":
   				$res = "SP";
   			break;
   			
   			case "1,2":
   				$res = "PS";
   			break;
   			
   			case "1":
   				$res = "P";
   			break;
   			
   			default:
   				$res = "";
   			break;
   		}
   		
   		return $res;
   	}
   	
   	private function mapEnrolmentType($type) {
   		/**
   		 * 
   		 * "tx_jshuniversity_courses.enrolment_type.I.0" => "Wahlfach",	
		 * "tx_jshuniversity_courses.enrolment_type.I.1" => "Kernfach",	
		 * "tx_jshuniversity_courses.enrolment_type.I.2" => "Wahl/Kernveranstaltung",		
   		 */
   		switch($type) {
   			case 0:
   				return "Wahl";
   			break;
   			case 1:
   				return "Pflicht";
   			break;
   			case 2:
   				return "Wahlpflicht";
   			break;
   		}
   	}
   	
   	private function mapSemester($uid) {
   		// $select, $local_table, $mm_table, $foreign_table, $whereClause
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
    		'tx_jshuniversity_semesters.term AS term, tx_jshuniversity_semesters.year AS year',
    		'',
    		'tx_jshuniversity_courses_semester_mm',
    		'tx_jshuniversity_semesters',
    		'AND tx_jshuniversity_courses_semester_mm.uid_local='.$uid,
    		'',
    		'1'
    	);
    	
    	if($er = $GLOBALS['TYPO3_DB']->sql_error()) {
    		$this->cli_echo($er.LF); exit();
    	}
    	$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    	$GLOBALS['TYPO3_DB']->sql_free_result($res);	
    	if($row['term'] === "WS") {
    		$ret['title'] = "Wintersemester ".$row['year']."/".(string)((int)$row['year']+1);
    	} elseif($row['term'] === "SS") {
    		$ret['title'] = "Sommersemester ".$row['year'];
    	} else {
    		// throw new Exception("No semester set for course with UID:".$uid);
    		return NULL;
    	}
    	$ret['year'] = $row['year'];
    	return $ret;
   	}
   	
   	private function getTutorsByUid($uid) {
   		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
    		'tx_jshuniversity_persons.actitle AS actitle, tx_jshuniversity_persons.lastname AS lastname, tx_jshuniversity_persons.firstname AS firstname',
    		'',
    		'tx_jshuniversity_courses_tutor_mm',
    		'tx_jshuniversity_persons',
    		'AND tx_jshuniversity_courses_tutor_mm.uid_local='.$uid
    	);
    	
    	if($er = $GLOBALS['TYPO3_DB']->sql_error()) {
    		$this->cli_echo($er.LF); exit();
    	}
    	
    	$names = array();
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$names[] = $row;
    	}
    	 	
    	$GLOBALS['TYPO3_DB']->sql_free_result($res);	
    	return $this->person_repo->findAllByFullnameWithoutPid($names);
    }
    
    private function emptyUploadsFolder() {
    	$dirs = array(
    		'uploads/tx_ciuniversity/' , 
    		'uploads/tx_ciuniversity/rte/'
    	);
    	foreach($dirs as $dir) {
    		$files = t3lib_div::getFilesInDir(PATH_site.$dir);	
    		foreach($files as $file) {
    			unlink(PATH_site.$dir.$file);
    		}
    	}
		$this->cli_echo('All pictures files deleted!'.LF);
    }
    
    private function migratePersonPictures() {
    	$dirs = array(
    		'uploads/tx_jshuniversity/' => 'uploads/tx_ciuniversity/' , 
    		'uploads/tx_jshuniversity/rte/' => 'uploads/tx_ciuniversity/rte/'
    	);
    	foreach($dirs as $dir => $dest) {
    		$files = t3lib_div::getFilesInDir(PATH_site.$dir);	
    		foreach($files as $file) {
    			copy(PATH_site.$dir.$file,PATH_site.$dest.$file);
    		}
    	}
		$this->cli_echo('All pictures files copied!'.LF);
    }
    
    private function migrateChairsHeads() {
    	if($this->person_repo->countAll() === 0) {
    		$this->cli_echo("Migration aborded: no persons could be found in DB (required).".LF);
    		exit(0);	
    	}
    	
    	$chairs = $this->chair_repo->findAllWithoutPid();	
    	foreach($chairs as $chair) {
    		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
	    		'tx_jshuniversity_chairs_head_mm.uid_foreign AS head',
	    		'tx_jshuniversity_chairs',
	    		'tx_jshuniversity_chairs_head_mm',
	    		'tx_jshuniversity_persons',
	    		"AND tx_jshuniversity_chairs.title='".$chair->getTitle()."' AND tx_jshuniversity_persons.migrated=1"
	    		);
    		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    		
    		if(empty($row)) continue;
    		
    		$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('lastname,firstname,actitle',$this->person_table_old,'uid='.$row['head']);
    		$fullName = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2);
    		$GLOBALS['TYPO3_DB']->sql_free_result($res);

    		$head = $this->person_repo->findOneByFullnameWithoutPid($fullName);
    		if(!empty($head)) {
    			$chair->setHead( $head );		
    		}
    		
    		$this->chair_repo->update($chair);	
    	}
    	
    	$this->persistanceManager->persistAll();
    	$this->cli_echo("Successfully mapped head of chairs!".LF);
	}
	
	private function getSectionsByUid($uid) {
		$ret = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
    		'tx_jshuniversity_sections.title AS title',
    		'',
    		'tx_jshuniversity_persons_section_mm',
    		'tx_jshuniversity_sections',
    		'AND tx_jshuniversity_persons_section_mm.uid_local='.$uid
    	);
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$ret .= $row['title'].', ';
    	}
    	if(!empty($ret)) $ret = substr($ret,0,-2);
    	
    	$GLOBALS['TYPO3_DB']->sql_free_result($res);
    	return $ret;
	}
	
	private function getChairsByUid($uid) {
		$chairs = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
    		$this->chair_table_old.'.*',
    		'',
    		$this->person_chair_mm_old,
    		$this->chair_table_old,
    		'AND '.$this->person_chair_mm_old.'.uid_local='.$uid
    	);
    	while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$chair = $this->chair_repo->findOneByTitleWithoutPid($row['title']);
    		if($chair) $chairs[] = $chair;
    	}
    	
    	$GLOBALS['TYPO3_DB']->sql_free_result($res);
    	return $chairs;
	}
	
	private function getChairByUid($uid) {
		// $select, $local_table, $mm_table, $foreign_table, $whereClause
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
    		'tx_jshuniversity_chairs.title AS title',
    		'',
    		'tx_jshuniversity_courses_chair_mm',
    		'tx_jshuniversity_chairs',
    		'AND tx_jshuniversity_courses_chair_mm.uid_local='.$uid,
    		'',
    		'tx_jshuniversity_courses_chair_mm.sorting',
    		'1'
    	);
    	
		if($er = $GLOBALS['TYPO3_DB']->sql_error()) {
    		$this->cli_echo($er.LF); exit();
    	}
    	
    	$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    	if(!$row) {
    		return false;
    	} else {
    		return $this->chair_repo->findOneByTitleWithoutPid($row);
    	}
	}
      
    private function truncateTables($tables=array()) {
    	if(empty($tables)) {
    		# delete all records of all tables of this extension
    		$tables = array(
    			'tx_ciuniversity_domain_model_course',
    			'tx_ciuniversity_domain_model_person',
    			'tx_ciuniversity_domain_model_chair',
    			'tx_ciuniversity_domain_model_module',
    			'tx_ciuniversity_domain_model_course',
    			'tx_ciuniversity_person_chair_mm',
    			'tx_ciuniversity_course_module_mm',
    		);
    	}
    	foreach($tables as $table) {
    		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery($table);
    	}
    	$this->cli_echo("All extension-tables truncated!".LF);
    }
    
    private function addMigratedColumnToTable($tableName) {
		$res = $GLOBALS['TYPO3_DB']->admin_query('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "'.
			$tableName.'" AND COLUMN_NAME = "migrated"');
		
		if(!$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$GLOBALS['TYPO3_DB']->admin_query(
    			'ALTER TABLE '.$tableName." ADD COLUMN migrated tinyint(4) unsigned NOT NULL default '0'");
		}
    }
    
    /**
     * 
     * In order to prevent reaching memory limits,
     * only 100 records per iteration are migrated
     * @param array $count
     */
    private function createLimitsAndOffsets($count) {
    	$res = array();
    	$div = $count/100;
    	$mod = $count%100;
    	for($i=0;$i<$div;$i++) {
    		$res[$i*100] = 100;
    	}
    	
    	if($mod) {
    		$res[$div*100] = $mod;
    	}
    	return $res;
    }
    
    private function getProgramMap() {
    	// 0= leer, 2=ba, 4=ma, 6=ba/ma, 12=ma,phd
    	return array( 
    		0 => 'IT-Systems Engineering BA',
    		2 => 'IT-Systems Engineering BA',
    		4 => 'IT-Systems Engineering MA',
    		6 => 'IT-Systems Engineering BA',
    		12 => 'IT-Systems Engineering MA'
    	);
    }
    
}	
?>