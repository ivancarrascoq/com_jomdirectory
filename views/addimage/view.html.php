<?php/*------------------------------------------------------------------------# com_jomdirectory - JomDirectory# ------------------------------------------------------------------------# author    Comdev# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL# Websites: http://comdev.eu------------------------------------------------------------------------*/// no direct accessdefined( '_JEXEC' ) or die( 'Restricted access' ); jimport('joomla.application.component.view');JLoader::register('JomdirectoryHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/jomdirectory.php');/** * Jomdirectory controller for item edit * * @package	Joomla.Administrator * @subpackage	com_jomdirectory * @copyright	Copyright (C) 2012 Comdev. All rights reserved. */class JomdirectoryViewAddimage extends JViewLegacy{    protected $form;    protected $item;    protected $state;    /**     * Display the view     */    public function display($tpl = null)    {            // Initialiase variables.            $this->form	= $this->get('Form');            $this->item	= $this->get('Item');            $this->state	= $this->get('State');//            $this->canDo	= JomdirectoryHelper::getActions($this->state->get('filter.category_id'));//        echo '<pre>';//        echo '------------- DEBUG --------------';//        echo $this->state->get('filter.category_id');//        print_r($this->canDo);//        print_r($this->state);//        echo '</pre>';            // Check for errors.//            if (count($errors = $this->get('Errors'))) {//                    JError::raiseError(500, implode("\n", $errors));//                    return false;//            }//            $this->addToolbar();            parent::display($tpl);    }    /**     * Add the page title and toolbar.     *     * @since	1.6     */    protected function addToolbar()    {//        JRequest::setVar('hidemainmenu', true);//        $user		= JFactory::getUser();//        $userId		= $user->get('id');//        $isNew		= ($this->item->id == 0);        // Since we don't track these assets at the item level, use the category id.//        $canDo		= JomdirectoryHelper::getActions($this->state->get('filter.category_id'), $this->item->id);//        JToolBarHelper::title(JText::_('COM_JOMDIRECTORY_PAGE_'.($isNew ? 'ADD_ARTICLE' : 'EDIT_ARTICLE')), 'article-add.png');        // If not checked out, can save the item.//        if (($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_jomdirectory', 'core.create')) > 0)) {                JToolBarHelper::apply('item.apply');                JToolBarHelper::save('item.save');//            if ($canDo->get('core.create')) {//                    JToolBarHelper::save2new('item.save2new');//            }//        }        // If an existing item, can save to a copy.//        if (!$isNew && $canDo->get('core.create')) {//                JToolBarHelper::save2copy('item.save2copy');//        }////        if (empty($this->item->id))  {//                JToolBarHelper::cancel('item.cancel');//        }//        else {//                JToolBarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');//        }////        JToolBarHelper::divider();//        JToolBarHelper::help('');    }}