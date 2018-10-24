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


/**
 * Content Component Category Tree
 *
 * @static
 * @package    Joomla.Site
 * @subpackage    com_jomdirectory
 * @since 1.6
 */
class JomdirectoryCategories extends Joomla_Application_Categories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__cddir_content';
		$options['extension'] = 'jomdirectory';
		$options['field'] = 'categories_id';
		parent::__construct($options);
	}
}

class JomdirectoryJomdirectoryCategories extends Joomla_Application_Categories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__cddir_content';
		$options['extension'] = 'com_jomdirectory.jomdirectory';
		$options['field'] = 'categories_id';
		parent::__construct($options);
	}
}

class JomdirectoryProductsCategories extends Joomla_Application_Categories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__cddir_products';
		$options['extension'] = 'com_jomdirectory.jomdirectory';
		$options['field'] = 'categories_id';
		parent::__construct($options);
	}
}

class JomdirectoryTypeFieldsCategories extends Joomla_Application_Categories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__cddir_fields';
		$options['extension'] = 'fields';
		parent::__construct($options);
	}
}

class JomdirectoryAddressCategories extends Joomla_Application_Categories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__cddir_content';
		$options['extension'] = 'address';
		parent::__construct($options);
	}
}
