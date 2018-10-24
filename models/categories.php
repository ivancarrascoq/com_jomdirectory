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
class JomdirectoryModelCategories extends JModelList
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{

		$params = $this->getState('params');
		$type = $params->get('type', 0);


		$app = JFactory::getApplication();
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*, count(b.id) AS how')
//				'a.id AS id, a.title AS title, a.alias AS alias, a.users_id, '.
//				'a.published AS published, a.categories_id AS categories_id, '.
//				'a.date_publish AS date_publish, a.date_publish_down AS date_publish_down'
//				'a.checked_out AS checked_out,'.
//				'a.checked_out_time AS checked_out_time, a.catid AS catid,' .
//				'a.clicks AS clicks, a.metakey AS metakey, a.sticky AS sticky,'.
//				'a.impmade AS impmade, a.imptotal AS imptotal,' .
//				'a.state AS state, a.ordering AS ordering,'.
//				'a.purchase_type as purchase_type,'.
//				'a.language, a.publish_up, a.publish_down'
		);
		$query->from($db->quoteName('#__cddir_categories') . ' AS a');

		$query->where('a.extension = \'com_jomdirectory.jomdirectory\'');
		$query->join('LEFT', '#__cddir_categories AS c ON (c.lft>=a.lft AND c.rgt<=a.rgt)');
		if ($type == 0) {
			$query->join('LEFT', '#__cddir_products AS b ON (b.categories_id=c.id AND b.published=1)');
		} else {
			$query->join('LEFT', '#__cddir_content AS b ON (b.categories_id=c.id AND b.published=1)');
		}


		$categoryId = $params->get('categories_id', false);
		if (is_numeric($categoryId) && $categoryId != 0) {
			$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int)$cat_tbl->level;
			$query->where('a.lft >= ' . (int)$lft);
			$query->where('a.rgt <= ' . (int)$rgt);
			$query->where('a.id != ' . (int)$categoryId);
		}


		$query->where('a.published = 1');


		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int)substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
//		if ($language = $this->getState('filter.language')) {
//			$query->where('a.language = ' . $db->quote($language));
//		}
//        $letter = $this->getUserStateFromRequest($this->context.'.filter.letter', 'letter', false, 'string');
		$letter = $app->input->getString('letter', false);
		if ($letter && $letter != 'all') {
			$query->where('LOWER(SUBSTRING(a.title, 1 ,1)) LIKE \'' . $db->escape($letter) . '\'');
		}


		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		// Add the list ordering clause.
//		$orderCol	= $this->state->get('list.ordering', 'a.id');
//		$orderDirn	= $this->state->get('list.direction', 'ASC');
//
		$query->group('a.id');
		$query->order($db->escape('a.lft ASC'));

//		echo nl2br(str_replace('#__','e1skn_',$query));
//        exit;

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter_published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

//		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.categories_id', 'filter_categories_id', '', 'string');
		$categoryId = JRequest::getInt('categories_id');
		$this->setState('filter.categories_id', $categoryId);

//		$letter = $this->getUserStateFromRequest($this->context.'.filter.letter', 'letter', false, 'string');
//                echo $letter.'a';
//        if($letter=='all' || $letter = false) {
//            $this->setState('filter.letter', false);
//        } else {
//            $this->setState('filter.letter', $letter);
//        }

//		$typeId = $this->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id', '', 'string');
//		$this->setState('filter.type_id', $typeId);

//		$userId = $this->getUserStateFromRequest($this->context.'.filter.users_id', 'filter_users_id', '', 'string');
//		$this->setState('filter.users_id', $userId);

//		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
//		$this->setState('filter.language', $language);


		$params = JComponentHelper::getParams('com_jomdirectory');

		$menu = $app->getMenu();
		$this->active = $menu->getActive();
		if ($this->active) {
			$menuParams = $this->active->params;
//                    $global = $menuParams->get('global_option', 1);
//                    if(!$global) {
			$paramsa = $menuParams->toArray();
			$paramsb = $params->toArray();
			foreach ($paramsa AS $key => $p) $paramsb[$key] = $p;
			$newObject = (object)$paramsb;
			$newObject->activeItemid = $this->active->id;
			$params->loadObject($newObject);
//                    }
		}

		$this->setState('params', $params);
//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            print_r($params);
//            echo '</pre>';
//exit;

		$ordering = $this->getUserStateFromRequest($this->context . '.filter.order', 'filter_order', '', 'string');
		$direction = $this->getUserStateFromRequest($this->context . '.filter.order_Dir', 'filter_order_Dir', '', 'string');

		// List state information.
//		parent::populateState($ordering, $direction);
	}

}
