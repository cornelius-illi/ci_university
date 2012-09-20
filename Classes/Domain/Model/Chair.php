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

class Tx_CiUniversity_Domain_Model_Chair extends Tx_Extbase_DomainObject_AbstractEntity {
	/** 
	 * @var string The title of the chair
	**/
	protected $title = '';
	
	/**
	 * @var Tx_CiUniversity_Domain_Model_Person The head of the chair
	 */
	protected $head;
	
	/**
	 * @var string The URL of the chair
	 */
	protected $url;
	
	/**
	 * @var string The Email of the chair
	 */
	protected $email;
	
	
	public function __construct($title='') {
		$this->setTitle($title);
	} 
	
	/**
	* Returns the title of a chair
	* 
	* @return string
	*/
	public function getTitle()
	{
	    return $this->title;
	}
	
	/**
	* Sets the title of the chair
	* 
	* @param string $title
	* @return void
	*/
	public function setTitle($title)
	{
	    $this->title = $title;
	}
	
	/**
	* Returns the head of a chair
	* 
	* @return Tx_CiUniversity_Domain_Model_Person
	*/
	public function getHead()
	{
	    return $this->head;
	}
	
	/**
	* Sets the head of the chair
	* 
	* @param Tx_CiUniversity_Domain_Model_Person $head
	* @return void
	*/
	public function setHead(Tx_CiUniversity_Domain_Model_Person $head)
	{
	    $this->head = $head;
	}
	
	/**
	* Returns the URL of a chair
	* 
	* @return string
	*/
	public function getUrl()
	{
	    return $this->url;
	}
	
	/**
	* Sets the url of the chair
	* 
	* @param string $url
	* @return void
	*/
	public function setUrl($url)
	{
	    $this->url = $url;
	}
	
	/**
	* Returns the Email of a chair
	* 
	* @return string
	*/
	public function getEmail()
	{
	    return $this->email;
	}
	
	/**
	* Sets the email of the chair
	* 
	* @param string $url
	* @return void
	*/
	public function setEmail($email)
	{
	    $this->email = $email;
	}
	
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}
}
?>