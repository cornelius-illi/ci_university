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
 * A repository for People
 */
class Tx_CiUniversity_Domain_Repository_PersonRepository extends Tx_Extbase_Persistence_Repository {
	
	public function findAllPaginate($searchTerms, $mode, $modes, $page, $perPage) {
		$query = $this->createQuery();
		$constraints = array();
		$modeConstraints = array();
		
		if (!empty($searchTerms)) {
			foreach($searchTerms as $sword) {
				$constraints[] = $query->like('firstname', '%'.$sword.'%');
				$constraints[] = $query->like('lastname', '%'.$sword.'%');	
			}
		} elseif($mode && in_array( $mode, $modes)) {
			for($i;$i<strlen($mode);++$i) {
				$constraints[] = $query->like('lastname',$mode[$i].'%');
			}	
		}
			
		if(count($constraints) > 0)
			$query->matching( $query->logicalOr($constraints) );	
		
		$res = Tx_CiUniversity_Utility_Pagination::prepareQuery($query, $page, $perPage);		
		$query->setOrderings( array('lastname' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING ) );
		return array(
			"pageCount" => $res["pageCount"], 
			"page" => $page, 
			"count" => $res["count"], 
			"result" => $query->execute() 
		);
	}
	
	/**
	 * 
	 * Checks if a person with same fullname-combination already exists.
	 * If so the name of the page is returned
	 * @param Tx_CiUniversity_Domain_Model_Person $person
	 */
	public function personExists(Tx_CiUniversity_Domain_Model_Person $person) {
		$query = $this->createQuery();
		$constraints[] = $query->equals('firstname', $person->getFirstname() );
		$constraints[] = $query->equals('lastname', $person->getLastname() );
		$constraints[] = $query->equals('actitle', $person->getActitle() );
		$constraints[] = $query->logicalNot( $query->equals('uid', $person->getUid() ) );
		$constraints[] = $query->equals('deleted', 0 );
	
		
		$query->matching( $query->logicalAnd($constraints) );
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		$result = $query->execute();
		
		if(count($result) > 0) {
			$arg = array( $result[0]->getPid() );
			$sql = 'SELECT p.title AS title, p.uid AS uid FROM pages p WHERE p.uid=?';
			$query = $this->createQuery();
			$query->statement($sql, $arg);
			$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
			return $query->execute();	
		}
		
		return FALSE;
	}
	
	/*
	 * functions below are needed for import (scheduler)
	 */
	
	/**
	 * Returns a Person-Object based on academic title, lastname and firstname
	 * This function is required when migrating data from jsh_university
	 * @param array $fullName
	 */
	public function findOneByFullnameWithoutPid($fullName, $throwError=false) {
		$query = $this->createQuery();
    	# identification - old record, new one
    	$constraints = array(
    		$query->equals('lastname',trim($fullName['lastname'])),
    		$query->equals('firstname',trim($fullName['firstname']))
    		//$query->equals('actitle',trim($fullName['actitle'])) - may differ, therefore don't use
    	);
    	$query->matching( $query->logicalAND( $constraints ) );
    	$query->getQuerySettings()->setRespectStoragePage(FALSE);
    	$query->getQuerySettings()->setRespectEnableFields(FALSE);
    	$result = $query->execute();
		if( count($result) === 0 && $throwError) {
			$be = t3lib_div::makeInstance('Tx_Extbase_Persistence_Storage_Typo3DbBackend');
    		$query = $result->getQuery();
			$parameters = array();
    		$statementParts = $be->parseQuery($query, $parameters);
			$sql = $be->buildQuery($statementParts, $parameters);
			$be->replacePlaceholders($sql, $parameters);	
			echo chr(10)."Query: ".$sql.chr(10);
			var_dump($fullName);exit();
		} else {
    		return $result->getFirst();
		}
    }
    
    public function findOneByFullnameUmlauts($fullName) {
    	$query = $this->createQuery();
    	# identification - old record, new one
    	$lastname = trim($fullName['lastname']);
    	$pos = strlen($lastname);
    	$ae = stripos($lastname, 'ä');
    	$oe = stripos($lastname, 'ö');
    	$ue = stripos($lastname, 'ü');
    	if(is_int($ae)) $pos = $ae;
    	if(is_int($ue) && $ue < $pos) $pos = $ue;
    	if(is_int($oe) && $oe < $pos) $pos = $oe;
    	
    	$lastname = substr($lastname,0,$pos);
    	
    	$constraints = array(
    		//$query->like('lastname',$lastname.'%'),
    		$query->equals('lastname',trim($fullName['lastname'])),
    		$query->equals('firstname',trim($fullName['firstname']))
    	);
    	
    	$query->matching( $query->logicalAND( $constraints ) );
    	$query->getQuerySettings()->setRespectStoragePage(FALSE);
    	$query->getQuerySettings()->setRespectEnableFields(FALSE);
    	$res = $query->execute();
    	if( count($res) === 0) {
			$be = t3lib_div::makeInstance('Tx_Extbase_Persistence_Storage_Typo3DbBackend');
    		$query = $res->getQuery();
			$parameters = array();
    		$statementParts = $be->parseQuery($query, $parameters);
			$sql = $be->buildQuery($statementParts, $parameters);
			$be->replacePlaceholders($sql, $parameters);	
			echo chr(10)."Query: ".$sql.chr(10);exit();
		} else {
    		return $res->getFirst();
		}
    }
	
	/**
	 * 
	 * Returns a Tx_Extbase_Persistence_ObjectStorage with Person-Objects
	 * This function is required when migrating data from jsh_university
	 * (migrating other_lecturers or tutors for courses)
	 * @param array $fullName
	 */
	public function findAllByFullnameWithoutPid($fullNames) {
		if(empty($fullNames)) return array();
		
		$query = $this->createQuery();
		$constraints = array();
		foreach($fullNames as $fullName) {
    		$constraints[] = $query->logicalAND(
				array(
					$query->equals('lastname',$fullName['lastname']),
    				$query->equals('firstname',$fullName['firstname']),
    				// $query->equals('actitle',$fullName['actitle']), - may differ
    				$query->equals('hidden',0) // wouldn't make sense to map
    			)
    		);
		}
		
		$query->matching( $query->logicalOr($constraints) );
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		$tutors = $query->execute();
		if( count($tutors) > count($fullNames)) {
			$be = t3lib_div::makeInstance('Tx_Extbase_Persistence_Storage_Typo3DbBackend');
    		$query = $tutors->getQuery();
			$parameters = array();
    		$statementParts = $be->parseQuery($query, $parameters);
			$sql = $be->buildQuery($statementParts, $parameters);
			$be->replacePlaceholders($sql, $parameters);	
			echo chr(10)."Query: ".$sql.chr(10);exit();
		}
		return $tutors->toArray();
	}
}
?>