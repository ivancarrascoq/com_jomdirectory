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
class JomdirectoryControllerAdmin_listings extends JControllerLegacy
{
	protected $user;

	function __construct($default = array())
	{
		parent::__construct($default);
		$this->user = JFactory::getUser();
	}

	public function publish()
	{
		$db = JFactory::getDBO();
		$query = "SELECT  COUNT(*) FROM #__cddir_content WHERE users_id={$this->user->id} and published=1";
		$db->setQuery($query);
		$listings_nr = $db->loadResult();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$listings_nr += count($cid);
		$limits = $this->getPlanLimits();
		if ($limits && $listings_nr > $limits->listings_nr) JError::raiseWarning(100, JText::_("COM_JOMDIRECTORY_NOT_ENOUGH_LISTINGS_IN_PLAN")); else if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_content SET published ="1" WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

	public function getPlanLimits()
	{
		$db = JFactory::getDBO();
		$old = Main_FrontAdmin::getPlanExpiry($this->user->id, 'com_jodirectory');
		$groups = $this->user->get('groups');
		$query = "SELECT group_id,listings_nr,premium_nr,video FROM #__cddir_plans WHERE extension='com_jomdirectory' ORDER BY price_annually desc, price_monthly desc, id desc";
		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach ($data as $row) {
			foreach ($groups as $plan) {
				if ($row->group_id == $plan && !$old) return $row;
			}
		}
		return $row;
	}

	function unpublish()
	{
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_content SET published ="0" WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
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
			$query = 'DELETE FROM #__cddir_content WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

	public function featured()
	{
		$db = JFactory::getDBO();
		$query = "SELECT  COUNT(*) FROM #__cddir_content WHERE users_id={$this->user->id} and featured=1";
		$db->setQuery($query);
		$listings_nr = $db->loadResult();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$listings_nr += count($cid);
		$limits = $this->getPlanLimits();
		if ($limits && $listings_nr > $limits->premium_nr) JError::raiseWarning(100, JText::_("COM_JOMDIRECTORY_NOT_ENOUGH_PREMIUM_IN_PLAN")); else if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_content SET featured ="1" WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

	public function unfeatured()
	{
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_content SET featured ="0" WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

	function add()
	{
		$app =& JFactory::getApplication();
		$app->redirect('index.php?option=com_jomdirectory&view=admin_additem');
	}
}
