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

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * JomDirectory Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_jomdirectory
 * @since 1.6
 */
abstract class JomdirectoryHelperRoute
{
	protected static $lookup;
	protected static $lookupProducts;
	protected static $lookupCart;


	public static function getArticleTagsRoute($id, $categoryId, $lang = false)
	{

		list($id, $alias) = explode(':', $id);

		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=item';
		$link .= '&id=' . $id;
		$link .= '&alias=' . $alias;
		if ((int)$categoryId >= 1) {
			$link .= '&categories_id=' . $categoryId;
			$needles['categories_id'] = $categoryId;
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid=' . $item;
		}


		return $link;

	}

	protected static function _findItem($needles = null)
	{
		//return 495;

		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');
		$itemid = null;

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component = JComponentHelper::getComponent('com_jomdirectory');
			if (JLanguageMultilang::isEnabled()) {
				$items = $menus->getItems(array('component_id', 'type', 'language'), array($component->id, 'component', JFactory::getLanguage()->getTag()));
			} else {
				$items = $menus->getItems(array('component_id', 'type'), array($component->id, 'component'));
			}

			self::$lookup['category'] = array();
			self::$lookup['address'] = array();
			self::$lookup['categoryandaddress'] = array();

			foreach ($items as $item) {
//                echo $item->link.'<br/>';
				if (isset($item->query) && isset($item->query['view']) && $item->query['view'] == 'items') {

					if (!empty($item->query['categories_id'])) $category = (int)$item->query['categories_id']; else $category = false;
					if (!empty($item->query['categories_address_id'])) $address = (int)$item->query['categories_address_id']; else $address = false;


					if (!$category && !$address) {
						self::$lookup['home'] = $item->id;
					}
					if ($category && !$address) {
						self::$lookup['category'][$category] = $item->id;
					}
					if (!$category && $address) {
						self::$lookup['address'][$address] = $item->id;
					}
					if ($category && $address) {
						self::$lookup['categoryandaddress'][$category . '-' . $address] = $item->id;
					}

				}
			}
//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            print_r(self::$lookup);
//            print_r($needles);
//            echo '</pre>';
//exit;

		}

		if (!empty($needles)) {
//            if (isset($needles['item'])) $id = (int) $needles['item'];
			if (!empty($needles['categories_id'])) $category = (int)$needles['categories_id']; else $category = false;
			if (!empty($needles['categories_address_id'])) $address = (int)$needles['categories_address_id']; else $address = false;

//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            print_r($needles);
//            echo '</pre>';
////exit;
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "<br/>";
//            echo 'Category: '.$category.' Address:'.$address.'<br/>';

			if ($category && !$address) {
				if (empty(self::$lookup['category'][$category])) {
					if (!empty(self::$lookup['home'])) return self::$lookup['home'];
				} else return self::$lookup['category'][$category];
			}
			if (!$category && $address) {
				if (empty(self::$lookup['address'][$address])) {
					if (!empty(self::$lookup['home'])) return self::$lookup['home'];
				} else return self::$lookup['address'][$address];
			}
			if ($category && $address) {
//                echo self::$lookup['categoryandaddress'][$category.'-'.$address].'3';
				if (empty(self::$lookup['categoryandaddress'][$category . '-' . $address])) {
					if (empty(self::$lookup['category'][$category])) {
						if (empty(self::$lookup['address'][$address])) {
							if (!empty(self::$lookup['home'])) return self::$lookup['home'];
						} else return self::$lookup['address'][$address];
					} else return self::$lookup['category'][$category];
				} else return self::$lookup['categoryandaddress'][$category . '-' . $address];
			}
		} else {
//            echo self::$lookup['home'].'4';
//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            print_r(self::$lookup['home']);
//            echo '</pre>';
//exit;
			if (!empty(self::$lookup['home'])) return self::$lookup['home'];
		}
		return null;
	}

	public static function getProductTagsRoute($id, $categoryId, $lang = false)
	{

		list($id, $alias) = explode(':', $id);

		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=product';
//        $link .= '&id='.$id;
		$link .= '&alias=' . $alias;
		if ((int)$categoryId >= 1) {
			$link .= '&categories_id=' . $categoryId;
			$needles['categories_id'] = $categoryId;
		}

		if ($item = self::_findProductItem($needles)) {
			$link .= '&Itemid=' . $item;
		}


		return $link;

	}

	protected static function _findProductItem($needles = null)
	{
		//return 495;

		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');
		$itemid = null;

		// Prepare the reverse lookup array.
		if (self::$lookupProducts === null) {
			self::$lookupProducts = array();

			$component = JComponentHelper::getComponent('com_jomdirectory');
			if (JLanguageMultilang::isEnabled()) {
				$items = $menus->getItems(array('component_id', 'language'), array($component->id, JFactory::getLanguage()->getTag()));
			} else {
				$items = $menus->getItems(array('component_id'), array($component->id));
			}


			self::$lookupProducts['category'] = array();
//            self::$lookup['address'] = array();
//            self::$lookup['categoryandaddress'] = array();

			foreach ($items as $item) {
//                echo $item->link.'<br/>';
				if (isset($item->query) && isset($item->query['view']) && $item->query['view'] == 'products') {

					if (!empty($item->query['categories_id'])) $category = (int)$item->query['categories_id']; else $category = false;
//                    if(!empty( $item->query['categories_address_id'])) $address = (int) $item->query['categories_address_id']; else $address = false;


					if (!$category) {
						self::$lookupProducts['home'] = $item->id;
					}
					if ($category) {
						self::$lookupProducts['category'][$category] = $item->id;
					}


				}
			}

//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            print_r(self::$lookupProducts);
//            echo '</pre>';
//exit;


		}

		if (!empty($needles)) {
			if (!empty($needles['categories_id'])) $category = (int)$needles['categories_id']; else $category = false;

			if ($category) {
				if (empty(self::$lookupProducts['category'][$category])) {
					if (!empty(self::$lookupProducts['home'])) return self::$lookupProducts['home'];
				} else return self::$lookupProducts['category'][$category];
			}


		} else {
			return self::$lookupProducts['home'];
		}
		return null;
	}

	public static function getArticleRoute($id, $alias, $categoryId, $categoryAddressId = false, $tmpl = false, $layout = false)
	{

		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=item';
		$link .= '&alias=' . $alias;
//        $link .= '&alias='.$alias;
		if ((int)$categoryId >= 1) {
			$link .= '&categories_id=' . $categoryId;
			$needles['categories_id'] = $categoryId;
		}
		if ((int)$categoryAddressId >= 1) {
			$link .= '&categories_address_id=' . $categoryAddressId;
			$needles['categories_address_id'] = $categoryAddressId;
		}

//        if ((int) $categoryId >= 1)
//        {
//            $needles = array(
//                'item'  => (int) $id);

		//Create the link
//            $link = 'index.php?option=com_jomdirectory&view=item&id='. $id .'&catid='.$categoryId;


		if ($tmpl) {
			$link .= '&tmpl=component';
		}
		if ($layout) {
			$link .= '&layout=' . $layout;
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid=' . $item;
		}


		return $link;

	}

	public static function getCartRoute($layout = false)
	{
		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=cart';


		if ($layout) {
			$link .= '&layout=' . $layout;
			$needles['layout'] = $layout;
		}
		if ($item = self::_findCartItem($needles)) {
			$link .= '&Itemid=' . $item;
		}

		return $link;

	}

	protected static function _findCartItem($needles = null)
	{
		//return 495;

		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');
		$itemid = null;

		// Prepare the reverse lookup array.
		if (self::$lookupCart === null) {
			self::$lookupCart = array();

			$component = JComponentHelper::getComponent('com_jomdirectory');
			if (JLanguageMultilang::isEnabled()) {
				$items = $menus->getItems(array('component_id', 'type', 'language'), array($component->id, 'component', JFactory::getLanguage()->getTag()));
			} else {
				$items = $menus->getItems(array('component_id', 'type'), array($component->id, 'component'));
			}

			foreach ($items as $item) {
				if (isset($item->query) && isset($item->query['view']) && $item->query['view'] == 'cart') {
					if (!empty($item->query['layout'])) $layout = $item->query['layout']; else $layout = 'default';
					self::$lookupCart[$layout] = $item->id;
				}
			}
		}

//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
////        var_dump(JLanguageMultilang::isEnabled());
//        echo $component->id.'aaa';
//        print_r($items);
//        print_r(self::$lookupCart);
//        echo '</pre>';
//exit;


		if (!empty($needles['layout'])) $layout = $needles['layout']; else $layout = 'default';

		if (!empty(self::$lookupCart[$layout])) {
			return self::$lookupCart[$layout];
		} elseif (!empty(self::$lookupCart['default'])) {
			return self::$lookupCart['default'];
		}


		return null;
	}

	public static function getProductRoute($id, $alias, $categoryId, $tmpl = false, $layout = false)
	{

		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=product';
		$link .= '&alias=' . $alias;
//        $link .= '&alias='.$alias;
		if ((int)$categoryId >= 1) {
			$link .= '&categories_id=' . $categoryId;
			$needles['categories_id'] = $categoryId;
		}

		if ($tmpl) {
			$link .= '&tmpl=component';
		}
		if ($layout) {
			$link .= '&layout=' . $layout;
		}

		if ($item = self::_findProductItem($needles)) {
			$link .= '&Itemid=' . $item;
		}


		return $link;

	}

	public static function getCategoryProductRoute($categoryId = null, $sort = false)
	{
		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=products';
		if ((int)$categoryId >= 1) {
			$link .= '&categories_id=' . $categoryId;
			$needles['categories_id'] = $categoryId;
		}


		//Create the link
		if ($item = self::_findProductItem($needles)) {
			if (isset($item)) {
				$link .= '&Itemid=' . $item;
			}
		}

		if (is_integer($sort)) {
			$link .= '&limitstart=' . $sort;
		}

		return $link;
	}

	public static function getCategoryRoute($categoryId = null, $categoryAddressId = null, $sort = false)
	{
		$needles = array();
		$link = 'index.php?option=com_jomdirectory&view=items';
		if ((int)$categoryId >= 1) {
			$link .= '&categories_id=' . $categoryId;
			$needles['categories_id'] = $categoryId;
		}
		if ((int)$categoryAddressId >= 1) {
			$link .= '&categories_address_id=' . $categoryAddressId;
			$needles['categories_address_id'] = $categoryAddressId;
		}


		//Create the link
		if ($item = self::_findItem($needles)) {
			if (isset($item)) {
				$link .= '&Itemid=' . $item;
			}
		}

		if (is_integer($sort)) {
			$link .= '&limitstart=' . $sort;
		}

		return $link;
	}
}