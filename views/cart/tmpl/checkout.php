<?php
$elements = $this->cart->getList();

JHtml::_('behavior.formvalidator');

$document = JFactory::getDocument();
?>
<h2><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_METHOD'); ?></h2>


<?php if (empty($this->items)): ?>
	<?php echo JText::_('COM_JOMDIRECTORY_CART_EMPTY'); ?>
<?php endif; ?>




<ul class="checkout-header mb-5 text-center">
    <li class="d-inline-block jd-header-block">
        <span class="d-block text-center float-left rounded-circle jd-header-number jd-header-active">1</span>
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
        <span class="d-block text-center float-left rounded-circle jd-header-number">4</span>
        <div class="d-block text-center jd-header-title"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_ORDER'); ?></div>
    </li>
</ul>

<div class="checkout-body">

    <div class="row my-5">
        <div class="col-md-6 mb-3">
            <div class="d-block position-relative card">
                <div class="card-title h4 p-3">
					<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_REGISTER'); ?>
                </div>
                <div class="card-body">
                    <form action="/index.php?option=com_jomdirectory&task=cart.formName&format=json" class="form-validate" method="post">
                        <p><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_REGISTER_INFO'); ?></p>
                        <div class="row">
                            <div class="col-md-6 col-sm-12 pr-2">
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_FIRST_NAME'); ?></label> <input type="text" aria-required="true" required="" class="validate-username required form-control" id="name" name="name">
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_COMPANY'); ?></label> <input type="text" class="form-control" id="company" name="company">
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_TEL'); ?></label> <input type="text" aria-required="true" required="" class="required form-control validate-numeric" id="telephone" name="telephone">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 pl-2">
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_LAST_NAME'); ?></label> <input type="text" aria-required="true" required="" class="required form-control" id="lastname" name="lastname">
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_EMAIL'); ?></label> <input type="text" aria-required="true" required="" class="required validate-email form-control" id="email" name="email">
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BILLING_FAX'); ?></label> <input type="text" class="form-control" id="fax" name="fax">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success mt-3" type="submit" id="register"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_REGISTER'); ?></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-block position-relative card">
                <div class="card-title h4 p-3">
					<?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_LOGIN'); ?>
                </div>
                <div class="card-body">
                    <form method="post" class="form-validate" action="/index.php?option=com_jomdirectory&task=cart.loginIn&format=json">
                        <fieldset>
                            <h4><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_LOGIN_INFO'); ?></h4>
                            <p><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_LOGIN_INFO1'); ?></p>
                            <div class="form-group">
                                <label class="required" for="login-email"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_LOGIN_USER'); ?></label> <input type="text" aria-required="true" required="" class="validate-username required form-control" id="username" name="jform[username]">
                            </div>
                            <div class="form-group">
                                <label class="required" for="login-password"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_LOGIN_PASS'); ?></label> <input type="password" aria-required="true" required="" maxlength="99" class="validate-password required form-control" value="" id="password" name="jform[password]">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary mt-3" type="submit"><?php echo JText::_('COM_JOMDIRECTORY_CHECKOUT_BOX_LOGIN'); ?></button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

