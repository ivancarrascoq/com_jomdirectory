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
class JomdirectoryViewCategories extends JViewLegacy
{
	protected $form;
	protected $items;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$app = JFactory::getApplication();
		// get menu for getting parameters
		$menu = $app->getMenu();
		$this->active = $menu->getActive();

//        $params = new JRegistry();
		$params = JRegistry::getInstance('com_jomdirectory.params.list');

		if ($this->active) {
			$params->loadString($this->active->params);
			$this->params = $params;
		}

//exit;

		$this->setLayout($this->params->get('layout'));

		$this->items = $this->get('Items');
		$this->state = $this->get('State');
//            $this->pagination   = $this->get('Pagination');

//        echo '<pre>';
//        echo "" . __FILE__ . " \n" . __METHOD__ . " Line: " . __LINE__ . "\n";
//        echo "\n";
//        print_r($this->items);
//        echo '</pre>';

//exit;

		$this->letters = array(JText::_('COM_JOMDIRECTORY_SEARCH_ALPHABET_ALL'), 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

		if (count($this->items)) {
			$tab = array();
			$tabsIds = array();
			$levelSub = false;
			foreach ($this->items AS $key => $it) {
				if ($key == 0) {
					if ($it->level > 1) {
						$levelSub = true;
						$levelSubHow = $it->level;
					}
				}

				if ($levelSub) {
					$it->level = $it->level - $levelSubHow + 1;
				}

				array_push($tabsIds, $it->id);
				if ($it->level >= 3) {
					unset($this->items[$key]);
					continue;
				}
				if ($it->level == 1) {
					$tab[$it->id] = $it;
				}
				if ($it->level == 2) {
					if (!isset($tab[$it->parent_id]) || !is_object($tab[$it->parent_id])) {
						$tab[$it->parent_id] = new stdClass();
					}
					if (!isset($tab[$it->parent_id]->deeper)) {
						$tab[$it->parent_id]->deeper = array();
					}
					array_push($tab[$it->parent_id]->deeper, $it);
					unset($this->items[$key]);
				}
			}

			// images
			$images = Main_Image::getInstance();
			$imagesInU = $images->getImagesInContent($tabsIds, 'category_id', 'com_jomdirectory.categories');
			if (isset($imagesInU) && !empty($imagesInU)) {
				foreach ($imagesInU AS $img) {
					$this->images[$img->category_id] = Main_Image_Helper::img($this->params->get('image_cat_width'), $img->path . DS . $img->name, $this->params->get('image_cat_format'));
				}
			}

			Main_Log::log($this->items, 'Items categories from view');
		}


		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_categories' . str_replace('_:', '_', $this->params->get('layout')) . '.css');


		if (JRequest::getVar('m') == 1) {
			$this->addTemplatePath(JPATH_COMPONENT_SITE . DS . 'views' . DS . 'categories' . DS . 'tmpl_mobile');
			$this->setLayout('m.' . $this->getLayout());
			JRequest::setVar('tmpl', 'component');
		}

		parent::display($tpl);
	}
}
