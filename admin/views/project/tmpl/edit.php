<?php
/** SportsManagement ein Programm zur Verwaltung für alle Sportarten
* @version         1.0.05
* @file                agegroup.php
* @author                diddipoeler, stony, svdoldie und donclumsy (diddipoeler@arcor.de)
* @copyright        Copyright: © 2013 Fussball in Europa http://fussballineuropa.de/ All rights reserved.
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
* SportsManagement ist Freie Software: Sie können es unter den Bedingungen
* der GNU General Public License, wie von der Free Software Foundation,
* Version 3 der Lizenz oder (nach Ihrer Wahl) jeder späteren
* veröffentlichten Version, weiterverbreiten und/oder modifizieren.
*
* SportsManagement wird in der Hoffnung, dass es nützlich sein wird, aber
* OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
* Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
* Siehe die GNU General Public License für weitere Details.
*
* Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
* Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
*
* Note : All ini files need to be saved as UTF-8 without BOM
*/ 
defined('_JEXEC') or die('Restricted access');
$templatesToLoad = array('footer');
sportsmanagementHelper::addTemplatePaths($templatesToLoad, $this);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form action="<?php echo JRoute::_('index.php?option=com_sportsmanagement&layout=edit&id='.(int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm">
<fieldset class="adminform">
			<legend><?php echo JText::sprintf('COM_SPORTSMANAGEMENT_ADMIN_PROJECT_LEGEND_DESC','<i>'.$this->item->name.'</i>'); ?></legend>
	<div class="col50">
	<?php
	echo JHtml::_('tabs.start','tabs', array('useCookie'=>1));
	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_DETAILS'), 'panel1');
	echo $this->loadTemplate('details');

	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_DATE'), 'panel2');
	echo $this->loadTemplate('date');

	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_PROJECT'), 'panel3');
	echo $this->loadTemplate('project');

	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_COMPETITION'), 'panel4');
	echo $this->loadTemplate('competition');

	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_FAVORITE'), 'panel5');
	echo $this->loadTemplate('favorite');

	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_PICTURE'), 'panel6');
	echo $this->loadTemplate('picture');

	echo JHtml::_('tabs.panel',JText::_('COM_SPORTSMANAGEMENT_TABS_EXTENDED'), 'panel7');
	echo $this->loadTemplate('extended');

	echo JHtml::_('tabs.end');
	?>
	<div class="clr"></div>
	
	<input type="hidden" name="task" value="project.edit" /> 
	<input type="hidden"name="oldseason" value="<?php echo $this->item->season_id; ?>" />
	<input type="hidden" name="oldleague" value="<?php echo $this->item->league_id; ?>" /> 
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo JHtml::_('form.token')."\n"; ?>
	</div>
	</fieldset>
</form>

<?PHP
echo "<div>";
echo $this->loadTemplate('footer');
echo "</div>";
?>   