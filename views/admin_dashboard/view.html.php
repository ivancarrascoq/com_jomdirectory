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

jimport('joomla.application.component.view');

/**
 * View to list an items.
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryViewAdmin_dashboard extends JViewLegacy
{
	protected $listings_count;
	protected $approved_count;
	protected $reviews_count;
	protected $chart;
	protected $user;
	protected $welcome;
	protected $reviews;
	protected $toolbar;
	protected $state;
	protected $pagination;
	protected $plan_name;
	protected $plan_limits;
	protected $plan_usage;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->user = $this->get('User');
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');
		if (!$this->user->name || !$this->get('LoginGroup')) {
			JError::raiseWarning(100, JText::_('JERROR_ALERTNOAUTHOR'));
			$app = JFactory::getApplication();
			if ($this->params->get('admin_form_login')) $app->redirect(JRoute::_('index.php?Itemid=' . $this->params->get('admin_form_login'))); else $app->redirect(JRoute::_(JURI::base()));
			return false;
		}
		$this->chart = $this->get('Chart');
		$this->listings_count = $this->get('ListingsCount');
		$this->approved_count = $this->get('ApprovedCount');
		$this->reviews_count = $this->get('ReviewsCount');
		$this->welcome = $this->get('WelcomeArticle');
		$this->pagination = $this->get('Pagination');
		$this->toolbar = $this->get('Toolbar');
		$this->reviews = $this->get('Items');
		$this->plan_name = $this->get('PlanName');
		$this->plan_limits = $this->get('PlanLimits');
		$this->plan_usage = $this->get('PlanUsage');
		$this->userImage = 'components/com_jomdirectory/assets/images/portrait_image.png';

		$images = Main_Image::getInstance();
		$imagesInU = current($images->getImagesInContent($this->user->id, 'users_id', ''));
		if (isset($imagesInU) && !empty($imagesInU)) $this->userImage = Main_Image_Helper::img(112, $imagesInU->path . DS . $imagesInU->name);

		$document = JFactory::getDocument();
		$runscript = '
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(' . $this->chart . ');

        var options = { 
            pointShape:"circle", pointSize:5, colors: ["#33b5e5", "#0022FF"], 
        } 
        
        var chart = new google.visualization.AreaChart(document.getElementById(\'chart\'));
        chart.draw(data, options);
        
      }
   ';
		$document->addScript('https://www.google.com/jsapi');
		$document->addScriptDeclaration($runscript);

		parent::display($tpl);
	}
}
