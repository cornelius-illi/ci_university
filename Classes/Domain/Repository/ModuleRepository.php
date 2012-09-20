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
 * A repository for Modules
 */
class Tx_CiUniversity_Domain_Repository_ModuleRepository extends Tx_Extbase_Persistence_Repository {
	function findOrCreateByTitle($title, $pid=484) {
		$module = $this->findOneByTitleSafe($title);
		if(!$module) {
			$module = new Tx_CiUniversity_Domain_Model_Module;
			$module->setTitle($title);
			$module->setPid($pid);
			
			// add to repository/ persist
			$this->add($module);
		}
		return $module;
	}	
	
	function findOneByTitleSafe($title) {
		$query = $this->createQuery();
		$query->matching( $query->equals('title', $title) );
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		return $query->execute()->getFirst();
	}
	
	function findAllWithoutPid() {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		return $query->execute();
	}
	
	function findAllForSemesterToArray($semester) {
		$result = array();
		$programs = array('IT-Systems Engineering BA', 'IT-Systems Engineering MA');
		foreach($programs as $program) {
			$query = $this->createQuery();
			$sql = 'SELECT m.modulegroup, m.title, m.uid, c.program, COUNT(mm.uid_local) AS courses
				FROM hpitypo3.tx_ciuniversity_domain_model_module m
				JOIN hpitypo3.tx_ciuniversity_course_module_mm mm ON m.uid=mm.uid_foreign
				JOIN hpitypo3.tx_ciuniversity_domain_model_course c ON c.uid=mm.uid_local 
				WHERE m.modulegroup != "" AND m.deleted=0 AND m.hidden=0
				  AND c.semester = ? AND c.program = ?
				  AND c.hidden=0 AND c.deleted=0
				GROUP BY m.title
				ORDER BY m.modulegroup, m.title
			';
			$query->statement($sql, array($semester, $program) );
			$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		
			$result[$program] = $query->execute();
			
			$clean = array();
			foreach($result[$program] as $modulgroup) {
				if(array_key_exists($modulgroup['modulegroup'], $clean)) {
					$clean[$modulgroup['modulegroup']][] = array(
						"title" => $modulgroup['title'],
						"courses" => $modulgroup['courses'],
						"uid" => $modulgroup['uid'],
					);
				} else {
					$clean[$modulgroup['modulegroup']] = array();
					$clean[$modulgroup['modulegroup']][] = array(
						"title" => $modulgroup['title'],
						"courses" => $modulgroup['courses'],
						"uid" => $modulgroup['uid'],
					);
				}
			}
			$result[$program] = $clean;
		}
		
		return $result;
		
	}
}
