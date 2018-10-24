<?php
/*------------------------------------------------------------------------
# com_jomcomdev - JomComdev
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
$user = JFactory::getUser();
?>

<div id="reviews-wrapper" itemprop="review" itemscope itemtype="http://schema.org/Review">
    <div class="position-relative clearfix mb-3 reviews-summary">
        <div class="card-body clearfix">
            <h3><?php echo JText::_('COM_JOMCOMDEV_REVIEWS'); ?> (<?php echo isset($this->item->reviews->items) ? count($this->item->reviews->items) : 0 ?>)</h3>
			<?php if (isset($this->item->reviews->rates)): ?>
				<?php foreach ($this->item->reviews->rates as $k => $el): ?>
                    <div class="form-group float-left mr-3">
                        <label class="text-dark font-weight-bold"><?php echo JText::_($k); ?></label> <input value="<?php echo (int)$el; ?>" type="text" readonly class="dial_st" data-width="50" data-height="50" data-displayPrevious="true" data-thickness=".2" autocomplete="off">
                    </div>
				<?php endforeach; ?>
			<?php endif; ?>
        </div>
		<?php if (isset($this->item->reviews->items)): ?>
			<?php if (isset($this->item->reviews->recommended)): ?>
                <a class="modal modal-block d-block position-absolute right-0 top-0 m-3" rel="{handler: 'iframe', size: {x: 600, y: 450}}" href='/index.php?option=com_jomdirectory&view=item&layout=chart&id=<?php echo JRequest::getVar('id'); ?>&alias=<?php echo $this->item->alias; ?>&tmpl=component'>
                    <i class="mdi mdi-chart-pie mdi-36px"></i></a>
			<?php endif; ?>
		<?php endif; ?>
    </div>
    <hr/>
	<?php if (isset($this->item->reviews->items)): ?>
		<?php foreach ($this->item->reviews->items AS $review): ?>
            <div class="reviews-body card-body">
                <div class="row mb-3">
                    <div class="col-md-3 text-center" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                        <i class="mdi mdi-account mdi-48px"></i>
                        <div class="reviews-user mt-1" itemprop="author"><?php echo $review->username ?></div>
                        <div class="reviews-score hasTip" <?php if (isset($this->item->reviews->rates)): ?>title="<?php foreach ($review->rates as $k => $el): ?><?php echo JText::_($k); ?>: <?php echo (int)$el; ?><br /><?php endforeach; ?>"<?php endif; ?>>
                            <input value="<?php echo round($review->rate * 2) / 2 ?>" type="text" class="dial" data-width="50" data-height="50" data-displayPrevious="true" data-thickness=".2" autocomplete="off" autocomplete="off" readonly>
                        </div>
                        <meta itemprop="worstRating" content="0">
                        <meta itemprop="bestRating" content="5"/>
                        <meta itemprop="ratingValue" content="<?php echo round($review->rate * 2) / 2 ?>"/>
                    </div>
                    <div class="col-md-9">
                        <div class="mt-3 small">
							<?php echo JText::_('COM_JOMCOMDEV_REVIEWS_DATE') ?>:
							<?php $date = new JDate($review->date_modified);
							echo $date->format(JText::_('DATE_FORMAT_LC3')); ?>
                            <meta itemprop="datePublished" content="<?php $date = new JDate($review->date_modified);
							echo $date->format(JText::_('DATE_FORMAT_LC2')); ?>">
                        </div>
                        <h3 class="my-1"><?php echo $review->title ?></h3>
                        <div class="my-3" itemprop="description"><?php echo $review->text ?></div>
                        <div class="clearfix reviews-body-footer">
							<?php if ($review->recommended): ?>
                                <div class="float-left">
									<?php $j = 0;
									echo "<label class='d-inline-block'>" . JText::_('COM_JOMCOMDEV_REVIEW_RECOMMENDED') . "</label>: ";
									foreach ($review->recommended as $el) {
										echo "<span class='badge-cd badge-info disabled ml-1'>" . JText::_($el) . "</span>";
										$j++;
									}
									?>
                                </div>
							<?php endif; ?>
							<?php if ($this->params->get('reviews_likes')): ?>
                                <div id="js-likes-<?php echo $review->id ?>" class="float-right">
									<?php if ($review->voted): ?>
                                        <span class="text-success"><?php echo JText::_('COM_JOMCOMDEV_REVIEWS_THANKS') ?></span>
									<?php else: ?>
										<?php echo JText::_('COM_JOMCOMDEV_REVIEWS_LIKES') ?>
                                        <a class="jd-ajax-json-jq" rel="{cnt: 'js-likes-<?php echo $review->id ?>', type: 'vote'}" href="<?php echo JRoute::_('index.php?option=com_jomcomdev&task=ajax.voteReviewsYes&format=json&id=' . $review->id) ?>"><i class="fa fa-check-circle-o"></i></a>
                                        <a class="jd-ajax-json-jq" rel="{cnt: 'js-likes-<?php echo $review->id ?>', type: 'vote'}" href="<?php echo JRoute::_('index.php?option=com_jomcomdev&task=ajax.voteReviewsNo&format=json&id=' . $review->id) ?>"><i class="fa fa-times-circle-o"></i></a>
									<?php endif; ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
	<?php else: ?>
        <div class="card card-body p-3 green lighten-5 mt-3"><?php echo JText::_('COM_JOMCOMDEV_NOREVIEWS') ?></div>
	<?php endif; ?>
    <hr/>
	<?php if ((!$this->params->get('reviews_guest_allow') && !$user->id)) : ?>
        <div class="card card-body p-3 green lighten-5 mt-3"><?php echo JText::_('COM_JOMCOMDEV_REVIEWS_LOGIN') ?></div>
	<?php else: ?>
        <form id="reviews-form" name="reviews-form" method="post" action="<?php echo JRoute::_('index.php?option=com_jomdirectory&task=form.reviews') ?>" class="form-validate">
            <div id="reviews-add" class="card-body">
                <h2><?php echo JText::_('COM_JOMCOMDEV_REVIEW_WRITE'); ?></h2>
                <div class="row m-0">
                    <div class="col-md-5">
                        <div class="form-group">
							<?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?>
                        </div>
						<?php if (!$user->id): ?>
                            <div class="form-group">
								<?php echo $this->form->getLabel('username'); ?>
								<?php echo $this->form->getInput('username'); ?>
                            </div>
                            <div class="form-group">
								<?php echo $this->form->getLabel('email'); ?>
								<?php echo $this->form->getInput('email'); ?>
                            </div>
						<?php else: ?>
                            <input type="hidden" name="jform[email]" value="<?php echo JFactory::getUser()->email ?>">
						<?php endif; ?>
                        <div class="form-group">
							<?php echo $this->form->getLabel('text'); ?>
							<?php echo $this->form->getInput('text'); ?>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group clearfix">
                            <div class="d-block font-weight-bold">
								<?php echo $this->form->getLabel('rates'); ?>
                            </div>
							<?php echo $this->form->getInput('rates'); ?>
                        </div>
						<?php if ($this->form->getInput('recommended')): ?>
                            <div class="form-group">
                                <div class="d-block font-weight-bold">
									<?php echo $this->form->getLabel('recommended'); ?>
                                </div>
								<?php echo $this->form->getInput('recommended'); ?>
                            </div>
						<?php endif; ?>
						<?php if ($this->params->get('enable_captcha') && ($this->params->get('enable_captcha_reg') || $user->guest)): ?>
                            <div id="jd-review-captcha" class="my-3"></div>
						<?php endif; ?>
                    </div>
                </div>
                <div class="my-3 clearfix">
					<?php if ($this->params->get('reviews_terms')): ?>
                        <div class="float-left  form-group form-check">
                            <div class="d-block">
								<?php echo $this->form->getLabel('terms'); ?>
                            </div>
							<?php echo $this->form->getInput('terms'); ?> <?php echo JText::_('COM_JOMCOMDEV_CONTACT_ACCEPT'); ?>
                            <a class="" rel="{handler: 'iframe', size: {x: 600, y: 450}}" href="<?php echo JRoute::_('index.php?Itemid=' . $this->params->get('reviews_terms_article') . '&tmpl=component') ?>"><?php echo JText::_('COM_JOMCOMDEV_CONTACT_TERMS'); ?></a>
                        </div>
					<?php endif; ?>
                    <input type="hidden" name="jform[content_id]" id="jform_content_id" value="<?php echo $this->item->id ?>">
					<?php echo $this->form->getInput('user_id'); ?>
                    <button type="submit" class="btn btn-primary float-right"><?php echo JText::_('COM_JOMCOMDEV_REVIEW_ADD'); ?></button>
                </div>
            </div>
        </form>
	<?php endif; ?>
</div> 

       