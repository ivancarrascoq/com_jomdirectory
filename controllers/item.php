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
class JomdirectoryControllerItem extends JControllerLegacy
{

	public function viewSaved()
	{

		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.items.list.save', 1);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryRoute('')));
	}

	public function viewUnSaved()
	{


		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.items.list.save', 0);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryRoute('')));
	}

	public function clearSaved()
	{


		$app = JFactory::getApplication('site');
		$app->setUserState('com_jomdirectory.items.list.save', 0);
		JomcomdevHelperRemember::clearFavorite('com_jomdirectory');
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryRoute('')));
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

			$app->setUserState('com_jomdirectory.items.filter.search', '');
			$app->setUserState('com_jomdirectory.items.filter.address', '');
			$app->setUserState('com_jomdirectory.items.filter.categories_address_id', '');
			$app->setUserState('com_jomdirectory.items.filter.categories_id', '');
			$app->setUserState('com_jomdirectory.items.filter.fields', '');

			$app->setUserState('com_jomdirectory.items.filter.favorites', '0');
			$app->setUserState('com_jomdirectory.items.filter.featured', '0');


			$app->setUserState('com_jomdirectory.items.filter.address-lat-lng', '');
			$app->setUserState('com_jomdirectory.items.filter.latitude', '');
			$app->setUserState('com_jomdirectory.items.filter.longitude', '');
			$app->setUserState('com_jomdirectory.items.filter.distancew', '');
			$app->setUserState('com_jomdirectory.items.list.limitstart', '');
		}

		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCategoryRoute('')));
	}

}
