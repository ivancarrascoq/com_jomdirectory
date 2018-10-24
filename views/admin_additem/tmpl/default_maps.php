<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;
?>

<div class="d-block position-relative card p-2 form-row  green lighten-3">
    <div class="row">
        <div class="col-md-3">
			<?php echo $this->form->getLabel('maps_lat'); ?>
			<?php echo $this->form->getInput('maps_lat'); ?>
        </div>
        <div class="col-md-3">
			<?php echo $this->form->getLabel('maps_lng'); ?>
			<?php echo $this->form->getInput('maps_lng'); ?>
        </div>
        <div class="col-md-6">
			<?php echo $this->form->getLabel('maps_search'); ?>
			<?php echo $this->form->getInput('maps_search'); ?>
        </div>
    </div>
    <div class="form-row mt-2">
		<?php echo $this->form->getInput('maps'); ?>
    </div>
</div>








 
