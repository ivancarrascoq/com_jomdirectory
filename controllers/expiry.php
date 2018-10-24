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

jimport('joomla.application.component.controllerform');
jimport('joomla.access.access');
jimport('joomla.user.user');
jimport('joomla.log.log');

class JomdirectoryControllerExpiry extends JControllerLegacy
{
	private $_tmpl;
	private $_extension = 'com_jomdirectory';

	public function __construct($config = array())
	{
		$this->_tmpl = JPATH_BASE . DS . 'components' . DS . 'com_jomdirectory' . DS . 'templates' . DS . 'emails';
		parent::__construct($config);
	}

	public function send()
	{

		$params = JComponentHelper::getParams($this->_extension);
		$paramsA = $params->toArray();
		$nr = 0;
		$config = JFactory::getConfig();

		$format = '%Y-%m-%d';
		$dateA = strftime($format, strtotime('+' . (int)$paramsA['membership_expiration_reminder'] . ' day'));
		$dateN = strftime($format);
		$db = JFactory::getDbo();
		$this->_extension = substr($this->_extension, 4);
		$query = "SELECT id FROM #__usergroups where title=" . $db->quote($this->_extension);
		$db->setQuery($query);
		if ($parent_id = $db->loadResult()) {
			$query = $db->getQuery(true);
			$query->select('id, title')->from('#__usergroups')->where('parent_id = ' . (int)$parent_id)->order('lft');

			$db->setQuery($query);
			$usergroups = $db->loadObjectList();
			$email = array();
			if ($usergroups) {
				foreach ($usergroups as $group) {
					$users = JAccess::getUsersByGroup($group->id);
					foreach ($users as $user) {
						$userT = JFactory::getUser($user);
						$date = Main_FrontAdmin::getPlanExpiry($userT->id, 'com_jomdirectory', 1);
						if ($dateA == $date) {
							echo $userT->name . "<br>";
							$data = array();
							$data['name'] = $userT->name;
							$data['user'] = $userT->name;
							$data['email'] = $userT->email;
							$data['expiry_date'] = $date;
							$data['plan'] = $group->title;
							$data['sitename'] = $config->get('sitename');
							$data['membership'] = $group->title;
							$data['company'] = $config->get('sitename');
							$data['days'] = $paramsA['membership_expiration_reminder'];
							$email[0] = $userT->email;
							$send = Main_Mail::send($data, 'MEMBERSHIP_EXPIRY', $email, 'com_jomdirectory');
							$nr++;
						}
						if ($dateN == $date) {
							$data = array();
							$data['name'] = $userT->name;
							$data['user'] = $userT->name;
							$data['email'] = $userT->email;
							$data['expiry_date'] = $date;
							$data['plan'] = $group->title;
							$data['sitename'] = $config->get('sitename');
							$data['membership'] = $group->title;
							$data['company'] = $config->get('sitename');
							$email[0] = $userT->email;
							$send = Main_Mail::send($data, 'MEMBERSHIP_EXPIRED', $email, 'com_jomdirectory');
						}
						$old = 0;
						$date1 = Main_FrontAdmin::getPlanExpiry($userT->id, 'com_jomdirectory');
						if ($dateN >= $date && strpos($date1, '#blocked') === false && strpos($date1, 'never expires') === false) $old = 1;
						$this->unpublish($userT->id, $group->id, $old);
					}
				}
			}
		}
		if ($nr) JLog::add('Sent ' . $nr . ' expire reminder emails');
		die ('Sent ' . $nr . ' expire reminder emails');
	}

	public function unpublish($user_id, $group_id, $old)
	{
		$db = JFactory::getDbo();
		$limits = null;
		$query = "SELECT group_id,listings_nr,premium_nr,video FROM #__cddir_plans WHERE extension='com_jomdirectory' ORDER BY price_annually desc, price_monthly desc, id desc";
		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach ($data as $row) {
			if ($row->group_id == $group_id && !$old) {
				$limits = $row;
				break;
			}
		}
		if ($old) {
			JUserHelper::removeUserFromGroup($user_id, $group_id);
			JUserHelper::addUserToGroup($user_id, $row->group_id);
		}
		if (!$limits) $limits = $row;
		if ($limits) {
			$featured_nr = 0;
			$listings_nr = 0;
			$query = "SELECT featured, id FROM #__cddir_content WHERE users_id={$user_id} and published=1 order by featured desc, date_created desc";
			$db->setQuery($query);
			$data = $db->loadObjectList();
			foreach ($data as $row) {
				$query = '';
				$listings_nr++;
				if ($row->featured) $featured_nr++;
				if ($listings_nr > $limits->listings_nr) $query = 'UPDATE #__cddir_content SET published ="0" WHERE id=' . (int)$row->id; else if ($featured_nr > $limits->premium_nr) $query = 'UPDATE #__cddir_content SET featured ="0" WHERE id=' . (int)$row->id;
				if ($query) {
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	protected function _mail($email, $data)
	{
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array($config->get('mailfrom'), $config->get('fromname'));

		$mailer->setSender($sender);
		$mailer->addRecipient($email);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$body = $this->_getTmpl($this->_tmpl . DS . 'membership_expiry_default.php', $data);

		$mailer->setSubject(JText::sprintf('COM_JOMDIRECTORY_MEMBERSHIP_EXPIRY_TITLE', $data['plan'], $data['sitename']));
		$mailer->setBody($body);
		$send = $mailer->Send();

	}

	protected function _getTmpl($path, $data)
	{
		if (!file_exists($path)) return false;
		ob_start();
		require $path;

		return ob_get_clean();
	}

	protected function _mail2($email, $data)
	{
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array($config->get('mailfrom'), $config->get('fromname'));

		$mailer->setSender($sender);
		$mailer->addRecipient($email);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$body = $this->_getTmpl($this->_tmpl . DS . 'membership_expired_default.php', $data);
		$mailer->setSubject(JText::sprintf('COM_JOMDIRECTORY_MEMBERSHIP_EXPIRED_TITLE', $data['plan'], $data['sitename']));
		$mailer->setBody($body);
		$send = $mailer->Send();

	}
}
