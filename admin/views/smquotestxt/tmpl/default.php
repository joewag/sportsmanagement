<?php 
defined('_JEXEC') or die('Restricted access');

$templatesToLoad = array('footer');
sportsmanagementHelper::addTemplatePaths($templatesToLoad, $this);
?>
<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SPORTSMANAGEMENT_ADMIN_EXT_TXT'); ?></legend>
<table>
<?PHP
foreach ( $this->files as $file )
{


			$link = JRoute::_('index.php?option=com_sportsmanagement&view=smquotetxt&layout=default&file_name='.$file);
			?>
			<tr class="">
				<td class="center"></td>
				<?php
					
                    ?>
                    <td class="center" nowrap="nowrap">
								<a	href="<?php echo $link; ?>" >
                                    <?php
									$imageTitle=JText::_('COM_SPORTSMANAGEMENT_ADMIN_EXT_TXT_EDIT');
									echo JHtml::_(	'image','administrator/components/com_sportsmanagement/assets/images/edit.png',
													$imageTitle,'title= "'.$imageTitle.'"');
									?>
                    </a>                 
					</td>
				<td>
                <?php
					
					echo $file;
					
					?>
     </td>
	
			</tr>
			<?php

    
}    

?>
</table>
</fieldset>
<?PHP
echo "<div>";
echo $this->loadTemplate('footer');
echo "</div>";
?>  