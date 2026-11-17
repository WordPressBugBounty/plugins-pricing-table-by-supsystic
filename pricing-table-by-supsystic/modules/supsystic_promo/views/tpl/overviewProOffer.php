<?php
/*
 * The offer of the Overview (Free and PRO): the classic plugin next to Data Tables, then Data Tables Free vs Data
 * Tables Pro and the licenses of Data Tables Pro, both as on https://supsystic.com/plugins/pricing-table/ (the page
 * sells the pricing tables of Data Tables). Buy links lead to Data Tables Pro with the code of the user (PTPRO50 with
 * Pricing Table PRO, PTFREE20 without); the code is not shown while a licensed Data Tables Pro runs on the site.
 * With a code every price shows the price of the page struck through and the price with the discount (rounded up to a
 * whole dollar by upsellPts::getDtDiscountPrice), every card names the code, and every Buy button puts its own license
 * (pid) in the cart with the code.
 */
$upsell = framePts::_()->getModule('upsell');
$dt = $upsell->getDt();
$ptProActive = framePts::_()->getModule('supsystic_promo')->isPro();
$coupon = $dt['proActive'] ? '' : $dt['coupon'];
$discount = $coupon ? (int) $dt['discount'] : 0;
$buy = function ($pid) use ($coupon) {
  return 'https://supsystic.com/?mod=user&addQty=1&goto=checkout&lang=en&action=addToCart&pid=' . (int) $pid . ($coupon ? '&coupon=' . rawurlencode($coupon) : '') . '&utm_source=plugin&utm_medium=overview_prices&utm_campaign=pricing_table';
};
// '$1,199' -> 1199.0; the price after the discount comes from the upsell module (rounded up: $49 -50% -> $25)
$amount = function ($price) {
  return (float) preg_replace('/[^0-9.]/', '', $price);
};
$withDiscount = function ($price) use ($upsell, $discount) {
  return $upsell->getDtDiscountPrice($price, $discount);
};
$deal = $coupon ? '−' . $discount . '% · ' . $coupon : '';
$yes = '<i class="fa fa-check" aria-label="Yes"></i>';
$no = '<i class="fa fa-minus" aria-label="No"></i>';
$compare = [
  ['Pricing tables with plans, badges and buy buttons', $yes, $yes],
  ['Editor', 'In place, cell by cell', 'Drag-and-drop builder with 13 blocks'],
  ['Designs', $ptProActive ? '44 templates' : '7 templates', '3 free themes, 100+ in Pro'],
  ['Desktop, tablet and phone preview', $no, $yes],
  ['Countdowns and sale prices', $no, $yes],
  ['Gutenberg block and Elementor widget', 'Shortcode', $yes],
  ['Data tables, WooCommerce product tables, charts', $no, $yes],
  ['Monthly / yearly switch, scheduled plans, load on click', $ptProActive ? 'Yes, in your PRO' : 'Pricing Table PRO', 'Data Tables Pro'],
  ['New features', 'Maintenance only', 'Active development'],
];
$licences = [
  'single' => ['name' => 'Personal', 'sites' => '1 site', 'note' => '1 Site License', 'terms' => [['1 Year', '$49', '/ year', '', 3443], ['3 Years', '$129', '/ 3 years', '', 22663], ['Lifetime', '$199', 'Pay once, use forever', '', 24158]]],
  'dev' => ['name' => 'Multi-site', 'sites' => 'Up to 5 sites', 'note' => 'Up to 5 Sites', 'terms' => [['1 Year', '$119', '/ year', '', 5890], ['3 Years', '$299', '/ 3 years', 'That\'s only $99/year', 22664], ['Lifetime', '$599', 'Pay once, use forever', '', 24159]]],
  'enterprise' => ['name' => 'Agency', 'sites' => 'Unlimited sites', 'note' => 'Unlimited Sites', 'terms' => [['1 Year', '$229', '/ year', '', 5907], ['3 Years', '$569', '/ 3 years', 'That\'s only $189/year', 22666], ['Lifetime', '$1,199', 'Pay once, use forever', '', 24188]]],
];
?>
<div id="pts-compare" class="pts-offer">
	<div class="pts-offer-head">
		<span class="pts-offer-label">Pricing Table or Data Tables</span>
		<h2>Your pricing tables, <em>plus everything Data Tables does.</em></h2>
		<p>Pricing Table keeps working as the classic plugin. Data Tables is where pricing tables get new features: a drag-and-drop builder, themes and previews for every device.</p>
	</div>
	<div class="pts-cmp" role="table" aria-label="Pricing Table or Data Tables">
		<div class="pts-cmp-row is-head" role="row">
			<span role="columnheader"></span>
			<span role="columnheader"><?php echo $ptProActive ? 'Pricing Table PRO' : 'Pricing Table'; ?> <small>classic</small></span>
			<span role="columnheader" class="is-dt">Data Tables <small>free on WordPress.org</small></span>
		</div>
		<?php foreach ($compare as $row) { ?>
			<div class="pts-cmp-row" role="row">
				<span role="rowheader"><?php echo esc_html($row[0]); ?></span>
				<span role="cell"><?php echo strpos($row[1], '<i') === 0 ? $row[1] : esc_html($row[1]); ?></span>
				<span role="cell" class="is-dt"><?php echo strpos($row[2], '<i') === 0 ? $row[2] : esc_html($row[2]); ?></span>
			</div>
		<?php } ?>
	</div>
	<div class="pts-offer-move">
		<?php echo $upsell->getDtButton(); ?>
		<span>Your tables are copied to Data Tables with their look; the originals stay here.</span>
	</div>

	<div class="pts-offer-head is-second">
		<span class="pts-offer-label">Data Tables Free vs Data Tables Pro</span>
		<h2>A free pricing table plugin. <em>Pro when you need more.</em></h2>
		<p>Data Tables Free has no limits on tables, plans or blocks. Data Tables Pro adds the themes and tools that help a pricing page sell.</p>
	</div>
	<div class="pts-offer-grid">
		<div class="pts-offer-card">
			<div class="pts-offer-card-head"><b>Data Tables Free</b><span>$0 · on WordPress.org</span></div>
			<ul class="pts-offer-checks">
				<li>Unlimited pricing tables, plans and features</li>
				<li>Drag-and-drop builder with all 13 blocks</li>
				<li>3 themes: Classic, Minimal and Warm</li>
				<li>Badges, sale prices, buttons and countdowns</li>
				<li>Desktop, tablet and mobile preview</li>
				<li>780+ icons and 700+ Google Fonts</li>
				<li>HTML and CSS panels, clone, JSON export</li>
				<li>Gutenberg block, Elementor widget, shortcode</li>
			</ul>
			<?php echo $upsell->getDtButton('is-light'); ?>
		</div>
		<div class="pts-offer-card is-pro">
			<div class="pts-offer-card-head"><b>Data Tables Pro</b>
					<?php if ($coupon) { ?>
						<span>from <s>$49</s> <?php echo esc_html($withDiscount('$49')); ?>/year · or <s>$199</s> <?php echo esc_html($withDiscount('$199')); ?> lifetime</span>
						<em class="pts-offer-deal"><?php echo esc_html($deal); ?></em>
					<?php } else { ?>
						<span>from $49/year · or $199 lifetime</span>
					<?php } ?>
				</div>
			<p class="pts-offer-plus">Everything in Free, plus:</p>
			<ul class="pts-offer-checks">
				<li>100+ premium themes for every niche and season</li>
				<li>Monthly / yearly switch with your own periods</li>
				<li>Scheduled plans with show-from and show-until dates</li>
				<li>Load the table on click</li>
				<li>All Data Tables Pro: import, export, charts, smart filters, WooCommerce extras</li>
				<li>Premium support and updates</li>
			</ul>
			<a class="pts-offer-btn" href="#pts-prices">View license options <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
		</div>
	</div>

	<div id="pts-prices" class="pts-prices" data-pts-prices>
		<div class="pts-offer-head is-second">
			<span class="pts-offer-label">Data Tables Pro licenses</span>
			<h2>Choose your license and get started</h2>
			<p>Every license unlocks all of Data Tables Pro, premium pricing themes included. Licenses differ only in the number of sites.</p>
		</div>
		<?php if ($coupon) { ?>
			<?php echo $upsell->getDtCoupon('pts-prices-coupon'); ?>
		<?php } ?>
		<div class="pts-prices-tabs" role="tablist">
			<?php $first = true; foreach ($licences as $key => $lic) { ?>
				<button type="button" role="tab" class="pts-prices-tab<?php echo $first ? ' is-active' : ''; ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" data-licence="<?php echo esc_attr($key); ?>"><b><?php echo esc_html($lic['name']); ?></b><span><?php echo esc_html($lic['sites']); ?></span></button>
			<?php $first = false; } ?>
		</div>
		<?php $first = true; foreach ($licences as $key => $lic) { ?>
			<div class="pts-prices-panel" role="tabpanel" data-licence="<?php echo esc_attr($key); ?>"<?php echo $first ? '' : ' hidden'; ?>>
				<?php foreach ($lic['terms'] as $i => $term) { ?>
					<div class="pts-price-card is-term-<?php echo (int) $i; ?><?php echo $coupon ? ' has-deal' : ''; ?>">
						<div class="pts-price-head"><?php echo esc_html($term[0]); ?></div>
						<div class="pts-price-body">
							<?php if ($coupon) { ?>
								<span class="pts-price-deal"><?php echo esc_html($deal); ?></span>
								<s class="pts-price-was"><?php echo esc_html($term[1]); ?></s>
								<strong><?php echo esc_html($withDiscount($term[1])); ?></strong>
							<?php } else { ?>
								<strong><?php echo esc_html($term[1]); ?></strong>
							<?php } ?>
							<span><?php echo esc_html($term[2]); ?></span>
							<?php if ($term[3]) { ?>
								<small><?php echo esc_html($coupon ? sprintf('That\'s only $%s/year', number_format(ceil($amount($withDiscount($term[1])) / 3))) : $term[3]); ?></small>
							<?php } ?>
							<ul>
								<li><?php echo esc_html($lic['note']); ?></li>
								<li>All Pro Features</li>
								<li>Premium Support</li>
							</ul>
						</div>
						<a class="pts-price-buy" href="<?php echo esc_url($buy($term[4])); ?>" target="_blank" rel="noopener">Buy Now</a>
						<?php if ($coupon) { ?>
							<span class="pts-price-coupon"><?php echo esc_html(sprintf('−%d%% with %s at checkout', $discount, $coupon)); ?></span>
						<?php } ?>
						<span class="pts-price-note">30-Day Money Back Guarantee</span>
					</div>
				<?php } ?>
			</div>
		<?php $first = false; } ?>
	</div>
</div>
