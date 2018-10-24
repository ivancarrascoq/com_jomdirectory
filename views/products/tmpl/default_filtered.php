<?php
$filtered = Main_Helpers_Filter::filter(array('products.filter.categories_address_id' => 'category', 'products.filter.address-lat-lng' => 'text', 'products.filter.categories_id' => 'category', 'products.filter.search' => 'text', 'products.filter.jd_mod_price' => 'rangeprice', 'products.filter.jd-mod-price_from' => 'text', 'products.filter.jd-mod-price_to' => 'text', 'products.filter.fields' => 'fields'), 'com_jomdirectory', 'products');

$browser_back = 0;
if (isset($_POST['browser_back'])) {
	header("Location:" . $_SERVER['REQUEST_URI']);
}
?>

<?php if ($filtered) : ?>
    <div class="d-block position-relative my-3">
        <h4 class="mb-1"><?php echo JText::_('COM_JOMCOMDEV_FILTERS_APPLIED'); ?>:</h4>
		<?php foreach ($filtered as $fitem): ?>
			<?php if ($fitem->type == "text"): ?>
                <a href="<?php echo $fitem->delete; ?>" title="<?php echo $fitem->text; ?>" class="btn btn-primary mr-1 mt-1"><?php echo JText::_($fitem->name); ?>: <?php echo $fitem->text; ?> <i class="fa fa-remove"></i></a>
			<?php elseif ($fitem->type == "category"): ?>
                <a href="<?php echo $fitem->delete; ?>" title="<?php echo $fitem->text; ?>" class="btn btn-primary mr-1 mt-1"><?php echo $fitem->text; ?> <i class="fa fa-remove"></i></a>
			<?php elseif ($fitem->type == "fields"): ?>
                <a href="<?php echo $fitem->delete; ?>" title="<?php echo $fitem->text; ?>" class="btn btn-primary mr-1 mt-1"><?php echo $fitem->text; ?>: <?php echo $fitem->value; ?> <i class="fa fa-remove"></i></a>
			<?php elseif ($fitem->type == "rangeprice"): ?>
                <a href="<?php echo $fitem->delete; ?>" title="<?php echo $fitem->text; ?>" class="btn btn-primary mr-1 mt-1"><?php echo JText::_('COM_JOMCOMDEV_FIELDSET_PRICE'); ?>: <?php echo $fitem->text; ?> <?php echo $this->params->get('adm_currency'); ?> <i class="fa fa-remove"></i></a>
			<?php endif; ?>
		<?php endforeach; ?>
        <a href="<?php echo JRoute::_('index.php?option=com_jomdirectory&task=product.stateClear'); ?>" title="<?php echo JText::_('COM_JOMCOMDEV_FILTERS_CLEAR_ALL'); ?> " class="btn btn-danger mr-1 mt-1"><?php echo JText::_('COM_JOMCOMDEV_FILTERS_CLEAR_ALL'); ?> <i class="fa fa-remove"></i></a>
        <hr/>
    </div>
<?php endif; ?>