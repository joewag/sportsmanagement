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

//echo 'project_id<pre>'.print_r($this->model->predictionProject->project_id, true).'</pre><br>';

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('globalviews','predictionheading');
sportsmanagementHelper::addTemplatePaths($templatesToLoad, $this);

?>
<div class="<?php echo COM_SPORTSMANAGEMENT_BOOTSTRAP_DIV_CLASS; ?>">
<?php

	echo $this->loadTemplate('predictionheading');
	echo $this->loadTemplate('sectionheader');

	if ( ( !isset($this->actJoomlaUser) ) || ( $this->actJoomlaUser->id == 0 ) )
	{
		echo $this->loadTemplate('view_deny');
	}
	else
	{
		if ( ( !$this->isPredictionMember ) && ( !$this->allowedAdmin ) )
		{
			echo $this->loadTemplate('view_not_member');
		}
		else
		{
			if ($this->isNewMember)
            {
                echo $this->loadTemplate('view_welcome');
                }

			if (!$this->tippEntryDone)
			{
				if (($this->config['show_help']==0)||($this->config['show_help']==2))
                {
                    echo $this->model->createHelptText($predictionProject->mode);
                }
                echo $this->loadTemplate('view_tippentry_do');
                //echo $this->loadTemplate('matchday_nav');
            if (($this->config['show_help']==1)||($this->config['show_help']==2))
			{
				echo $this->model->createHelptText($predictionProject->mode);
			}
            
			}
			else
			{
				echo $this->loadTemplate('view_tippentry_done');
			}
		}
	}
  
//  echo $this->loadTemplate('matchday_nav');
	
?>
<div>
<?PHP
//backbutton
echo $this->loadTemplate('backbutton');
// footer
echo $this->loadTemplate('footer');
?>
</div>
<?PHP

?>

</div>