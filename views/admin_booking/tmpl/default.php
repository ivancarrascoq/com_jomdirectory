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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$header = new JLayoutFile('default_header', $basePath = JPATH_ROOT . '/components/com_jomdirectory/views/admin_dashboard/tmpl');
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		// Turn radios into btn-group
		jQuery('.radio.btn-group label').addClass('btn');
		jQuery(".btn-group label:not(.active)").click(function () {
			var label = jQuery(this);
			var input = jQuery('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		jQuery(".btn-group input[checked=checked]").each(function () {
			if (jQuery(this).val() == '') {
				jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
			} else if (jQuery(this).val() == 0) {
				jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
			}
		});

	});
</script>

<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'admin_booking.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<div id="jomdirectory-admin">

	<?php echo $header->render(null); ?>

	<?php if ($this->blocked): ?>
        <div class="text-center my-5">
			<?php echo "<span class='badge-cd badge-danger'>" . JText::_('COM_JOMDIRECTORY_ADM_PLAN_BLOCKED') . "</span>"; ?>
        </div>
	<?php else: ?>

        <div class="border-bottom mt-3 clearfix">
            <div class="float-left">
                <h3><?php echo empty($this->item->id) ? JText::_('COM_JOMDIRECTORY_ADM_NEW_ARTICLE') : JText::sprintf('COM_JOMDIRECTORY_ADM_EDIT_ARTICLE', $this->item->id); ?></h3>
            </div>
            <div class="float-right">
				<?php echo $this->toolbar ?>
            </div>
        </div>

        <div id="admin-body" class="card card-body my-3">

            <form action="<?php echo JRoute::_('?option=com_jomdirectory&layout=edit&id=' . (int)$this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="item-form" class="form-validate ">
                <div class="jd-admin-box">

                    <div class="form-group">
						<?php echo $this->form->getInput('state'); ?>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
	                        <?php echo $this->form->getLabel('items_id'); ?>
	                        <?php echo $this->form->getInput('items_id'); ?>
                        </div>
                        <div class="col-md-4">
	                        <?php echo $this->form->getLabel('service_id'); ?>
	                        <?php echo $this->form->getInput('service_id'); ?>
                        </div>
                        <div class="col-md-4">
	                        <?php echo $this->form->getLabel('price'); ?>
	                        <?php echo $this->form->getInput('price'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
	                        <?php echo $this->form->getLabel('date_reservation'); ?>
	                        <?php echo $this->form->getInput('date_reservation'); ?>
                        </div>
                        <div class="col-md-4">
	                        <?php echo $this->form->getLabel('hour_from'); ?>
	                        <?php echo $this->form->getInput('hour_from'); ?>
                        </div>
                        <div class="col-md-4">
	                        <?php echo $this->form->getLabel('hour_to'); ?>
	                        <?php echo $this->form->getInput('hour_to'); ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="task" value=""/> <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return'); ?>"/>
				<?php echo JHtml::_('form.token'); ?>
            </form>

            <div class="mt-3 clearfix">
                <div class="float-right">
					<?php echo $this->toolbar ?>
                </div>
            </div>

        </div>
	<?php endif; ?>
</div>