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


<div id="jomdirectory-categories-square" class="bootstrap">
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
		<?php
		$i = 1;
		$all = count($this->items);
		foreach ($this->items AS $cat):
			$i++;
			if ($type == 1) {
				$link = JRoute::_(JomdirectoryHelperRoute::getCategoryRoute($cat->id));
			} else {
				$link = JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute($cat->id));
			}
			//$link = JRoute::_('index.php?option=com_jomdirectory&view=items&categories_id='.$cat->id); 
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
                <div class="car mt-3">
                    <div class="p-0 view overlay">
						<?php if ($this->params->get('categoires_img')): ?>
							<?php if ($images): ?>
								<?php echo JHTML::_('image', $images, $cat->title, 'class="card-img"') ?>
							<?php else: ?>
								<?php echo JHTML::_('image', JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg', 'no-photo', 'class="card-img"') ?>
							<?php endif; ?>
                            <div class="h-100 d-flex justify-content-start">
                                <div class="position-absolute align-self-end text-white text-uppercase p-3">
									<?php echo $cat->title ?>
                                </div>
                            </div>
                            <div class="mask rgba-black-light d-flex">
                                <div class="mx-auto align-self-center">
                                    <a class="btn btn-primary" href="<?php echo $link ?>"><i class="fa fa-chevron-right"></i></a>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</div>

