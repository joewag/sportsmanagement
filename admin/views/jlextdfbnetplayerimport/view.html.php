<?php
/** SportsManagement ein Programm zur Verwaltung f�r alle Sportarten
* @version         1.0.05
* @file                jlextdfbnetplayerimport.php
* @author                diddipoeler, stony, svdoldie und donclumsy (diddipoeler@arcor.de)
* @copyright        Copyright: � 2013 Fussball in Europa http://fussballineuropa.de/ All rights reserved.
* @license                This file is part of SportsManagement.
*
* SportsManagement is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* SportsManagement is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with SportsManagement.  If not, see <http://www.gnu.org/licenses/>.
*
* Diese Datei ist Teil von SportsManagement.
*
* SportsManagement ist Freie Software: Sie k�nnen es unter den Bedingungen
* der GNU General Public License, wie von der Free Software Foundation,
* Version 3 der Lizenz oder (nach Ihrer Wahl) jeder sp�teren
* ver�ffentlichten Version, weiterverbreiten und/oder modifizieren.
*
* SportsManagement wird in der Hoffnung, dass es n�tzlich sein wird, aber
* OHNE JEDE GEW�HRLEISTUNG, bereitgestellt; sogar ohne die implizite
* Gew�hrleistung der MARKTF�HIGKEIT oder EIGNUNG F�R EINEN BESTIMMTEN ZWECK.
* Siehe die GNU General Public License f�r weitere Details.
*
* Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
* Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
*
* Note : All ini files need to be saved as UTF-8 without BOM
*/

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );


/**
 * sportsmanagementViewjlextdfbnetplayerimport
 * 
 * @package   
 * @author 
 * @copyright diddi
 * @version 2013
 * @access public
 */
class sportsmanagementViewjlextdfbnetplayerimport extends sportsmanagementView 
{
	
    function init()
    //function display($tpl = null) 
    {
		//global $app;
		
		if ($this->getLayout () == 'default') {
			$this->_displayDefault ( $tpl );
			return;
		}
		
        /*
		if ($this->getLayout () == 'default_edit') {
			$this->_displayDefaultEdit ( $tpl );
			return;
		}
		
		if ($this->getLayout () == 'default_update') {
			$this->_displayDefaultUpdate ( $tpl );
			return;
		}
		
		if ($this->getLayout () == 'info') {
			$this->_displayInfo ( $tpl );
			return;
		}
		
		if ($this->getLayout () == 'selectpage') {
			$this->_displaySelectpage ( $tpl );
			return;
		}
		*/
        
		// Set toolbar items for the page
		//JToolBarHelper::title ( JText::_ ( 'COM_SPORTSMANAGEMENT_ADMIN_LMO_IMPORT_TITLE_1_3' ), 'generic.png' );
		//JToolBarHelper::help ( 'screen.joomleague', true );
		//$app = JFactory::getApplication();
//		$jinput = $app->input;
//		$option = $jinput->getCmd('option');
//		$uri = JFactory::getURI ();
		$config = JComponentHelper::getParams ( 'com_media' );
		$post = $this->jinput->post;
		$files = $this->jinput->get('files');
		
		//$this->request_url	= $uri->toString ();
		$this->config	= $config;
		
		$revisionDate = '2011-04-28 - 12:00';
		$this->revisionDate	= $revisionDate ;
		
		//parent::display ( $tpl );
	}
	
    /*
    private function _displayInfo($tpl) {
		$mtime = microtime ();
		$mtime = explode ( " ", $mtime );
		$mtime = $mtime [1] + $mtime [0];
		$starttime = $mtime;
		$app = JFactory::getApplication ();
		$db = JFactory::getDBO ();
		$post = JRequest::get ( 'post' );
		
		$model = $this->getModel ( 'jlextdfbnetplayerimport' );
		
		// Set toolbar items for the page
		// JToolBarHelper::title(JText::_('COM_SPORTSMANAGEMENT_ADMIN_DFBNET_IMPORT_TITLE_3_3'),'generic.png');
		// JToolBarHelper::back();
		// JToolBarHelper::help('screen.joomleague',true);
		
		$this->assignRef ( 'starttime', $starttime );
		$this->assignRef ( 'importData', $model->importData ( $post ) );
		$this->assignRef ( 'postData', $post );
		$revisionDate = '2011-04-28 - 12:00';
		$this->assignRef ( 'revisionDate', $revisionDate );
		
		
		parent::display ( $tpl );
	}
    */
    
	function _displayDefault($tpl) 
    {
		//global $option;
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		
		$db		= sportsmanagementHelper::getDBConnection();
		$uri = JFactory::getURI ();
		$user = JFactory::getUser ();
		
		// $model = $this->getModel('project') ;
		// $projectdata = $this->get('Data');
		// $this->assignRef( 'name', $projectdata->name);
		
		$model = $this->getModel ();
		$project = $app->getUserState ( $option . 'project' );
		$this->project	= $project;
		$config = JComponentHelper::getParams ( 'com_media' );
		
		$this->request_url	= $uri->toString ();
		$this->config	= $config;
		$revisionDate = '2011-04-28 - 12:00';
		$this->revisionDate	= $revisionDate;
		$import_version = 'NEW';
		$this->import_version	= $import_version;
		
		//$this->addToolbar ();
		parent::display ( $tpl );
	}
    
    
	function _displayDefaultUpdate($tpl) 
    {
		// global $app, $option;
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		
		$db		= sportsmanagementHelper::getDBConnection();
		$uri = JFactory::getURI ();
		$user = JFactory::getUser ();
		$model = $this->getModel ();
		//$option = 'com_joomleague';
		$project = $app->getUserState ( $option . 'project' );
		$this->project	= $project;
		$config = JComponentHelper::getParams ( 'com_media' );
		
		$uploadArray = $app->getUserState ( $option . 'uploadArray', array () );
		$lmoimportuseteams = $app->getUserState ( $option . 'lmoimportuseteams' );
		$whichfile = $app->getUserState ( $option . 'whichfile' );
		//$delimiter = $app->getUserState ( $option . 'delimiter' );
		
		$this->uploadArray	= $uploadArray;
		$this->importData	= $model->getUpdateData ();
		
		// $this->assignRef('xml',$model->getData());
		
		parent::display ( $tpl );
	}
    
    
    
    
    
	protected function addToolbar() 
    {
        // global $app, $option;
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		
		// Get a refrence of the page instance in joomla
		$document	= JFactory::getDocument();
        // Set toolbar items for the page
		$stylelink = '<link rel="stylesheet" href="'.JURI::root().'administrator/components/com_sportsmanagement/assets/css/jlextusericons.css'.'" type="text/css" />' ."\n";
		$document->addCustomTag($stylelink);
        
        // Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'COM_SPORTSMANAGEMENT_ADMIN_DFBNET_IMPORT' ),'dfbnet' );
        JToolBarHelper::back('JPREV','index.php?option=com_sportsmanagement&view=extensions');
        JToolBarHelper::divider();
		sportsmanagementHelper::ToolbarButtonOnlineHelp();
		JToolBarHelper::preferences($option);

	}
}

?>