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

jimport('joomla.application.component.controller');

/**
 * Component Controller
 *
 * @package    Joomla.Site
 * @subpackage    com_jomdirectory
 */
class JomdirectoryController extends JControllerLegacy
{
	/**
	 * @var        string    The default view.
	 * @since    2.5
	 */
	protected $default_view = 'items';

	/**
	 * Method to display a view.
	 *
	 * @param    boolean            If true, the view output will be cached
	 * @param    array            An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return    JController        This object to support chaining.
	 * @since    2.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$urlparams = array('Itemid' => 'INT', 'categories_type_id' => 'INT', 'categories_address_id' => 'INT', 'view' => 'WORD', 'l' => 'WORD', 'date' => 'CMD', 'categories_address_id' => 'INT', 'limitstart' => 'INT');
		$user = JFactory::getUser();
		$cachable = true;

		if ($user->get('id')) {
			$cachable = false;

		}


		parent::display($cachable, $urlparams);

		$params = JComponentHelper::getParams('com_jomdirectory');
		if ($params->get('enable_powered') && JRequest::getString('layout') != 'chart') {
			$anchor = $params->get('backlink', 'Joomla Directory');
			echo "<div class='text-center my-3 jc-powered'>" . 'Powered by &copy; JomDirectory <a href="http://comdev.eu/jomdirectory" title="' . $anchor . '">' . $anchor . '</a>' . "</div>";
		}

		return $this;
	}

}
