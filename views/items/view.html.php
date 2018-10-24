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

jimport('joomla.application.component.view');

/**
 * View to list an items.
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryViewItems extends JViewLegacy
{

	protected $form;
	protected $item;
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

		JText::script('COM_JOMCOMDEV_SAVED');

		$this->items = $this->get('Items');
		$this->itemsMap = $this->get('ItemsForMap');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

		$this->params = $this->state->get('params');

		$document = JFactory::getDocument();

		$layout = $this->params->get('layout');
		if (!empty($layout)) $this->setLayout($layout);


		if ($this->params->get('layout') == '_:default') {
			$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_items.css');
		} else {
			$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_items' . str_replace('_:', '_', $this->params->get('layout')) . '.css');
		}


		if (empty($this->items)) {
			parent::display($tpl);
			return;
		}

		// address
		$address = new Main_Address();

		foreach ($this->items AS $key => $item) {
			// fields
			$field = new Main_FieldsSite($item->id, 'com_jomdirectory', 'allof');
			$this->_fields = $field->getFields(array(24, 81));
			$item->fields = $this->_fields->prepareFields($item->id);

			$paid_field = new Main_FieldsSite($item->id, 'com_jomdirectory', 'allof');
			$paid_field_id = $paid_field->getGroupsIDs();

			$paid_fields = $paid_field->getFields($paid_field_id["COM_JOMCOMDEV_TYPE_FIELDS_GROUP_PAIDITEMS"]);
			$item->paid_fields = $paid_fields->prepareFields($item->id);

			$item->address = $address->getAddress($item->categories_address_id);
		}

		$tabsIds = array();
		$tabsIdsU = array();
		foreach ($this->items AS $it) {
			array_push($tabsIds, $it->id);
			$tabsIdsU[$it->id] = $it->users_id;
		}

		// images
		$images = Main_Image::getInstance();
		$imagesIn = $images->getImagesInContent($tabsIds);
		$imagesInU = $images->getImagesInContent($tabsIdsU, 'users_id', '');


		$imgWidth = $this->params->get('image_items_width');
		$imgFormat = $this->params->get('image_items_format');
		$imgLogoWidth = $this->params->get('image_logo_width');

		$this->images = array();
		$this->images_logo = array();
		if (isset($imagesIn) && !empty($imagesIn)) {
			foreach ($imagesIn AS $img) {
				if ($img->alias == 'com-jomcomdev-type-images-intro') {
					if (!array_key_exists($img->content_id, $this->images)) $this->images[$img->content_id] = Main_Image_Helper::img($imgWidth, $img->path . DS . $img->name, $imgFormat);
				}
				// Logo company get
				if ($img->alias == 'com-jomcomdev-type-images-logo') {
					if (!array_key_exists($img->content_id, $this->images_logo)) $this->images_logo[$img->content_id] = Main_Image_Helper::img($imgLogoWidth, $img->path . DS . $img->name);
				}
			}
		}


		$this->images_user = array();
		if (isset($imagesInU) && !empty($imagesInU)) {
			foreach ($tabsIdsU AS $contentId => $usersId) {
				if (!array_key_exists($contentId, $this->images_user)) {
					if (isset($imagesInU[$usersId])) $this->images_user[$contentId] = Main_Image_Helper::img($imgLogoWidth, $imagesInU[$usersId]->path . DS . $imagesInU[$usersId]->name);
				}
			}
		}

		$jVerArr = explode('.', JVERSION);
		if ($jVerArr[0] >= '3') {
			foreach ($this->items as $i => $item) :
				$item->tags = new JHelperTags;
				$item->tags->getItemTags('com_jomdirectory.content', $item->id);
			endforeach;
		}

		$table = JTable::getInstance('Statistic', 'JomcomdevTable');
		$table->addViewListStats($tabsIds, 'com_jomdirectory');

		Main_Log::log($this->items, 'Items from view');

		$this->_prepareDocument();

		parent::display($tpl);
	}


	private function _prepareDocument($type = false)
	{
		$title = null;
		$description = null;
		$keywords = null;

		$standardTitleAdd = ' ';
		$standardDescAdd = '';
		$standardKeywordsAdd = '';

		$active = JFactory::getApplication()->getMenu()->getActive();
		if (empty($active->id)) return false;
		$params = $active->params;

		if ($params->get('page_title')) {
			$title = $params->get('page_title');
		} else {
			$title = $active->title;
		}
		$description = $params->get('menu-meta_description');
		$keywords = $params->get('menu-meta_keywords');


		$title = $title . ' ' . $standardTitleAdd;
		$description = $standardDescAdd . ' ' . $description;
		$keywords = $keywords . ' ' . $standardKeywordsAdd;


		$this->document->setTitle($title);
		$this->document->setDescription($description);
		$this->document->setMetadata('keywords', $keywords);

	}
}
