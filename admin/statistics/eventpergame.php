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

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'statistics'.DS.'base.php');


/**
 * SMStatisticEventPergame
 * 
 * @package 
 * @author diddi
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class SMStatisticEventPergame extends SMStatistic 
{
//also the name of the associated xml file	
	var $_name = 'eventpergame';
	
	var $_calculated = 1;
	
	var $_showinsinglematchreports = 0;
	
    var $_ids = 'event_ids';
    //var $_ids = 'stat_ids';
    
	/**
	 * SMStatisticEventPergame::__construct()
	 * 
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * SMStatisticEventPergame::getPlayerStatsByProject()
	 * 
	 * @param mixed $person_id
	 * @param integer $projectteam_id
	 * @param integer $project_id
	 * @param integer $sports_type_id
	 * @return
	 */
	function getPlayerStatsByProject($person_id, $projectteam_id = 0, $project_id = 0, $sports_type_id = 0)
	{
	   $app = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        
		$sids = SMStatistic::getSids($this->_ids);
		$num = SMStatistic::getPlayerStatsByProjectForEvents($person_id, $projectteam_id, $project_id, $sports_type_id, $sids);
		$den = SMStatistic::getGamesPlayedByPlayer($person_id, $projectteam_id, $project_id, $sports_type_id);
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' sids<br><pre>'.print_r($sids,true).'</pre>'),'Notice');
}

		return self::formatValue($num, $den, SMStatistic::getPrecision());
	}
	

	
	/**
	 * SMStatisticEventPergame::getRosterStats()
	 * 
	 * @param mixed $team_id
	 * @param mixed $project_id
	 * @param mixed $position_id
	 * @return
	 */
	function getRosterStats($team_id, $project_id, $position_id)
	{
		$sids = SMStatistic::getSids($this->_ids);
		$num = $this->getRosterStatsForEvents($team_id, $project_id, $position_id, $sids);
		$den = $this->getGamesPlayedByProjectTeam($team_id, $project_id, $position_id);
		$precision = SMStatistic::getPrecision();
		
		$res = array();
		foreach (array_unique(array_merge(array_keys($num), array_keys($den))) as $person_id) 
		{
			$n = isset($num[$person_id]) ? $num[$person_id]->value : 0;
			$d = isset($den[$person_id]) ? $den[$person_id]->value : 0;
			$res[$person_id] = new stdclass();
			$res[$person_id]->person_id = $person_id;
			$res[$person_id]->value = self::formatValue($n, $d, $precision);
		}
		return $res;
	}

	/**
	 * SMStatisticEventPergame::getPlayersRanking()
	 * 
	 * @param mixed $project_id
	 * @param mixed $division_id
	 * @param mixed $team_id
	 * @param integer $limit
	 * @param integer $limitstart
	 * @param mixed $order
	 * @return
	 */
	function getPlayersRanking($project_id, $division_id, $team_id, $limit = 20, $limitstart = 0, $order=null)
	{
		$sids = SMStatistic::getQuotedSids($this->_ids);
		$option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
		$db = sportsmanagementHelper::getDBConnection();
        
        $query_core = JFactory::getDbo()->getQuery(true);
        
        $query_select_count = 'SUM(ms.event_sum) AS num, tp.id AS tpid, tp.person_id';
        $query_num	= SMStatistic::getPlayersRankingStatisticQuery($project_id, $division_id, $team_id, $sids, $query_select_count,'event');
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query_num<br><pre>'.print_r($query_num->dump(),true).'</pre>'),'');
        }
        
		$query_den = SMStatistic::getGamesPlayedQuery($project_id, $division_id, $team_id);
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query_den<br><pre>'.print_r($query_den->dump(),true).'</pre>'),'');
}

		$query_select_count = ' COUNT(DISTINCT tp.id) as count';

		$query_select_details = ' (n.num / d.played) AS total, n.person_id, 1 as rank,'
							  . ' tp.id AS teamplayer_id, tp.person_id, tp.picture AS teamplayerpic,'
							  . ' p.firstname, p.nickname, p.lastname, p.picture, p.country,'
							  . ' st.team_id, pt.picture AS projectteam_picture,'
							  . ' t.picture AS team_picture, t.name AS team_name, t.short_name AS team_short_name';

		
        
        $query_core->select($query_select_count);
        $query_core->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp');
        $query_core->join('INNER','('.$query_num.') AS n ON n.tpid = tp.id');
        $query_core->join('INNER','('.$query_den.') AS d ON d.tpid = tp.id');
        $query_core->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_person AS p ON p.id = tp.person_id ');
        $query_core->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id AS st ON st.team_id = tp.team_id ');
        $query_core->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ON pt.team_id = st.id');
        $query_core->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_team AS t ON st.team_id = t.id');
        $query_core->where('pt.project_id = ' . $project_id);
        $query_core->where('p.published = 1');

		if ($division_id != 0)
		{
            $query_core->where('pt.division_id = ' . $division_id);
		}
		if ($team_id != 0)
		{
            $query_core->where('st.team_id = ' . $team_id);
		}
        
		
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query_core<br><pre>'.print_r($query_core->dump(),true).'</pre>'),'');
        }
        
		$res = new stdclass;
		//$db->setQuery($query_select_count.$query_core);
        $db->setQuery($query_core);
		$res->pagination_total = $db->loadResult();
        
        $query_core->clear('select');
        $query_core->select($query_select_details);
		$query_core->order('total '.(!empty($order) ? $order : $this->getParam('ranking_order', 'DESC')).' '); 
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query_core<br><pre>'.print_r($query_core->dump(),true).'</pre>'),'');
}

		//$db->setQuery($query_select_details.$query_core.$query_end_details, $limitstart, $limit);
        $db->setQuery($query_core, $limitstart, $limit);
		$res->ranking = $db->loadObjectList();

		if ($res->ranking)
		{
			$precision = $this->getPrecision();
			// get ranks
			$previousval = 0;
			$currentrank = 1 + $limitstart;
			foreach ($res->ranking as $k => $row) 
			{
				if ($row->total == $previousval) {
					$res->ranking[$k]->rank = $currentrank;
				}
				else {
					$res->ranking[$k]->rank = $k + 1 + $limitstart;
				}
				$previousval = $row->total;
				$currentrank = $res->ranking[$k]->rank;

				$res->ranking[$k]->total = self::formatValue($res->ranking[$k]->total, 1, $precision);
			}
		}

		return $res;
	}
		
	
	/**
	 * SMStatisticEventPergame::getTeamsRanking()
	 * 
	 * @param mixed $project_id
	 * @param integer $limit
	 * @param integer $limitstart
	 * @param mixed $order
	 * @return
	 */
	function getTeamsRanking($project_id, $limit = 20, $limitstart = 0, $order = NULL)
	{
		$app = JFactory::getApplication();
        $sids = SMStatistic::getQuotedSids($this->_ids);
		$db = sportsmanagementHelper::getDBConnection();
        $query = $db->getQuery(true);
        $query_num = $db->getQuery(true);
        $query_den = $db->getQuery(true);
        
        
        $query_num->select('SUM(es.event_sum) AS num, pt.id');
        $query_num->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp');
        $query_num->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id AS st ON st.team_id = tp.team_id ');
        $query_num->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ON pt.team_id = st.id ');
        $query_num->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_event AS es ON es.teamplayer_id = tp.id AND es.event_type_id IN ('. implode(',', $sids) .')' );
        $query_num->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match AS m ON m.id = es.match_id AND m.published = 1 ');
        $query_num->where('pt.project_id = '. $projectid);
        $query_num->where('tp.published = 1');
        $query_num->group('pt.id');

		$query_den->select('COUNT(m.id) AS value, pt.id');
        $query_den->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ');
        $query_den->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match AS m ON m.projectteam1_id = pt.id OR m.projectteam2_id = pt.id AND m.team1_result IS NOT NULL AND m.published = 1 ');
        $query_den->where('pt.project_id = '. $projectid);
        $query_den->group('pt.id');
	
        $query->select('(n.num / d.value) AS total, pt.team_id');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id as st ON st.id = pt.team_id ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_team AS t ON st.team_id = t.id ');
        
        $query->join('INNER','('.$query_num.') AS n ON n.id = pt.id ');
        $query->join('INNER','('.$query_den.') AS d ON d.id = pt.id ');
        
        $query->where('pt.project_id = '. $projectid);
        $query->order('total DESC');
		
		$db->setQuery($query, $limitstart, $limit);
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' dump<br><pre>'.print_r($query->dump(),true).'</pre>'),'');
        }
        
		$res = $db->loadObjectList();
	
		if (!empty($res))
		{
			$precision = $this->getPrecision();
			// get ranks
			$previousval = 0;
			$currentrank = 1 + $limitstart;
			foreach ($res as $k => $row) 
			{
				if ($row->total == $previousval) {
					$res[$k]->rank = $currentrank;
				}
				else {
					$res[$k]->rank = $k + 1 + $limitstart;
				}
				$previousval = $row->total;
				$currentrank = $res[$k]->rank;
	
				$res[$k]->total = $this->formatValue($res[$k]->total, 1, $precision);
			}
		}
		return $res;
	}
	
	/**
	 * SMStatisticEventPergame::getStaffStats()
	 * 
	 * @param mixed $person_id
	 * @param mixed $team_id
	 * @param mixed $project_id
	 * @return
	 */
	function getStaffStats($person_id, $team_id, $project_id)
	{
		$sids = SMStatistic::getQuotedSids($this->_ids);
		
		$db = sportsmanagementHelper::getDBConnection();
        $query = JFactory::getDbo()->getQuery(true);
        $app = JFactory::getApplication();
        
        $query->select('SUM(ms.value) AS value, tp.person_id');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id AS st ON st.team_id = tp.team_id ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ON pt.team_id = st.id');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_staff_statistic AS ms ON ms.team_staff_id = tp.id AND ms.statistic_id IN ('. implode(',', $sids) .')');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match AS m ON m.id = ms.match_id AND m.published = 1');
        
        $query->where('st.team_id = '. $team_id);
        $query->where('pt.project_id = '. $project_id);
        $query->where('tp.person_id = '. $person_id);
        $query->group('tp.id');

		$db->setQuery($query);
        
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' dump<br><pre>'.print_r($query->dump(),true).'</pre>'),'');
        
		$num = $db->loadResult();
		
        $query->clear();
        $query->select('SUM(ms.id) AS value, tp.person_id');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id AS st ON st.team_id = tp.team_id ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ON pt.team_id = st.id');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_staff AS ms ON ms.team_staff_id = tp.id');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match AS m ON m.id = ms.match_id AND m.published = 1');
        
        $query->where('st.team_id = '. $team_id);
        $query->where('pt.project_id = '. $project_id);
        $query->where('tp.person_id = '. $person_id);
        $query->group('tp.id');

		$db->setQuery($query);
        
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' dump<br><pre>'.print_r($query->dump(),true).'</pre>'),'');
        
		$den = $db->loadResult();
	
		return $this->formatValue($num, $den, $this->getPrecision());
	}
	

	function getHistoryStaffStats($person_id)
	{
		$sids = $this->getQuotedSids();
		
		$db = &sportsmanagementHelper::getDBConnection();
		$query = ' SELECT SUM(ms.value) AS value, tp.person_id '
		       . ' FROM #__joomleague_team_staff AS tp '
		       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
		       . ' INNER JOIN #__joomleague_match_staff_statistic AS ms ON ms.team_staff_id = tp.id '
		       . '   AND ms.statistic_id IN ('. implode(',', $sids) .')'
		       . ' INNER JOIN #__joomleague_match AS m ON m.id = ms.match_id '
		       . '   AND m.published = 1 '
		       . ' WHERE tp.person_id = '. $db->Quote($person_id)
		       . ' GROUP BY tp.id '
		       ;
		$db->setQuery($query);
		$num = $db->loadResult();
		
		$query = ' SELECT COUNT(ms.id) AS value, tp.person_id '
		       . ' FROM #__joomleague_team_staff AS tp '
		       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
		       . ' INNER JOIN #__joomleague_match_staff AS ms ON ms.team_staff_id = tp.id '
		       . ' INNER JOIN #__joomleague_match AS m ON m.id = ms.match_id '
		       . '   AND m.published = 1 '
		       . ' WHERE tp.person_id = '. $db->Quote($person_id)
		       . ' GROUP BY tp.id '
		       ;
		$db->setQuery($query);
		$den = $db->loadResult();
	
		return $this->formatValue($num, $den, $this->getPrecision());
	}

	/**
	 * SMStatisticEventPergame::formatValue()
	 * 
	 * @param mixed $num
	 * @param mixed $den
	 * @param mixed $precision
	 * @return
	 */
	function formatValue($num, $den, $precision)
	{
		$value = (!empty($num) && !empty($den)) ? $num / $den : 0;
		return number_format($value, $precision);
	}
}