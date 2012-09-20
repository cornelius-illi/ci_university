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
 *
 * The shell call is
 * /www/typo3/php cli_dispatch.phpsh EXTKEY TASK
 * 
 * @author	Cornelius Illi <Cornelius.Illi@student.hpi.uni-potsdam.de>
 * @package TYPO3
 */

if (!defined('TYPO3_cliMode')) {
	die('Access denied: CLI only.');
}

$_EXTKEY = 'ci_university';

require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/CourseRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Repository/ModuleRepository.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Course.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Domain/Model/Module.php');
require_once(t3lib_extMgm::extPath($_EXTKEY) .'Classes/Utility/CsvHandler.php');

class Tx_CiUniversity_Cli_Charset extends t3lib_cli {
	
	protected $persistanceManager;
    protected $course_repo;
    protected $module_repo;
    protected $person_repo;
    /* @var $csvHandler TxCiUniversity_Utility_CsvHandler */
    protected $csvHandler;
   
	function __construct() {
		$this->persistanceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
		$this->module_repo = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_ModuleRepository');
	}
    
    public function check($filename = "") {
    	$this->charsetConfig(); exit;
    	
    	if( !$this->csvHandler->loadFile($filename,';','',true) ) {
    		$this->cli_echo($this->csvHandler->getErrorMsg() . LF);exit();	
    	}
    	
    	while($row = $this->csvHandler->read() ) {
    		$proAr = explode(' ', $row['Dozent']);
    		$fullName = array(
    			'firstname' => $proAr[1],
    			'lastname' => $proAr[2],
    			'actitle' => $proAr[0]
    		);
    		$prof = $this->person_repo->findOneByFullnameUmlauts($fullName);
    		$this->cli_echo($row['Dozent']." / ".$prof->getFullname().LF);
    		exit();
    	}
    }
   
    protected function charsetConfig() {
    	$this->cli_echo("MySQL server version: ".mysql_get_server_info().LF);
		$this->cli_echo("MySQL client info: ".mysql_get_client_info().LF);

		$charset = mysql_client_encoding($GLOBALS['TYPO3_DB']->link);
		$this->cli_echo("The current client-character set is: ".$charset.chr(10));

		//mysql_set_charset('utf8',$db); 
		//$charset = mysql_client_encoding($db);
		//print "The current client-character set is: ".$charset.chr(10);

		$res = $GLOBALS['TYPO3_DB']->sql_query('SHOW VARIABLES LIKE "%character_set%";');
		while ($ar = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$this->cli_echo($ar["Variable_name"].": ".$ar["Value"].LF);
		} 
		
		//$charsets = $GLOBALS['TYPO3_DB']->admin_get_charsets();
		//foreach($charsets as $charset) {
		//	var_dump($charset); 
		//	$this->cli_echo(LF);
		//} 
		
		$modules = $this->module_repo->findAllWithoutPid();
		$this->cli_echo("Modules: ".count($modules).LF);
		foreach($modules as $module) {
			$this->cli_echo($module->getTitle().' ('.$module->getUid().')'.LF);
		}
    }
}