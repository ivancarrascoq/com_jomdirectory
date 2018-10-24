<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
defined('_JEXEC') or die;

$type = $this->params->get('type', 0);
$letter = JFactory::getApplication()->input->getString('letter', false);

?>

<div id="jomdirectory-categories-modern" class="bootstrap">
	<?php if ($this->params->get('enable_cat_index')): ?>
        <div class="mb-5 text-center">
			<?php //print_r($this->letters);print_r($letter);exit; ?>
			<?php foreach ($this->letters AS $key => $l): ?>
				<?php if ($key == 0): ?>
                    <a class="btn mx-0 py-2 px-3 <?php if ($letter == strtolower($l) || empty($letter)): ?>btn-primary<?php else: ?>btn-outline-primary<?php endif; ?>" href="<?php echo JRoute::_('index.php?option=com_jomdirectory&view=categories&letter=all&Itemid=' . JRequest::getInt('Itemid')) ?>"><?php echo $l ?></a>
				<?php else: ?>
                    <a class="btn mx-0 py-2 px-3 <?php if ($letter == $l): ?>btn-primary<?php else: ?>btn-outline-primary<?php endif; ?>" href="<?php echo JRoute::_('index.php?option=com_jomdirectory&view=categories&letter=' . $l . '&Itemid=' . JRequest::getInt('Itemid')) ?>"><?php echo strtoupper($l) ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>
    <div class="row">
		<?php $i = 1;
		$all = count($this->items);
		foreach ($this->items AS $cat): $i++;
			if ($type == 1) {
				$link = JRoute::_(JomdirectoryHelperRoute::getCategoryRoute($cat->id));
			} else {
				$link = JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute($cat->id));
			}
			if (isset($this->images[$cat->id])) $images = $this->images[$cat->id]; else $images = false;

			$column_size = 12;
			switch ($this->params->get('cat_columns')) {
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
            <div class="col-md-<?php echo $column_size; ?>">
                <div class="card mt-3">
                    <div class="card-body p-2">
                        <a class="" href="<?php echo $link ?>">
							<?php if ($images): ?>
								<?php echo JHTML::_('image', $images, $cat->title, '') ?>
							<?php else: ?>
								<?php echo JHTML::_('image', JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg', 'no-photo') ?>
							<?php endif; ?>
                        </a>
                    </div>
					<?php if ($this->params->get('categoires_sub')): ?>
						<?php if (isset($cat->deeper)) : ?>
                            <div class="jd-cat-sub">
                                <div class="jd-cat-subcat p-2 mb-1">
									<?php $howDeeper = count($cat->deeper);
									foreach ($cat->deeper AS $key => $deep):
										if ($type == 1) {
											$linkDeep = JRoute::_(JomdirectoryHelperRoute::getCategoryRoute($deep->id));
										} else {
											$linkDeep = JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute($deep->id));
										}
										?>
                                        <div class=""><i class="mdi mdi-folder-multiple-outline"></i> <?php echo JHTML::_('link', $linkDeep, $deep->title, 'class="jd-subcat-title"') ?> <?php if ($howDeeper - 1 > $key): ?><?php endif; ?></div>
									<?php endforeach; ?>
                                </div>
                            </div>
						<?php endif; ?>
					<?php endif; ?>
                    <div class="card-footer p-2 clearfix">
                        <span class="card-title text-uppercase font-weight-bold d-block float-left"><i class="fa fa-folder-open-o"></i> <?php echo JHTML::_('link', $link, $cat->title, 'class="text-primary"') ?> </span>
						<?php if ($this->params->get('categoires_count')): ?>
                            <span class="btn m-0 px-2 py-1 bg-success text-white float-right"> <?php echo $cat->how ?></span>
						<?php endif; ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</div>  