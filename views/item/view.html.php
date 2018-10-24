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
JLoader::register('JomdirectoryHelper', JPATH_COMPONENT . '/helpers/jomdirectory.php');

/**
 * Jomdirectory controller for item edit
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryViewItem extends JViewLegacy
{

	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$dispatcher = JDispatcher::getInstance();

		JText::script('COM_JOMCOMDEV_REVIEWS_THANKS');
		JText::script('COM_JOMDIRECTORY_REVIEWS_VOTED');
		JText::script('COM_JOMDIRECTORY_SAVED');
		JText::script('COM_JOMCOMDEV_SAVED');

		// Initialiase variables.
		$this->item = $this->get('Item');

		if (empty($this->item->id)) {
			JError::raiseError(404, JText::_('COM_JOMCOMDEV_SITE_NOT_EXISTS'));
		}

		$model = $this->getModel();

		$this->item->reviews = $model->getReviews($this->item);
		$this->form = $this->get('Form');
		$this->formTellAFriend = $this->get('FormTellAFriend');
		$this->formContact = $this->get('FormContact');
		$this->state = $this->get('State');
		$this->articles = $model->getArticle($this->item->id);
		$this->params = $this->state->get('params');
		$this->item->text = $this->item->fulltext;
		$this->item->service = $model->getService($this->item->id);
		$this->enable_calendar = $model->getCalendar($this->item->id);

		//files
		$this->item->file = Main_File::get($this->item->id, 'com_jomdirectory', true);


		// fields
		$field = new Main_FieldsSite($this->item->id, 'com_jomdirectory', $this->item->categories_id);
		$fields = $field->getFields();
		$this->item->fields = $fields->prepareFields($this->item->id);

		// address
		$address = new Main_Address();
		$address = $address->getAddress($this->item->categories_address_id);
		$this->item->address = $address;

		// images
		$images = Main_Image::getInstance();

		$dataImages = $images->getImagesInContent($this->item->id, $where = 'content_id', $extension = 'com_jomdirectory');
		$size = array();
		$size['intro'] = new stdClass();
		$size['intro']->widthBig = $this->params->get('image_main_gallery_width');
		$size['intro']->acBig = $this->params->get('image_main_gallery_format');
		$size['intro']->widthBigger = 1024;
		$size['intro']->acBigger = $this->params->get('image_main_gallery_format');
		$size['intro']->widthSmall = 100;
		$size['intro']->acSmall = '1/1';
		$size['gallery'] = new stdClass();
		$size['gallery']->widthBig = $this->params->get('image_gallery_width');
		$size['gallery']->acBig = $this->params->get('image_gallery_format');
		$size['gallery']->widthBigger = 1024;
		$size['gallery']->acBigger = $this->params->get('image_gallery_format_big');
		$size['gallery']->widthSmall = 100;
		$size['gallery']->acSmall = $this->params->get('image_gallery_format', '1/1');
		$size['logo'] = new stdClass();
		$size['logo']->widthBig = $this->params->get('item_image_logo_width');
		$size['logo']->acBig = false;
		$size['logo']->widthBigger = 1024;
		$size['logo']->acBigger = false;
		$size['logo']->widthSmall = 100;
		$size['logo']->acSmall = false;

		$imagesIn = Main_Image_Helper::galleryWithSize($dataImages, $size);
		$this->item->images = $imagesIn;

		$imagesInU = current($images->getImagesInContent($this->item->users_id, 'users_id', ''));
		if (isset($imagesInU) && !empty($imagesInU)) $this->item->userImage = Main_Image_Helper::img($this->params->get('image_logo_width'), $imagesInU->path . DS . $imagesInU->name);

		$modelProduts = JModelLegacy::getInstance('Products', 'JomdirectoryModel');
		$paramsProducts = new stdClass();
		$paramsProducts->company_id = $this->item->id;
		$paramsProducts->limit = 1000;
		$this->item->products = $modelProduts->getItemsForModule($paramsProducts);
		if (!empty($this->item->products)) {
			$priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), 'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));
			$tabsIds = array();
			foreach ($this->item->products AS $it) {
				$it->price = Main_Price::changeSingle($it->price, $priceParams);
				$it->price_old = Main_Price::changeSingle($it->price_old, $priceParams);
				array_push($tabsIds, $it->id);
			}


			$imagesIn = $images->getImagesInContent($tabsIds, 'content_id', 'com_jomdirectory.products');
			$imgWidth = $this->params->get('image_product_width');
			$imgFormat = $this->params->get('image_product_format');
			$imgLogoWidth = $this->params->get('image_logo_width');

			$productsImages = array();
			if (isset($imagesIn) && !empty($imagesIn)) {
				foreach ($imagesIn AS $img) {
					if ($img->alias == 'com-jomcomdev-type-images-intro') {
						if (!array_key_exists($img->content_id, $productsImages)) $productsImages[$img->content_id] = Main_Image_Helper::img($imgWidth, $img->path . DS . $img->name, $imgFormat);
					}
				}
			}
			foreach ($this->item->products AS $it) {
				if (!empty($productsImages[$it->id])) {
					$it->image = $productsImages[$it->id];
				}
			}
		}

		$offset = $this->state->get('list.offset');
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onContentPrepareJd', array('com_jomdirectory.item', &$this));
		$results = $dispatcher->trigger('onContentPrepare', array('com_jomdirectory.item', &$this->item, &$this->params, $offset));

		$this->item->event = new stdClass();
		$results = $dispatcher->trigger('onContentAfterTitle', array('com_jomdirectory.item', &$this->item, &$this->params, $offset));
		$this->item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_jomdirectory.item', &$this->item, &$this->params, $offset));
		$this->item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_jomdirectory.item', &$this->item, &$this->params, $offset));
		$this->item->event->afterDisplayContent = trim(implode("\n", $results));

		if  ( $this->item->webpage && $ret = parse_url($this->item->webpage) ) {
			if ( !isset($ret["scheme"]) ){
				$this->item->webpage = "http://".$this->item->webpage;
			}
		}
		  
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if (JRequest::getVar('m') == 1) {
			$this->addTemplatePath(JPATH_COMPONENT_SITE . DS . 'views' . DS . 'item' . DS . 'tmpl_mobile');
			$this->setLayout('m.' . $this->getLayout());
			JRequest::setVar('tmpl', 'component');
		}

		Main_Log::log($this->item, 'Item from view');

		$table = JTable::getInstance('Statistic', 'JomcomdevTable');
		$table->addViewItemStats($this->item->id, 'com_jomdirectory');

		$document = JFactory::getDocument();
		//JHtml::_('bootstrap.framework');


		//$document->addScript(JURI::base(true) . '/components/com_jomcomdev/javascript/calendar.js');


		if (JRequest::getString('layout')) {
			$layout = JRequest::getString('layout');
		} else {
			$layout = $this->params->get('layout');
		}
		if (!empty($layout)) $this->setLayout($layout);

		$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_item.css');


		$jVerArr = explode('.', JVERSION);
		if ($jVerArr[0] >= '3') {
			$this->item->tags = new JHelperTags;
			$this->item->tags->getItemTags('com_jomdirectory.content', $this->item->id);
		}

		$this->_setMeta($this->item);

		JHtml::_('jquery.framework');

		parent::display($tpl);
	}

	protected function _setMeta($item)
	{
		if (!empty($item->meta_title)) {
			$this->document->setTitle($item->meta_title);
		} else $this->document->setTitle($item->title);
		if (!empty($item->meta_description)) {
			$this->document->setDescription($item->meta_description);
		} else $this->document->setTitle($item->title . ' - ' . $item->categoryTitle);


	}


}

