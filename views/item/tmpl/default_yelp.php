<?php
/*------------------------------------------------------------------------
# com_jomdirectory - JomDirectory
# ------------------------------------------------------------------------
# author    Comdev
# copyright Copyright (C) 2018 comdev.eu. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://comdev.eu
------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$url = "https://api.yelp.com/oauth2/token";
$P[] = "grant_type=client_credentials";
$P[] = "client_id=" . $this->params->get('yelp_key');
$P[] = "client_secret=" . $this->params->get('yelp_secret');
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, join("&", $P));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$r = curl_exec($ch);
if (FALSE === $r) echo curl_error($ch);
curl_close($ch);
$returned_items = json_decode($r, true);

$url = "https://api.yelp.com/v3/businesses/" . $this->item->yelp_id . "/reviews";
$authorization = "Authorization: Bearer " . $returned_items['access_token'];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
if (FALSE === $r) echo curl_error($ch);
$r = curl_exec($ch);
curl_close($ch);
$returned_items = json_decode($r, true);

function cmp($a, $b)
{
	return strcmp($a["time_created"], $b["time_created"]);
}

if (count($returned_items['reviews'])):
	usort($returned_items['reviews'], "cmp");
	$returned_items['reviews'] = array_reverse($returned_items['reviews']);
	foreach ($returned_items['reviews'] AS $review):?>
        <div class="ce-reviewsBody">
            <div class="row mb-3">
                <div class="col-md-3 text-center ce-reviewsBodySidebar" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
					<?php if ($review['user']['image_url']): ?>
                        <img src='<?php echo $review['user']['image_url'] ?>' width='85' class="img-thumbnail">
					<?php else: ?>
                        <i class="mdi mdi-account mdi-48px"></i>
					<?php endif; ?>
                    <div class="mt-1"><?php echo $review['user']['name'] ?></div>
                </div>
                <div class="col-md-9 ce-reviewsBodyContent">
                    <div class="ce-reviewsBodyDate">
						<?php echo JText::_('COM_JOMCOMDEV_REVIEWS_DATE') ?>:
						<?php $date = new JDate($review['time_created']);
						echo $date->format(JText::_('DATE_FORMAT_LC2')); ?> <br> <input value="<?php echo (int)$review['rating'] ?>" type="text" readonly class="yelp_stars" data-width="80" data-height="80" data-displayPrevious="true" data-fgColor="#a88e4b" data-thickness=".2">
                    </div>
                    <div class="ce-reviewsText my-3" itemprop="description">
						<?php echo $review['text'] ?>
                        <a href='<?php echo $review['url'] ?>' target='_blank'> <?php echo JText::_('COM_JOMDIRECTORY_LEARN_MORE') ?></a>
                    </div>

                </div>
            </div>

        </div>
	<?php endforeach; ?>
<?php else: ?>
    <div class="card card-body p-3 green lighten-5 mt-3"><?php echo JText::_('COM_JOMCOMDEV_NOREVIEWS') ?></div>
<?php endif; ?>