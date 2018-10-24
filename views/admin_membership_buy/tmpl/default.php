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

$payments = Main_Payment::getMethods();

?>

<div id="jomdirectory-admin">

	<?php echo $header->render(null); ?>

    <h4 class="text-center my-3"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP') ?> <?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UPGRADE') ?>: <?php echo JText::_($this->newplan->name) ?></h4>
    <form method="post" action="/index.php?option=com_jomdirectory&view=admin_membership_buy&layout=review" id="billing_form" class="form-validate " data-link="/index.php?option=com_jomcomdev&task=cart.formShipping&format=json">

        <div class="d-block position-relative card my-5">
            <div class="card-body">
                <div class="clearfix">
					<?php if ($this->params->get('monthly_plan')): ?>
                        <div class="clearfix form-group">
                            <input type='radio' value='0' name='annualy'> <?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_MONTHLY') ?> - <?php echo $model->priceChange($this->newplan->price_monthly, $this->priceParams) ?>
                        </div>
					<?php endif ?>
                    <div class="clearfix form-group">
                        <input type='radio' value='1' name='annualy' checked> <?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_ANNUALLY') ?> - <?php echo $model->priceChange($this->newplan->price_annually, $this->priceParams) ?>
                    </div>
                    <input type='hidden' name='plan_id' value='<?php echo $this->newplan->id ?>'>
                </div>
            </div>
        </div>
        <div class="d-block position-relative card my-5">
            <div class="card-title p-2 h5">
				<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT_METHOD'); ?>
            </div>
            <div class="card-body">
                <div class="clearfix">
					<?php foreach ($payments AS $key => $p): ?>
                        <div class="clearfix form-row">
                            <label for="fixed_pay0" class="float-left"> <input type="radio" value="<?= $p->name ?>" id="fixed_pay<?= $key ?>" checked="checked" name="payment"> <?= $p->params->title ?> </label>
                        </div>
					<?php endforeach; ?>
                </div>
                <button class="btn btn-success mt-3" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_CONTINUE'); ?></button>
            </div>
        </div>
    </form>
</div>