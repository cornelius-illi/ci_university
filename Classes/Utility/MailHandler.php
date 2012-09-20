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

class Tx_CiUniversity_Utility_MailHandler {
	// for plain-mails: t3lib_div::plainMailEncoded($email,$subject,$message);
	// for html-mails: t3lib/class.t3lib_htmlmail.php
		
	public function sendMail($email, $subject, $message) {
		$subject = '[TYPO3][BE-COURSE-SYNC]: '.$subject;
		$message = t3lib_div::breakTextForEmail($message);	
		$headers = 'From: webmaster@hpi.uni-potsdam.de' . "
" .
'Reply-To: webmaster@hpi.uni-potsdam.de' . "
" .
'Return-Path: webmaster@hpi.uni-potsdam.de' . "
".
'X-Mailer: PHP/';
		
		t3lib_div::plainMailEncoded($email,$subject,$message,$headers);
	} 
}