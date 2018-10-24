<?php
/*------------------------------------------------------------------------
# com_jomholiday - JomHoliday
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
JHtml::_('jquery.framework');
?>

<script>
	datapie = [
		<?php
		$j = 0;
		if (isset($this->item->reviews->recommended)) foreach ($this->item->reviews->recommended as $k => $el) {
			if ($j) echo ",";
			echo '{label:"' . JText::_($k) . '", data:' . $el . '}';
			$j++;
		}
		?>
	];
	<?php if ($j): ?>
	jQuery(function () {
		jQuery.plot("#recommended_for", datapie, {
			series: {
				pie: {
					show: true
				}
			},
			legend: {
				show: false
			}
		});
	});
	<?php endif ?>
</script>
<center>
    <div id="recommended_for" style="width:400px;height:350px;margin:20px;position:relative"></div>
</center>
             