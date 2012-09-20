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

class Tx_CiUniversity_Domain_Model_Module extends Tx_Extbase_DomainObject_AbstractValueObject {
	/** 
	 * @var string 
	**/
	protected $title = '';
	
	
	/** 
	 * @var string 
	**/
	protected $identifier = '';
	
	/** 
	 * @var string 
	**/
	protected $modulegroup = '';
	
	public function __construct($title='') {
		$this->setTitle($title);
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
	* returns title 
	* @return string
	*/
	public function getIdentifier()
	{
	    return $this->identifier;
	}
	
	/**
	* sets title
	* @param string $title
	* @return void
	*/
	public function setIdentifier($identifier)
	{
	    $this->identifier = $identifier;
	}
	
/**
	* returns the modulegroup 
	* @return string
	*/
	public function getModulegroup()
	{
	    return $this->modulegroup;
	}
	
	/**
	* sets title
	* @param string $title
	* @return void
	*/
	public function setModulegroup($modulegroup)
	{
	    $this->modulegroup = $modulegroup;
	}
}
?>