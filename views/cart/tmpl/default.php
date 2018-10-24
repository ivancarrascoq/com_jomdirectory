<?php

$elements = $this->cart->getList();

$document = JFactory::getDocument();

?>


<script type="text/javascript">
	jQuery(document).ready(function () {
		Comdev.cart.init();
	});
</script>

<style>
    @media only screen and (max-width: 760px),
    (min-device-width: 768px) and (max-device-width: 1024px) {

        /* Force table to not be like tables anymore */
        table, thead, tbody, th, td, tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        tr {
        }

        td {
            /* Behave  like a "row" */
            border: none;
            position: relative;
            padding-left: 50%;
        }

        td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        tfoot a {
            clear: both;
            text-align: center;
        }

    }
</style>

<div class="jd-cart-wrapper my-5">
    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="d-block position-relative card">
                <div class="card-title p-3 h4"><?php echo JText::_('COM_JOMDIRECTORY_CART'); ?></div>
                <div class="card-body">
					<?php if (empty($this->items)): ?>
                        <p><?php echo JText::_('COM_JOMDIRECTORY_CART_EMPTY'); ?></p>
                        <a href="<?= JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute()) ?>" class="btn btn-primary mt-3"><?php echo JText::_('COM_JOMDIRECTORY_CART_CONTINUE'); ?></a>
					<?php else: ?>
                        <table class="table table-striped" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_NAME'); ?></th>
                                <th><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_PRICE'); ?></th>
                                <th width="10%"><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_QTY'); ?></th>
                                <th><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_SUBTOTAL'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ($elements AS $e): ?>
                                <tr id="<?php echo $e->id; ?>">
                                    <td>
                                        <a href="/index.php?option=com_jomdirectory&task=cart.delete&format=json&id=<?= $e->id ?>" title="x" class="btn btn-sm btn-danger px-2"><i class="fa fa-remove"></i></a>
                                    </td>
                                    <td>
										<?php if ($e->image): ?>
                                            <img src="<?php echo $e->image; ?>" alt="thumb" class="img-thumbnail thumbnail-small z-depth-1" style="max-width: 200px;"/>
										<?php endif; ?>
                                    </td>
                                    <td><?php echo $e->title; ?></td>
                                    <td>
										<?php echo Main_Price::changeSingle($e->price, $this->priceParams) ?>
										<?php if ($this->oldprices[$e->id] != 0): ?>
                                            <br>
                                            <del class="text-danger"><?php echo Main_Price::changeSingle($this->oldprices[$e->id], $this->priceParams); ?></del>
										<?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <div class="qty-holder">
                                            <a class="d-inline-block btn btn-sm btn-outline-primary px-2 table_qty_dec" href="/index.php?option=com_jomdirectory&task=cart.subtract&format=json&id=<?= $e->id ?>">-</a>
                                            <input maxlength="12" class="d-inline-block text-center qty form-control px-1" title="Qty" value="<?php echo $e->count ?>" style="width: 60px;">
                                            <a class="d-inline-block btn btn-sm btn-outline-primary px-2 table_qty_inc" href="/index.php?option=com_jomdirectory&task=cart.subtractadd&format=json&id=<?= $e->id ?>">+</a>
                                        </div>
                                    </td>
                                    <td>
										<?php echo Main_Price::changeSingle($e->price * $e->count, $this->priceParams) ?>
										<?php if ($this->oldprices[$e->id] != 0): ?>
                                            <br>
                                            <del><?php echo Main_Price::changeSingle($this->oldprices[$e->id] * $e->count, $this->priceParams); ?></del>
										<?php endif; ?>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6">
                                    <div class="mt-3 clearfix">
                                        <a href="<?= JRoute::_(JomdirectoryHelperRoute::getCategoryProductRoute()) ?>" title="back" class="float-left btn btn-primary mb-3"><?php echo JText::_('COM_JOMDIRECTORY_CART_CONTINUE'); ?></a>
                                        <!--<a href="/index.php?option=com_jomdirectory&task=cart.update&format=json" title="back" class="float-right btn btn-primary"><?php echo JText::_('COM_JOMDIRECTORY_CART_UPDATE'); ?></a>-->
                                        <a href="/index.php?option=com_jomdirectory&task=cart.clearCart&format=json" title="back" class="float-right btn btn-primary mb-3"><?php echo JText::_('COM_JOMDIRECTORY_CART_CLEAR'); ?></a>
                                    </div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
					<?php endif; ?>
                </div>
            </div>
        </div>
		<?php if (!empty($this->items)): ?>
            <div class="col-md-4">
                <div class="d-block position-relative card">
                    <div class="card-title p-3 h4"><?php echo JText::_('COM_JOMDIRECTORY_CART_TOTAL'); ?></div>
                    <div class="card-body">
                        <div class="clearfix">
                            <label class="item-label float-left"><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_SUBTOTAL'); ?></label> <span class="item-price float-right"><?php echo Main_Price::changeSingle($this->cart->getPrice(), $this->priceParams) ?></span>
                        </div>
                        <!--                    <div class="cart-row clearfix">
                        <label class="item-label float-left"><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_TAX'); ?></label>
                        <span class="item-price float-right">22%</span>
                    </div>-->
                        <div class="cart-row mt-1 clearfix">
                            <label class="item-label h5 float-left"><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_TOTAL'); ?></label> <span class="item-price h5 float-right"><?php echo Main_Price::changeSingle($this->cart->getPrice(), $this->priceParams) ?></span>
                        </div>
                        <div class="my-3 text-center">
                            <a href="<?= JRoute::_(JomdirectoryHelperRoute::getCartRoute('checkout')) ?>" title="checkout" class="btn btn-success"><?php echo JText::_('COM_JOMDIRECTORY_CART_CHECKOUT'); ?>
                                <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>
    </div>
</div>

