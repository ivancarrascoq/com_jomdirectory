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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');

// add scripts
$document = JFactory::getDocument();

$runScript = "window.addEvent('domready', function() {";

$runScript .= "
jQuery(document).on('click', '[data-toggle=\"lightbox\"]', function(event) {
	event.preventDefault();
	jQuery(this).ekkoLightbox();
});
jQuery('#share-btn').popover({
	html : true,
	content: function() {
		return jQuery('.popover-html').html();
	},
});
";

if ($this->params->get('enable_yelp')):
	$runScript .= "jQuery('.yelp_stars').rating({theme: 'krajee-fa', min: 0,  max: 5,  size: 'xs', showCaption: false,  showClear: false });";
endif;

if ($this->params->get('enable_reviews')):

	//$runScript .= "jQuery('.dial_st').val (function () { return this.value/20});";
	$runScript .= "jQuery('.dial_st').rating({theme: 'krajee-fa', min: 0,  max: 5,  size: 'xs', disabled: true,  showCaption: false,  showClear: false });";
	$runScript .= "jQuery('.dial_st').parent().css('font-size', '14px');";

	$runScript .= "
    jQuery('.jd-ajax-json-jq').click(function(event){
                event.preventDefault();
                var href = this.getProperty('href');
                var rel = eval('(' + this.getProperty('rel') + ')');
                 jQuery.ajax({
                    type: 'GET',
                    async: false,
                    url: href,
                    success: function(responseText) {
                        var data = responseText;
                        var cnt =  jQuery('#'+rel.cnt);
                        if(rel.type=='vote') {
                            var info = Joomla.JText._('COM_JOMCOMDEV_REVIEWS_THANKS','Thanks! (No Language in JS)');
                            cnt.html(info +' <i class=\"fa fa-check-circle-o\"></i> <i class=\"fa fa-times-circle-o\"></i> <span class=\"jd-likes-green\"> '+data.yes+'</span> | <span class=\"jd-likes-red\">'+data.no+'</span>');
                        }
                      }
                });
          });
    ";
	if ((!$this->params->get('reviews_guest_allow') && !$user->id)) {
		//$runScript .= "jQuery('.dial').val (function () {;return this.value/20});";
		$runScript .= "jQuery('.dial').rating({theme: 'krajee-fa', min: 0,  max: 5,  size: 'xs', showCaption: false,  showClear: false });";
		$runScript .= "jQuery('.dial').parent().css('font-size', '14px');";
	}

endif;


$runScript .= "Comdev.ajax.init();";
if ($this->item->maps_lat && $this->item->maps_lng && $this->params->get('enable_map')) {
	$runScript .= "Comdev.maps.set({zoom: " . $this->params->get('map_zoom', 15) . ",container: 'jd-item-box-maps', lat: " . $this->item->maps_lat . ", lng: " . $this->item->maps_lng . ", typeMapView: " . $this->params->get('map_type', 0) . "});";
	$runScript .= "Comdev.maps.init();";
}

$runScript .= "}); ";


$document->addScript('https://maps.google.com/maps/api/js?key=' . $this->params->get('maps_api_key') . '&libraries=places');


//$document->addScript(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/js/star-rating.js');
//$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/css/star-rating.css');
//$document->addScript(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/themes/krajee-fa/theme.js');
//$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/node_modules/bootstrap-star-rating/themes/krajee-fa/theme.css');

//$document->addScript(JURI::base() . 'components/com_jomcomdev/node_modules/ekko-lightbox/dist/ekko-lightbox.js');
//$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/node_modules/ekko-lightbox/dist/ekko-lightbox.css');

//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.css');


$document->addScriptDeclaration($runScript);

$countImg = 0;
if (!empty($this->item->images->intro)) {
	$countImg = count($this->item->images->intro);
	//Was jssor
}

// FACEBOOK 
if (isset($this->item->images->intro[0])) $document->addCustomTag('<meta property="og:image" content="' . $this->item->images->intro[0]->big . '" />');
?>




