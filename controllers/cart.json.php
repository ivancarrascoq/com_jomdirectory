<?php
/*------------------------------------------------------------------------
# com_jomcomdev - Comdev Framework
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

use PayPal\Service\AdaptivePaymentsService;
use PayPal\Types\AP\PaymentDetailsRequest;
use PayPal\Types\Common\RequestEnvelope;

//use PayPal\Core\PPHttpConfig;


/**
 * Jomcomdev Component ajax controller
 *
 * @package    Joomla.Site
 * @subpackage    com_jomcomdev
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryControllerCart extends JControllerLegacy
{
	protected $_cart;
	protected $_session;
	protected $_sessionName = 'jd-cart';
	protected $_sessionData;
	protected $_modelPrducts;

	public function __construct($config = array())
	{

		$this->params = JComponentHelper::getParams('com_jomdirectory');
		$this->priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), //                             'currency_type'=>$this->params->get('currency_type',2),
			'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));
		$remember = Main_Remember::getInstance('cart');
		$remember->setAdapter(new Main_Remember_Cookie(true));
		$remember->setTime('6000');

		$this->_cart = new Main_Shop_Cart($remember);
		$this->_modelPrducts = JModelLegacy::getInstance('Products', 'JomdirectoryModel');

		$this->_sessionData = new stdClass();
		$this->_session = JFactory::getSession();

		$params = JComponentHelper::getParams('com_jomdirectory');
		if ($params->get('admin_sandbox')) {
			$this->_configPaypal = array("mode" => "sandbox",

				"acct1.UserName" => $params->get('admin_paypal_username'), "acct1.Password" => $params->get('admin_paypal_password'), "acct1.Signature" => $params->get('admin_paypal_signature'), "acct1.AppId" => "APP-80W284485P519543T"

			);
		} else {
			$this->_configPaypal = array("mode" => "live", "acct1.UserName" => $params->get('admin_paypal_username'), "acct1.Password" => $params->get('admin_paypal_password'), "acct1.Signature" => $params->get('admin_paypal_signature'), "acct1.AppId" => $params->get('admin_paypal_appid')

			);
		}

		parent::__construct($config);
	}

	public function add()
	{
		$id = $this->input->getInt('id');
		$title = $this->input->getString('title');
		$price = $this->input->getString('price');
		$image = $this->input->getString('image');
		$type = $this->input->getString('type');
		$params = $this->input->getString('params', false);

		$product = new Main_Shop_Product_Standard($id, $title, $price, $image, $params, $type);
//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//        print_r($product);
//        echo '</pre>';
//exit;
		$list = $this->_cart->add($product);

		$this->getajax();

	}

	public function getajax()
	{
		$list = (array)$this->_cart->getList();
		$list = array_values($list);
		$price = $this->_cart->getPrice();
		foreach ($list AS $l) {
			$l->priceText = Main_Price::changeSingle($l->price, $this->priceParams);
			$l->priceTextAll = Main_Price::changeSingle($price, $this->priceParams);
		}
		$data = json_encode($list);

		echo $data;

	}

	public function check()
	{
		$this->_sessionData = $this->_session->get($this->_sessionName);

//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//        print_r($this->_sessionData);
//        echo '</pre>';
//exit;

		$payKey = $this->_sessionData->payKey;
		$params = JComponentHelper::getParams('com_jomdirectory');
		$db = JFactory::getDbo();

		$sql = $db->getQuery(true);
		$sql->select('this.*');
		$sql->from('#__cddir_jomdirectory_buy AS this');
		$sql->where('this.payKey=' . $db->quote($payKey));

		$db->setQuery($sql);
		$book = $db->loadObject();
//        $payKey = $book->payKey;

		if (empty($payKey)) return false;

		require_once JPATH_ROOT . '/components/com_jomcomdev/libraries/PayPal/PPAutoloader.php';
		PPAutoloader::register();


		$requestEnvelope = new RequestEnvelope("en_US");
		$paymentDetailsReq = new PaymentDetailsRequest($requestEnvelope);
		$paymentDetailsReq->payKey = $payKey;

		$service = new AdaptivePaymentsService($this->_configPaypal);
		try {
			/* wrap API method calls on the service object with a try catch */
			$response = $service->PaymentDetails($paymentDetailsReq);

			if ($response->status == 'COMPLETED') {
				$query = 'UPDATE #__cddir_jomdirectory_buy SET state="paid" where payKey=' . (int)$payKey;
				$db->setQuery($query);
				$db->query();

				$date = new JDate($book->date_add);
				$payParams = array('link' => '<a href=\"/administrator/index.php?option=com_jomdirectory&task=buy.edit&id=' . $book->id . '\">' . JText::_('COM_JOMDIRECTORY_BUY') . ' #' . $date->format("Ymd") . $book->id . '</a>');
				$price = $this->_sessionData->count->priceAll;
				Main_FrontAdmin::saveMembershipPayment(null, $price, JText::_('COM_JOMDIRECTORY_BUY') . ' ' . $date->format("Ymd") . $book->id, '', 'com_jomdirectory', 'complete', 'PayPal', 'buy', $payParams);
			}

			$this->_sessionData->order = $date->format("Ymd") . $book->id;
			$this->_session->set($this->_sessionName, $this->_sessionData);

			$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('finish')));

		} catch (Exception $ex) {
			echo '<pre>';
			echo "------------- DEBUG AJ --------------\n";
			echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
			print_r($ex);
			echo '</pre>';
//exit;
			exit;
		}

	}

	public function delete()
	{
		$id = (int)$this->input->getInt('id');
		$this->_cart->delete((int)$id);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute()));

	}

	public function deleteajax()
	{

		$id = (int)$this->input->getInt('id');
		$this->_cart->delete((int)$id);

		$this->getajax();

	}

	public function clearCart()
	{
		$this->_cart->delete();
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute()));

	}

	public function subtract()
	{
		$id = (int)$this->input->getInt('id');
		$this->_cart->subtract($id);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute()));

	}

	public function subtractadd()
	{
		$id = (int)$this->input->getInt('id');
		$this->_cart->add($id);
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute()));

	}


	public function loginIn()
	{

		$jinput = JFactory::getApplication()->input;

		$data = $jinput->getArray(array('jform' => array('username' => 'string', 'password' => 'string',)));
		$credentials = array('username' => $data['jform']['username'], 'password' => $data['jform']['password']);


		$login_site = JFactory::getApplication('site');
		$isloged = $login_site->login($credentials, $options = array());
		$user = JFactory::getUser();
		if ($user->id) {
			$data = new stdClass();
			$data->id = $user->id;
			$data->name = $user->name;
			$data->username = $user->username;
			$data->email = $user->email;

			$this->_sessionData->user = (object)$data;
			$this->_session->set($this->_sessionName, $this->_sessionData);
		}

//        $this->redirect('/index.php?option=com_jomdirectory&view=cart&layout=billing');
		if ($isloged) {
			$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('billing')));
		} else {
			throw new Exception('Login error');
		}
//        $this->redirect();
//        exit;
	}

	public function formName()
	{
		$in = JFactory::getApplication()->input;


		$data = $in->getArray();
		$data = $this->_cleanRequest($data);


		$this->_sessionData->user = (object)$data;

		$this->_session->set($this->_sessionName, $this->_sessionData);
//        $this->_cart->summarySave($this->_sessionData);

//         echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//        print_r($this->_sessionData);
//        echo '</pre>';
//exit;
//        $this->setRedirect('/index.php?option=com_jomdirectory&view=cart&layout=billing');
		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('billing')));
//        $this->redirect();
//    exit;

	}

	private function _cleanRequest($data)
	{
		$cleanArray = array('option', 'Itemid', 'task', 'format', 'jd_cart_-', 'lang', 'language');


		foreach ($data AS $key => $d) {
			if (in_array($key, $cleanArray)) {
				unset($data[$key]);
			}
			if (strlen($d) == 32 && strlen($key) == 32) {
				unset($data[$key]);
			}
			if (empty($d)) {
				unset($data[$key]);
			}
		}

		return $data;

	}

	public function formAddress()
	{
		$in = JFactory::getApplication()->input;


		$data = $in->getArray();
		$data = $this->_cleanRequest($data);

		$this->_sessionData = $this->_session->get($this->_sessionName);
		$this->_sessionData->address = (object)$data;

		$this->_session->set($this->_sessionName, $this->_sessionData);
//        $this->_cart->summarySave($this->_sessionData);

		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('payment')));

	}

	public function formShipping()
	{
		$in = JFactory::getApplication()->input;

		$data = $in->getArray();
		$data = $this->_cleanRequest($data);
		if ($data['coupon']) $data['coupon_discount'] = $this->getDiscount($data['coupon']); else $data['coupon_discoun'] = 0;

		$this->_sessionData = $this->_session->get($this->_sessionName);
		$this->_sessionData->shipping = (object)$data;

		$this->_session->set($this->_sessionName, $this->_sessionData);
//        $this->_cart->summarySave($this->_sessionData);


		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('review')));

	}

	public function getDiscount($coupon)
	{
		$db = JFactory::getDbo();
		$sql = $db->getQuery(true);
		$sql->select('this.*');
		$sql->from('#__cddir_coupons AS this');
		$sql->where('this.code=' . $db->quote($coupon) . ' and this.published=1');
		$date = JFactory::getDate();
		$now = $date->toSql();
		$sql->where('(this.date_publish <= ' . $db->Quote($now) . ' OR this.date_publish IS NULL)');
		$sql->where('(this.date_publish_down >= ' . $db->Quote($now) . ' OR this.date_publish_down IS NULL)');
		$db->setQuery($sql);
		if ($c = $db->loadObject()) {
			$price = $this->_cart->getPrice();
			if ($c->type) $drop = $price / 100 * $c->price_drop; else $drop = $c->price_drop;
			if ($drop > $price) $drop = $price;
			return $drop;
		} else return 0;
	}

	public function formPaymentsCheck()
	{


		$in = JFactory::getApplication()->input;
		$this->_sessionData = $this->_session->get($this->_sessionName);
		$payKey = $in->getString('paymentId');
		$params = JComponentHelper::getParams('com_jomdirectory');

		$p = new Main_Payment();
		$p->setMethod($this->_sessionData->shipping->payment);
		$payment = $p->check($payKey);


		$db = JFactory::getDbo();

		$sql = $db->getQuery(true);
		$sql->select('this.*');
		$sql->from('#__cddir_jomdirectory_buy AS this');
		$sql->where('this.id=' . $db->quote($this->_sessionData->orderId));

		$db->setQuery($sql);
		$book = $db->loadObject();


		if ($payment->state == 'approved') {
			$query = 'UPDATE #__cddir_jomdirectory_buy SET state="paid", payKey="' . $payKey . '" where id=' . (int)$book->id;
			$db->setQuery($query);
			$db->query();

			$date = new JDate($book->date_add);
			$payParams = array('link' => '<a href=\"/administrator/index.php?option=com_jomdirectory&task=buy.edit&id=' . $book->id . '\">' . JText::_('COM_JOMDIRECTORY_BUY') . ' #' . $date->format("Ymd") . $book->id . '</a>');
			$price = $this->_sessionData->count->priceAll;
			Main_FrontAdmin::saveMembershipPayment(null, $price, JText::_('COM_JOMDIRECTORY_BUY') . ' ' . $date->format("Ymd") . $book->id, '', 'com_jomdirectory', 'complete', 'PayPal', 'buy', $payParams);
		}

		$this->_sessionData->order = $date->format("Ymd") . $book->id;
		$this->_session->set($this->_sessionName, $this->_sessionData);

		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('finish')));


//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
////        print_r($book);
//        print_r($payment);
//        echo '</pre>';
//exit;
	}

	public function formPayments()
	{
		$in = JFactory::getApplication()->input;
		$this->_sessionData = $this->_session->get($this->_sessionName);
		$paramsJd = JComponentHelper::getParams('com_jomdirectory');


		// FOR Mail
		$dataMail = array();
		$dataMail['product'] = '';
		foreach ($this->_cart->getList() AS $key => $pc) {
			$price = $pc->count * $pc->price;
//            $dataMail['product'] .= '<div>'.$pc->count.' x '.$pc->title.'  -  '.$pc->priceString.'</div>';
			$dataMail['product'] .= '<div>' . $pc->count . ' x ' . $pc->title . '  -  ' . $price . '</div>';
			$dataMail['product'] .= '<br>';

		}
		$dataMail['vat'] = $paramsJd->get('currency_brutto') . ' %';
		$dataMail['total_price'] = $this->_sessionData->count->priceAllString;
		$dataMail['billing_address'] = $this->_sessionData->address->country . ', ' . $this->_sessionData->address->state . ', ' . $this->_sessionData->address->zip . ' ' . $this->_sessionData->address->city . ', ' . $this->_sessionData->address->address . '<br>';
		if (!empty($this->_sessionData->address->shipping_skip) && $this->_sessionData->address->shipping_skip) {
			$dataMail['shipping_address'] = $this->_sessionData->address->country_ship . ', ' . $this->_sessionData->address->state_ship . ', ' . $this->_sessionData->address->zip_ship . ' ' . $this->_sessionData->address->city_ship . ', ' . $this->_sessionData->address->address_ship;
		} else $dataMail['shipping_address'] = '';

		$dataMail['sitename'] = JFactory::getConfig()->get('sitename');

		$db = JFactory::getDbo();
		$list = $this->_cart->getList();
		foreach ($list AS $l) {
			if ($l->type == 'service') {
				$params = json_decode($l->params, JSON_HEX_QUOT);
				$dateStart = new JDate($params['dateStart']);
				$dateEnd = new JDate($params['dateEnd']);
				$sql = "INSERT INTO `#__cddir_service_book` (" . "`id`," . " `state`," . " `service_id`," . " `users_id`," . " `price`," . " `hour_from`," . " `hour_to`," . " `date_published`," . " `date_reservation`," . " `notes`" . ") VALUES (NULL," . " 'WAIT'," //users_id
					. " '" . $params['id'] . "'," //product_id
					. " '" . $this->_sessionData->user->id . "'," // user
					. " '" . $this->_sessionData->count->priceAll . "'," //price_shipping
					. " '" . $dateStart->toSql() . "'," //payment_type
					. " '" . $dateEnd->toSql() . "'," //date_add
					. " CURRENT_TIMESTAMP," //date_modified
					. " '" . $dateStart->toSql() . "'," //payment_type
					. " '" . $l->params . "'" //price
					. ");";
				$db->setQuery($sql);
				$db->execute();
			}
		}


		$sql = "INSERT INTO `#__cddir_jomdirectory_buy` (" . "`id`," . " `users_id`," . " `product_id`," . " `address`," . " `user`," . " `price_shipping`," . " `payment_type`," . " `date_modified`," . " `date_add`," . " `state`," . " `price`," . " `notes`," . " `params`," . " `payKey`" . ") VALUES (NULL," . " '" . $this->_sessionData->user->id . "'," //users_id
			. " '" . json_encode($this->_cart->getList(), JSON_HEX_QUOT) . "'," //product_id
			. " '" . json_encode($this->_sessionData->address, JSON_HEX_QUOT) . "'," //billing_address
			. " '" . json_encode($this->_sessionData->user, JSON_HEX_QUOT) . "'," // user
			. " '" . $this->_sessionData->count->priceShipping . "'," //price_shipping
			. " '" . $this->_sessionData->shipping->payment . "'," //payment_type
			. " CURRENT_TIMESTAMP," //date_modified
			. " '" . JDate::getInstance()->toSql() . "'," //date_add
			. " 'wait'," //state
			. " '" . $this->_sessionData->count->priceAll . "'," //price
			. " ''," //notes
			. " '" . json_encode($this->_sessionData->shipping->name) . "'," //params
			. " ''" //payKey
			. ");";
		$db->setQuery($sql);
		$db->execute();


		$date = new JDate();
		$this->_sessionData->order = $date->format("Ymd") . $db->insertid();
		$this->_sessionData->orderId = $db->insertid();
		$this->_session->set($this->_sessionName, $this->_sessionData);

		$dataMail['order_id'] = $this->_sessionData->order;
		$price = $this->_sessionData->count->priceAll;
		$send = Main_Mail::send($dataMail, 'CART_DONE', $this->_sessionData->user->email, 'com_jomdirectory');
		Main_FrontAdmin::saveMembershipPayment(null, $price, JText::_('COM_JOMDIRECTORY_BUY') . ' ' . $date->format("Ymd"), '', 'com_jomdirectory', 'complete', $this->_sessionData->shipping->payment, 'buy');

		$this->_cart->delete(); // clear cart

//        $returnUrl = trim(JUri::base(), '/').JRoute::_(JomdirectoryHelperRoute::getCartRoute('finish'));
		$returnUrl = trim(JUri::base(), '/') . '/index.php?option=com_jomdirectory&task=cart.formPaymentsCheck&format=json';
		$cancelUrl = trim(JUri::base(), '/') . JRoute::_(JomdirectoryHelperRoute::getCartRoute('default'));
		$paramsPay = new stdClass();
		$paramsPay->returnUrl = $returnUrl;
		$paramsPay->cancelUrl = $cancelUrl;
		$paramsPay->currency = $paramsJd->get('admin_paypal_currency');

		$p = new Main_Payment();
		$p->setMethod($this->_sessionData->shipping->payment);
		$info = $p->pay($this->_sessionData->count->priceAll, $paramsPay);

		$endinfo = $p->getEndInfo();


		$this->setRedirect(JRoute::_(JomdirectoryHelperRoute::getCartRoute('finish')), $endinfo);

//order_id,sitename,product_qty,product_name,product_price,vat,total_price,billing_address,shipping_address
//            $data = array();
//            $data['order_id'] = '';
//            $data['sitename'] = '';
//            $data['product_qty'] = '';
//            $data['product_name'] = '';
//            $data['product_price'] = '';
//            $data['vat'] = '';
//            $data['total_price'] = '';
//            $data['billing_address'] = '';
//            $data['shipping_address'] = '';
//            $send = Main_Mail::send($data, 'CART_DONE', $email, 'com_jomdirectory');
//
		return;
	}

}
