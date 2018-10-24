<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

$countItems = count($this->items);

$app = JFactory::getApplication();
$saved = $app->getUserState('com_jomdirectory.items.list.save');

$runScript = "window.addEvent('domready', function() {";

$runScript .= "Comdev.ajax.init();";

$enableMapListing = $this->params->get('enable_maps_listing', 1);
if ($enableMapListing) $runScript .= "Comdev.maps.set({container: 'jd-itemsMap'});";
if ($enableMapListing) $runScript .= "Comdev.maps.initList('" . $this->itemsMap . "');";
$runScript .= "}); ";

$document = JFactory::getDocument();

if ($enableMapListing) {
	$document->addScript('https://maps.google.com/maps/api/js?key=' . $this->params->get('maps_api_key') . '&libraries=places');
	$document->addScript(JURI::root() . 'components/com_jomcomdev/javascript/markerclusterer.js');
	$document->addScript(JURI::root() . 'components/com_jomcomdev/javascript/markerwithlabel.js');
}
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
	var zzz;
	jQuery(function () {
		/*jQuery('.dial').val(function () {
			return this.value / 20
		});*/
		jQuery('.dial').rating({theme: 'krajee-fa', min: 0, max: 5, size: 'xs', disabled: true, showCaption: false, showClear: false});
		jQuery('.dial').parent().css('font-size', '14px');

		jQuery('.marker-hover').mouseenter(function () {
			var id = jQuery(this).attr('id');
			id = id.replace('itemJc', '');
			jQuery('img[src="/components/com_jomcomdev/assets/images/iconMap.png?itemJc=' + id + '"]').addClass('marker-hovered');
		}).mouseleave(function () {
			var id = jQuery(this).attr('id');
			id = id.replace('itemJc', '');
			jQuery('img[src="/components/com_jomcomdev/assets/images/iconMap.png?itemJc=' + id + '"]').removeClass('marker-hovered');
		});
	});
</script>

