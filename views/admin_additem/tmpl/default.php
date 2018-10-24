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
JHTML::_('behavior.calendar');
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
		if (task == 'admin_additem.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('fulltext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<div id="jomdirectory-admin" class="admin-add-listing">

	<?php echo $header->render(null); ?>

	<?php if ($this->blocked): ?>
        <div class="my-5">
			<?php echo "<span class='alert alert-danger'>" . JText::_('COM_JOMDIRECTORY_ADM_PLAN_BLOCKED') . "</span>"; ?>
        </div>
	<?php elseif ($this->pre_limit[0]): ?>
        <div class="my-5">
			<?php echo "<span class='alert alert-danger'>" . JText::_('COM_JOMDIRECTORY_NOT_ENOUGH_LISTINGS_IN_PLAN') . "</span>"; ?>
        </div>
	<?php else: ?>

        <div class="mt-3 clearfix">
            <div class="float-left">
                <h3><?php echo empty($this->item->id) ? JText::_('COM_JOMDIRECTORY_ADM_NEW_ARTICLE') : JText::sprintf('COM_JOMDIRECTORY_ADM_EDIT_ARTICLE', $this->item->id); ?></h3>
            </div>
            <div class="float-right">
				<?php echo $this->toolbar ?>
            </div>
        </div>

        <div id="admin-body" class="card card-body my-3">

            <form action="<?php echo JRoute::_('?option=com_jomdirectory&layout=edit&id=' . (int)$this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="item-form" class="form-validate ">

                <div class="form-group">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
                </div>
				<?php if ($this->params->get('admin_enable_alias')): ?>
                    <div class="form-group">
						<?php echo $this->form->getLabel('alias'); ?>
						<?php echo $this->form->getInput('alias'); ?>
                    </div>
				<?php endif; ?>
                <div class="form-group row">
                    <div class="col-md-4">
	                    <?php echo $this->form->getLabel('categories_id'); ?>
	                    <?php echo $this->form->getInput('categories_id'); ?>
                    </div>
                    <div class="col-md-4">
	                    <?php echo $this->form->getLabel('phone'); ?>
	                    <?php echo $this->form->getInput('phone'); ?>
                    </div>
                    <div class="col-md-4">
	                    <?php echo $this->form->getLabel('webpage'); ?>
	                    <?php echo $this->form->getInput('webpage'); ?>
                    </div>
                </div>
                <div class="form-group clearfix">
					<?php echo $this->form->getLabel('fulltext'); ?>
					<?php echo $this->form->getInput('fulltext'); ?>
                </div>
				<?php if ($this->params->get('enable_short_desc')): ?>
                    <div class="form-group">
						<?php echo $this->form->getLabel('introtext'); ?>
						<?php echo $this->form->getInput('introtext'); ?>
                    </div>
				<?php endif; ?>
                <div class="form-group row">
                    <div class="col-md-4">
	                    <?php echo $this->form->getLabel('language'); ?>
	                    <?php echo $this->form->getInput('language'); ?>
                    </div>
                    <div class="col-md-8">
	                    <?php if ($this->params->get('enable_tags')): ?>
                            <div class="form-group">
		                    <?php if ($this->params->get('enable_tags')): ?>
			                    <?php $jVerArr = explode('.', JVERSION);
			                    if ($jVerArr[0] >= '3'):
				                    ?>
				                    <?php foreach ($this->get('form')->getFieldset('jmetadata') as $field) : ?>
				                    <?php if ($field->name == 'jform[metadata][tags][]') : ?>
					                    <?php echo $field->label; ?>
					                    <?php echo $field->input; ?>
				                    <?php endif; ?>
			                    <?php endforeach; ?>
			                    <?php endif; ?>
                                </div>
		                    <?php endif; ?>
	                    <?php endif; ?>
                    </div>
                </div>
				<?php if ($this->params->get('frontadmin_enable_location')): ?>
                    <h3><i class="fa fa-map-marker"></i> <?php echo JText::_('COM_JOMDIRECTORY_TAB_LOCATION'); ?></h3>
                    <div class="form-group clearfix">
						<?php echo $this->form->getLabel('categories_address_id'); ?>
						<?php echo $this->form->getInput('categories_address_id'); ?>
                    </div>
                    <div class="form-group">
						<?php echo $this->form->getLabel('fulladdress'); ?>
						<?php echo $this->form->getInput('fulladdress'); ?>
                    </div>
					<?php echo $this->loadTemplate('maps'); ?>
				<?php endif; ?>
				<?php if ($this->params->get('enable_yelp')): ?>
                    <div class="form-group mt-3">
						<?php echo $this->form->getLabel('yelp_id'); ?>
						<?php echo $this->form->getInput('yelp_id'); ?>
                    </div>
				<?php endif; ?>

                <div class="my-3">
					<?php echo JHtml::_('sliders.start', 'content-sliders-' . $this->item->id, array('useCookie' => 1)); ?>

					<?php echo JHtml::_('sliders.panel', '<i class="fa fa-bars"></i> ' . JText::_('COM_JODIRECTORY_FIELDSET_CUSTOM_FIELDS') . '<i class="fa fa-chevron-down fa-panel-right float-right "></i>', 'publishing-details'); ?>
                    <div class="my-3">
						<?php echo $this->form->getLabel('fields'); ?>
						<?php echo $this->form->getInput('fields'); ?>
                    </div>

					<?php echo $this->loadTemplate('images'); ?>

					<?php if ($this->params->get('enable_calendar') && $this->params->get('frontadmin_enable_calendar') && $this->limits->paid_fields['calendar']): ?>
						<?php echo $this->loadTemplate('calendar'); ?>
					<?php endif; ?>


					<?php echo JHtml::_('sliders.end'); ?>
                </div>
        </div>

		<?php echo $this->form->getInput('asset_id'); ?>
        <input type="hidden" name="task" value=""/> <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return'); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
        </form>

        <div class="pt-3 mt-3 clearfix">
            <div class="float-right">
				<?php echo $this->toolbar ?>
            </div>
        </div>

	<?php endif; ?>
</div>