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
 * A repository for Courses
 */
class Tx_CiUniversity_Domain_Repository_CourseRepository extends Tx_Extbase_Persistence_Repository {
	
	public function findDemanded($chair,$semester, $module, $program) {
		$ret = array();
		$programQuery = $this->createQuery();
		$programSql = 'SELECT DISTINCT program FROM tx_ciuniversity_domain_model_course ORDER BY program';
		$programQuery->statement($programSql,array());
		$programQuery->getQuerySettings()->setReturnRawQueryResult(TRUE);
		$programs = $programQuery->execute();
		
		if(!empty($program)) {
			for($i=0;$i<count($programs);$i++) {
				if($programs[$i]['program'] !== $program) {
					unset($programs[$i]);
				}
			}
		}
			
		foreach($programs as $program) {
			$query = $this->createQuery();
			$constraints = array ();
			$constraints[] = $query->equals('program', $program['program']);
			if(!empty($chair)) $constraints[] = $query->equals('chair', $chair);
			if(!empty($semester)) $constraints[] = $query->equals('semester', $semester);
			if(!empty($module) && intval($module) ) $constraints[] = $query->equals('modules.uid', intval($module) );
			
			$query->matching( $query->logicalAND($constraints) );
			$query->getQuerySettings()->setRespectStoragePage(FALSE);
			$query->setOrderings( array('title' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING ) );
			$ret[ $program['program'] ] = $query->execute();
			if( count($ret[ $program['program'] ]) === 0) {
			$be = t3lib_div::makeInstance('Tx_Extbase_Persistence_Storage_Typo3DbBackend');
    		$query = $ret[ $program['program'] ]->getQuery();
			$parameters = array();
    		$statementParts = $be->parseQuery($query, $parameters);
			$sql = $be->buildQuery($statementParts, $parameters);
			$be->replacePlaceholders($sql, $parameters);	
			echo chr(10)."Query: ".$sql.chr(10);exit();
			}
		}
		return $ret;
	}
	
	public function findAllSemestersToArray() {
		$query = $this->createQuery();
		$sql = 'SELECT DISTINCT semester FROM tx_ciuniversity_domain_model_course ORDER BY year DESC, semester DESC';
		$query->statement($sql, array());
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		$res = $query->execute();
		
		$ret = array();
		foreach($res as $el) {
			$ret[] = array( 0 => $el['semester'], 1 => $el['semester'], 2 => '');
		}
		return $ret;
	}
	
	
	/* methods below are just needed for importing/ syncing */
	
	public function findNonMapped($semesters) {
		$query = $this->createQuery();
		$constraints = array();
		$sems = array();
		$constraints[] = $query->equals('unit_id', '');
		foreach($semesters as $semester) {
			$sems[] = $query->equals('semester', $semester);
		}
		if( count($sems) > 0) $constraints[] = $query->logicalOr($sems);
		if( count($constraints) < 2 ) {
			$query->matching( current($constraints) );
		} else {
			$query->matching( $query->logicalAnd($constraints) );
		}
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		return $query->execute();
	}
	
	public function findOneByUnitIdAndSemester($unitId, $semester) {
		$query = $this->createQuery();
		$query->matching( 
			$query->logicalAnd(
				$query->equals('unit_id', $unitId),
				$query->equals('semester', $semester)
			)
		 );
		
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		$query->setLimit(1);
		return $query->execute()->getFirst();
		
	}
	
	public function findOneByTitleAndSemester($title, $semester) {
		$query = $this->createQuery();
		$query->matching( 
			$query->logicalAnd( 
				$query->equals('title', $title),
				$query->equals('semester', $semester)
			)
		);
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		$query->setLimit(1);
		return $query->execute()->getFirst();
	}
	
	public function findByTitleAndSemesterWithFullTextSearch($title,$semester) {
		$query = $this->createQuery();
		
		$sql = "SELECT uid, title, MATCH(title) AGAINST(?) AS score
			FROM tx_ciuniversity_domain_model_course 
			WHERE semester = ? AND MATCH(title) AGAINST(?) 
            ORDER BY score DESC
        "; 
		
		$query->statement($sql, array($title,$semester,$title) );
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		return $query->execute();
	}
}
?>