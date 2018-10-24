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

//jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.modeladmin');

/**
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryModelItem extends JModelAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';


	/**
	 * Method to get the record form.
	 *
	 * @param   array $data Data for the form. [optional]
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_jomdirectory.item.reviews.data', 'addreviews', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string $name The name of the form.
	 * @param   string $source The form source. Can be XML string if file flag is set to false.
	 * @param   array $options Optional array of options for the form creation.
	 * @param   boolean $clear Optional argument to force load a new form.
	 * @param   string $xpath An optional xpath to search for the fields.
	 *
	 * @return  mixed  JForm object on success, False on error.
	 *
	 * @see     JForm
	 * @since   11.1
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear) {
			return $this->_forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

		try {
			$form = JForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data']) {
				// Get the data for the form.
				$data = $this->loadFormDataJD($name);
			} else {
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	public function loadFormDataJD($name)
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($name, array());

		if (empty($data)) {
			$data = new stdClass();
			$data->content_id = JRequest::getInt('id', false);
		}
		return $data;
	}

	public function getFormTellAFriend($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_jomdirectory.item.tellafriend.data', 'addtellafriend', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	public function getFormContact($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_jomdirectory.item.contact.data', 'addcontact', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get article data.
	 *
	 * @param    integer    The id of the article.
	 *
	 * @return    mixed    Menu item data object on success, false on failure.
	 */
	public function getArticle($pk = null)
	{
		$id = JRequest::getInt('id', $pk);

		if (!$id) return false;

		$select = $this->_db->getQuery(true);
		$select->select('this.*');
		$select->from('#__cddir_connections AS a');
		$select->join('INNER', '#__content AS this ON a.com_content_article_id = this.id');
		$select->where('a.directory_id = ' . $this->_db->quote($id));
		$select->where("a.extension = 'com_jomdirectory'");
		$select->order('a.ordering ASC');

		$this->_db->setQuery($select);
		$items = $this->_db->loadObjectList();

		return $items;
	}

	public function getService($id = null)
	{

		if (!$id) return false;

		$select = $this->_db->getQuery(true);
		$select->select('a.*');
		$select->from('#__cddir_service AS a');
		$select->where('a.items_id = ' . $this->_db->quote($id));
		$select->order('a.title ASC');

		$this->_db->setQuery($select);
		$items = $this->_db->loadObjectList();

		return $items;
	}

	public function getItemFromUserid($id)
	{

		if (!$id) return false;

		$select = $this->_db->getQuery(true);
		$select->select('a.id');
		$select->from('#__cddir_content AS a');
		$select->where('a.users_id = ' . $this->_db->quote($id));

		$this->_db->setQuery($select);
		$items = $this->_db->loadObject();

		return $items;
	}

	public function getReviews($item = null)
	{
		$how = 0;
		$data = new stdClass();
		$data->rates = array();
		$data->recommended = array();
		$data->count = 0;
		$data->rate = 0;

//		$id = JRequest::getInt('id', false);
		$params = JComponentHelper::getParams('com_jomdirectory');
		$listing = $item;
		$id = $listing->id;
		if (!$id) return $data;

		$select = $this->_db->getQuery(true);
		$select->select('a.*, IF(a.user_id > 0, b.name, a.username) AS username ');
		$select->from('#__cddir_reviews AS a');
		$select->join('left', '#__users AS b ON b.id=a.user_id');
		$select->where('a.content_id = ' . $this->_db->quote($id));
		$select->where('a.extension = "com_jomdirectory"');
		$select->where('a.published = 1');
		$select->where('a.approved = 1');
		$select->order('a.id DESC');

		$this->_db->setQuery($select);
		$item = $this->_db->loadObjectList();


		if (!$item) return $data;


		$rated_elements = $this->getElements($listing->language, 'ratings');
		$recommended_elements = $this->getElements($listing->language, 'recommendations');
		foreach ($item AS $i) {
			$i->voted = JomcomdevHelperRemember::checkReviewsLike($i->id);
			$how = $how + $i->rate;

			$i->rates = array();
			$i->recommended = array();
			foreach ($recommended_elements as $el) if ($this->getOldValue($i->id, $el->id, 'recommendation')) {
				$i->recommended[] = $el->name;
				if (isset($data->recommended[$el->name])) $data->recommended[$el->name]++; else $data->recommended[$el->name] = 1;
			}
			foreach ($rated_elements as $el) {
				$value = $this->getOldValue($i->id, $el->id, 'rate');
				$i->rates[$el->name] = $value;
				if (isset($data->rates[$el->name])) $data->rates[$el->name] += $value; else $data->rates[$el->name] = $value;
			}
		}

		foreach ($rated_elements as $el) $data->rates[$el->name] = $data->rates[$el->name] / count($item);

		$data->items = $item;
		$data->count = count($item);
		$data->rate = $how / $data->count;
		return $data;
	}

	/**
	 * Method to get article data.
	 *
	 * @param    integer    The id of the article.
	 *
	 * @return    mixed    Menu item data object on success, false on failure.
	 */

	protected function getElements($lang, $element)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id AS id, a.title AS name, a.language , a.published');
		$query->from($db->quoteName('#__cddir_categories') . ' AS a');
		$query->where('a.extension ="com_jomdirectory.' . $element . '"');
		$query->where('a.published =1');
		if ($lang && $lang != "*") $query->where('(a.language = "*" OR a.language="' . $lang . '")');
		$query->order($db->escape('a.lft'));
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}

	protected function getOldValue($review_id, $type_id, $column)
	{
		if ($review_id) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($column);
			$query->from($db->quoteName('#__cddir_rates'));
			$query->where('type_id =' . (int)$type_id);
			$query->where('review_id =' . (int)$review_id);
			$query->where('extension ="com_jomdirectory"');
			$db->setQuery($query);
			$result = $db->loadResult();
			return $result;
		}
	}

	public function getCalendar($pk = null)
	{
		$id = JRequest::getInt('id', $pk);

		$extension = 'com_jomdirectory';
		$params = JComponentHelper::getParams($extension);
		if ($params->get('enable_calendar')) {
			$select = $this->_db->getQuery(true);
			$select->select('id ');
			$select->from('#__cddir_calendar ');
			$select->where('content_id = ' . $this->_db->quote($id));
			$select->where('extension = "' . $extension . '"');
			$this->_db->setQuery($select);
			$item = $this->_db->loadObjectList();
			if ($item) return true; else return false;
		} else return false;
		$listing = $this->getItem();
		if (!$id) return false;
	}

	/**
	 * Method to get article data.
	 *
	 * @param    integer    The id of the article.
	 *
	 * @return    mixed    Menu item data object on success, false on failure.
	 */
	public function getItem($idOn = false)
	{
		$searchType = 'alias';
		if ($idOn) {
			$id = $idOn;
			if (is_int($id)) {
				$searchType = 'id';
			}
		} else {
			$id = JRequest::getString('alias', $idOn);
		}

		if (!$id) return false;

		$select = $this->_db->getQuery(true);
		$select->select('a.*, b.title AS categoryTitle, c.email AS userEmail');
		$select->from('#__cddir_content AS a');
		$select->join('INNER', '#__cddir_categories AS b ON a.categories_id = b.id');
		$select->join('LEFT', '#__users AS c ON a.users_id = c.id');
		$select->where('a.' . $searchType . ' = ' . $this->_db->quote($id));
		$select->where('a.published = 1');

		$user = JFactory::getUser();
		$aclgroups = implode(',', $user->getAuthorisedViewLevels());
		$select->where('a.access IN (' . $aclgroups . ')')->where('b.access IN (' . $aclgroups . ')');

		$this->_db->setQuery($select);
		$item = $this->_db->loadObject();

		return $item;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('item.id', $pk);

		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);

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
