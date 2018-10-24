<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2013 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;
?>

<?php echo JHtml::_('sliders.panel', JText::_('COM_JOMDIRECTORY_FIELDSET_MAPS'), 'maps'); ?>
<fieldset class="panelform">
    <ul class="adminformlist">
        <?php $hidden_fields = ''; ?>
        <?php foreach ($this->form->getFieldset('maps') as $field) : ?>
            <?php if (!$field->hidden && $field->id!='jform_maps') : ?>
            <?php if (method_exists($field, 'getCSSClass')) : ?>
            <li class="<?php echo $field->getCSSClass(); ?>">
            <?php else : ?>
            <li>
            <?php endif; ?>

                    <?php echo $field->label; ?>
                    <?php echo $field->input; ?>
            </li>
            <?php else : $hidden_fields.= $field->input; ?>
            <?php endif; ?>
            <?php endforeach; ?>
    </ul>
    <?php echo $this->form->getInput('maps'); ?>
    <?php echo $hidden_fields; ?>
</fieldset>



 
