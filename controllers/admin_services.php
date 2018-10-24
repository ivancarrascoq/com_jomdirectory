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

jimport('joomla.application.component.controlleradmin');

/**
 * Articles list controller class.
 *
 * @package        Joomla.Administrator
 * @subpackage    com_contact
 * @since    1.6
 */
class JomdirectoryControllerAdmin_services extends JControllerAdmin
{

	/**
	 * Class constructor.
	 *
	 * @param   array $config A named array of configuration variables.
	 *
	 * @since    1.6
	 */
	protected $user;

	function __construct($default = array())
	{
		parent::__construct($default);
		$this->user = JFactory::getUser();
	}

	public function publish()
	{
		$db = JFactory::getDBO();
		$query = "SELECT  COUNT(*) FROM #__cddir_services WHERE users_id={$this->user->id} and published=1";
		$db->setQuery($query);
		$listings_nr = $db->loadResult();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_services SET published ="1" WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

	function unpublish()
	{
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__cddir_services SET published ="0" WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
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
			$query = 'DELETE FROM #__cddir_services WHERE id IN ( ' . $cids . ' ) AND users_id=' . (int)$this->user->id;
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
		$app->redirect('index.php?option=com_jomdirectory&view=admin_service');
	}
}