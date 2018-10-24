<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/
defined('_JEXEC') or die;
JHTML::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/jqueryui/jquery-ui.css');
//$document->addScript(JURI::root() . 'components/com_jomcomdev/node_modules/jquery-file-upload/js/jquery.uploadfile.min.js');
//$document->addStyleSheet(JURI::root() . 'components/com_jomcomdev/node_modules/jquery-file-upload/css/uploadfile.css');
$extension = "com_jomdirectory";
$params = JComponentHelper::getParams($extension);
$maxsize = $params->get("max_att_size", "-1");


if ($maxsize > 0) $maxsize *= 1024;
$language = explode("-", JFactory::getLanguage()->getTag());

?>

<script>
	var filesInProcess = 0;
	var uploadImagesSettings;
	var imageGroupSelect;
	jQuery(document).ready(function () {

		uploadSettings(0)
		reloadImages();
	});

	function uploadSettings(group) {
		jQuery(".ajax-upload-dragdrop").hide();
		imageGroupSelect = group;
		uploadImagesSettings = {
			url: "<?php echo JRoute::_("index.php?option=com_jomcomdev&task=image.drop&tmpl=component&content_id=" . $this->item->id . "&lang=" . $language[0] . "&extension=" . $extension);?>&amp;group=" + group,
			method: "POST",
			allowedTypes: "<?php echo $params->get("file_ext", "*")?>",
			maxFileSize: "<?php echo $maxsize?>",
			fileName: "files_to_upload",
			multiple: true,
			dragDropStr: "<div style='float: right; text-transform: uppercase; color: #ccc'><?php echo JText::_('COM_JOMCOMDEV_DROP_FILES')?></div>",
			abortStr: "<?php echo JText::_('COM_JOMCOMDEV_ABORT')?>",
			cancelStr: "<?php echo JText::_('COM_JOMCOMDEV_CANCEL')?>",
			doneStr: "<?php echo JText::_('COM_JOMCOMDEV_DONE')?>",
			uploadErrorStr: "<?php echo JText::_('COM_JOMCOMDEV_IMAGE_LIMIT')?>",
			extErrorStr: "<?php echo JText::_('COM_JOMDIRECTORY_CFG_FILE_EXT')?>",
			sizeErrorStr: "<?php echo JText::_('COM_JOMDIRECTORY_CFG_MAX_ATT_SIZE')?>",
			onSuccess: function (files, data, xhr) {
				reloadImages();
			},
			onSubmit: function (files, data, xhr) {
				filesInProcess++;
				count = <?php echo (int)$this->pre_limit[1]?>-filesInProcess - jQuery(".cddir_del_i").length;
				if (count < 0) return false;
			}
		}

		jQuery("#mulitplefileuploader").uploadFile(uploadImagesSettings);
	}

	function reloadImages() {
		jQuery.post("<?php echo JURI::base(true) ?>/index.php?option=com_jomcomdev&task=image.AJAXImages&tmpl=component&id=<?php echo $this->item->id ?>&extension=<?php echo $extension ?>&viewO=<?php echo JRequest::getVar('view') ?>&limit=<?php echo $this->pre_limit[1]?>&lang=<?php echo $language[0]?>")
			.done(function (resp) {
				tmp = resp.split("<_cut_>");
				jQuery("#ajaxImages").html(tmp[1]);
				orderids = 'id=';
				jQuery('#sortable2').children().each(function () {
					orderids += jQuery(this).attr('name') + ",";
				});
				jQuery.post("<?php echo JURI::base(true) ?>/index.php?option=com_jomcomdev&task=file.order&tmpl=component&" + orderids + "&content_id=<?php echo $this->item->id ?>&extension=<?php echo $extension ?>&lang=<?php echo $language[0]?>")
					.done(function (resp) {
						filesInProcess = 0;
						orderids = 'id=';
						jQuery('#sortable1').children().each(function () {
							orderids += jQuery(this).attr('name') + ",";
						});
						jQuery.post("<?php echo JURI::base(true) ?>/index.php?option=com_jomcomdev&task=image.order&tmpl=component&" + orderids + "&content_id=<?php echo $this->item->id ?>&extension=<?php echo $extension ?>&lang=<?php echo $language[0]?>")
						if (imageGroupSelect) jQuery('#imagesGroup').val(imageGroupSelect);
					})
			})
	}
</script>
<?php echo JHtml::_('sliders.panel', '<i class="fa fa-picture-o"></i> ' . JText::_('COM_JOMDIRECTORY_FIELDSET_IMAGES') . '<i class="fa fa-chevron-down fa-panel-right float-right "></i>', 'images'); ?>
<div class="mb-3">

	<?php if ($this->item->id): ?>
        <fieldset class="panelform">
            <div class="adminformlist">
                <div id="mulitplefileuploader" class="comdev-no-limit"><?php echo JText::_('COM_JOMCOMDEV_ADD_FILES') ?></div>
                <div id="ajaxImages"></div>
            </div>
        </fieldset>
	<?php else: ?>
        <div class="alert alert-danger"><?php echo JText::_('COM_JOMCOMDEV_FILEADD_NOID_INFO') ?></div>
	<?php endif ?>

</div>

<?php echo JHtml::_('sliders.panel', '<i class="fa fa-video-camera"></i> ' . JText::_('COM_JOMDIRECTORY_FIELD_YT') . '<i class="fa fa-chevron-down fa-panel-right float-right "></i>', 'images'); ?>
<div class="mb-3">

	<?php echo $this->form->getlabel('youtube_link'); ?>
	<?php echo $this->form->getInput('youtube_link'); ?>

</div>