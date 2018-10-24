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
class JomdirectoryViewMembership extends JViewLegacy
{
	protected $state;
	protected $plans;
	protected $plan;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');
		$this->fields = $this->get('Fields');

		if (JFactory::getUser()->id) JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_jomdirectory&view=admin_membership'));


		$this->plans = $this->get('Items');

		$this->priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), 'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));

		$document = JFactory::getDocument();
		parent::display($tpl);
	}
}
