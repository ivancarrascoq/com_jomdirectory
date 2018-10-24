<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

$countItems = count($this->items);

$app = JFactory::getApplication();
$saved = $app->getUserState('com_jomdirectory.items.list.save');

$runScript = "window.addEvent('domready', function() {";
// add tabs
$runScript .= "Comdev.ajax.init();";
//$runScript .= $this->itemsMap;
$enableMapListing = $this->params->get('enable_maps_listing', 1);
if ($enableMapListing) $runScript .= "Comdev.maps.set({container: 'jd-itemsMap'});";
if ($enableMapListing) $runScript .= "Comdev.maps.initList('" . $this->itemsMap . "');";
$runScript .= "}); ";

$document = JFactory::getDocument();
// add scripts
if ($enableMapListing) {
	$document->addScript('https://maps.google.com/maps/api/js?key=' . $this->params->get('maps_api_key') . '&libraries=places');
	$document->addScript(JURI::root() . 'components/com_jomcomdev/javascript/markerclusterer.js');
	$document->addScript(JURI::root() . 'components/com_jomcomdev/javascript/markerwithlabel.js');
}
$document->addScriptDeclaration($runScript);

$startNumberPage = 0;
if (JRequest::getInt('limitstart') > 0) $startNumberPage = JRequest::getInt('limitstart');
if (JRequest::getInt('start') > 0) $startNumberPage = JRequest::getInt('start');
JHtml::_('jquery.framework');

$document->addScript(JURI::base() . 'components/com_jomcomdev/javascript/bootstrap-star-rating-master/js/star-rating.js');
$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/javascript/bootstrap-star-rating-master/css/star-rating.css');
$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/javascript/bootstrap-star-rating-master/css/bootstrap.css');
?>
<script>
	jQuery(function () {
		jQuery('.dial').val(function () {
			return this.value / 20
		});
		jQuery('.dial').rating({min: 0, max: 5, size: 'm', disabled: true, showCaption: false, showClear: false});
		jQuery('.dial').css('font-size', '12px');
	});
</script>


<div id="jd-grid-columns">

	<?php if ($this->params->get('enable_filterred')): ?>
		<?php echo $this->loadTemplate('filtered'); ?>
	<?php endif ?>

	<?php if ($countItems == 0): ?>
		<?php if ($this->params->get('enable_save')): ?>
			<?php if ($saved): ?>
				<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="cd-button"') ?>
			<?php endif; ?>
		<?php endif; ?>
		<div class="cd-alert"><i class="uk-icon-info"></i> <span class=""><?php echo JText::_('COM_JOMDIRECTORY_NO_ITEMS'); ?></span></div>
		<?php return; endif; ?>

	<div class="uk-panel">
		<form name="jdFormItems" action="#" method="post" class="uk-form">
			<div class="jd-toolbar-left uk-form-row uk-float-left">
				<label class="cd-form-label"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_BY'); ?></label>
				<?php
				$arraySort = array(
					array('v' => 'latest', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_LATEST')),
					array('v' => 'updated', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_UPDATED')),
					array('v' => 'alfa', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_ALPHABETICALLY')),
					array('v' => 'rated_desc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_DESC')),
					array('v' => 'rated_asc', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_RATED_ASC')),
					array('v' => 'most_viewed', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_MOSTVIEWED'))
				);
				?>
				<div class="uk-form-icon uk-form-icon-flip uk-display-inline">
					<i class="uk-icon-caret-down"></i>
					<?php echo JHtml::_('select.genericlist', $arraySort, 'jdItemsSort', 'class="" onChange="this.form.submit()"', 'v', 't', $this->state->{'list.sort'}); ?>
				</div>
				<label class="cd-form-label"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_PERPAGE'); ?></label>
				<div class="uk-form-icon uk-form-icon-flip uk-display-inline">
					<i class="uk-icon-caret-down"></i>
					<?php echo JHtml::_('select.genericlist', array(array('text' => 8, 'value' => 8), array('text' => 10, 'value' => 10), array('text' => 16, 'value' => 16), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="uk-form-width-mini" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
				</div>
			</div>
			<div class="jd-toolbar-right uk-float-right">
				<?php if ($this->params->get('enable_save')): ?>
					<?php if ($saved): ?>
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewUnSaved'), JText::_('COM_JOMDIRECTORY_UNSAVED'), 'class="cd-button"') ?> &nbsp;
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.clearSaved'), JText::_('COM_JOMDIRECTORY_CLEARSAVED'), 'class="cd-button"') ?> &nbsp;
					<?php else: ?>
						<?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item.viewSaved'), "<i class=\"fa fa-star\"></i> " . JText::_('COM_JOMDIRECTORY_SAVED'), 'class="cd-button"') ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</form>
	</div>

	<?php if ($enableMapListing): ?>
		<div class="uk-panel jd-itemsMapBox">
			<div id="jd-itemsMap" style="width: 100%; height: 300px;"></div>
		</div>
	<?php endif; ?>

	<div class="uk-panel jd-pages uk-clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
			<div class="cd-pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
	</div>

	<div class="uk-panel jd-itemsBody">
		<div class="uk-grid uk-grid-width-1-1 uk-grid-width-small-1-2 uk-grid-width-medium-1-<?php echo $this->params->get('columns-grid'); ?>" data-uk-grid-match="{target:'.uk-panel'}">

			<?php foreach ($this->items as $key => $item):
				$link = JRoute::_(JomdirectoryHelperRoute::getArticleRoute($item->id, $item->alias, $item->categories_id, $item->categories_address_id));
				if (isset($this->images[$item->id]))
					$images = $this->images[$item->id];
				else
					$images = false;
				$number = ($key + 1) + $startNumberPage;
				?>
				<div class="uk-grid-margin">
					<div class="uk-panel jd-box <?php if ($item->featured) echo 'jd-itemPremium'; ?>">
						<a id="itemJc<?php echo $number ?>"></a>
						<div class="jd-boxInner">
							<?php if ($item->featured): ?>
								<span class="jd-featured uk-display-block uk-position-absolute"><?= JText::_('COM_JOMDIRECTORY_FIELD_FEATURED'); ?></span>
							<?php endif; ?>
							<a href="<?php echo $link ?>" title="<?php echo $item->title; ?>">
								<?php if ($images): ?>
									<?php echo JHTML::_('image', $images, 'img', 'class="jd-itemImage"') ?>
								<?php else: ?>
									<?php echo JHTML::_('image', JURI::root() . 'components/com_jomcomdev/assets/images/nophoto.jpg', 'no-photo', 'class="jd-itemImage"') ?>
								<?php endif; ?>
							</a>
							<div class="jd-item-box-cover cd-box-sizing uk-position-absolute"></div>
							<div class="jd-item-box-details uk-position-absolute">
								<a href="<?php echo $link ?>" class="uk-position-z-index"><?php echo JText::_('COM_JOMDIRECTORY_DETAILS'); ?> <i class="fa fa-chevron-right"></i></a>
							</div>
							<div class="jd-item-box-category uk-position-absolute"><?php echo $item->category_title ?></div>
							<?php if ($this->params->get('enable_user_logo')): ?>
								<?php if (isset($this->images_logo[$item->id])) : ?>
									<div class="jd-item-box-logo uk-position-absolute">
										<div class="jd-item-logo uk-text-center">
											<?php echo JHTML::_('image', $this->images_logo[$item->id], 'brand', 'style="max-width: ' . $this->params->get('image_logo_width') . 'px"') ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endif; ?>
							<div class="jd-item-box-address cd-box-sizing uk-position-absolute cd-padding-left">
								<?php if ($this->params->get('enable_reviews')): ?>
									<?php if ($item->rateHow > 0): ?>
										<div class="jd-stars jd-item-box-score uk-position-absolute">
											<input value="<?php echo round($item->rateSum / $item->rateHow * 20) ?>" type="text" readonly class="dial" data-width="50" data-height="50" data-displayPrevious="true" data-fgColor="#a88e4b" data-thickness=".2">
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($item->address): ?>
									<div class="jd-item-address uk-position-absolute cd-box-sizing"><i class="fa fa-map-marker"></i> <?php echo implode(', ', $item->address) ?> <?php echo $item->fulladdress ?></div>
								<?php endif; ?>
							</div>
							<div class="jd-item-title cd-box-sizing uk-position-absolute <?php if ($item->featured): ?>jd-title-premium<?php endif; ?>">
								<h2 class="uk-display-inline-block cd-padding-left"><?php echo $item->title; ?></h2>
								<div id="jd-item-box-toolbar-fav<?php echo $item->id ?>" class="uk-float-right cd-padding-right">
									<?php if ($this->params->get('enable_save')): ?>
										<?php if (!JomcomdevHelperRemember::checkFavorite($item->id)): ?>
											<a class="jd-ajax-json cd-button cd-button-white cd-button-small" href="/index.php?option=com_jomcomdev&task=ajax.addFavorite&format=json&id=<?php echo $item->id ?>" rel="{cnt: 'jd-item-box-toolbar-fav<?php echo $item->id ?>', type: 'favorite'}">
												<i class="fa fa-star-o"></i>
											</a>
										<?php else: ?>
											<span class="cd-button cd-button-small cd-button-success"><i class="fa fa-star"></i></span>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<div class="jd-pages cd-margin uk-clearfix">
				<?php if ($this->pagination->get('pages.total') > 1): ?>
					<div class="cd-pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>