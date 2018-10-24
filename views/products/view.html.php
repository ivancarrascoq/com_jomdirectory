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
class JomdirectoryViewProducts extends JViewLegacy
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
		$app = JFactory::getApplication();

		$this->items = $this->get('Items');
		$this->itemsMap = $this->get('ItemsForMap');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

		$this->params = $this->state->get('params');
		JHtml::_('jquery.framework');

		$modJdCart = Main_Modules::isInstalled('mod_jomdirectory_cart');
		if (!$modJdCart) {
			$this->params->set('cart', 0);
		}

		$this->active = JFactory::getApplication()->getMenu()->getActive();
		$document = JFactory::getDocument();

		$metaDesc = $this->params->get('menu-meta_description', false);
		if ($metaDesc) $document->setMetaData('description', $metaDesc);


		$layout = $this->params->get('layout');
		if (!empty($layout)) $this->setLayout($layout);


		if ($app->getUserState('com_jomdirectory.products.list.save')) {
			$this->setLayout('schowek');
		}
		if ($app->getUserState('com_jomdirectory.products.list.compare')) {
			$this->setLayout('compare');
		}

		$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_items_product.css');


		if (empty($this->items)) {
			parent::display($tpl);
			return;
		}


		$compared = $app->getUserState('com_jomdirectory.products.list.compare');

		// address
		$address = new Main_Address();

		if ($compared) {
			$arrayCustom = array(23, 24, 133);
		} else $arrayCustom = array(24, 133);

		foreach ($this->items AS $key => $item) {
			// fields
			$field = new Main_FieldsSite($item->id, 'com_jomdirectory.products', 'allof');
			$this->_fields = $field->getFields($arrayCustom);
			$item->fields = $this->_fields->prepareFields($item->id);

			$paid_field = new Main_FieldsSite($item->id, 'com_jomdirectory.products', 'allof');
			$paid_field_id = $paid_field->getGroupsIDs();

			$paid_fields = $paid_field->getFields($paid_field_id["COM_JOMCOMDEV_TYPE_FIELDS_GROUP_PAIDITEMS"]);
			$item->paid_fields = $paid_fields->prepareFields($item->id);
			//address
			$item->address = $address->getAddress($item->categories_address_id);
		}


		$categoryId = JFactory::getApplication()->input->getInt('categories_id', false);
		$fieldsValues = $app->getUserState('com_jomdirectory.products.filter.fields', '');
		$fields = new Main_Fields(false, 'com_jomdirectory.products', $categoryId);
		$this->activeFilters = $fields->getActiveFilter($fieldsValues);

		$priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), //                             'currency_type'=>$this->params->get('currency_type',2),
			'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));


		$tabsIds = array();
		$tabsIdsU = array();
		foreach ($this->items AS $it) {
			$it->price_int = $it->price;
			$it->price = Main_Price::changeSingle($it->price, $priceParams);
			$it->price_old = Main_Price::changeSingle($it->price_old, $priceParams);
			array_push($tabsIds, $it->id);
			$tabsIdsU[$it->id] = $it->users_id;
		}

		// images
		$images = Main_Image::getInstance();
		$imagesIn = $images->getImagesInContent($tabsIds, 'content_id', 'com_jomdirectory.products');
		$imagesInU = $images->getImagesInContent($tabsIdsU, 'users_id', '');


		$imgWidth = $this->params->get('image_product_width');
		$imgFormat = $this->params->get('image_product_format');
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


		foreach ($this->items as $i => $item) {
			$item->tags = new JHelperTags;
			$item->tags->getItemTags('com_jomdirectory.products', $item->id);
		}


		$table = JTable::getInstance('Statistic', 'JomcomdevTable');
		$table->addViewListStats($tabsIds, 'com_jomdirectory.products');

		Main_Log::log($this->items, 'Items from view');

		parent::display($tpl);
	}
}
