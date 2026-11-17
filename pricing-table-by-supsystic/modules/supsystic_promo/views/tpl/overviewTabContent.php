<?php
/*
 * Overview in the look of the Data Tables and Photo Gallery overviews. Pricing Table is the classic plugin now, so the
 * page leads to Data Tables (tables, charts and pricing tables in one plugin) and to the move of the tables in one
 * click, the same for Free and PRO: the comparison, Data Tables Free vs Pro, the licenses and the code (PTPRO50 for
 * the owners of Pricing Table PRO, PTFREE20 for the others). Pricing Table PRO keeps all its features, License and updates.
 */
$isPro = framePts::_()->getModule('supsystic_promo')->isPro();
$upsell = framePts::_()->getModule('upsell');
$dt = $upsell ? $upsell->getDt() : null;
$options = framePts::_()->getModule('options');
$createUrl = $options->getTabUrl('tables_add_new');
$listUrl = $options->getTabUrl('tables');
$settingsUrl = $options->getTabUrl('settings');
$imgUrl = PTS_PLUGINS_URL . '/' . PTS_PLUG_NAME . '/modules/supsystic_promo/img/';
$dtPageUrl = 'https://supsystic.com/plugins/pricing-table/?utm_source=plugin&utm_medium=overview&utm_campaign=pricing_table';
$heroMock = '<div class="pts-ov-mock">' .
  '<div class="pts-ov-mock-switch"><b>Monthly</b><b>Yearly</b></div>' .
  '<div class="pts-ov-mock-cols">' .
  '<div class="pts-ov-mock-col"><b>Starter</b><strong>$9</strong><small>per month</small><i></i><i></i><i></i><span>Choose</span></div>' .
  '<div class="pts-ov-mock-col is-best"><em>Most popular</em><b>Growth</b><strong>$29</strong><small>per month</small><i></i><i></i><i></i><i></i><span>Choose</span></div>' .
  '<div class="pts-ov-mock-col"><b>Business</b><strong>$59</strong><small>per month</small><i></i><i></i><i></i><span>Choose</span></div>' .
  '</div></div>';
?>
<div class="pts-overview">
	<?php if ($dt) { ?>
		<section class="pts-overview-hero is-dt">
			<div class="pts-overview-hero-copy">
				<div class="pts-overview-kicker">
					<img src="<?php echo esc_url($imgUrl . 'plugin-icon.png'); ?>" alt="">
					<span><?php echo $dt['ptPro'] ? 'Pricing Table PRO by Supsystic · classic' : 'Pricing Table by Supsystic · classic'; ?></span>
				</div>
				<h1>Pricing tables are now part of <em>Data Tables</em>.</h1>
				<p>Data Tables Generator by Supsystic builds pricing tables, data tables, WooCommerce product tables and charts in one plugin: a drag-and-drop pricing builder with themes, a Gutenberg block and an Elementor widget. Move your pricing tables there in one click; they keep their look.<?php echo $dt['ptPro'] ? ' Your Pricing Table PRO keeps working as it is.' : ''; ?></p>
				<div class="pts-overview-actions">
					<?php echo $upsell->getDtButton('is-hero'); ?>
					<a class="button button-secondary button-hero" href="#pts-compare"><i class="fa fa-balance-scale"></i> Compare</a>
				</div>
				<div class="pts-overview-trust">
					<span><i class="fa fa-check"></i> Free on WordPress.org</span>
					<span><i class="fa fa-check"></i> Your tables move in one click</span>
					<span><i class="fa fa-check"></i> Shortcodes are replaced only when you choose</span>
				</div>
			</div>
			<div class="pts-overview-hero-media is-pricing" aria-hidden="true">
				<span class="pts-ov-dt-badge"><i class="fa fa-table"></i> Data Tables</span>
				<?php echo $heroMock; ?>
			</div>
		</section>
		<?php echo $upsell->getDtCoupon('pts-overview-coupon'); ?>
	<?php } else { ?>
		<section class="pts-overview-hero">
			<div class="pts-overview-hero-copy">
				<div class="pts-overview-kicker">
					<img src="<?php echo esc_url($imgUrl . 'plugin-icon.png'); ?>" alt="">
					<span><?php echo $isPro ? 'Pricing Table PRO by Supsystic' : 'Pricing Table by Supsystic'; ?></span>
				</div>
				<h1>Pricing tables that make the right plan easy to pick.</h1>
				<p>Build responsive pricing tables and feature comparison tables right on the table: type your prices, mark the plan you want to sell with a badge, add icons, tooltips and buttons, and publish with a shortcode.</p>
				<div class="pts-overview-actions">
					<a class="button button-primary button-hero" href="<?php echo esc_url($createUrl); ?>"><i class="fa fa-plus"></i> Add New Table</a>
					<a class="button button-secondary button-hero" href="<?php echo esc_url($listUrl); ?>"><i class="fa fa-list"></i> All tables</a>
				</div>
				<div class="pts-overview-trust">
					<span><i class="fa fa-check"></i> No code required</span>
					<span><i class="fa fa-check"></i> Responsive on every screen</span>
					<span><i class="fa fa-check"></i> Unlimited tables, columns and rows</span>
				</div>
			</div>
			<div class="pts-overview-hero-media is-pricing" aria-hidden="true"><?php echo $heroMock; ?></div>
		</section>
	<?php } ?>

	<?php if ($dt) { ?>
		<section class="pts-overview-wide">
			<?php include __DIR__ . DIRECTORY_SEPARATOR . 'overviewProOffer.php'; ?>
		</section>
	<?php } ?>

	<section class="pts-overview-layout">
		<div class="pts-overview-main">
			<section id="pts-classic" class="pts-overview-card pts-ov-classic">
				<div class="pts-overview-card-head"><div><span class="pts-overview-pill"><?php echo $isPro ? 'Your Pricing Table PRO' : 'Your classic Pricing Table'; ?></span><h2>Nothing changes for your tables.</h2></div></div>
				<ul class="pts-ov-classic-list">
					<li><i class="fa fa-check"></i> Your tables and their shortcodes keep working on every page.</li>
					<li><i class="fa fa-check"></i> You can still edit them here and add new ones.</li>
					<?php if ($isPro) { ?>
						<li><i class="fa fa-check"></i> Your Pricing Table PRO keeps its 44 templates, the Monthly / Yearly switch, scheduled columns, loading on click, access roles, updates and support.</li>
					<?php } else { ?>
						<li><i class="fa fa-check"></i> Owners of a Pricing Table PRO license keep its templates, the Monthly / Yearly switch, scheduled columns, loading on click, access roles and updates.</li>
					<?php } ?>
					<li><i class="fa fa-check"></i> Moving to Data Tables copies your tables; the originals stay here.</li>
				</ul>
				<div class="pts-ov-classic-actions">
					<a class="button button-primary" href="<?php echo esc_url($createUrl); ?>"><i class="fa fa-plus"></i> Add New Table</a>
					<a class="button" href="<?php echo esc_url($listUrl); ?>"><i class="fa fa-list"></i> All tables</a>
				</div>
			</section>

			<section id="pts-build-suite" class="pts-overview-suite is-gallery-suite">
				<div class="pts-overview-suite-head">
					<div class="pts-overview-suite-icon"><i class="fa fa-table"></i></div>
					<div><span class="pts-overview-pill">Classic editor</span><h2>Edit the table right where visitors will see it.</h2><p>Every text, price, icon and button is edited in place. Add columns and rows in one click, drag them into order and see the result at once.</p></div>
				</div>
				<div class="pts-overview-capability-grid">
					<article><i class="fa fa-pencil"></i><h3>Visual editor</h3><p>Click any cell to change its text, font, color and size. Undo and redo keep every step safe.</p></article>
					<article><i class="fa fa-th-large"></i><h3>Templates</h3><p><?php echo $isPro ? '44 templates for hosting, SaaS, shops, restaurants and teams.' : '7 templates to start from; switch a table to another one at any time.'; ?></p></article>
					<article><i class="fa fa-columns"></i><h3>Vertical and horizontal</h3><p>Plans as columns or as rows, with an optional description column, head, footer and description rows.</p></article>
					<article><i class="fa fa-certificate"></i><h3>Badges and buttons</h3><p>Mark the plan to sell with a badge, add buy buttons with any link, icons and tooltips.</p></article>
					<article><i class="fa fa-mobile"></i><h3>Responsive tables</h3><p>On phones the columns line up one after another; choose the width at which this happens.</p></article>
					<article><i class="fa fa-code"></i><h3>CSS, HTML and export</h3><p>Edit the CSS and HTML of a table, clone it, and move tables between sites with JSON export and import.</p></article>
					<?php if ($isPro) { ?>
						<article><i class="fa fa-toggle-on"></i><h3>Monthly / yearly switch</h3><p>One switch changes every price of the table. Name the options yourself and choose the first one.</p></article>
						<article><i class="fa fa-calendar"></i><h3>Scheduled columns</h3><p>Give a plan a show-from and a show-until date: a holiday offer appears and goes away on its own.</p></article>
						<article><i class="fa fa-bolt"></i><h3>Load on click</h3><p>The table waits behind its title and loads only when a visitor opens it.</p></article>
						<article><i class="fa fa-users"></i><h3>Access for your team</h3><p>Let editors and other roles update the prices without full admin rights.</p></article>
					<?php } ?>
				</div>
			</section>

			<section id="pts-overview-videos" class="pts-overview-video-grid">
				<article class="pts-overview-card">
					<div class="pts-overview-card-head"><div><span class="pts-overview-pill">Learn</span><h2>Video tutorial</h2></div></div>
					<div class="pts-overview-video-frame"><iframe title="Pricing Table tutorial" src="https://www.youtube.com/embed/v8h2k3vvpdM" frameborder="0" allowfullscreen loading="lazy"></iframe></div>
				</article>
				<article class="pts-overview-card">
					<div class="pts-overview-card-head"><div><span class="pts-overview-pill">Discover</span><h2>Supsystic overview</h2></div></div>
					<div class="pts-overview-video-frame"><iframe title="Supsystic overview" src="https://www.youtube.com/embed/dKd_9g6JzfU" frameborder="0" allowfullscreen loading="lazy"></iframe></div>
				</article>
			</section>

			<section id="pts-overview-server" class="pts-overview-card">
				<div class="pts-overview-card-head"><div><span class="pts-overview-pill">Diagnostics</span><h2>Server settings</h2></div><p>Useful environment details for troubleshooting.</p></div>
				<div class="pts-overview-server-grid">
					<?php foreach ($this->serverSettings as $title => $element) { ?>
						<div class="pts-overview-server-item<?php echo !empty($element['error']) ? ' is-error' : ''; ?>"><span><?php echo esc_html(trim($title)); ?></span><strong><?php echo esc_html(trim((string) $element['value'])); ?></strong></div>
					<?php } ?>
				</div>
			</section>
		</div>

		<aside class="pts-overview-sidebar">
			<?php if ($dt) { ?>
				<section class="pts-overview-card pts-ov-dt-card">
					<span class="pts-overview-pill">Data Tables</span><h2>Move to Data Tables</h2>
					<p>Tables, charts and pricing tables in one plugin. Your pricing tables move in one click.</p>
					<?php echo $upsell->getDtButton('is-block'); ?>
				</section>
			<?php } ?>
			<section class="pts-overview-card pts-overview-resource-card">
				<span class="pts-overview-pill">Resources</span><h2>Useful links</h2>
				<a class="pts-overview-resource-link" href="<?php echo esc_url($createUrl); ?>"><i class="fa fa-plus-circle"></i> Create a new table</a>
				<a class="pts-overview-resource-link" href="<?php echo esc_url($listUrl); ?>"><i class="fa fa-list"></i> Manage tables</a>
				<a class="pts-overview-resource-link" href="<?php echo esc_url($settingsUrl); ?>"><i class="fa fa-cog"></i> Settings</a>
				<a class="pts-overview-resource-link" href="#pts-build-suite"><i class="fa fa-table"></i> Editor features</a>
				<a class="pts-overview-resource-link" href="#pts-overview-videos"><i class="fa fa-play"></i> Video tutorials</a>
				<a class="pts-overview-resource-link" href="#pts-overview-server"><i class="fa fa-cog"></i> Server settings</a>
				<a class="pts-overview-resource-link" href="https://supsystic.com/docs/pricing-tables/?utm_source=plugin&amp;utm_medium=overview&amp;utm_campaign=pricing_table" target="_blank" rel="noopener"><i class="fa fa-book"></i> Documentation</a>
				<a class="pts-overview-resource-link" href="<?php echo esc_url($dtPageUrl); ?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Pricing tables in Data Tables</a>
			</section>

			<section id="pts-overview-support" class="pts-overview-card pts-overview-support-card">
				<span class="pts-overview-pill">Support</span><h2>Need help with a table?</h2><p>Send us your question, issue or feature request. The Supsystic support team will help you choose the right setup.</p>
				<a class="button button-primary" href="https://supsystic.com/contact-us/" target="_blank" rel="noopener"><i class="fa fa-life-ring"></i> Contact support</a>
			</section>
		</aside>
	</section>
</div>
