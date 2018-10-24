<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<?php defined('_JEXEC') or die; ?>
<form action="<?php echo JRoute::_('index.php?option=com_jomdirectory&layout=default&view=addimage&tmpl=component&id='.(int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate">
    <ul class="adminformlist">
        <li><?php echo $this->form->getLabel('title'); ?>
        <?php echo $this->form->getInput('title'); ?></li>

        <li><?php echo $this->form->getLabel('published'); ?>
        <?php echo $this->form->getInput('published'); ?></li>

        <li><?php echo $this->form->getLabel('file'); ?>
        <?php echo $this->form->getInput('file'); ?></li>
    </ul>

    <div class="clr"></div>
    <?php echo $this->form->getLabel('description'); ?>

    <div class="clr"></div>
    <?php echo $this->form->getInput('description'); ?>    
    <?php echo $this->form->getInput('content_id'); ?>  
    
<a class="toolbar" onclick="Joomla.submitbutton('addimage.apply')" href="#">
<span class="icon-32-apply">
</span>
<?php echo JText::_('COM_JOMDIRECTORY_ADM_SAVE');?>
</a>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>
