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
class JomdirectoryModelAdmin_services extends JModelList
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';
	protected $user;

	function __construct()
	{
		parent::__construct();
		$this->user = JFactory::getUser();
	}

	function getCategories()
	{
		$cat_array = array(array('v' => '', 't' => JText::_('COM_JOMDIRECTORY_ADM_ALL')));
		$query = "SELECT title, id, level FROM #__cddir_categories WHERE extension='com_jomdirectory.service' ORDER BY lft";
		$data = $this->_getList($query);
		foreach ($data as $row):
			for ($i = 1; $i < $row->level; $i++) $row->title = "&#160;&#160;&#160;" . $row->title;
			$temp_array = array('v' => $row->id, 't' => $row->title);
			array_push($cat_array, $temp_array);
		endforeach;
		return $cat_array;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getToolbar()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jomdirectory/assets/css/jomdirectory_admin.css');

		$controller = 'admin_services';
		jimport('joomla.html.toolbar');
		$bar = new JToolBar('toolbar');

		if (Joomla_Version::if3()) {
			$bar->appendButton('standard', 'new', JText::_('COM_JOMDIRECTORY_ADM_ADD'), $controller . '.add', false);
			$bar->appendButton('standard', 'publish', JText::_('COM_JOMDIRECTORY_ADM_PUBLISH'), $controller . '.publish', false);
			$bar->appendButton('standard', 'unpublish', JText::_('COM_JOMDIRECTORY_ADM_UNPUBLISH'), $controller . '.unpublish', false);
			$bar->appendButton('standard', 'delete', JText::_('COM_JOMDIRECTORY_ADM_DELETE'), $controller . '.delete', false);
		} else {
			$bar->appendButton('Frontend', 'new', JText::_('COM_JOMDIRECTORY_ADM_ADD'), $controller . '.add', false);
			$bar->appendButton('Frontend', 'publish', JText::_('COM_JOMDIRECTORY_ADM_PUBLISH'), $controller . '.publish', false);
			$bar->appendButton('Frontend', 'unpublish', JText::_('COM_JOMDIRECTORY_ADM_UNPUBLISH'), $controller . '.unpublish', false);
			$bar->appendButton('Frontend', 'delete', JText::_('COM_JOMDIRECTORY_ADM_DELETE'), $controller . '.delete', false);
		}


		return $bar->render();
	}

	public function getLoginGroup()
	{
		$component = "com_jomdirectory";
		$user = JFactory::getUser();
		return Main_FrontAdmin::getLoginGroup($user->id, $component);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('#__cddir_service as a');

		$query->select('c.title AS category_title, c.color AS category_color');
		$query->join('LEFT', '#__cddir_categories AS c ON c.id = a.categories_id');

		$query->select('u.name AS username');
		$query->join('LEFT', '#__users AS u ON u.id = a.users_id');

		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int)$published);
		}

		// Filter by category.
		$categoryId = $this->getState('filter.categories_id');
		if (is_numeric($categoryId)) {
			$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int)$cat_tbl->level;
			$query->where('c.lft >= ' . (int)$lft);
			$query->where('c.rgt <= ' . (int)$rgt);
//			$query->where('a.categories_id = '.(int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int)substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.title LIKE ' . $search . ')');
			}
		}

		$query->where('a.users_id =' . (int)$this->user->id);

		$sort = $this->getState('list.sort');
		switch ($sort) {
			case 'alfa':
				$query->order($db->escape('a.title, a.date_publish DESC'));
				break;
			case 'updated':
				$query->order($db->escape('a.date_modified DESC'));
				break;

			case 'latest':
			default:
				$query->order($db->escape('a.date_publish DESC'));
		}

		$query->group($db->escape('a.id'));


		return $query;

	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return    void
	 * @since    1.6
	 */
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

		$limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'jdItemsPerPage', $params->get('listing_per_page'), 'uint');
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.list.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);

		$this->setState('list.start', $limitstart);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.categories_id', 'filter_category', '', 'string');
		$this->setState('filter.categories_id', $categoryId);


		$sort = $this->getUserStateFromRequest($this->context . '.list.sort', 'jdItemsSort', 'latest', 'string');
		$this->setState('list.sort', $sort);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = false)
	{
		$app = JFactory::getApplication();
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = JRequest::getVar($request, $old_state, 'default', $type);

		if (($cur_state != $new_state) && ($resetPage)) {
			JRequest::setVar('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null) {
			$app->setUserState($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

}