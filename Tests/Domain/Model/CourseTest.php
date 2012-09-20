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

class Tx_CiUniversity_Domain_Model_CourseTest extends Tx_Extbase_BaseTestCase {
	
	/**
	 * @test
	 */
	function anInstanceOfCourseCanBeConstructed() {
		$course = new Tx_CiUniversity_Domain_Model_Course('Title'); 
		$this->assertEquals('Title', $course->getTitle());
	}
	
	/**
	 * @test
	 */
	function anInstanceOfCourseWithSemesterCanBeConstructed() {
		$course = new Tx_CiUniversity_Domain_Model_Course('Title');
		$mockSemester = $this->getMock('Tx_CiUniversity_Domain_Model_Semester');
		$course->setSemester($mockSemester);
		$this->assertEquals($mockSemester,$course->getSemester());
	}
	
	/**
	 * @test
	 */
	function anInstanceOfCourseWithProgramCanBeConstructed() {
		$course = new Tx_CiUniversity_Domain_Model_Course('Title');
		$program = $this->getMock('Tx_CiUniversity_Domain_Model_Program');
		$course->setProgram($program);
		$this->assertEquals($program,$course->getProgram());
	}
	
	/**
	 * @test
	 */
	function anInstanceOfCourseWithModuleCanBeConstructed() {
		$course = new Tx_CiUniversity_Domain_Model_Course('Title');
		$module = $this->getMock('Tx_CiUniversity_Domain_Model_Module');
		$course->addModule( $module );
		$this->assertTrue($course->getModules()->contains($module));
	}
	
	/**
	 * @test
	 */
	function anInstanceOfCourseWithSegmentCanBeConstructed() {
		$course = new Tx_CiUniversity_Domain_Model_Course('Title');
		$segment = $this->getMock('Tx_CiUniversity_Domain_Model_Segment');
		$course->addSegment($segment);
		$this->assertTrue($course->getSegments()->contains($segment));
	}
}