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

jimport('joomla.application.component.controller');

/**
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryControllerProduct extends JControllerLegacy
{

	public function viewSaved()
	{

		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.products.list.save', 1);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute('')));
	}

	public function viewUnSaved()
	{


		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.products.list.save', 0);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute('')));
	}

	public function viewUnCompared()
	{


		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.products.list.compare', 0);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute('')));
	}

	public function clearSaved()
	{


		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.products.list.save', 0);
		JomcomdevHelperRemember::clearFavorite('com_jomdirectory_products');
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute('')));
	}

	public function stateClear()
	{


		$app = JFactory::getApplication('site');

		$id = $app->input->getString('id', false);
		if ($id) {
			if ($id == 'com_jomdirectory.items.filter.fields') {
				$val = $app->input->getString('val', false);
				$values = $app->getUserState($id);
				foreach ($values AS $key => $v) {
					if (array_key_exists($val, $v)) {
						unset($values[$key]);
					}
				}
				$app->setUserState($id, $values);
			} else {
				$app->setUserState($id, '');
			}
		} else {
			$app->setUserState('com_jomdirectory.products.filter.search', '');
			$app->setUserState('com_jomdirectory.products.filter.address', '');
			$app->setUserState('com_jomdirectory.products.filter.categories_address_id', '');
			$app->setUserState('com_jomdirectory.products.filter.categories_id', '');
			$app->setUserState('com_jomdirectory.products.filter.fields', '');
			$app->setUserState('com_jomdirectory.products.list.limitstart', '');
			$app->setUserState('com_jomdirectory.products.filter.jd-mod-price_from', '');
			$app->setUserState('com_jomdirectory.products.filter.jd-mod-price_to', '');
			$app->setUserState('com_jomdirectory.products.filter.jd_mod_price', '');

			$app->setUserState('com_jomdirectory.products.filter.address-lat-lng', '');
			$app->setUserState('com_jomdirectory.products.filter.latitude', '');
			$app->setUserState('com_jomdirectory.products.filter.longitude', '');
			$app->setUserState('com_jomdirectory.products.filter.distancew', '');
		}

		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute('')));
	}

	public function viewCompared()
	{

		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.products.list.compare', 1);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute('')));
	}

	public function clearCompared()
	{


		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.products.list.compare', 0);
		JomcomdevHelperRemember::clearFavorite('com_jomdirectory_products_compare');
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute(''))//                    JRoute::_('index.php?Itemid=101')
		);
	}
}
