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
?>

<div id="jdCarousel" class="carousel slide carousel-fade carousel-thumbnails mb-5" data-ride="carousel">
    <?php $ratio = $this->params->get('product_main_gallery_format'); //Set ration to slider
    if ($ratio != 0) {
        $ratio = explode('/', $ratio);
        $ratio = $ratio[1]/$ratio[0]*100;
    }?>
    <div class="carousel-inner" role="listbox" style="<?= ($ratio) ? 'padding-bottom:'.$ratio.'%;' : '' ?>">
        <div class="<?= ($ratio) ? 'image-inner' : '' ?>">
		<?php foreach ($this->item->images->intro AS $key => $img): ?>
            <div class="carousel-item <?php if ($key == 0) {
				echo 'active';
			} ?>">
                <img class="d-block" u="image" src="<?php echo $img->big ?>" alt="slide-<?php echo $key ?>">
            </div>
		<?php endforeach; ?>
        </div>
        <a class="carousel-control-prev" href="#jdCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#jdCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span>
        </a>
    </div>
    <ol class="carousel-indicators position-relative">
		<?php foreach ($this->item->images->intro AS $key => $img): ?>
            <li data-target="#jdCarousel" data-slide-to="<?php echo $key ?>" class="<?php if ($key == 0) {
				echo 'active';
			} ?>"><img class="d-block w-100" src="<?php echo $img->big ?>" class="img-fluid"></li>
		<?php endforeach; ?>
    </ol>
</div>
