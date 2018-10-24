<?php

/* ------------------------------------------------------------------------
  # com_jomdirectory - JomDirectory
  # ------------------------------------------------------------------------
  # author    Comdev
  # copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://comdev.eu
  ------------------------------------------------------------------------ */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jomcomdev' . DS . 'tables' . DS . 'category.php';

/**
 * Jomdirectory Component Category Model
 *
 * @package    Joomla.Administrator
 * @subpackage    com_jomdirectory
 * @copyright    Copyright (C) 2012 Comdev. All rights reserved.
 */
class JomdirectoryModelProducts extends JModelList
{

	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JOMDIRECTORY';

	public function getItemsForMap()
	{
		$app = JFactory::getApplication();
		// Get a storage key.
		$store = $this->getStoreId('getMapJd');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->getListQuery(true);
//		$items = "\n var comdevDataMaps = ".json_encode($this->_getList($query))."; \n";
		$start = $this->getStart();
		$items = $this->_getList($query, $start, $this->getState('list.limit'));

		$params = $this->getState('params');
//                $page = $params->get('listing_per_page', 10);
//                $page = $this->getState('list.limit',10);

		foreach ($items AS $key => $i) {
//                    $i->title = htmlspecialchars($i->title, ENT_QUOTES);

			$i->title = strip_tags($i->title);
			$i->title = preg_replace('/["\']/', '', $i->title);
			$i->title = str_replace(array("\r", "\n"), '', $i->title);
			$i->number = $start + $key + 1;
//                    $siteNr = ceil($i->number/$page);
//                    if($siteNr==1 && $page!=1) $siteNr = 0;
//                    $sort = ($siteNr-1)*$page;
//                    if($sort<0) $sort = 0;
//                    $i->link = trim(JURI::base(),'/').JRoute::_(JomdirectoryHelperRoute::getCategoryRoute($app->input->getCmd('categories_id', ''),$sort)).'#itemJc'.$i->number;
			$i->link = '';
			if ($start) $i->link .= JUri::current() . '?start=' . $start;
			$i->link .= '#itemJc' . $i->number;
		}
//                echo '<pre>';
//                echo "------------- DEBUG AJ --------------\n";
//                echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//                print_r($items);
//                echo '</pre>';
//exit;
		$items = json_encode($items);

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery($forMaps = false)
	{


		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$params = $this->getState('params');

		$table = JTable::getInstance('Statistic', 'JomcomdevTable');


		// Select the required fields from the table.
		if (!$forMaps) {
			$query->select($this->getState('list.select', 'a.id AS id, a.title AS title, a.alias AS alias, a.users_id, a.featured, a.fulladdress, ' . 'a.sku, a.tax, a.quantity, ' . 'a.published AS published, a.categories_address_id, a.categories_id AS categories_id, a.introtext, ' . 'a.date_publish AS date_publish,a.price,a.price_old, a.date_publish_down AS date_publish_down, l.view_item'));
			$query->select('c.title AS category_title, c.color AS category_color, c.parent_id AS category_parent_id, cp.title AS category_parent_title');
			$query->select('COUNT(d.id) AS rateHow, SUM(d.rate) AS rateSum, SUM(d.rate)/COUNT(d.id) as rateSort');
			$query->select('u.name AS username');
		} else {
			$query->select($this->getState('list.select', 'a.id AS id, a.title AS title, a.alias AS alias, a.users_id, a.featured, a.categories_id AS categories_id, ' . 'a.maps_lat, a.maps_lng'));
			$query->select('COUNT(d.id) AS rateHow, SUM(d.rate) AS rateSum, SUM(d.rate)/COUNT(d.id) as rateSort');

//                    $query->where("a.maps_lat!='0'");
//                    $query->where("a.maps_lat IS NOT NULL");
//                    $query->where("a.maps_lng!='0'");
//                    $query->where("a.maps_lng IS NOT NULL");
		}
		$query->from($db->quoteName('#__cddir_products') . ' AS a');

		// Join over the language
//		$query->select('l.title AS language_title');
//		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');
		// Join over the categories.
		$query->join('LEFT', '#__cddir_categories AS c ON c.id = a.categories_id');
		$query->join('LEFT', '#__cddir_categories AS cp ON c.parent_id = cp.id');
		$query->join('LEFT', '#__cddir_categories AS e ON e.id = a.categories_address_id');

		$query->join('LEFT', '#__cddir_reviews AS d ON (a.id = d.content_id AND d.published=1 AND d.approved=1 AND d.extension = "com_jomdirectory.products")');

		$query->join('LEFT', '#__users AS u ON u.id = a.users_id');

		$query->select('ff.title AS titleComapny');
		$query->join('LEFT', '#__cddir_content AS ff ON ff.id = a.company_id');

		$query->join('LEFT', '#__cddir_statistic AS l ON (l.item_id = a.id AND l.extension=\'com_jomdirectory\')');


		// Filter by published published
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int)$published);
		}
		$query->where('a.approved = 1');

		// date modiefie
		$date = JFactory::getDate();
		$now = $date->toSql();
		$query->where('(a.date_publish <= ' . $this->_db->Quote($now) . ' OR a.date_publish IS NULL)');
		$query->where('(a.date_publish_down >= ' . $this->_db->Quote($now) . ' OR a.date_publish_down IS NULL)');

		$catRequest = JRequest::getInt('categories_id');
		if (!empty($catRequest)) {
			$app = JFactory::getApplication();
			$app->setUserState($this->context . '.list.save', false);
			$app->setUserState($this->context . '.list.compare', false);

		}

		$priceFrom = $this->getState('filter.jd-mod-price_from', false);
		$priceTo = $this->getState('filter.jd-mod-price_to', false);
		if ($priceFrom || $priceTo) {
			if ($priceFrom) $query->having('MIN(a.price) >= ' . $priceFrom);
			if ($priceTo) $query->having('MIN(a.price) <= ' . $priceTo);
		}


		$saved = $this->getUserStateFromRequest($this->context . '.list.save', 'save');
		$savedData = JomcomdevHelperRemember::getFavorite('com_jomdirectory_products');

		if ($saved && !empty($savedData)) {
			$query->where('a.id IN (' . implode(',', $savedData) . ')');
			$this->setState('filter.categories_id', false);
		} elseif ($saved && empty($savedData)) {
			$query->where('1=0');
		}

		$compare = $this->getUserStateFromRequest($this->context . '.list.compare', 'save');
		$compareData = JomcomdevHelperRemember::getFavorite('com_jomdirectory_products_compare');

		if ($compare && !empty($compareData)) {
			$query->where('a.id IN (' . implode(',', $compareData) . ')');
			$this->setState('filter.categories_id', false);
		} elseif ($compare && empty($compareData)) {
			$query->where('1=0');
		}


		$searchLatLng = false;
		$jcItemLat = $this->getState('filter.latitude');
		$jcItemLng = $this->getState('filter.longitude');
		$miles = $this->getState('filter.distancew');
		if (!empty($jcItemLat) && !empty($jcItemLng)) {
			$searchLatLng = true;
//            $milesOrKm = $params->get('type_of_radius', 0);
//            if($milesOrKm) {
//                $miles = $miles*1.609344;
//            }
			$query->select("(3959 * acos(cos(radians('" . $jcItemLat . "')) * cos(radians(a.maps_lat)) * cos( radians(a.maps_lng) - radians('" . $jcItemLng . "')) + sin(radians('" . $jcItemLat . "')) * sin(radians(a.maps_lat)))) AS distance");
			$query->having("distance < " . $miles);
		}

		// Filter by category.
		$categoryId = $this->getState('filter.categories_id');


		if (is_numeric($categoryId) && $categoryId != 0) {
			if (!$forMaps) $table->addViewItemStats($categoryId, 'com_jomdirectory.category');

			$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int)$cat_tbl->level;
			$query->where('c.lft >= ' . (int)$lft);
			$query->where('c.rgt <= ' . (int)$rgt);
		}
		if (is_array($categoryId) && !empty($categoryId)) {
			$query->where('c.id IN  (' . implode(',', $categoryId) . ')');
		}

		$addressId = $this->getState('filter.categories_address_id');
		if (is_numeric($addressId) && $addressId != 0) {

			if (!$forMaps) $table->addViewItemStats($addressId, 'com_jomdirectory.location');

			$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
			$cat_tbl->load($addressId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int)$cat_tbl->level;
			$query->where('e.lft >= ' . (int)$lft);
			$query->where('e.rgt <= ' . (int)$rgt);
//			$query->where('a.categories_id = '.(int) $categoryId);
		}

		$authorId = $this->getState('filter.users_id');
		if (is_numeric($authorId)) {
			$type = $this->getState('filter.users_id.include', true) ? '= ' : '<>';
			$query->where('a.users_id ' . $type . (int)$authorId);
		}

		// Filter by client.
//		$clientId = $this->getState('filter.client_id');
//		if (is_numeric($clientId)) {
//			$query->where('a.cid = '.(int) $clientId);
//		}
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int)substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');

				if ($params->get('enable_shearch_tags', 1)) {
					$query->join('LEFT', "#__contentitem_tag_map AS tagm ON (a.id=tagm.content_item_id AND type_alias='com_jomdirectory.products')");
					$query->join('LEFT', '#__tags AS tag ON tag.id=tagm.tag_id ');
					//                            $query->group('tag.id');
					$query->select('GROUP_CONCAT(DISTINCT tag.title, \' \') AS tagsconcat');
					$query->having('( tagsconcat LIKE ' . $search . ' OR a.title LIKE ' . $search . ' OR ff.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
					//                            $searchTag = $db->Quote('%'.$db->escape($search, true).'%');
					//                            $query->where(' (GROUP_CONCAT(DISTINCT tag.title) LIKE '.$searchTag.')');
//                                    $query->where('tagsconcat LIKE '.$search.'');
//                                    $query->where(' HAVING tagsconcat LIKE '.$search.'');
					//                $query->where("type_alias='com_jomdirectory.content'");
				} else {
					$query->where('(a.title LIKE ' . $search . ' OR ff.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
				}
			}
		}
		// Filter by search in title
		$searchAddress = $this->getState('filter.address');
		if (!empty($searchAddress)) {
			$searchAddress = $db->Quote('%' . $db->escape($searchAddress, true) . '%');
			$query->where('(e.path LIKE ' . $searchAddress . ' OR a.fulladdress LIKE ' . $searchAddress . ')');
		}

		$user = JFactory::getUser();
		$aclgroups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('a.access IN (' . $aclgroups . ')')->where('c.access IN (' . $aclgroups . ')');

		$premiunOnTop = $this->getState('list.premium_on_top');
		if ($premiunOnTop) {
			$premiunOnTopAdd = 'a.featured DESC, ';
		} else
			$premiunOnTopAdd = '';
		$sort = $this->getState('list.sort');

		switch ($sort) {
			case 'viewed':
				$query->order($db->escape('l.view_item DESC, a.date_publish DESC'));
				break;
			case 'alfa':
				$query->order($db->escape($premiunOnTopAdd . 'a.title, a.date_publish DESC'));
				break;
			case 'updated':
				$query->order($db->escape($premiunOnTopAdd . 'a.date_modified DESC'));
				break;
			case 'rated_desc':
				$query->order($db->escape($premiunOnTopAdd . 'rateSort DESC, rateHow DESC, a.date_publish DESC'));
				break;
			case 'rated_asc':
				$query->order($db->escape($premiunOnTopAdd . 'rateSort ASC, rateHow DESC, a.date_publish DESC'));
				break;

			case 'latest':
			default:
				$query->order($db->escape($premiunOnTopAdd . 'a.date_publish DESC'));
		}
//if(!$forMaps)        echo str_replace('#__', 'bgnqs_', $query);
//if(!$forMaps)        echo str_replace('#__', 'jos_', $query);
//        exit;


		// Filter on the language.
//		if ($language = $this->getState('filter.language')) {
//			$query->where('a.language = ' . $db->quote($language));
//		}
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');


		$fields = new Main_Fields(false, 'com_jomdirectory.products', $categoryId);
		$filterFields = $this->getState('filter.fields');

		$fields->generateSearchQuery($filterFields, $query);

		$query->group($db->escape('a.id'));


//        echo str_replace('#__', 'jos_', $query);
//        echo str_replace('#__', 'qbzsq_', $query);

		return $query;
	}

	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app = JFactory::getApplication();
		$method = $app->input->getMethod();

		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = JRequest::getVar($request, $old_state, 'default', $type);


//                if($key==$this->context.'.filter.categories_id') {
//                    $new_state = JRequest::getVar($request, '', 'default', $type);
//                }
		if ($key == $this->context . '.list.limitstart') {
			$new_state = JRequest::getVar($request, 0, 'default', $type);
		}
		if ($method == 'POST' && $key == $this->context . '.filter.fields') {
			$new_state = JRequest::getVar($request, false, 'default', $type);

		}

		if ($key == $this->context . '.filter.categories_id') {

			$lm = JRequest::getVar('start');
			if (!empty($lm)) {
				$new_state = null;
			} else {
				$new_state = JRequest::getVar($request, 0, 'default', $type);
				if ($new_state == 0) $new_state = null;

				if ($method == 'POST' && $new_state == '') {
					$new_state = '';
				}
			}

		}

//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//        echo "\n".$key.' - '.$old_state.' - '.$cur_state.' - '.$new_state.' - '.$this->context.'filter.categories_id';
//        print_r($_REQUEST);
//        echo '</pre>';

		if (($cur_state != $new_state) && ($resetPage)) {
			JRequest::setVar('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null) {
			$app->setUserState($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

	public function getItemsForModule($params)
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS id, a.title AS title, a.alias, a.users_id, a.featured, a.introtext, a.price, a.company_id, a.price_old, ' . 'c.id AS categoryId, c.title AS categoryTitle, c.alias AS categoryAlias, d.view_item');

		$query->from($db->quoteName('#__cddir_products') . ' AS a');

		// Join over the language
//		$query->select('l.title AS language_title');
//		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');
//
		// Join over the categories.
		$query->join('LEFT', '#__cddir_categories AS c ON c.id = a.categories_id');
		$query->join('LEFT', '#__cddir_statistic AS d ON (d.item_id = a.id AND d.extension=\'com_jomdirectory.products\')');

		$query->where('a.published = 1');
		$query->where('a.approved = 1');
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');

		if (isset($params->featured) && $params->featured) $query->where('a.featured = 1');

		if (isset($params->id) && $params->id) {
			$query->where('a.id IN (' . $params->id . ')');
		}
		if (isset($params->company_id) && $params->company_id) {
			$query->where('a.company_id =' . $params->company_id);
		}
		if (isset($params->not_id) && $params->not_id) {
			$query->where('a.id !=' . $params->not_id);
		}

		// Filter by category.
		if (isset($params->category)) {
			$categoryId = $params->category;
			if (is_numeric($categoryId) && $categoryId != 0) {

				$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
				$cat_tbl->load($categoryId);
				$rgt = $cat_tbl->rgt;
				$lft = $cat_tbl->lft;
				$query->where('c.lft >= ' . (int)$lft);
				$query->where('c.rgt <= ' . (int)$rgt);
			}
		}

		if (isset($params->sort)) {
			$sort = $params->sort;
		} else $sort = '';
		switch ($sort) {
			case 'most_viewed':
				$query->order($db->escape('d.view_item DESC, a.date_publish DESC'));
				break;
			case 'alfa':
				$query->order($db->escape('a.title, a.date_publish DESC'));
				break;
			case 'updated':
				$query->order($db->escape('a.date_modified DESC'));
				break;
			case 'rated_desc':
				$query->order($db->escape('rateSort DESC, rateHow DESC, a.date_publish DESC'));
				break;
			case 'rated_asc':
				$query->order($db->escape('rateSort ASC, rateHow DESC, a.date_publish DESC'));
				break;
			case 'compare':
				$query->order($db->escape('c.title ASC, a.title ASC'));
				break;

			case 'latest':
			default:
				$query->order($db->escape('a.date_publish DESC'));
		}

		$query->group($db->escape('a.id'));

//        $query = sprintf("SELECT address, name, lat, lng, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance "
//                . "FROM markers "
//                . "HAVING distance < '%s' "
//                . "ORDER BY distance LIMIT 0 , 20",
//  $db->escape($params->latitude),
//  $db->escape($params->longitude),
//  $db->escape($params->latitude),
//  $db->escape($params->distance));
//        $query->select(sprintf("( 3959 * acos( cos( radians('%s') ) * cos( radians( a.maps_lat ) ) * cos( radians( a.maps_lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( a.maps_lat ) ) ) ) AS distance",
//              $db->escape($params->latitude),
//              $db->escape($params->longitude),
//              $db->escape($params->latitude))
//        );
//
//
//        echo str_replace('#__', 'qbzsq_', $query);
//        exit;

		if (isset($params->limit)) {
			$limit = $params->limit;
		} else $limit = 10;
		$db->setQuery($query, 0, $limit);
		$items = $db->loadObjectList();

		return $items;
	}

	public function getItemsForMapModule($params)
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$this->params = $this->getState('params');


//        $query->select(
//            'a.id AS id, a.title AS title, a.alias, a.users_id, a.featured, a.introtext, a.categories_address_id, '.
//            'c.id AS categoryId, c.title AS categoryTitle, c.alias AS categoryAlias, d.view_item'
//        );


		$query->select('a.id AS id, a.title AS title, a.price,  a.alias AS alias, a.users_id, a.featured, a.fulladdress, a.introtext, ' . 'a.published AS published, a.categories_address_id, a.categories_id AS categories_id,  ' . 'a.date_publish AS date_publish, a.date_publish_down AS date_publish_down, d.view_item, a.maps_lat, a.maps_lng');
		$query->select('c.title AS category_title, c.alias AS category_alias, c.color AS category_color');
//        $query->select('COUNT(d.id) AS rateHow, SUM(d.rate) AS rateSum, SUM(d.rate)/COUNT(d.id) as rateSort');
//        $query->select('u.name AS username');


		$query->from($db->quoteName('#__cddir_products') . ' AS a');

		// Join over the language
//		$query->select('l.title AS language_title');
//		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');
//
		// Join over the categories.
		$query->join('LEFT', '#__cddir_categories AS c ON c.id = a.categories_id');
		$query->join('LEFT', '#__cddir_statistic AS d ON (d.item_id = a.id AND d.extension=\'com_jomdirectory\')');
		$query->join('LEFT', '#__cddir_categories AS e ON e.id = a.categories_address_id');
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');

		$query->where('a.published = 1');
		$query->where('a.approved = 1');
		$query->where("a.maps_lat!=''");
		$query->where("a.maps_lng!=''");

		if (!empty($params->featured)) {
			if ($params->featured) $query->where('a.featured = 1');
		}

		if (!empty($params->favorites)) {
			if ($params->favorites) {
				$savedData = JomcomdevHelperRemember::getFavorite('com_jomdirectory');
				if (empty($savedData)) {
					$query->where('0=1');
				} else $query->where('a.id IN (' . implode(',', $savedData) . ')');

			}
		}

		if (!empty($params->category)) {
			// Filter by category.
			$categoryId = $params->category;
			if (is_numeric($categoryId) && $categoryId != 0) {
				$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
				$cat_tbl->load($categoryId);
				$rgt = $cat_tbl->rgt;
				$lft = $cat_tbl->lft;
				$query->where('c.lft >= ' . (int)$lft);
				$query->where('c.rgt <= ' . (int)$rgt);
			}
			if (is_array($categoryId) && !empty($categoryId)) {
				$query->where('c.id IN  (' . implode(',', $categoryId) . ')');
			}
		}

		if (!empty($params->categories_address_id)) {
			$addressId = $params->categories_address_id;
			if (is_numeric($addressId) && $addressId != 0) {
				$cat_tbl = JTable::getInstance('Category', 'JomcomdevTable');
				$cat_tbl->load($addressId);
				$rgt = $cat_tbl->rgt;
				$lft = $cat_tbl->lft;
//                $baselevel = (int) $cat_tbl->level;
				$query->where('e.lft >= ' . (int)$lft);
				$query->where('e.rgt <= ' . (int)$rgt);
				//			$query->where('a.categories_id = '.(int) $categoryId);
			}
		}

		if (!empty($params->search)) {
			$search = $db->Quote('%' . $db->escape($params->search, true) . '%');
			$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
		}
		if (!empty($params->tags)) {
			$query->join('LEFT', "#__contentitem_tag_map AS tagm ON (a.id=tagm.content_item_id AND type_alias='com_jomdirectory.content')");
//                    $query->join('LEFT', '#__tags AS tag ON tag.id=tagm.tag_id ');
			$query->where('tagm.tag_id=' . $params->tags);
		}

		$sort = '';
		if (!empty($params->sort)) $sort = $params->sort;
		switch ($sort) {
			case 'most_viewed':
				$query->order($db->escape('d.view_item DESC, a.date_publish DESC'));
				break;
			case 'alfa':
				$query->order($db->escape('a.title, a.date_publish DESC'));
				break;
			case 'updated':
				$query->order($db->escape('a.date_modified DESC'));
				break;
			case 'rated_desc':
				$query->order($db->escape('rateSort DESC, rateHow DESC, a.date_publish DESC'));
				break;
			case 'rated_asc':
				$query->order($db->escape('rateSort ASC, rateHow DESC, a.date_publish DESC'));
				break;

			case 'latest':
			default:
				$query->order($db->escape('a.date_publish DESC'));
		}

		$query->group($db->escape('a.id'));

		if (!empty($params->latitude) && !empty($params->longitude) && !empty($params->distance)) {
			$query->select(sprintf("( 3959 * 1.609344 * acos( cos( radians('%s') ) * cos( radians( a.maps_lat ) ) * cos( radians( a.maps_lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( a.maps_lat ) ) ) ) AS distance ", $db->escape($params->latitude), $db->escape($params->longitude), $db->escape($params->latitude)));
			$query->having('distance < ' . $db->escape($params->distance)); //1.609344 - kilometers
		}

//        echo str_replace('#__', 'qbzsq_', $query);
//        exit;

		if (empty($params->limit)) $params->limit = false;
		$db->setQuery($query, 0, $params->limit);
		$items = $db->loadObjectList();

		if (empty($items)) return false;


		$tabsId = array();
		foreach ($items AS $i) $tabsId[] = $i->id;

		if (!empty($tabsId)) {
			$imagesModel = Main_Image::getInstance();
			$imagesIn = $imagesModel->getImagesInContent($tabsId, 'content_id', 'com_jomdirectory.products');
			if (isset($imagesIn) && !empty($imagesIn)) {
				$images = array();
				foreach ($imagesIn AS $img) {
					if (!array_key_exists($img->content_id, $images) && $img->alias = 'com-jomdirectory-type-images-intro') $images[$img->content_id] = Main_Image_Helper::img(100, $img->path . DS . $img->name, '1/1');
				}
			}
		}

		$priceParams = array('currency' => $this->params->get('adm_currency'), 'currency_position' => $this->params->get('currency_position', 1), 'number_format' => $this->params->get('number_format', ''), 'currency_rest_separator' => $this->params->get('currency_rest_separator', ' '), //                             'currency_type'=>$this->params->get('currency_type',2),
			'decimal_digits' => $this->params->get('decimal_digits', 2), 'tax' => $this->params->get('currency_brutto', false));

		foreach ($items AS $key => $i) {

			$imageMarker = JURI::root() . 'modules/mod_jomdirectory_maps/images/markers/marker_default.png';
//            echo $imageMarker = JURI::root().'modules/mod_jomdirectory_maps/images/markers/marker_'.$i->category_alias.'.png';
			if (file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod_jomdirectory_maps' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'markers' . DIRECTORY_SEPARATOR . 'marker_' . $i->category_alias . '.png')) {
				$imageMarker = JURI::root() . 'modules/mod_jomdirectory_maps/images/markers/marker_' . $i->category_alias . '.png';
			}
//            $i->title = addslashes($i->title);
//            $i->title = htmlentities($i->title, ENT_QUOTES);
//            $i->introtext = htmlentities(strip_tags($i->introtext), ENT_QUOTES);
//            $i->introtext = preg_replace('@[\s]{2,}@',' ', strip_tags($i->introtext));
//            $i->title = htmlentities($i->title, ENT_QUOTES);

			$i->title = strip_tags($i->title);
			$i->title = preg_replace('/["\']/', '', $i->title);
			$i->title = str_replace(array("\r", "\n"), '', $i->title);

			$i->introtext = strip_tags($i->introtext);
			$i->introtext = preg_replace('/["\']/', '', $i->introtext);
			$i->introtext = str_replace(array("\r", "\n"), '', $i->introtext);
			$i->introtext = Main_Text::minimalize($i->introtext, 100);

//            $i->category_title = preg_replace ('/["\']/', '', $i->category_title);

			$i->category_title = strip_tags($i->category_title);
			$i->category_title = preg_replace('/["\']/', '', $i->category_title);
			$i->category_title = str_replace(array("\r", "\n"), '', $i->category_title);

			$i->fulladdress = strip_tags($i->fulladdress);
			$i->fulladdress = preg_replace('/["\']/', '', $i->fulladdress);
			$i->fulladdress = str_replace(array("\r", "\n"), '', $i->fulladdress);

			$i->price = Main_Price::changeSingle($i->price, $priceParams);
//            $i->fulladdress = htmlentities(strip_tags($i->fulladdress), ENT_QUOTES);
//            $i->fulladdress = preg_replace('@[\s]{2,}@',' ', strip_tags($i->fulladdress));

			if (!empty($images[$i->id])) $i->image = $images[$i->id]; else
				$i->image = false;
//            $i->title = str_replace('\'', "\'", $i->title);
			$i->markerImage = $imageMarker;
			//$i->link = trim(JURI::base(),'/').JRoute::_(JomdirectoryHelperRoute::getArticleRoute($i->id, $i->alias, $i->categories_id));
			$i->link = trim(JURI::base(), '/') . JRoute::_(JomdirectoryHelperRoute::getProductRoute($i->id, $i->alias, $i->categories_id));
		}

//        echo '<pre>';
//        echo "------------- DEBUG AJ --------------\n";
//        echo __FILE__ . "\n" . __METHOD__ . " - Line: " . __LINE__ . "\n";
//        echo "\n";
//        print_r($items);
//        echo '</pre>';
//        exit;
//		$items = addslashes(json_encode($items));
//		$items = htmlentities(json_encode($items), ENT_QUOTES);
		$items = json_encode($items);
//        echo $items;
//        exit;
		return $items;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{

		// Initialise variables.
		$app = JFactory::getApplication('site');

		// Load the parameters.
//            $params = $app->getParams();
		$params = JComponentHelper::getParams('com_jomdirectory');

		$menu = $app->getMenu();
		$this->active = $menu->getActive();
		if ($this->active) {
			$menuParams = $this->active->params;
			$global = $menuParams->get('global_option', 1);
			$paramsa = $menuParams->toArray();
			$paramsb = $params->toArray();
			if (!$global) {
				foreach ($paramsa AS $key => $p) $paramsb[$key] = $p;
				$newObject = (object)$paramsb;
				$newObject->activeItemid = $this->active->id;
				$params->loadObject($newObject);
			} else {
				$dataParams = array_merge($paramsa, $paramsb);
				$newObject = (object)$dataParams;
			}
			$params->loadObject($newObject);
		}


		$this->setState('params', $params);
		$this->setState('list.premium_on_top', $params->get('premium_on_top'));


		$limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'jdItemsPerPage', $params->get('listing_per_page'), 'uint', false);
		$this->setState('list.limit', $limit);

		$fields = $this->getUserStateFromRequest($this->context . '.filter.fields', 'jdfields', '');
		$this->setState('filter.fields', $fields);

		$value = $this->getUserStateFromRequest($this->context . '.list.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		$jcItemLatInfo = $this->getUserStateFromRequest($this->context . '.filter.address-lat-lng', 'address-lat-lng', '');
		$this->setState('filter.address-lat-lng', $jcItemLatInfo);
		$jcItemLat = $this->getUserStateFromRequest($this->context . '.filter.latitude', 'latitude', '');
		$this->setState('filter.latitude', $jcItemLat);
		$jcItemLng = $this->getUserStateFromRequest($this->context . '.filter.longitude', 'longitude', '');
		$this->setState('filter.longitude', $jcItemLng);
		$jcItemDistance = $this->getUserStateFromRequest($this->context . '.filter.distancew', 'distancew', '');
		$this->setState('filter.distancew', $jcItemDistance);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'search', '', 'string');
		$this->setState('filter.search', $search);

		$searchAddress = $this->getUserStateFromRequest($this->context . '.filter.address', 'address', '', 'string');
		$this->setState('filter.address', $searchAddress);

		$categories_address_id = $this->getUserStateFromRequest($this->context . '.filter.categories_address_id', 'categories_address_id', '', 'string');
		$this->setState('filter.categories_address_id', $categories_address_id);

		$published = $this->getUserStateFromRequest($this->context . '.filter_published', 'filter_published', '1', 'int');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.categories_id', 'categories_id', '', 'RAW');
		$this->setState('filter.categories_id', $categoryId);

		$jd_mod_price = $this->getUserStateFromRequest($this->context . '.filter.jd_mod_price', 'jd_mod_price', '', 'STRING');
		if (!empty($jd_mod_price)) {
			list($priceFrom, $priceTo) = explode(';', $jd_mod_price);
		} else {
			$priceFrom = $this->getUserStateFromRequest($this->context . '.filter.jd-mod-price_from', 'jd-mod-price_from', null);
			$priceTo = $this->getUserStateFromRequest($this->context . '.filter.jd-mod-price_to', 'jd-mod-price_to', null);
		}
		$this->setState('filter.jd-mod-price_from', $priceFrom);
		$this->setState('filter.jd-mod-price_to', $priceTo);

		$sort = $this->getUserStateFromRequest($this->context . '.list.sort', 'jdItemsSort', false, 'string');
		if (!$sort) $sort = $params->get('listing_layout', 'latest');
		$this->setState('list.sort', $sort);

//        $method = $app->input->getMethod();
//        if ($method == 'POST') {
//            $uri = JUri::current();
//            $app->redirect($uri);
//        }
	}

}
