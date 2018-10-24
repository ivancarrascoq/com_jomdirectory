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

?>
<script type="text/javascript" src="/components/com_jomdirectory/assets/js/chart/MilkChart.yc.js"></script>
<script>
	window.addEvent('domready', function () {
		var chart = new MilkChart.Column("chart", ({
			width: 300,
			height: 300,
			padding: 10,
			showKey: false,
		}));
		var chart_item = new MilkChart.Column("chart_item", ({
			width: 300,
			height: 300,
			padding: 10,
			showKey: false,
		}));
		var chart_favorite = new MilkChart.Column("chart_favorite", ({
			width: 300,
			height: 300,
			padding: 10,
			showKey: false,
		}));
		$$('.jd_stats').cerabox({animation: 'ease', group: false, width: 950, height: 550, fullSize: true});
	});
</script>
<div class="my-3 clearfix">
	<?php
	$link = JRoute::_('?option=com_jomdirectory&view=admin_statistic&content_id=' . $this->id . '&current=' . $this->dates['back'] . '&tmpl=component');
	echo JHTML::_('link', $link, JText::_('COM_JOMDIRECTORY_ADM_PREV'), 'class="jd_stats btn btn-primary" data-type="ajax" style="float:left"');
	if ($this->dates['forward']) {
		$link = JRoute::_('?option=com_jomdirectory&view=admin_statistic&content_id=' . $this->id . '&current=' . $this->dates['forward'] . '&tmpl=component');
		echo JHTML::_('link', $link, JText::_('COM_JOMDIRECTORY_ADM_NEXT'), 'class="jd_stats btn btn-primary" data-type="ajax" style="float:right"');
	}
	?>
</div>
<div class="d-block position-relative card p-3 my-3 clearfix">
    <div class="float-left">
        <h3><?php echo JText::_('COM_JOMDIRECTORY_ADM_STAT_LIST') ?></h3>
		<?php
		if (!empty($this->chart)):
			?>
            <div>
                <table id="chart">
                    <thead>
                    <tr>
                        <th><?php echo JText::_('COM_JOMDIRECTORY_ADM_HITS') ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ($this->chart as $row): ?>
                        <tr>
                            <td><?php echo $row->view_in_list ?></td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
						<?php foreach ($this->chart as $row): ?>
                            <th><?php echo $row->date_year ?>-<?php echo $row->date_month ?></th>
						<?php endforeach; ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
		<?php endif; ?>
    </div>
    <div class="float-left">
        <h3><?php echo JText::_('COM_JOMDIRECTORY_ADM_STAT_ITEM') ?></h3>
		<?php
		if (!empty($this->chart)):
			?>
            <div>
                <table id="chart_item">
                    <thead>
                    <tr>
                        <th><?php echo JText::_('COM_JOMDIRECTORY_ADM_HITS') ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ($this->chart as $row): ?>
                        <tr>
                            <td><?php echo $row->view_item ?></td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
						<?php foreach ($this->chart as $row): ?>
                            <th><?php echo $row->date_year ?>-<?php echo $row->date_month ?></th>
						<?php endforeach; ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
		<?php endif; ?>
    </div>
    <div class="float-left">
        <h3><?php echo JText::_('COM_JOMDIRECTORY_ADM_STAT_FAVORITE') ?></h3>
		<?php
		if (!empty($this->chart)):
			?>
            <div>
                <table id="chart_favorite">
                    <thead>
                    <tr>
                        <th><?php echo JText::_('COM_JOMDIRECTORY_ADM_HITS') ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ($this->chart as $row): ?>
                        <tr>
                            <td><?php echo $row->add_favorite ?></td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
						<?php foreach ($this->chart as $row): ?>
                            <th><?php echo $row->date_year ?>-<?php echo $row->date_month ?></th>
						<?php endforeach; ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
		<?php endif; ?>
    </div>
</div>