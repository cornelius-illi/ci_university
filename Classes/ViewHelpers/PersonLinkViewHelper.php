<?php 
	class Tx_CiUniversity_ViewHelpers_PersonLinkViewHelper extends Tx_Fluid_Core_ViewHelper_TagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 * @author Cornelius Illi
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
		$this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
		$this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
	}
	
	/**
	 * This is the pilink-viewhelper
	 *
	 * @param string $uri Person->getUrl()
	 * @param string $pageUid Person SinglePid
	 * @param Tx_CiUniversity_Domain_Model_Person $person Person
	 * @return string Rendered link
	 */
	public function render($uri, $pageUid, $person) {
		if(!$person instanceof Tx_CiUniversity_Domain_Model_Person) {
			return $this->renderChildren();
		}
		
		$uriBuilder = $this->controllerContext->getUriBuilder();
		
		if(!empty($uri) && intval($uri)) { // pid set
			$uri = $uriBuilder
				->reset()
				->setTargetPageUid(intval($uri))
				->build();			
		} elseif (!empty($uri)) {
			$scheme = parse_url($uri, PHP_URL_SCHEME);
			if ($scheme === NULL && $defaultScheme !== '') {
				$uri = $defaultScheme . '://' . $uri;
			}
		} else {
			$args = array('tx_ciuniversity_ciuniversity' => array('person' => $person->getUid() ) );
			$uri = $uriBuilder
				->reset()
				->setTargetPageUid($pageUid)
				->setArguments($args)
				->build();
		}
		
		$this->tag->addAttribute('href', $uri);
		$this->tag->setContent($this->renderChildren());

		return $this->tag->render();
	}
	
}
?>