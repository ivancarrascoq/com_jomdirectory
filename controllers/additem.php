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
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryControllerAddItem extends JControllerForm
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

		$this->view_list = 'items';

		parent::__construct($config);
	}
}
