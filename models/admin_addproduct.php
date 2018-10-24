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
class JomdirectoryModelAdmin_addproduct extends JModelAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
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
	 * Proxy for getModel.
	 * @since    1.6
	 */
	public function getTable($name = 'Product', $prefix = 'JomdirectoryTable', $config = array('ignore_request' => true))
	{
		$model = parent::getTable($name, $prefix, $config);
		return $model;
	}

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
		$form = $this->loadForm('com_jomdirectory.admin_addproduct', 'admin_addproduct', array('control' => 'jform', 'load_data' => $loadData));
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
		$listings_nr = 0;
		$featured_nr = 0;
		$jdfields = JRequest::getVar('jdfields');

		$query = "SELECT featured, id FROM #__cddir_products WHERE users_id={$this->user->id}";
		$res = $this->_getList($query);
		foreach ($res as $row) {
			if (!$data['id'] || $row->id != $data['id']) {
				$listings_nr++;
				if ($row->featured) $featured_nr++;
			}
		}
		if ($limits = $this->getPlanLimits()) {
			$limits->paid_fields = json_decode($limits->paid_fields, true);
			if ($limits->paid_fields) {
				$count = count($jdfields);
				for ($i = 0; $i < $count; $i++) {
					foreach ($limits->paid_fields as $id => $isset) if (!$isset && isset($jdfields[$i][$id])) unset($jdfields[$i]);
				}
				JRequest::setVar('jdfields', $jdfields);
			}
			if (isset($limits->paid_fields['tags_nr']) && count($data['tags']) > (int)$limits->paid_fields['tags_nr']) {
				JError::raiseWarning(100, JText::_("COM_JOMDIRECTORY_NOT_ENOUGH_TAGS_IN_PLAN"));
				$data['tags'] = array_slice($data['tags'], 0, (int)$limits->paid_fields['tags_nr']);
			}
			if (isset($limits->paid_fields['products_nr']) && $listings_nr >= $limits->paid_fields['products_nr'] && !$data['id']) {
				$this->setError(JText::_("COM_JOMDIRECTORY_NOT_ENOUGH_PRODUCTS_IN_PLAN"));
				return false;
			}
			if ($featured_nr >= $limits->premium_nr && $data['featured']) {
				JError::raiseWarning(100, JText::_("COM_JOMDIRECTORY_NOT_ENOUGH_PREMIUM_IN_PLAN"));
				$data['featured'] = 0;
			}
			if (!$limits->video && $data['youtube_link']) {
				JError::raiseWarning(100, JText::_("COM_JOMDIRECTORY_NOT_YOUTUBE_IN_PLAN"));
				$data['youtube_link'] = '';
			}
			if (!$limits->attachments && $data['jdfile']) {
				JError::raiseWarning(100, JText::_("COM_JOMDIRECTORY_NOT_ATTACHMENTS_IN_PLAN"));
				$data['jdfile'] = '';
				JRequest::setVar('jdfile', null);
			}
		}
//		echo "<br><BR>";
		if (parent::save($data)) {
			$id = (int)$this->getState($this->getName() . '.id');
			$tmpl = JPATH_BASE . DS . 'components' . DS . 'com_jomdirectory' . DS . 'templates' . DS . 'emails';
			$params_config = JComponentHelper::getParams('com_jomdirectory');

			$mailer = JFactory::getMailer();
			$config = JFactory::getConfig();
			$sender = array($config->get('mailfrom'), $config->get('fromname'));
			$mailer->setSender($sender);
			$mailer->addRecipient($config->get('mailfrom'));
			$mailer->isHTML(true);
			$mailer->Encoding = 'base64';
			$data['link'] = JURI::base() . "administrator/index.php?option=com_jomdirectory&view=product&layout=edit&id=" . $id;
			if ($params_config->get('admin_notification_items') && !$params_config->get('admin_approve_items')) {
				$body = $this->_getTmpl($tmpl . DS . 'listing_notification_new_default.php', $data);
				$mailer->setSubject(strip_tags(JText::sprintf('COM_JOMDIRECTORY_EMAIL_LISTING2_HEADER', 'Administrator')));
				$mailer->setBody($body);
				$mailer->Send();


			}
			$item = $this->getItem();
			if ($params_config->get('admin_approve_items') && !$item->approved) {
				$body = $this->_getTmpl($tmpl . DS . 'listing_notification_approve_default.php', $data);
				$mailer->setSubject(strip_tags(JText::sprintf('COM_JOMDIRECTORY_EMAIL_LISTING1_HEADER', 'Administrator')));
				$mailer->setBody($body);
				$mailer->Send();

			}
			return true;
		}
		return false;
	}

	public function getPlanLimits()
	{
		$old = Main_FrontAdmin::getPlanOld($this->user->id, 'com_jomdirectory');
		$groups = $this->user->get('groups');
		$query = "SELECT group_id,listings_nr,premium_nr,video,paid_fields,attachments,images_nr FROM #__cddir_plans WHERE extension='com_jomdirectory' ORDER BY price_annually desc, price_monthly desc, id desc";
		$data = $this->_getList($query);
		foreach ($data as $row) {
			foreach ($groups as $plan) {
				if ($row->group_id == $plan && !$old) return $row;
			}
		}
		return $row;
	}

	protected function _getTmpl($path, $data)
	{
		if (!file_exists($path)) return false;
		ob_start();
		require $path;

		return ob_get_clean();
	}

	public function getToolbar()
	{
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$item = $this->getItem();
		$isNew = ($item->id == 0);

		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_jomdirectory/assets/css/jomdirectory_admin.css');

		$controller = 'admin_addproduct';
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

	public function getLimit()
	{
		$resp = array(false, false);
		$listings_nr = 0;
		$item = $this->getItem();
		$query = "SELECT featured, id FROM #__cddir_products WHERE users_id={$this->user->id}";
		$res = $this->_getList($query);
		foreach ($res as $row) {
			if (!$item->id || $row->id != $item->id) $listings_nr++;
		}
		if ($limits = $this->getPlanLimits()) {
			$limits->paid_fields = json_decode($limits->paid_fields, true);
			if (isset($limits->paid_fields['products_nr']) && $listings_nr >= $limits->paid_fields['products_nr'] && !$item->id) {
				$resp[0] = true;
			}
			$resp[1] = $limits->images_nr;
		}
		return $resp;
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
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jomdirectory.edit.admin_addproduct.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null)
	{

		if ($item = parent::getItem($pk)) {

			$jVerArr = explode('.', JVERSION);
			if ($jVerArr[0] >= '3') {
				// Convert the metadata field to an array.
				$registry = new JRegistry;
				if (isset($item->metadata)) $registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();

				if (!empty($item->id)) {
					$item->tags = new JHelperTags;
					$item->tags->getTagIds($item->id, 'com_jomdirectory.products');
					$item->metadata['tags'] = $item->tags;
				}
			}


			//$item->articletext = trim($item->articletext) != '' ? $item->articletext . "<hr id=\"system-readmore\" />" . $item->articletext : $item->introtext;
		}

		return $item;
	}
}
