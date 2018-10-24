<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

$params = JComponentHelper::getParams('com_jomdirectory');
$model = JModelLegacy::getInstance("admin_membership", "JomdirectoryModel");

defined('_JEXEC') or die;
$payments = Main_Payment::getMethods();
?>

<section class="pricing-table">
    <div class="container">
        <div class="block-heading">
            <h2><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP') ?></h2>
            <h4><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_TAG') ?></h4>
        </div>
        <div class="row justify-content-md-center">

			<?php $i = 0;
			$buy = 0;
            foreach ($this->plans as $row): ?>
                <?php $row->paid_fields=json_decode($row->paid_fields,true);?>
				<?php if ($this->params->get('show_free_plan', 1) || $i): ?>
                    <div class="col-md-5 col-lg-4">
                        <div class="item h-100">
							<?php if ($row->best_value): ?>
                                <div class="ribbon"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_BESTVALUE') ?></div>
                            <?php endif ?>
                            <div class="heading">
                                <h3><?php echo JText::_($row->name); ?></h3>
                            </div>
                            <p></p>
                            <div class="features">
                                <h4>
                                    <span class="feature"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_LISTINGS') ?></span> : <span class="value">
                                        <?php if ($row->listings_nr >= 99999):
	                                        echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
                                        else:
	                                        echo $row->listings_nr;
                                        endif; ?>
                                    </span>
                                </h4>
                                <h4>
                                    <span class="feature"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_IMAGES') ?></span> : <span class="value">
									<?php if ($row->images_nr >= 99999):
										echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
									else:
										echo $row->images_nr;
									endif; ?>
                                    </span>

                                </h4>
                                <h4>
                                    <span class="feature"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_PREMIUM_LISTINGS') ?></span> : <span class="value">
									<?php if ($row->premium_nr >= 99999):
										echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
									else:
										echo $row->premium_nr;
									endif; ?>
                                    </span>
                                </h4>
	                            <?php if ($this->params->get('product_display')): ?>
                                    <h4>
                                        <span class="feature">  <?php echo JText::_('COM_JOMDIRECTORY_PRODUCTS') ?></span> : <span class="value">
	                                            <?php if (!isset($row->paid_fields['products']) || $row->paid_fields['products'] == 0): echo "<i class='fa fa-check'></i> ";
	                                            else: echo "<i class='fa fa-check'></i>"; endif; ?>
                                            </span>
                                    </h4>
	                            <?php endif; ?>
                                <h4>
                                    <span class="feature"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_ATTACHMENTS') ?></span> : <span class="value">
	                                <?php if ($row->attachments): echo "<i class='fa fa-check'></i>"; else: echo "<i class='fa fa-times'></i>"; endif; ?>
                                    </span>
                                </h4>
                                <h4>
                                    <span class="feature"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_VIDEO') ?></span> : <span class="value">
		                            <?php if ($row->video): echo "<i class='fa fa-check'></i>"; else: echo "<i class='fa fa-times'></i>"; endif; ?>
                                    </span>
                                </h4>
                                <h4>
                                    <span class="feature">  <?php echo JText::_('COM_JOMDIRECTORY_FIELD_PHONE') ?></span> : <span class="value">
	                                    <?php if ($row->paid_fields['phone'] == 1): echo "<i class='fa fa-check'></i> ";
	                                    else: echo "<i class='fa fa-times'></i>"; endif; ?>
                                    </span>
                                </h4>
                                <h4>
                                    <span class="feature">  <?php echo JText::_('COM_JOMDIRECTORY_FIELD_WEBPAGE') ?></span> : <span class="value">
	                                    <?php if ($row->paid_fields['webpage'] == 1): echo "<i class='fa fa-check'></i> ";
	                                    else: echo "<i class='fa fa-times'></i>"; endif; ?>
                                    </span>
                                </h4>

								<?php if ($this->params->get('enable_calendar') && $this->params->get('frontadmin_enable_calendar')): ?>
                                    <h4>
                                        <span class="feature"><?php echo JText::_('COM_JOMDIRECTORY_CALENDAR') ?></span> : <span class="value">
		                            <?php if ($row->paid_fields['calendar'] == 0): echo "<i class='fa fa-times'></i>"; else: echo "<i class='fa fa-check'></i>"; endif; ?>
                                    </span>
                                    </h4>
								<?php endif; ?>

								<?php foreach ($this->fields AS $field): ?>
                                    <h4>
                                        <span class="feature"><?php echo JText::_($field->name) ?></span> : <span class="value">
			                            <?php if (!isset($row->paid_fields[$field->id]) || $row->paid_fields[$field->id] == 0): echo "<i class='fa fa-times'></i>"; else: echo "<i class='fa fa-check'></i>"; endif; ?>
                                        </span>
                                    </h4>
								<?php endforeach; ?>

                            </div>
                            <div class="price">
								<?php if ($row->price_monthly == 0 || $row->price_annually == 0): ?>
                                    <h4>
										<?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_FREE'); ?>
                                    </h4>
								<?php endif ?>

								<?php if ($row->price_monthly || $row->price_annually): $temp = explode(".", $row->price_monthly);
									if (!isset($temp[1])) $row->price_monthly .= ".00"; else if (strlen($temp[1]) == 1) $row->price_monthly .= "0";
									$temp = explode(".", $row->price_annually);
									if (!isset($temp[1])) $row->price_annually .= ".00"; else if (strlen($temp[1]) == 1) $row->price_annually .= "0";
									?>
									<?php if ($this->params->get('monthly_plan')): ?>
                                        <h4>
											<?php echo $model->priceChange($row->price_monthly, $this->priceParams) ?>
                                            <span class="small d-block"> <?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_MONTHLY') ?></span>
                                        </h4>
									<?php endif ?>
                                    <h5>
										<?php echo $model->priceChange($row->price_annually, $this->priceParams) ?>
                                        <span class="small d-block"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_ANNUALLY') ?></span>
                                    </h5>
								<?php endif; ?>
                            </div>
                            <div class="text-center">
								<?php if ($payments && $buy): ?>
                                    <a href="<?php echo JURI::root() ?>index.php?option=com_jomcomdev&view=registration&comdev_plan_id=<?php echo $row->id ?>&comdev_plan_extension=com_jomdirectory" class="btn btn-lg  <?php if ($row->best_value): ?>btn-secondary <?php else: ?> btn-outline-secondary <?php endif; ?>">
                                        <i class="fa fa-arrow-circle-up"></i> <?php echo JText::_('COM_JOMDIRECTORY_MEMBERSHIP_JOIN') ?>
                                    </a>
								<?php elseif (!$buy): ?>
                                    <a href="<?php echo JURI::root() ?>index.php?option=com_users&view=registration" class="btn btn-primary btn-lg" data-type="ajax">
                                        <i class="fa fa-arrow-circle-up"></i> <?php echo JText::_('COM_JOMDIRECTORY_MEMBERSHIP_JOIN') ?>
                                    </a>
									<?php $buy = 1; ?>
								<?php endif; ?>
								<?php if ($this->plan == $row->group_id): ?>
                                    <span class="btn btn-outline-danger  disabled"> <?php echo JText::_('COM_JOMCOMDEV_NO_PAYMENT_METHOD') ?></span>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
				<?php endif;
				$i++; ?>
			<?php endforeach; ?>
        </div>
    </div>
</section>
