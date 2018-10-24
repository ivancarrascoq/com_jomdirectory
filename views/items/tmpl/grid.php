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

<section id="jomdirectory-items-grid" class="jomdirectory bootstrap">

	<?php if ($this->params->get('enable_filterred')): ?>
		<?php echo $this->loadTemplate('filtered'); ?>
	<?php endif ?>

	<?php if ($countItems == 0): ?>
		<?php if ($this->params->get('enable_save')): ?>
			<?php if ($saved): ?>
				<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?>
			<?php endif; ?>
		<?php endif; ?>
        <div class="alert alert-primary"><i class="fa fa-info"></i> <span><?php echo JText::_('COM_JOMDIRECTORY_NO_ITEMS'); ?></span></div>
		<?php return; endif; ?>

    <div class="d-block position-relative">
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

	<?php if ($enableMapListing): ?>
        <div class="jd-itemsMapBox mt-3">
            <div id="jd-itemsMap" style="width: 100%; height: 300px;"></div>
        </div>
	<?php endif; ?>

    <div class="mt-3 mb-3 clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>
	<?php $column_size = 12;
	switch ($this->params->get('columns-grid')) {
		case 1:
			$column_size = 12;
			break;
		case 2:
			$column_size = 6;
			break;
		case 3:
			$column_size = 4;
			break;
		case 4:
			$column_size = 3;
			break;
	}
	?>
    <div class="row">
		<?php
		foreach ($this->items as $key => $item):
			$link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($item->id, $item->alias, $item->categories_id, $item->categories_address_id));
			if (isset($this->images[$item->id])) $images = $this->images[$item->id]; else
				$images = false;
			$number = ($key + 1) + $startNumberPage;
			?>
            <div class="col-sm-<?php echo $column_size ?> mb-3 <?php if ($item->featured) echo 'jd-itemPremium'; ?> d-flex">
                <a id="itemJc<?php echo $number ?>"></a>
                <div class="card w-100">
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
                        <h3 class="mb-2"> <a href="<?php echo $link ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a></h3>
						<?php if (!empty($item->address) or !empty($item->fulladdress)) : ?><i class="mdi mdi-map-marker-outline"></i><?php endif; ?>
						<?php if (!empty($item->address)) : ?>
                            <span itemprop="addressLocality"><?php echo implode('<i class="mdi mdi-chevron-right"></i>', $item->address) ?></span>
						<?php endif; ?>
						<?php if (!empty($item->fulladdress)) : ?>
                            <span itemprop="streetAddress"><i class="mdi mdi-chevron-right"></i><?php echo $item->fulladdress ?></span>
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
    </div>
    <div class="my-3 clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>
</section>