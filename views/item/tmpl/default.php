<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
// no direct access

defined('_JEXEC') or die;
jimport('joomla.version');
$version = new JVersion();
$user = JFactory::getUser();

if (!empty($this->item->images->intro)) {
	$countImg = count($this->item->images->intro);
} else $countImg = 0;
?>

<?php echo $this->loadTemplate('script'); ?>

<section id="jomdirectory-item" class="jomdirectory bootstrap" itemscope itemtype="http://schema.org/LocalBusiness">

    <div class="header mb-3 d-block position-relative">
        <div class="row align-items-center">
            <div class="col-md-auto text-left">
                <h1 class="m-0" itemprop="name"><?php echo $this->item->title ?></h1>
                <meta itemprop="url" content="<?php echo JURI::current(); ?>"/>
                <address class="mt-1 h4" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
					<?php if (!empty($this->item->address) or !empty($this->item->fulladdress)) : ?><i class="mdi mdi-map-marker-outline"></i><?php endif; ?>
					<?php if (!empty($this->item->address)) : ?>
                        <span itemprop="addressLocality"><?php echo implode('<i class="mdi mdi-chevron-right"></i>', $this->item->address) ?></span>
					<?php endif; ?>
					<?php if (!empty($this->item->fulladdress)) : ?>
                        <span itemprop="streetAddress"><i class="mdi mdi-chevron-right"></i><?php echo $this->item->fulladdress ?></span>
					<?php endif; ?>
                </address>
            </div>
            <div class="col justify-content-end">
				<?php if ($this->params->get('enable_reviews')): ?>
                    <div class="pull-right">
                        <div class="d-inline-block mr-3 text-center" itemscope itemtype="http://schema.org/AggregateRating">
							<?php if ($this->item->reviews): ?>
                                <div class="badge-cd red">
				    <!--ivanx--><!--span class="h2"-->
                                    <span class="h5"><?php echo round($this->item->reviews->rate) ?>/5</span>
                                </div>
                                <a href="#reviews" class="d-block">
                                    <span class="reviews-count mt-1"><?php echo $this->item->reviews->count ?><?php echo JText::_('COM_JOMDIRECTORY_SCORE_REVIEWS'); ?></span>
                                </a>
							<?php else: ?>
                                <span class="jd-score-reviews">(<?php echo JText::_('COM_JOMDIRECTORY_SCORE_NOREVIEWS'); ?>)</span>
							<?php endif; ?>
                            <meta itemprop="itemReviewed" content="<?php echo $this->item->title ?>"/>
                            <meta itemprop="bestRating" content="5"/>
                            <meta itemprop="worstRating" content="0"/>
                            <meta itemprop="ratingValue" content="<?php echo round($this->item->reviews->rate) ?>"/>
                            <meta itemprop="reviewCount" content="<?php echo $this->item->reviews->count ?>"/>
                        </div>
			<!--ivanx--><!--a href="#reviews" class="btn btn-outline-info d-inline-block align-top"-->
                        <a href="#reviews" class="btn d-inline-block align-top"><i class="mdi mdi-voice"></i> <?php echo JText::_('COM_JOMDIRECTORY_REVIEW_ADD'); ?></a>
						<?php if ($this->params->get('enable_social')): ?>

			    <!--ivanx--><!--button id="share-btn" type="button" class="btn btn-outline-dark d-inline-block align-top"
                                    data-placement="top"-->
                            <button id="share-btn" type="button" class="btn d-inline-block align-top"
                                    data-placement="top">
                                <i class="mdi mdi-share-variant"></i> <?php echo JText::_('COM_JOMDIRECTORY_SHARE'); ?>
                            </button>
							<?php $uri = JUri::getInstance();
							$uri = $uri->toString();
							$mydoc = JFactory::getDocument();
							$title = $mydoc->getTitle(); ?>
                            <!-- Insert here share buttons -->
                            <div class="popover-html d-none">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-facebook"></i></a>
                                <a href="https://plus.google.com/share?url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-google"></i></a>
                                <a href="https://twitter.com/intent/tweet?text=<?php echo $title; ?>&url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-twitter"></i></a>
                                <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-linkedin"></i></a>
                                <a href="https://pinterest.com/pin/create/button/?url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-pinterest"></i></a>
                                <a href="https://www.reddit.com/login?dest=https://www.reddit.com/submit?title=<?php echo $title; ?>&url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-reddit"></i></a>
                                <a href="https://www.stumbleupon.com/submit?title=<?php echo $title; ?>&url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-stumbleupon"></i></a>
                                <a href="https://del.icio.us/login?log=out&provider=sharethis&title=<?php echo $title; ?>&url=<?php echo $uri; ?>"
                                   class="d-inline-block btn btn-primary"><i class="fa fa-delicious"></i></a>
                            </div>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 pr-0">
            <div class="card slideshow-container">
                <div class="row no-gutters">
					<?php if (isset($this->item->images->intro) && $countImg > 1): ?>
                        <!-- SLIDESHOW -->
						<?php echo $this->loadTemplate('slider'); ?>
					<?php elseif ($countImg == 1): $img = current($this->item->images->intro); ?>
                        <a class="introGallery" data-toggle="lightbox" data-gallery="gallery2" data-title="<?php echo strip_tags($img->description) ?>" href="<?php echo $img->bigger ?>">
                            <img src="<?php echo $img->big ?>" alt="<?php echo $img->title; ?>" class="w-100"/>
                        </a>
					<?php else: ?>
                        <div class="d-flex justify-content-center align-items-center" style="height: 170px;"><i class="fa fa-camera-retro fa-3x" style="color: #e1e1e1"></i></div>
					<?php endif; ?>

                    <?php if ($this->params->get('enable_user_logo')): ?>
						<?php
						if (isset($this->item->images->logo)):
							$logo = current($this->item->images->logo);
							?>
                            <div class="position-absolute m-5">
								<?php echo JHTML::_('image', $logo->big, 'brand', 'style="max-width: ' . $this->params->get('item_image_logo_width') . 'px class="img-thumbnail" itemprop="logo"') ?>
                            </div>
						<?php endif; ?>
					<?php endif; ?>
                </div>
				<?php if ($this->item->youtube_link && $this->params->get('enable_video')): ?>
                    <div class="card-footer clearfix">
                        <div class="position-relative">
                            <a class="modal d-block position-relative" rel="{handler: 'iframe', size: {x: <?php echo $this->params->get('video_width', 600) ?>, y: <?php echo $this->params->get('video_height', 400) ?>}}" href="<?php echo Main_Url::parseYoutube($this->item->youtube_link) ?>">
                                <i class="mdi mdi-youtube-play"></i> <?php echo JText::_('COM_JOMDIRECTORY_VIDEO_WATCH'); ?></a>
                        </div>
                    </div>
				<?php endif; ?>
            </div>

            <div class="mt-3">
				<?php echo $this->loadTemplate('tabs'); ?>
            </div>

			<?php if ($this->item->service): ?>
                <div class="card card-body mt-3 booking-services">
					<?php echo $this->loadTemplate('service'); ?>
                </div>
			<?php endif; ?>

			<?php if (isset($this->item->images->gallery)): ?>
                <div class="card card-body mt-3 d-block clearfix">
                    <h3><?php echo JText::_('COM_JOMDIRECTORY_TAB_TITLE_IMAGE'); ?></h3>
					<?php foreach ($this->item->images->gallery AS $img): ?>
                        <a href="<?php echo $img->bigger ?>" data-toggle="lightbox" data-gallery="gallery" class="highslide d-block float-left mr-3" data-title="<?php echo strip_tags($img->description) ?>">
							<?php echo JHTML::_('image', $img->big, $img->title, 'class="img-thumbnail mr-1 mb-1" style="max-width: ' . $this->params->get('image_gallery_width') . 'px" itemprop="image"') ?>
                        </a>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>

			<?php if ($this->params->get('enable_reviews')): ?>
                <div id="reviews" class="card mt-3 reviews-container">
					<?php echo $this->loadTemplate('reviews'); ?>
                </div>
			<?php endif; ?>

        </div>
        <div class="col-md-4">
	        <?php if (($this->item->maps_lat && $this->item->maps_lng) or !empty($this->item->address) or !empty($this->item->fulladdress) or !empty($this->item->phone) or  !empty($this->item->webpage) ): ?>
            <div class="card card-body map-container">
				<?php if ($this->params->get('enable_map')): ?>
					<?php if ($this->item->maps_lat && $this->item->maps_lng): ?>
                        <div id="jd-item-box-maps" style="width: 100%; height: 200px;"></div>
					<?php endif; ?>
				<?php endif; ?>
                <ul class="list-group">
					<?php if (!empty($this->item->address)) : ?>
                    <li class="list-group-item">
						<?php if (!empty($this->item->address) or !empty($this->item->fulladdress)) : ?><i class="mdi mdi-map-marker-outline"></i><?php endif; ?>
                        <span itemprop="addressLocality"><?php echo implode('<i class="mdi mdi-chevron-right"></i>', $this->item->address) ?></span>
						<?php endif; ?>
						<?php if (!empty($this->item->fulladdress)) : ?>
                        <span itemprop="streetAddress"><i class="mdi mdi-chevron-right"></i><?php echo $this->item->fulladdress ?></span>
                    </li>
				<?php endif; ?>
					<?php if (!empty($this->item->phone)) : ?>
                        <li class="list-group-item"><i class="mdi mdi-phone"></i> <?php echo $this->item->phone ?></li>
					<?php endif; ?>
					<?php if (!empty($this->item->webpage)) : ?>
                        <li class="list-group-item"><i class="mdi mdi-web"></i>
                            <a href="<?php echo $this->item->webpage ?>" title="<?php echo $this->item->webpage ?>" target="_blank"><?php echo $this->item->webpage ?></a>
                        </li>
					<?php endif; ?>
                </ul>
            </div>
	        <?php endif; ?>
            <div class="card-block bg-transparent mt-3 text-center">
				<?php if ($this->params->get('enable_save')): ?>
                    <span id="jd-item-box-toolbar-fav">
					<?php if (!JomcomdevHelperRemember::checkFavorite($this->item->id)): ?>
                        <a class="jd-ajax-json mr-1 btn btn-light " href="<?php echo JRoute::_("index.php?option=com_jomcomdev&task=ajax.addFavorite&format=json&id=" . $this->item->id); ?>" rel="{cnt: 'jd-item-box-toolbar-fav', type: 'favorite'}" style="color: #696969 !important;"> <!--ivanx added style-->
                            <i class="fa fa-heart-o"></i> <?php echo JText::_('COM_JOMDIRECTORY_SAVE'); ?>
                        </a>
					<?php else: ?>
                        <span class="jd-ajax-json btn btn-primary disabled mr-1"><i class="fa fa-heart-o"></i></span>
					<?php endif; ?>
                    </span>
				<?php endif; ?>
				<?php if ($this->params->get('enable_taf')): ?>
                    <button data-toggle="modal" data-target="#modalTafForm" class="tellAFriendBox mr-1 btn  btn-light" title="<?php echo $this->item->title ?>" style="color: #696969 !important;"><!--ivanx-->
                        <i class="fa fa-envelope-o"></i> <?php echo JText::_('COM_JOMDIRECTORY_TAF'); ?>
                    </button>
				<?php endif; ?>
				<?php if ($this->params->get('enable_print')): ?>
                    <a rel="nofollow" class="btn  btn-light" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" href="<?php echo JRoute::_(JomdirectoryHelperRoute::getArticleRoute($this->item->id, $this->item->alias, $this->item->categories_id, false, true, 'print')); ?>" style="color: #696969 !important;"> <!--ivanx-->
                        <i class="fa fa-print"></i> <?php echo JText::_('COM_JOMDIRECTORY_PRINT'); ?>
                    </a>
				<?php endif; ?>
            </div>

			<?php if ($this->item->fields->showGroup("paiditems") or $this->item->fields->showGroup("paiditem")): ?>
                <div class="card card-body bg-secondary text-white mt-3 paid-fields-container">
					<?php echo $this->item->fields->showGroup("paiditems") ?>
					<?php echo $this->item->fields->showGroup("paiditem") ?>
                </div>
			<?php endif; ?>

			<?php if ($this->params->get('enable_contact')): ?>
                <div class="card card-body mt-3 contact-container">
                    <h4>
						<?php echo JText::_('COM_JOMDIRECTORY_CONTACT_OWNER'); ?>
						<?php echo $this->item->title ?>
                    </h4>
					<?php if ($this->params->get('enable_contact')): ?>
                        <form id="jd-email-form" name="jd-email-form" method="post" action="<?php echo JRoute::_('index.php?option=com_jomdirectory&task=form.contact') ?>" class="form-validate">
                            <div class="jd-email-error" id="jd-email-error"></div>
                            <div class="form-group">
								<?php echo $this->formContact->getLabel('name'); ?>
								<?php echo $this->formContact->getInput('name'); ?>
                            </div>
                            <div class="form-group">
								<?php echo $this->formContact->getLabel('email'); ?>
								<?php echo $this->formContact->getInput('email'); ?>
                            </div>
                            <div class="form-group">
								<?php echo $this->formContact->getLabel('phone'); ?>
								<?php echo $this->formContact->getInput('phone'); ?>
                            </div>

                            <div class="form-group">
								<?php echo $this->formContact->getLabel('message'); ?>
								<?php echo $this->formContact->getInput('message'); ?>
								<?php echo $this->formContact->getInput('content_id'); ?>
                            </div>
                            <div class="form-group">
								<?php if ($this->params->get('enable_captcha') && ($this->params->get('enable_captcha_reg') || $user->guest)): ?>
                                    <div id="jd-email-captcha"></div>
								<?php endif; ?>
                            </div>
                            <div class="jd-contact-footer clearfix">
                                <div class="float-left">
									<?php if ($this->params->get('enable_terms')): ?>
                                        <div class="jd-email-terms-agree my-3">
											<?php echo $this->formContact->getInput('terms2'); ?> <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_ACCEPT'); ?>
                                            <a class="modal modal-block position-relative d-inline" rel="{handler: 'iframe', size: {x: 600, y: 450}}" href="<?php echo JRoute::_('index.php?Itemid=' . $this->params->get('contact_terms') . '&tmpl=component') ?>"> <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_TERMS'); ?></a>
											<?php echo JText::_('COM_JOMDIRECTORY_CONTACT_AND'); ?>
                                            <a class="modal modal-block position-relative d-inline" rel="{handler: 'iframe', size: {x: 600, y: 450}}" href="<?php echo JRoute::_('index.php?Itemid=' . $this->params->get('contact_privacy') . '&tmpl=component') ?>"> <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_PRIV'); ?></a>
                                        </div>
									<?php else: ?>
                                        <input type="hidden" value="1" id="jform_terms2" name="jform[terms]">
									<?php endif; ?>
                                </div>
                                <input type="hidden" value="<?= $this->item->userEmail ?>" name="jform[email_owner]">
                                <div class="float-right">
                                    <button type="submit" value="" class="btn btn-primary"><i class="fa fa-send-o"></i> <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_SENDEMAIL'); ?></button>
                                </div>
                            </div>
                        </form>
					<?php endif; ?>
                </div>
			<?php endif; ?>

			<?php if ($this->params->get('enable_features')): ?>
                <div class="card card-body mt-3 features-container">
                    <h4><?php echo JText::_('COM_JOMDIRECTORY_KEY_INFO'); ?></h4>
                    <div class="clearfix">
						<?php echo $this->item->fields->showGroup("item") ?>
                    </div>
                </div>
			<?php endif; ?>

			<?php if ($this->params->get('enable_tags')): ?>
                <div class="card card-body mt-3">
                    <h4 class="mb-0"><?php echo JText::_('COM_JOMDIRECTORY_TAGS'); ?> </h4>
					<?php $jVerArr = explode('.', JVERSION);
					if ($jVerArr[0] >= '3'):
						?>
						<?php $tagsData = $this->item->tags->getItemTags('com_jomdirectory.content', $this->item->id); ?>
						<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
						<?php echo $this->item->tagLayout->render($tagsData); ?>
					<?php endif; ?>
                </div>
			<?php endif; ?>

        </div>
    </div>
</section><!--END OF JD-ITEM-WRAPPER -->


<?php if ($this->params->get('enable_taf')): ?>
    <div class="modal fade" id="modalTafForm" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog" role="document">
            <form method="post" action="?task=form.taf" class="form-validate modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="ModalCenterTitle"><?php echo JText::_('COM_JOMDIRECTORY_TAF_TITLE'); ?></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
						<?php echo $this->formTellAFriend->getLabel('name'); ?>
						<?php echo $this->formTellAFriend->getInput('name'); ?>
                    </div>
                    <div class="form-group">
						<?php echo $this->formTellAFriend->getLabel('email'); ?>
						<?php echo $this->formTellAFriend->getInput('email'); ?>
                    </div>
                    <div class="form-group">
						<?php echo $this->formTellAFriend->getLabel('email_rec'); ?>
						<?php echo $this->formTellAFriend->getInput('email_rec'); ?>
                    </div>
                    <div class="form-group">
						<?php echo $this->formTellAFriend->getLabel('subject'); ?>
						<?php echo $this->formTellAFriend->getInput('subject'); ?>
                    </div>
                    <div class="form-group">
						<?php echo $this->formTellAFriend->getLabel('messagetaf'); ?>
						<?php echo $this->formTellAFriend->getInput('messagetaf'); ?>
                    </div>
					<?php if ($this->params->get('enable_captcha') && ($this->params->get('enable_captcha_reg') || $user->guest)): ?>
                        <div id="jd-taf-captcha"></div>
					<?php endif; ?>
                </div>
                <div class="modal-footer">
					<?php echo JHtml::_('form.token'); ?>
                    <input type="submit" class="btn btn-primary mb-0" value="<?php echo JText::_('COM_JOMDIRECTORY_TAF_SEND'); ?>">
					<?php echo $this->formTellAFriend->getInput('content_id'); ?>
					<?php echo $this->formTellAFriend->getInput('user_id'); ?>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>


<?php if ($this->params->get('enable_captcha') && ($this->params->get('enable_captcha_reg') || $user->guest)): ?>
	<?php $plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
	$params = new JRegistry($plugin->params); ?>
    <script>
		var CaptchaCallback = function () {
			<?php if($this->params->get('enable_reviews') && ($this->params->get('reviews_guest_allow') || $user->id)):?>
			grecaptcha.render('jd-review-captcha', {'sitekey': '<?php echo $params->get('public_key', ''); ?>'});
			<?php endif;?>
			<?php if($this->params->get('enable_contact')):?>
			grecaptcha.render('jd-email-captcha', {'sitekey': '<?php echo $params->get('public_key', ''); ?>'});
			<?php endif;?>
			<?php if ($this->params->get('enable_taf')):?>
			grecaptcha.render('jd-taf-captcha', {'sitekey': '<?php echo $params->get('public_key', ''); ?>'});
			<?php endif;?>
		};
    </script>
    <script src="//www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit" async defer></script>
<?php endif; ?>

