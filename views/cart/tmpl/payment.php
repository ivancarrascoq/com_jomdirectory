<?php

$elements = $this->cart->getList();
$payments = Main_Payment::getMethods();


?>

<h2><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT'); ?></h2>

<ul id="checkout-header" class="checkout-header mb-5 text-center" data-uk-switcher="{connect:'#switch-from-content', animation: 'fade'}">
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">1</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_METHOD'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">2</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number jd-header-active">3</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT'); ?></div>
    </li>
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number">4</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_ORDER'); ?></div>
    </li>
</ul>

<div class="checkout-body">
    <form method="post" action="/index.php?option=com_jomdirectory&task=cart.formShipping&format=json" id="billing_form" class="form-validate " data-link="/index.php?option=com_jomcomdev&task=cart.formShipping&format=json">
        <div class="d-block position-relative card my-4">
            <div class="card-title h4 p-3">
				<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_SHIPPING_METHOD'); ?>
            </div>
            <div class="card-body">
				<?php $i = 0;
				foreach ($this->shipping AS $key => $sh): $i++; ?>
                    <div class="clearfix">
                        <div class="float-left">
                            <input type="radio" value="<?= $key ?>" id="fixed_rate<?= $key ?>" name="shipping" <?php if ($i == 1): ?>checked="checked"<?php endif; ?>>
                            <label for="fixed_rate<?= $key ?>"><?= $sh->name ?></label>
                        </div>
						<?php if (!empty($sh->price_string)): ?>
                            <div class="float-right"><strong><?= $sh->price_string ?></strong></div>
						<?php endif; ?>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
        <div class="d-block position-relative card my-4">
            <div class="card-title h4 p-3">
				<?php echo JText::_('COM_JOMDIRECTORY_COUPON'); ?>
            </div>
            <div class="card-body">
                <div class="clearfix">
                    <div class="form-group">
                        <input class="form-control" type="text" value="" checked="checked" name="coupon">
                    </div>
                </div>
            </div>
        </div>
        <div class="d-block position-relative card my-4">
            <div class="card-title h4 p-3">
				<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_PAYMENT_METHOD'); ?>
            </div>
            <div class="card-body">
                <div class="clearfix">
					<?php foreach ($payments AS $key => $p): ?>
                        <div class="clearfix">
                            <input type="radio" value="<?= $p->name ?>" id="fixed_pay<?= $key ?>" checked="checked" name="payment">
                            <label for="fixed_pay<?= $key ?>"><?= $p->params->title ?></label>
                        </div>
					<?php endforeach; ?>
                </div>
                <button class="btn btn-success mt-3" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_CONTINUE'); ?></button>
            </div>
        </div>
    </form>
</div>

