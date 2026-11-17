<?php
/*
 * General settings in the look of the Data Tables settings: a page head with Save and the options in cards. The fields
 * are the same as before (opt_values[...] names, values, data-optkey, the PRO class), only their layout is new.
 * Options of other modules (the optionsDefine filter) get a card of their own.
 */
$upsell = framePts::_()->getModule('upsell');
$isPro = framePts::_()->getModule('supsystic_promo')->isPro();
$cardsDef = [
  'editor' => [
    'icon' => 'fa-pencil-square-o',
    'title' => __('Table editor', PTS_LANG_CODE),
    'text' => __('How the editor saves your changes.', PTS_LANG_CODE),
    'opts' => ['disable_autosave'],
  ],
  'access' => [
    'icon' => 'fa-users',
    'title' => __('Access', PTS_LANG_CODE),
    'text' => __('Who can work with the plugin.', PTS_LANG_CODE),
    'opts' => ['access_roles'],
  ],
];
$cards = [];
$optToCard = [];
foreach ($cardsDef as $cKey => $c) {
  foreach ($c['opts'] as $o) {
    $optToCard[$o] = $cKey;
  }
}
foreach ($this->options as $optCatKey => $optCatData) {
  if (!isset($optCatData['opts']) || empty($optCatData['opts'])) {
    continue;
  }
  foreach ($optCatData['opts'] as $optKey => $opt) {
    if (empty($opt['html'])) {
      continue;
    }
    $cKey = isset($optToCard[$optKey]) ? $optToCard[$optKey] : 'cat_' . $optCatKey;
    if (!isset($cards[$cKey])) {
      $cards[$cKey] = isset($cardsDef[$cKey])
        ? $cardsDef[$cKey]
        : ['icon' => 'fa-sliders', 'title' => isset($optCatData['label']) ? $optCatData['label'] : __('More', PTS_LANG_CODE), 'text' => ''];
      $cards[$cKey]['rows'] = [];
    }
    $cards[$cKey]['rows'][$optKey] = $opt;
  }
}
// The order of the cards: editor and access first
uksort($cards, function ($a, $b) use ($cardsDef) {
  $order = array_keys($cardsDef);
  $ia = array_search($a, $order, true);
  $ib = array_search($b, $order, true);
  $ia = $ia === false ? 99 : $ia;
  $ib = $ib === false ? 99 : $ib;
  return $ia - $ib;
});
?>
<section class="pts-set-head">
	<span class="pts-set-head-icon"><i class="fa fa-cogs" aria-hidden="true"></i></span>
	<div class="pts-set-head-text">
		<h2><?php _e('Settings', PTS_LANG_CODE); ?></h2>
		<p><?php _e('These options apply to all your pricing tables.', PTS_LANG_CODE); ?></p>
	</div>
	<ul class="supsystic-bar-controls pts-set-actions">
		<li title="<?php _e('Save all options'); ?>">
			<button class="button button-primary" id="ptsSettingsSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', PTS_LANG_CODE); ?>
			</button>
		</li>
	</ul>
</section>
<section>
	<form id="ptsSettingsForm" class="ptsInputsWithDescrForm pts-set-form">
		<div class="pts-set-cards">
			<?php foreach ($cards as $cKey => $card) { ?>
				<?php $cardLocked = !$isPro && array_filter($card['rows'], function ($opt) {
      return isset($opt['pro']) && !empty($opt['pro']);
    }); ?>
				<div class="pts-set-card<?php echo $cardLocked ? ' is-locked' : ''; ?>" data-pts-set-card="<?php echo esc_attr($cKey); ?>">
					<div class="pts-set-card-head">
						<span class="pts-set-card-icon"><i class="fa <?php echo esc_attr($card['icon']); ?>" aria-hidden="true"></i></span>
						<div>
							<h3><?php echo esc_html($card['title']); ?></h3>
							<?php if (!empty($card['text'])) { ?><p><?php echo esc_html($card['text']); ?></p><?php } ?>
						</div>
					</div>
					<?php if ($cardLocked && $cKey === 'access' && $upsell) { ?>
						<?php echo $upsell->getProNote('access', __('Access for other user roles is in Pricing Table PRO and in Data Tables Pro.', PTS_LANG_CODE)); ?>
					<?php } ?>
					<?php foreach ($card['rows'] as $optKey => $opt) { ?>
						<?php
      $htmlType = $opt['html'];
      $htmlOpts = ['value' => $opt['value'], 'attrs' => 'data-optkey="' . $optKey . '"'];
      if (in_array($htmlType, ['selectbox', 'selectlist']) && isset($opt['options'])) {
        if (is_callable($opt['options'])) {
          $htmlOpts['options'] = call_user_func($opt['options']);
        } elseif (is_array($opt['options'])) {
          $htmlOpts['options'] = $opt['options'];
        }
      }
      $isProOpt = isset($opt['pro']) && !empty($opt['pro']);
      if ($isProOpt) {
        $htmlOpts['attrs'] .= ' class="ptsProOpt"';
      }
      $isWide = in_array($htmlType, ['selectlist', 'textarea'], true);
      ?>
						<div class="pts-set-row<?php echo $isWide ? ' is-wide' : ''; ?><?php echo $isProOpt && !$isPro ? ' is-locked' : ''; ?>">
							<div class="pts-set-label">
								<span class="pts-set-name"><?php echo $opt['label']; ?></span>
								<i class="fa fa-question supsystic-tooltip" title="<?php echo $opt['desc']; ?>"></i>
								<?php if ($isProOpt && !$isPro && !$upsell) { ?>
									<span class="ptsProOptMiniLabel">
										<a href="<?php echo $opt['pro']; ?>" target="_blank">
											<?php _e('PRO option', PTS_LANG_CODE); ?>
										</a>
									</span>
								<?php } ?>
								<?php if (!empty($opt['changed_on'])) { ?>
									<span class="description pts-set-changed">
										<?php $opt['value'] ? printf(__('Turned On %s', PTS_LANG_CODE), datePts::_($opt['changed_on'])) : printf(__('Turned Off %s', PTS_LANG_CODE), datePts::_($opt['changed_on'])); ?>
									</span>
								<?php } ?>
							</div>
							<div class="pts-set-control">
								<?php echo htmlPts::$htmlType('opt_values[' . $optKey . ']', $htmlOpts); ?>
							</div>
							<div id="ptsFormOptDetails_<?php echo $optKey; ?>" class="ptsOptDetailsShell">
							<?php switch ($optKey) {
         } ?>
							<?php if (isset($opt['add_sub_opts']) && !empty($opt['add_sub_opts'])) {
           if (is_string($opt['add_sub_opts'])) {
             echo $opt['add_sub_opts'];
           } elseif (is_callable($opt['add_sub_opts'])) {
             echo call_user_func_array($opt['add_sub_opts'], [$this->options]);
           }
         } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php echo htmlPts::hidden('mod', ['value' => 'options']); ?>
		<?php echo htmlPts::hidden('action', ['value' => 'saveGroup']); ?>
      <?php wp_nonce_field('pts_nonce', 'pts_nonce'); ?>
	</form>
</section>
