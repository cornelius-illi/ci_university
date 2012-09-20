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

class Tx_CiUniversity_Controller_CourseController extends Tx_Extbase_MVC_Controller_ActionController {
	protected static $_EXTKEY = 'ci_university';
	
	protected $courseRepository;
	protected $moduleRepository;
	protected $error = false;
	
	protected function initializeAction() {
		$this->courseRepository = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_CourseRepository');
		$this->moduleRepository = t3lib_div::makeInstance('Tx_CiUniversity_Domain_Repository_ModuleRepository');
		
		// don't do anything else if we have an ajax request
		if($this->request->hasArgument('ajax') && $this->request->getArgument('ajax') === 1) return;
		
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
		
		// extjs/ ajax
		if($this->settings['useExtJS']) {
			// Load ExtJS libraries and stylesheets
        	$GLOBALS['TSFE']->backPath = TYPO3_mainDir;
        	$GLOBALS['TSFE']->getPageRenderer()->loadExtJS();
       		$GLOBALS['TSFE']->getPageRenderer()->enableExtJSQuickTips();
		}
	}	
	
	public function indexAction() { 
		if($this->error && !$this->request->hasArgument('ajax')) {
			$this->view->assign('error',true);
			return $this->view->render();
		} elseif($this->error && $this->request->hasArgument('ajax')) {
			echo '';
			exit();
		}
		
		$module = '';
		if($this->request->hasArgument('module')) {
			$module = $this->request->getArgument('module');
		}
		
		$program = '';
		if($this->request->hasArgument('program')) {
			$program = $this->request->getArgument('program');
		}
		
		$chair = ($this->settings['chair'] === 'all') ? '' : $this->settings['chair'];
		$semester = ($this->settings['semester'] === 'all') ? '' : $this->settings['semester'];
		if($this->request->hasArgument('ajax') && $this->request->getArgument('ajax') === 1) {
			$semester = $this->request->getArgument('semester');
		}
		$courses = $this->courseRepository->findDemanded($chair, $semester, $module, $program);
		
		if($this->request->hasArgument('ajax') && $this->request->getArgument('ajax') === 1) {	
			$resArray = array();
			foreach($courses as $programs) {
				foreach($programs as $course) {
					$resArray[] = array(
						'uid' => $course->getUid(),
						'title' => $course->getTitle(),
						'credits' => $course->getCredits(),
						'sws' => $course->getSws(),
						'lecturer' => $course->getAllLecturerString(),
						'url' => $GLOBALS['TSFE']->baseUrl.$this->uriBuilder
							->setTargetPageUid($this->settings['courseSinglePid'])
							->setArguments( array( 'tx_ciuniversity_ciuniversity' => 
								array('course' => $course->getUid(),
								'year' => $course->getYear()
							)) )
							->build(),
					);
				}
			}
			
			$result = json_encode(array( 'results' => $resArray ));
			
			return $result;
		}
			
		$this->view->assign('courses_by_program', $courses);
		$this->view->assign('cSPid', $this->settings['courseSinglePid']);
		$this->view->assign('pSPid', $this->settings['personSinglePid']);
		// when parameters have been set
		if(!empty($module)) {
			$this->view->assign('param', $this->moduleRepository->findByUid($module)->getTitle());
		}
		
		// when extjs/ajax is set active
		if($this->settings['useExtJS']) {
			$jsCode = "
				var w;
				var doAjax = function(a,rUid) {
					Ext.Ajax.request({
    					url: '".$GLOBALS['TSFE']->baseUrl."index.php',
    					method: 'GET',
    					params: {
        					eID: 'ciuniversityAjaxDispatcher',
        					request: Ext.encode({
            					extensionName:'CiUniversity',
            					pluginName:'CiUniversity',
            					controllerName:'Course',
            					actionName:'show',
            					arguments: {
                					course: rUid
                				}
        					})
    					},
    					success:function(response, request) {
       						var d = Ext.get('tx-ciuniversity-singlebox');
							d.dom.innerHTML = response.responseText; 
							showWindow(a);
    					},
    					failure:function(response, request) {
    						window.document.write(response.responseText);
    					}
					});
				}
				
				var showWindow = function(aTitle) {
						w = new Ext.Window({
				    	applyTo:'tx-ciuniversity-singlebox',
				        layout:'fit',
				        width:650,
				        height:350,
				        closeAction:'hide',
				        plain: true,
				        modal: true,
				        title: aTitle,
				        resizable: false,
				        draggable: false,
				        
						items: new Ext.TabPanel({
			            	applyTo: 'tx-ciuniversity-single-tabs',
			                autoTabs:true,
			                activeTab:0,
			                deferredRender:false,
			                border:false
			            }),
			            buttons: [{
                    		text: 'Close',
                    		handler: function(){
                        		w.hide();
                    		}
                		}]
					});
					w.render()
    				w.center();
					w.show();	
				}
				
				var singleCourseLinkClicked = function(e) {
			   		var a = Ext.get(e.target);
			        doAjax(a.dom.innerHTML,a.dom.nextSibling.innerHTML);
			   }
			   
			   var myMask = new Ext.LoadMask(Ext.getBody(), {msg:'Please wait...'});		
			   Ext.Ajax.on('beforerequest', myMask.show, myMask);
			   Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
			   Ext.Ajax.on('requestexception', myMask.hide, myMask);
			   
			   	var bd = Ext.getBody();
			    bd.createChild({tag: 'div', html: '', id: 'tx-ciuniversity-singlebox' });  
    			Ext.select('#tx-ciuniversity-courselist-table a.courselink').on('click', singleCourseLinkClicked, this, {stopEvent:true});
			";
			
			foreach($courses as $program) {
				foreach($program as $course) {
					$hasModules = $course->getModulesString();
					if(!empty($hasModules)) {
						$jsCode .= "new Ext.ToolTip({        
					        target: 'tx-ciuniversity-modules-tooltip-target-".$course->getUid()."',
					        anchor: 'top',
					        html: null,
					        showDelay : 50,
					        anchorOffset : 20,
					        pageX: 20,
					        mouseOffset: [20,2],
					        baseCls : 'tx-ciuniversity-anchor',
					        contentEl: 'tx-ciuniversity-modules-tooltip-".$course->getUid()."'
					  	});";
					}
				}
			}
			
			$GLOBALS['TSFE']->getPageRenderer()->addExtOnReadyCode($jsCode);
			
		}
	}
	
	public function modulesAction() {
		$semester = ($this->settings['semester'] === 'all') ? '' : $this->settings['semester'];
		$data = $this->moduleRepository->findAllForSemesterToArray($semester);
		
		// when extjs/ajax is set active
		if($this->settings['useExtJS']) {
			$jsCode = "var w;
				var createStore = function(params) {
						var ar = params.split(',');
						var req = Ext.encode({
            				extensionName:'CiUniversity',
            				pluginName:'CiUniversity',
            				controllerName:'Course',
            				actionName:'index',
            				arguments: {
                					module: parseInt(ar[0]),
                					program: ar[1],
                					semester: '".$semester."',
                			}
        				});
						var params = '?eID=ciuniversityAjaxDispatcher&request=' + req
						return new Ext.data.JsonStore({
						autoDestroy: true,
						url: '".$GLOBALS['TSFE']->baseUrl."index.php' + params,
						storeId: 'myStore',
						root: 'results',
						idProperty: 'uid',
						fields: [
								{name: 'uid', type: 'int'},
								'title', 
								{name:'credits', type: 'int'}, 
								{name:'sws', type: 'int'}, 
								'lecturer',
								'url',
						]
					});
				}
				
				var handleGridRowClick = function (g, rowIndex, e){
					var record = g.getStore().getAt(rowIndex);//OK, we have our record
					window.location = record.data.url;
				}
					
				var moduleLinkClicked = function(e) {
				 	var a = Ext.get(e.target);	 	
				 	var CourseStore = createStore(a.dom.nextSibling.innerHTML);
				 	
					if(!CourseStore) {
						Ext.MessageBox.alert('JSONStore failure:', 'Using URI: ' + a.dom.href);
					} else {
						var CourseColumnModel = new Ext.grid.ColumnModel(
							[{
						  		header: 'UID',
							    readOnly: true,
							    dataIndex: 'uid',
							    hidden: true,
							},{
						  		header: 'Title',
							    readOnly: true,
							    dataIndex: 'title',
							    width: 300,
							},{
						    	header: 'ECTS',
						    	dataIndex: 'credits',
						    	width: 60,
						    	readOnly: true,
						  	},{
							 	header: 'SWS',
								dataIndex: 'credits',
							    width: 40,
							    readOnly: true,
							},{
								 header: 'Dozenten',
								 dataIndex: 'lecturer',
								 width: 280,
								 readOnly: true,
							},{
								 header: 'URL',
								 dataIndex: 'url',
								 readOnly: true,
								 hidden: true,
							}]
						);
						CourseColumnModel.defaultSortable= true;
					
						var CourseListingGrid =  new Ext.grid.GridPanel({
				    		id: 'CourseListingGrid',
				    		store: CourseStore,
				    		cm: CourseColumnModel,
				    		enableColLock:false,
				    		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
				    		autoHeight: true,
				    		width: 680,
						});
						
						CourseListingGrid.addListener('rowclick', handleGridRowClick);
						
						var CourseListingWindow = new Ext.Window({
						    id: 'CourseListingWindow',
						    title: 'Courses for ' + a.dom.innerHTML,
						    closable:true,
						    //closeAction:'hide',
						    width: 700,
						    autoHeight:true,
						    plain:true,
						    layout: 'fit',
						    items: CourseListingGrid,
						  	plain: true,
				        	modal: true,
				        	resizable: false,
				        	draggable: false,  
				        	buttons: [{
                    			text: 'Close',
                    			handler: function(){
                        			CourseListingWindow.close();
                    			}
                			}]
						});

						CourseStore.load();
						CourseListingWindow.show();
					}
				}
				
				Ext.select('#tx-ciuniversity-modules-list a').on('click', moduleLinkClicked, this, {stopEvent:true});	
			";
		}
		
		$GLOBALS['TSFE']->getPageRenderer()->addExtOnReadyCode($jsCode);
		
		$this->view->assign('data', $data);
	}
	
	private function generatePersonLink(Tx_CiUniversity_Domain_Model_Person $person) {
		$pUrl = $person->getUrl();
		if(!empty($pUrl) && intval($pUrl)) { // pid set
			$uri = $this->uriBuilder
				->reset()
				->setTargetPageUid(intval($pUrl))
				->build();			
		} elseif (!empty($pUrl)) {
			$defaultScheme = 'http';
			$parsedURL = parse_url($pUrl, PHP_URL_SCHEME);
			if ($parsedURL['scheme'] === NULL) {
				$uri = $defaultScheme . '://' . $pUrl;
			} else {
				$uri = $pUrl;
			}
			
		} else {
			$args = array('tx_ciuniversity_ciuniversity' => array('person' => $person->getUid() ) );
			$uri = $this->uriBuilder
				->reset()
				->setTargetPageUid($this->settings["personSinglePid"])
				->setArguments($args)
				->build(); // 
		}
		return $uri;
	}
	
	private function getLL($key) {
		return Tx_Extbase_Utility_Localization::translate($key, Tx_CiUniversity_Controller_CourseController::$_EXTKEY);
	}
	
	/**
	 * Renders a single course
	 *
	 * @param Tx_CiUniversity_Domain_Model_Course $course The course to be displayed
	 * @return string The rendered HTML string
	 */
	public function showAction(Tx_CiUniversity_Domain_Model_Course $course) {
		if($this->request->hasArgument('ajax') && $this->request->getArgument('ajax') === 1) {	
			$result = '<div id="tx-ciuniversity-single-tabs"> 
		        	<div class="x-tab" title="'.$this->getLL('basicInfo').'"> 
		            	<p><b>'.$this->getLL('lecturer').': </b>
		            		<a href="'.$this->generatePersonLink($course->getLecturer()).'" >'.$course->getLecturer()->getFullname().'</a>
		            	';
			$oLs = $course->getOtherLecturers()->toArray();
			if( count($oLs) > 0) {
				$result .= ', ';
				foreach($oLs as $oL) {
					$result .= '<a href="'.$this->generatePersonLink($oL).'">'.$oL->getFullname().'</a>, ';
				}
				$result = substr($result,0,-2);
			}
			$result .=  '</p>
		            	<p><b>'.$this->getLL('sws').': </b> '.$course->getSws().'</b></p>
						<p><b>'.$this->getLL('ects').': </b> '.$course->getCredits().'</p>
						<p><b>'.$this->getLL('enrolmentDeadline').': </b> '.$course->getEnrolmentDeadline().'</p>
						<p><b>'.$this->getLL('program').': </b> '.$course->getProgram().'</p>
						<p><b>'.$this->getLL('teachingForm').': </b> '.$course->getTeachingForm().'</p>
						<p><b>'.$this->getLL('enrolmentType').': </b> '.$course->getEnrolmentType().'</p>
			';
		   
			if( count($course->getModules()) > 0) {
				$result .= '<p><b>'.$this->getLL('modules').': </b></p><ul>';
				foreach($course->getModules() as $module)
					$result .= '<li>'.$module->getTitle().' ('.$module->getModulegroup().')</li>';
				$result .= '</ul></p>';
			}
		    $result .= '</div>';
			if( strlen($course->getDescription()) ) {
				$result .= 	'<div class="x-tab" title="'.$this->getLL('description').'"> 
		            	<p>'.$course->getDescription().'</p>
		       		</div> ';
			}
			if( strlen($course->getRequirements()) ) {
				$result .= 	'<div class="x-tab" title="'.$this->getLL('requirements').'"> 
		            	<p>'.$course->getRequirements().'</p>
		       		</div> ';
			}
			if( strlen($course->getLiterature()) ) {
				$result .= '<div class="x-tab" title="'.$this->getLL('literature').'"> 
		            	<p>'.$course->getLiterature().'</p>
		       		</div> ';
			}
			if( strlen($course->getDescription()) ) {
				$result .= '<div class="x-tab" title="'.$this->getLL('examination').'"> 
		            	<p>'.$course->getExamination().'</p>
		       		</div> ';
			}
			if( strlen($course->getDates()) ) {
				$result .= 	'<div class="x-tab" title="'.$this->getLL('dates').'"> 
		            	<p>'.$course->getDates().'</p>
		       		</div> ';
			}       		
		       		
		    $result .= '</div>';
			return $result;
		}

		
		if($this->error) {
			$this->view->assign('error',true);
			return $this->view->render();
		}
		
		$condition = !($course->getOnlyUseCoursePageContents() && (strlen($course->getUrl()) > 0) );
		if(!$condition) {
			if($GLOBALS['TSFE']->config['config']['language'] === "de") {
				$this->view->assign('notice', $this->settings['referToCoursePageText']['de']);
			} else {
				$this->view->assign('notice', $this->settings['referToCoursePageText']['en']);
			}
		}
		
		$this->view->assign('showTexts', $condition);
		$this->view->assign('course', $course);
		$this->view->assign('cSPid', $this->settings['courseSinglePid']);
		$this->view->assign('pSPid', $this->settings['personSinglePid']);
	}
}

?>