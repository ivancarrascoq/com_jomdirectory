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

jimport('joomla.application.component.modeladmin');

/**
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryModelAdmin_service extends JModelAdmin
{
	protected $text_prefix = 'COM_JOMDIRECTORY';
	protected $user;


	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->user = JFactory::getUser();
	}

	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param    type $type The table type to instantiate
	 * @param    string $prefix A prefix for the table class name. Optional.
	 * @param    array $config Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 * @since    1.6
	 */
	public function getTable($name = 'Service', $prefix = 'JomdirectoryTable', $config = array('ignore_request' => true))
	{
		$model = parent::getTable($name, $prefix, $config);
		return $model;
	}

	/**
	 * Method to get the row form.
	 *
	 * @param    array $data Data for the form.
	 * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A JForm object on success, false on failure
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_jomdirectory.admin_service', 'admin_service', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param    array    The form data.
	 *
	 * @return    boolean    True on success.
	 * @since    1.6
	 */
	public function save($data)
	{

		if (parent::save($data)) {

			return true;
		}
		return false;
	}

	public function getToolbar()
	{
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$item = $this->getItem();
		$isNew = ($item->id == 0);

		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jomdirectory/assets/css/jomdirectory_admin.css');

		$controller = 'admin_service';
		jimport('joomla.html.toolbar');
		$bar = new JToolBar('toolbar');

		// Since we don't track these assets at the item level, use the category id.
		//$canDo		= JomdirectoryHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		$canDo = JomdirectoryHelper::getActions($this->state->get('filter.category_id'), 0);
		if (Joomla_Version::if3()) {
			// If not checked out, can save the item.
			if (($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_jomdirectory', 'core.create')) > 0)) {
				$bar->appendButton('standard', 'apply', JText::_('COM_JOMDIRECTORY_ADM_APPLY'), $controller . '.apply', false);
				$bar->appendButton('standard', 'save', JText::_('COM_JOMDIRECTORY_ADM_SAVE'), $controller . '.save', false);
			}

			// If an existing item, can save to a copy.
			if (!$isNew && $canDo->get('core.edit')) {
				$bar->appendButton('standard', 'save', JText::_('COM_JOMDIRECTORY_ADM_SAVE_COPY'), $controller . '.save2copy', false);
			}

			if (empty($this->item->id)) {
				$bar->appendButton('standard', 'cancel', JText::_('COM_JOMDIRECTORY_ADM_CANCEL'), $controller . '.cancel', false);
			}
		} else {
			// If not checked out, can save the item.
			if (($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_jomdirectory', 'core.create')) > 0)) {
				$bar->appendButton('Frontend', 'apply', JText::_('COM_JOMDIRECTORY_ADM_APPLY'), $controller . '.apply', false);
				$bar->appendButton('Frontend', 'save', JText::_('COM_JOMDIRECTORY_ADM_SAVE'), $controller . '.save', false);
			}

			// If an existing item, can save to a copy.
			if (!$isNew && $canDo->get('core.edit')) {
				$bar->appendButton('Frontend', 'save', JText::_('COM_JOMDIRECTORY_ADM_SAVE_COPY'), $controller . '.save2copy', false);
			}

			if (empty($this->item->id)) {
				$bar->appendButton('Frontend', 'cancel', JText::_('COM_JOMDIRECTORY_ADM_CANCEL'), $controller . '.cancel', false);
			}
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
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 * @since    1.6
	 */

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jomdirectory.edit.service.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}


		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param    integer $pk The id of the primary key.
	 *
	 * @return    mixed    Object on success, false on failure.
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

		}

		return $item;
	}

}