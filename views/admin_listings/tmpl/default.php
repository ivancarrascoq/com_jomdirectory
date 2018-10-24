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
$document->addScript(JURI::base() . 'components/com_jomcomdev/javascript/CeraBox/cerabox.min.js');
$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/javascript/CeraBox/style/cerabox.css');
$document->addScript(JURI::base(true) . '/components/com_jomdirectory/assets/js/chart/MilkChart.yc.js');

$runScript = "window.addEvent('domready', function() {
	$$('.jd_stats').cerabox({width: 950, height:450});
});";
$document->addScriptDeclaration($runScript);
$header = new JLayoutFile('default_header', $basePath = JPATH_ROOT . '/components/com_jomdirectory/views/admin_dashboard/tmpl');
?>

<div id="jomdirectory-admin">

	<?php echo $header->render(null); ?>

    <div class="border-bottom mt-3 clearfix">
        <div class="float-left">
            <h3><?php echo JText::_('COM_JOMDIRECTORY_ADM_LISTINGS') ?></h3>
        </div>
        <div class="float-right">
			<?php echo $this->toolbar ?>
        </div>
    </div>

    <div id="admin-header" class="my-3 pb-3 border-bottom">
        <form name="jdFormItems" action="" method="post" class="">
            <div class="clearfix form-row align-items-center">
                <div class="d-inline">
                    <input id="filter_search" class="form-control" type="text" value='<?php echo $this->state->{'filter.search'} ?>' name="filter_search">
                </div>
                <button class="btn btn-primary" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_ADM_SEARCH'); ?></button>
                <button class="btn btn-primary" onclick="document.id('filter_search').value='';this.form.submit();" type="button"><?php echo JText::_('COM_JOMDIRECTORY_ADM_CLEAR'); ?></button>

                <label class="my-0 mx-2"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_BY'); ?></label>
				<?php
				$arraySort = array(array('v' => 'latest', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_LATEST')), array('v' => 'updated', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_UPDATED')), array('v' => 'alfa', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_ALPHABETICALLY')), array('v' => 'rated_desc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_DESC')), array('v' => 'rated_asc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_ASC')), array('v' => 'most_viewed', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_MOSTVIEWED')));
				?>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', $arraySort, 'jdItemsSort', 'class="custom-select mb-0" onChange="this.form.submit()"', 'v', 't', $this->state->{'list.sort'}); ?>
                </div>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', array(array('text' => 5, 'value' => 5), array('text' => 10, 'value' => 10), array('text' => 15, 'value' => 15), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
                </div>
                <label class="my-0 mx-2"><?php echo JText::_('COM_JOMDIRECTORY_ADM_CATEGORY') ?></label>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', $this->categories, 'filter_category', 'class="custom-select mb-0" onChange="this.form.submit()" style="width:100px"', 'v', 't', $this->state->{'filter.categories_id'}); ?>
                </div>
            </div>
        </form>
    </div>
    <div id="admin-body">
        <form action="" method="post" name="adminForm" id="adminForm">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/></th>
                    <th><?php echo JText::_('COM_JOMDIRECTORY_ADMIN_TITLE') ?></th>
                    <th><?php echo JText::_('COM_JOMDIRECTORY_ADM_CATEGORY') ?></th>
                    <th><?php echo JText::_('COM_JOMDIRECTORY_FIELD_CREATED_LABEL') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php $i = 0;
				foreach ($this->listings as $row): ?>
                    <tr>
                        <td class="text-nowrap">
							<?php echo JHTML::_('grid.id', $i, $row->id); ?>
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'admin_listings.', 1, 'cb', $row->date_publish, $row->date_publish_down); ?>
							<?php echo JHtml::_('frontadmin.featured', $row->featured, $i, true, 'admin_listings'); ?>
                        </td>
                        <td>
							<?php if ($row->featured): ?><span class="badge-cd badge-danger"><?php echo JText::_('COM_JOMDIRECTORY_FIELD_FEATURED') ?></span> <?php endif; ?>
							<?php $link = JRoute::_('?option=com_jomdirectory&task=admin_additem.edit&id=' . $row->id); ?>
                            <h4 class="my-1"><?php echo JHTML::_('link', $link, $row->title); ?></h4>
                        </td>
                        <td><?php echo $row->category_title ?></td>
                        <td><?php echo JHtml::_('date', $row->date_created, JText::_('DATE_FORMAT_LC3')); ?></td>
                        <td class="text-center text-nowrap">
							<?php
							if ($this->params->get('admin_allow_reviews')):
								$link = JRoute::_('?option=com_jomdirectory&view=admin_comments&content_id=' . $row->id);
								echo JHTML::_('link', $link, '<i class="fa fa-comment-o"></i>', 'class="btn btn-sm btn-primary mr-1"');
							endif;
							$link = JRoute::_('?option=com_jomdirectory&view=admin_statistic&content_id=' . $row->id . '&current=' . date("Y-m") . '&tmpl=component');
							echo JHTML::_('link', $link, '<i class="fa fa-signal"></i>', 'class="jd_stats btn btn btn-sm btn-info" data-type="ajax"');
							?>
                        </td>
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
</div>