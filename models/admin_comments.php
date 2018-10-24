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
class JomdirectoryModelAdmin_comments extends JModelList
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

		$controller = 'admin_comments';
		jimport('joomla.html.toolbar');
		$bar = new JToolBar('toolbar');

		if (Joomla_Version::if3()) {
			$bar->appendButton('standard', 'publish', JText::_('COM_JOMDIRECTORY_ADM_APPROVE'), $controller . '.publish', false);
			$bar->appendButton('standard', 'unpublish', JText::_('COM_JOMDIRECTORY_ADM_UNAPPROVE'), $controller . '.unpublish', false);
			$bar->appendButton('standard', 'delete', JText::_('COM_JOMDIRECTORY_ADM_DELETE'), $controller . '.delete', false);
		} else {
			$bar->appendButton('Frontend', 'publish', JText::_('COM_JOMDIRECTORY_ADM_APPROVE'), $controller . '.publish', false);
			$bar->appendButton('Frontend', 'unpublish', JText::_('COM_JOMDIRECTORY_ADM_UNAPPROVE'), $controller . '.unpublish', false);
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

		$query->select($this->getState('list.select', 'a.id AS id, a.title AS title, a.date_modified, a.username, a.published, a.text, a.approved, a.content_id, c.categories_id, c.alias, u.name, c.categories_address_id '));

		$query->from($db->quoteName('#__cddir_reviews') . ' AS a');
		$query->join('LEFT', '#__users AS u ON u.id = a.user_id');

		$query->where('a.extension = "com_jomdirectory"');

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int)substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.title LIKE ' . $search . ' OR a.text LIKE ' . $search . ')');
			}
		}

		// Join over the categories.
		$query->join('LEFT', '#__cddir_content AS c ON c.id = a.content_id');


		// Filter by published published
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int)$published);
		}

		$query->where('a.content_id =' . $this->getState('content_id'));
		$query->where('c.users_id =' . (int)$this->user->id);


		$sort = $this->getState('list.sort');
		switch ($sort) {
			case 'most_viewed':
				$query->order($db->escape('a.hits DESC, a.id DESC'));
				break;
			case 'alfa':
				$query->order($db->escape('a.title, a.id DESC'));
				break;
			case 'updated':
				$query->order($db->escape('a.date_modified DESC'));
				break;
			case 'rated_desc':
				$query->order($db->escape('rateSum DESC, a.id DESC'));
				break;
			case 'rated_asc':
				$query->order($db->escape('rateSum ASC, a.id DESC'));
				break;

			case 'latest':
			default:
				$query->order($db->escape('a.id DESC'));
		}

		$query->group($db->escape('a.id'));
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

		$published = $this->getUserStateFromRequest($this->context . '.filter_published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$sort = $this->getUserStateFromRequest($this->context . '.list.sort', 'jdItemsSort', 'latest', 'string');
		$this->setState('list.sort', $sort);

		$content_id = $this->getUserStateFromRequest($this->context . '.content_id', 'content_id', '', 'uint');
		$this->setState('content_id', $content_id);
	}

	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
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