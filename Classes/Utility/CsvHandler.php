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

define('LENGHT', 4096);

class Tx_CiUniversity_Utility_CsvHandler {
	
	protected $fp;
	protected $headers;
	protected $headerDef;
	protected $delimiter;
	protected $enclosure;
	protected $error_msg;
	protected $lineNumber = 0;
	
	public function __construct() {
		$this->headers = array();
		$this->headerDef = 'UnitID,UnitBezeichnung,Modul,Semester,Credits,SWS,Studiengang,Dozent,Lehrform,Benotet,Enschreibefrist,Belegungsart';
	}
	
	public function loadFile($filename, $delimiter=';', $enclosure='', $noHeadersFlag=false) {
		if(isset($this->fp)) {
			$this->setErrorMsg("File already loaded! Use close() before loading new file!");
			return false;	
		}
		if(empty($filename)) {
			$this->setErrorMsg("Filename not specified! Second parameter needs to be full-path to csv-file!");
			return false;
		} elseif(!t3lib_div::validPathStr($filename)) {
    		$this->setErrorMsg($filename." is an invalid path!");
    		return false;
    	} elseif (!file_exists($filename)) {
    		$this->setErrorMsg($filename." cannot be found!");
    		return false;
    	} else {
    		$this->delimiter = $delimiter;
    		$this->enclosure = $enclosure;
    		$this->fp = fopen($filename,"r"); 
    		$this->createHeaders( $this->__getcsv(), $noHeadersFlag );
    		return true;
    	}
	}
	
	public function close() {
		$res = fclose($this->fp);
		$this->fp = NULL;
		$this->headers = array();
		return $res;
	}
	
	public function read() {
		if (!$this->fp) { return false; }
		if(empty($this->headers)) { 
			if(!$this->createHeaders( $this->__getcsv() ) ) {
				$this->setErrorMsg("Header definitions or number of columns differ from the once defined!".LF);
				return false;
			}
		}
		
		if($line = $this->__getcsv()) {
			$ret = array();			
			for($i=0;$i<count($line);++$i) {
				$ret[ $this->headers[$i] ] = addslashes($line[$i]);
			}
			return $ret;
		} else {
			return false;
		}
	}
	
	public function getLineNumber() {
		return $this->lineNumber;
	}
	
	private function __getcsv() {
		/* Note:  If PHP is not properly recognizing the line endings when reading files 
		 * either on or created by a Macintosh computer, enabling the auto_detect_line_endings
		 * run-time configuration option may help resolve the problem. 
		 */
		if(empty($this->enclosure)) {
			$result = fgetcsv($this->fp, LENGHT, $this->delimiter);
		} else {
			$result = fgetcsv($this->fp, LENGHT, $this->delimiter, $this->enclosure);
		}
		$this->lineNumber++;
		return $result;
	}
	
	private function createHeaders($ar, $noHeadersFlag=false) {
		if( ((implode(',', $ar) !== $this->headerDef) && !$noHeadersFlag) || 
			($noHeadersFlag && count($ar) !== count( explode(',', $this->headerDef)) ) ) {
			return false;
		}
		
		if(!$noHeadersFlag) {
			$this->headers = $ar;	
		} else {
			$this->headers = explode(',', $this->headerDef);
		}
		return true;
	}
	
	private function setErrorMsg($msg) {
		$this->error_msg = $msg;
	}
	
	public function getErrorMsg() {
		return $this->error_msg;
	}
	
	public function isError() {
		return !empty($this->error_msg);
	}
}