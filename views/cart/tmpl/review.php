<?php

$elements = $this->cart->getList();
$data = JFactory::getSession()->get('jd-cart');

$data->shipping->name = $this->shipping[$data->shipping->shipping];
if (isset($data->shipping->coupon)) {
	$data->shipping->name->coupon_discount = $data->shipping->coupon_discount;
	$data->shipping->name->coupon = $data->shipping->coupon;
} else $data->shipping->coupon_discount = 0;
$data->count = new stdClass();
$data->count->priceShipping = $this->shipping[$data->shipping->shipping]->price;
$data->count->priceAll = $this->cart->getPrice() + $data->count->priceShipping - $data->shipping->coupon_discount;
$data->count->priceShippingString = Main_Price::changeSingle($data->count->priceShipping, $this->priceParams);
$data->count->priceAllString = Main_Price::changeSingle($data->count->priceAll, $this->priceParams);
$this->cart->summarySave($data);

JFactory::getSession()->set('jd-cart', $data);
?>

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

<h2><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_ORDER'); ?></h2>

<ul class="checkout-header mb-5 text-center" data-uk-switcher="{connect:'#switch-from-content', animation: 'fade'}">
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">1</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_METHOD'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">2</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">3</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number jd-header-active">4</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_ORDER'); ?></div>
    </li>
</ul>

<div class="checkout-body my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="d-block position-relative card">
                <div class="card-title h4 p-3"><?php echo JText::_('COM_JOMDIRECTORY_CART'); ?></div>
                <div class="card-body">
					<?php if (empty($this->items)): ?>
                        <p><?php echo JText::_('COM_JOMDIRECTORY_CART_EMPTY'); ?></p>
                        <button class="btn btn-primary mt-3" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_CART_CONTINUE'); ?></button>
					<?php else: ?>
                        <table class="table table-striped" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_NAME'); ?></th>
                                <th><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_PRICE'); ?></th>
                                <th width="10%"><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_QTY'); ?></th>
                                <th><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_SUBTOTAL'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ($elements AS $e): ?>
                                <tr id="<?= $e->id; ?>">
                                    <td>
										<?php if ($e->image): ?>
                                            <img src="<?php echo $e->image; ?>" alt="thumb" class="img-thumbnail" style="max-width: 100px;"/>
										<?php endif; ?>
                                    </td>
                                    <td><?php echo $e->title; ?></td>
                                    <td>
										<?php echo Main_Price::changeSingle($e->price, $this->priceParams) ?>
										<?php if ($this->oldprices[$e->id] != 0): ?>
                                            <br/>
                                            <del class="text-danger"><?php echo Main_Price::changeSingle($this->oldprices[$e->id], $this->priceParams); ?></del>
										<?php endif; ?>
                                    </td>
                                    <td><?php echo $e->count ?></td>
                                    <td>
										<?php echo Main_Price::changeSingle($e->price * $e->count, $this->priceParams) ?>
										<?php if ($this->oldprices[$e->id] != 0): ?>
                                            <br/>
                                            <del class="text-danger"><?php echo Main_Price::changeSingle($this->oldprices[$e->id] * $e->count, $this->priceParams); ?></del>
										<?php endif; ?>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                            <tr class="jd-total">
                                <td colspan="4" class="text-right text-primary"><?php echo JText::_('COM_JOMDIRECTORY_CART_ITEM_SUBTOTAL'); ?></td>
                                <td><span class="price-subtotal text-primary"><?php echo Main_Price::changeSingle($this->cart->getPrice(), $this->priceParams) ?></span></td>
                            </tr>
							<?php if (isset($this->cart->data->shipping->coupon)): ?>
                                <tr>
                                    <td colspan="4" class="text-right"><?php echo JText::_('COM_JOMDIRECTORY_COUPON'); ?></td>
                                    <td><?php echo Main_Price::changeSingle(-$this->cart->data->shipping->coupon_discount, $this->priceParams) ?></td>
                                </tr>
							<?php endif; ?>
                            <tr>
                                <td colspan="4" class="text-right"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_SHIPPING'); ?></td>
                                <td><?php echo $this->cart->data->count->priceShippingString; ?></td>
                            </tr>
                            <tr class="jd-total">
                                <td colspan="4" class="text-right h5"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_ITEM_TOTAL'); ?></td>
                                <td class="h5"><?php echo $this->cart->data->count->priceAllString; ?></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5">
                                    <a href="/index.php?option=com_jomdirectory&task=cart.formPayments&format=json" title="order" class="float-right mt-3 btn btn-primary btn-lg"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PLACE_ORDER'); ?></a>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
					<?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-block position-relative card">
                <div class="card-title h4 p-3"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PROGRESS'); ?></div>
                <div class="card-body">
                    <dl>
                        <div class="my-3">
                            <dt class="h5">
								<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_ADDRESS'); ?>
                            </dt>
                            <dd class="complete">
                                <address>
									<?= $this->cart->data->address->address ?><br>
									<?= $this->cart->data->address->state ?> <?= $this->cart->data->address->zip ?><br>
									<?= $this->cart->data->address->country ?><br>
                                </address>
                            </dd>
                        </div>
						<?php if (!empty($this->cart->data->address->shipping_skip)): ?>
                            <div class="my-3">
                                <dt class="h5">
									<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_SHIPPING_ADDRESS'); ?>
                                </dt>
                                <dd class="complete">
                                    <address>
										<?= $this->cart->data->address->address_ship ?><br>
										<?= $this->cart->data->address->state_ship ?> <?= $this->cart->data->address->zip_ship ?><br>
										<?= $this->cart->data->address->country_ship ?><br>
                                    </address>
                                </dd>
                            </div>
						<?php endif ?>
                        <div class="my-3">
                            <dt class="h5">
								<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_SHIPPING_METHOD'); ?>
                            </dt>
                            <dd class="complete">
								<?= $this->cart->data->shipping->name->name ?>
                                <span class="price"><?= $this->cart->data->shipping->name->price_string ?></span>
                            </dd>
                        </div>
                        <div class="my-3">
                            <dt class="h5">
								<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT_METHOD'); ?>
                            </dt>
                            <dd class="complete">
                                <p><?= JText::_('COM_JOMCOMDEV_PAYMENT_METHOD_' . strtoupper($this->cart->data->shipping->payment)) ?></p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

