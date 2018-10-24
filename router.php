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

jimport('joomla.application.categories');

/**
 * Build the route for the com_jomdirectory component
 *
 * @param   array   An array of URL arguments
 * @return  array   The URL arguments to use to assemble the subsequent URL.
 * @since   1.5
 */
function jomdirectoryBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		if (current(explode('_', $query['view'])) == 'admin') {
			return $segments;
		}
		unset($query['view']);
	}

//    if (isset($query['id']) && isset($query['alias'])){
//        $segments[] = $query['id'].'-'.$query['alias'];
//        unset($query['alias']);
//        unset($query['id']);
//    }
	if (isset($query['alias'])) {
		$segments[] = $query['alias'];
		unset($query['alias']);
		unset($query['id']);
	}
//    unset($query['catid']);
	unset($query['limit']);
	unset($query['categories_id']);
	unset($query['categories_address_id']);

	return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param   array   The segments of the URL to parse.
 *
 * @return  array   The URL attributes to be used by the application.
 * @since   1.5
 */
function jomdirectoryParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();

	if (!empty($segments[0])) {
		$vars['alias'] = str_replace(':', '-', $segments[0]);
//        $article_data = explode(':',$segments[0]); 
//
//        if(!empty($article_data[0])) $vars['id'] = $article_data[0];
//        if(!empty($article_data[1])) $vars['alias'] = $article_data[1];
	}
	if (!empty($item->query['categories_id'])) $vars['categories_id'] = $item->query['categories_id'];
	if (!empty($item->query['categories_address_id'])) $vars['categories_address_id'] = $item->query['categories_address_id'];

	if ($item->query['view'] == 'items' && isset($vars['alias'])) {
		$vars['view'] = 'item';
		require_once JPATH_BASE . '/components/com_jomdirectory/models/item.php';
		$model = JModelLegacy::getInstance("Item", "JomdirectoryModel");
	}
	if ($item->query['view'] == 'products' && isset($vars['alias'])) {
		$vars['view'] = 'product';
		require_once JPATH_BASE . '/components/com_jomdirectory/models/product.php';
		$model = JModelLegacy::getInstance("Product", "JomdirectoryModel");
	}

	$item = $model->getItem($vars['alias']);

	$vars['id'] = $item->id;
	//var_dump($vars);

	return $vars;
}