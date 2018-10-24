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

jimport('joomla.application.component.view');

/**
 * View to list an items.
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryViewAdmin_messages extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $toolbar;
	protected $categories;
	protected $state;
	protected $pagination;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->user = $this->get('User');
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');

		if (!$this->user->name || !$this->get('LoginGroup')) {
			JError::raiseWarning(100, JText::_('JERROR_ALERTNOAUTHOR'));
			$app = JFactory::getApplication();
			if ($this->params->get('admin_form_login')) $app->redirect(JRoute::_('index.php?Itemid=' . $this->params->get('admin_form_login'))); else $app->redirect(JRoute::_(JURI::base()));
			return false;
		}


		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->categories = $this->get('Categories');
		$this->pagination = $this->get('Pagination');

		$this->toolbar = $this->get('Toolbar');

		parent::display($tpl);
	}
}
