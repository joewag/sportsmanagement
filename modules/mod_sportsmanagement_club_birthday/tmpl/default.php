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

// no direct access
defined('_JEXEC') or die('Restricted access'); 

$refresh = $params->def("refresh");
$minute = $params->def("minute");
$height = $params->def("height");
$width  = $params->def("width");


if ($refresh == 1)
{
	$birthdaytext = "<script type=\"text/javascript\" language=\"javascript\">
			var reloadTimer = null;
			window.onload = function()
			{
			    setReloadTime($minute);
		    }
			function setReloadTime(secs)
				{
				    if (arguments.length == 1) {
			        if (reloadTimer) clearTimeout(reloadTimer);
			        reloadTimer = setTimeout(\"setReloadTime()\", Math.ceil(parseFloat(secs) * 1000));
			    }
		    else {
		        location.reload();
		    }
			}
		 </script> 
<div align=\"center\"><a href=\"javascript:location.reload();\"><img src=\"modules/mod_sportsmanagement_club_birthday/css/icon_refresh.gif\" border=\"0\" title=\"Refresh\">&nbsp;&nbsp;&nbsp;&nbsp;<b>Refresh</b></a></div><br>";
}
else {$birthdaytext = "";} 
$id = 1;
$idstring = '';
$idstring = $id.$params->get( 'moduleclass_sfx' ) ;



?>

<?PHP
if(count($clubs) > 0) 
{
    
switch ($mode)
{
	// bootstrap mode template
	case 'B':

?>    
<div class="row">
        <div class="row">
           
            <div class="col-md-12">
                <!-- Controls -->
                <div class="controls pull-right hidden-xs">
                    <a class="left fa fa-chevron-left btn btn-primary" href="#carousel-<?php echo $module->module; ?>-<?php echo $module->id; ?>"
                        data-slide="prev"></a><a class="right fa fa-chevron-right btn btn-primary" href="#carousel-<?php echo $module->module; ?>-<?php echo $module->id; ?>"
                            data-slide="next"></a>
                </div>
                
            </div>
        </div>
        
        <div id="carousel-<?php echo $module->module; ?>-<?php echo $module->id; ?>" class="carousel slide hidden-xs" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
            
<?PHP
$a = 0;
foreach ($clubs AS $club) 
{
//$thispic = sportsmanagementHelper::getDefaultPlaceholder('clublogobig');                                                       
$thispic = $module->picture_server.DS.$club->picture;
$active = ($a==0) ? 'active' : '';    

$club->default_picture = sportsmanagementHelper::getDefaultPlaceholder('clublogobig');  

if ( $params->get('show_picture') ) 
{

if ( curl_init($club->picture) && $club->picture != '' ) 
{
$thispic = $club->picture;
}
elseif( curl_init($club->default_picture) && $club->default_picture != '' ) 
{
$thispic = $club->default_picture;
}

}

switch ($club->days_to_birthday) 
{
case 0: $whenmessage = $params->get('todaymessage');break;
case 1: $whenmessage = $params->get('tomorrowmessage');break;
default: $whenmessage = str_replace('%DAYS_TO%', $club->days_to_birthday, trim($futuremessage));break;
}
        
if ( $club->founded != '0000-00-00' )
{
$birthdaytext2 = htmlentities(trim(JText::_($params->get('birthdaytext'))), ENT_COMPAT , 'UTF-8');
$dayformat = htmlentities(trim($params->get('dayformat')));
$birthdayformat = htmlentities(trim($params->get('birthdayformat')));
$birthdaytext2 = str_replace('%WHEN%', $whenmessage, $birthdaytext2);
$birthdaytext2 = str_replace('%AGE%', $club->age, $birthdaytext2);
$birthdaytext2 = str_replace('%DATE%', strftime($dayformat, strtotime($club->year.'-'.$club->daymonth)), $birthdaytext2);
$birthdaytext2 = str_replace('%DATE_OF_BIRTH%', strftime($birthdayformat, strtotime($club->date_of_birth)), $birthdaytext2);
}
else
{
$birthdaytext2 = htmlentities(trim(JText::_($params->get('birthdaytextyear'))), ENT_COMPAT , 'UTF-8');
$birthdaytext2 = str_replace('%AGE%', $club->age_year, $birthdaytext2);
}
            
$birthdaytext2 = str_replace('%BR%', '<br />', $birthdaytext2);
$birthdaytext2 = str_replace('%BOLD%', '<b>', $birthdaytext2);
$birthdaytext2 = str_replace('%BOLDEND%', '</b>', $birthdaytext2);

?>              
                <div class="item <?php echo $active; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-item">
                                <div class="photo">
                                    <img src="<?php echo $thispic; ?>" class="img-responsive" alt="<?php echo $club->name; ?>" width="60" />
                                </div>
                            </div>
                        </div>        
        </div>
        
        <div class="info">
                                    <div class="row">
                                    <div class="price col-md-6">
<h5>
	<?php 
	//echo $club->name; 
$flag = $params->get('show_club_flag')? JSMCountries::getCountryFlag($club->country) . "&nbsp;" : "";
$text = $club->name;
$usedname = $flag.$text;  
echo $usedname;   	
	?>
</h5>
                                            
                                        </div>
                                    <div class="price col-md-6">
                                            
                                            <h5 class="price-text-color"><?php echo $birthdaytext2; ?></h5>
                                        </div>    
                                    </div>
                                    </div>
        </div>  
<?PHP 
$a++;   
}
?>            
        </div>
</div>
</div>         
<?PHP
    
    
    break;
    // bootstrap mode template
	case 'B2':
?>
<div id="myClubBirthday<?php echo $module->id; ?>" class="carousel slide" data-interval="3000" data-ride="carousel">
<!-- Indicators -->
<ol class="carousel-indicators">
<?PHP
for ($a=0; $a < count($clubs);$a++)
{
$active = ($a==0) ? 'class="active"' : '';    
?>    
<li data-target="#myClubBirthday<?php echo $module->id; ?>" data-slide-to="<?php echo $a; ?>" <?php echo $active; ?> ></li></li>
<?PHP    
}
?>
</ol>

<!-- Wrapper for slides -->
<div class="carousel-inner" role="listbox">
<?PHP
$a = 0;
foreach ($clubs AS $club) 
{
$active = ($a==0) ? 'active' : '';  
$thispic = '';
$club->default_picture = sportsmanagementHelper::getDefaultPlaceholder('clublogobig');  
if ($params->get('show_picture')==1) 
{
if (file_exists(JPATH_BASE.'/'.$club->picture)&&$club->picture!='') 
{
$thispic = $club->picture;
}
elseif (file_exists(JPATH_BASE.'/'.$club->default_picture)&&$club->default_picture!='') 
{
$thispic = $club->default_picture;
}
}
switch ($club->days_to_birthday) 
{
case 0: $whenmessage = $params->get('todaymessage');break;
case 1: $whenmessage = $params->get('tomorrowmessage');break;
default: $whenmessage = str_replace('%DAYS_TO%', $club->days_to_birthday, trim($futuremessage));break;
}
        
if ( $club->founded != '0000-00-00' )
{
$birthdaytext2 = htmlentities(trim(JText::_($params->get('birthdaytext'))), ENT_COMPAT , 'UTF-8');
$dayformat = htmlentities(trim($params->get('dayformat')));
$birthdayformat = htmlentities(trim($params->get('birthdayformat')));
$birthdaytext2 = str_replace('%WHEN%', $whenmessage, $birthdaytext2);
$birthdaytext2 = str_replace('%AGE%', $club->age, $birthdaytext2);
$birthdaytext2 = str_replace('%DATE%', strftime($dayformat, strtotime($club->year.'-'.$club->daymonth)), $birthdaytext2);
$birthdaytext2 = str_replace('%DATE_OF_BIRTH%', strftime($birthdayformat, strtotime($club->date_of_birth)), $birthdaytext2);
}
else
{
$birthdaytext2 = htmlentities(trim(JText::_($params->get('birthdaytextyear'))), ENT_COMPAT , 'UTF-8');
$birthdaytext2 = str_replace('%AGE%', $club->age_year, $birthdaytext2);
}
            
$birthdaytext2 = str_replace('%BR%', '<br />', $birthdaytext2);
$birthdaytext2 = str_replace('%BOLD%', '<b>', $birthdaytext2);
$birthdaytext2 = str_replace('%BOLDEND%', '</b>', $birthdaytext2);
?>    
<div class="item <?php echo $active; ?>">
<img src="<?php echo $thispic; ?>" alt="<?php echo $club->name; ?>" width="<?php echo $params->get('picture_width'); ?>" >
<div class="carousel-caption">
<h3><?php echo $club->name; ?></h3>
<p><?php echo $birthdaytext2; ?></p>
</div>
</div>
<?PHP 
$a++;   
}
?>



</div>
<!-- Left and right controls -->
<a class="left carousel-control" href="#myClubBirthday<?php echo $module->id; ?>" role="button" data-slide="prev">
<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
<span class="sr-only">Previous</span>
</a>
<a class="right carousel-control" href="#myClubBirthday<?php echo $module->id; ?>" role="button" data-slide="next">
<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
<span class="sr-only">Next</span>
</a>
</div>
<?PHP    
    break;
    case 'T':
	case 'L':	
	foreach ($clubs AS $club) 
    {
    $idstring = $id.$params->get( 'moduleclass_sfx' ) ;
	if ($mode == 'T')
	{
		$birthdaytext .= '<div id="'.$idstring.'" class="textdiv">';
	}
    
        $club->default_picture = sportsmanagementHelper::getDefaultPlaceholder('clublogobig');
        
		if (($params->get('limit')> 0) && ($counter == intval($params->get('limit')))) break;
		$class = ($k == 0)? $params->get('sectiontableentry1') : $params->get('sectiontableentry2');

		$thispic = "";
		$flag = $params->get('show_club_flag')? JSMCountries::getCountryFlag($club->country) . "&nbsp;" : "";
		
        $text = $club->name;
        $usedname = $flag.$text;
		
		$club_link = "";
        $club_link = sportsmanagementHelperRoute::getClubInfoRoute($club->project_id,
																$club->id);
		
		$showname = JHTML::link( $club_link, $usedname );
		
        $birthdaytext .= '<div class="qslide">';
			
			$birthdaytext .= '<div class="tckproject"><p>' . $showname . '</p></div>';
			
            if ($params->get('show_picture')==1) 
        {
			if (file_exists(JPATH_BASE.'/'.$club->picture)&&$club->picture!='') 
            {
				$thispic = $club->picture;
			}
			elseif (file_exists(JPATH_BASE.'/'.$club->default_picture)&&$club->default_picture!='') 
            {
				$thispic = $club->default_picture;
			}
			$birthdaytext .= '<div style="width:100%"><center><img style="" src="'.JURI::base().'/'.$thispic.'" alt="'.$text.'" title="'.$text.'"';
			if ($params->get('picture_width') != '') $birthdaytext .= ' width="'.$params->get('picture_width').'"';
			$birthdaytext .= ' /></center></div><br />';

		}
        
        switch ($club->days_to_birthday) 
        {
			case 0: $whenmessage = $params->get('todaymessage');break;
			case 1: $whenmessage = $params->get('tomorrowmessage');break;
			default: $whenmessage = str_replace('%DAYS_TO%', $club->days_to_birthday, trim($futuremessage));break;
		}
        
        if ( $club->founded != '0000-00-00' )
        {
            $birthdaytext2 = htmlentities(trim(JText::_($params->get('birthdaytext'))), ENT_COMPAT , 'UTF-8');
            $dayformat = htmlentities(trim($params->get('dayformat')));
		    $birthdayformat = htmlentities(trim($params->get('birthdayformat')));
		    $birthdaytext2 = str_replace('%WHEN%', $whenmessage, $birthdaytext2);
            $birthdaytext2 = str_replace('%AGE%', $club->age, $birthdaytext2);
            $birthdaytext2 = str_replace('%DATE%', strftime($dayformat, strtotime($club->year.'-'.$club->daymonth)), $birthdaytext2);
    		$birthdaytext2 = str_replace('%DATE_OF_BIRTH%', strftime($birthdayformat, strtotime($club->date_of_birth)), $birthdaytext2);
        }
        else
        {
            $birthdaytext2 = htmlentities(trim(JText::_($params->get('birthdaytextyear'))), ENT_COMPAT , 'UTF-8');
            $birthdaytext2 = str_replace('%AGE%', $club->age_year, $birthdaytext2);
        }
            
            $birthdaytext2 = str_replace('%BR%', '<br />', $birthdaytext2);
		$birthdaytext2 = str_replace('%BOLD%', '<b>', $birthdaytext2);
		$birthdaytext2 = str_replace('%BOLDEND%', '</b>', $birthdaytext2);
            $birthdaytext .= '<div style="width:100%"><center>'.$birthdaytext2.'</center></div><br />';
            
            $birthdaytext .= '</div>';
            
    
    if ($mode == 'T')
	{
		$birthdaytext .= "</div>";
	}
	$id++;
    
	}
    break;
    

}            
    
   
    
}




switch ($mode)
{
	// ticker mode template
	case 'T':
		echo $birthdaytext;
		break;
			
	// list mode template
	case 'L':
		?>
	<div id="qscroller<?php echo $params->get( 'moduleclass_sfx' ); ?>" ><?php echo $birthdaytext; ?></div>
		<?php
		break;

	
    
    

	// Horizontal scroll mode template
	case 'H':
	?>
	<div id="qscroller<?php echo $params->get( 'moduleclass_sfx' ); ?>" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px"></div>
	<div class="hide"><?php echo $birthdaytext; ?></div>

	<?php
	break;
}
?>
