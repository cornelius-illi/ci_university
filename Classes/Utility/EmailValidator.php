<?php
class tx_ciuniversity_mail_eval {
	
	/**
	* This function just return the field value as it is. No transforming,
	* hashing will be done on server-side.
	*
	* @return      JavaScript code for evaluating the
	*/
	function returnFieldJS() {
		return 'return value;';
	}
	
	function evaluateFieldValue($value, $is_in, &$set) {
    	if((is_string($value) && preg_match('
			/
				^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*
				@
				(?:
					(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2}|aero|asia|biz|cat|com|edu|coop|gov|info|int|invalid|jobs|localdomain|mil|mobi|museum|name|net|org|pro|tel|travel)|
					localhost|
					(?:(?:\d{1,2}|1\d{1,2}|2[0-5][0-5])\.){3}(?:(?:\d{1,2}|1\d{1,2}|2[0-5][0-5]))
				)
				\b
			/ix', $value)) || empty($value) ) {
    		$set = TRUE;
		} else {
			$set = FALSE;
			$message = t3lib_div::makeInstance('t3lib_FlashMessage',
				'The chair-mail-address is incorrect!',
   				 '',
    			t3lib_FlashMessage::ERROR
    		);
    		t3lib_FlashMessageQueue::addMessage($message);
		}
    	return $value;
    }
}
?>