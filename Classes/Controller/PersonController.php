<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Cornelius Illi <cornelius.illi@student.hpi.uni-potsdam.de>
 *  All rights reserved
 *
 ***************************************************************/

class Tx_CiUniversity_Controller_PersonController extends Tx_Extbase_MVC_Controller_ActionController {
	
	protected $personRepository;
	protected $error = false;
	
	protected function initializeAction() {
		$this->courseRepository = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_CourseRepository');
		if( (strlen($this->settings['courseSinglePid']) < 1) && empty($this->settings['courseSingle']) ) {
			$this->flashMessages->add('SinglePID for Courses not set!','Configuration Error',t3lib_FlashMessage::ERROR);
			$this->error = true;
		} 
		if( (strlen($this->settings['personSinglePid']) < 1) && empty($this->settings['personSingle'])) {
			$this->error = true;
			$this->flashMessages->add('SinglePID for Persons not set!','Configuration Error',t3lib_FlashMessage::ERROR);
		}
		
		// single-pages set via plugin-element overrule ts-config
		if(!empty($this->settings['courseSingle'])) 
			$this->settings['courseSinglePid'] = $this->settings['courseSingle'];
		if(!empty($this->settings['personSingle'])) 
			$this->settings['personSinglePid'] = $this->settings['personSingle'];
		
	}	
	
	/**
	 * Renders a list of persons (record storage page + recursive need to be set)
	 * @return string The rendered HTML string
	 */
	public function indexAction() { 
		if($this->error) {
			$this->view->assign('error',true);
			return $this->view->render();
		}
		
		$piVars = array();
		$this->getArgumentFor('sword', $piVars);
		$this->getArgumentFor('mode', $piVars);
		$this->getArgumentFor('page', $piVars);
		if($piVars['mode'] === 'All') unset($piVars['mode']);
		// sword has priority
		if(isset($piVars['sword']) && isset($piVars['mode'])) {
			unset($piVars['mode']);
		}
		
		$searchTerms = (strlen($piVars['sword']) > 0) ? t3lib_div::trimExplode(',', $piVars['sword']) : array();
		$perPage = (strlen($this->settings['itemsPerPage']) > 0) ? intval($this->settings['itemsPerPage']) : 20;
		$preSearchModes = (strlen($this->settings['searchModeSelector']) > 0) ? t3lib_div::trimExplode(',', $this->settings['searchModeSelector']) : array();
		
		$persons = $this->personRepository->findAllPaginate($searchTerms, $piVars['mode'],$preSearchModes, $piVars['page'], $perPage);
		$this->view->assign('persons', $persons);
		$this->view->assign('cSPid', $this->settings['courseSinglePid']);
		$this->view->assign('pSPid', $this->settings['personSinglePid']);
		$preSearchModes = array_merge( array( 0 => 'All'), $preSearchModes);
		$this->view->assign('smodes', $preSearchModes);
		$this->view->assign('vars', $piVars);
		$this->view->assign('items', $perPage);
	}
	
	/**
	 * Gets a the piVar in a correct way
	 *
	 * @return mixed
	 */
	private function getArgumentFor($name, &$piVars) {
		if($this->request->hasArgument($name) && strlen(trim($this->request->getArgument($name))) > 0)
			$piVars[$name] = trim($this->request->getArgument($name));
	}
	
	/**
	 * Renders a single person
	 *
	 * @param Tx_CiUniversity_Domain_Model_Person $person The person to be displayed
	 * @dontvalidate $person
	 * @return string The rendered HTML string
	 */
	public function showAction(Tx_CiUniversity_Domain_Model_Person $person) {
		if($this->error) {
			$this->view->assign('error',true);
			return $this->view->render();
		}
		$this->view->assign('person', $person);
		$this->view->assign('cSPid', $this->settings['courseSinglePid']);
		$this->view->assign('pSPid', $this->settings['personSinglePid']);
	}
}

?>