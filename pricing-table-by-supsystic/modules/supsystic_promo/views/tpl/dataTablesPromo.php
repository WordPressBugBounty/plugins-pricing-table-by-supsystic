<?php
$isPro = $this->isPro;
$couponCode = $this->couponCode;
$plans = $this->plans;
$videoUrl = $this->videoUrl;
$themesImgUrl = $this->themesImgUrl;
$ctaLink = $this->ctaLink;
?>
<div id="ptsDtPromoOverlay" class="pts-dt-promo-overlay">
	<div class="pts-dt-promo-modal" role="dialog" aria-modal="true" aria-labelledby="ptsDtPromoTitle">
		<button type="button" class="pts-dt-promo-close" id="ptsDtPromoClose" aria-label="<?php esc_attr_e('Close', PTS_LANG_CODE); ?>">&times;</button>
		<div class="pts-dt-promo-grid">
			<div class="pts-dt-promo-media">
				<div class="pts-dt-promo-video-wrap">
					<video class="pts-dt-promo-video" id="ptsDtPromoVideo" src="<?php echo esc_url($videoUrl); ?>" muted loop playsinline preload="metadata"></video>
				</div>
				<div class="pts-dt-promo-themes">
					<img src="<?php echo esc_url($themesImgUrl); ?>" alt="<?php esc_attr_e('Data Tables themes gallery', PTS_LANG_CODE); ?>" />
				</div>
			</div>
			<div class="pts-dt-promo-content">
				<div class="pts-dt-promo-eyebrow-row">
					<span class="pts-dt-promo-eyebrow"><?php esc_html_e('Pricing Table + Data Tables', PTS_LANG_CODE); ?></span>
					<span class="pts-dt-promo-badge"><?php echo $isPro ? esc_html__('50% OFF', PTS_LANG_CODE) : esc_html__('NEW OFFER', PTS_LANG_CODE); ?></span>
				</div>
				<h2 id="ptsDtPromoTitle" class="pts-dt-promo-title"><?php esc_html_e('Your Pricing Table has a bigger brother', PTS_LANG_CODE); ?></h2>
				<p class="pts-dt-promo-subtitle">
					<?php esc_html_e('Pricing Table and Data Tables are one Data Tables Toolkit, built on the same modern, friendly interface — toggle switchers, ajax loading and all.', PTS_LANG_CODE); ?>
				</p>
				<ul class="pts-dt-promo-features">
					<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Advanced data tables & WooCommerce product tables', PTS_LANG_CODE); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Native charts & diagrams', PTS_LANG_CODE); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Auto-updating tables from Google Sheets or a database', PTS_LANG_CODE); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Front-end editing, right on the page', PTS_LANG_CODE); ?></li>
				</ul>

				<div class="pts-dt-promo-offer">
					<div class="pts-dt-promo-offer-title">
						<?php
      if ($isPro) {
        esc_html_e('As a Pricing Table Pro user, save 50% on Data Tables Pro', PTS_LANG_CODE);
      } else {
        esc_html_e('Get Data Tables Pro for just $49 in your first year', PTS_LANG_CODE);
      }
      ?>
					</div>
					<div class="pts-dt-promo-plans pts-dt-promo-plans-count-<?php echo count($plans); ?>">
						<?php foreach ($plans as $plan) { ?>
							<div class="pts-dt-promo-plan<?php echo !empty($plan['highlight']) ? ' pts-dt-promo-plan-highlight' : ''; ?>">
								<?php if (!empty($plan['highlight'])) { ?>
									<span class="pts-dt-promo-plan-tag"><?php esc_html_e('Best value', PTS_LANG_CODE); ?></span>
								<?php } ?>
								<span class="pts-dt-promo-plan-term"><?php echo esc_html($plan['term']); ?></span>
								<span class="pts-dt-promo-plan-price">
									<span class="pts-dt-promo-price-old"><?php echo esc_html($plan['priceOld']); ?></span>
									<span class="pts-dt-promo-price-new"><?php echo esc_html($plan['priceNew']); ?></span>
								</span>
								<a href="<?php echo esc_url($plan['buyUrl']); ?>" target="_blank" rel="noopener" class="pts-dt-promo-plan-buy"><?php esc_html_e('Buy now', PTS_LANG_CODE); ?></a>
							</div>
						<?php } ?>
					</div>
					<div class="pts-dt-promo-coupon">
						<?php if ($isPro) { ?>
							<?php printf(esc_html__('Code %s is applied automatically — offer valid through October 10, 2026', PTS_LANG_CODE), '<strong>' . esc_html($couponCode) . '</strong>'); ?>
						<?php } else { ?>
							<?php printf(esc_html__('Code %1$s is applied automatically · renews at %2$s/year', PTS_LANG_CODE), '<strong>' . esc_html($couponCode) . '</strong>', '$59'); ?>
						<?php } ?>
					</div>
				</div>

				<p class="pts-dt-promo-reassurance">
					<?php esc_html_e('Your existing Pricing Tables stay exactly as they are — nothing to migrate or rebuild. This upgrade is entirely optional, so if Pricing Table already does the job, there is nothing you need to change.', PTS_LANG_CODE); ?>
				</p>

				<div class="pts-dt-promo-actions">
					<a href="<?php echo esc_url($ctaLink); ?>" target="_blank" rel="noopener" class="pts-dt-promo-learn-more"><?php esc_html_e('Learn more about Data Tables Pro', PTS_LANG_CODE); ?></a>
					<button type="button" class="pts-dt-promo-dismiss" id="ptsDtPromoDismiss"><?php esc_html_e('Maybe later', PTS_LANG_CODE); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
