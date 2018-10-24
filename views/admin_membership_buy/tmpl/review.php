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
$document = JFactory::getDocument();
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$model = JModelLegacy::getInstance("admin_membership_buy", "JomdirectoryModel");
$header = new JLayoutFile('default_header', $basePath = JPATH_ROOT . '/components/com_jomdirectory/views/admin_dashboard/tmpl');

?>

<div id="admin-wrapper">

	<?php echo $header->render(null); ?>

    <h4 class="text-center my-3"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP') ?> <?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UPGRADE') ?>: <?php echo JText::_($this->newplan->name) ?></h4>

    <div class="d-block position-relative card my-5">
        <div class="card-body">
			<?php
			$this->_session = JFactory::getSession();
			$this->_sessionData = $this->_session->get('comdev_membership');
			if ($this->_sessionData->payment == "bank") {
				$plugin = JPluginHelper::getPlugin('jcpayments', 'bank');
				$params = new JRegistry($plugin->params);
				echo nl2br($params->get('payment_instruction'));
			}
			?>
        </div>
    </div>
</div>