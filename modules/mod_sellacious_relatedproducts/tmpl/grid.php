<?php
/**
 * @version     1.6.0
 * @package     Sellacious Related Products Module
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Bhavika Matariya <info@bhartiy.com> - http://www.bhartiy.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('script', 'com_sellacious/util.modal.js', false, true);
JHtml::_('stylesheet', 'com_sellacious/util.modal.css', null, true);

if ($helper->config->get('product_compare')):
	JHtml::_('script', 'com_sellacious/util.compare.js', false, true);
endif;

JHtml::_('script', 'com_sellacious/fe.view.sellacious.js', false, true);
JHtml::_('script', 'com_sellacious/util.cart.aio.js', false, true);

if ($layoutview != 'product'):
	JHtml::_('script', 'com_sellacious/fe.view.products.js', false, true);
endif;

JHtml::_('stylesheet', 'com_sellacious/font-awesome.min.css', null, true);
JHtml::_('stylesheet', 'mod_sellacious_relatedproducts/style.css', null, true);
JHtml::_('stylesheet', 'com_sellacious/fe.view.cart.aio.css', null, true);

?>
<div class="mod-sellacious-relatedproducts related-grid-layout <?php echo $class_sfx; ?>">
	<?php foreach ($relatedProducts AS $relatedProduct):
		if (($splCategory) && count($relatedProductsSpl) > 0):
			if (in_array($relatedProduct, $relatedProductsSpl)):
				$splCategoryClass = 'spl-cat-' . $splCategory;
			else:
				$splCategoryClass = '';
			endif;
		else:
			$splCategoryClass = '';
		endif;

		$prodHelper = new \Sellacious\Product($relatedProduct);

		$sellers = $helper->product->getSellers($relatedProduct, false);
		$seller  = ModSellaciousRelatedProducts::getCheapestSeller($sellers, $relatedProduct);

		$seller_uid = 0;
		if (is_object($seller) && isset($seller->seller_uid)):
			$seller_uid = $seller->seller_uid;
		endif;

		$item         = $helper->product->getItem($relatedProduct);
		$item->images = $helper->product->getImages($relatedProduct);
		$item->code   = $prodHelper->getCode($seller_uid);
		$ratings      = $helper->rating->getProductRating($item->id, 0, $seller_uid);
		$price        = $prodHelper->getPrice($seller_uid, 1, $c_cat);
		$seller_attr  = $prodHelper->getSellerAttributes($seller_uid);

		if (!is_object($seller_attr)):
			$seller_attr                 = new stdClass();
			$seller_attr->price_display  = 0;
			$seller_attr->stock_capacity = 0;
		endif;

		$item                = array_merge((array) $item, (array) $price);
		$item                = (object) $item;
		$seller_info         = ModSellaciousRelatedProducts::getSellerInfo($seller_uid);
		$item->seller_email  = $seller_info->seller_email;
		$item->seller_mobile = $seller_info->seller_mobile;

		$item->rating = $ratings->rating;
		$images = (array) $item->images;

		$url   = 'index.php?option=com_sellacious&view=product&p=' . $item->code;
		$url_m = JRoute::_($url . '&layout=modal&tmpl=component');

		$sl_params = array(
			'title'    => JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_QUICK_VIEW'),
			'url'      => $url_m,
			'height'   => '600',
			'width'    => '800',
			'keyboard' => true,
		);
		echo JHtml::_('bootstrap.renderModal', 'modal-' . $item->code, $sl_params);

		$s_currency = $helper->currency->forSeller($price->seller_uid, 'code_3');

		$options = array(
			'title'    => JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_CART_TITLE'),
			'backdrop' => 'static',
		);

		?>
		<script>
			jQuery(document).ready(function ($) {
				if ($('#modal-cart').length == 0) {
					var $html = <?php echo json_encode(JHtml::_('bootstrap.renderModal', 'modal-cart', $options)); ?>;
					$('body').append($html);

					var $cartModal = $('#modal-cart');
					var oo = new SellaciousViewCartAIO;
					oo.token = $('#formToken').attr('name');
					oo.initCart('#modal-cart .modal-body', true);
					$cartModal.find('.modal-body').html('<div id="cart-items"></div>');
					$cartModal.data('CartModal', oo);
				}
			});
		</script>
		<div class="related-product-wrap-grid">
			<div class="related-product-box <?php echo $splCategoryClass; ?>" data-rollover="container">
				<div class="image-box">
					<a href="<?php echo $url; ?>">
						<img src="<?php echo reset($images); ?>" data-rollover='<?php echo htmlspecialchars(json_encode($images)); ?>' title="<?php echo $item->title; ?>">
					</a>

					<?php
					if ((in_array('products', (array) $helper->config->get('splcategory_badge_display'))) && ($splCategoryClass)):
						$badges = $helper->media->getImages('splcategories.badge', (int) $splCategory, false);

						if ($badges): ?>
							<img src="<?php echo reset($badges) ?>" class="spl-cat-badge"/><?php
						endif;
					endif;
					?>
				</div>
				<div class="related-product-info-box">
					<div class="related-product-title">
						<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
							<?php echo $item->title; ?>
						</a>
					</div>

					<?php $allow_rating = $helper->config->get('product_rating'); ?>

					<?php if ($allow_rating && $displayratings == '1'): ?>
						<div class="product-stars">
							<?php echo $helper->core->getStars($item->rating, true, 5.0) ?>
						</div>
					<?php endif; ?>

					<hr class="isolate">
					<?php
					$allowed_price_display = (array) $helper->config->get('allowed_price_display');
					$security              = $helper->config->get('contact_spam_protection');

					if ($seller_attr->price_display == 0)
					{
						$price_display = $helper->config->get('product_price_display');
						$price_d_pages = (array) $helper->config->get('product_price_display_pages');

						if ($price_display > 0 && in_array('products', $price_d_pages))
						{
							$price = round($item->price_id, 3) > 0 ? $helper->currency->display($item->sales_price, $s_currency, $c_currency, true) : JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_NOT_AVAILABLE');

							if ($price_display == 2 && round($item->list_price, 2) >= 0.01)
							{
								?>
								<div class="related-product-price"><?php echo $price; ?></div>
								<div class="old-price">
									<del><?php echo $helper->currency->display($item->list_price, $s_currency, $c_currency, true); ?></del>
									<span class="related-product-offer"><?php echo strtoupper(JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_OFFER')); ?></span>
								</div>
								<?php
							}
							else
							{
								?>
								<div class="related-product-price pull-left"><?php echo $price; ?></div>
								<?php
							}
							?>
							<div class="clearfix"></div>
							<?php
						}
					}
					elseif ($seller_attr->price_display == 1 && in_array(1, $allowed_price_display))
					{
						?>
						<div class="btn-toggle btn-price-toggle">
							<button type="button" class="btn btn-default" data-toggle="true"><?php
								echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_PRICE_DISPLAY_CALL_US'); ?></button>
							<button type="button" class="btn btn-default hidden" data-toggle="true"><?php

								if ($security)
								{
									$b64text = $helper->media->writeText($item->seller_mobile, 2, true);
									?><img src="data:image/png;base64,<?php echo $b64text; ?>"/><?php
								}
								else
								{
									echo $item->seller_mobile;
								} ?>
							</button>
						</div>
						<div class="clearfix"></div>
						<?php
					}
					elseif ($seller_attr->price_display == 2 && in_array(2, $allowed_price_display))
					{
						?>
						<div class="btn-toggle btn-price-toggle">
							<button type="button" class="btn btn-default" data-toggle="true"><?php
								echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_PRICE_DISPLAY_EMAIL_US'); ?></button>
							<button type="button" class="btn btn-default hidden" data-toggle="true"><?php

								if ($security)
								{
									$b64text = $helper->media->writeText($item->seller_email, 2, true);
									?><img src="data:image/png;base64,<?php echo $b64text; ?>"/><?php
								}
								else
								{
									echo $item->seller_email;
								} ?>
							</button>
						</div>
						<?php
					}
					elseif ($seller_attr->price_display == 3 && in_array(3, $allowed_price_display))
					{
						$options = array(
							'title'    => (JText::sprintf('MOD_SELLACIOUS_RELATEDPRODUCTS_PRICE_DISPLAY_OPEN_QUERY_FORM_FOR',
								addslashes($item->title), isset($item->variant_title) ? addslashes($item->variant_title) : '')),
							'backdrop' => 'static',
							'height'   => '520',
							'keyboard' => true,
							'url'      => "index.php?option=com_sellacious&view=product&p={$item->code}&layout=query&tmpl=component",
						);

						echo JHtml::_('bootstrap.renderModal', "query-form-{$item->code}", $options);
						?>
						<div class="productquerybtn">
							<a href="#query-form-<?php echo $item->code; ?>" role="button" data-toggle="modal" class="btn btn-primary">
								<i class="fa fa-file-text"></i> <?php echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_PRICE_DISPLAY_OPEN_QUERY_FORM'); ?>
							</a>
						</div>
						<?php
					}
					?>
					<div class="clearfix"></div>

					<?php if ($featurelist == '1'):
						$features = $item->features;
						if (count($features)): ?>
							<hr class="isolate">
							<ul class="related-product-features">
								<?php
								foreach ($features as $feature)
								{
									echo '<li>' . htmlspecialchars($feature) . '</li>';
								}
								?>
							</ul>
							<div class="clearfix"></div>
						<?php endif; ?>
					<?php endif;

					$allow_checkout = $helper->config->get('allow_checkout');
					$compare_allow  = $helper->product->isComparable($item->id);
					$display_stock	= $this->helper->config->get('frontend_display_stock');

					if ($seller_attr->price_display == 0 || $displayquickviewbtn == '1'): ?>
						<div class="product-action-btn">
							<?php if ($allow_checkout && $seller_attr->price_display == 0): ?>
								<?php if ((int) $seller_attr->stock_capacity > 0): ?>
									<?php if ($displayaddtocartbtn == '1'): ?>
										<hr class="isolate">
										<button type="button" class="btn btn-primary btn-add-cart add" data-item="<?php echo $item->code; ?>">
											<i class="fa fa-shopping-cart"></i> <?php echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_ADD_TO_CART'); ?>
											<?php if ($display_stock):
												echo '('. (int) $seller_attr->stock_capacity . ')';
											endif ;?>
										</button>
									<?php endif; ?>

									<?php if ($displaybuynowbtn == '1'): ?>
										<button type="button" class="btn btn-default btn-add-cart buy" data-item="<?php echo $item->code; ?>" data-checkout="true">
											<i class="fa fa-bolt" aria-hidden="true"></i> <?php echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_BUY_NOW'); ?>
										</button>
									<?php endif; ?>
								<?php else: ?>
									<hr class="isolate">
									<button class="btn lbl-no-stock btn-primary">
										<i class="fa fa-times"></i> <?php echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_OUT_OF_STOCK'); ?>
									</button>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ($displayquickviewbtn == '1'): ?>
								<a href="#modal-<?php echo $item->code; ?>" role="button" data-toggle="modal" class="btn btn-default btn-quick-view">
									<i class="fa fa-search"></i> <?php echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_QUICK_VIEW'); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($compare_allow && $displaycomparebtn == '1'): ?>
						<label class="product-compare btn btn-default"><?php echo JText::_('MOD_SELLACIOUS_RELATEDPRODUCTS_COMPARE'); ?>
							<input type="checkbox" class="btn-compare" data-item="<?php echo $item->code; ?>" /></label>
					<?php endif; ?>

					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<div class="clearfix"></div>
</div>
