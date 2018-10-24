<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


$tabsGroup = $this->item->fields->getGroup("tabs");
//echo '<pre>';
//echo '------------- DEBUG --------------';
//echo "";
//print_r($this->item);
//print_r($this->item->images);
//print_r($this->state);
//echo '</pre>';
?>

<script language="javascript">
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1&appId=267794609909013";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>


<!-- Simply example to render image -->
<!-- <img src="<?php echo Main_Image_Helper::img(200, 'bt/9-66-katar_u_dziecI.jpg', '1/1')?>" /> -->


<div id="jd-item-wrapper-m">
    <div class="jd-item-box-main">
        <div class="jd-item-box-header">
            <div class="jd-item-box-header-back">
                <?php echo JHTML::_('image', JURI::root() . 'components/com_jomdirectory/assets/images/arrow_back_ico.png', 'back', '') ?>
                <?php echo JHTML::_('link', JRoute::_('index.php?option=com_jomdirectory&task=item&id=' . $this->item->id), JText::_('COM_JOMDIRECTORY_BACK_TO_SEARCH'), 'class="jd-item-back"') ?>
            </div>
            <div class="jd-item-box-header-social">
                <g:plusone size="medium"></g:plusone>
                <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                <div class="fb-like" data-send="false" data-layout="button_count" data-show-faces="true"></div>
            </div>
            <div class="jd-clear"></div>
        </div> <!--END OF JD-ITEM-BOX-HEADER -->
        <div class="jd-item-box-main-body">
            <div class="jd-item-box-table">
                <div class="jd-item-box-table-cel jd-cel1">
                    <h1 class="jd-item-box-title"><?php echo $this->item->title?></h1>
                    <?php foreach($this->item->images->intro AS $img): ?>
                        <?php echo JHTML::_('image', JURI::root() . $img->src, 'target', '') ?>
                    <?php endforeach; ?>
                    <div class="jd-item-box-location">
                        <?php echo JHTML::_('image', JURI::root() . 'components/com_jomdirectory/assets/images/pin.png', 'target', 'class="jd-item-box-location-img"') ?>
                        <span class="jd-item-box-address"><?php echo implode(', ',$this->item->address)?></span>
                    </div>
                    <div class="jd-fields-container">
                        <?php echo $this->item->fields->getOne("1")->show()?>
                        <?php echo $this->item->fields->showGroup()?>
                    </div>
                </div>
                <div class="jd-clear"></div>
            </div>
            <div class="jd-item-box-footer">
                <div class="jd-item-box-toolbar">
                    <a href="<?php echo JRoute::_('') ?>">
                        <?php echo JHTML::_('image', JURI::root() . 'components/com_jomdirectory/assets/images/bullet_ico.png', 'save', 'class="jd-item-box-bullet-img"') ?>
                        <?php echo JText::_('COM_JOMDIRECTORY_SAVE'); ?>
                    </a>
                    <?php echo JText::_('COM_JOMDIRECTORY_SAVED'); ?>
                    <a href="<?php echo JRoute::_('') ?>">
                        <?php echo JHTML::_('image', JURI::root() . 'components/com_jomdirectory/assets/images/bullet_ico.png', 'save', 'class="jd-item-box-bullet-img"') ?>
                        <?php echo JText::_('COM_JOMDIRECTORY_TAF'); ?>
                    </a>
                    <a href="<?php echo JRoute::_('') ?>">
                        <?php echo JHTML::_('image', JURI::root() . 'components/com_jomdirectory/assets/images/bullet_ico.png', 'save', 'class="jd-item-box-bullet-img"') ?>
                        <?php echo JText::_('COM_JOMDIRECTORY_PRINT'); ?>
                    </a>
                </div>
            </div>
        </div><!--END OF JD-ITEM-BOX-BODY -->
    </div><!--END OF JD-ITEM-BOX-MAIN -->
    
    <div class="jd-item-box jd-tags">
        <div class="jd-item-box-body">
             <label class="jd-item-box-footer-tags"><?php echo JText::_('COM_JOMDIRECTORY_TAGS'); ?> : </label> Cameras, Red, Silver
        </div>
    </div><!--END OF JD-ITEM-BOX --> 
    <div id="jd-item-box-tabs-menu-mobile">
    <div id="jd-item-box-tabs">
        <ul id="jd-item-box-tabs-menu">
            <li class="jd-tab1"><div class="jd-tab-text"><?php echo JText::_('COM_JOMDIRECTORY_TAB_DETAILS'); ?></div></li>
            <li class="jd-tab2"><div class="jd-tab-text"><?php echo JText::_('COM_JOMDIRECTORY_TAB_PHOTOS'); ?>(6)</div></li>
            <li class="jd-tab3"><div class="jd-tab-text"><?php echo JText::_('COM_JOMDIRECTORY_TAB_EVENTS'); ?></div></li>
            <li class="jd-tab4"><div class="jd-tab-text"><?php echo JText::_('COM_JOMDIRECTORY_TAB_FEATURES'); ?></div></li>
            <li class="jd-tab5"><div class="jd-tab-text"><?php echo JText::_('COM_JOMDIRECTORY_TAB_LOCATION'); ?></div></li>
            <li class="jd-tab6"><div class="jd-tab-text"><?php echo JText::_('COM_JOMDIRECTORY_TAB_REVIEWS'); ?>(3)</div></li>
            <?php foreach($tabsGroup AS $t):  ?>
                <li class="jd-tabs <?php echo $t->getElementValue('alias')?>"><div class="jd-tab-text"><?php echo $t->getElementValue('name')?></div></li>
            <?php endforeach; ?>
        <div class="jd-clear"></div>
        </ul>
    </div>
    </div>
    <div class="jd-clear"></div>
    <div class="jd-item-box" id="jd-item-box-tabs">
        <div id="jd-item-box-tabs-body">
            <div id="jd-tab1">
                <p><?php echo $this->item->fulltext?></p>
            </div>
            <div id="jd-tab2">
                <?php foreach($this->item->images->gallery AS $img): ?>
                    <?php echo JHTML::_('image', JURI::root() . $img->src, 'target', '') ?>
                <?php endforeach; ?>                
            </div>
            <div id="jd-tab3">jd-tab3</div>
            <div id="jd-tab4">
                <?php echo $this->item->fields->showGroup("second")?>
            </div>
            <div id="jd-tab5">
                <div id="jd-item-box-maps" style="width: 100%; height: 200px;"></div>
            </div>
            <div id="jd-tab6">jd-tab6</div>
            <?php foreach($tabsGroup AS $t):  ?>
                <div id="<?php echo $t->getElementValue('alias')?>">
                    <?php echo $t->show()?>
                </div>
            <?php endforeach; ?>
        </div>
    </div><!--END OF JD-ITEM-BOX --> 
     <div class="jd-item-box jd-contact">
        <div class="jd-item-box-body">
            <h2 class="jd-item-box-contact-title"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_OWNER');?> 
                <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_OFTHE');?> <?php echo $this->item->title?>
            </h2>
            <form id="jd-email-form" name="jd-email-form" method="post" action="<?php echo JRoute::_('') ?>" class="form-validate">
            <div class="jd-email-error" id="jd-email-error"></div>
                <div class="jd-item-box-table" >
                
                    <ul class="jd-email-inputs">
                        <li>
                                <label class="jd-label"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_NAME');?><span style='color:#ff0000'>*</span></label>
                                <input type="text" maxlength="50" value="" name="inquirerName" id="inquirerName" class="required"/>
                        </li>
                        <li>
                                <label class="jd-label"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_EMAIL');?><span style='color:#ff0000'>*</span></label>
                                <input type="text" maxlength="50" value="" name="inquirerEmail" id="inquirerEmail" class="required validate-email"/>
                        </li> 
                        <li>
                                <label class="jd-label"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_PHONE');?></label>
                                <input type="text" maxlength="21" value="" name="inquirerPhone" id="inquirerPhone" />
                        </li>
                    </ul>
                    <div class="jd-email-message-section">
                        <textarea class="jd-email-message" title="" maxlength="500" rows="6" style="width: 100%"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_MESSAGE');?></textarea>
                        <div class="jd-email-comments-counter">
                                <?php $num1 = 500;$num2 = 3;$num3 = 3; ?>
                                <?php echo JText::sprintf('COM_JOMDIRECTORY_CONTACT_MAX', $num1, $num2); ?>
                        </div>
                    </div>
                   <div class="jd-clear"></div>
                </div>
                <div class="jd-email-contact-footer">
                    <div class="jd-email-button-section">
                    <div class="loading"></div>
                        <div class="jd-email-captcha">
                            Captcha
                            <input type="submit" value="<?php echo JText::_('COM_JOMDIRECTORY_CONTACT_SENDEMAIL');?>" class="jd-button jd-button-save">
                        </div>
                        <div class="jd-clear"></div>
                    </div>
                    <div class="jd-email-terms-container">
                            <div class="jd-email-terms-agree">
                                <input type="checkbox" name="jd-accept-policy" />
                                <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_ACCEPT');?><a href="<?php echo JRoute::_('') ?>"><?php echo JText::_('COM_JOMDIRECTORY_CONTACT_TERMS');?></a>
                                <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_AND');?> <a href="<?php echo JRoute::_('') ?>"> <?php echo JText::_('COM_JOMDIRECTORY_CONTACT_PRIV');?></a>
                            </div>
                    </div>
                </div>
            </form>
	<div class="jd-clear"></div>
        </div>
    </div><!--END OF JD-ITEM-BOX --> 
</div><!--END OF JD-ITEM-WRAPPER -->
