<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;


JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$runScript = "window.addEvent('domready', function() {";

// add tabs
if ($this->item->maps_lat && $this->item->maps_lng) {
	$runScript .= "Comdev.maps.set({zoom: " . $this->params->get('map_zoom', 15) . ",container: 'jd-item-box-maps', lat: " . $this->item->maps_lat . ", lng: " . $this->item->maps_lng . "});";
	$runScript .= "Comdev.maps.init();";
}

$runScript .= "}); ";

// add scripts
$document = JFactory::getDocument();
$document->addScript('https://maps.google.com/maps/api/js?key=' . $this->params->get('maps_api_key') . '&libraries=places');
$document->addScriptDeclaration($runScript);
$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_item_print.css');
?>


<div id="jd-item-wrapper" class="">

    <div class="my-3">
        <a onclick="window.print(); return false;" href="#" class="btn btn-primary">
            <i class="fa fa-print"></i> <?php echo JText::_('COM_JOMDIRECTORY_PRINT'); ?>
        </a>
    </div> <!--END OF JD-ITEM-BOX-HEADER -->
    <hr/>
    <div class="my-3">
        <h1 class="jd-itemTitle" itemprop="name"><?php echo $this->item->title ?></h1>
        <div class="jd-itemAddressBox">
			<?php if (!empty($this->item->address)) : ?>
                <i class="fa fa-map-marker"></i>
                <span class="jd-itemAddress" itemprop="addressLocality"><?php echo implode('<span class="jd-item-middot"> · </span> ', $this->item->address) ?></span>
                <span class="jd-itemFullAddress" itemprop="streetAddress"><?php echo $this->item->fulladdress ?></span>
			<?php endif; ?>
        </div>
    </div>
    <hr/>
    <div class="row" style="margin: 0 -15px;">
        <div class="col-6">
			<?php echo $this->item->fields->showGroup("item") ?>
        </div>
        <div class="col-6">
			<?php if (isset($this->item->images->intro)): ?>
				<?php foreach ($this->item->images->intro AS $img): ?>
					<?php echo JHTML::_('image', $img->small, 'target', 'class="img-thumbnail mr-1 mb-1"') ?>
				<?php endforeach; ?>
			<?php endif; ?>
        </div>
    </div>
    <hr/>
    <div class="my-3">
        <h2><?php echo JText::_('COM_JOMDIRECTORY_TAB_DETAILS'); ?></h2>
        <p><?php echo $this->item->fulltext ?></p>
    </div>
    <hr/>
    <div class="my-3">
        <h2><?php echo JText::_('COM_JOMDIRECTORY_TAB_LOCATION'); ?></h2>
        <div id="jd-item-box-maps" style="width: 100%; height: 200px;"></div>
    </div>


</div><!--END OF JD-ITEM-WRAPPER -->