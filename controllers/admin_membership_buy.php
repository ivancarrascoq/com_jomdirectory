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

jimport('joomla.application.component.controller');

/**
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryControllerAdmin_membership_buy extends JControllerLegacy
{
	public function change_plan()
	{
		$data = JRequest::get('post');
		$model = $this->getModel('admin_membership_buy');
		$model->changePlan((int)$data['plan_id'], (int)$data['len']);
		echo "<script>location.href=location.href</script>";
	}

	public function start_payment()
	{
		$amount = JRequest::getVar('amount');
		$name = JRequest::getVar('name');
		Main_FrontAdmin::saveMembershipPayment(JFactory::getUser()->id, $amount, $name, '', 'com_jomdirectory', 'cancelled', 'PayPal', 'membership');
		die("1");
	}
}
