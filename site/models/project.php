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

if (! defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

if (! defined('JSM_PATH'))
{
DEFINE( 'JSM_PATH','components/com_sportsmanagement' );
}

jimport('joomla.application.component.model');
jimport('joomla.utilities.arrayhelper');

if (!class_exists('sportsmanagementModeldatabasetool')) 
{
require_once(JPATH_ADMINISTRATOR.DS.JSM_PATH.DS.'models'.DS.'databasetool.php');
// sprachdatei aus dem backend laden
$langtag = JFactory::getLanguage();

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$config = JFactory::getConfig();
$lang = JFactory::getLanguage();
$extension = 'com_sportsmanagement';
$base_dir = JPATH_ADMINISTRATOR;
$language_tag = $langtag->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
    
// welche tabelle soll genutzt werden
$paramscomponent = JComponentHelper::getParams( 'com_sportsmanagement' );
$database_table	= $paramscomponent->get( 'cfg_which_database_table' );
$show_debug_info = $paramscomponent->get( 'show_debug_info' );  
$show_query_debug_info = $paramscomponent->get( 'show_query_debug_info' ); 
$cfg_which_database_server = $paramscomponent->get( 'cfg_which_database_server' );

if (! defined('COM_SPORTSMANAGEMENT_CFG_WHICH_DATABASE'))
{
DEFINE( 'COM_SPORTSMANAGEMENT_CFG_WHICH_DATABASE',$paramscomponent->get( 'cfg_which_database' ) );
}

if (! defined('COM_SPORTSMANAGEMENT_TABLE'))
{
DEFINE( 'COM_SPORTSMANAGEMENT_TABLE',$database_table );
}

if (! defined('COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO'))
{
DEFINE( 'COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO',$show_debug_info );
}

if (! defined('COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO'))
{
DEFINE( 'COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO',$show_query_debug_info );
}


/*
if ( $paramscomponent->get('cfg_dbprefix') && !defined('COM_SPORTSMANAGEMENT_PICTURE_SERVER') )
{
DEFINE( 'COM_SPORTSMANAGEMENT_PICTURE_SERVER',$paramscomponent->get( 'cfg_which_database_server' ) );    
}
else
{
if ( COM_SPORTSMANAGEMENT_CFG_WHICH_DATABASE || JRequest::getInt( 'cfg_which_database', 0 ) )
{
if (! defined('COM_SPORTSMANAGEMENT_PICTURE_SERVER'))
{        
DEFINE( 'COM_SPORTSMANAGEMENT_PICTURE_SERVER',$cfg_which_database_server );
}    
}
else
{
if (! defined('COM_SPORTSMANAGEMENT_PICTURE_SERVER'))
{        
DEFINE( 'COM_SPORTSMANAGEMENT_PICTURE_SERVER',JURI::root() );
}    
}
}
*/

}
            
/**
 * sportsmanagementModelProject
 * 
 * @package   
 * @author 
 * @copyright diddi
 * @version 2014
 * @access public
 */
class sportsmanagementModelProject extends JModelLegacy
{
	static $_project = null;
	static $projectid = 0;
    static $matchid = 0;
    static $_round_from;
    static $_round_to;
    
    static $_match = null;

	/**
	 * project league country
	 * @var string
	 */
	var $country = null;
	/**
	 * data array for teams
	 * @var array
	 */
	static $_teams = null;

	/**
	 * data array for matches
	 * @var array
	 */
	var $_matches = null;

	/**
	 * data array for rounds
	 * @var array
	 */
	static $_rounds = null;

	/**
	 * data project stats
	 * @var array
	 */
	static $_stats = null;

	/**
	 * data project positions
	 * @var array
	 */
	static $_positions = null;

	/**
	 * cache for project divisions
	 *
	 * @var array
	 */
	static $_divisions = null;

	/**
	 * caching for current round
	 * @var object
	 */
	static $_current_round;
    
    static $seasonid = 0;
    static $cfg_which_database = 0;
    
    static $favteams = NULL;
    
    static $projectslug	= '';
	static $divisionslug = '';
	static $roundslug = '';
    
    static $layout = '';


	/**
	 * sportsmanagementModelProject::__construct()
	 * 
	 * @return void
	 */
	function __construct()
	{
		// Reference global application object
        $app = JFactory::getApplication();
        // JInput object
        $jinput = $app->input;
        //sportsmanagementHelper::$_success_text[__CLASS__] = array();
        self::$projectid = $jinput->getVar('p','0');
        self::$cfg_which_database = $jinput->getVar('cfg_which_database','0');
        self::$matchid = $jinput->getVar('mid','0');
        self::$layout = $jinput->getVar('layout','');
        //$app->setUserState( "com_sportsmanagement.cfg_which_database", JRequest::getInt('cfg_which_database',0) );
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r(self::$projectid,true).'</pre>'),'');
        //self::updateHits(self::$projectid);
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $my_text = 'from '.self::$_round_from.'<br>';    
        $my_text .= 'to '.self::$_round_to.'<br>';
        $my_text .= 'projectid '.self::$projectid.'<br>';
//        $my_text .= 'round '.$this->round.'<br>';
//        $my_text .= 'current_round '.$this->current_round.'<br>';
//        $my_text .= 'self from '.$this->from.'<br>';
//        $my_text .= 'self to '.$this->to.'<br>';
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
        }
        
		parent::__construct();
	}


	/**
	 * sportsmanagementModelProject::updateHits()
	 * 
	 * @param integer $projectid
	 * @return void
	 */
	public static function updateHits($projectid=0,$inserthits=0)
    {
        $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
    $db = sportsmanagementHelper::getDBConnection(TRUE, self::$cfg_which_database );
 $query = $db->getQuery(true);
 
 if ( $inserthits )
 {
 $query->update($db->quoteName('#__sportsmanagement_project'))->set('hits = hits + 1')->where('id = '.(int)$projectid);
 
$db->setQuery($query);
 
$result = $db->execute();
}  
//$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.'<br><pre>'.print_r($db->getErrorMsg(),true).'</pre>'),'Error');     
    }
    
    
	/**
	 * sportsmanagementModelProject::getProject()
	 * 
	 * @param integer $cfg_which_database
	 * @param string $call_function
	 * @param integer $inserthits
	 * @return
	 */
	public static function getProject($cfg_which_database = 0,$call_function = '',$inserthits=0)
	{
		$option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        //$db = JFactory::getDbo();
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $starttime = microtime();
        
        if ( !self::$projectid )
        {
            self::$projectid = JRequest::getInt('p',0);
        }
        
        self::updateHits(self::$projectid,$inserthits); 

      // $this->projectid = JRequest::getInt('p',0);
    
//    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' call_function<br><pre>'.print_r($call_function,true).'</pre>'),'');
//    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' cfg_which_database<br><pre>'.print_r($cfg_which_database,true).'</pre>'),'');
//    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r(self::$projectid,true).'</pre>'),'');
//    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' _current_round<br><pre>'.print_r(self::$_current_round,true).'</pre>'),'');
    
    if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
        $my_text = 'projectid -> '.self::$projectid.'<br>' ;   
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
        

    //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r(self::$projectid,true).'</pre>'),'');
    }
    
        //if (is_null(self::$_project) && self::$projectid > 0)
        if ( self::$projectid > 0 )
		{
			//fs_sport_type_name = sport_type folder name
            $query->select('p.*, l.country, st.id AS sport_type_id, st.name AS sport_type_name');
            $query->select('st.icon AS sport_type_picture, l.picture as leaguepicture, l.name as league_name, s.name as season_name  ');
            $query->select('LOWER(SUBSTR(st.name, CHAR_LENGTH( "COM_SPORTSMANAGEMENT_ST_")+1)) AS fs_sport_type_name');
            $query->select('CONCAT_WS( \':\', p.id, p.alias ) AS slug');
            
            $query->select('CONCAT_WS( \':\', l.id, l.alias ) AS league_slug');
            $query->select('CONCAT_WS( \':\', s.id, s.alias ) AS season_slug');
            $query->select('CONCAT_WS( \':\', r.id, r.alias ) AS round_slug');
            
            $query->select('l.cr_picture as cr_leaguepicture');
            $query->from('#__sportsmanagement_project AS p ');
        $query->join('INNER','#__sportsmanagement_sports_type AS st ON p.sports_type_id = st.id ');
        $query->join('LEFT','#__sportsmanagement_league AS l ON p.league_id = l.id ');
        $query->join('LEFT','#__sportsmanagement_season AS s ON p.season_id = s.id ');
        $query->join('LEFT','#__sportsmanagement_round AS r ON p.current_round = r.id ');
            $query->where('p.id = '. (int)self::$projectid);
            

			$db->setQuery($query,0,1);
            
//            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' dump<br><pre>'.print_r($query->dump(),true).'</pre>'),'');
            
            if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
        //$result = $db->loadObject();
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' call_function<br><pre>'.print_r($call_function,true).'</pre>'),'Error');
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' result<br><pre>'.print_r($result,true).'</pre>'),'Error');
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' _project<br><pre>'.print_r(self::$_project,true).'</pre>'),'Error');

    
			self::$_project = $db->loadObject();
            
            if ( self::$_project)
            {
            self::$projectslug = self::$_project->slug;
            }
            
            if ( !self::$seasonid )
            {
            self::$seasonid = self::$_project->season_id;
            }
            
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' season_id<br><pre>'.print_r(self::$_project->season_id,true).'</pre>'),'');
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' seasonid<br><pre>'.print_r(self::$seasonid,true).'</pre>'),'');
            
            
            if ( !self::$_project && COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' seasonid<br><pre>'.print_r(self::$seasonid,true).'</pre>'),'Error');
            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.'<br><pre>'.print_r($db->getErrorMsg(),true).'</pre>'),'Error');
        }
            
            
		}
		return self::$_project;
	}

	/**
	 * sportsmanagementModelProject::setProjectID()
	 * 
	 * @param integer $id
	 * @return void
	 */
	public static function setProjectID($id=0,$cfg_which_database = 0)
	{
	   $app = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        
		self::$projectid = (int)$id;
		self::$_project = null;
        self::$_current_round = 0;
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $my_text = 'projectid -> '.self::$projectid.'<br>' ;
        
        //sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
        //sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
        //sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
        
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r(self::$projectid,true).'</pre>'),'');
        }
        
	}

	/**
	 * sportsmanagementModelProject::getSportsType()
	 * 
	 * @return
	 */
	public static function getSportsType($cfg_which_database = 0)
	{
		if (!$project = self::getProject($cfg_which_database,__METHOD__))
		{
			$this->setError(0, Jtext::_('COM_SPORTSMANAGEMENT_ERROR_PROJECTMODEL_PROJECT_IS_REQUIRED'));
			return false;
		}
		
		return $project->sports_type_id;
	}
  
  
  

	
	/**
	 * sportsmanagementModelProject::getCurrentRound()
	 * 
	 * @param mixed $view
	 * @param integer $cfg_which_database
	 * @return
	 */
	public static function getCurrentRound($view=NULL,$cfg_which_database = 0)
	{
	   $app = JFactory::getApplication();
		$round = self::increaseRound($cfg_which_database);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' view: '.$view.' round<br><pre>'.print_r($round,true).'</pre>'),'');
        self::$_current_round = $round;
        
        switch ($view)
        {
            case 'result':
            sportsmanagementModelResults::$roundid = self::$_current_round;
            break;
        }
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' view: '.$view.' _current_round<br><pre>'.print_r(self::$_current_round,true).'</pre>'),'');
        
		return ($round ? $round->id : 0);
	}

	
	/**
	 * sportsmanagementModelProject::getCurrentRoundNumber()
	 * 
	 * @param integer $cfg_which_database
	 * @return
	 */
	public static function getCurrentRoundNumber($cfg_which_database = 0)
	{
	   $app = JFactory::getApplication();
		$round = self::increaseRound($cfg_which_database);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' round<br><pre>'.print_r($round,true).'</pre>'),'');
        
		return ($round ? $round->roundcode : 0);
	}

	/**
	 * method to update and return the project current round
	 * @return object
	 */
	public static function increaseRound($cfg_which_database = 0)
	{
		$option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $result = '';
        $project = self::getProject($cfg_which_database,__METHOD__);

        if (!self::$_current_round)
		{
		    //$project = self::getProject($cfg_which_database,__METHOD__);
			
//            if (!$project = self::getProject($cfg_which_database,__METHOD__)) 
//            {
//                JError::raiseError(500, Jtext::_('COM_SPORTSMANAGEMENT_ERROR_PROJECTMODEL_PROJECT_IS_REQUIRED'));
//				return false;
//			}
			
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project<br><pre>'.print_r($project,true).'</pre>'),'');
            
			$current_date = strftime("%Y-%m-%d %H:%M:%S");
            $query->clear();
            $query->select('r.id, r.roundcode, CONCAT_WS( \':\', r.id, r.alias ) AS round_slug');
            $query->from('#__sportsmanagement_round AS r ');
            
	
			//$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project<br><pre>'.print_r($project->current_round_auto,true).'</pre>'),'');
            
            // determine current round according to project settings
			switch ($project->current_round_auto)
			{
				case 0 :	 // manual mode
                $query->where('r.id = '.$project->current_round);
                $query->where('r.project_id = '.$project->id);
					break;
	
				case 1 :	 // get current round from round_date_first
                $query->where('r.project_id = '.$project->id);
                $query->where("(r.round_date_first - INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')");
                $query->order('r.round_date_first DESC LIMIT 1');
					break;
	
				case 2 : // get current round from round_date_last
                $query->where('r.project_id = '.$project->id);
                $query->where("(r.round_date_last - INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')");
                $query->order('r.round_date_first DESC LIMIT 1');
					break;
	
				case 3 : // get current round from first game of the round
                $query->join('INNER','#__sportsmanagement_match AS m ON m.round_id = r.id');
                $query->where('r.project_id = '.$project->id);
                $query->where("(m.match_date - INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')");
                $query->order('m.match_date DESC LIMIT 1');
					break;
	
				case 4 : // get current round from last game of the round
                $query->join('INNER','#__sportsmanagement_match AS m ON m.round_id = r.id');
                $query->where('r.project_id = '.$project->id);
                $query->where("(m.match_date + INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')");
                $query->order('m.match_date ASC LIMIT 1');
					break;
			}
            
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' round<br><pre>'.print_r($query->dump(),true).'</pre>'),'');
              try{
			$db->setQuery($query);
			$result = $db->loadObject();
				      }
catch (Exception $e){
    echo $e->getMessage();
}
			// If result is empty, it probably means either this is not started, either this is over, depending on the mode. 
			// Either way, do not change current value
			if (!$result)
			{
			 
//            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Error');
//            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($db->getErrorMsg(),true).'</pre>'),'Error');
             
			$query->clear();
            $query->select('r.id, r.roundcode,CONCAT_WS( \':\', r.id, r.alias ) AS round_slug');
            $query->from('#__sportsmanagement_round AS r ');
            $query->where('r.id = '.$project->current_round);
            $query->where('r.project_id = '. $project->id);

            try{
				$db->setQuery($query);
				$result = $db->loadObject();
				}
catch (Exception $e){
    echo $e->getMessage();
}

				if (!$result)
				{
				    //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project<br><pre>'.print_r($project,true).'</pre>'),'');
                    
				    $query->clear();
                    $query->select('r.id, r.roundcode,CONCAT_WS( \':\', r.id, r.alias ) AS round_slug');
                    $query->from('#__sportsmanagement_round AS r ');
                    $query->where('r.project_id = '. $project->id);
                    
                    // determine current round according to project settings
			switch ($project->current_round_auto)
			{
				case 0 :	 // manual mode
                case 2 : // get current round from round_date_last
                // the current value is invalid... saison is over, just take the last round
                        $query->order('r.roundcode DESC');
					break;
                    default:
                    // the current value is invalid... just take the first round
                        $query->order('r.roundcode ASC');
                    break;
                    }
                        
//					if ( $project->current_round_auto == 2 ) 
//                    {
//					    // the current value is invalid... saison is over, just take the last round
//                        $query->order('r.roundcode DESC');
//
//					    $db->setQuery($query);
//					    $result = $db->loadObject();					
//					} 
//                    else 
//                    {
//					    // the current value is invalid... just take the first round
//                        $query->order('r.roundcode ASC');
//
//					    $db->setQuery($query);
//					    $result = $db->loadObject();
//					}
					
          try{
                    $db->setQuery($query);
	                $result = $db->loadObject();  
                  }
catch (Exception $e){
    echo $e->getMessage();
}  
				}
			}
			
			// Update the database if determined current round is different from that in the database
//            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' result<br><pre>'.print_r($result,true).'</pre>'),'');
//            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' current_round<br><pre>'.print_r($project->current_round,true).'</pre>'),'');
//            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' id<br><pre>'.print_r($result->id,true).'</pre>'),'');
            
			if ($result && ($project->current_round <> $result->id))
			{
			 // Must be a valid primary key value.
             $object = new stdClass();
             $object->id = $project->id;
             $object->current_round = $result->id;
             // Update their details in the users table using id as the primary key.
             $resultupdate = JFactory::getDbo()->updateObject('#__sportsmanagement_project', $object, 'id');

				if (!$resultupdate) 
                {
//                    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' resultupdate<br><pre>'.print_r($resultupdate,true).'</pre>'),'Error');
//                    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' object<br><pre>'.print_r($object,true).'</pre>'),'Error');
                    JError::raiseWarning(500, JText::_('COM_SPORTSMANAGEMENT_ERROR_CURRENT_ROUND_UPDATE_FAILED'));
				}
                else
                {
//                    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' resultupdate<br><pre>'.print_r($resultupdate,true).'</pre>'),'');
//                    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' object<br><pre>'.print_r($object,true).'</pre>'),'');
                }
			}
            
            if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $my_text = 'result <br><pre>'.print_r($result,true).'</pre>'; 
   
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' result<br><pre>'.print_r($result,true).'</pre>'),'');
        }
       
            
			self::$_current_round = $result;
		}
       
        if ( !isset(self::$_current_round->round_slug) )
        {
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project_current_round<br><pre>'.print_r($project->current_round,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project<br><pre>'.print_r($project->id,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project<br><pre>'.print_r($project->name,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' _current_round<br><pre>'.print_r(self::$_current_round,true).'</pre>'),'');
        self::$roundslug = '';
        }
        else
        {
        self::$roundslug = self::$_current_round->round_slug;    
        }
        
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' result<br><pre>'.print_r($result,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project->id<br><pre>'.print_r($project->id,true).'</pre>'),'');
        
        
		return self::$_current_round;
	}

	/**
	 * sportsmanagementModelProject::getColors()
	 * 
	 * @param string $configcolors
	 * @return
	 */
	public static function getColors($configcolors='',$cfg_which_database = 0)
	{
		$s=substr($configcolors,0,-1);

		$arr1=array();
		if(trim($s) != "")
		{
			$arr1=explode(";",$s);
		}

		$colors=array();

		$colors[0]["from"]="";
		$colors[0]["to"]="";
		$colors[0]["color"]="";
		$colors[0]["description"]="";

		for($i=0; $i < count($arr1); $i++)
		{
			$arr2=explode(",",$arr1[$i]);
			if(count($arr2) != 4)
			{
				break;
			}

			$colors[$i]["from"]=$arr2[0];
			$colors[$i]["to"]=$arr2[1];
			$colors[$i]["color"]=$arr2[2];
			$colors[$i]["description"]=$arr2[3];
		}
		return $colors;
	}

	/**
	 * sportsmanagementModelProject::getDivisionsId()
	 * 
	 * @param integer $divLevel
	 * @return
	 */
	function getDivisionsId($divLevel=0,$cfg_which_database = 0)
	{
		$option = JRequest::getCmd('option');
	   $app = JFactory::getApplication();
       // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        // Select some fields
        $query->select('id');
        // From 
		$query->from('#__sportsmanagement_division');
        // Where
        $query->where('project_id = '.(int)self::$projectid);
        
        if ( $divLevel == 1 )
		{
            $query->where('(parent_id=0 OR parent_id IS NULL)');
		}
		else if ($divLevel==2)
		{
            $query->where('parent_id > 0');
		}
        $query->order('ordering');
		$db->setQuery($query);
		
		if(version_compare(JVERSION,'3.0.0','ge')) 
        {
        // Joomla! 3.0 code here
        $res = $db->loadColumn();
        }
        elseif(version_compare(JVERSION,'2.5.0','ge')) 
        {
        // Joomla! 2.5 code here
        $res = $db->loadResultArray();
        } 
        
		if(count($res) == 0) {
			echo JText::_('COM_SPORTSMANAGEMENT_RANKING_NO_SUBLEVEL_DIVISION_FOUND') . $divLevel;
		}
		return $res;
	}

	/**
	 * return an array of division id and it's subdivision ids
	 * @param int division id
	 * @return int
	 */
	public static function getDivisionTreeIds($divisionid,$cfg_which_database = 0)
	{
		if ($divisionid == 0) {
			return self::getDivisionsId(0,$cfg_which_database);
		}
		$divisions = self::getDivisions(0,$cfg_which_database);
		$res = array($divisionid);
		foreach ($divisions as $d)
		{
			if ($d->parent_id == $divisionid) {
				$res[]=$d->id;
			}
		}
		return $res;
	}

	/**
	 * sportsmanagementModelProject::getDivision()
	 * 
	 * @param mixed $id
	 * @return
	 */
	public static function getDivision($id,$cfg_which_database = 0)
	{
		$divs = self::getDivisions(0,$cfg_which_database);
		if ($divs && isset($divs[$id])) {
			return $divs[$id];
		}
		$div = new stdClass();
		$div->id = 0;
		$div->name = '';
		return $div;
	}

	/**
	 * sportsmanagementModelProject::getDivisions()
	 * 
	 * @param integer $divLevel
	 * @return
	 */
	public static function getDivisions($divLevel=0,$cfg_which_database = 0)
	{
		$option = JRequest::getCmd('option');
	   $app = JFactory::getApplication();
       // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        $project = self::getProject($cfg_which_database,__METHOD__); 
		if ($project->project_type == 'DIVISIONS_LEAGUE')
		{
			if (empty(self::$_divisions))
			{
				// Select some fields
                $query->select('*');
                // From 
		          $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_division');
                // Where
                $query->where('project_id = '.(int)self::$projectid);

				$db->setQuery($query);
				self::$_divisions = $db->loadObjectList('id');
			}
			if ($divLevel)
			{
				$ids = self::getDivisionsId($divLevel,$cfg_which_database);
				$res = array();
				foreach ($this->_divisions as $d)
				{
					if (in_array($d->id,$ids)) {
						$res[] = $d;
					}
				}
				return $res;
			}
			return self::$_divisions;
		}
		return array();
	}

	/**
	 * return project rounds objects ordered by roundcode
	 *
	 * @param string ordering 'ASC or 'DESC'
	 * @return array
	 */
	public static function getRounds($ordering='ASC',$cfg_which_database = 0,$slug = TRUE)
	{
		$option = JRequest::getCmd('option');
	   $app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $starttime = microtime(); 
        
        if ( !self::$projectid )
        {
            self::$projectid = JRequest::getInt('p',0);
        } 
        
        if (empty(self::$_rounds))
		{
			// Select some fields
            if ( $slug )
            {
            $query->select('CONCAT_WS( \':\', id, alias ) AS id');
            }
            else
            {
            $query->select('id');    
            }
                $query->select('round_date_first,round_date_last,CASE LENGTH(name) when 0 then roundcode else name END as name,roundcode');
                // From 
		          $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_round');
                // Where
                $query->where('project_id = '.(int)self::$projectid);
                // order
                $query->order('roundcode ASC');

			$db->setQuery($query);
            
            if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
			self::$_rounds = $db->loadObjectList();
		}
		
        if ( !self::$_rounds && COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
	    {
	    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' '.'<pre>'.print_r($db->getErrorMsg(),true).'</pre>' ),'Error');   
		$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid'.'<pre>'.print_r(self::$projectid,true).'</pre>' ),'Error');
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query'.'<pre>'.print_r($query->dump(),true).'</pre>' ),'Error');
	    }
        
        if ($ordering == 'DESC') 
        {
			return array_reverse(self::$_rounds);
		}
		
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query'.'<pre>'.print_r($query->dump(),true).'</pre>' ),'');
        
        return self::$_rounds;
	}

	
	/**
	 * sportsmanagementModelProject::getRoundOptions()
	 * 
	 * @param string $ordering
	 * @param integer $cfg_which_database
	 * @param bool $slug
	 * @return
	 */
	public static function getRoundOptions($ordering='ASC',$cfg_which_database = 0,$slug = TRUE)
	{
		$option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $starttime = microtime(); 
        
        if ( !self::$projectid )
        {
            self::$projectid = JRequest::getInt('p',0);
        } 
        
        // Select some fields
        if ( $slug )
        {
        $query->select('CONCAT_WS( \':\', id, alias ) AS slug');
        }
        $query->select('id AS value');
        $query->select("CASE LENGTH(name) when 0 then CONCAT('".JText::_('COM_SPORTSMANAGEMENT_MATCHDAY_NAME'). "',' ', id) else name END as text");
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_round ');
        $query->where('project_id = '.(int)self::$projectid);
        $query->order('roundcode '.$ordering);

		$db->setQuery($query);
        $result = $db->loadObjectList();
        
        if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
        if ( !$result && COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
	    {
	    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' '.'<pre>'.print_r($db->getErrorMsg(),true).'</pre>' ),'Error');   
		$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid'.'<pre>'.print_r(self::$projectid,true).'</pre>' ),'Error');
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query'.'<pre>'.print_r($query->dump(),true).'</pre>' ),'Error');
	    }
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' query'.'<pre>'.print_r($query->dump(),true).'</pre>' ),'');
        
		return $result;
	}

	/**
	 * sportsmanagementModelProject::getTeaminfo()
	 * 
	 * @param mixed $projectteamid
	 * @return
	 */
	public static function getTeaminfo($projectteamid,$cfg_which_database = 0)
	{
		$option = JRequest::getCmd('option');
	   $app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $starttime = microtime(); 
        
        if ( $projectteamid )
        {
        // Select some fields
        $query->select('t.*,t.id as team_id,t.picture,t.picture as team_picture,t.extended as teamextended ');
        $query->select('CONCAT_WS(\':\',t.id,t.alias) AS team_slug');
        $query->select('pt.division_id,pt.picture AS projectteam_picture');
        $query->select('c.logo_small,c.logo_middle,c.logo_big');
        //$query->select('IF((ISNULL(pt.picture) OR (pt.picture="")),(IF((ISNULL(t.picture) OR (t.picture="")), c.logo_big , t.picture)) , pt.picture) as picture');
        // From 
		$query->from('#__sportsmanagement_project_team AS pt ');
        $query->join('INNER','#__sportsmanagement_season_team_id as st ON st.id = pt.team_id ');
        $query->join('INNER','#__sportsmanagement_team AS t ON st.team_id = t.id ');
        $query->join('INNER','#__sportsmanagement_club AS c ON t.club_id = c.id  ');
        // Where
        $query->where('pt.id = '. (int)$projectteamid );
         
		$db->setQuery($query);
        
        if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
        $result = $db->loadObject();
        
            if ( !$result && COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
		    {
		    $my_text = 'getErrorMsg<pre>'.print_r($db->getErrorMsg(),true).'</pre>'; 
        $my_text .= 'dump<pre>'.print_r($query->dump(),true).'</pre>';
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);  
			//$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' '.'<pre>'.print_r($db->getErrorMsg(),true).'</pre>' ),'Error');
		    }
            
            if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
       {
        $my_text = 'dump<pre>'.print_r($query->dump(),true).'</pre>'; 
        $my_text .= 'team_id<pre>'.print_r($result,true).'</pre>';
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' team_id'.'<pre>'.print_r($result,true).'</pre>' ),'');
        }
		
        }
        else
        {
            $result = FALSE;
        }
        
        $db->disconnect(); // See: http://api.joomla.org/cms-3/classes/JDatabaseDriver.html#method_disconnect
        return $result;
        
	}

	
	/**
	 * sportsmanagementModelProject::_getTeams()
	 * 
	 * @param string $teamname
	 * @return
	 */
	public static function & _getTeams($teamname='name',$cfg_which_database = 0,$call_function = '')
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $starttime = microtime(); 

		//if (empty(self::$_teams))
		//{
		  // Select some fields
          $query->select('tl.id AS projectteamid,tl.division_id,tl.standard_playground,tl.admin,tl.start_points,tl.points_finally,tl.neg_points_finally,tl.matches_finally,tl.won_finally,tl.draws_finally,tl.lost_finally');
          $query->select('tl.homegoals_finally,tl.guestgoals_finally,tl.diffgoals_finally,tl.info,tl.reason,tl.team_id as project_team_team_id,tl.checked_out,tl.checked_out_time,tl.is_in_score,tl.picture AS projectteam_picture');
          $query->select('IF((ISNULL(tl.picture) OR (tl.picture="")),(IF((ISNULL(t.picture) OR (t.picture="")), c.logo_small , t.picture)) , t.picture) as picture,tl.project_id');
          $query->select('t.picture as team_picture,t.id,t.name,t.short_name,t.middle_name,t.notes,t.club_id');
          $query->select('u.username,u.email');
          $query->select('st.team_id');
          $query->select('c.email as club_email,c.logo_small,c.logo_middle,c.logo_big,c.country,c.website');
          $query->select('d.name AS division_name,d.shortname AS division_shortname,d.parent_id AS parent_division_id');
          $query->select('plg.name AS playground_name,plg.short_name AS playground_short_name');
          $query->select('CONCAT_WS(\':\',p.id,p.alias) AS project_slug');
          $query->select('CONCAT_WS(\':\',t.id,t.alias) AS team_slug');
          $query->select('CONCAT_WS(\':\',tl.id,t.alias) AS projectteam_slug');
          $query->select('CONCAT_WS(\':\',d.id,d.alias) AS division_slug');
          $query->select('CONCAT_WS(\':\',c.id,c.alias) AS club_slug');
          
          // f�r die anzeige der teams im frontend
          $query->select('t.name as team_name,t.short_name,t.middle_name,t.club_id,t.website AS team_www,t.picture as team_picture,c.name as club_name,c.address as club_address');
          $query->select('c.zipcode as club_zipcode,c.state as club_state,c.location as club_location,c.email as club_email,c.unique_id,c.country as club_country,c.website AS club_www');
          
          
          $query->from('#__sportsmanagement_project_team AS tl ');
          $query->join('LEFT',' #__sportsmanagement_season_team_id st ON st.id = tl.team_id ');
          $query->join('LEFT',' #__sportsmanagement_team t ON st.team_id = t.id ');
          $query->join('LEFT',' #__users u ON tl.admin=u.id ');
          $query->join('LEFT',' #__sportsmanagement_club c ON t.club_id = c.id ');
          $query->join('LEFT',' #__sportsmanagement_division d ON d.id = tl.division_id ');
          $query->join('LEFT',' #__sportsmanagement_playground plg ON plg.id = tl.standard_playground ');
          $query->join('LEFT',' #__sportsmanagement_project AS p ON p.id = tl.project_id ');
          $query->where('tl.project_id = '.(int)self::$projectid);

			$db->setQuery($query);
            
            
            
            if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
            
			self::$_teams = $db->loadObjectList();
            
            //$app->enqueueMessage(JText::_(get_class($this).' '.__FUNCTION__.' teams<br><pre>'.print_r($db->getErrorMsg(),true).'</pre>'),'');
            //$app->enqueueMessage(JText::_(get_class($this).' '.__FUNCTION__.' teams<br><pre>'.print_r($this->_teams,true).'</pre>'),'');
		//}
		return self::$_teams;
	}

	
	/**
	 * sportsmanagementModelProject::getTeams()
	 * 
	 * @param integer $division
	 * @param string $teamname
	 * @return
	 */
	public static function getTeams($division=0,$teamname='name',$cfg_which_database = 0,$call_function = '')
	{
	   $app = JFactory::getApplication();
       //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' cfg_which_database<br><pre>'.print_r($cfg_which_database,true).'</pre>'),'Notice');
       //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' call_function<br><pre>'.print_r($call_function,true).'</pre>'),'Notice');
        
		$teams = array();
		if ($division != 0)
		{
			$divids = self::getDivisionTreeIds($division,$cfg_which_database);
			foreach ((array)self::_getTeams($teamname,$cfg_which_database,__METHOD__) as $t)
			{
				if (in_array($t->division_id,$divids))
				{
					$teams[]=$t;
				}
			}
		}
		else
		{
			$teams = self::_getTeams($teamname,$cfg_which_database);
		}

		return $teams;
	}

	/**
	 * return array of team ids
	 *
	 * @return array	 *
	 */
	function getTeamIds($division=0,$cfg_which_database = 0)
	{
		$teams = array();
		foreach ((array)self::_getTeams($teamname,$cfg_which_database,__METHOD__) as $t)
		{
			if (!$division || $t->division_id == $division) {
				$teams[] = $t->id;
			}
		}
		return $teams;
	}

	
	/**
	 * sportsmanagementModelProject::getTeamsIndexedById()
	 * 
	 * @param integer $division
	 * @param string $teamname
	 * @return
	 */
	public static function getTeamsIndexedById($division=0,$teamname='name',$cfg_which_database = 0)
	{
		$result = self::getTeams($division,$teamname,$cfg_which_database);
		$teams = array();
		if (count($result))
		{
			foreach($result as $r)
			{
				$teams[$r->id] = $r;
			}
		}

		return $teams;
	}

	
	/**
	 * sportsmanagementModelProject::getTeamsIndexedByPtid()
	 * 
	 * @param integer $division
	 * @param string $teamname
	 * @return
	 */
	public static function getTeamsIndexedByPtid($division=0,$teamname='name',$cfg_which_database = 0,$call_function = '')
	{
	   $app = JFactory::getApplication();
       //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' cfg_which_database<br><pre>'.print_r($cfg_which_database,true).'</pre>'),'Notice');
       //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' call_function<br><pre>'.print_r($call_function,true).'</pre>'),'Notice');
       
		$result = self::getTeams($division,$teamname,$cfg_which_database,$call_function);
		$teams = array();

		if (count($result))
		{
			foreach($result as $r)
			{
				$teams[$r->projectteamid] = $r;
			}
		}
		return $teams;
	}

	/**
	 * sportsmanagementModelProject::getFavTeams()
	 * 
	 * @return
	 */
	public static function getFavTeams($cfg_which_database = 0)
	{
		$project = self::getProject($cfg_which_database,__METHOD__);
		if(!is_null($project))
        {
        self::$favteams = explode(",",$project->fav_team);
		return self::$favteams;
        }
		else
        {
		return array();
        }
	}

	/**
	 * sportsmanagementModelProject::getEventTypes()
	 * 
	 * @param integer $evid
	 * @return
	 */
	public static function getEventTypes($evid=0,$cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        $query->select('et.id AS etid,et.name,et.icon');
        $query->select('me.event_type_id AS id');
        $query->select('CONCAT_WS( \':\', et.id, et.alias ) AS event_slug');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_eventtype AS et');
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_event AS me ON et.id = me.event_type_id');

		if ($evid != 0)
		{

            $query->where('me.event_type_id = '.(int)$evid);
		}
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
            {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'');
        }

		$db->setQuery($query);
		return $db->loadObjectList('etid');
	}



	/**
	 * sportsmanagementModelProject::getprojectteamID()
	 * 
	 * @param mixed $teamid
	 * @return
	 */
	public static function getprojectteamID($teamid,$cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
    
    if ( !self::$projectid )
        {
            self::$projectid = JRequest::getInt('p',0);
        } 
        
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        $query->select('id');
        $query->from('#__sportsmanagement_project_team');
        $query->where('team_id = '.(int)$teamid);
        $query->where('project_id = '.(int)self::$projectid);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Method to return a playgrounds array (id,name)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getPlaygrounds($cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $query->select('id AS value,name AS text');
        $query->from('#__sportsmanagement_playground');
        $query->order('text ASC');

		$db->setQuery($query);
		if (!$result = $db->loadObjectList())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		else
		{
			return $result;
		}
	}
    
    
	/**
	 * sportsmanagementModelProject::getProjectGameRegularTime()
	 * 
	 * @param mixed $project_id
	 * @return
	 */
	function getProjectGameRegularTime($project_id,$cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $gameprojecttime = 0;
        $query->select('game_regular_time');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_project');
        $query->where('id = '.(int)$project_id);

		$db->setQuery($query);
		$result = $db->loadObject();
		
        $gameprojecttime += $result->game_regular_time;
        if ( $result->allow_add_time )
        {
            $gameprojecttime += $result->add_time;
        }
        
        return $gameprojecttime;
	}

	/**
	 * sportsmanagementModelProject::getReferees()
	 * 
	 * @return
	 */
	function getReferees($cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
		$project = self::getProject($cfg_which_database,__METHOD__);
		if ($project->teams_as_referees)
		{
		  $query->select('id AS value,name AS text');
          $query->from('#__sportsmanagement_team');
          $query->order('name');

			$db->setQuery($query);
			$refs = $db->loadObjectList();
		}
		else
		{
		  $query->select('id AS value,firstname,lastname');
          $query->from('#__sportsmanagement_project_referee');
          $query->order('lastname');

			$db->setQuery($query);
			$refs = $db->loadObjectList();
			foreach($refs as $ref)
			{
				$ref->text = $ref->lastname.",".$ref->firstname;
			}
		}
		return $refs;
	}

	/**
	 * sportsmanagementModelProject::getTemplateConfig()
	 * 
	 * @param mixed $template
	 * @return
	 */
	public static function getTemplateConfig($template,$cfg_which_database = 0,$call_function = '')
	{
		$option = JRequest::getCmd('option');
        $app	= JFactory::getApplication();
        
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' call_function<br><pre>'.print_r($call_function,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' template<br><pre>'.print_r($template,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' cfg_which_database<br><pre>'.print_r($cfg_which_database,true).'</pre>'),'');
//        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r(self::$projectid,true).'</pre>'),'');
        
        //self::$projectid = JRequest::getInt('p',0);
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        //$query2 = $db->getQuery(true);
        
        //first load the default settings from the default <template>.xml file
		$paramsdata = "";
		$arrStandardSettings = array();
        
        $xmlfile = JPATH_COMPONENT_SITE.DS.'settings'.DS.'default'.DS.$template.'.xml';
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {

            
        $my_text = ' template -> '.$template.'<br>';    
        $my_text .= ' projectid -> '.self::$projectid.'<br>';
        $my_text .= ' xmlfile -> '.$xmlfile.'<br>';
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);

        
            
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' template<br><pre>'.print_r($template,true).'</pre>'),'');
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' projectid<br><pre>'.print_r(self::$projectid,true).'</pre>'),'');
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' xmlfile<br><pre>'.print_r($xmlfile,true).'</pre>'),'');
        }
       

		if( self::$projectid == 0) return $arrStandardSettings;

$query->select('t.params');
$query->from('#__sportsmanagement_template_config AS t');
$query->join('INNER','#__sportsmanagement_project AS p ON p.id = t.project_id');
$query->where('t.template LIKE '.$db->Quote($template));
$query->where('p.id = '.(int)self::$projectid);

$starttime = microtime(); 
		$db->setQuery($query);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'');
        
       
        if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
		if (! $result = $db->loadResult())
		{
			$project = self::getProject($cfg_which_database,__METHOD__);
            
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' project<br><pre>'.print_r($project,true).'</pre>'),'');
            
			if ( !empty($project) && $project->master_template > 0 )
			{
			 $query->clear();
				$query->select('t.params');
                $query->from('#__sportsmanagement_template_config AS t');
                $query->join('INNER','#__sportsmanagement_project AS p ON p.id = t.project_id');
                $query->where('t.template LIKE '.$db->Quote($template));
                $query->where('p.id = '.$project->master_template);

                $starttime = microtime(); 
				$db->setQuery($query);
                
                //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'');
                
                if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $my_text = 'query <br><pre>'.print_r($query->dump(),true).'</pre>'; 
            //$my_text .= 'settings <br><pre>'.print_r($settings,true).'</pre>';    
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
                //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'');
                }
                
                if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
				if (! $result = $db->loadResult())
				{
					JError::raiseNotice(500,JText::_('COM_SPORTSMANAGEMENT_MASTER_TEMPLATE_MISSING')." ".$template);
					JError::raiseNotice(500,JText::_('COM_SPORTSMANAGEMENT_MASTER_TEMPLATE_MISSING_PID'). $project->master_template);
					JError::raiseNotice(500,JText::_('COM_SPORTSMANAGEMENT_TEMPLATE_MISSING_HINT'));
                    
                    if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $my_text = 'arrStandardSettings <br><pre>'.print_r($arrStandardSettings,true).'</pre>'; 
            //$my_text .= 'settings <br><pre>'.print_r($settings,true).'</pre>';    
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
                    //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' arrStandardSettings<br><pre>'.print_r($arrStandardSettings,true).'</pre>'),'');
                    }
                    
					return $arrStandardSettings;
				}
			}
			else
			{
				//JError::raiseNotice(500,'project ' . $this->projectid . '  setting not found');
				//there are no saved settings found, use the standard xml file default values
                
                if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $my_text = 'arrStandardSettings <br><pre>'.print_r($arrStandardSettings,true).'</pre>'; 
            //$my_text .= 'settings <br><pre>'.print_r($settings,true).'</pre>';    
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
                //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' arrStandardSettings<br><pre>'.print_r($arrStandardSettings,true).'</pre>'),'');
                }
                
				return $arrStandardSettings;
			}
		}
		
        $jRegistry = new JRegistry;
		//$jRegistry->loadString($result, 'ini');
        if(version_compare(JVERSION,'3.0.0','ge')) 
        {
        $jRegistry->loadString($result); 
        }
        else
        {
        $jRegistry->loadJSON($result);
        }
        
/*        
        $extended = JForm::getInstance('extended', $xmlfile,
				array('control'=> 'extended'),
				false, '/config');
		$extended->bind($jRegistry);
*/        
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' jRegistry<br><pre>'.print_r($jRegistry,true).'</pre>'),'');
        
		$configvalues = $jRegistry->toArray(); 

		//merge and overwrite standard settings with individual view settings
		$settings = array_merge($arrStandardSettings,$configvalues);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' settings<br><pre>'.print_r($settings,true).'</pre>'),'');
        
        if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
        {
            $my_text = 'query <br><pre>'.print_r($query->dump(),true).'</pre>'; 
            $my_text .= 'settings <br><pre>'.print_r($settings,true).'</pre>';    
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['class'] = __CLASS__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['zeile'] = __LINE__;
//        sportsmanagementHelper::$_success_text[__METHOD__][__FUNCTION__]['text'] = $my_text;
        
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' settings<br><pre>'.print_r($settings,true).'</pre>'),'');
        }
        
        return $settings;
		
		//return $extended;
	}

	/**
	 * sportsmanagementModelProject::getOverallConfig()
	 * 
	 * @return
	 */
	public static function getOverallConfig($cfg_which_database = 0)
	{
		return self::getTemplateConfig('overall',$cfg_which_database,__METHOD__);
	}

	
	/**
	 * sportsmanagementModelProject::getMapConfig()
	 * 
	 * @param integer $cfg_which_database
	 * @return
	 */
	function getMapConfig($cfg_which_database = 0)
	{
		return self::getTemplateConfig('map',$cfg_which_database,__METHOD__);
	}

  	/**
   	* @author diddipoeler 
   	* @since  2011-11-12
   	* @return country from project-league
   	*/
   	function getProjectCountry($cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        $query->select('l.country');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_league as l');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project as pro ON pro.league_id = l.id ');
        $query->where('pro.id = '. (int)self::$projectid );

		  $db->setQuery( $query );
		  $this->country = $db->loadResult();
		  return $this->country;
  	} 
        
	/**
	 * return events assigned to the project
	 * @param int position_id if specified,returns only events assigned to this position
	 * @return array
	 */
	public static function getProjectEvents($position_id=0,$cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        $query->select('et.id,et.name,et.icon');
        $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_eventtype AS et');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_position_eventtype AS pet ON pet.eventtype_id = et.id ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_position AS ppos ON ppos.position_id = pet.position_id ');
        $query->where('ppos.project_id = '. (int)self::$projectid );

		if ($position_id)
		{
		  $query->where('ppos.position_id = '. (int)$position_id );
		}
        $query->group('et.id');
		$db->setQuery($query);
		$events = $db->loadObjectList('id');
		return $events;
	}

	/**
	 * returns stats assigned to positions assigned to project
	 * @param int statid 0 for all stats
	 * @param int positionid 0 for all positions
	 * @return array objects
	 */
	public static function getProjectStats($statid=0,$positionid=0,$cfg_which_database = 0)
	{
	  $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' _stats<br><pre>'.print_r(self::$_stats,true).'</pre>'),'');

		if (empty(self::$_stats))
		{
			require_once (JPATH_ADMINISTRATOR.DS.JSM_PATH.DS.'statistics'.DS.'base.php');
			$project = self::getProject($cfg_which_database,__METHOD__);
			$project_id = $project->id;
            $query->select('ppos.id as pposid,ppos.position_id AS position_id');
            $query->select('stat.id,stat.name,stat.short,stat.class,stat.icon,stat.calculated,stat.params, stat.baseparams');
            $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_statistic AS stat');
            $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_position_statistic AS ps ON ps.statistic_id = stat.id ');
            $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_position AS ppos ON ppos.position_id = ps.position_id
						  AND ppos.project_id='.$project_id);
            $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_position AS pos ON pos.id = ps.position_id');
            
            if ( $statid )
            {
                $query->where('stat.id = '.(int)$statid);
            }
            $query->where('stat.published = 1');
            $query->where('pos.published = 1');
            $query->order('pos.ordering,ps.ordering');

			$db->setQuery($query);
			self::$_stats = $db->loadObjectList();

		}
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' _stats<br><pre>'.print_r(self::$_stats,true).'</pre>'),'');
        
		// sort into positions
		$positions = self::getProjectPositions($cfg_which_database);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' positions<br><pre>'.print_r($positions,true).'</pre>'),'');
        
		$stats = array();
		
        // init
//		foreach ($positions as $pos)
//		{
//			$stats[$pos->id] = array();
//		}
        
		if (count(self::$_stats) > 0)
		{
			foreach (self::$_stats as $k => $row)
			{
				if (!$statid || $statid == $row->id || (is_array($statid) && in_array($row->id, $statid)))
				{
					if ( !isset($stats[$row->position_id]) )
                    {
                    $stats[$row->position_id] = array();    
                    }
                    
                    $stat = SMStatistic::getInstance($row->class);
					$stat->bind($row);
					$stat->set('position_id',$row->position_id);
					$stats[$row->position_id][$row->id] = $stat;
				}
			}
			if ($positionid)
			{
				return (isset($stats[$positionid]) ? $stats[$positionid] : array());
			}
			else
			{
			 //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' stats<br><pre>'.print_r($stats,true).'</pre>'),'');
				return $stats;
			}
		}
		else
		{
		  //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' stats<br><pre>'.print_r($stats,true).'</pre>'),'');
			return $stats;
		}
	}

	/**
	 * sportsmanagementModelProject::getProjectPositions()
	 * 
	 * @return
	 */
	public static function getProjectPositions($cfg_which_database = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);

		if (empty(self::$_positions))
		{
		  $query->select('pos.id,pos.persontype,pos.name,pos.ordering,pos.published');
          $query->select('ppos.id AS pposid');
          $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_position AS ppos');
          $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_position AS pos ON ppos.position_id = pos.id');
          $query->where('ppos.project_id = '.(int)self::$projectid );

			$db->setQuery($query);
			self::$_positions = $db->loadObjectList('id');
		}
		return self::$_positions;
	}

	
	/**
	 * sportsmanagementModelProject::getClubIconHtml()
	 * 
	 * @param mixed $team
	 * @param integer $type
	 * @param integer $with_space
	 * @param string $club_icon
	 * @param integer $cfg_which_database
	 * @param integer $roundcode
	 * @return
	 */
	public static function getClubIconHtml(&$team,$type=1,$with_space=0,$club_icon='logo_small',$cfg_which_database = 0,$roundcode = 0)
	{
	   $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
    
//    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' team<br><pre>'.print_r($team,true).'</pre>'),'');
//    $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' club_icon<br><pre>'.print_r($club_icon,true).'</pre>'),'');
    
		if ( $type == 1 )
		{
           
           if ( !sportsmanagementHelper::existPicture($team->$club_icon) )
    {
    $team->$club_icon = sportsmanagementHelper::getDefaultPlaceholder($club_icon);    
    }

$image = sportsmanagementHelperHtml::getBootstrapModalImage($roundcode.'team'.$team->team_id,$team->$club_icon,$team->name,'20');

            return $image;
		}
		elseif (($type==2) && (isset($team->country)))
		{
			return JSMCountries::getCountryFlag($team->country);
		}
	}

	/**
	 * Method to store the item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data,$table='',$cfg_which_database = 0)
	{
		if ($table=='')
		{
			$row =& $this->getTable();
		}
		else
		{
			$row =& JTable::getInstance($table,'Table');
		}

		// Bind the form fields to the items table
		if (!$row->bind($data))
		{
			$this->setError(JText::_('Binding failed'));
			return false;
		}

		// Create the timestamp for the date
		$row->checked_out_time=gmdate('Y-m-d H:i:s');

		// if new item,order last,but only if an ordering exist
		if ((isset($row->id)) && (isset($row->ordering)))
		{
			if (!$row->id && $row->ordering != NULL)
			{
				$row->ordering=$row->getNextOrder();
			}
		}

		// Make sure the item is valid
		if (!$row->check())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the item to the database
		if (!$row->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $row->id;
	}

	/**
	 * sportsmanagementModelProject::isUserProjectAdminOrEditor()
	 * 
	 * @param integer $userId
	 * @param mixed $project
	 * @return
	 */
	public static function isUserProjectAdminOrEditor($userId=0, $project,$cfg_which_database = 0)
	{
		$result = false;
		if($userId > 0)
		{
			$result = ( $userId==$project->admin || $userId==$project->editor );
		}
		return $result;
	}

	/**
	 * returns match substitutions
	 * @param int match id
	 * @return array
	 */
	public static function getMatchSubstitutions($match_id,$cfg_which_database = 0)
	{
	  $option = JRequest::getCmd('option');
	$app = JFactory::getApplication();
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' match_id'.'<pre>'.print_r($match_id,true).'</pre>' ),'');  
        
        // Select some fields
        $query->select('mp.in_out_time,mp.teamplayer_id,mp.in_for');
        $query->select('pt.team_id,pt.id AS ptid');
        $query->select('tp1.person_id,tp1.jerseynumber');
        $query->select('tp2.person_id AS out_person_id');
        $query->select('p2.id AS out_ptid,p2.firstname AS out_firstname,p2.nickname AS out_nickname,p2.lastname AS out_lastname');
        $query->select('p.firstname,p.nickname,p.lastname,p.id AS playerid');
        $query->select('pos.name AS in_position');
        $query->select('ppos.id AS pposid1');
        $query->select('pos2.name AS out_position');
        $query->select('ppos2.id AS pposid2');
        $query->select('CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug');
        $query->select('CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug');
        
        // From 
		$query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_player AS mp');
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp1 ON tp1.id = mp.teamplayer_id');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id AS st1 ON st1.team_id = tp1.team_id ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ON pt.team_id = st1.id');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_team AS t ON t.id = st1.team_id');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_person AS p ON tp1.person_id = p.id');
        
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp2 ON tp2.id = mp.in_for');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_person AS p2 ON tp2.person_id = p2.id');
        
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_person_project_position AS ppp1 on ppp1.person_id = tp1.person_id and ppp1.persontype = tp1.persontype');
        
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_position AS ppos ON ppos.id = ppp1.project_position_id');
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_position AS pos ON ppos.position_id = pos.id');
        
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_player AS mp2 ON mp.match_id = mp2.match_id AND mp.in_for = mp2.teamplayer_id');
        
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_person_project_position AS ppp2 on ppp2.person_id = tp2.person_id and ppp2.persontype = tp2.persontype');
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_position AS ppos2 ON ppos2.id = ppp2.project_position_id');
        $query->join('LEFT','#__'.COM_SPORTSMANAGEMENT_TABLE.'_position AS pos2 ON ppos2.position_id = pos2.id');
   
        // Where
        $query->where('ppp1.project_id = '.(int)self::$projectid);
        $query->where('ppp2.project_id = '.(int)self::$projectid);
        
        $query->where('mp.match_id = '.(int)$match_id);
        $query->where('mp.came_in > 0');
        $query->where('p.published = 1');
        $query->where('p2.published = 1');
        // group
        $query->group('mp.in_out_time, mp.teamplayer_id, pt.team_id');
        // order
        $query->order('(mp.in_out_time+0)');                
                    
		$db->setQuery($query);
		//echo($this->_db->getQuery());
		$result = $db->loadObjectList();
        
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
 
        if ( !$result )
	    {
	       if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
       {
        $my_text = 'getErrorMsg -><pre>'.print_r($db->getErrorMsg(),true).'</pre>';
          $my_text .= 'dump -><pre>'.print_r($query->dump(),true).'</pre>';  
          sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
		//$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' '.'<pre>'.print_r($db->getErrorMsg(),true).'</pre>' ),'Error');
        }
	    }
        
		return $result;
	}

	
    /**
     * sportsmanagementModelProject::getMatch()
     * 
     * @return
     */
    public static function getMatch()
	{
	   // Reference global application object
        $app = JFactory::getApplication();
        // JInput object
        $jinput = $app->input;
        // Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
        $option = $jinput->getCmd('option');
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, self::$cfg_which_database );
        $query = $db->getQuery(true);
        
		if (is_null(self::$_match))
		{
		  $query->select('m.*,DATE_FORMAT(m.time_present,"%H:%i") time_present, r.project_id, p.timezone ');
          $query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_match AS m ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_round AS r on r.id = m.round_id ');
        $query->join('INNER','#__'.COM_SPORTSMANAGEMENT_TABLE.'_project AS p on r.project_id = p.id ');
        $query->where('m.id = '.(int)self::$matchid );
        
//			$query='SELECT m.*,DATE_FORMAT(m.time_present,"%H:%i") time_present, r.project_id, p.timezone 
//					FROM #__joomleague_match AS m 
//					INNER JOIN #__joomleague_round AS r on r.id=m.round_id 
//					INNER JOIN #__joomleague_project AS p on r.project_id=p.id 
//					WHERE m.id='. $db->Quote(self::$matchid)
//			      ;
			
            if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
       {
            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
            }
            
            $db->setQuery($query,0,1);
			self::$_match = $db->loadObject();
			if (self::$_match)
			{
				sportsmanagementHelper::convertMatchDateToTimezone(self::$_match);
			}
		}
		return self::$_match;
	}
    
    /**
	 * returns match events
	 * @param int match id
	 * @return array
	 */
	public static function getMatchEvents($match_id,$showcomments=0,$sortdesc=0,$cfg_which_database = 0)
	{
		// Reference global application object
        $app = JFactory::getApplication();
        // JInput object
        $jinput = $app->input;
        // Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
        $option = $jinput->getCmd('option');
        // Get a db connection.
        $db = sportsmanagementHelper::getDBConnection(TRUE, $cfg_which_database );
        $query = $db->getQuery(true);
        $starttime = microtime(); 
        
        if ($showcomments == 1) 
        {
		    $join = 'LEFT';
		    //$addline = ' me.notes,';
            $query->select('me.notes');
		} 
        else 
        {
		    $join = 'INNER';
		    //$addline = '';
		}
		$esort = '';
        $arrayobjectsort = '1';
		if ($sortdesc == 1) 
        {
		    $esort = ' DESC';
            $arrayobjectsort = '-1';
		}
        // Select some fields
        $query->select('me.event_type_id,me.id as event_id,me.event_time,me.notice,me.projectteam_id AS ptid,me.event_sum');
        $query->select('pt.team_id AS team_id');
        $query->select('et.name AS eventtype_name');
        $query->select('t.name AS team_name');
        $query->select('tp.picture AS tppicture1');
        $query->select('p.id AS playerid,p.firstname AS firstname1,p.nickname AS nickname1,p.lastname AS lastname1,p.picture AS picture1');
        // From 
		$query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_event AS me');
        $query->join($join,'#__'.COM_SPORTSMANAGEMENT_TABLE.'_eventtype AS et ON me.event_type_id = et.id');
        $query->join($join,'#__'.COM_SPORTSMANAGEMENT_TABLE.'_project_team AS pt ON me.projectteam_id = pt.id');
        $query->join($join,'#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_id AS st ON st.id = pt.team_id');
        $query->join($join,'#__'.COM_SPORTSMANAGEMENT_TABLE.'_team AS t ON st.team_id = t.id');
        $query->join($join,'#__'.COM_SPORTSMANAGEMENT_TABLE.'_season_team_person_id AS tp ON tp.team_id = st.team_id AND tp.id = me.teamplayer_id');
        $query->join($join,'#__'.COM_SPORTSMANAGEMENT_TABLE.'_person AS p ON tp.person_id = p.id');

		// Where
        $query->where('me.match_id = '.(int)$match_id );
        $query->where('p.published = 1');
        // order
        $query->order('(me.event_time + 0)'. $esort .', me.event_type_id, me.id');
        	
		$db->setQuery( $query );
        
        if ( COM_SPORTSMANAGEMENT_SHOW_QUERY_DEBUG_INFO )
        {
            $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
		
        $events = $db->loadObjectList();
        
        if ( !$events )
	    {
	       if ( COM_SPORTSMANAGEMENT_SHOW_DEBUG_INFO )
       {
        $my_text = ' <br><pre>'.print_r($db->getErrorMsg(),true).'</pre>';    
        //sportsmanagementHelper::$_success_text[__METHOD__][__LINE__] = $my_text;
        sportsmanagementHelper::setDebugInfoText(__METHOD__,__FUNCTION__,__CLASS__,__LINE__,$my_text);
        
		//$app->enqueueMessage(JText::_(get_class($this).' '.__FUNCTION__.' '.'<pre>'.print_r($db->getErrorMsg(),true).'</pre>' ),'Error');
        }
	    }
        
    $query = $db->getQuery(true);    
    $query->clear();
    // Select some fields
        $query->select('*');
        // From 
		$query->from('#__'.COM_SPORTSMANAGEMENT_TABLE.'_match_commentary');
        // Where
        //$query->where('match_id = '. (int)$this->matchid );
        $query->where('match_id = '. (int)$match_id );
    
    $db->setQuery($query);
		$commentary = $db->loadObjectList();
        if ( $commentary )
        {
            foreach ( $commentary as $comment )
            {
                $temp = new stdClass();
                $temp->event_type_id = 0;
                $temp->event_sum = $comment->type;
                $temp->event_time = $comment->event_time;
                $temp->notes = $comment->notes;
                $events[] = $temp;
            }
        }
        $events = JArrayHelper::sortObjects($events,'event_time',$arrayobjectsort);
        return $events;
	}
	
	/**
	 * sportsmanagementModelProject::hasEditPermission()
	 * 
	 * @param mixed $task
	 * @return
	 */
	public static function hasEditPermission($task=null,$cfg_which_database = 0)
	{
		$option = JRequest::getCmd('option');
        $app = JFactory::getApplication();
        $allowed = false;
		$user = JFactory::getUser();
        
        // ist der user der einer gruppe zugeordnet ?
        $groups = JUserHelper::getUserGroups($user->get('id')); 
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' groups'.'<pre>'.print_r($groups,true).'</pre>' ),'');
        
		if($user->id > 0) 
        {
			if(!is_null($task)) 
            {
				if (!$user->authorise($task, $option)) 
                {
					$allowed = false;
					error_log(JText::sprintf('COM_SPORTSMANAGEMENT_CLUBINFO_PAGE_ERROR_ACL_PERMISSION',$task));
				} 
                else 
                {
					$allowed = true;
				}
			}
            
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' '.'<pre>'.print_r($allowed,true).'</pre>' ),'');
            
			//if no ACL permission, check for overruling permission by project admin/editor (compatibility < 2.5)
			if(!$allowed) 
            {
				// If not, then check if user is project admin or editor
				$project = self::getProject($cfg_which_database,__METHOD__);
				if(self::isUserProjectAdminOrEditor($user->id, $project))
				{
					$allowed = true;
				} 
                else 
                {
					error_log(Jtext::_('COM_SPORTSMANAGEMENT_CLUBINFO_PAGE_ERROR_ADMIN_EDITOR'));
                    $app->enqueueMessage(JText::_('COM_SPORTSMANAGEMENT_CLUBINFO_PAGE_ERROR_ADMIN_EDITOR'),'Error');
				}
			}
            
            //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' '.'<pre>'.print_r($allowed,true).'</pre>' ),'');
            
		}
		return $allowed;
	}
}
?>