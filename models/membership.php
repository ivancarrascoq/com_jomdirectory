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

jimport('joomla.application.component.modellist');

/**
 * Jomdirectory Component Category Model
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryModelMembership extends JModelList
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';

	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app = JFactory::getApplication();
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = JRequest::getVar($request, $old_state, 'default', $type);

		// Save the new value only if it is set in this request.
		if ($new_state !== null) {
			$app->setUserState($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

	public function priceChange($data, $priceParams)
	{
		if (!(int)$data) return JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_FREE');
		if (is_array($priceParams) && !empty($priceParams)) {
			$d = new stdClass;
			$d->price_netto = $data;

			if (isset($priceParams['tax'])) {
				$vat = $priceParams['tax'] / 100;
				$d->price = $d->price_netto + ($d->price_netto * $vat);
			}

			$d->price = number_format($d->price, $priceParams['decimal_digits'], $priceParams['currency_rest_separator'], $priceParams['number_format']);

			if (isset($priceParams['currency'])) {
				switch ($priceParams['currency_position']) {
					case '1':
						$d->price = $priceParams['currency'] . ' ' . $d->price;
						break;
					case '2':
					default:
						$d->price = $d->price . ' ' . $priceParams['currency'];
						break;
				}
			}
		}
		return $d->price;
	}

	public function getFields()
	{
		$field = new Main_Fields(false, 'com_jomdirectory', 'all');
		$paid_field_id = $field->getGroupsIDs();
		$query = $this->_db->getQuery(true);
		$query->select('a.id, a.name');
		$query->from('#__cddir_fields AS a');
		$query->join('inner', '#__cddir_categories AS b ON b.id = a.categories_type_id');
		$query->where('(a.categories_group_id = ' . (int)$paid_field_id["COM_JOMCOMDEV_TYPE_FIELDS_GROUP_PAIDITEM"] . ' OR a.categories_group_id = ' . (int)$paid_field_id["COM_JOMCOMDEV_TYPE_FIELDS_GROUP_PAIDITEMS"] . ')');
		$query->where('a.published = 1');
		$query->where('(a.extension = "com_jomdirectory" OR a.extension = "com_jomdirectory.products")');


		$this->_db->setQuery($query);
		$items = $this->_db->loadObjectList();
		return $items;
	}

	protected function getListQuery()
	{

		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select', 'a.id, a.name, a.price_monthly, a.price_annually, a.best_value, a.listings_nr, a.images_nr, a.attachments, a.premium_nr, a.video, a.group_id, a.paid_fields, a.description  '));

		$query->from($db->quoteName('#__cddir_plans') . ' AS a');
		$query->order($db->escape('price_annually, price_monthly'));
		$query->where("extension='com_jomdirectory'");
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');

		$query->group($db->escape('a.id'));
		return $query;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('site');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_jomdirectory');

		$menu = $app->getMenu();
		$this->active = $menu->getActive();
		if ($this->active) {
			$menuParams = $this->active->params;
			$global = $menuParams->get('global_option');
			if (!$global) {
				$paramsa = $menuParams->toArray();
				$paramsb = $params->toArray();
				foreach ($paramsa AS $key => $p) $paramsb[$key] = $p;
				$newObject = (object)$paramsb;
				$newObject->activeItemid = $this->active->id;
				$params->loadObject($newObject);
			}
		}
		$this->setState('params', $params);
	}
}