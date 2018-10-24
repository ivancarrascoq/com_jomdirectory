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
$document = JFactory::getDocument();
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/moment/min/moment.min.js');
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.css');
//$document->addStyleSheet("https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/fullcalendar/dist/fullcalendar.min.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/fullcalendar/dist/fullcalendar.min.css');

$id = JFactory::getApplication()->input->get('id');
$extension = "com_jomdirectory";
if ($this->item->id):
	echo JHtml::_('sliders.panel', '<i class="fa fa-calendar"></i> ' . JText::_('COM_JOMDIRECTORY_CALENDAR') . '<i class="fa fa-chevron-down fa-panel-right float-right "></i>', 'calendar');
	?>
    <script>
		window.addEvent('domready', function () {
			fullcalendarInit();
		});

		function fullcalendarReInit() {
			jQuery('#ComdevCalendarBox').fullCalendar('destroy');
			fullcalendarInit();
		}

		function fullcalendarInit() {
			jQuery("#ComdevCalendarForm").hide();
			fullcalendarNotes.init({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				container: 'ComdevCalendarBox',
				items_id: <?php echo $id;?>,
				extension: '<?php echo $extension;?>',
			});
		}

		function fullcalendarEventSubmit() {
			address = '/index.php?option=com_jomcomdev&task=calendar.saveData&format=raw';
			start = jQuery("#comdev_start").val();
			end = jQuery("#comdev_end").val();
			allDay = jQuery("#comdev_allDay").val();
			title = jQuery("#jform_calendar_title").val();
			description = jQuery("#jform_calendar_description").val();
			color = jQuery("#jform_calendar_color").val();
			myRequest = new Request.HTML({url: address}).post('items_id=<?php echo $id;?>&extension=<?php echo $extension;?>&start=' + start + '&end=' + end + '&allDay=' + allDay + '&title=' + title + '&color=' + color + '&description=' + description);
			myRequest.onSuccess = function () {
				fullcalendarReInit();
			};
		}
    </script>
    <div id="ComdevCalendarBox" class=""></div>
    <div id="ComdevCalendarForm" style="display:none" class="">
        <div class="form-group">
			<?php echo $this->form->getLabel('calendar_title'); ?>
			<?php echo $this->form->getInput('calendar_title'); ?>
        </div>
        <div class="form-group">
			<?php echo $this->form->getLabel('calendar_color'); ?>
			<?php echo $this->form->getInput('calendar_color'); ?>
        </div>
        <div class="form-group">
			<?php echo $this->form->getLabel('calendar_description'); ?>
			<?php echo $this->form->getInput('calendar_description'); ?>
        </div>
        <div class="form-group">
            <a class="btn btn-success" href="javascript:fullcalendarEventSubmit()"><?php echo JText::_('COM_JOMDIRECTORY_SAVE') ?></a>
            <a class="btn btn-warning" href="javascript:fullcalendarReInit()"><?php echo JText::_('COM_JOMDIRECTORY_CANCEL') ?></a>
        </div>
    </div>
<?php endif;
 
