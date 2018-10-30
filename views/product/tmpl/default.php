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

if (!empty($this->item->images->intro)) {
	$countImg = count($this->item->images->intro);
} else $countImg = 0;
?>

<?php echo $this->loadTemplate('script'); ?>

<div id="jomdirectory-product" class="jomdirectory bootstrap" itemscope itemtype="http://schema.org/Product">
    <div class="row no-gutters">
        <div class="col-md-6 pr-md-5">
            <!-- SLIDESHOW -->
			<?php if (isset($this->item->images->intro)) {
				$img = current($this->item->images->intro);
			}
			?>
			<?php if (isset($this->item->images->intro) && $countImg > 1): ?>
				<?php echo $this->loadTemplate('slider'); ?>
			<?php elseif ($countImg == 1): $img = current($this->item->images->intro); ?>
                <a class="introGallery" data-toggle="lightbox" data-gallery="gallery2" data-title="<?php echo strip_tags($img->description) ?>" href="<?php echo $img->bigger ?>">
                    <img src="<?php echo $img->big ?>" alt="<?php echo $img->title; ?>" itemprop="image"/></a>
			<?php else: ?>
				<?php echo JHTML::_('image', 'components/com_jomcomdev/assets/images/nophoto.jpg', 'img', 'class=""') ?>
			<?php endif; ?>

			<?php if ($this->params->get('enable_user_logo')): ?>
				<?php
				if (isset($this->item->images->logo)):
					$logo = current($this->item->images->logo);
					?>
                    <div class="jd-itemLogo">
						<?php echo JHTML::_('image', $logo->big, 'brand', 'style="max-width: ' . $this->params->get('item_image_logo_width') . 'px" itemprop="logo"') ?>
                    </div>
				<?php endif; ?>
			<?php endif; ?>
        </div>
        <div class="col-md-6">
            <section class="jd-product-section mb-3">
                <h1 class="m-0 jd-product-title" itemprop="name"><?php echo $this->item->title ?></h1>
                <div class="sub-header clearfix">
					<?php if ($this->params->get('enable_reviews')): ?>
                        <div class="justify-content-center align-items-center flex-column my-1 float-left" itemscope="" itemtype="http://schema.org/AggregateRating">
							<?php if ($this->item->reviews): ?>
                                <div class="d-inline-block mr-1" itemprop="ratingValue">
                                    <input value="<?php echo round($this->item->reviews->rate * 2) /2; ?>" type="text" readonly class="dial_st" data-width="50" data-height="50" data-displayPrevious="true" data-fgColor="#a88e4b" data-thickness=".2" autocomplete="off">
                                </div>
                                <div class="d-inline-block">
                                    <a href="#reviews">
                                        <span class="d-inline-block mr-1"><?php echo $this->item->reviews->count ?></span><?php echo JText::_('COM_JOMDIRECTORY_SCORE_REVIEWS'); ?>
                                    </a>
                                </div>
                                <meta itemprop="itemReviewed" content="<?php echo $this->item->title ?>"/>
                                <meta itemprop="bestRating " content="5"/>
                                <meta itemprop="worstRating " content="0"/>
                                <meta itemprop="ratingValue" content="<?php echo round($this->item->reviews->rate * 2) /2; ?>"/>
                                <meta itemprop="reviewCount" content="<?php echo $this->item->reviews->count ?>"/>
							<?php else: ?>
                                <span class="d-inline-block"> (<?php echo JText::_('COM_JOMDIRECTORY_SCORE_NOREVIEWS'); ?>)</span>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                    <div class="float-right">
						<?php if ($this->item->youtube_link && $this->params->get('product_video')): ?>
                            <div class="float-right">
                                <a class="modal btn btn-sm btn-light px-2  mx-1 my-0" rel="{handler: 'iframe', size: {x: <?php echo $this->params->get('video_width', 600) ?>, y: <?php echo $this->params->get('video_height', 400) ?>}}" href="<?php echo Main_Url::parseYoutube($this->item->youtube_link) ?>"><i class="fa fa-youtube"></i></a>
                            </div>
						<?php endif; ?>
						<?php if ($this->params->get('product_save')): ?>
                            <div id="jd-item-box-toolbar-fav<?php echo $this->item->id ?>" class="float-right">
								<?php if (!JomcomdevHelperRemember::checkFavorite($this->item->id, 'com_jomdirectory_products')): ?>
                                    <a class="jd-ajax-json btn btn-sm btn-light px-2  mx-1 my-0" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&extension=com_jomdirectory_products&format=json&id=<?php echo $this->item->id ?>" rel="{cnt: 'jd-item-box-toolbar-fav<?php echo $this->item->id ?>', type: 'favorite'}" style="color: #333333 !important;"> <!--ivanx style-->
                                        <i class="fa fa-heart-o"></i>
                                    </a>
								<?php else: ?>
                                    <span class="btn btn-sm btn-primary px-2  mx-1 my-0 disabled"><i class="fa fa-heart"></i></span>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
						<?php if ($this->params->get('product_compare')): ?>
                            <div id="jd-item-box-toolbar-comp<?php echo $this->item->id ?>" class="float-right ml-1">
								<?php if (!JomcomdevHelperRemember::checkFavorite($this->item->id, 'com_jomdirectory_products_compare')): ?>
                                    <a class="jd-ajax-json btn btn-sm btn-light px-2 m-0" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&extension=com_jomdirectory_products_compare&format=json&id=<?php echo $this->item->id ?>" rel="{cnt: 'jd-item-box-toolbar-comp<?php echo $this->item->id ?>', type: 'compare'}" style="color: #333333 !important;"><!--ivanx added style -->
                                        <i class="fa fa-copy"></i>
                                    </a>
								<?php else: ?>
                                    <span class="btn btn-sm btn-secondary px-2 m-0 disabled"><i class="fa fa-copy"></i></span>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </section>
            <section class="jd-product-section mb-3">
                <div class="jd-prduct-meta my-1">
					<?php if ($this->params->get('product_availability')): ?>
                        <div class="d-block my-2 pb-2 border-bottom">
                            <span class="col-6 d-inline-block"><?php echo JText::_('COM_JOMDIRECTORY_PRODUCT_AVAILABILITY'); ?></span> <span class="col-6 <?php if ($this->item->quantity != 0): ?>text-success in-stock<?php else: ?>text-success out-stock<?php endif; ?>"> <?php echo $this->item->quantity; ?>
								<?php if ($this->item->quantity == 0): ?>
									<?php echo JText::_('COM_JOMDIRECTORY_PRODUCT_OUT_STOCK'); ?>
								<?php else: ?>
									<?php echo JText::_('COM_JOMDIRECTORY_PRODUCT_IN_STOCK'); ?>
								<?php endif; ?>
                            </span>
                        </div>
					<?php endif; ?>
					<?php if ($this->params->get('product_sku')): ?>
                        <div class="d-block my-2">
                            <span class="col-6 d-inline-block"><?php echo JText::_('COM_JOMDIRECTORY_PRODUCT_SKU'); ?></span> <span itemprop="sku" class="col-6 "><?php echo $this->item->sku; ?></span>
                        </div>
					<?php endif; ?>
                </div>
            </section>
			<?php if ($this->params->get('product_short_desc')): ?>
                <section class="jd-product-section mb-3" itemprop="description">
					<?php echo $this->item->introtext ?>
                </section>
			<?php endif; ?>
            <div class="price-container clearfix">
				<?php if ($this->params->get('product_price')): ?>
                    <div class="float-left" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<?php if ($this->item->price_old != 0): ?>
                            <span class="price-sale d-block mb-1"><del><?php echo $this->item->price_old; ?><?php $this->params->get('adm_currency', 'EU'); ?></del></span>
						<?php endif; ?>
                        <span class="price" itemprop="price" content="<?php echo $this->item->price; ?>"><?php echo $this->item->price; ?></span>
                    </div>
				<?php endif; ?>
                <div class="clearfix float-right">
		            <?php if ($this->params->get('enable_social')): ?>
                        <button id="share-btn" type="button" class="btn btn-sm align-top" data-placement="top">
                        <!--button id="share-btn" type="button" class="btn btn-sm btn-outline-dark align-top" data-placement="top"--><!--ivanx-->
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
                    <a href="#reviews" class="btn btn-sm align-top" onclick="$('#reviews-tab').trigger('click')"><i class="mdi mdi-voice"></i> <?php echo JText::_('COM_JOMDIRECTORY_REVIEW_ADD'); ?></a>
                    <!--a href="#reviews" class="btn btn-sm btn-outline-info align-top" onclick --><!--ivanx-->
                </div>
            </div>
            <section class="jd-product-footer clearfix">
                <form enctype="multipart/form-data" method="post" class="cart mt-3 float-left">
					<?php if ($this->params->get('cart')): ?>
                        <a data-property="add-to-cart" data-object="{id: <?= $this->item->id ?>, title: '<?= htmlspecialchars($this->item->title) ?>', price: <?= $this->item->price_int ?>, image: '<?php if (!empty($img->small)) echo $img->small ?>'}" class="btn btn-lg btn-secondary">
                            <i class="fa fa-shopping-cart"></i> <?php echo JText::_('COM_JOMDIRECTORY_CART_ADD'); ?></a>
					<?php endif; ?>
                </form>
                <div class="text-muted float-right mt-5"><i class="mdi mdi-folder-multiple-outline"></i> <?php echo $this->item->categoryTitle; ?></div>
            </section>
        </div>
    </div>
    <div class="mt-5">
		<?php echo $this->loadTemplate('tabs'); ?>
    </div>
</div>

<?php if ($this->params->get('enable_captcha') && ($this->params->get('enable_captcha_reg') || $user->guest)): ?>
	<?php $plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
	$params = new JRegistry($plugin->params); ?>
    <script>
		var CaptchaCallback = function () {
			<?php if($this->params->get('enable_reviews') && ($this->params->get('reviews_guest_allow') || $user->id)):?>
			grecaptcha.render('jd-review-captcha', {'sitekey': '<?php echo $params->get('public_key', ''); ?>'});
			<?php endif;?>
		};
    </script>
    <script src="//www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit" async defer></script>
<?php endif; ?>


<!--ivanx-->
<script type="text/javascript">
  $(window).load(function(){
      document.styleSheets[11].disabled = true;
/*      var i;
        for (i=0; i<30; i++){
              var a = document.styleSheets[i].href;
              alert(a + "--->" + i);
        };
*/
    })
</script>

