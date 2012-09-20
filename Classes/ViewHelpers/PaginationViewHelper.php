<?php

class Tx_CiUniversity_ViewHelpers_PaginationViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	
	/**
	 * This is the pager-viewhelper
	 *
	 * @param int $page Current page
	 * @param int $pageCount Last page
	 * @param int $count Last page
	 * @param int $pagesOnPager Last page
	 * @param int $itemsOnPage Last page
	 * @param array $pivars Last page
	 */
	public function render($page = 1, $pageCount = 0, $count = 0, $pagesOnPager = 5, $itemsOnPage=20, $pivars=array()) {
		$result = "";
		
		if($pageCount == 0) return $result;
		
		/* pager */ 
		$pp['first'] = '<li>'.$this->createURI("&laquo;",$pivars, "results",1).'</li>';
		$pp['back'] = ($page == 1) ? '' : '<li>'.$this->createURI("&lsaquo;",$pivars, "results",$page-1).'</li>';		
		$pp['forward'] = '<li>'.$this->createURI("&rsaquo;",$pivars, "results",$page+1).'</li>';
		$pp['last'] = '<li>'.$this->createURI("&raquo;",$pivars, "results",$pageCount).'</li>';
		if ($pageCount <= $pagesOnPager) $pp['last'] = '';
		if ($page==0) $pp['back'] = '';	
		if ($page==$pageCount) $pp['forward'] = '';	
		
		if ($page <= ceil($pagesOnPager/2) ) {
			$loopStart = 1;
			$pp['first'] = '';	
		} else {
			if ( ($page + floor($pagesOnPager/2)) > $pageCount) {
				$loopStart = $pageCount-$pagesOnPager;
				$pp['last'] = '';	
			}
			else $loopStart = $page-(floor($pagesOnPager/2));			
		}
		if($loopStart === 0) $loopStart = 1;
		
		$loopEnd = ( ($loopStart+$pagesOnPager) >= $pageCount ) ? $pageCount : $loopStart+$pagesOnPager-1;				
		
		for ($i=$loopStart;$i<=($loopEnd);$i++) {		
			$pipe = ($loopStart==$i) ? '' : '<span class="pipe">|</span>';
			$active = ($page == $i || ($page == 1 && $i==$loopStart) ) ? ' class="active"' : '';			
			$link = (empty($active)) ? $link = $this->createURI($i,$pivars, "results",$i) : $i;		
			$pages .= '<li'.$active.'>'.$pipe.$link.'</li>'; 
			
		}
				
		$pages = '<ul>'.$pp['first'].$pp['back'].$pages.$pp['forward'].$pp['last'].'</ul>';						
		
		if ($pageCount>1) $ret['pager'] = '<div class="right">'.$pages.'</div>';						
		
		/* pageOn */
		
		$resultStart = (intval($page)*$itemsOnPage) - ($itemsOnPage-1);		
		$resultEnd = $resultStart+$itemsOnPage-1;		
		if ($count < $resultEnd) $resultEnd = $count;
		
		$resExpress = 'Ergebnisse '.$resultStart.'-'.$resultEnd;
		if ($resultEnd == $resultStart)  $resExpress = 'Ergebnis '.$resultStart;		
		
		$ret['pageOn'] = '<div class="left"><div class="txt">Seite '.$page.' von '.$pageCount.' | '.$resExpress.' von '.$count.'</div></div>';
		
		$result = $ret['pageOn'].$ret['pager'];
		
		return $result;
	}
	
	/*
	* @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	*/
	private function createURI($link=1,$arguments = array(),$section="",$page=false) {
			if($page !== false) $arguments["page"] = $page;
			$uriBuilder = $this->controllerContext->getUriBuilder();
			$uri = $uriBuilder
			->reset()
			->setNoCache(true)
			->setUseCacheHash(false)
			->setSection($section)
			->uriFor($action, $arguments, $controller, $extensionName, $pluginName);
		return '<a href="'.$uri.'">'.$link.'</a>';
	}
}
?>