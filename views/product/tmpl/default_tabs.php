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
<div id="tabs" class="clearfix">
    <ul id="tabs-header" class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-primary" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">
				<?php echo JText::_('COM_JOMDIRECTORY_TAB_DETAILS'); ?>
            </a>
        </li>
		<?php if ($this->params->get('product_gallery')): ?>
            <li class="nav-item">
                <a class="nav-link text-primary" id="photos-tab" data-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="false">
					<?php echo JText::_('COM_JOMDIRECTORY_TAB_PHOTOS'); ?>(<?php echo isset($this->item->images->gallery) ? count($this->item->images->gallery) : 0 ?>)
                </a>
            </li>
		<?php endif; ?>
		<?php if ($this->params->get('enable_features')): ?>
            <li class="nav-item">
                <a class="nav-link text-primary" id="features-tab" data-toggle="tab" href="#features" role="tab" aria-controls="features" aria-selected="false">
					<?php echo JText::_('COM_JOMDIRECTORY_TAB_ADDITIONAL_INFO'); ?>
                </a>
            </li>
		<?php endif; ?>
		<?php if ($this->params->get('product_reviews')): ?>
            <li class="nav-item">
                <a class="nav-link text-primary" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">
					<?php echo JText::_('COM_JOMDIRECTORY_TAB_REVIEWS'); ?>(<?php echo isset($this->item->reviews->items) ? count($this->item->reviews->items) : 0 ?>)
                </a>
            </li>
		<?php endif; ?>
		<?php if ($this->params->get('enable_att')): ?>
			<?php if (!empty($this->item->file) && !empty($this->item->file['com-jomcomdev-files-tabs']) && is_array($this->item->file)): ?>
                <li class="nav-item">
                    <a class="nav-link text-primary" id="attachments-tab" data-toggle="tab" href="#attachments" role="tab" aria-controls="attachments" aria-selected="false">
						<?php echo JText::_('COM_JOMDIRECTORY_ATTACHMENTS'); ?>
                    </a>
                </li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('product_company_info')): ?>
            <li class="nav-item">
                <a class="nav-link text-primary" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">
					<?php echo JText::_('COM_JOMDIRECTORY_PRODUCT_BRAND'); ?>
                </a>
            </li>
		<?php endif; ?>
		<?php foreach ($tabsGroup AS $t): ?>
            <li class="nav-item">
                <a class="nav-link text-primary" id="<?php echo $t->getElementValue('alias') ?>-tab" data-toggle="tab" href="#<?php echo $t->getElementValue('alias') ?>" role="tab" aria-controls="<?php echo $t->getElementValue('alias') ?>" aria-selected="false"><?php echo $t->getElementValue('name') ?></a>
            </li>
		<?php endforeach; ?>
        <div class="clearfix"></div>
    </ul>


    <div class="tab-content card card-body" id="tab-content">

        <div id="detail" class="tab-pane show active" role="tabpanel" aria-labelledby="detail-tab">
            <h3><?php echo JText::_('COM_JOMDIRECTORY_TAB_DETAILS'); ?></h3>
            <p itemprop="description"><?php echo $this->item->fulltext ?></p>
            <div class="my-3">
				<?php echo $this->item->fields->showGroup("items") ?>
            </div>
			<?php if ($this->params->get('enable_articles') && !empty($this->articles)): ?>
                <div id="jd-tab10">
                    <h3><?php echo JText::_('COM_JOMDIRECTORY_ARTICLES'); ?></h3>
					<?php foreach ($this->articles AS $art): ?>
                        <div class="jd-article-title">
                            <a href="<?php echo ContentHelperRoute::getArticleRoute($art->id, $art->catid) ?>"><?php echo $art->title ?></a>
                        </div>
                        <div class="jd-article-data">
							<?php echo $art->introtext ?>
                        </div>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>
            <hr/>
			<?php $jVerArr = explode('.', JVERSION);
			if ($jVerArr[0] >= '3'):
				?>
				<?php $tagsData = $this->item->tags->getItemTags('com_jomdirectory.products', $this->item->id); ?>
				<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
				<?php echo $this->item->tagLayout->render($tagsData); ?>
			<?php endif; ?>
        </div>
		<?php if ($this->params->get('product_gallery')): ?>
			<?php $desc = '';
			if (!empty($img->description)) {
				$desc = strip_tags($img->description);
				$desc = "data-title=\"$desc\"";
			} ?>
            <div id="photos" class="tab-pane" role="tabpanel" aria-labelledby="photos-tab">
				<?php if (isset($this->item->images->gallery)): ?>
                    <h3><?php echo JText::_('COM_JOMDIRECTORY_TAB_TITLE_IMAGE'); ?></h3>
					<?php foreach ($this->item->images->gallery AS $img): ?>
                        <a class="gallery-thumb" href="<?php echo $img->bigger ?>" data-toggle="lightbox" data-gallery="gallery" <?php echo $desc ?>>
							<?php echo JHTML::_('image', $img->big, $img->title, 'class="img-thumbnail mr-1 mb-1" style="max-width: '.$this->params->get('image_gallery_width').'px"') ?>
                        </a>
					<?php endforeach; ?>
				<?php endif; ?>
            </div>
		<?php endif; ?>
        <div id="features" class="tab-pane" role="tabpanel" aria-labelledby="features-tab">
            <h3><?php echo JText::_('COM_JOMDIRECTORY_TAB_ADDITIONAL_INFO'); ?></h3>
            <div class="clearfix jd-custom-fields">
				<?php echo $this->item->fields->showGroup("item") ?>
				<?php echo $this->item->fields->showGroup("state") ?>
				<?php echo $this->item->fields->showGroup("paiditems") ?>
				<?php echo $this->item->fields->showGroup("paiditem") ?>
            </div>
        </div>


		<?php if ($this->params->get('product_reviews')): ?>
            <div id="reviews" class="tab-pane" role="tabpanel" aria-labelledby="reviews-tab">
				<?php echo $this->loadTemplate('reviews'); ?>
            </div>
		<?php endif; ?>

		<?php if ($this->params->get('enable_att')): ?>
			<?php if (!empty($this->item->file) && !empty($this->item->file['com-jomcomdev-files-tabs']) && is_array($this->item->file)): ?>
                <div id="attachments" class="tab-pane" role="tabpanel" aria-labelledby="attachments-tab">
                    <h3><?php echo JText::_('COM_JOMDIRECTORY_ATTACHMENTS'); ?></h3>
					<?php foreach ($this->item->file['com-jomcomdev-files-tabs'] AS $file): ?>
                        <div class="">
                            <a href="<?php echo $file->srcDest ?> "><?php echo $file->title ?></a>
                        </div>
                        <div class="">
							<?php echo JText::_('COM_JOMDIRECTORY_FILES_SIZE'); ?>: <?php echo $file->sizeDest ?>
							<?php echo JText::_('COM_JOMDIRECTORY_FILES_EXT'); ?>: <?php echo $file->type ?>
                            <br> <?php echo nl2br($file->description); ?>
                        </div>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->params->get('product_company_info')): ?>
            <div id="info" class="tab-pane" role="tabpanel" aria-labelledby="info-tab">
				<?php if (!empty($this->item->company)): ?>
                    <div class="row m-0">
                        <h3 class="col-12">
                            <a href="<?= JRoute::_(JomdirectoryHelperRoute::getArticleRoute($this->item->company->id, $this->item->company->alias, $this->item->company->categories_id, $this->item->company->categories_address_id)); ?>"><?php echo $this->item->company->title; ?></a>
                        </h3>
                        <div class="col-md-10">
							<?php echo $this->item->company->fulltext; ?>
                        </div>
                        <div class="col-md-2">
							<?php
							if (isset($this->item->company->images->logo)):
								$logo = current($this->item->company->images->logo);
								?>
								<?php echo JHTML::_('image', $logo->big, 'brand', 'style="max-width: ' . $this->params->get('item_image_logo_width') . 'px"') ?>
							<?php endif; ?>
                        </div>
                    </div>
                    <hr/>
					<?php if (!empty($this->item->company->address)): ?>
                        <h4 class="mt-0"><i class="mdi mdi-map-marker"></i> <?php echo implode(', ', $this->item->company->address) . ' ' . $this->item->company->fulladdress; ?></h4>
					<?php endif; ?>
					<?php if ($this->item->company->maps_lat && $this->item->company->maps_lng && $this->params->get('enable_map')): ?>
                        <div id="jd-item-box-maps" style="width: 100%; height: 400px;"></div>
					<?php endif; ?>
				<?php endif; ?>
            </div>
		<?php endif; ?>

        <!--END OF TAB6 REWIEWS-->
        <!--CUSTOM TABS -->
		<?php foreach ($tabsGroup AS $t): ?>
            <div id="<?php echo $t->getElementValue('alias') ?>" class="tab-pane" role="tabpanel" aria-labelledby="<?php echo $t->getElementValue('alias') ?>-tab">
				<?php echo $t->show() ?>
            </div>
		<?php endforeach; ?>
    </div>

</div>



    



    
