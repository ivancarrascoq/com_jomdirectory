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
 * Jomdirectory Component Items controller
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryControllerAdmin_messages extends JControllerLegacy
{
	/**
	 * Class constructor.
	 *
	 * @param   array $config A named array of configuration variables.
	 *
	 * @since    1.6
	 */
	function __construct($config = array())
	{

		$this->view_list = 'admin_messages';

		parent::__construct($config);

	}


	function delete()
	{
		global $option;
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'DELETE FROM #__cddir_messages' . ' WHERE id IN ( ' . $cids . ' )';
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('" . $db->getErrorMsg(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		parent::display();
	}

}
