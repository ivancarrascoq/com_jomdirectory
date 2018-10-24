<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal');
$document = JFactory::getDocument();

$header = new JLayoutFile('default_header', $basePath = JPATH_ROOT . '/components/com_jomdirectory/views/admin_dashboard/tmpl');
?>

<div id="jomdirectory-admin">

	<?php echo $header->render(null); ?>

    <div class="border-bottom mt-3 clearfix">
        <div class="float-left">
            <h3><?php echo JText::_('COM_JOMDIRECTORY_BOOKING') ?></h3>
        </div>
        <div class="float-right">
			<?php echo $this->toolbar ?>
        </div>
    </div>

    <div id="admin-header" class="my-3 pb-3 border-bottom">

        <form name="jdFormItems" action="" method="post" class="">
            <div class="mt-3 clearfix form-row align-items-center">
                <div class="d-inline">
                    <input id="filter_search" class="form-control" type="text" value='<?php echo $this->state->{'filter.search'} ?>' name="filter_search">
                </div>
                <button class="btn btn-primary" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_ADM_SEARCH'); ?></button>
                <button class="btn btn-primary" onclick="document.id('filter_search').value='';this.form.submit();" type="button"><?php echo JText::_('COM_JOMDIRECTORY_ADM_CLEAR'); ?></button>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', array(array('text' => 5, 'value' => 5), array('text' => 10, 'value' => 10), array('text' => 15, 'value' => 15), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
                </div>
            </div>
        </form>
    </div>
    <form action="" method="post" name="adminForm" id="adminForm">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_ORDER_STATUS') ?></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_DATE_RESERVATION') ?></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_BRAND') ?></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_SERVICE') ?></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_PRODUCT_PRICE') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
			<?php $i = 0;
			foreach ($this->listings as $row): ?>
                <tr>
                    <td class="text-nowrap">
						<?php echo JHTML::_('grid.id', $i, $row->id); ?>
                    </td>
                    <td class="text-nowrap">
						<?php echo JText::_('COM_JOMDIRECTORY_STATE_' . strtoupper($row->state)); ?>
                    </td>
                    <td>
						<?php $link_booking = JRoute::_('?option=com_jomdirectory&task=admin_booking.edit&id=' . $row->id); ?>
                        <h4 class="my-1"><?php echo JHTML::_('link', $link_booking , JHtml::_('date', $row->date_reservation, JText::_('DATE_FORMAT_LC3'))); ?></h4>
                    </td>
                    <td>
						<?php $link = JRoute::_('?option=com_jomdirectory&task=admin_additem.edit&id=' . $row->itemsID); ?>
                        <h4 class="my-1"><?php echo JHTML::_('link', $link, $row->itemsTitle); ?></h4></td>
                    <td>
						<?php $link = JRoute::_('?option=com_jomdirectory&task=admin_service.edit&id=' . $row->service_id); ?>
                        <h4 class="my-1"><?php echo JHTML::_('link', $link, $row->serviceTitle); ?></h4></td>
                    <td><?php echo Main_Price::changeSingle($row->price, $this->priceParams); ?></td>
                    <td><a href="<?php echo $link_booking; ?>" class="btn btn-primary"><i class="fa fa-chevron-circle-right"></i> </a></td>
                </tr>
				<?php $i++; endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value=""/> <input type="hidden" name="hidemainmenu" value=""/> <input type="hidden" name="boxchecked" value=""/>
    </form>

    <div class="pagination clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>
</div>
