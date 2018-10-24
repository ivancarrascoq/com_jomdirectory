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

$header = new JLayoutFile('default_header', $basePath = JPATH_ROOT . '/components/com_jomdirectory/views/admin_dashboard/tmpl');
?>
<div id="jd-admin-wrapper">
	<?php echo $header->render(null); ?>

    <div class="border-bottom mt-3 clearfix">
        <h3><?php echo JText::_('COM_JOMDIRECTORY_ADM_HELP') ?></h3>
    </div>

    <div id="admin-body">
        <div class="card card-body">
            <h3><?php echo $this->article['title'] ?></h3>
			<?php echo $this->article['text'] ?>
        </div>
    </div>
</div>