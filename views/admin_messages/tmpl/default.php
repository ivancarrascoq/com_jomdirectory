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
$jVerArr = explode('.', JVERSION);
if ($jVerArr[0] >= '3') JHtml::_('bootstrap.tooltip'); else JHtml::_('behavior.tooltip');
function strip_html_tags($str)
{
	$str = preg_replace('/(<|>)\1{2}/is', '', $str);
	$str = preg_replace(array(// Remove invisible content
		'@<head[^>]*?>.*?</head>@siu', '@<style[^>]*?>.*?</style>@siu', '@<script[^>]*?.*?</script>@siu', '@<noscript[^>]*?.*?</noscript>@siu',), "", //replace above with nothing
		$str);
	$str = strip_tags($str, "<p><div><br><p><b>");
	return $str;
}

$header = new JLayoutFile('default_header', $basePath = JPATH_ROOT . '/components/com_jomdirectory/views/admin_dashboard/tmpl');

JHTML::_('behavior.modal');
$document = JFactory::getDocument();
$document->addScript(JURI::base() . 'components/com_jomcomdev/javascript/CeraBox/cerabox.min.js');
$document->addStyleSheet(JURI::base() . 'components/com_jomcomdev/javascript/CeraBox/style/cerabox.css');

$runScript = "window.addEvent('domready', function() {
	 $$('.message_box_init').cerabox({
    width:700,
    height: 650
    });
});";
$document->addScriptDeclaration($runScript);
?>

<div id="jomdirectory-admin">

	<?php echo $header->render(null); ?>

    <div class="border-bottom mt-3 clearfix">
        <div class="float-left">
            <h3><?php echo JText::_('COM_JOMDIRECTORY_ADM_MESSAGES') ?></h3>
        </div>
        <div class="float-right">
			<?php echo $this->toolbar ?>
        </div>
    </div>

    <div id="admin-header" class="my-3 pb-3 border-bottom">
        <form name="jdFormItems" action="" method="post" class="">
            <div class="mt-3 clearfix form-row align-items-center">
                <div class="d-inline">
                    <input id="filter_search" class="form-control" type="text" value='<?php echo $this->state->{'filter.search'} ?>' name="filter_search">
                </div>
                <button class="btn btn-primary" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_ADM_SEARCH'); ?></button>
                <button class="btn btn-primary" onclick="document.id('filter_search').value='';this.form.submit();" type="button"><?php echo JText::_('COM_JOMDIRECTORY_ADM_CLEAR'); ?></button>

                <label class="my-0 mx-2"><?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_BY'); ?></label>
				<?php
				$arraySort = array(array('v' => 'a.date DESC', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_LATEST')), array('v' => 'c.title', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_ALPHABETICALLY')), array('v' => 'a.email_from', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_EMAIL_FROM')), array('v' => 'a.email_to', 't' => JText::_('COM_JOMDIRECTORY_HEADER_SORT_EMAIL_TO')));
				?>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', $arraySort, 'jdItemsSort', 'class="custom-select mb-0" onChange="this.form.submit()"', 'v', 't', $this->state->{'list.sort'}); ?>
                </div>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', array(array('text' => 5, 'value' => 5), array('text' => 10, 'value' => 10), array('text' => 15, 'value' => 15), array('text' => 30, 'value' => 30), array('text' => 60, 'value' => 60)), 'jdItemsPerPage', 'class="custom-select mb-0" onChange="this.form.submit()"', 'value', 'text', $this->state->{'list.limit'}); ?>
                </div>
                <label class="my-0 mx-2"><?php echo JText::_('COM_JOMDIRECTORY_ADM_CATEGORY') ?></label>
                <div class="d-inline">
					<?php echo JHtml::_('select.genericlist', $this->categories, 'filter_category', 'class="custom-select mb-0" onChange="this.form.submit()" style="width:100px"', 'v', 't', $this->state->{'filter.categories_id'}); ?>
                </div>
            </div>
        </form>
    </div>

    <form action="" method="post" name="adminForm" id="adminForm">

        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/></th>
                <th><i class="fa fa-envelope-o"></i> <?php echo JText::_('COM_JOMDIRECTORY_ADMIN_TITLE') ?></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_EMAIL_FROM') ?> / <?php echo JText::_('COM_JOMDIRECTORY_HEADER_SORT_EMAIL_TO') ?></th>
                <th><?php echo JText::_('COM_JOMDIRECTORY_FIELD_CREATED_LABEL') ?></th>
            </tr>
            </thead>
            <tbody>
			<?php $i = 0;
			foreach ($this->items as $i => $item): ?>
                <tr>
                    <td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td>
                        <a href="#message_box<?php echo $i ?>" class="message_box_init" title="<?= $this->escape($item->title); ?>"><?= $this->escape($item->title); ?> </a>
                        <div style='display:none'>
                            <div id="message_box<?php echo $i ?>" class="card">
                                <div class="card-header"><h2><?php echo JText::_('COM_JOMDIRECTORY_MESSAGE'); ?></h2></div>
                                <div class="card-body">
	                                <?php echo  strip_html_tags($item->message); ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
						<?php echo $this->escape($item->email_from); ?> ->
						<?php echo $this->escape($item->email_to); ?>
                    </td>
                    <td><?php echo JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC3')); ?></td>
                </tr>
				<?php $i++; endforeach; ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value=""/> <input type="hidden" name="hidemainmenu" value=""/> <input type="hidden" name="boxchecked" value=""/>
    </form>

    <div class="pagination clearfix">
		<?php if ($this->pagination->get('pages.total') > 1): ?>
            <div class="pagination justify-content-center"><?php echo $this->pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
    </div>

</div>

