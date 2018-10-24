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
jimport('joomla.user.helper');

/**
 * Jomdirectory Component Category Model
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryModelAdmin_membership_buy extends JModelList
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';
	protected $user;
	protected $plan;
	protected $newplan;

	function __construct()
	{
		parent::__construct();
		if ($this->user = JFactory::getUser()) $this->plan = $this->getPlan();
		$this->newplan = $this->getNew_plan();
	}

	public function getPlan($notOld = 0)
	{
		$old = 0;
		if (Main_FrontAdmin::getPlanOld($this->user->id, 'com_jomdirectory') && !$notOld) $old = 1;
		$groups = $this->user->get('groups');
		$query = "SELECT group_id FROM #__cddir_plans WHERE extension='com_jomdirectory' ORDER BY price_annually desc, price_monthly desc";
		$data = $this->_getList($query);
		foreach ($data as $row) {
			foreach ($groups as $plan) {
				if ($row->group_id == $plan && !$old) return $plan;
			}
		}
		return $row->group_id;
	}

	public function getNew_plan()
	{

		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select', 'a.id, a.name, a.price_monthly, a.price_annually, a.best_value, a.listings_nr, a.images_nr, a.attachments, a.premium_nr, a.video, a.group_id '));

		$query->from($db->quoteName('#__cddir_plans') . ' AS a');
		$query->where("a.id='" . $this->getState("plan_id") . "'");
		$query->where("extension='com_jomdirectory'");

		$query->group($db->escape('a.id'));
		$data = $this->_getList($query);
		foreach ($data as $row) return $row;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getSuccessArticle()
	{
		$d = array();
		$d['title'] = '';
		$d['text'] = '';
		$params = JComponentHelper::getParams('com_jomdirectory');
		$articleId = $params->get('admin_paypal_articleid');
		$db = $this->getDbo();
		$query = "SELECT * FROM #__content WHERE id = " . intval($articleId[0]);
		$db->setQuery($query);
		if ($data = $db->loadObject()) {
			if (!$data->title) $d['title'] = JText::_('COM_JOMDIRECTORY_ADM_WELCOME'); else $d['title'] = $data->title;
			if (!$data->fulltext) $d['text'] = $data->introtext; else $d['text'] = $data->fulltext;
		}
		return $d;
	}

	public function getPaymentResponse()
	{
		$in = JFactory::getApplication()->input;
		$this->_session = JFactory::getSession();
		$this->_sessionData = $this->_session->get('comdev_membership');
		$payKey = $in->getString('paymentId');
		$p = new Main_Payment();
		$p->setMethod($this->_sessionData->payment);
		$payment = $p->check($payKey);
		if ($payment->state == 'approved') {
			$this->changePlan($this->newplan->group_id, $this->_sessionData->annualy, $this->_sessionData->priceAll, 'membership-' . $payKey);
			//print_r ($payment);
		} else {
			//print_r ($payment);
			JError::raiseWarning(100, "PAYMENT ERROR");
		}
	}

	public function changePlan($id, $len, $price, $orders_id)
	{
		JUserHelper::removeUserFromGroup($this->user->id, $this->getPlan(1));
		JUserHelper::addUserToGroup($this->user->id, $id);
		if ($len) $date = date("Y-m-d", strtotime("+1 year")); else $date = date("Y-m-d", strtotime("+1 month"));
		Main_FrontAdmin::changePlanExpiry($this->user->id, 'com_jomdirectory', $date);
		$paramsADD['plan_name'] = Main_FrontAdmin::getGroupName($id);
		$paramsADD['plan_expiry'] = $date;
		$paramsADD['old_group_id'] = $this->getPlan(1);
		$paramsADD['new_group_id'] = $id;
		Main_FrontAdmin::saveMembershipPayment($this->user->id, $price, $orders_id, $id, 'com_jomdirectory', 'complete', 'PayPal', 'membership', $paramsADD);
	}

	public function getSendPayment()
	{
		$params = JComponentHelper::getParams('com_jomdirectory');
		$returnUrl = trim(JUri::base(), '/') . '/index.php?option=com_jomdirectory&view=admin_membership_buy&layout=success';
		$cancelUrl = trim(JUri::base(), '/') . '/index.php?option=com_jomdirectory&view=admin_membership';
		$paramsPay = new stdClass();
		$paramsPay->returnUrl = $returnUrl;
		$paramsPay->cancelUrl = $cancelUrl;
		$paramsPay->currency = $params->get('adm_currency');

		$in = JFactory::getApplication()->input;

		$this->_sessionData = new stdClass();
		$this->_session = JFactory::getSession();
		$this->_sessionData->plan_id = $in->get('plan_id');
		$this->_sessionData->annualy = $in->get('annualy');
		$this->_sessionData->payment = $in->get('payment');
		if ($in->get('annualy')) $this->_sessionData->priceAll = $this->newplan->price_annually; else $this->_sessionData->priceAll = $this->newplan->price_monthly;
		$this->_session->set('comdev_membership', $this->_sessionData);

		if ($this->_sessionData->payment == 'bank') {
			if ($in->get('annualy')) $date = date("Y-m-d", strtotime("+1 year")); else $date = date("Y-m-d", strtotime("+1 month"));
			$paramsADD['plan_name'] = Main_FrontAdmin::getGroupName($this->newplan->group_id);
			$paramsADD['plan_expiry'] = $date;
			$paramsADD['old_group_id'] = $this->getPlan(1);
			$paramsADD['new_group_id'] = $this->newplan->group_id;
			Main_FrontAdmin::saveMembershipPayment($this->user->id, $this->_sessionData->priceAll, 'membership-' . $this->user->id . time(), $this->newplan->group_id, 'com_jomdirectory', 'pending', "Offline", 'membership', $paramsADD);
		}

		$p = new Main_Payment();
		$p->setMethod($this->_sessionData->payment);
		$info = $p->pay($this->_sessionData->priceAll, $paramsPay);
	}

	public function getLoginGroup()
	{
		$component = "com_jomdirectory";
		$user = JFactory::getUser();
		return Main_FrontAdmin::getLoginGroup($user->id, $component);
	}

	public function priceChange($data, $priceParams, $clean = 0)
	{
		if (is_array($priceParams) && !empty($priceParams)) {
			$d = new stdClass;
			$d->price_netto = $data;

			if (isset($priceParams['tax'])) {
				$vat = $priceParams['tax'] / 100;
				$d->price = $d->price_netto + ($d->price_netto * $vat);
			}
			if ($clean) return number_format($d->price, 2, '.', '');
			$d->price = number_format($d->price, $priceParams['decimal_digits'], $priceParams['currency_rest_separator'], $priceParams['number_format']);

			if (isset($priceParams['currency'])) {
				switch ($priceParams['currency_position']) {
					case '1':
						$d->price = $priceParams['currency'] . ' ' . $d->price;
						break;
					case '2':
					default:
						$d->price = $d->price . ' ' . $priceParams['currency'];
						break;
				}
			}
		}
		return $d->price;
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

		$plan_id = $this->getUserStateFromRequest($this->context . '.plan_id', 'plan_id', '', 'uint');
		$this->setState('plan_id', $plan_id);
	}

	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app = JFactory::getApplication();
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = JRequest::getVar($request, $old_state, 'default', $type);

		// Save the new value only if it is set in this request.
		if ($new_state !== null) {
			$app->setUserState($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}
}