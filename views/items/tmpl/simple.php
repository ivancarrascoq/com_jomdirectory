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


<section id="jomdirectory-list" class="jomdirectory bootstrap">

	<?php if ($this->params->get('enable_filterred')): ?>
		<?php echo $this->loadTemplate('filtered'); ?>
	<?php endif ?>

	<?php if ($countItems == 0): ?>
		<?php if ($saved): ?>
			<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?>
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
					<?php echo JHtml::_('select.genericlist', array(array('text' => 5, 'value' => 5), array('text' => 10, 'value' => 10), array('text' => 15, 'value' => 15), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
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

    <div class="d-block position-relative mt-3">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>

	<?php foreach ($this->items as $key => $item): ?>
		<?php
		//$link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($item->id, $item->alias, $item->categories_id));
		$link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($item->id, $item->alias, $item->categories_id, $item->categories_address_id));
		if (isset($this->images[$item->id])) $images = $this->images[$item->id]; else $images = false;
		$number = ($key + 1) + JRequest::getInt('limitstart');
		?>
        <a id="itemJc<?php echo $number ?>"></a>
        <div class="clearfix <?php if ($item->featured) echo 'jd-item-box-premium'; ?> mb-5">
            <div class="card text-center mb-3">
                <a href="<?php echo $link ?>">
					<?php if ($images): ?>
						<?php echo JHTML::_('image', $images, 'img', '') ?>
					<?php endif; ?>
                </a>
				<?php if ($this->params->get('enable_user_logo')): ?>
                    <div class="card-img-overlay d-flex align-items-end">
						<?php if (isset($this->images_logo[$item->id])) : ?>
							<?php echo JHTML::_('image', $this->images_logo[$item->id], 'brand', 'style="max-width: ' . $this->params->get('image_logo_width') . 'px"') ?>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
				<?php if ($item->featured): ?>
                    <div class="card-img-overlay">
                        <i class="mdi mdi-star mdi-36px text-white"></i>
                    </div>
				<?php endif; ?>
            </div>
            <div class="clearfix">
                <div class="float-left">
                    <h1 class="mb-1"><?php echo JHTML::_('link', $link, $item->title, 'class="jd-items-item-title"') ?></h1>
					<?php if ($this->params->get('enable_date')) { ?>
						<?php $date = new JDate($item->date_publish);
						echo $date->format(JText::_('DATE_FORMAT_LC1')); ?>
					<?php } ?>
                    <h4 class="mt-1 teal-text">
                        <span class="jd-items-category mr-1"><i class="mdi mdi-folder-multiple-outline"></i> <?php echo $item->category_title ?></span>
						<?php if (!empty($item->address) or !empty($item->fulladdress)) : ?><i class="mdi mdi-map-marker-outline"></i><?php endif; ?>
						<?php if (!empty($item->address)) : ?>
                            <span itemprop="addressLocality"><?php echo implode('<i class="mdi mdi-chevron-right"></i>', $item->address) ?></span>
						<?php endif; ?>
						<?php if (!empty($item->fulladdress)) : ?>
                            <span> · </span>
                            <span itemprop="streetAddress"><i class="mdi mdi-chevron-right"></i><?php echo $item->fulladdress ?></span>
						<?php endif; ?>
                    </h4>
                </div>
            </div>
			<?php if ($this->params->get('enable_short_desc')): ?>
                <div class="card-text"><?php echo $item->introtext ?></div>
			<?php endif; ?>

            <div class="d-flex justify-content-between mt-3">
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
                            <a class="jd-ajax-json btn btn-outline-primary btn-sm" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&format=json&id=<?php echo $item->id ?>" rel="{cnt: 'jd-item-box-toolbar-fav<?php echo $item->id ?>', type: 'favorite'}">
                                <i class="fa fa-heart-o"></i>
                            </a>
						<?php else: ?>
                            <span class="btn btn-sm btn-success"><i class="fa fa-heart"></i></span>
						<?php endif; ?>
					<?php endif; ?>
                </div>
            </div>
            <hr/>
        </div>

	<?php endforeach; ?>

    <div class="my-3">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>

</section> <!--END OF JD-ITEMS-WRAPPER -->