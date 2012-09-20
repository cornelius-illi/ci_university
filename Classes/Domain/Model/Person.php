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

class Tx_CiUniversity_Domain_Model_Person extends Tx_Extbase_DomainObject_AbstractEntity {
	/** 
	 * @var string 
	**/
	protected $lastname = '';
	
	/** 
	 * @var string 
	**/
	protected $firstname = '';
	
	/** 
	 * @var string 
	**/
	protected $customline = '';
	
	/** 
	 * @var string 
	**/
	protected $actitle = '';
	
	/** 
	 * @var string 
	**/
	protected $phone = '';
	
	/** 
	 * @var string 
	**/
	protected $fax = '';
	
	/** 
	 * @var string 
	**/
	protected $room = '';
	
	/** 
	 * @var string 
	**/
	protected $email = '';
	
	/** 
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CiUniversity_Domain_Model_Chair>
	**/
	protected $chairs;
	
	/** 
	 * @var string 
	**/
	protected $sections = '';
	
	
	/** 
	 * @var string 
	**/
	protected $customtext = '';
	
	/** 
	 * @var string 
	**/
	protected $image = '';
	
	/** 
	 * @var string 
	**/
	protected $url = '';
	
	public function __construct($lastname='') {
		$this->setLastname($lastname);
		$this->setChairs(new Tx_Extbase_Persistence_ObjectStorage);
	}
	
	/**
	* returns the firstname of a person
	* @return string
	*/
	public function getFirstname()
	{
	    return $this->title;
	}
	
	/**
	* sets firstname of a person
	* @param string $firstname
	* @return void
	*/
	public function setFirstname($firstname)
	{
	    $this->firstname = $firstname;
	}
	
	/**
	* returns the lastname of a person
	* @return string
	*/
	public function getLastname()
	{
	    return $this->title;
	}
	
	/**
	* sets lastname of a person
	* @param string $lastname
	* @return void
	*/
	public function setLastname($lastname)
	{
	    $this->lastname = $lastname;
	}
	
	public function getFullname() {
		return $this->actitle." ".$this->firstname." ".$this->lastname;
	}
	
	/**
	* returns the customline of a person
	* @return string
	*/
	public function getCustomline()
	{
	    return $this->customline;
	}
	
	/**
	* sets the customline of a person
	* @param string $customline
	* @return void
	*/
	public function setCustomline($customline)
	{
	    $this->customline = $customline;
	}
	
	/**
	* returns the academic title of a person
	* @return string
	*/
	public function getActitle()
	{
	    return $this->actitle;
	}
	
	/**
	* sets the academic title of a person
	* @param string $actitle
	* @return void
	*/
	public function setActitle($actitle)
	{
	    $this->actitle = $actitle;
	}
	
	/**
	* returns the phone number of a person
	* @return string
	*/
	public function getPhone()
	{
	    return $this->phone;
	}
	
	/**
	* sets the phone number of a person
	* @param string $phone
	* @return void
	*/
	public function setPhone($phone)
	{
	    $this->phone = $phone;
	}
	
	/**
	* returns the fax number of a person
	* @return string
	*/
	public function getFax()
	{
	    return $this->fax;
	}
	
	/**
	* sets the fax number of a person
	* @param string $fax
	* @return void
	*/
	public function setFax($fax)
	{
	    $this->fax = $fax;
	}
	
	/**
	* returns the room number of a person
	* @return string
	*/
	public function getRoom()
	{
	    return $this->room;
	}
	
	/**
	* sets the room number of a person
	* @param string $room
	* @return void
	*/
	public function setRoom($room)
	{
	    $this->room = $room;
	}
	
	/**
	* returns the email of a person
	* @return string
	*/
	public function getEmail()
	{
	    return $this->email;
	}
	
	/**
	* sets the email of a person
	* @param string $email
	* @return void
	*/
	public function setEmail($email)
	{
	    $this->email = $email;
	}
	
	/**
	* returns the sections of a person
	* @return string
	*/
	public function getSections()
	{
	   return $this->sections;
	}
	
	/**
	* sets the sections of a person
	* @param string $sections
	* @return void
	*/
	public function setSections($sections)
	{
		$this->sections = $sections;
	}
	
	public function getFirstChairName() {
		return $this->chairs->current()->getTitle();
	}
	
	public function getFirstChairUrl() {
		return $this->chairs->current()->getUrl();
	}
	
	/**
	* returns the chairs (or null) of a person as list
	* @return string
	*/
	public function getChairsList()
	{
	    $ret = '';
	    foreach($this->chairs as $chair) {
	    	$ret .= $chair->getTitle().', ';
	    }
	    return substr($ret, 0, (strlen($ret) - 2));
	}
	
	/**
	* returns the chair (or null) of a person
	* @return Tx_Extbase_Persistence_ObjectStorage
	*/
	public function getChairs()
	{
	    return $this->chairs;
	}
	
	/**
	* sets the chair of a person
	* @param Tx_Extbase_Persistence_ObjectStorage $chairs
	* @return void
	*/
	public function setChairs(Tx_Extbase_Persistence_ObjectStorage $chairs)
	{
	    $this->chairs = $chairs;
	}
	
	/**
	 * Adds a tutor to the course
	 *
	 * @param Tx_CiUniversity_Domain_Model_Chair The segment to be added
	 * @return void
	 */
	public function addChair(Tx_CiUniversity_Domain_Model_Chair $chair) {
		$this->chairs->attach($chair);
	}
	
	private function cleanHTML($attr) {
		// removes all empty-tags
		// see: http://stackoverflow.com/questions/3809108/how-to-remove-empty-paragraph-tags-from-string
		$pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";
		return trim(preg_replace($pattern, '', $attr));
	}
	
	/**
	* returns the customtext of a person
	* @return string
	*/
	public function getCustomtext()
	{
	    return $this->cleanHTML($this->customtext);
	}
	
	/**
	* sets the customtext of a person
	* @param string $customtext
	* @return void
	*/
	public function setCustomtext($customtext)
	{
	    $this->customtext = $this->cleanHTML($customtext);
	}
	
	/**
	* returns the foto of a person
	* @return image
	*/
	public function getImage()
	{
	    return $this->image;
	}
	
	/**
	* sets the foto of a person
	* @param string $chair
	* @return void
	*/
	public function setImage($image)
	{
	    $this->image = $image;
	}
	
	/**
	* returns the url to the private website of a person
	* @return string
	*/
	public function getUrl()
	{
	    return $this->url;
	}
	
	/**
	* sets the url to the private website of a person
	* @param string $chair
	* @return void
	*/
	public function setUrl($url)
	{
	    $this->url = $url;
	}
		
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}
}
?>