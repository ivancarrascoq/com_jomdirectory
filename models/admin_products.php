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
class JomdirectoryModelAdmin_products extends JModelList
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

	public function getToolbar()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jomdirectory/assets/css/jomdirectory_admin.css');

		$controller = 'admin_products';
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

	protected function getListQuery()
	{

		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select', 'a.id AS id, a.company_id, a.title AS title, a.alias AS alias, a.featured, a.approved, a.price,  ' . 'a.published AS published, a.categories_id AS categories_id, ' . 'a.sku, a.tax, a.quantity, ' . 'a.date_publish AS date_publish, a.date_publish_down AS date_publish_down')
//				'a.checked_out AS checked_out,'.
//				'a.checked_out_time AS checked_out_time, a.catid AS catid,' .
//				'a.clicks AS clicks, a.metakey AS metakey, a.sticky AS sticky,'.
//				'a.impmade AS impmade, a.imptotal AS imptotal,' .
//				'a.state AS state, a.ordering AS ordering,'.
//				'a.purchase_type as purchase_type,'.
//				'a.language, a.publish_up, a.publish_down'
		);
		$query->from($db->quoteName('#__cddir_products') . ' AS a');

		// Join over the language
//		$query->select('l.title AS language_title');
//		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
//		$query->select('uc.name AS editor');
//		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the categories.
		$query->select('c.title AS category_title, c.color AS category_color');
		$query->join('LEFT', '#__cddir_categories AS c ON c.id = a.categories_id');

		// Join over the types.
//		$query->select('e.title AS type_title');
//		$query->join('LEFT', '#__avt_connections AS d ON d.content_id = a.id AND d.categories_type_id = 462');
//		$query->join('LEFT', '#__categories AS e ON e.id = d.categories_id');

		$query->select('u.name AS username');
		$query->join('LEFT', '#__users AS u ON u.id = a.users_id');

		$query->select('fff.title as companyTitle');
		$query->join('LEFT', '#__cddir_content AS fff ON fff.id = a.company_id');

		$query->select('count(d.id) AS reviews');
		$query->join('LEFT', '#__cddir_reviews AS d ON (d.content_id = a.id AND d.extension = "com_jomdirectory")');


		// Join over the clients.
//		$query->select('cl.name AS client_name,cl.purchase_type as client_purchase_type');
//		$query->join('LEFT', '#__banner_clients AS cl ON cl.id = a.cid');

		// Filter by published published
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

		$typeId = $this->getState('filter.type_id');
		if (is_numeric($typeId)) {
			$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
			$cat_tbl->load($typeId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int)$cat_tbl->level;
			$query->where('e.lft >= ' . (int)$lft);
			$query->where('e.rgt <= ' . (int)$rgt);
//			$query->where('a.categories_id = '.(int) $categoryId);
		}

		$query->where('a.users_id =' . (int)$this->user->id);

		// Filter by client.
//		$clientId = $this->getState('filter.client_id');
//		if (is_numeric($clientId)) {
//			$query->where('a.cid = '.(int) $clientId);
//		}

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
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('products.order', 'a.id');
		$orderDirn = $this->state->get('products.direction', 'DESC');
//
//		$query->order($db->escape('a.date_publish DESC'));
		$query->group($db->escape('a.id'));

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

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

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.categories_id', 'filter_category', '', 'string');
		$this->setState('filter.categories_id', $categoryId);


		$sort = $this->getUserStateFromRequest($this->context . '.list.sort', 'jdItemsSort', 'latest', 'string');
		$this->setState('list.sort', $sort);

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