<?php
/**
 * Review request card (reviewPts). "Is it working well?" leads to the WordPress.org review form or to support.
 * @var array $card
 */
$card = $this->card;
?>
<section class="pts-review<?php echo !empty($card['isOverview']) ? ' is-overview' : ''; ?>" data-pts-review data-ajax-url="<?php echo esc_url($card['ajaxUrl']); ?>" data-nonce="<?php echo esc_attr($card['nonce']); ?>" aria-label="<?php echo esc_attr__('Rate Pricing Table', PTS_LANG_CODE); ?>">
	<div class="pts-review-art" aria-hidden="true">
		<svg class="pts-review-icon" viewBox="0 0 48 48" width="48" height="48" focusable="false">
			<rect x="5" y="13" width="11" height="25" rx="3" fill="#fff" fill-opacity=".18" stroke="#fff" stroke-width="2.2"/>
			<rect x="18.5" y="8" width="11" height="32" rx="3" fill="#fff" fill-opacity=".85" stroke="#fff" stroke-width="2.2"/>
			<rect x="32" y="13" width="11" height="25" rx="3" fill="#fff" fill-opacity=".18" stroke="#fff" stroke-width="2.2"/>
			<path d="M21.5 15h5M21.5 20h5M21.5 25h5" stroke="#149b93" stroke-width="2.2" stroke-linecap="round"/>
		</svg>
		<span class="pts-review-badge">
			<svg viewBox="0 0 16 16" width="14" height="14" focusable="false"><path d="M3.5 8.3l3 3 6-6.6" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</span>
		<svg class="pts-review-spark is-one" viewBox="0 0 10 10" width="12" height="12" focusable="false"><path d="M5 0l1.2 3.8L10 5 6.2 6.2 5 10 3.8 6.2 0 5l3.8-1.2z" fill="#ffc53d"/></svg>
		<svg class="pts-review-spark is-two" viewBox="0 0 10 10" width="9" height="9" focusable="false"><path d="M5 0l1.2 3.8L10 5 6.2 6.2 5 10 3.8 6.2 0 5l3.8-1.2z" fill="#4ae8ea"/></svg>
		<svg class="pts-review-spark is-three" viewBox="0 0 10 10" width="7" height="7" focusable="false"><path d="M5 0l1.2 3.8L10 5 6.2 6.2 5 10 3.8 6.2 0 5l3.8-1.2z" fill="#ffc53d"/></svg>
	</div>

	<div class="pts-review-body" aria-live="polite">
		<div class="pts-review-step is-active" data-review-step="ask">
			<p class="pts-review-eyebrow"><?php echo esc_html($card['eyebrow']); ?></p>
			<h3 class="pts-review-title"><?php echo esc_html($card['title']); ?></h3>
			<p class="pts-review-text">
				<?php echo esc_html($card['text']); ?>
				<?php if (!empty($card['link'])) { ?>
					<a class="pts-review-inline-link" href="<?php echo esc_url($card['link']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($card['linkText']); ?> &rarr;</a>
				<?php } ?>
			</p>
			<div class="pts-review-question"><?php esc_html_e('Is Pricing Table working well for you?', PTS_LANG_CODE); ?></div>
			<div class="pts-review-actions">
				<button type="button" class="pts-review-btn is-primary" data-review-go="yes"><?php esc_html_e('Yes, it works great', PTS_LANG_CODE); ?></button>
				<button type="button" class="pts-review-btn" data-review-go="no"><?php esc_html_e('Not really', PTS_LANG_CODE); ?></button>
			</div>
		</div>

		<div class="pts-review-step" data-review-step="yes" hidden>
			<p class="pts-review-eyebrow"><?php esc_html_e('Glad to hear it', PTS_LANG_CODE); ?></p>
			<h3 class="pts-review-title"><?php esc_html_e('Would you rate Pricing Table on WordPress.org?', PTS_LANG_CODE); ?></h3>
			<p class="pts-review-text"><?php esc_html_e('It takes about a minute. Your review helps other site owners find the plugin and keeps us improving it.', PTS_LANG_CODE); ?></p>
			<div class="pts-review-stars" aria-hidden="true">
				<?php for ($i = 1; $i <= 5; $i++) { ?>
					<svg class="pts-review-star" style="--pts-star: <?php echo (int) $i; ?>" viewBox="0 0 24 24" width="26" height="26" focusable="false"><path d="M12 2.6l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.4l-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/></svg>
				<?php } ?>
			</div>
			<div class="pts-review-actions">
				<a class="pts-review-btn is-primary" href="<?php echo esc_url($card['reviewUrl']); ?>" target="_blank" rel="noopener noreferrer" data-review-answer="rate">
					<?php esc_html_e('Write a review', PTS_LANG_CODE); ?>
					<svg viewBox="0 0 16 16" width="13" height="13" aria-hidden="true" focusable="false"><path d="M6 3h7v7M13 3L5 11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</a>
				<button type="button" class="pts-review-btn" data-review-answer="later"><?php esc_html_e('Not now', PTS_LANG_CODE); ?></button>
				<button type="button" class="pts-review-link" data-review-answer="done"><?php esc_html_e('I already did', PTS_LANG_CODE); ?></button>
			</div>
		</div>

		<div class="pts-review-step" data-review-step="no" hidden>
			<p class="pts-review-eyebrow"><?php esc_html_e('We are here to help', PTS_LANG_CODE); ?></p>
			<h3 class="pts-review-title"><?php esc_html_e('Sorry it is not working the way you need', PTS_LANG_CODE); ?></h3>
			<p class="pts-review-text"><?php esc_html_e('Tell us what went wrong and our support team will help you sort it out.', PTS_LANG_CODE); ?></p>
			<div class="pts-review-actions">
				<a class="pts-review-btn is-primary" href="<?php echo esc_url($card['supportUrl']); ?>" target="_blank" rel="noopener noreferrer" data-review-answer="support"><?php esc_html_e('Contact support', PTS_LANG_CODE); ?></a>
				<button type="button" class="pts-review-btn" data-review-answer="later"><?php esc_html_e('Not now', PTS_LANG_CODE); ?></button>
				<a class="pts-review-link" href="<?php echo esc_url($card['reviewUrl']); ?>" target="_blank" rel="noopener noreferrer" data-review-answer="rate"><?php esc_html_e('Leave a review anyway', PTS_LANG_CODE); ?></a>
			</div>
		</div>

		<div class="pts-review-step" data-review-step="thanks" hidden>
			<p class="pts-review-eyebrow"><?php esc_html_e('Thank you', PTS_LANG_CODE); ?></p>
			<h3 class="pts-review-title"><?php esc_html_e('You made our day', PTS_LANG_CODE); ?></h3>
			<p class="pts-review-text"><?php esc_html_e('Every review helps the whole Supsystic team keep making Pricing Table better.', PTS_LANG_CODE); ?></p>
		</div>

		<div class="pts-review-step" data-review-step="support" hidden>
			<p class="pts-review-eyebrow"><?php esc_html_e('Thank you', PTS_LANG_CODE); ?></p>
			<h3 class="pts-review-title"><?php esc_html_e('We will get it sorted', PTS_LANG_CODE); ?></h3>
			<p class="pts-review-text"><?php esc_html_e('Our support page is open in a new tab. Describe the problem and we will get back to you.', PTS_LANG_CODE); ?></p>
		</div>
	</div>

	<button type="button" class="pts-review-close" data-review-answer="close" aria-label="<?php echo esc_attr__('Dismiss', PTS_LANG_CODE); ?>">
		<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" focusable="false"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
	</button>
</section>
