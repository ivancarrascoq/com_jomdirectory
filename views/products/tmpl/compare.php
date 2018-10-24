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

$user = JFactory::getUser();

$countItems = count($this->items);

$app = JFactory::getApplication();
$compared = $app->getUserState('com_jomdirectory.products.list.compare');

$runScript = "window.addEvent('domready', function() {";
// add tabs
$runScript .= "Comdev.ajax.init();";

$runScript .= "}); ";

$document = JFactory::getDocument();
// add scripts
$document->addScriptDeclaration($runScript);

$startNumberPage = 0;
if (JRequest::getInt('limitstart') > 0) $startNumberPage = JRequest::getInt('limitstart');
if (JRequest::getInt('start') > 0) $startNumberPage = JRequest::getInt('start');
$jVerArr = explode('.', JVERSION);

JHtml::_('jquery.framework');


//$document->addScript(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/js/star-rating.js');
//$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/css/star-rating.css');
//$document->addScript(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/themes/krajee-fa/theme.js');
//$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/themes/krajee-fa/theme.css');
?>
<script>
	jQuery(function () {
		/*jQuery('.dial').val(function () {
			return this.value / 20
		});*/
		jQuery('.dial').rating({theme: 'krajee-fa', min: 0, max: 5, size: 'xs', disabled: true, showCaption: false, showClear: false});
		jQuery('.dial').parent().css('font-size', '14px');
	});
</script>


<div id="jomdirectory-products" class="jomdirectory clearfix bootstrap">

	<?php echo $this->loadTemplate('filtered'); ?>

	<?php if ($countItems == 0): ?>
		<?php if ($compared): ?>
			<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=product.viewUnCompared'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"'); ?>
		<?php endif; ?>
        <div class="alert alert-primary mt-5"><i class="fa fa-info"></i> <span class=""><?php echo JText::_('COM_JOMDIRECTORY_NO_ITEMS'); ?></span></div>
		<?php return;
	endif; ?>

    <form name="jdFormItems" action="#" method="post" class="clearfix">
        <div class="clearfix">
            <div class="form-row align-items-center float-left">
                <label class="my-0 mx-1"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_BY'); ?></label>
				<?php
				$arraySort = array(array('v' => 'latest', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_LATEST')), array('v' => 'updated', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_UPDATED')), array('v' => 'alfa', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_ALPHABETICALLY')), array('v' => 'rated_desc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_DESC')), array('v' => 'rated_asc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_ASC')), array('v' => 'viewed', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_MOSTVIEWED')));

				?>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', $arraySort, 'jdItemsSort', 'class="custom-select mb-0" onChange="this.form.submit()"', 'v', 't', $this->state->{'list.sort'}); ?>
                </div>
                <div class="d-block d-md-none w-100 my-2"></div>
                <label class="my-0 mx-1"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_PERPAGE'); ?></label>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', array(array('text' => 8, 'value' => 8), array('text' => 10, 'value' => 10), array('text' => 15, 'value' => 15), array('text' => 16, 'value' => 16), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
                </div>
            </div>
            <div class="float-right">
				<?php if ($this->params->get('product_save')): ?>
					<?php if ($compared): ?>
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=product.viewUnCompared'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="btn btn-primary"') ?> &nbsp;
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=product.clearCompared'), JText::_('COM_JOMDIRECTORY_CLEAR_COMPARED'), 'class="btn btn-primary"') ?> &nbsp;
					<?php endif; ?>
				<?php endif; ?>
            </div>
        </div>
    </form>

    <div class="pagination my-3 clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>
    <div class="row">
		<?php $column_size = 12;
		switch ($this->params->get('product_layout')) {
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
		<?php foreach ($this->items as $key => $item):
			$link = JRoute::_(JomdirectoryHelperRoute::getProductRoute($item->id, $item->alias, $item->categories_id));
			if (isset($this->images[$item->id])) $images = $this->images[$item->id]; else $images = false;
			$number = ($key + 1) + $startNumberPage;
			?>
            <a id="itemJc<?php echo $number ?>"></a>
            <div class="col-<?php echo $column_size; ?>">
                <div class="card">
                    <div class="position-relative view overlay">
						<?php if ($item->fields->showGroup("state", false) != ""): ?>
                            <div class="je-ribbon-wrapper-green">
                                <div class="je-ribbon-green"><?php echo $item->fields->showGroup("state") ?></div>
                            </div>
						<?php endif; ?>
						<?php if ($item->featured): ?>
                            <div class="jd-product-featured position-absolute"><?= JText::_('COM_JOMDIRECTORY_FIELD_FEATURED'); ?></div>
						<?php endif; ?>

						<?php if (isset($this->images_logo[$item->id])) : ?>
                            <div class="card-img">
								<?php echo JHTML::_('image', $this->images_logo[$item->id], 'brand') ?>
                            </div>
						<?php else: ?>
							<?php if ($images): ?>
								<?php echo JHTML::_('image', $images, 'img', 'class="card-img"') ?>
							<?php else: ?>
								<?php $width = $this->params->get('image_items_width') . "px"; ?>
								<?php echo JHTML::_('image', JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg', 'no-photo', 'class="card-img"') ?>
								<?php $images = JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg'; ?>
							<?php endif; ?>
						<?php endif; ?>
                        <div class="mask rgba-black-light d-flex">
                            <div class="mx-auto align-self-center">
                                <a class="btn btn-danger btn-sm m-0 px-3" href="<?php echo $link ?>"><?php echo JText::_('COM_JOMDIRECTORY_DETAILS'); ?> <i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <h3 class="card-title my-3 text-center">
                            <a href="<?php echo $link ?> " title="<?php echo $item->title; ?>">
								<?php echo $item->title; ?>
                            </a>
                        </h3>
                        <div class="card-text position-relative clearfix">
							<?php if ($this->params->get('product_price')): ?>
								<?php if ($item->price_old != 0): ?>
                                    <span class="d-block price-sale"><del><?php echo $item->price_old; ?></del></span>
								<?php endif; ?>
                                <span class="d-inline price"><?php echo $item->price; ?> </span>
							<?php endif; ?>
                            <div class="d-inline">
								<?php if ($this->params->get('enable_reviews')): ?>
                                    <div class="<?php if ($this->params->get('reviews_product_rating_method')): ?>jd-stars<?php else: ?>jd-knob<?php endif; ?> float-right text-center">
										<?php if ($item->rateHow > 0): ?>
                                            <div class="jd-scorePoints">
                                                <input value="<?php echo round($item->rateSum / $item->rateHow * 2) /2; ?>" type="text" readonly class="dial" data-width="50" data-height="50" data-displayPrevious="true" data-fgColor="#a88e4b" data-thickness=".1" autocomplete="off">
                                            </div>
										<?php endif; ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-2 mt-3 clearfix">
						<?php if ($this->params->get('cart')): ?>
                            <a data-property="add-to-cart" data-object="{id: <?= $item->id ?>, title: '<?= htmlspecialchars($item->title) ?>', price: <?= $item->price_int ?>, image: '<?= $images ?>'}" class="btn btn-info btn-sm">
                                <i class="fa fa-shopping-cart"></i> <?php echo JText::_('COM_JOMDIRECTORY_CART_ADD'); ?></a>
						<?php endif; ?>
                        <div id="jd-item-box-toolbar-comp<?php echo $item->id ?>" class="float-right ml-1">
							<?php if ($this->params->get('product_compare')): ?>
								<?php if (!JomcomdevHelperRemember::checkFavorite($item->id, 'com_jomdirectory_products_compare')): ?>
                                    <a class="jd-ajax-json btn btn-sm btn-light px-2 mx-1 my-0" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&extension=com_jomdirectory_products_compare&format=json&id=<?php echo $item->id ?>" rel="{cnt: 'jd-item-box-toolbar-comp<?php echo $item->id ?>', type: 'compare'}">
                                        <i class="fa fa-copy"></i>
                                    </a>
								<?php else: ?>
                                    <span class="btn btn-sm btn-light px-2 mx-1 my-0 disabled"><i class="fa fa-copy"></i></span>
								<?php endif; ?>
							<?php endif; ?>
                        </div>
                        <div id="jd-item-box-toolbar-fav<?php echo $item->id ?>" class="float-right">
							<?php if ($this->params->get('product_save')): ?>
								<?php if (!JomcomdevHelperRemember::checkFavorite($item->id, 'com_jomdirectory_products')): ?>
                                    <a class="jd-ajax-json btn btn-sm btn-light px-2 m-0" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&extension=com_jomdirectory_products&format=json&id=<?php echo $item->id ?>" rel="{cnt: 'jd-item-box-toolbar-fav<?php echo $item->id ?>', type: 'favorite'}">
                                        <i class="fa fa-heart-o"></i>
                                    </a>
								<?php else: ?>
                                    <span class="btn btn-sm btn-light px-2 mx-1 my-0 disabled"><i class="fa fa-heart"></i></span>
								<?php endif; ?>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
    <div class="pagination my-3 clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>
</div>
