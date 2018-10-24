<?php

/* ------------------------------------------------------------------------
  # com_jomdirectory - JomDirectory
  # ------------------------------------------------------------------------
  # author    Comdev
  # copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://comdev.eu
  ------------------------------------------------------------------------ */
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
class JomdirectoryModelAdmin_dashboard extends JModelList
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

	public function getApprovedCount()
	{
		return $this->getListingsCount('0');
	}

	public function getListingsCount($approved = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($this->getState('list.select', 'a.id AS id'));
		$query->from($db->quoteName('#__cddir_content') . ' AS a');
		if ($approved != null) $query->where('a.approved = ' . (int)$approved);
		$query->where('a.users_id =' . $this->user->id);
		return $this->_getListCount($query);
	}

	public function getReviewsCount()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select', 'a.id AS id'));
		$query->from($db->quoteName('#__cddir_reviews') . ' AS a');
		$query->join('LEFT', '#__cddir_content AS c ON c.id = a.content_id');
		$query->where('a.extension = "com_jomdirectory"');
		$query->where('c.users_id =' . $this->user->id);
		return $this->_getListCount($query);
	}

	function getChart()
	{
		$d = '[["Name","Hits"],';
		$query = "SELECT c.title, c.id, c.fulladdress, c.alias, c.categories_id, c.categories_address_id, SUM( s.view_item ) AS hits FROM #__cddir_content as c LEFT JOIN #__cddir_statistic as s ON c.id=s.item_id WHERE c.users_id =" . $this->user->id . " group by c.id order by hits desc limit 10";
		$data = $this->_getList($query);
		if (!empty($data) && is_array($data)) {
			foreach ($data as $row):
				$title = htmlspecialchars($row->title);
				$d .= '["' . $title . '",' . $row->hits . '],';
			endforeach;
		} else $d .= '["",0]';
		$d .= "]";
		return $d;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getWelcomeArticle()
	{
		$d = array();
		$d['title'] = '';
		$d['text'] = '';
		$params = JComponentHelper::getParams('com_jomdirectory');
		$articleId = $params->get('admin_welcome_article');
		$db = $this->getDbo();
		$query = "SELECT * FROM #__content WHERE id = " . intval($articleId[0]);
		$db->setQuery($query);
		if ($data = $db->loadObject()) {
			if (!$data->title) $d['title'] = JText::_('COM_JOMDIRECTORY_ADM_WELCOME'); else
				$d['title'] = $data->title;
			if (!$data->fulltext) $d['text'] = $data->introtext; else
				$d['text'] = $data->fulltext;
		}
		return $d;
	}

	public function getToolbar()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jomdirectory/assets/css/jomdirectory_admin.css');

		$controller = 'Admin_dashboard';
		jimport('joomla.html.toolbar');
		$bar = new JToolBar('toolbar');
		$bar->appendButton('standard', 'publish', JText::_('COM_JOMDIRECTORY_ADM_APPROVE'), $controller . '.publish', false);
		$bar->appendButton('standard', 'delete', JText::_('COM_JOMDIRECTORY_ADM_DELETE'), $controller . '.delete', false);

		return $bar->render();
	}

	public function getPlanName()
	{
		$old = Main_FrontAdmin::getPlanOld($this->user->id, 'com_jomdirectory');
		$groups = $this->user->get('groups');
		$query = "SELECT group_id,name FROM #__cddir_plans WHERE extension='com_jomdirectory' ORDER BY price_annually desc, price_monthly desc, id desc";
		$data = $this->_getList($query);
		if (!empty($data) && is_array($data)) foreach ($data as $row) {
			foreach ($groups as $plan) {
				if ($row->group_id == $plan && !$old) return $row->name;
			}
		}
		if (!isset($row->name)) return "basic"; else
			return $row->name;
	}

	public function getPlanLimits()
	{
		$old = Main_FrontAdmin::getPlanOld($this->user->id, 'com_jomdirectory');
		$groups = $this->user->get('groups');
		$query = "SELECT group_id,listings_nr,premium_nr,video,name FROM #__cddir_plans WHERE extension='com_jomdirectory' and language in ('" . JFactory::getLanguage()->getTag() . "','*') ORDER BY price_annually desc, price_monthly desc,id desc";
		$data = $this->_getList($query);
		$best = 1;
		if (!empty($data) && is_array($data)) foreach ($data as $row) {
			foreach ($groups as $plan) {
				if ($row->listings_nr >= 99999) $row->listings_nr = JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
				if ($row->premium_nr >= 99999) $row->premium_nr = JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
				if ($row->group_id == $plan && !$old) {
					$row->best = $best;
					return $row;
				}
			}
			$best = 0;
		}
		if (!isset($row->listings_nr)) {
			$row = new stdClass;
			$row->listings_nr = JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
			$row->premium_nr = JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_UNLIMITED');
			$row->best = $best;
			return $row;
		}
		$row->best = $best;
		return $row;
	}

	public function getPlanUsage()
	{
		$resp['listings'] = 0;
		$resp['featured'] = 0;
		$resp['expiry'] = '';
		$query = "SELECT featured, id FROM #__cddir_content WHERE users_id={$this->user->id}";
		$res = $this->_getList($query);
		foreach ($res as $row) {
			$resp['listings']++;
			if ($row->featured) $resp['featured']++;
		}
		$resp['products'] = 0;
		$resp['featured_products'] = 0;
		$query = "SELECT featured, id FROM #__cddir_products WHERE users_id={$this->user->id}";
		$res = $this->_getList($query);
		foreach ($res as $row) {
			$resp['products']++;
			if ($row->featured) $resp['featured_products']++;
		}

		if ($data = Main_FrontAdmin::getPlanExpiry($this->user->id, 'com_jomdirectory', 0)) $resp['expiry'] = $data; else $resp['expiry'] = JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_NEVER');

		return $resp;
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

		$query->select($this->getState('list.select', 'a.id AS id, a.title AS title, a.date_modified, a.username, a.published, a.text, a.approved, a.content_id, c.categories_id, c.categories_address_id, c.alias, u.name'));

		$query->from($db->quoteName('#__cddir_reviews') . ' AS a');

		// Join over the categories.
		$query->join('LEFT', '#__cddir_content AS c ON c.id = a.content_id');
		$query->join('LEFT', '#__users AS u ON u.id = a.user_id');
		$query->where('a.approved = 0');
		$query->where('a.extension = "com_jomdirectory"');
		$query->where('c.users_id =' . (int)$this->user->id);

		$query->order($db->escape('a.id DESC'));
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

		$limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'jdItemsPerPage', $params->get('listing_per_page'), 'uint');
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.list.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);

		$this->setState('list.start', $limitstart);
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
