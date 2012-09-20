<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Cornelius Illi <cornelius.illi@student.hpi.uni-potsdam.de>
*  All rights reserved
*
*  This class is a backport of the corresponding class of FLOW3. 
*  All credits go to the v5 team.
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

$_EXTKEY = 'ci_university';
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/PersonRepository.php');

/**
 * Validator for Unique fullname-combinations (actitle + firstname + lastname)
 *
 * @package ci_university
 * @scope prototype
 */
class Tx_CiUniversity_Domain_Validator_PersonValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	protected $personRepo;
	
	public function __construct() {
		$this->personRepo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_PersonRepository');
	}
	
	/**
	 * Returns TRUE, if the given person has a unique name-combination
	 *
	 * If at least one error occurred, the result is FALSE.
	 *
	 * @param mixed $value The value that should be validated
	 * @return boolean TRUE if the value is an amount, otherwise FALSE
	 */
	public function isValid($person) {
		$this->errors = array();
		if (!$person instanceof Tx_CiUniversity_Domain_Model_Person) {
			$this->addError('The given Object is not a Person.', time() );
			return FALSE;
		}
		
		if( $page = $this->personRepo->personExists($person) ) {
			$this->addError('A Person with the name '.$person->getFullname().' already exists on the page: "'.
				$page['title'] , '"(UID: '.$page['uid'].').' . time() );
			return FALSE;
		}
		
		return TRUE;
	}
}

?>