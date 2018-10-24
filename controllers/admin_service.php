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

/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class JomdirectoryControllerAdmin_service extends JControllerForm
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

		JRequest::setVar('view', 'admin_service');
		$this->view_list = 'admin_services';
		$this->view_item = 'admin_service';

		parent::__construct($config);
	}
}