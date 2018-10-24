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

jimport('joomla.application.component.modellist');

/**
 * Jomdirectory Component Category Model
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryModelAdmin_statistic extends JModelList
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';
	protected $user;
	protected $dates;
	protected $item_id;

	function __construct()
	{
		parent::__construct();
		$this->user = JFactory::getUser();
		$this->item_id = (int)JRequest::getVar('content_id');
		$current = strtotime(JRequest::getVar('current') . "-01");
		$this->dates['to'] = date("Y-n", $current);
		$this->dates['from'] = date("Y-n", strtotime("-1 year", $current));
		$this->dates['back'] = date("Y-m", strtotime("-1 year", $current));
		if (date("Y") > date("Y", $current)) $this->dates['forward'] = date("Y-m", strtotime("+1 year", $current)); else $this->dates['forward'] = null;
	}

	function getChart()
	{
		$return_array = array();
		$from = explode("-", $this->dates['from']);
		$to = explode("-", $this->dates['to']);
		$query = "SELECT view_in_list, view_item, add_favorite, date_year, date_month FROM #__cddir_statistic WHERE item_id =" . $this->item_id . " AND (date_year<" . $to[0] . " OR (date_year=" . $to[0] . " AND date_month<=" . $to[1] . ")) AND (date_year>" . $from[0] . " OR (date_year=" . $from[0] . " AND date_month>=" . $to[1] . "))  ORDER BY date_year, date_month";
		$data = $this->_getList($query);
		for ($i = 0; $i < 12; $i++) {
			$temp = strtotime($from[0] . "-" . $from[1] . "-01");
			$newfrom = date("Y-n", strtotime("+1 month", $temp));
			$from = explode("-", $newfrom);
			$newrow = new stdClass;
			$newrow->date_year = $from[0];
			if ($from[1] < 10) $newrow->date_month = "0" . $from[1]; else $newrow->date_month = $from[1];
			$newrow->view_in_list = 0;
			$newrow->view_item = 0;
			$newrow->add_favorite = 0;
			foreach ($data as $row) {
				if ($row->date_year == $from[0] && $row->date_month == $from[1]) {
					$newrow->view_in_list = $row->view_in_list;
					$newrow->view_item = $row->view_item;
					$newrow->add_favorite = $row->add_favorite;
					break;
				}
			}
			$return_array[] = $newrow;
		}
		return $return_array;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getDates()
	{
		return $this->dates;
	}

	public function getLoginGroup()
	{
		$component = "com_jomdirectory";
		$user = JFactory::getUser();
		return Main_FrontAdmin::getLoginGroup($user->id, $component);
	}
}