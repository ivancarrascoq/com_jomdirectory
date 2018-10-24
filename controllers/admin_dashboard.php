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
class JomdirectoryControllerAdmin_dashboard extends JControllerLegacy
{
	function publish()
	{
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_reviews SET approved ="1" WHERE id IN ( ' . $cids . ' ) and extension="com_jomdirectory"';
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
			$params_config = JComponentHelper::getParams('com_jomdirectory');
			if ($params_config->get('reviews_moderate')) {
				$tmpl = JPATH_BASE . DS . 'components' . DS . 'com_jomdirectory' . DS . 'templates' . DS . 'emails';
				$params_config = JComponentHelper::getParams('com_jomdirectory');

				//$mailer = JFactory::getMailer();
				//$config = JFactory::getConfig();
				//$sender = array(
				//     $config->get( 'mailfrom' ),
				//    $config->get( 'fromname' ) );
				// $mailer->setSender($sender);
				$email = array();
				for ($i = 0; $i < count($cid); $i++) {
					$query = 'SELECT content_id,user_id,title,text,username,email FROM #__cddir_reviews WHERE id=' . (int)$cid[$i];
					$db->setQuery($query);
					$row = $db->loadRow();
					if ($row[0]) {
						$query = 'SELECT alias,categories_id,categories_address_id,title FROM #__cddir_content WHERE id=' . (int)$row[0];
						$db->setQuery($query);
						$item = $db->loadRow();
						if ($row[1]) {
							$user = JFactory::getUser($row[1]);
							$data['name'] = $user->get('name');
							//$mailer->addRecipient($user->get( 'email' ));
							$email[0] = $user->get('email');
						} else {
							$data['name'] = $row[4];
							//$mailer->addRecipient($row[5]);
							$email[0] = $row[5];
						}
						$data['link'] = JURI::base() . JRoute::_(JomdirectoryHelperRoute::getArticleRoute($row[0], $item[0], $item[1], $item[2]));
						$data['listing'] = $item[3];
						$data['review_title'] = $row[2];
						$data['text'] = $row[3];
						//$mailer->isHTML(true);
						//$mailer->Encoding = 'base64';
						//$body   =  $this->_getTmpl($tmpl.DS.'listing_notification_review_approved_default.php', $data);
						//$mailer->setSubject(strip_tags(JText::sprintf('COM_JOMDIRECTORY_EMAIL_REVIEW1_HEADER', $data['name'])));
						//$mailer->setBody($body);
						//$mailer->Send();
						$send = Main_Mail::send($data, 'REVIEW_APPROVED', $email, 'com_jomdirectory');

					}
				}
			}
		}
		parent::display();
	}

	function delete()
	{
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'DELETE FROM #__cddir_reviews WHERE id IN ( ' . $cids . ' ) and extension="com_jomdirectory"';
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

	protected function _getTmpl($path, $data)
	{
		if (!file_exists($path)) return false;
		ob_start();
		require $path;

		return ob_get_clean();
	}
}
