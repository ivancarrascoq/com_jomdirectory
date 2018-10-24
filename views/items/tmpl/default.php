<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

include("scripts.php");

?>

<section id="jomdirectory-list" class="jomdirectory d-block position-relative clearfix">

	<?php if ($this->params->get('enable_filterred')): ?>
		<?php echo $this->loadTemplate('filtered'); ?>
	<?php endif ?>

	<?php if ($countItems == 0): ?>
		<?php if ($saved): ?>
			<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?>
		<?php endif; ?>
            <div class="alert alert-primary"><i class="fa fa-info"></i> <span class=""><?php echo JText::_('COM_JOMDIRECTORY_NO_ITEMS'); ?></span></div>
     <?php return; endif; ?>

    <div class="d-block position-relative clearfix">
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
					<?php echo JHtml::_('select.genericlist', array(array('text' => 5, 'value' => 5), array('text' => 8, 'value' => 8), array('text' => 10, 'value' => 10), array('text' => 15, 'value' => 15), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
                </div>
            </div>
            <div class="float-right">
				<?php if ($this->params->get('enable_save')): ?>
					<?php if ($saved): ?>
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?>
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.clearSaved'), JText::_('COM_JOMDIRECTORY_CLEARSAVED'), 'class="btn btn-primary"') ?>
					<?php else: ?>
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewSaved'), "<i class=\"mdi mdi-heart-outline\"></i> " . JText::_('COM_JOMDIRECTORY_SAVED'), 'class="btn btn-primary"') ?>
					<?php endif; ?>
				<?php endif; ?>
            </div>
        </form>
    </div>

	<?php if ($enableMapListing): ?>
        <div class="d-block position-relative mt-3">
            <div id="jd-itemsMap" style="width: 100%; height: 300px;"></div>
        </div>
	<?php endif; ?>

    <div class="d-block position-relative mt-3">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>

    <div class="mt-3">
		<?php
		foreach ($this->items as $key => $item):
			$link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($item->id, $item->alias, $item->categories_id, $item->categories_address_id));
			if (isset($this->images[$item->id])) $images = $this->images[$item->id]; else $images = false;
			$number = ($key + 1) + $startNumberPage;
			?>
            <a id="itemJc<?php echo $number ?>"></a>
            <div class="mb-3 card">
                <div class="row no-gutters">
					<?php if ($this->params->get('enable_list_image')): ?>
                        <div class="col-md-4 pr-3">
                            <div class="view overlay">
								<?php if ($item->featured): ?>
                                    <span class="d-block position-absolute jd-title-featured"><?= JText::_('COM_JOMDIRECTORY_FIELD_FEATURED'); ?></span>
								<?php endif; ?>
								<?php if ($images): ?>
									<?php echo JHTML::_('image', $images, 'img', 'class="card-img rounded-0"') ?>
								<?php else: ?>
									<?php $width = $this->params->get('image_items_width') . "px"; ?>
									<?php echo JHTML::_('image', JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg', 'card-img rounded-0') ?>
								<?php endif; ?>

								<?php if (isset($this->images_logo[$item->id])) : ?>
                                    <div class="card-img-overlay">
										<?php echo JHTML::_('image', $this->images_logo[$item->id], 'logo', 'class="rounded-0"') ?>
                                    </div>
								<?php endif; ?>
                                <div class="card-img-overlay"><span class="map-number p-1 text-white"><i class="fa fa-map-marker"></i> <?php echo $number ?></span></div>
                                <a href="<?php echo $link ?>" title="<?php echo $item->title; ?>" class="mask rgba-black-strong text-center d-flex">
                                    <span class="align-self-center w-100"><i class="fa fa-search fa-3x text-white"></i></span>
                                </a>
                            </div>
                        </div>
					<?php endif; ?>
                    <div class="col-md-8">
                        <div class="d-block position-relative card-body p-2">
                            <div class="card-title">
                                <h2 class="m-0">
                                    <a href="<?php echo $link ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a>
                                </h2>
                            </div>
                            <address class="m-1">
								<?php if (!empty($item->address) or !empty($item->fulladdress)) : ?><i class="mdi mdi-map-marker-outline"></i><?php endif; ?>
								<?php if (!empty($item->address)) : ?>
                                    <span itemprop="addressLocality"><?php echo implode('<i class="mdi mdi-chevron-right"></i>', $item->address) ?></span>
								<?php endif; ?>
								<?php if (!empty($item->fulladdress)) : ?>
                                    <span itemprop="streetAddress"><i class="mdi mdi-chevron-right"></i><?php echo $item->fulladdress ?></span>
								<?php endif; ?>
                            </address>
							<?php if ($this->params->get('enable_short_desc')): ?>
								<?php if ($item->introtext != ''): ?>
                                    <div class="card-text mt-3 pl-3 border-left">
										<?php $words = explode(" ", $item->introtext);
										echo implode(" ", array_splice($words, 0, 25)); ?>...
                                    </div>
								<?php endif; ?>
							<?php endif; ?>
                            <div class="clearfix my-3">
                                <div class="card-text float-left">
									<?php echo $item->paid_fields->showGroup('paiditems') ?>
									<?php echo $item->fields->showGroup('items') ?>
                                </div>
								<?php if ($this->params->get('enable_reviews')): ?>
                                    <div class="<?php if ($this->params->get('reviews_rating_method')): ?>jd-stars<?php else: ?>jd-knob<?php endif; ?> float-right d-flex align-items-center">
										<?php if ($item->rateHow > 0): ?>
                                            <div class="d-inline-block mr-3">
                                                <a href="<?php echo $link ?>#jd-tab7"><span><?php echo JText::_('COM_JOMDIRECTORY_SCORE'); ?></span></a>
                                            </div>
                                            <div class="d-inline-block">
                                                <input value="<?php echo round($item->rateSum / $item->rateHow * 2) /2; ?>" type="text" readonly class="dial" data-width="50" data-height="50" data-displayPrevious="true" data-fgColor="#ee413c" data-thickness=".1" autocomplete="off">
                                            </div>
										<?php endif; ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer grey lighten-5 <?php if ($item->featured): ?>footer-premium<?php endif; ?> d-block position-relative clearfix">
                    <div class="d-inline-block mr-3">
                        <i class="mdi mdi-folder-multiple-outline"></i> <?php echo $item->category_title ?>
                    </div>
					<?php if ($this->params->get('enable_tags')): ?>
                        <div class="d-inline-block">
							<?php $jVerArr = explode('.', JVERSION);
							if ($jVerArr[0] >= '3'):
								?>
								<?php $tagsData = $item->tags->getItemTags('com_jomdirectory.content', $item->id); ?>
								<?php $item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
								<?php echo $item->tagLayout->render($tagsData); ?>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                    <div id="jd-item-box-toolbar-fav<?php echo $item->id ?>" class="float-right">
						<?php if ($this->params->get('enable_save')): ?>
							<?php if (!JomcomdevHelperRemember::checkFavorite($item->id)): ?>
                                <a class="jd-ajax-json px-2" href="<?php echo JRoute::_("index.php?option=com_jomcomdev&task=ajax.addFavorite&format=json&id=" . $item->id); ?>" rel="{cnt: 'jd-item-box-toolbar-fav<?php echo $item->id ?>', type: 'favorite'}">
                                    <i class="mdi mdi-heart-outline"></i>
                                </a>
							<?php else: ?>
                                <span class=""><i class="mdi mdi-heart text-primary"></i></span>
							<?php endif; ?>
						<?php endif; ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>

        <div class="clearfix">
			<?php if ($this->pagination->get('pages.total') > 1): ?>
                <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
			<?php endif; ?>
        </div>

    </div>

</section>