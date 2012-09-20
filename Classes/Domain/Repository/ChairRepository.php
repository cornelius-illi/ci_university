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
 * A repository for Chairs
 */
class Tx_CiUniversity_Domain_Repository_ChairRepository extends Tx_Extbase_Persistence_Repository {
	
	public function findAllChairsToArray() {
		$query = $this->createQuery();
		$sql = 'SELECT title FROM tx_ciuniversity_domain_model_chair ORDER BY title';
		$query->statement($sql, array());
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		$res = $query->execute();
		
		$ret = array();
		foreach($res as $el) {
			$ret[] = array( 0 => $el['title'], 1 => $el['title'], 2 => '');
		}
		return $ret;
	}
	
	/* methods below are just needed for importing/ syncing */
	
	/**
	 * Returns all objects of this repository
	 * @return array An array of objects, empty if no objects found
	 */
	public function findAllWithoutPid() {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$result = $query->execute();
		return $result;
	}
	
	public function findOneByTitleWithoutPid($title) {
		$query = $this->createQuery();
		$query->matching($query->equals('title', $title))->setLimit(1);
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$result = $query->execute()->getFirst();
		return $result;
	}
}
?>