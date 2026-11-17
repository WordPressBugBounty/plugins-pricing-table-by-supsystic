<?php
/**
 * The banner at the top of every Pricing Table page (Free and PRO): pricing tables are now part of Data Tables. One
 * button by the state of Data Tables (install, activate, update, move the tables, open them), what stays, and the code
 * on Data Tables Pro (PTPRO50 with Pricing Table PRO, PTFREE20 without; none with a licensed Data Tables Pro).
 * The banner cannot be closed: it folds into a one-line strip (the mark, the title, the code and the unfold button),
 * kept for every user in user meta (upsellPts::ajaxFoldDtBanner). The look is the Contact Form → CES banner.
 */
$dt = $this->dt;
$upsell = $this->upsell;
$folded = !empty($this->folded);
$progress = $dt['total'] > 0 && $dt['moved'] > 0;
?>
<section class="pts-dt-banner<?php echo $folded ? ' is-folded' : ''; ?>" data-pts-dt-banner data-ajax-url="<?php echo esc_url($this->ajaxUrl); ?>" data-nonce="<?php echo esc_attr($this->foldNonce); ?>" aria-label="<?php echo esc_attr__('Pricing tables are now part of Data Tables', PTS_LANG_CODE); ?>">
	<div class="pts-dt-banner-art" aria-hidden="true">
		<span class="pts-dt-banner-node is-price"><i class="fa fa-tag"></i></span>
		<span class="pts-dt-banner-node is-table"><i class="fa fa-table"></i></span>
		<span class="pts-dt-banner-node is-chart"><i class="fa fa-bar-chart"></i></span>
		<span class="pts-dt-banner-node is-shop"><i class="fa fa-shopping-cart"></i></span>
		<span class="pts-dt-banner-core">DT</span>
	</div>
	<div class="pts-dt-banner-body">
		<p class="pts-dt-banner-eyebrow"><i class="fa fa-rocket" aria-hidden="true"></i><span><?php esc_html_e('Pricing tables are now part of Data Tables', PTS_LANG_CODE); ?></span></p>
		<h3 class="pts-dt-banner-title"><?php esc_html_e('Pricing tables, data tables, WooCommerce tables and charts in one free plugin', PTS_LANG_CODE); ?></h3>
		<p class="pts-dt-banner-text">
			<?php if ($dt['ptPro']) { ?>
				<?php esc_html_e('Move your pricing tables to Data Tables Generator in one click: a drag-and-drop pricing builder, themes and a preview for every device. Your Pricing Table PRO keeps working as it is.', PTS_LANG_CODE); ?>
			<?php } else { ?>
				<?php esc_html_e('Move your pricing tables to Data Tables Generator in one click: a drag-and-drop pricing builder, themes and a preview for every device. Your tables here keep working as they are.', PTS_LANG_CODE); ?>
			<?php } ?>
		</p>
		<?php if ($progress) { ?>
			<div class="pts-dt-banner-progress">
				<span class="pts-dt-banner-bar"><span style="width: <?php echo (int) min(100, round(100 * $dt['moved'] / $dt['total'])); ?>%"></span></span>
				<span><?php
				/* translators: 1: moved tables, 2: all tables */
				echo esc_html(sprintf(__('%1$d of %2$d tables moved to Data Tables', PTS_LANG_CODE), $dt['moved'], $dt['total']));
				?></span>
			</div>
		<?php } ?>
		<div class="pts-dt-banner-actions">
			<?php echo $upsell->getDtButton('is-banner'); ?>
			<a class="pts-dt-banner-link" href="<?php echo esc_url($this->overviewUrl . '#pts-compare'); ?>"><?php esc_html_e('What changes?', PTS_LANG_CODE); ?></a>
		</div>
		<?php echo $upsell->getDtCoupon('is-banner'); ?>
	</div>
	<button type="button" class="pts-dt-banner-fold" data-pts-dt-fold aria-expanded="<?php echo $folded ? 'false' : 'true'; ?>" title="<?php echo $folded ? esc_attr__('Show more', PTS_LANG_CODE) : esc_attr__('Show less', PTS_LANG_CODE); ?>" data-title-more="<?php echo esc_attr__('Show more', PTS_LANG_CODE); ?>" data-title-less="<?php echo esc_attr__('Show less', PTS_LANG_CODE); ?>">
		<i class="fa fa-angle-up" aria-hidden="true"></i>
	</button>
</section>
