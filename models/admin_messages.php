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
class JomdirectoryModelAdmin_messages extends JModelList
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
		$query = "SELECT title, id, level FROM #__cddir_categories WHERE extension='com_jomdirectory.jomdirectory' ORDER BY lft";
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

	public function getContentID()
	{
		return $this->content_id;
	}

	public function getToolbar()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jomdirectory/assets/css/jomdirectory_admin.css');

		$controller = 'admin_messages';
		jimport('joomla.html.toolbar');
		$bar = new JToolBar('toolbar');

		if (Joomla_Version::if3()) {
			$bar->appendButton('standard', 'delete', JText::_('COM_JOMDIRECTORY_ADM_DELETE'), $controller . '.delete', false);
		} else {
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

	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from($db->quoteName('#__cddir_messages') . ' AS a');

		$query->select('c.title AS title');
		$query->join('LEFT', '#__cddir_content AS c ON c.id = a.content_id');

		$query->select('d.title AS category_title');
		$query->join('LEFT', '#__cddir_categories AS d ON d.id = c.categories_id');

		$query->select('u.name AS username');
		$query->join('LEFT', '#__users AS u ON u.id = c.users_id');

		// Filter by category.
		$categoryId = $this->getState('filter.categories_id');
		if (is_numeric($categoryId)) {
			$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int)$cat_tbl->level;
			$query->where('d.lft >= ' . (int)$lft);
			$query->where('d.rgt <= ' . (int)$rgt);
//			$query->where('a.categories_id = '.(int) $categoryId);
		}

		$authorId = $this->user->id;
		if (is_numeric($authorId)) {
			$type = $this->getState('filter.users_id.include', true) ? '= ' : '<>';
			$query->where('c.users_id ' . $type . (int)$authorId);
		}

		$query->where('a.extension = "com_jomdirectory"');
		// Filter by search in message
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int)substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.message LIKE ' . $search . ' OR a.email_to LIKE ' . $search . ' OR a.email_from LIKE ' . $search . ')');
			}
		}
		$orderCol = $this->getState('list.sort');
		if ($orderCol) $query->order($db->escape($orderCol)); else $query->order($db->escape('a.date DESC'));
		return $query;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('site');

		// Load the parameters.
//            $params = $app->getParams();
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

		$sort = $this->getUserStateFromRequest($this->context . '.list.sort', 'jdItemsSort', 'a.date DESC', 'string');
		$this->setState('list.sort', $sort);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.categories_id', 'filter_category', '', 'string');
		$this->setState('filter.categories_id', $categoryId);

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
