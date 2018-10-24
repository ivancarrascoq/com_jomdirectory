<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHTML::_('behavior.modal', 'a.modal');
JText::script('COM_JOMCOMDEV_MONTH_NAMES');
JText::script('COM_JOMCOMDEV_DAY_NAMES_SHORT');
JText::script('COM_JOMCOMDEV_CLLENDAR_BUTTONS');
JText::script('COM_JOMCOMDEV_TODAY');
JText::script('COM_JOMCOMDEV_MONTH');
JText::script('COM_JOMCOMDEV_WEEK');
JText::script('COM_JOMCOMDEV_DAY');

$document = JFactory::getDocument();

//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/moment/min/moment.min.js');
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.css');
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/fullcalendar/dist/fullcalendar.min.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/fullcalendar/dist/fullcalendar.min.css');

$id = JRequest::getInt('id', false);
$extension = 'com_jomdirectory';
?>
<script>
	window.addEvent('domready', function () {
		fullcalendarNotes.init({site: true, container: 'ComdevCalendarBox', items_id: <?php echo $id;?>, extension: '<?php echo $extension;?>'});
	});
</script>
<div id="ComdevCalendarBox"></div>   

 
