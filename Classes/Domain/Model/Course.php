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

class Tx_CiUniversity_Domain_Model_Course extends Tx_Extbase_DomainObject_AbstractEntity {
	/** 
	 * @var int 
	**/
	protected $unitId;
	
	/** 
	 * @var string 
	**/
	protected $title = '';
	
	/** 
	 * @var int 
	**/
	protected $year;
	
	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Module> the module
	 */
	protected $modules;
	
	/**
	 * @var string the semester
	 */
	protected $semester;
	
	/**
	 * @var int
	 */
	protected $credits;
	
	/**
	 * @var int
	 */
	protected $sws;
	
	/**
	 * @var string the program
	 */
	protected $program;
	
	/**
	 * @var Tx_CiUniversity_Domain_Model_Person The lecturer
	 */
	protected $lecturer;
	
	/**
	 * @var string
	 */
	protected $teachingForm;
	
	/**
	 * @var boolean
	 */
	protected $graded = 1;
	
	/**
	 * @var string
	 */
	protected $enrolmentDeadline;
  	
	/**
	 * @var string
	 */
	protected $enrolmentType;
  	
	/**
	 * @var int
	 */
	protected $maxParticipants;
	
	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person> The other lecturers
	 * @lazy
	 */
	protected $otherLecturers;
	
	/**
	 * @var Tx_CiUniversity_Domain_Model_Chair
	 */
	protected $chair;
	
	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person> The tutors
	 */
	protected $tutors;
	
	/**
	 * @var string
	 */
	protected $description= '';
	
	/**
	 * @var string
	 */
	protected $requirements = '';
	
	/**
	 * @var string
	 */
	protected $examination = '';
	
	/**
	 * @var string
	 */
	protected $literature ='';
	
	/**  
	 * @var string
	 */
	protected $learning = '';
	
	/**
	 * @var string
	 */
	protected $dates = '';
	
	/**
	 * @var string
	 */
	protected $url = '';
	
	/**
	 * @var bool
	 */
	protected $onlyUseCoursePageContents = 0;
	
	
	public function __construct($title='') {
		$this->setTitle($title);
		$this->otherLecturers = new Tx_Extbase_Persistence_ObjectStorage;
		$this->tutors = new Tx_Extbase_Persistence_ObjectStorage;
		$this->modules = new Tx_Extbase_Persistence_ObjectStorage;
	}
	
	/**
	* returns the UnitID of a course
	* @return string
	*/
	public function getUnitId()
	{
	    return $this->unitID;
	}
	
	/**
	* sets the UnitID of a course
	* @param string $title
	* @return void
	*/
	public function setUnitId($unitId)
	{
	    $this->unitId = $unitId;
	}
	
	/**
	* returns title
	* @return string
	*/
	public function getTitle()
	{
	    return $this->title;
	}
	
	/**
	* sets title
	* @param string $title
	* @return void
	*/
	public function setTitle($title)
	{
	    $this->title = $title;
	}
	
	/**
	* returns the year (internal for sorting)
	* @return string
	*/
	public function getYear()
	{
	    return $this->year;
	}
	
	/**
	* sets the year
	* @param int $year
	* @return void
	*/
	public function setYear($year)
	{
	    $this->year = $year;
	}
	
	/**
	* Returns the module assigned to the course
	* 
	* @return Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person>
	*/
	public function getModules()
	{
	    return $this->modules;
	}
	
	/**
	* Returns the module assigned to the course
	* 
	* @return Tx_Extbase_Persistence_ObjectStorage
	*/
	public function getModulesString()
	{
	    $res ='';
	    foreach($this->modules as $module) {
	    	$res .= $module->getTitle().', ';
	    }
	    if(strlen($res) > 2) {
	    	$res = substr($res,0,-2);
	    }
	    return trim($res);
	}
	
	/**
	 * Sets the module of the course
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage The modules the course is assigned to
	 * @return void
	 */
	public function setModules(Tx_Extbase_Persistence_ObjectStorage $modules) {
		$this->modules = $modules;
	}
	
	public function addModule(Tx_CiUniversity_Domain_Model_Module $module) {
		$this->modules->attach($module);
	}
	
	public function removeModule(Tx_CiUniversity_Domain_Model_Module $module) {
		$this->modules->detach($module);
	}
	
	/**
	* Returns the semester of a course
	* @return Tx_CiUniversity_Domain_Model_Semester
	*/
	public function getSemester()
	{
	    return $this->semester;
	}
	
	/**
	* Sets the semester of a course
	* 
	* @param string The semester to be set
	* @return void
	*/
	public function setSemester($semester)
	{
	    $this->semester = $semester;
	}
	
	/**
	* returns the credits of a course 
	* @return int
	*/
	public function getCredits()
	{
	    return $this->credits;
	}
	
	/**
	* sets the credits of a course
	* @param int $ects
	* @return void
	*/
	public function setCredits($credits)
	{
	    $this->credits = $credits;
	}
	
	/**
	* returns sws of a course
	* @return int
	*/
	public function getSws()
	{
	    return $this->sws;
	}
	
	/**
	* sets the sws of a course
	* @param int $sws
	* @return void
	*/
	public function setSws($sws)
	{
	    $this->sws = $sws;
	}
	
	/**
	* Returns the program of a course
	* @return string
	*/
	public function getProgram()
	{
	    return $this->program;
	}
	
	/**
	* Sets the program of a course
	* 
	* @param string The program to be set
	* @return void
	*/
	public function setProgram($program)
	{
	    $this->program = $program;
	}
	
	/**
	* Returns the lecturer (main, synced) of a course
	* @return Tx_CiUniversity_Domain_Model_Person
	*/
	public function getLecturer()
	{
	    return $this->lecturer;
	}
	
	public function getAllLecturerString() {
		$result = $this->lecturer->getFullname().', ';
		foreach($this->otherLecturers as $lecturer)
			$result .= $lecturer->getFullname();
		return trim(substr($result,0,-2));
	}
	
	/**
	* Sets the lecturer (main, synced) of a course
	* 
	* @param Tx_CiUniversity_Domain_Model_Person The lecturer to be set
	* @return void
	*/
	public function setLecturer(Tx_CiUniversity_Domain_Model_Person $lecturer)
	{
	    $this->lecturer = $lecturer;
	}
	
	/**
	* Returns the teaching form of a course
	* @return string
	*/
	public function getTeachingForm()
	{
	    return $this->teachingForm;
	}
	
	/**
	* Sets the teaching form of a course
	* 
	* @param string The teaching form to be set
	* @return void
	*/
	public function setTeachingForm($teachingForm)
	{
	    $this->teachingForm = $teachingForm;
	}
	
	/**
	* Returns wether a course is graded or not
	* @return boolean
	*/
	public function getGraded()
	{
	    return $this->graded;
	}
	
	/**
	* Sets the graded value of a course
	* 
	* @param boolean The graded value to be set
	* @return void
	*/
	public function setGraded($graded)
	{
	    $this->graded = $graded;
	}
	
	/**
	* Returns the enrolment deadline of a course
	* @return string
	*/
	public function getEnrolmentDeadline()
	{
	    return $this->enrolmentDeadline;
	}
	
	/**
	* Sets the enrolment deadline of a course
	* 
	* @param string The enrolement deadline to be set
	* @return void
	*/
	public function setEnrolmentDeadline($enrolmentDeadline)
	{
	    $this->enrolmentDeadline = $enrolmentDeadline;
	}
	
	/**
	* Returns the enrolment type of a course
	* @return string
	*/
	public function getEnrolmentType()
	{
	    return $this->enrolmentType;
	}
	
	/**
	* Sets the enrolment type of a course
	* 
	* @param string The enrolment type to be set
	* @return void
	*/
	public function setEnrolmentType($enrolmentType)
	{
	    $this->enrolmentType = $enrolmentType;
	}
	
	/**
	* Returns the enrolment type of a course
	* @return int
	*/
	public function getMaxParticipants()
	{
	    return $this->maxParticipants;
	}
	
	/**
	* Sets the enrolment type of a course
	* 
	* @param string The enrolment type to be set
	* @return void
	*/
	public function setMaxParticipants($maxParticipants)
	{
	    $this->maxParticipants = $maxParticipants;
	}
	
	/**
	* Returns the segments assigned to the course
	* 
	* @return Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person>
	*/
	public function getOtherLecturers()
	{
	    return clone $this->otherLecturers;
	}
	
	/**
	 * Sets the other lecturers of the course
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person> The other lecturers assigned to the course
	 * @return void
	 */
	public function setOtherLecturers(Tx_Extbase_Persistence_ObjectStorage $lecturers) {
		$this->otherLecturers = $lecturers;
	}
	
	/**
	 * Adds a tutor to the course
	 *
	 * @param Tx_CiUniversity_Domain_Model_Person The other lecturer to be added
	 * @return void
	 */
	public function addOtherLecturer(Tx_CiUniversity_Domain_Model_Person $lecturer) {
		$this->otherLecturers->attach($lecturer);
	}
	
	/**
	* Returns the chair of a course
	* @return Tx_CiUniversity_Domain_Model_Chair
	*/
	public function getChair()
	{
	    return $this->chair;
	}
	
	/**
	* Sets the chair of a course
	* 
	* @param Tx_CiUniversity_Domain_Model_Chair The chair to be set
	* @return void
	*/
	public function setChair(Tx_CiUniversity_Domain_Model_Chair $chair)
	{
	    $this->chair = $chair;
	}
	
	/**
	* Returns the tutors assigned to the course
	* 
	* @return Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person>
	*/
	public function getTutors()
	{
	    return clone $this->tutors;
	}
	
	/**
	 * Sets the other lecturers of the course
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Person> The other lecturers assigned to the course
	 * @return void
	 */
	public function setTutors(Tx_Extbase_Persistence_ObjectStorage $tutors) {
		$this->tutors = $tutors;
	}
	
	/**
	 * Adds a tutor to the course
	 *
	 * @param Tx_CiUniversity_Domain_Model_Segment The segment to be added
	 * @return void
	 */
	public function addTutor(Tx_CiUniversity_Domain_Model_Person $tutor) {
		$this->tutors->attach($tutor);
	}
	
	private function cleanHTML($attr) {
		// removes all empty-tags
		// see: http://stackoverflow.com/questions/3809108/how-to-remove-empty-paragraph-tags-from-string
		$pattern = 	'/<[^\/>]*>([\s]?)*<\/[^>]*>/';
		return trim(preg_replace($pattern, '', $attr));
	}
	
	/**
	* Returns the description of a course
	* @return string
	*/
	public function getDescription()
	{
	    return $this->cleanHTML($this->description);
	}
	
	/**
	* Sets the description of a course
	* 
	* @param string The description to be set
	* @return void
	*/
	public function setDescription($description)
	{
	    $this->description = $this->cleanHTML($description);
	}
	
	/**
	* Returns the requirements of a course
	* @return string
	*/
	public function getRequirements()
	{
	    return $this->cleanHTML($this->requirements);
	}
	
	/**
	* Sets the requirements of a course
	* 
	* @param string The requirements to be set
	* @return void
	*/
	public function setRequirements($requirements)
	{
	    $this->requirements = $this->cleanHTML($requirements);
	}
	
	/**
	* Returns the examination of a course
	* @return string
	*/
	public function getExamination()
	{
	    return $this->cleanHTML($this->examination);
	}
	
	/**
	* Sets the examination of a course
	* 
	* @param string The examination to be set
	* @return void
	*/
	public function setExamination($examination)
	{
	    $this->examination = $this->cleanHTML($examination);
	}
	
	/**
	* Returns the required literature of a course
	* @return string
	*/
	public function getLiterature()
	{
	    return $this->cleanHTML($this->literature);
	}
	
	/**
	* Sets the required literature of a course
	* 
	* @param string The literature to be set
	* @return void
	*/
	public function setLiterature($literature)
	{
	    $this->literature = $this->cleanHTML($literature);
	}
	
	/**
	* Returns the learning of a course
	* @return string
	*/
	public function getLearning()
	{
	    return $this->cleanHTML($this->learning);
	}
	
	/**
	* Sets the learning of a course
	* 
	* @param string The learning to be set
	* @return void
	*/
	public function setLearning($learning)
	{
	    $this->learning = $this->cleanHTML($learning);
	}
	
	/**
	* Returns the dates of a course
	* @return string
	*/
	public function getDates()
	{
	    return $this->cleanHTML($this->dates);
	}
	
	/**
	* Sets the dates of a course
	* 
	* @param string The dates to be set
	* @return void
	*/
	public function setDates($dates)
	{
	    $this->dates = $this->cleanHTML($dates);
	}
	
	/**
	* Returns the url of a course
	* @return string
	*/
	public function getUrl()
	{
	    return $this->url;
	}
	
	/**
	* Sets the URL of a course
	* 
	* @param string The URL to be set
	* @return void
	*/
	public function setUrl($url)
	{
	    $this->url = $url;
	}
	
	/**
	* Returns wether only the course page should be displayed instead
	* @return bool
	*/
	public function getOnlyUseCoursePageContents()
	{
	    return $this->onlyUseCoursePageContents;
	}
	
	/**
	* Sets the onlyUseCoursePageContents value of a course
	* 
	* @param bool The onlyUseCoursePageContents value to be set
	* @return void
	*/
	public function setOnlyUseCoursePageContents($onlyUseCoursePageContents)
	{
	    $this->onlyUseCoursePageContents = $onlyUseCoursePageContents;
	}
}
?>