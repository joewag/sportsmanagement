<?php 
/** SportsManagement ein Programm zur Verwaltung f�r alle Sportarten
* @version         1.0.05
* @file                agegroup.php
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
* OHNE JEDE GEW�HELEISTUNG, bereitgestellt; sogar ohne die implizite
* Gew�hrleistung der MARKTF�HIGKEIT oder EIGNUNG F�R EINEN BESTIMMTEN ZWECK.
* Siehe die GNU General Public License f�r weitere Details.
*
* Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
* Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
*
* Note : All ini files need to be saved as UTF-8 without BOM
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
require_once(JPATH_COMPONENT_SITE.DS.'models'.DS.'clubinfo.php' );

/**
 * sportsmanagementViewClubPlan
 * 
 * @package   
 * @author 
 * @copyright diddi
 * @version 2014
 * @access public
 */
class sportsmanagementViewClubPlan extends JView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$uri = JFactory::getURI();
		$model = $this->getModel();
		$project = sportsmanagementModelProject::getProject();
		$config = sportsmanagementModelProject::getTemplateConfig($this->getName());
		$this->assignRef('project',$project);
		$this->assignRef('overallconfig',sportsmanagementModelProject::getOverallConfig());
		$this->assignRef('config',$config);
		$this->assignRef('showclubconfig',$showclubconfig);
		$this->assignRef('favteams',sportsmanagementModelProject::getFavTeams());
		$this->assignRef('club',sportsmanagementModelClubInfo::getClub());
		switch ($config['type_matches']) 
        {
			case 0 : case 4 : // all matches
				$this->assignRef('allmatches',$model->getAllMatches($config['MatchesOrderBy'],$config['type_matches']));
				break;
			case 1 : // home matches
				$this->assignRef('homematches',$model->getHomeMatches($config['MatchesOrderBy'],$config['type_matches']));
				break;
			case 2 : // away matches
				$this->assignRef('awaymatches',$model->getAwayMatches($config['MatchesOrderBy'],$config['type_matches']));
				break;
			default: // home+away matches
				$this->assignRef('homematches',$model->getHomeMatches($config['MatchesOrderBy'],$config['type_matches']));
				$this->assignRef('awaymatches',$model->getAwayMatches($config['MatchesOrderBy'],$config['type_matches']));
				break;
		}
		$this->assignRef('startdate',$model->getStartDate());
		$this->assignRef('enddate',$model->getEndDate());
		$this->assignRef('teams',$model->getTeams());
		$this->assignRef('model',$model);
		$this->assign('action',$uri->toString());

		// Set page title
		$pageTitle=JText::_('COM_SPORTSMANAGEMENT_CLUBPLAN_TITLE');
		if (isset($this->club)){
			$pageTitle .= ': '.$this->club->name;
		}
		$document->setTitle($pageTitle);
		
		//$this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );

		//build feed links
		$project_id = (!empty($this->project->id)) ? '&p='.$this->project->id : '';
		$club_id = (!empty($this->club->id)) ? '&cid='.$this->club->id : '';
		$rssVar = (!empty($this->club->id)) ? $club_id : $project_id;

		//$feed='index.php?option=com_joomleague&view=clubplan&cid='.$this->club->id.'&format=feed';
		$feed='index.php?option=com_sportsmanagement&view=clubplan'.$rssVar.'&format=feed';
		$rss=array('type' => 'application/rss+xml','title' => JText::_('COM_SPORTSMANAGEMENT_CLUBPLAN_RSSFEED'));

		// add the links
		$document->addHeadLink(JRoute::_($feed.'&type=rss'),'alternate','rel',$rss);
		parent::display($tpl);
	}

}
?>