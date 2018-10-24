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

$fieldSets = $this->form->getFieldsets('params');

foreach ($fieldSets as $name => $fieldSet) :
	$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_JOMDIRECTORY_' . $name . '_FIELDSET_LABEL';
	echo JHtml::_('sliders.panel', JText::_($label), $name . '-options');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">' . $this->escape(JText::_($fieldSet->description)) . '</p>';
	endif;
	?>
    <fieldset class="panelform">
		<?php $hidden_fields = ''; ?>
        <ul class="adminformlist">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<?php if (method_exists($field, 'getCSSClass')) : ?>
                        <li class="<?php echo $field->getCSSClass(); ?>">
					<?php else : ?>
                        <li>
					<?php endif; ?>

					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
                    </li>
				<?php else : $hidden_fields .= $field->input; ?>
				<?php endif; ?>
			<?php endforeach; ?>
        </ul>
		<?php echo $hidden_fields; ?>
    </fieldset>
<?php endforeach; ?>



 
