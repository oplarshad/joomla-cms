<?php
/**
 * @version     1.6.0
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access
defined('_JEXEC') or die;

use Joomla\String\StringHelper;

// Get the mime type class.
$mime = !empty($this->result->mime) ? 'mime-' . $this->result->mime : null;
$this->result->description = strip_tags($this->result->description);
// Calculate number of characters to display around the result
$term_length = StringHelper::strlen($this->query->input);
$desc_length = $this->params->get('description_length', 255);
$pad_length = $term_length < $desc_length ? (int) floor(($desc_length - $term_length) / 2) : 0;

// Find the position of the search term
$pos = $term_length ? StringHelper::strpos(StringHelper::strtolower($this->result->description), StringHelper::strtolower($this->query->input)) : false;

// Find a potential start point
$start = ($pos && $pos > $pad_length) ? $pos - $pad_length : 0;

// Find a space between $start and $pos, start right after it.
$space = StringHelper::strpos($this->result->description, ' ', $start > 0 ? $start - 1 : 0);
$start = ($space && $space < $pos) ? $space + 1 : $start;

$description = JHtml::_('string.truncate', StringHelper::substr($this->result->description, $start), $desc_length, true);
$route       = $this->result->route;
$result      = $this->result;
$item        = new stdClass;
$dPrice      = null;
try
{
	$uri  = new JUri($result->url);
	$code = $uri->getVar('p');

	$helper = SellaciousHelper::getInstance();
	$helper->product->parseCode($code, $productId, $variantId, $sellerUid);

	$product    = new Sellacious\Product($productId, $variantId, $sellerUid);
	$price      = $product->getPrice(null);
	$categories = $product->getCategories();
	$categories = $this->helper->category->loadColumn(array('list.select' => 'a.title', 'id' => $categories));
	$sCurrency  = $this->helper->currency->forSeller($price->seller_uid, 'code_3');
	$dPrice     = $price->no_price ? null : $this->helper->currency->display($price->basic_price, $sCurrency, '');
	$image      = $this->helper->product->getImage($productId);
}
catch (Exception $e)
{
	return;
}
?>
<tr>
	<td style="width: 110px;">
		<img src="<?php echo $image ?>" style="max-height: 100%; max-width: 100%;"/>
	</td>
	<td>
		<h4 class="result-title <?php echo $mime; ?>"><a href="<?php
			echo JRoute::_($route); ?>"><?php echo $this->result->title; ?></a></h4>

		<strong class="pull-right"><?php echo $dPrice ?></strong>

		<p class="result-category<?php echo $this->pageclass_sfx; ?>"><strong><?php
				echo JText::_('COM_SELLACIOUS_SEARCH_PREFIX_CATEGORIES') ?></strong><?php echo implode(', ', $categories); ?></p>

		<p class="result-text<?php echo $this->pageclass_sfx; ?>"><?php echo $description; ?></p>

		<div class="search-btnarea">
			<a href="<?php echo JRoute::_($this->result->route); ?>"
			   class="btn btn-primary btn-small"><?php echo strtoupper(JText::_('COM_SELLACIOUS_PRODUCT_DETAILS')); ?></a>
		</div>
	</td>
</tr>
