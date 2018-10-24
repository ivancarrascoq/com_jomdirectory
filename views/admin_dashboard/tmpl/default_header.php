<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
JHtmlBehavior::framework('more');
$params = JComponentHelper::getParams('com_jomdirectory');
$view = JFactory::getApplication()->input->get('view');
$user = JFactory::getUser()->name;
$plan_limits = Main_FrontAdmin::getPlanLimits(JFactory::getUser()->id, 'com_jomdirectory');
$paid_fields = json_decode($plan_limits->paid_fields, true);

$lang = JFactory::getLanguage();
$locales = $lang->getLocale();
$dlang = JFactory::getLanguage($lang->getDefault());
$dlocales = $dlang->getLocale();
if ($dlocales[4] != $locales[4]) $linkpostfix = "-" . $locales[4]; else $linkpostfix = '';
?>

<div id="jomdirectory-admin-header">
    <div class="card p-2 clearfix d-block">
        <div class="float-left p-2"><?php echo JText::_('COM_JOMDIRECTORY_ADM_WELCOME') ?> <strong><?php echo $user ?></strong></div>
        <div class="float-right">
            <div class="d-inline-block">
				<?php if ($params->get('enable_profile')): ?>
					<?php if ($params->get('admin_admin_profile')): ?>
                        <div class="d-inline">
                            <a href="<?php echo JRoute::_('index.php?Itemid=' . $params->get('admin_admin_profile')) . $linkpostfix ?>" class="btn btn-primary btn-sm"><i class="fa fa-user"></i> <?php echo JText::_('COM_JOMDIRECTORY_ADM_POFILE') ?></a>
                        </div>
					<?php endif ?>
					<?php if ($params->get('admin_profile_edit')): ?>
                        <div class="d-inline">
                            <a href="<?php echo JRoute::_('index.php?Itemid=' . $params->get('admin_profile_edit')) . $linkpostfix ?>" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> <?php echo JText::_('COM_JOMDIRECTORY_ADM_SETTINGS') ?></a>
                        </div>
					<?php endif ?>
				<?php endif ?>
                <div class="d-inline">
					<?php $userToken = JSession::getFormToken(); ?>
                    <a href="<?php echo JRoute::_('?option=com_users&task=user.logout&' . $userToken . '=1') ?>" class="btn btn-sm btn-danger"><i class="fa fa-caret-square-o-up"></i> <?php echo JText::_('COM_JOMDIRECTORY_ADM_LOGOUT') ?></a>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark my-3">
        <ul class="navbar-nav">
            <li class="nav-item <?php echo ($view == 'admin_dashboard') ? 'active' : null ?>">
				<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_dashboard'), '<i class="fa fa-dashboard"></i> ' . JText::_('COM_JOMDIRECTORY_ADM_DASHBOARD'), 'class="nav-link"') ?>
            </li>
            <li class="nav-item <?php echo ($view == 'admin_listings' || $view == 'admin_comments') ? 'active' : null ?>">
				<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_listings'), JText::_('COM_JOMDIRECTORY_ADM_LISTINGS'), 'class="nav-link"') ?></li>
            <li class="nav-item <?php echo ($view == 'admin_additem') ? 'active' : null ?>">
				<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_additem'), '<i class="fa fa-plus-square"></i> ' . JText::_('COM_JOMDIRECTORY_ADM_ADD_NEW'), 'class="nav-link"') ?></li>
			<?php if ($params->get('product_display')): ?>
				<?php if (isset($paid_fields['products']) && $paid_fields['products'] == '1'): ?>
                    <li class="nav-item <?php echo ($view == 'admin_products') ? 'active' : null ?>">
						<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_products'), JText::_('COM_JOMDIRECTORY_PRODUCTS'), 'class="nav-link"') ?></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($params->get('enable_booking')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
						<?php echo JText::_('COM_JOMDIRECTORY_BOOKING'); ?>
                    </a>
                    <div class="dropdown-menu">
						<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_services'), JText::_('COM_JOMDIRECTORY_SERVICE'), 'class="dropdown-item"') ?>
						<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_bookings'), JText::_('COM_JOMDIRECTORY_BOOKING'), 'class="dropdown-item"') ?>
                    </div>
                </li>
			<?php endif; ?>
            <li class="nav-item <?php echo ($view == 'admin_membership') ? 'active' : null ?>">
				<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_membership'), JText::_('COM_JOMDIRECTORY_ADM_MEMBERSHIP_PLANS'), 'class="nav-link"') ?></li>
            <li class="nav-item <?php echo ($view == 'admin_messages') ? 'active' : null ?>">
				<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_messages'), JText::_('COM_JOMDIRECTORY_ADM_MESSAGES'), 'class="nav-link"') ?></li>
            <li class="nav-item <?php echo ($view == 'admin_help') ? 'active' : null ?>">
				<?php echo JHTML::_('link', JRoute::_('?option=com_jomdirectory&view=admin_help'), JText::_('COM_JOMDIRECTORY_ADM_HELP'), 'class="nav-link"') ?></li>
        </ul>
    </nav>
</div>


    