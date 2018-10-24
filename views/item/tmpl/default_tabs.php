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

require_once JPATH_BASE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';

$user = JFactory::getUser();
$tabsGroup = $this->item->fields->getGroup("tabs");

?>
<!--TABS -->
<div id="tabs" class="clearfix mt-5">
    <div class="row no-gutters">
        <ul class="col-12 nav nav-tabs" id="tabs-header" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-primary" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true"><?php echo JText::_('COM_JOMDIRECTORY_TAB_DETAILS'); ?></a>
            </li>
			<?php if ($this->params->get('enable_att')): ?>
				<?php if (!empty($this->item->file) && !empty($this->item->file['com-jomcomdev-files-tabs']) && is_array($this->item->file)): ?>
                    <li class="nav-item">
                        <a class="nav-link text-primary" id="attachments-tab" data-toggle="tab" href="#attachments" role="tab" aria-controls="attachments" aria-selected="false"><?php echo JText::_('COM_JOMDIRECTORY_ATTACHMENTS'); ?></a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->enable_calendar): ?>
                <li class="nav-item">
                    <a class="nav-link text-primary" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab" aria-controls="calendar" aria-selected="false"><?php echo JText::_('COM_JOMDIRECTORY_CALENDAR'); ?></a>
                </li>
			<?php endif; ?>
			<?php if ($this->params->get('product_display')): ?>
				<?php if (!empty($this->item->products)): ?>
                    <li class="nav-item">
                        <a class="nav-link text-primary" id="products-tab" data-toggle="tab" href="#products" role="tab" aria-controls="products" aria-selected="false"><?php echo JText::_('COM_JOMDIRECTORY_PRODUCTS'); ?></a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->params->get('enable_yelp')): ?>
				<?php if ($this->item->yelp_id): ?>
                    <li class="nav-item">
                        <a class="nav-link text-primary" id="yelp-tab" data-toggle="tab" href="#yelp" role="tab" aria-controls="yelp" aria-selected="false"><?php echo JText::_('COM_JOMDIRECTORY_YELP_REVIEWS'); ?></a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
			<?php foreach ($tabsGroup AS $t): ?>
				<?php if (trim($t->show())): ?>
                    <li class="nav-item">
                        <a class="nav-link text-primary" id="<?php echo $t->getElementValue('alias') ?>-tab" data-toggle="tab" href="#<?php echo $t->getElementValue('alias') ?>" role="tab" aria-controls="<?php echo $t->getElementValue('alias') ?>" aria-selected="false"><?php echo $t->getElementValue('name') ?></a>
                    </li>
				<?php endif; ?>
			<?php endforeach; ?>
        </ul>
    </div>

    <!--TABS BODY -->
    <div class="tab-content card card-body" id="tab-content">

        <div id="detail" class="tab-pane show active" role="tabpanel" aria-labelledby="detail-tab">
            <h3><?php echo JText::_('COM_JOMDIRECTORY_TAB_DETAILS'); ?></h3>
            <span itemprop="description">
                <?php echo $this->item->introtext ?>
                <?php echo $this->item->fulltext ?>
            </span>
			<?php if ($this->params->get('enable_articles') && !empty($this->articles)): ?>
                <div id="jd-tab10">
                    <h3><?php echo JText::_('COM_JOMDIRECTORY_ARTICLES'); ?></h3>
					<?php foreach ($this->articles AS $art): ?>
                        <div>
                            <a href="<?php echo ContentHelperRoute::getArticleRoute($art->id, $art->catid) ?>"><?php echo $art->title ?></a>
                        </div>
                        <div>
							<?php echo $art->introtext ?>
                        </div>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>
        </div>


		<?php if ($this->params->get('enable_att')): ?>
			<?php if (!empty($this->item->file) && !empty($this->item->file['com-jomcomdev-files-tabs']) && is_array($this->item->file)): ?>
                <div id="attachments" class="tab-pane" role="tabpanel" aria-labelledby="attachments-tab">
                    <h3><?php echo JText::_('COM_JOMDIRECTORY_ATTACHMENTS'); ?></h3>
					<?php foreach ($this->item->file['com-jomcomdev-files-tabs'] AS $file): ?>
                        <div class="my-3">
                            <a href="<?php echo $file->srcDest ?> "><h4 class="mb-1"><?php echo $file->title ?></h4></a>
                            <div class="text-muted">
								<?php echo JText::_('COM_JOMDIRECTORY_FILES_SIZE'); ?>: <?php echo $file->sizeDest ?>
								<?php echo JText::_('COM_JOMDIRECTORY_FILES_EXT'); ?>: <?php echo $file->type ?>
                            </div>
                            <p class="mt-1"><?php echo nl2br($file->description); ?></p>
                            <hr/>
                        </div>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->enable_calendar): ?>
            <div id="calendar" class="tab-pane" role="tabpanel" aria-labelledby="calendar-tab">
                <h3><?php echo JText::_('COM_JOMDIRECTORY_CALENDAR'); ?></h3>
				<?php echo $this->loadTemplate('calendar'); ?>
            </div>
		<?php endif; ?>

		<?php if ($this->params->get('product_display')): ?>
            <div id="products" class="tab-pane" role="tabpanel" aria-labelledby="products-tab">
                <h3><?php echo JText::_('COM_JOMDIRECTORY_PRODUCTS'); ?></h3>
				<?php foreach ($this->item->products AS $product): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <img src="<?php echo $product->image ?>" class="" alt="<?php echo $product->title; ?>"/>
                        </div>
                        <div class="col-md-7">
                            <h3 class="mt-1 mb-2">
                                <a href="<?= JRoute::_(JomdirectoryHelperRoute::getProductRoute($product->id, $product->alias, $product->categoryId)) ?>"><?php echo $product->title; ?></a>
                            </h3>
                            <span class="d-block text-primary mt-1"><i class="mdi mdi-folder-multiple-outline"></i> <?php echo $product->categoryTitle; ?></span>
                            <p>
								<?php $words = explode(" ", $product->introtext);
								echo implode(" ", array_splice($words, 0, 15)); ?>...
                            </p>
                        </div>
                        <div class="col-md-2">
							<?php if ($this->params->get('product_price')): ?>
                                <span class="font-weight-bold"><?php echo $product->price; ?></span>
							<?php endif; ?>
                        </div>
                    </div>
                    <hr>
				<?php endforeach; ?>
            </div>
		<?php endif; ?>

		<?php if ($this->params->get('enable_yelp')): ?>
			<?php if ($this->item->yelp_id): ?>
                <div id="yelp" class="tab-pane" role="tabpanel" aria-labelledby="yelp-tab">
					<?php echo $this->loadTemplate('yelp'); ?>
                </div>
			<?php endif; ?>
		<?php endif; ?>


        <!--CUSTOM TABS -->
		<?php foreach ($tabsGroup AS $t): ?>
			<?php if (trim($t->show())): ?>
                <div id="<?php echo $t->getElementValue('alias') ?>" class="tab-pane" role="tabpanel" aria-labelledby="<?php echo $t->getElementValue('alias') ?>-tab">
					<?php echo $t->show() ?>
                </div>
			<?php endif; ?>
		<?php endforeach; ?>
    </div>

</div><!--END OF JD-ITEM-BOX -->
