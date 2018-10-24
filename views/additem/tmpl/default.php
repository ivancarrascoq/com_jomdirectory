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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'item.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div id="jd-default-wrapper">
<form action="<?php echo JRoute::_('index.php?option=com_jomdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo empty($this->item->id) ? JText::_('COM_JOMDIRECTORY_NEW_ARTICLE') : JText::sprintf('COM_JOMDIRECTORY_EDIT_ARTICLE', $this->item->id); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?></li>

                <li><?php echo $this->form->getLabel('alias'); ?>
                <?php echo $this->form->getInput('alias'); ?></li>

                <li><?php echo $this->form->getLabel('categories_id'); ?>
                <?php echo $this->form->getInput('categories_id'); ?></li>
                
                <li><?php echo $this->form->getLabel('categories_address_id'); ?>
                <?php echo $this->form->getInput('categories_address_id'); ?></li>

                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>

                <li><?php echo $this->form->getLabel('access'); ?>
                <?php echo $this->form->getInput('access'); ?></li>

                <li><?php echo $this->form->getLabel('featured'); ?>
                <?php echo $this->form->getInput('featured'); ?></li>

                <li><?php echo $this->form->getLabel('language'); ?>
                <?php echo $this->form->getInput('language'); ?></li>

                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
            </ul>

            <div class="clr"></div>
            <?php echo $this->form->getLabel('articletext'); ?>
            
            <div class="clr"></div>
            <?php echo $this->form->getInput('articletext'); ?>
        </fieldset>
    </div>
    
    
    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
        <?php // Do not show the publishing options if the edit form is configured not to. ?>
        <?php  //if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JOMDIRECTORY_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('users_id'); ?>
                <?php echo $this->form->getInput('users_id'); ?></li>

                <li><?php echo $this->form->getLabel('date_created'); ?>
                <?php echo $this->form->getInput('date_created'); ?></li>

                <li><?php echo $this->form->getLabel('date_publish'); ?>
                <?php echo $this->form->getInput('date_publish'); ?></li>

                <li><?php echo $this->form->getLabel('date_publish_down'); ?>
                <?php echo $this->form->getInput('date_publish_down'); ?></li>                                       
                
                <li><?php echo $this->form->getLabel('date_modified'); ?>
                <?php echo $this->form->getInput('date_modified'); ?></li>                                       

                <?php if ($this->item->hits) : ?>
                        <li><?php echo $this->form->getLabel('hits'); ?>
                        <?php echo $this->form->getInput('hits'); ?></li>
                <?php endif; ?>

            </ul>
        </fieldset>
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JOMDIRECTORY_FIELDSET_CUSTOM_FIELDS'), 'publishing-details'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('fields'); ?>
                        <?php echo $this->form->getInput('fields'); ?></li>
            </ul>
        </fieldset>
        <?php  //endif; ?>     
        
        <?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
        <fieldset class="panelform">
             <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('meta_title'); ?>
                <?php echo $this->form->getInput('meta_title'); ?></li>

                <li><?php echo $this->form->getLabel('meta_description'); ?>
                <?php echo $this->form->getInput('meta_description'); ?></li>
             </ul>
        </fieldset>
        
        <?php if(isset($this->model->event_display_form)): ?>        
            <?php         
//            $dispatcher = JDispatcher::getInstance();
//            JPluginHelper::importPlugin('content');            
//            $dispatcher->trigger($this->model->event_display_form, array($this->model->getContext(), &$this->item));
            ?>
        <?php endif; ?>               
            
        <?php echo $this->loadTemplate('options'); ?>
        <?php echo $this->loadTemplate('maps'); ?>
        <div class="clr"></div>

        <?php echo JHtml::_('sliders.end'); ?>
        
                                                        
    </div>


	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100 fltlft">
			<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

				<?php echo JHtml::_('sliders.panel', JText::_('COM_JOMDIRECTORY_FIELDSET_RULES'), 'access-rules'); ?>
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>

			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
    
    <div>
        <?php echo $this->form->getInput('asset_id'); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>

    
</form>
</div>