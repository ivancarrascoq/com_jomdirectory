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
defined('_JEXEC') or die('Restricted access');

include("scripts.php");

?>

<script>
	function hashchanged() {
		var hash = location.hash.replace(/^#/, '');
		res = hash.split(",");
		if (!res[1]) {
			jQuery("#" + hash).next().css({border: '0 solid #ff0000'}).animate({borderWidth: 5}, 200);

			setTimeout(function () {
				jQuery("#" + hash).next().animate({borderWidth: 0}, 200);
				location.href = location.href + ",!";
			}, 1000);
		}
	}

	jQuery(document).ready(function () {
		var topHeight = jQuery('.tm-toolbar').height();
		var navHeight = jQuery('.tm-navbar').height();
		var navHeightInner = (jQuery('.navbar-inner').height() > jQuery('#header').height()) ? jQuery('.navbar-inner').height() : jQuery('#header').height();
		var timgHeight = jQuery('.tm-top-image').height();
		var tdHeight = jQuery('.navbar-fixed-top2').height();
		jQuery('.jd-side-top').css('top', topHeight + navHeight + navHeightInner + timgHeight + tdHeight + 'px');
        jQuery('#jd-itemsMap').css('top', topHeight + navHeight + navHeightInner + timgHeight + tdHeight + 'px');
	});

</script>

<style>
    html {
        overflow-y: hidden;
        overflow: -moz-scrollbars-none;
    }

    .jc-powered {
        display: none !important;
    }
</style>

<section id="jomdirectory-items-grid" class="jomdirectory position-relative clearfix bootstrap">


	<?php if ($countItems == 0): ?>
        <div class="d-block position-relative">
			<?php if ($saved): ?>
				<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?> &nbsp;
			<?php endif; ?>
			<?php echo $this->loadTemplate('filtered'); ?>
            <div class="alert alert-primary"><i class="fa fa-info"></i> <span><?php echo JText::_('COM_JOMDIRECTORY_NO_ITEMS'); ?></span></div>
        </div>
		<?php return; endif; ?>

    <div id="jd-itemsMap" class="d-block position-fixed" style="width: 47%; left: 0; top: 110px; bottom: 0;"></div>

    <div class="d-block position-fixed p-2 jd-side-top" style="overflow-y: scroll;  right: 0; bottom: 0; width: 52%;">

		<?php if ($countItems > 0): ?>
			<?php if ($this->params->get('enable_filterred')): ?>
				<?php echo $this->loadTemplate('filtered'); ?>
			<?php endif ?>
		<?php endif; ?>


        <div class="d-block position-relative mb-3">
            <form name="jdFormItems" action="#" method="post" class="clearfix">
                <div class="form-row align-items-center float-left">
                    <label class="my-0 mx-1"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_BY'); ?></label>
					<?php
					$arraySort = array(array('v' => 'latest', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_LATEST')), array('v' => 'updated', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_UPDATED')), array('v' => 'alfa', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_ALPHABETICALLY')), array('v' => 'rated_desc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_DESC')), array('v' => 'rated_asc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_ASC')), array('v' => 'most_viewed', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_MOSTVIEWED')));
					?>
                    <div class="d-inline">
						<?php echo JHtml::_('select.genericlist', $arraySort, 'jdItemsSort', 'class="custom-select mb-0" onChange="this.form.submit()"', 'v', 't', $this->state->{'list.sort'}); ?>
                    </div>
                    <div class="d-block d-md-none w-100 my-2"></div>
                    <label class="my-0 mx-1"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_PERPAGE'); ?></label>
                    <div class="d-inline">
						<?php echo JHtml::_('select.genericlist', array(array('text' => 8, 'value' => 8), array('text' => 10, 'value' => 10), array('text' => 16, 'value' => 16), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
                    </div>
                </div>
                <div class="float-right">
					<?php if ($this->params->get('enable_save')): ?>
						<?php if ($saved): ?>
							<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?> &nbsp;
							<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.clearSaved'), JText::_('COM_JOMDIRECTORY_CLEARSAVED'), 'class="btn btn-primary"') ?> &nbsp;
						<?php else: ?>
							<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewSaved'), "<i class=\"fa fa-heart-o\"></i> " . JText::_('COM_JOMDIRECTORY_SAVED'), 'class="btn btn-primary"') ?>
						<?php endif; ?>
					<?php endif; ?>
                </div>
            </form>
        </div>


        <div class="clearfix">
            <div class="row m-0">
				<?php
				foreach ($this->items as $key => $item):
					$link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($item->id, $item->alias, $item->categories_id, $item->categories_address_id));
					if (isset($this->images[$item->id])) $images = $this->images[$item->id]; else
						$images = false;
					$number = ($key + 1) + $startNumberPage;
					?>
                    <div class="col-sm-6 <?php if ($item->featured) echo 'jd-itemPremium'; ?> mb-3">
                        <!--a id="itemJc<?php echo $number ?>"></a-->
                        <div id="itemJc<?php echo $number ?>" class="card marker-hover">
                            <div class="p-0 view overlay">
								<?php if ($item->featured): ?>
                                    <span class="d-block position-absolute jd-title-featured"><?= JText::_('COM_JOMDIRECTORY_FIELD_FEATURED'); ?></span>
								<?php endif; ?>
								<?php if ($images): ?>
									<?php echo JHTML::_('image', $images, 'img', 'class="card-img"') ?>
								<?php else: ?>
									<?php echo JHTML::_('image', JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg', 'no-photo', 'class="card-img"') ?>
								<?php endif; ?>
                                <div class="card-img-overlay d-flex align-items-end">
                                    <div class="right-0 position-absolute align-self-start pr-4 category"><span class="badge-cd rgba-black-strong"><?php echo $item->category_title ?></span></div>
									<?php if ($this->params->get('enable_user_logo')): ?>
										<?php if (isset($this->images_logo[$item->id])) : ?>
                                            <div class="position-absolute">
                                                <div class="text-center">
													<?php echo JHTML::_('image', $this->images_logo[$item->id], 'brand', 'style="max-width: ' . $this->params->get('image_logo_width') . 'px"') ?>
                                                </div>
                                            </div>
										<?php endif; ?>
									<?php endif; ?>

                                </div>
                                <div class="mask rgba-black-light d-flex">
                                    <div class="mx-auto align-self-center">
                                        <a class="btn btn-primary" href="<?php echo $link ?>"><?php echo JText::_('COM_JOMDIRECTORY_DETAILS'); ?> <i class="fa fa-chevron-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body rounded-bottom grey lighten-5 pt-3 <?php if ($item->featured): ?>jd-title-premium<?php endif; ?> clearfix">
                                <h3 class="mb-2"><a href="<?php echo $link ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a></h3>
								<?php if (!empty($item->address) or !empty($item->fulladdress)) : ?><i class="mdi mdi-map-marker-outline"></i><?php endif; ?>
								<?php if (!empty($item->address)) : ?>
                                    <span itemprop="addressLocality"><?php echo implode('<span> · </span> ', $item->address) ?></span>
								<?php endif; ?>
								<?php if (!empty($item->fulladdress)) : ?>
                                    <span> · </span>
                                    <span itemprop="streetAddress"><?php echo $item->fulladdress ?></span>
								<?php endif; ?>
                                <hr/>
                                <div class="d-flex justify-content-between">
                                    <div>
										<?php if ($this->params->get('enable_reviews')): ?>
											<?php if ($item->rateHow > 0): ?>
                                                <div class="d-inline-block">
                                                    <input value="<?php echo round($item->rateSum / $item->rateHow * 2) /2; ?>" type="text" readonly class="dial" data-width="50" data-height="50" data-displayPrevious="true" data-fgColor="#a88e4b" data-thickness=".2" autocomplete="off">
                                                </div>
                                                <span class="small d-inline-block">(<?php echo $item->rateHowD ?>) <?php echo JText::_('COM_JOMDIRECTORY_SCORE_REVIEWS'); ?></span>
											<?php endif; ?>
										<?php endif; ?>
                                    </div>
                                    <div id="jd-item-box-toolbar-fav<?php echo $item->id ?>">
										<?php if ($this->params->get('enable_save')): ?>
											<?php if (!JomcomdevHelperRemember::checkFavorite($item->id)): ?>
                                                <a class="jd-ajax-json px-2" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&format=json&id=<?php echo $item->id ?>" rel="{cnt: 'jd-item-box-toolbar-fav<?php echo $item->id ?>', type: 'favorite'}">
                                                    <i class="fa fa-heart-o"></i>
                                                </a>
											<?php else: ?>
                                                <span class=" text-primary px-2"><i class="fa fa-heart"></i></span>
											<?php endif; ?>
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endforeach; ?>
                <div class="my-3 clearfix w-100">
					<?php if ($this->pagination->get('pages.total') > 1): ?>
                        <div class="pagination justify-content-center"><?= $this->pagination->getPagesLinks(); ?></div>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</section>

</div>












