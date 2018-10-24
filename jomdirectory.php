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

// Include dependencies
jimport('joomla.application.component.controller');

require_once JPATH_BASE . DS . 'components' . DS . 'com_jomcomdev' . DS . 'helpers' . DS . 'remember.php';

$document = JFactory::getDocument();

$baseurl = JURI::base(true);


$app = JFactory::getApplication();
$params = $app->getParams();
$input = $app->input;
$view = $input->get('view');
if (current(explode('_', $view)) == 'admin') {
	$menu = $app->getMenu();
	$menuItem = $menu->getItems('link', 'index.php?option=com_jomdirectory&view=admin_dashboard', true);
	if (isset($menuItem->id)) {
		$input->set('Itemid', $menuItem->id);
		$app->getMenu()->setActive($menuItem->id);
	}
}


JLoader::register('JomdirectoryHelperRoute', dirname(__FILE__) . '/helpers/route.php');
JLoader::register('JButtonFrontend', dirname(__FILE__) . DS . 'helpers' . DS . 'frontend.php');

JHtml::_('jquery.framework');

$view = JRequest::getString('view') ? '_' . JRequest::getString('view') : '';


$lang = JFactory::getLanguage();
$lang->load('com_jomcomdev', JPATH_BASE, null, false, false) || $lang->load('com_jomcomdev', JPATH_BASE . DS, "en-GB", false, false) || $lang->load('com_jomcomdev', JPATH_BASE . DS . 'components' . DS . 'com_jomcomdev' . DS . null, false, false) || $lang->load('com_jomcomdev', JPATH_BASE . DS . 'components' . DS . 'com_jomcomdev' . DS . "en-GB", false, false);


$document->addScript(JURI::base().'components/com_jomcomdev/assets/comdev-main.js');
$document->addStyleSheet(JURI::base().'components/com_jomcomdev/assets/css/comdev-main.css');

$document->addScript(JURI::base().'components/com_jomcomdev/javascript/comdev.js');

$document->addScript(JURI::base() . 'components/com_jomdirectory/assets/js/jomdirectory.js');

$document->addStyleSheet(JURI::base() . 'components/com_jomdirectory/assets/css/jomdirectory.css');


// Add form path and fields
JForm::addFormPath(JPATH_COMPONENT_SITE . '/models/forms');


$controller = JControllerLegacy::getInstance('Jomdirectory');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
