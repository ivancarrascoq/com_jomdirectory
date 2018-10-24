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
class JomdirectoryViewCart extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		$this->params = JComponentHelper::getParams('com_jomdirectory');

		$modJdCart = Main_Modules::isInstalled('mod_jomdirectory_cart');
		if (!$modJdCart) {
			JError::raiseError(500, "Cart is not install");
		}

		$app = JFactory::getApplication();
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}


		$remember = Main_Remember::getInstance('cart');
		$remember->setAdapter(new Main_Remember_Cookie(true));
		$remember->setTime('6000');

		$this->cart = new Main_Shop_Cart($remember);

		$list = $this->cart->getList();
		$idsService = array();
		$ids = array();
		foreach ($list as $l) {
			if ($l->type == 'service') {
				$params = json_decode($l->params);
				array_push($idsService, $params->id);
			} else {
				array_push($ids, $l->id);

			}
		}

//            $ids = $this->cart->getIds();

		if (!empty($ids) || !empty($idsService)) {
			if (!empty($ids)) {
				$model = JModelLegacy::getInstance('Products', 'JomdirectoryModel');

				$params = new stdClass();
				$params->id = implode(',', $ids);
				$this->items = $model->getItemsForModule($params);

			}
			if (!empty($idsService)) {
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->from("#__cddir_service AS c");
				$query->select('c.*');
				$query->where("c.id IN (" . implode(',', $idsService) . ")");
				$db->setQuery($query);

				$this->items = $db->loadObjectList();


			}

		} else {
			$this->items = false;
			if ($this->getLayout() != 'default' && $this->getLayout() != 'finish') {
				$url = JRoute::_(JomdirectoryHelperRoute::getCartRoute());
				$app->redirect($url);
				return false;
			}
		}
		if ($this->getLayout() == 'finish') {
			$articleId = $this->params->get('cart_article_finish');
			$db = JFactory::getDbo();
			$query = "SELECT * FROM #__content WHERE id = " . intval($articleId[0]);
			$db->setQuery($query);
			$this->article = $db->loadObject();
		}


		$this->priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), //                             'currency_type'=>$this->params->get('currency_type',2),
			'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));

		$field = new Main_FieldsSite(null, 'com_jomdirectory.products', 'allof');
		$fieldIds = $field->getGroupsIDs();

		$fields = $field->getFields($fieldIds["COM_JOMCOMDEV_TYPE_FIELDS_GROUP_SHIPPING"])->getGroup('shipping');

		$shippingCount = array();
		if (!empty($this->items)) {
			foreach ($this->items AS $key => $item) {

				$field = new Main_FieldsSite($item->id, 'com_jomdirectory.products', 'allof');
				$fieldIds = $field->getGroupsIDs();

				$field = $field->getFields($fieldIds["COM_JOMCOMDEV_TYPE_FIELDS_GROUP_SHIPPING"]);
				$fieldsIn = $field->prepareValues($item->id);

				if (!empty($fieldsIn->shipping)) {
					$item->shipping = $fieldsIn->shipping;

					foreach ($item->shipping as $key => $s) {
						if (!array_key_exists($key, $shippingCount)) {
							$shippingCount[$key] = new stdClass();
							$shippingCount[$key]->name = $s->name;
							$shippingCount[$key]->price = $s->value;
						} else {
							$shippingCount[$key]->price = $shippingCount[$key]->price + $s->value;
						}
					}
				}

				$item->priceText = Main_Price::changeSingle($item->price, $this->priceParams);
				$this->oldprices[$item->id] = $item->price_old;
			}
		}

		$this->shipping = array();
		foreach ($fields AS $f) {
			$idShipping = $f->getElementValue('id');
			$this->shipping[$idShipping] = new stdClass();
			$this->shipping[$idShipping]->name = $f->getElementValue('name');
			if (!empty($shippingCount[$idShipping])) {
				$this->shipping[$idShipping]->price = $shippingCount[$idShipping]->price;
				$this->shipping[$idShipping]->price_string = Main_Price::changeSingle($this->shipping[$idShipping]->price, $this->priceParams);

			}
		}
//            echo '<pre>';
//            echo "------------- DEBUG AJ --------------\n";
//            echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//            print_r($this->shipping);
//            echo '</pre>';
//exit;

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true) . '/components/com_jomdirectory/assets/css/jd_cart.css');

		parent::display($tpl);
	}
}
