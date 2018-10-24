<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
JHtmlBehavior::framework('more');
?>
<div id="jomdirectory-admin" class="jomdirectory dashboard">

	<?php echo $this->loadTemplate('header'); ?>

    <div class="border-bottom mt-3 clearfix">
        <div class="float-left">
            <h2><?php echo JText::_('COM_JOMDIRECTORY_ADM_DASHBOARD') ?></h2>
        </div>
        <div class="float-right mt-1">
            <p class="text-uppercase"><span><?php echo JText::sprintf('COM_JOMDIRECTORY_ADM_MEMBERSHIP_STATUS', $this->plan_name); ?></span></p>
        </div>
    </div>
    <div id="admin-body" class="my-3">
		<?php if ($this->welcome['title']): ?>
            <div class="d-block position-relative card card-body">
				<?php echo $this->welcome['text'] ?>
            </div>
		<?php endif; ?>
        <div class="my-3 clearfix">
            <div class="card card-body z-depth-1 red lighten-2 d-inline-block mr-3 my-1 p-4 text-white" style="width: 200px;">
                <div class="clearfix">
                    <div class="d-inline-block font-weight-bold h4"><?php echo $this->listings_count ?></div>
                    <div class="float-right btn-circle red lighten-3">
                        <i class="fa fa-copy"></i>
                    </div>
                </div>
                <div class="text-uppercase mt-3"><?php echo JText::_('COM_JOMDIRECTORY_ADM_TOTAL_L') ?></div>
            </div>
            <div class="card card-body blue lighten-1 d-inline-block mr-3 my-1 p-4 text-white" style="width: 200px;">
                <div class="clearfix">
                    <div class="d-inline-block font-weight-bold h4"><?php echo $this->approved_count ?></div>
                    <div class="float-right btn-circle blue lighten-2">
                        <i class="fa fa-send-o"></i>
                    </div>
                </div>
                <div class="text-uppercase mt-3"><?php echo JText::_('COM_JOMDIRECTORY_ADM_PENDING_L') ?></div>
            </div>
            <div class="card card-body green lighten-2 d-inline-block mr-3 my-1 p-4 text-white" style="width: 200px;">
                <div class="clearfix">
                    <div class="d-inline-block font-weight-bold h4"><?php echo $this->reviews_count ?></div>
                    <div class="float-right btn-circle green lighten-3">
                        <i class="fa fa-comments-o"></i>
                    </div>
                </div>
                <div class="text-uppercase mt-3"><?php echo JText::_('COM_JOMDIRECTORY_ADM_TOTAL_REVIEWS') ?></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><?php echo JText::_('COM_JOMDIRECTORY_ADM_TOTAL_REPORT') ?></div>
            <div class="card-body">
                <div id="chart" style="height:320px;"></div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-6">
                <div class="d-block position-relative card h-100">
                    <div class="card-header"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP') ?></div>
                    <div class="card-body">
                        <h3><span class="badge-cd amber darken-2 float-right"><?php echo JText::_($this->plan_name) ?></span></h3>
                        <div class="my-2">
                            <label class="d-inline-block h6"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_EXPIRY') ?></label>:
							<?php $expiry = str_replace('"', '', $this->plan_usage['expiry']); ?>
							<?php if (strpos($expiry, '#blocked') !== false): ?>
								<?php echo "<span class='badge-cd badge-danger'>" . JText::_('COM_JOMDIRECTORY_ADM_PLAN_BLOCKED') . "</span>"; ?>
							<?php elseif (strpos($expiry, 'never expires') !== false): ?>
								<?php echo "<span class='badge-cd badge-success'>" . JText::_('COM_JOMDIRECTORY_ADM_PLAN_NEVER_EXPIRES') . "</span>"; ?>
							<?php else: ?>
								<?php echo $expiry; ?>
							<?php endif; ?>
                        </div>
                        <div class="mb-2">
                            <label class="d-inline-block h6"> <?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_LISTINGS') ?></label>:
							<?php echo $this->plan_usage['listings'] ?>/<?php echo $this->plan_limits->listings_nr ?>
                        </div>
                        <div class="mb-2">
                            <label class="d-inline-block h6"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_PREMIUM_LISTINGS') ?></label>:
							<?php echo $this->plan_usage['featured'] ?>/<?php echo $this->plan_limits->premium_nr ?>
                        </div>
						<?php if ($this->params->get('product_display')): ?>
                            <div class="mb-2">
                                <label class="d-inline-block h6"><?php echo JText::_('COM_JOMDIRECTORY_PRODUCTS') ?></label>:
								<?php echo $this->plan_usage['products'] ?>/<?php echo $this->plan_limits->listings_nr ?>
                            </div>
                            <div class="mb-2">
                                <label class="d-inline-block h6"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_PREMIUM_PRODUCTS') ?></label>:
								<?php echo $this->plan_usage['featured_products'] ?>/<?php echo $this->plan_limits->premium_nr ?>
                            </div>
						<?php endif; ?>
						<?php if (!$this->plan_limits->best): ?>
                            <div class="text-center">
                                <hr/>
                                <a href="<?php echo JRoute::_('?option=com_jomdirectory&view=admin_membership') ?>" class="btn btn-primary"><?php echo JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UPGRADE_NOW') ?> <i class="fa fa-chevron-up"></i></a>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-block position-relative card h-100">
                    <div class="card-header"><?php echo JText::_('COM_JOMDIRECTORY_ADM_POFILE') ?></div>
                    <div class="card-body clearfix">
                        <div class="float-left">
                            <div class="mb-3">
                                <label class="d-block h6"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_NAME') ?></label> <?php echo $this->user->name ?>
                            </div>
                            <div class="mb-3">
                                <label class="d-block h6"><?php echo JText::_('COM_JOMDIRECTORY_PROFILE_REG_DATE') ?></label> <?php echo JHtml::_('date', $this->user->registerDate, JText::_('DATE_FORMAT_LC3')); ?>
                            </div>
                            <div class="mb-3">
                                <label class="d-block h6"><?php echo JText::_('COM_JOMDIRECTORY_PROFILE_LAST_VISIT') ?></label> <?php echo JHtml::_('date', $this->user->lastvisitDate, JText::_('DATE_FORMAT_LC3')); ?>
                            </div>
                        </div>
                        <div class="float-right">
							<?php echo JHTML::_('image', JURI::root() . $this->userImage, 'profile', 'class="mt-3 mr-3 img-thumbnail"') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php if ($this->params->get('enable_reviews')): ?>
			<?php if ($this->params->get('admin_allow_reviews')): ?>
                <div class="d-block position-relative card">
                    <div class="card-header"><?php echo JText::_('COM_JOMDIRECTORY_ADM_APPROVE_COMMENTS') ?></div>
                    <div class="card-body">
                        <div class="text-right">
							<?php echo $this->toolbar ?>
                        </div>
                        <form action="" method="post" name="adminForm" id="adminForm">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/></th>
                                    <th><?php echo JText::_('COM_JOMDIRECTORY_ADMIN_TITLE') ?></th>
                                    <th><?php echo JText::_('COM_JOMDIRECTORY_MESSAGE') ?></th>
                                    <th><?php echo JText::_('COM_JOMDIRECTORY_FIELD_CREATED_LABEL') ?></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php $i = 0;
								foreach ($this->reviews as $row): ?>
                                    <tr>
                                        <td class="text-nowrap">
											<?php echo JHTML::_('grid.id', $i, $row->id), '&nbsp;'; ?>
											<?php echo JHtml::_('jgrid.published', $row->approved, $i, 'admin_dashboard.', 1); ?>
                                        </td>
                                        <td class="col-md-auto">
											<?php $link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($row->content_id, $row->alias, $row->categories_id, $row->categories_address_id)); ?>
											<?php echo JHTML::_('link', $link, $row->title); ?>
                                        </td>
                                        <td><?php echo $row->text; ?></td>
                                        <td class="text-nowrap">
											<?php echo $row->name ?> <?php echo $row->username ?><br> <span><?php echo $row->date_modified ?></span>
                                        </td>
                                    </tr>
									<?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                            <input type="hidden" name="task" value=""/> <input type="hidden" name="hidemainmenu" value=""/> <input type="hidden" name="boxchecked" value=""/>
                        </form>
                        <div class="clearfix">
							<?php if ($this->pagination->get('pages.total') > 1): ?>
                                <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
		<?php endif; ?>
    </div>
</div>
