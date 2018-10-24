<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryControllerForm extends JControllerForm
{
	private $_tmpl;

	public function __construct($config = array())
	{
		$this->_tmpl = JPATH_BASE . DS . 'components' . DS . 'com_jomdirectory' . DS . 'templates' . DS . 'emails';
		parent::__construct($config);
	}

	public function productReviews()
	{
		$data = JRequest::getVar('jform');
		$db = JFactory::getDBO();
		$query = "SELECT users_id FROM #__cddir_products WHERE id=" . (int)$data['content_id'];
		$db->setQuery($query);
		$item = $db->loadObject();
		$usero = JFactory::getUser($item->users_id);
		$this->reviews($usero, 1);
	}

	public function reviews($usero = null, $products = null)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jomdirectory' . DS . 'tables');
		$table = JTable::getInstance('Comment', 'JomdirectoryTable');
		$model = JModelLegacy::getInstance('Item', 'JomdirectoryModel');
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();


		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		if ($user->id) {
			$form->removeField('username');
		}


		$params = JComponentHelper::getParams('com_jomdirectory');

		if (!$params->get('reviews_terms')) {
			$form->removeField('terms');
		}

		// Get the user data.
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');
		$data = $model->validate($form, $requestData);

		if ($params->get('enable_captcha') && ($params->get('enable_captcha_reg') || $user->guest)) {
			require_once JPATH_BASE . DS . 'components' . DS . 'com_jomcomdev' . DS . 'libraries' . DS . 'recaptcha' . DS . 'recaptchalib.php';
			$plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
			$params_c = new JRegistry($plugin->params);
			$reCaptcha = new ReCaptcha($params_c->get('private_key', ''));
			if ($_POST["g-recaptcha-response"]) {
				$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
			}
			if ($resp == null || !$resp->success) {
				$app->enqueueMessage(JText::_('COM_JOMDIRECTORY_WRONG_CAPTCHA'), 'warning');
				$data = false;
			}
		}

		$method = JRequest::getMethod();
		$redirect = Main_Url::getReferer('reviews');


		$moderateReviews = $params->get('reviews_moderate', 0);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}


			// Save the data in the session.
			$app->setUserState('com_jomdirectory.item.reviews.data', $requestData);

			// Redirect back to the registration screen.
			$this->setRedirect($redirect);
			return false;
		} else {

			$data = JRequest::getVar('jform');
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
			if ($user->id) $data['user_id'] = $user->id;
			if ($moderateReviews) {
				$data['published'] = 1;
				$data['approved'] = 0;
			} else {
				$data['approved'] = 1;
				$data['published'] = 1;
			}

			if ($params->get('reviews_rating_method')) {
				foreach ($data["ratings"] as $key => $val) $data["ratings"][$key] = $val;
			}
			$sum = array_sum($data["ratings"]);
			if ($sum) $data['rate'] = $sum / count($data["ratings"]);

			if ($products) {
				$sqlextension = "com_jomdirectory.products";
				$data['extension'] = "com_jomdirectory.products";
			} else $sqlextension = "com_jomdirectory";

			$table->save($data);

			foreach ($data["ratings"] as $k => $el) {
				$query = 'INSERT INTO #__cddir_rates SET review_id=' . $table->id . ', type_id=' . (int)$k . ', rate=' . (int)$el . ', extension="' . $sqlextension . '"';
				$db->setQuery($query);
				$db->query();
				$db->insertid();
			}
			foreach ($data["recomended"] as $k => $el) {
				$query = 'INSERT INTO #__cddir_rates SET review_id=' . $table->id . ', type_id=' . (int)$k . ', recommendation=' . $db->quote($el) . ', extension="' . $sqlextension . '"';
				$db->setQuery($query);
				$db->query();
				$db->insertid();
			}
			$message = JText::_('COM_JOMDIRECTORY_FRONT_FORM_REVIEWS_SAVED');
			if ($method == 'POST') {
				$this->setMessage($message);
				$this->setRedirect($redirect);
			}

			if (!$usero) {
				$query = "SELECT users_id FROM #__cddir_content WHERE id=" . (int)$data['content_id'];
				$db->setQuery($query);
				$item = $db->loadObject();
				$usero = JFactory::getUser($item->users_id);
			}

			$data['link'] = JURI::base() . trim($redirect, '/');
			$item = $model->getItem((int)$data['content_id']);
			$data['title'] = $item->title;
			$data['admin_title'] = $item->title;
			$data['admin_link'] = JURI::base() . '/index.php?option=com_jomdirectory&view=admin_dashboard';


			//$mailer = JFactory::getMailer();
			//$config = JFactory::getConfig();

			//$sender = array(
			//$config->get( 'mailfrom' ),
			//$config->get( 'fromname' ) );

			$data['name'] = $usero->username;
			$email = array();
			array_push($email, $usero->email);

			//$mailer->setSender($sender);
			// $mailer->addRecipient($usero->email);

			//$mailer->isHTML(true);
			// $mailer->Encoding = 'base64';
			// $body   =  $this->_getTmpl($this->_tmpl.DS.'listing_notification_review_approve_default.php', $data);
			// $mailer->setSubject(JText::_('COM_JOMDIRECTORY_EMAIL_FORM_CONTACT_TITLE'));
			//$mailer->setBody($body);


			// $send = $mailer->Send();

			if ($moderateReviews) $send = Main_Mail::send($data, 'REVIEW_APPROVE', $email, 'com_jomdirectory'); else $send = Main_Mail::send($data, 'REVIEW_ADD', $email, 'com_jomdirectory');
		}
	}

	public function taf()
	{
		JSession::checkToken() or die;


		$params = JComponentHelper::getParams('com_jomdirectory');
		if (!$params->get('enable_taf')) return false;
//        $table = JTable::getInstance('Reviews','JomdirectoryTable');
		$model = JModelLegacy::getInstance('Item', 'JomdirectoryModel');
//        $user = JFactory::getUser();
		$app = JFactory::getApplication();


		// Validate the posted data.
		$form = $model->getFormTellAFriend();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}


		// Get the user data.
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');
		$data = $model->validate($form, $requestData);


		if ($params->get('enable_captcha') && ($params->get('enable_captcha_reg') || $user->guest)) {
			require_once JPATH_BASE . DS . 'components' . DS . 'com_jomcomdev' . DS . 'libraries' . DS . 'recaptcha' . DS . 'recaptchalib.php';
			$plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
			$params_c = new JRegistry($plugin->params);
			$reCaptcha = new ReCaptcha($params_c->get('private_key', ''));
			if ($_POST["g-recaptcha-response"]) {
				$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
			}
			if ($resp == null || !$resp->success) {
				$app->enqueueMessage(JText::_('COM_JOMDIRECTORY_WRONG_CAPTCHA'), 'warning');
				$data = false;
			}
		}


		$method = JRequest::getMethod();
		$redirect = Main_Url::getReferer();

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_jomdirectory.item.taf.data', $requestData);

			// Redirect back to the registration screen.			
			$this->setRedirect($redirect);
			return false;
		} else {
			$data = JRequest::getVar('jform');
			$data['link'] = JURI::base() . trim($redirect, '/');

			$item = $model->getItem((int)$data['content_id']);
			$data['title'] = $item->title;
			$data['taf_title'] = $data['subject'];
			$data['email_content'] = $data['messagetaf'];

			$email = array();
			array_push($email, $data['email_rec']);

			$send = Main_Mail::send($data, 'TELLAFRIEND', $email, 'com_jomdirectory', $data['email']);

			//$mailer = JFactory::getMailer();
			//$config = JFactory::getConfig();
			//$sender = array(
			//$config->get( 'mailfrom' ),
			//$config->get( 'fromname' ) );

			//$mailer->setSender($sender);
			//$mailer->addRecipient($data['email_rec']);
			//if($params->get('admin_email_copy') && $config->get( 'mailfrom' )!=$data['email_rec']) {
			//	 $mailer->addRecipient($config->get( 'mailfrom' ));
			//}
			//$mailer->isHTML(true);
			//$mailer->Encoding = 'base64';
			//$body   =  $this->_getTmpl($this->_tmpl.DS.'taf_default.php', $data);


//            
//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            echo "\n".$body;
//            echo $this->_tmpl.DS.'taf_default.php';
//            if(file_exists($this->_tmpl.DS.'taf_default.php')) echo 'tal';
//            echo '</pre>';
//            exit;

			//$mailer->setSubject($data['subject'].' - '.JText::_('COM_JOMDIRECTORY_FRONT_FORM_TAF_TITLE'));
			//$mailer->setBody($body);


			//$send = $mailer->Send();
			if (!$send) {
				$message = JText::_('COM_JOMDIRECTORY_FRONT_FORM_TAF_SEND_FAIL');
			} else {
				$message = JText::_('COM_JOMDIRECTORY_FRONT_FORM_TAF_SEND');

				$db = JFactory::getDBO();
				$query = 'INSERT INTO #__cddir_messages SET content_id =' . (int)$data['content_id'] . ', email_from=' . $db->quote($data['email']) . ', email_to=' . $db->quote($data['email_owner']) . ', date=now(), type="Contact", extension="com_jomdirectory", message=' . $db->quote($send[0]->body) . '';
				$db->setQuery($query);
				$db->query();
			}

			$this->setMessage($message);
			$this->setRedirect($redirect);
		}


	}

	public function contact()
	{

//        $table = JTable::getInstance('Reviews','JomdirectoryTable');
		$model = JModelLegacy::getInstance('Item', 'JomdirectoryModel');
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		$params = JComponentHelper::getParams('com_jomdirectory');


		// Validate the posted data.
		$form = $model->getFormContact();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}


		if (!$params->get('enable_terms')) {
			$form->removeField('terms');
		}

		// Get the user data.
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');
		$data = $model->validate($form, $requestData);
		$item = $model->getItem((int)$data['content_id']);
		$data['title'] = $item->title;
		$data['email_content'] = $data['message'];

		if ($params->get('enable_captcha') && ($params->get('enable_captcha_reg') || $user->guest)) {
			require_once JPATH_BASE . DS . 'components' . DS . 'com_jomcomdev' . DS . 'libraries' . DS . 'recaptcha' . DS . 'recaptchalib.php';
			$plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
			$params_c = new JRegistry($plugin->params);
			$reCaptcha = new ReCaptcha($params_c->get('private_key', ''));
			if ($_POST["g-recaptcha-response"]) {
				$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
			}
			if ($resp == null || !$resp->success) {
				$app->enqueueMessage(JText::_('COM_JOMDIRECTORY_WRONG_CAPTCHA'), 'warning');
				$data = false;
			}
		}


//        $method = JRequest::getMethod();
		$redirect = Main_Url::getReferer();

//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//        print_r($redirect);
//        echo '</pre>';
//exit;

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_jomdirectory.item.contact.data', $requestData);

			// Redirect back to the registration screen.
			$this->setRedirect($redirect);
			return false;
		} else {

//            $data = JRequest::getVar('jform');
			$data['link'] = JURI::base() . trim($redirect, '/');
			$config = JFactory::getConfig();

			if ($data['email_owner']) {
				$email = $data['email_owner'];
			} else {
				$email = $config->get('mailfrom');
			}

			$send = Main_Mail::send($data, 'CONTACT', $email, 'com_jomdirectory', $data['email']);

			if (!$send) {
				$message = JText::_('COM_JOMDIRECTORY_FRONT_FORM_CONTACT_SEND_FAIL') . $send;
			} else {
				$message = JText::_('COM_JOMDIRECTORY_FRONT_FORM_CONTACT_SEND');

				$db = JFactory::getDBO();
				$query = 'INSERT INTO #__cddir_messages SET content_id =' . (int)$data['content_id'] . ', email_from=' . $db->quote($data['email']) . ', email_to=' . $db->quote($data['email_owner']) . ', date=now(), type="Contact", extension="com_jomdirectory", message=' . $db->quote($send[0]->body) . '';
				$db->setQuery($query);
				$db->query();
			}

			$this->setMessage($message);
			$this->setRedirect($redirect);
		}


	}


	protected function _getTmpl($path, $data)
	{
		if (!file_exists($path)) return false;
		ob_start();
		require $path;

		return ob_get_clean();
	}
}
