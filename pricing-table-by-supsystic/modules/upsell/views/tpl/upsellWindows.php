<?php
/**
 * PRO feature windows of the free version (upsellPts). One <dialog>; each feature is a <template> that upsell.js puts
 * into it. The pictures are small looped CSS scenes (css/upsell.css), still for visitors who turn off motion.
 *
 * @var array $features
 * @var upsellPts $upsell
 */
$features = $this->features;
$upsell = $this->upsell;
// A small pricing table of the pictures: [name, price, accent column]
$ptsUpMiniCol = function ($name, $price, $isAccent = false, $extra = '') {
  return '<span class="pts-up-col' . ($isAccent ? ' is-accent' : '') . ($extra ? ' ' . $extra : '') . '">' .
    '<b class="pts-up-col-name">' . esc_html($name) . '</b>' .
    '<span class="pts-up-col-price">' . $price . '</span>' .
    '<i></i><i></i><i></i>' .
    '<span class="pts-up-col-btn"></span>' .
    '</span>';
};
?>
<dialog class="pts-up" data-pts-up aria-labelledby="pts-up-title">
	<button type="button" class="pts-up-close" data-pts-up-close aria-label="<?php echo esc_attr__('Close', PTS_LANG_CODE); ?>">&times;</button>
	<div class="pts-up-body" data-pts-up-body></div>
</dialog>
<?php foreach ($features as $key => $f) { ?>
<template id="pts-up-<?php echo esc_attr($key); ?>">
	<div class="pts-up-hero">
		<span class="pts-up-eyebrow"><i class="fa fa-diamond" aria-hidden="true"></i><?php esc_html_e('PRO feature', PTS_LANG_CODE); ?></span>
		<div class="pts-up-head">
			<div>
				<h2 id="pts-up-title"><?php echo esc_html($f['title']); ?></h2>
				<p><?php echo esc_html($f['text']); ?></p>
			</div>
			<?php echo $upsell->getDtButton('pts-up-cta'); ?>
		</div>
	</div>
	<div class="pts-up-main">
		<div class="pts-up-demo" aria-hidden="true">
			<div class="pts-up-demo-bar"><i></i><i></i><i></i><span><?php esc_html_e('On your page', PTS_LANG_CODE); ?></span></div>
			<div class="pts-up-stage pts-up-stage-<?php echo esc_attr($key); ?>">
				<?php if ($key === 'templates') { ?>
					<span class="pts-up-tpls">
						<?php foreach (['is-a', 'is-b', 'is-c', 'is-d', 'is-e', 'is-f'] as $i => $tone) { ?>
							<span class="pts-up-tpl <?php echo esc_attr($tone); ?>" style="--pts-up-i: <?php echo (int) $i; ?>">
								<span></span><span class="is-mid"></span><span></span>
							</span>
						<?php } ?>
					</span>
					<span class="pts-up-badge"><i class="fa fa-diamond"></i> +37</span>
				<?php } elseif ($key === 'toggle') { ?>
					<span class="pts-up-switch"><span class="pts-up-switch-knob"></span><b><?php esc_html_e('Monthly', PTS_LANG_CODE); ?></b><b><?php esc_html_e('Yearly', PTS_LANG_CODE); ?></b></span>
					<span class="pts-up-pt">
						<?php
      echo $ptsUpMiniCol('Basic', '<span class="pts-up-m">$9</span><span class="pts-up-y">$90</span>');
      echo $ptsUpMiniCol('Plus', '<span class="pts-up-m">$19</span><span class="pts-up-y">$190</span>', true);
      echo $ptsUpMiniCol('Pro', '<span class="pts-up-m">$29</span><span class="pts-up-y">$290</span>');
      ?>
					</span>
				<?php } elseif ($key === 'schedule') { ?>
					<span class="pts-up-date"><i class="fa fa-calendar"></i> <?php esc_html_e('Nov 24 – Nov 30', PTS_LANG_CODE); ?></span>
					<span class="pts-up-pt">
						<?php
      echo $ptsUpMiniCol('Basic', '$9');
      echo $ptsUpMiniCol('Plus', '$19');
      echo $ptsUpMiniCol('Black Friday', '$7', true, 'is-timed');
      ?>
					</span>
				<?php } elseif ($key === 'ajax') { ?>
					<span class="pts-up-page">
						<span class="pts-up-line is-wide"></span>
						<span class="pts-up-line"></span>
						<span class="pts-up-load"><i class="fa fa-chevron-right"></i><?php esc_html_e('Our plans', PTS_LANG_CODE); ?><span class="pts-up-spin"></span></span>
						<span class="pts-up-reveal">
							<span class="pts-up-pt">
								<?php
        echo $ptsUpMiniCol('Basic', '$9');
        echo $ptsUpMiniCol('Plus', '$19', true);
        echo $ptsUpMiniCol('Pro', '$29');
        ?>
							</span>
						</span>
					</span>
				<?php } elseif ($key === 'access') { ?>
					<span class="pts-up-roles">
						<?php foreach ([[__('Administrator', PTS_LANG_CODE), 'is-on'], [__('Editor', PTS_LANG_CODE), 'is-turn'], [__('Shop manager', PTS_LANG_CODE), 'is-turn is-late'], [__('Author', PTS_LANG_CODE), '']] as $role) { ?>
							<span class="pts-up-role <?php echo esc_attr($role[1]); ?>"><i class="fa fa-user"></i><b><?php echo esc_html($role[0]); ?></b><span class="pts-up-role-sw"></span></span>
						<?php } ?>
					</span>
				<?php } else { ?>
					<span class="pts-up-icon"><i class="fa <?php echo esc_attr($f['icon']); ?>"></i></span>
				<?php } ?>
			</div>
		</div>
		<div class="pts-up-info">
			<h3><?php esc_html_e('What you get', PTS_LANG_CODE); ?></h3>
			<ul>
				<?php foreach ($f['points'] as $point) { ?><li><?php echo esc_html($point); ?></li><?php } ?>
			</ul>
			<h3><?php esc_html_e('Great for', PTS_LANG_CODE); ?></h3>
			<div class="pts-up-uses">
				<?php foreach ($f['uses'] as $use) { ?><span><?php echo esc_html($use); ?></span><?php } ?>
			</div>
		</div>
	</div>
	<div class="pts-up-dt">
		<span class="pts-up-dt-icon" aria-hidden="true"><i class="fa fa-table"></i></span>
		<div class="pts-up-dt-text">
			<b><?php esc_html_e('In Data Tables', PTS_LANG_CODE); ?></b>
			<span><?php echo esc_html($f['dt']); ?> <?php esc_html_e('Pricing tables, data tables and charts in one plugin; your tables move in one click.', PTS_LANG_CODE); ?></span>
		</div>
	</div>
	<?php echo $upsell->getDtCoupon('pts-up-coupon'); ?>
	<div class="pts-up-more">
		<span class="pts-up-more-label"><?php esc_html_e('Also in PRO', PTS_LANG_CODE); ?></span>
		<?php foreach ($features as $otherKey => $other) { ?>
			<?php if ($otherKey !== $key) { ?>
				<button type="button" data-pts-upsell="<?php echo esc_attr($otherKey); ?>"><i class="fa <?php echo esc_attr($other['icon']); ?>" aria-hidden="true"></i> <?php echo esc_html($other['chip']); ?></button>
			<?php } ?>
		<?php } ?>
	</div>
</template>
<?php } ?>
