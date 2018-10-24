<h2><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING'); ?></h2>


<ul class="checkout-header mb-5 text-center">
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">1</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_METHOD'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number jd-header-active">2</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">3</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">4</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_ORDER'); ?></div>
    </li>
</ul>

<div class="checkout-body">

    <div class="d-block position-relative card">
        <div class="card-title h4 p-3">
			<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING'); ?>
        </div>
        <div class="card-body">
            <form method="post" action="/index.php?option=com_jomdirectory&task=cart.formAddress&format=json" id="billing_form" class="form-validate">
                <div class="form-group">
                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_ADDRESS'); ?></label> <textarea name="address" aria-required="true" required="" id="address" class="form-control"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_CITY'); ?></label> <input type="text" aria-required="true" required="" class="required form-control" id="city" name="city">
                        </div>
                        <div class="form-group">
                            <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_ZIP'); ?></label> <input type="text" aria-required="true" required="" class="required form-control" id="zip" name="zip">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_STATE'); ?></label> <input type="text" aria-required="true" required="" class="required form-control" id="state" name="state">
                        </div>
                        <div class="form-group">
                            <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_COUNTRY'); ?></label> <input type="text" aria-required="true" required="" class="required form-control" id="country" name="country">
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label onclick="jQuery('.jd-shipping-info').hide();" class="btn btn-primary active"> <input type="radio" class="shipping_skip" name="shipping_skip" checked="checked" value="0"> <?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_SHIPPING_USEYES'); ?>
                        </label> <label onclick="jQuery('.jd-shipping-info').show();" class="btn btn-secondary"> <input type="radio" class="shipping_skip" name="shipping_skip" value="1"> <?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_SHIPPING_USENO'); ?>
                        </label>
                    </div>
                </div>
                <div class="position-relative card jd-shipping-info mt-3" style="display: none;">
                    <div class="card-title h4 p-3">
						<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_SHIPPING_INFO'); ?>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_ADDRESS'); ?></label> <textarea name="address_ship" id="address" class="form-control"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_CITY'); ?></label> <input type="text" class="required form-control" id="city" name="city_ship">
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_ZIP'); ?></label> <input type="text" class="required form-control" id="zip" name="zip_ship">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_STATE'); ?></label> <input type="text" class="required form-control" id="state" name="state_ship">
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_COUNTRY'); ?></label> <input type="text" class="required form-control" id="country" name="country_ship">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-success mt-3" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_CONTINUE'); ?></button>
            </form>
        </div>
    </div>

</div>
