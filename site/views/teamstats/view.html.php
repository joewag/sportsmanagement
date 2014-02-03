<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class sportsmanagementViewTeamStats extends JView
{
	function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document= JFactory::getDocument();

		$model = $this->getModel();
		$config = sportsmanagementModelProject::getTemplateConfig($this->getName());

		$tableconfig = sportsmanagementModelProject::getTemplateConfig( "ranking" );
		$eventsconfig = sportsmanagementModelProject::getTemplateConfig( "eventsranking" );
		$flashconfig = sportsmanagementModelProject::getTemplateConfig( "flash" );

		$this->assign( 'project', sportsmanagementModelProject::getProject() );
		if ( isset( $this->project ) )
		{
			$this->assign( 'overallconfig', sportsmanagementModelProject::getOverallConfig() );
			if ( !isset( $this->overallconfig['seperator'] ) )
			{
				$this->overallconfig['seperator'] = ":";
			}
			$this->assignRef('config', $config );

			$this->assignRef('tableconfig', $tableconfig );
			$this->assignRef('eventsconfig', $eventsconfig );
			$this->assign('actualround', sportsmanagementModelProject::getCurrentRound() );
			$this->assign('team', $model->getTeam() );
			
            $this->assign('highest_home', $model->getHighest('HOME') );
			$this->assign('highest_away', $model->getHighest('AWAY') );
            
			//$this->assign('highestdef_home', $model->getHighestDefHome( ) );
			//$this->assign('highestdef_away', $model->getHighestDefAway( ) );
            $this->assign('highestdef_home', $model->getHighestDef('HOME') );
			$this->assign('highestdef_away', $model->getHighestDef('AWAY') );
            
            $this->assign('highestdraw_home', $model->getHighestDraw('HOME') );
			$this->assign('highestdraw_away', $model->getHighestDraw('AWAY') );
			
            //$this->assign('totalshome', $model->getSeasonTotalsHome( ) );
			//$this->assign('totalsaway', $model->getSeasonTotalsAway( ) );
			$this->assign('totalshome', $model->getSeasonTotals('HOME') );
            $this->assign('totalsaway', $model->getSeasonTotals('AWAY') );
            
            
            
            $this->assign('matchdaytotals', $model->getMatchDayTotals( ) );
			$this->assign('totalrounds', $model->getTotalRounds( ) );
			$this->assign('totalattendance', $model->getTotalAttendance() );
			$this->assign('bestattendance', $model->getBestAttendance() );
			$this->assign('worstattendance', $model->getWorstAttendance() );
			$this->assign('averageattendance', $model->getAverageAttendance() );
			$this->assign('chart_url', $model->getChartURL( ) );
			$this->assign('nogoals_against', $model->getNoGoalsAgainst( ) );
			$this->assign('logo', $model->getLogo( ) );
			$this->assign('results',  $model->getResults());

			$this->_setChartdata(array_merge($flashconfig, $config));
		}
		
		//$this->assign('show_debug_info', JComponentHelper::getParams('com_sportsmanagement')->get('show_debug_info',0) );
		
		// Set page title
		$pageTitle = JText::_( 'COM_SPORTSMANAGEMENT_TEAMSTATS_PAGE_TITLE' );
		if ( isset( $this->team ) )
		{
			$pageTitle .= ': ' . $this->team->name;
		}
		$document->setTitle( $pageTitle );

		parent::display( $tpl );
	}

	/**
	 * assign the chartdata object for open flash chart library
	 * @param $config
	 * @return unknown_type
	 */
	function _setChartdata($config)
	{
		require_once( JPATH_COMPONENT_SITE.DS."assets".DS."classes".DS."open-flash-chart".DS."open-flash-chart.php" );

		$data = $this->get('ChartData');

		// Calculate Values for Chart Object
		$forSum = array();
		$againstSum = array();
		$matchDayGoalsCount = array();
		$matchDayGoalsCount[] = 0;
		$round_labels = array();

		$matchDayGoalsCountMax = 0;
		foreach( $data as $rw )
		{
			if (!$rw->goalsfor) $rw->goalsfor = 0;
			if (!$rw->goalsagainst) $rw->goalsagainst = 0;
			$forSum[]     = intval($rw->goalsfor);
			$againstSum[] = intval($rw->goalsagainst);

			// check, if both results are missing and avoid drawing the flatline of "0" goals for not played games yet
			if ((!$rw->goalsfor) && (!$rw->goalsagainst))
			{
				$matchDayGoalsCount[] = 0;
			}
			else
			{
				$matchDayGoalsCount[] = intval($rw->goalsfor + $rw->goalsagainst);
			}
			$round_labels[] = $rw->roundcode;
		}
		
		$chart = new open_flash_chart();
		//$chart->set_title( $title );
		$chart->set_bg_colour($config['bg_colour']);

		$barfor = new $config['bartype_1']();
		$barfor->set_values( $forSum );
		$barfor->set_tooltip( JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_GOALS_FOR'). ": #val#" );
		$barfor->set_colour( $config['bar1'] );
		$barfor->set_on_show(new bar_on_show($config['animation_1'], $config['cascade_1'], $config['delay_1']));
		$barfor->set_key(JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_GOALS_FOR'), 12);

		$baragainst = new $config['bartype_2']();
		$baragainst->set_values( $againstSum );
		$baragainst->set_tooltip(   JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_GOALS_AGAINST'). ": #val#" );
		$baragainst->set_colour( $config['bar2'] );
		$baragainst->set_on_show(new bar_on_show($config['animation_2'], $config['cascade_2'], $config['delay_2']));
		$baragainst->set_key(JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_GOALS_AGAINST'), 12);

		$chart->add_element($barfor);
		$chart->add_element($baragainst);

		// total
		$d = new $config['dotstyle_3']();
		$d->size((int)$config['line3_dot_strength']);
		$d->halo_size(1);
		$d->colour($config['line3']);
		$d->tooltip(JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_TOTAL2').' #val#');

		$line = new line();
		$line->set_default_dot_style($d);
		$line->set_values(array_slice( $matchDayGoalsCount,1) );
		$line->set_width( (int) $config['line3_strength'] );
		$line->set_key(JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_TOTAL'), 12);
		$line->set_colour( $config['line3'] );
		$line->on_show(new line_on_show($config['l_animation_3'], $config['l_cascade_3'], $config['l_delay_3']));
		$chart->add_element($line);		
		
		$x = new x_axis();
		$x->set_colours($config['x_axis_colour'], $config['x_axis_colour_inner']);
		$x->set_labels_from_array($round_labels);
		$chart->set_x_axis( $x );
		$x_legend = new x_legend( JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_ROUNDS') );
		$x_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_x_legend( $x_legend );

		$y = new y_axis();
		$y->set_range( 0, max($matchDayGoalsCount)+2, $config['y_axis_steps']);
		$y->set_colours($config['y_axis_colour'], $config['y_axis_colour_inner']);
		$chart->set_y_axis( $y );
		$y_legend = new y_legend( JText::_('COM_SPORTSMANAGEMENT_TEAMSTATS_GOALS') );
		$y_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_y_legend( $y_legend );

		$this->assignRef( 'chartdata',  $chart);
	}
}
?>