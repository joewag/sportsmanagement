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

defined( '_JEXEC' ) or die( 'Restricted access' );
$option = JRequest::getCmd('option');
$templatesToLoad = array('footer','listheader');
sportsmanagementHelper::addTemplatePaths($templatesToLoad, $this);
JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.modal' );

//$url = JPATH_ADMINISTRATOR . DS. 'components'.DS.$option. DS.'assets'.DS.'icons'.DS.'dfbnet-logo.gif';
$url = 'administrator'.DS.'components'.DS.$option. DS.'assets'.DS.'icons'.DS.'sislogo.png';
//$url16 = 'components/com_joomleague/extensions/jlextdfbnetplayerimport/admin/assets/images/dfbnet-logo-16.gif';
$alt = 'DFBNet';

$attribs['width'] = '170px';
$attribs['height'] = '26px';
$attribs['align'] = 'left';
//$logo = JHtml::_('image', $url, $alt, $attribs);

// Set toolbar items for the page
//$doc = JFactory::getDocument();
//$style = " .icon-48-fb {components/com_joomleague/extensions/jlextdfbnetplayerimport/admin/assets/images/dfbnet-logo-16.gif); no-repeat; }";
//$doc->addStyleDeclaration( $style );

//JToolBarHelper::title( JText::_( 'COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT' ), 'generic.png' );

//JToolBarHelper::title(   JText::_( 'COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT' ), $url16 );

//JToolBarHelper::save();
//JToolBarHelper::apply();


/*
echo 'default project <br>';
echo '<pre>';
print_r($url);
echo '</pre>';
*/

/*
echo 'default projectteams <br>';
echo '<pre>';
print_r($this->projectteams);
echo '</pre>';
*/

?>

<div id="editcell">
	<form enctype='multipart/form-data' method='post' name="adminForm">
		<table class='adminlist'>
			<thead>
			  <tr>
			    <th>
			      <?php echo JHtml::_('image', $url, $alt, $attribs);; ?>
			      <?php echo JText::sprintf('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_TABLE_TITLE_1',$this->config->get('upload_maxsize')); ?>
			    </th>
			  </tr>
			</thead>
			<tfoot>
			  <tr>
			    <td>
				<?php
				echo '<br />';
				echo '<b>'.JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_EXTENTION_INFO').'</b><br />';
				echo JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_HINT1').'<br />';
				echo JText::sprintf('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_HINT2',$this->revisionDate);
				/*
				$linkParams=array();
				$linkParams['target']='_blank';
				$linkURL='http://forum.joomleague.net/viewtopic.php?f=13&t=10985#p51461';
				$link=JRoute::_($linkURL);
				$linkParams['title']=JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_TOPIC_FORUM');
				$forumLink=JHtml::link($link,$linkURL,$linkParams);
				$linkURL='http://bugtracker.joomleague.net/issues/226';
				$link=JRoute::_($linkURL);
				$linkParams['title']=JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_TOPIC_BUGTRACKER');
				$bugtrackerLink=JHtml::link($link,$linkURL,$linkParams);
				echo '<p>'.JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_HINT3').'</p>';
				echo "<p>$forumLink</p>";
				echo "<p>$bugtrackerLink</p>";
				*/
				?>
			    </td>
			  </tr>
			</tfoot>
			<tbody>
      <?php
      // TODO: Check update functionality in later version of that extension. For now, disabled
      if ( 0 )
      {
      ?>
      <tr>
      <td>
      <fieldset>
      <legend>
				<?php
				echo JText::_( 'COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_SELECT_USE_PROJECT');
				?>
			</legend>      
      <input class='input_box' type='checkbox' id='dfbimportupdate' name='dfbimportupdate'  /><?php echo JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_USE_PROJECT'); ?>      
      </fieldset>
      </td>
      </tr>
      <?php
      }
      ?>
      

      <?php
      
      ?>
      
      
      <tr>
      <td>
      <fieldset>
            <legend>
				<?php
				echo JText::_( 'COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_FILE' );
				?>
			</legend>

			
                <input type="text" name='liganummer' value='' size="100" />
				<input class="button" type="submit" onclick="return Joomla.submitform('jlextsisimport.save')" value="<?php echo JText::_('COM_SPORTSMANAGEMENT_ADMIN_SIS_IMPORT_UPLOAD_BUTTON'); ?>" />
			</fieldset>
      </td>
      </tr>
      </tbody>
		</table>
		<input type="hidden" name='sent' value='1' />
		<input type="hidden" name='MAX_FILE_SIZE' value='<?php echo $this->config->get('upload_maxsize'); ?>' />
		<input type="hidden" name="option" value="com_sportsmanagement" /> 
		<input type="hidden" name='task' value='jlextsisimport.save' />
		<?php echo JHtml::_('form.token')."\n"; ?>
	</form>
</div>
<?PHP
echo "<div>";
echo $this->loadTemplate('footer');
echo "</div>";
?>   