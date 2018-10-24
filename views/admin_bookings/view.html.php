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
class JomdirectoryViewAdmin_bookings extends JViewLegacy
{
	protected $listings;
	protected $toolbar;
	protected $state;
	protected $pagination;
	protected $content_id;
	protected $user;

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

		$this->listings = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->toolbar = $this->get('Toolbar');

		$this->priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), 'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));

		$document = JFactory::getDocument();

		parent::display($tpl);
	}

}
