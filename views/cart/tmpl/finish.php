<?php

$sessionData = JFactory::getSession()->get('jd-cart');

?>

<h3>ORDER ID: <?= $sessionData->order ?></h3>
<hr/>

<?= $this->article->introtext ?>

<?= $this->article->fulltext ?>


