<?php/*------------------------------------------------------------------------# com_jomdirectory - JomDirectory# ------------------------------------------------------------------------# author    Comdev# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL# Websites: http://comdev.eu------------------------------------------------------------------------*/// no direct accessdefined('_JEXEC') or die('Restricted access');jimport('joomla.application.component.view');JLoader::register('JomdirectoryHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/jomdirectory.php');/** * Jomdirectory controller for item edit * * @package    Joomla.Administrator * @subpackage    com_jomdirectory * @copyright    Copyright (C) 2012 Comdev. All rights reserved. */class JomdirectoryViewAdmin_addproduct extends JViewLegacy{	protected $form;	protected $item;	protected $state;	protected $toolbar;	protected $user;	public function display($tpl = null)	{		// Initialiase variables.		$this->user = $this->get('User');		$this->state = $this->get('State');		$this->params = $this->state->get('params');		$this->form = $this->get('Form');		$this->item = $this->get('Item');		if (!$this->user->name || !$this->get('LoginGroup') || ($this->user->id != $this->item->users_id && $this->item->id)) {			JError::raiseWarning(100, JText::_('JERROR_ALERTNOAUTHOR'));			$app = JFactory::getApplication();			if ($this->params->get('admin_form_login')) $app->redirect(JRoute::_('index.php?Itemid=' . $this->params->get('admin_form_login'))); else $app->redirect(JRoute::_(JURI::base()));			return false;		}		$rootCategory = $this->params->get('root_category_product');		if (!empty($rootCategory)) $this->form->setFieldAttribute('categories_id', 'root', $rootCategory);		$this->toolbar = $this->get('Toolbar');		$this->limits = $this->get('PlanLimits');		$this->limits->paid_fields = json_decode($this->limits->paid_fields, true);		$this->pre_limit = $this->get('Limit');		$datap = Main_FrontAdmin::getPlanExpiry($this->user->id, 'com_jomdirectory', 0);		if (isset ($datap) && strpos($datap, '#blocked') !== false) {			$this->blocked = 1;		} else $this->blocked = 0;		parent::display($tpl);	}}