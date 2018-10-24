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

$id = JRequest::getInt('id', false);
$extension = 'com_jomdirectory';


?>
<script>
	window.addEvent('domready', function () {
		fullcalendarService.init({site: true, container: 'ComdevCalendarBoxService', items_id: <?php echo $id;?>, extension: '<?php echo $extension;?>'});
	});
</script>

<div class="table-responsive">
    <h3>Available Booking Services</h3>
    <table class="table table-striped">
        <thead>
        <th>Service Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Action</th>
        </thead>
        <tbody>
		<?php foreach ($this->item->service AS $service): ?>
            <tr>
                <td><?php echo $service->title ?></td>
                <td><?php echo $service->description ?></td>
                <td><?php echo $service->price ?><?php echo $this->params->get('adm_currency', 'EUR'); ?></td>
                <td class="text-right">
                    <a hare="#" data-id="<?= $service->id ?>" data-object="{id: <?= $service->id ?>, title: '<?= htmlspecialchars($service->title) ?>', price: <?= $service->price ?>}" class="jc-service-slot btn-primary btn btn-primary"><i class="fa fa-calendar"></i> Time Slots</a>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
    <div id="jc-status-area" class="z-index-1"></div>
    <div id="ComdevCalendarBoxService" class="my-3"></div>
</div>



 
