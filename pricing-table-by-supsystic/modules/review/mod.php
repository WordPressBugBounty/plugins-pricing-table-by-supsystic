<?php
/**
 * Asks for a review on WordPress.org once the plugin has done something useful for the site, the same way as Data
 * Tables and Photo Gallery: a pricing table went live on a published page, a Monthly / Yearly switch went live (PRO),
 * several tables were built or, as a fallback, two weeks of use. The card is shown inside the plugin's own pages
 * only, to the users allowed to use the plugin, and it is not shown again once it is answered.
 *
 * An add-on can report its own moment with do_action('pts_review_moment', $type, ['table' => $id]).
 * The rate notice of old versions (options start_usage, done_rate_msg, hide_rate_msg) is taken into account.
 */
class reviewPts extends modulePts
{
  const KEY = 'pts_review';
  const REVIEW_URL = 'https://wordpress.org/support/plugin/pricing-table-by-supsystic/reviews/#new-post';
  const SUPPORT_URL = 'https://supsystic.com/contact-us/';
  const ASSETS_VERSION = 'rv-3';
  // No card in the first days after install, whatever happens
  const MIN_DAYS = 3;
  // Without any moment the card comes after two weeks of use
  const FALLBACK_DAYS = 14;
  const TABLES_MILESTONE = 3;
  const SNOOZE_DAYS = 30;
  const MAX_SNOOZES = 3;
  /**
   * Moments in the order the card picks them: the first one recorded wins
   * @var string[]
   */
  private $_momentTypes = ['live', 'toggle'];
  /** @var array|null */
  private $_state = null;
  /** @var array|null|false False until the card is built for the current request */
  private $_card = false;
  public function init()
  {
    parent::init();
    add_filter('do_shortcode_tag', [$this, 'onShortcodeOutput'], 10, 3);
    add_action('wp_after_insert_post', [$this, 'onPostSaved'], 10, 2);
    add_action('pts_review_moment', [$this, 'recordMoment'], 10, 2);
    add_action('wp_ajax_pts_review_respond', [$this, 'ajaxRespond']);
    if (is_admin()) {
      add_filter('plugin_row_meta', [$this, 'addPluginRowLink'], 10, 2);
      add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
      // The plugin page prints it at the top of its content panel
      dispatcherPts::addAction('adminContentTop', [$this, 'printCard']);
    }
  }
  /**
   * A pricing table is rendered on a published page that a visitor (or the site owner) opens: the table is live.
   * @param string|false $output
   * @param string $tag
   * @param array|string $attr
   * @return string|false
   */
  public function onShortcodeOutput($output, $tag, $attr)
  {
    if ($tag !== PTS_SHORTCODE || !is_string($output) || !is_array($attr) || empty($attr['id'])) {
      return $output;
    }
    $needLive = !$this->_hasMoment('live');
    $needToggle = !$this->_hasMoment('toggle') && $this->_isPro();
    if (!$needLive && !$needToggle) {
      return $output;
    }
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || is_preview() || is_feed() || is_customize_preview() || isset($_GET['elementor-preview'])) {
      return $output;
    }
    // A table, or its title that loads it on click (PRO)
    if (!is_singular() || (strpos($output, 'ptsBlock') === false && strpos($output, 'ptsToggleAjax') === false)) {
      return $output;
    }
    $post = get_queried_object();
    if (!$post instanceof WP_Post || $post->post_status !== 'publish' || post_password_required($post)) {
      return $output;
    }
    $data = ['table' => (int) $attr['id'], 'post' => (int) $post->ID];
    if ($needLive) {
      $this->_addMoment('live', $data);
    }
    if ($needToggle && $this->_tableHasSwitch((int) $attr['id'])) {
      $this->_addMoment('toggle', $data);
    }
    return $output;
  }
  /**
   * A post with a pricing table shortcode is published: the table is live even before anyone opens the page.
   * @param int $postId
   * @param WP_Post $post
   */
  public function onPostSaved($postId, $post)
  {
    if (!$post instanceof WP_Post || $post->post_status !== 'publish' || $this->_hasMoment('live')) {
      return;
    }
    if (wp_is_post_revision($postId) || wp_is_post_autosave($postId) || !is_post_type_viewable($post->post_type)) {
      return;
    }
    $tableId = $this->_findTableIn((string) $post->post_content);
    if ($tableId) {
      $this->_addMoment('live', ['table' => $tableId, 'post' => (int) $postId]);
    }
  }
  /**
   * Handler of the "pts_review_moment" action
   * @param string $type One of the moment types
   * @param array $data ['table' => id]
   */
  public function recordMoment($type, $data = [])
  {
    if (!is_string($type) || $type === 'live' || !in_array($type, $this->_momentTypes, true) || $this->_hasMoment($type)) {
      return;
    }
    $this->_addMoment($type, ['table' => is_array($data) && isset($data['table']) ? (int) $data['table'] : 0]);
  }
  public function ajaxRespond()
  {
    check_ajax_referer(self::KEY, '_wpnonce');
    if (!$this->_currentUserCanUsePlugin()) {
      wp_send_json_error(null, 403);
    }
    $answer = isset($_POST['answer']) && is_string($_POST['answer']) ? sanitize_key(wp_unslash($_POST['answer'])) : '';
    if (!$this->saveAnswer(get_current_user_id(), $answer)) {
      wp_send_json_error(null, 400);
    }
    wp_send_json_success();
  }
  /**
   * The card for the current user on the current page, or null when there is nothing to show
   * @return array|null
   */
  public function getCard()
  {
    if ($this->_card !== false) {
      return $this->_card;
    }
    $this->_card = null;
    if (!framePts::_()->isAdminPlugOptsPage() || !apply_filters('pts_review_request', true)) {
      return null;
    }
    $options = framePts::_()->getModule('options');
    $tab = $options ? $options->getActiveTab() : '';
    if (!in_array($tab, ['overview', 'tables', 'settings'], true)) {
      return null;
    }
    $userId = get_current_user_id();
    if (!$userId || !$this->_currentUserCanUsePlugin()) {
      return null;
    }
    $answer = $this->getAnswer($userId);
    if ($answer['status'] === 'rated' || $answer['status'] === 'dismissed') {
      return null;
    }
    if ($answer['status'] === 'snoozed' && $answer['until'] > time()) {
      return null;
    }
    $state = $this->getState();
    if (time() - $state['since'] < self::MIN_DAYS * DAY_IN_SECONDS) {
      return null;
    }
    $moment = $this->_pickMoment($state);
    if (!$moment) {
      return null;
    }
    $this->_card = array_merge($moment, [
      'reviewUrl' => self::REVIEW_URL,
      'supportUrl' => self::SUPPORT_URL,
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce(self::KEY),
      'isOverview' => $tab === 'overview',
    ]);
    return $this->_card;
  }
  public function enqueueAssets()
  {
    if (!$this->getCard()) {
      return;
    }
    $version = PTS_VERSION . '-' . self::ASSETS_VERSION;
    wp_enqueue_style('pts-review', $this->getModPath() . 'css/review.css', [], $version);
    wp_enqueue_script('pts-review', $this->getModPath() . 'js/review.js', [], $version, true);
  }
  public function printCard()
  {
    $card = $this->getCard();
    if (!$card) {
      return;
    }
    // Every value is escaped by the template
    echo $this->getView()->getCardHtml($card);
  }
  /**
   * Saves the answer of a user. "rate" and "done" end the requests; "later", "support" and "close" put them off for a
   * month, and the third time (or the first time on a site that answered the old rate notice) for good.
   * @param int $userId
   * @param string $answer
   * @return bool
   */
  public function saveAnswer($userId, $answer)
  {
    $userId = (int) $userId;
    if (!$userId || !is_string($answer)) {
      return false;
    }
    $now = time();
    if ($answer === 'rate' || $answer === 'done') {
      update_user_meta($userId, self::KEY, ['status' => 'rated', 'answer' => $answer, 'time' => $now]);
      return true;
    }
    if ($answer === 'later' || $answer === 'support' || $answer === 'close') {
      $snoozes = $this->getAnswer($userId)['snoozes'] + 1;
      $state = $this->getState();
      $final = $snoozes >= self::MAX_SNOOZES || $state['legacy'];
      update_user_meta($userId, self::KEY, [
        'status' => $final ? 'dismissed' : 'snoozed',
        'answer' => $answer,
        'until' => $now + self::SNOOZE_DAYS * DAY_IN_SECONDS,
        'snoozes' => $snoozes,
        'time' => $now,
      ]);
      return true;
    }
    return false;
  }
  /**
   * "Rate us" link in the plugin's row on the Plugins page
   * @param array $links
   * @param string $file
   * @return array
   */
  public function addPluginRowLink($links, $file)
  {
    if (!is_array($links) || $file !== plugin_basename(PTS_DIR . PTS_MAIN_FILE)) {
      return $links;
    }
    $links[] = sprintf(
      '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s <span style="color:#ffb900;letter-spacing:1px;" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span></a>',
      esc_url(self::REVIEW_URL),
      esc_attr__('Rate Pricing Table on WordPress.org', PTS_LANG_CODE),
      esc_html__('Rate us', PTS_LANG_CODE),
    );
    return $links;
  }
  /**
   * Site state: when the plugin started to be used, the recorded moments and whether the old rate notice was answered
   * @return array
   */
  public function getState()
  {
    if ($this->_state !== null) {
      return $this->_state;
    }
    $state = get_option(self::KEY);
    if (!is_array($state) || !isset($state['since']) || !is_int($state['since'])) {
      $state = $this->_createState();
      update_option(self::KEY, $state, true);
    }
    $state['legacy'] = !empty($state['legacy']);
    $state['moments'] = isset($state['moments']) && is_array($state['moments']) ? $state['moments'] : [];
    $this->_state = $state;
    return $this->_state;
  }
  /**
   * @param int $userId
   * @return array ['status' => ''|'snoozed'|'rated'|'dismissed', 'until' => int, 'snoozes' => int]
   */
  public function getAnswer($userId)
  {
    $answer = get_user_meta((int) $userId, self::KEY, true);
    $answer = is_array($answer) ? $answer : [];
    return [
      'status' => isset($answer['status']) && is_string($answer['status']) ? $answer['status'] : '',
      'until' => isset($answer['until']) ? (int) $answer['until'] : 0,
      'snoozes' => isset($answer['snoozes']) ? max(0, (int) $answer['snoozes']) : 0,
    ];
  }
  private function _isPro()
  {
    $promo = framePts::_()->getModule('supsystic_promo');
    return $promo && $promo->isPro();
  }
  /**
   * The same rule as the plugin menu: the capability of the plugin (PRO can give it to more roles)
   * @return bool
   */
  private function _currentUserCanUsePlugin()
  {
    $menu = framePts::_()->getModule('adminmenu');
    $cap = $menu ? $menu->getMainCap() : 'manage_options';
    return current_user_can($cap ? $cap : 'manage_options');
  }
  /**
   * The first state of a site. Existing sites keep their history: the start is the first table (or the start of the
   * old rate notice), and an answered old rate notice is remembered.
   * @return array
   */
  private function _createState()
  {
    global $wpdb;
    $now = time();
    $since = $now;
    $legacy = false;
    $options = framePts::_()->getModule('options');
    if ($options) {
      if ((int) $options->get('done_rate_msg') || (int) $options->get('hide_rate_msg')) {
        $legacy = true;
      }
      $startUsage = (int) $options->get('start_usage');
      if ($startUsage > 0) {
        $since = min($since, $startUsage);
      }
    }
    $suppress = $wpdb->suppress_errors(true);
    $firstTable = $wpdb->get_var("SELECT MIN(date_created) FROM {$wpdb->prefix}pts_tables WHERE original_id != 0");
    $wpdb->suppress_errors($suppress);
    if ($firstTable && ($firstTime = strtotime($firstTable)) && $firstTime > 0) {
      $since = min($since, $firstTime);
    }
    return ['since' => (int) $since, 'legacy' => $legacy, 'moments' => []];
  }
  /**
   * @param array $state
   * @return array|null
   */
  private function _pickMoment(array $state)
  {
    foreach ($this->_momentTypes as $type) {
      if (empty($state['moments'][$type]) || !is_array($state['moments'][$type])) {
        continue;
      }
      $moment = $state['moments'][$type];
      $tableId = isset($moment['table']) ? (int) $moment['table'] : 0;
      $table = $this->_getTableLabel($tableId);
      if ($table === null) {
        // The table was deleted since then
        continue;
      }
      $table = trim($table) !== '' ? trim($table) : '#' . $tableId;
      $post = isset($moment['post']) ? get_post((int) $moment['post']) : null;
      $postLive = $post instanceof WP_Post && $post->post_status === 'publish';
      if ($type === 'live') {
        if (!$postLive) {
          continue;
        }
        $postTitle = get_the_title($post);
        return [
          'type' => 'live',
          'eyebrow' => __('Pricing table published', PTS_LANG_CODE),
          'title' => $postTitle !== '' ? sprintf(__('“%1$s” is live on “%2$s”', PTS_LANG_CODE), $table, $postTitle) : sprintf(__('“%s” is live on your site', PTS_LANG_CODE), $table),
          'text' => __('Visitors can now compare your plans and pick the one that fits.', PTS_LANG_CODE),
          'link' => get_permalink($post),
          'linkText' => __('View page', PTS_LANG_CODE),
        ];
      }
      return [
        'type' => 'toggle',
        'eyebrow' => __('Monthly / yearly switch', PTS_LANG_CODE),
        'title' => sprintf(__('“%s” now lets visitors switch the prices', PTS_LANG_CODE), $table),
        'text' => __('One click changes every price of the table.', PTS_LANG_CODE),
        'link' => $postLive ? get_permalink($post) : '',
        'linkText' => __('View page', PTS_LANG_CODE),
      ];
    }
    // A site that answered the old rate notice is asked once more only after a real success
    if ($state['legacy']) {
      return null;
    }
    $tables = $this->_countTables();
    if ($tables >= self::TABLES_MILESTONE) {
      return [
        'type' => 'tables',
        'eyebrow' => __('Milestone', PTS_LANG_CODE),
        'title' => sprintf(__('You have built %d pricing tables with Pricing Table', PTS_LANG_CODE), $tables),
        'text' => __('Nice work. Your plans are easy to compare.', PTS_LANG_CODE),
      ];
    }
    $days = (int) floor((time() - $state['since']) / DAY_IN_SECONDS);
    if ($tables >= 1 && $days >= self::FALLBACK_DAYS) {
      $weeks = (int) floor($days / 7);
      return [
        'type' => 'time',
        'eyebrow' => __('Thank you for using Pricing Table', PTS_LANG_CODE),
        'title' => $weeks < 9 ? sprintf(__('Pricing Table has been working on your site for %d weeks', PTS_LANG_CODE), $weeks) : __('Pricing Table has been working on your site for a while now', PTS_LANG_CODE),
        'text' => __('We hope it makes showing your plans and prices easier.', PTS_LANG_CODE),
      ];
    }
    return null;
  }
  private function _hasMoment($type)
  {
    $state = $this->getState();
    return !empty($state['moments'][$type]);
  }
  private function _addMoment($type, array $data)
  {
    $state = $this->getState();
    $state['moments'][$type] = array_merge($data, ['time' => time()]);
    $this->_state = $state;
    update_option(self::KEY, $state, true);
  }
  /**
   * The first table in a pricing table shortcode of the content
   * @param string $content
   * @return int
   */
  private function _findTableIn($content)
  {
    if ($content === '' || strpos($content, PTS_SHORTCODE) === false) {
      return 0;
    }
    if (preg_match_all('/' . get_shortcode_regex([PTS_SHORTCODE]) . '/', $content, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $shortcode) {
        $attributes = shortcode_parse_atts($shortcode[3]);
        if (is_array($attributes) && !empty($attributes['id']) && (int) $attributes['id'] > 0) {
          return (int) $attributes['id'];
        }
      }
    }
    return 0;
  }
  /**
   * @param int $id
   * @return string|null Null when there is no such table
   */
  private function _getTableLabel($id)
  {
    global $wpdb;
    if ($id < 1) {
      return null;
    }
    $label = $wpdb->get_var($wpdb->prepare("SELECT label FROM {$wpdb->prefix}pts_tables WHERE id = %d AND original_id != 0", $id));
    return $label === null ? null : wp_strip_all_tags((string) $label);
  }
  private function _tableHasSwitch($id)
  {
    $tables = framePts::_()->getModule('tables');
    $table = $tables ? $tables->getModel()->supGetById($id) : null;
    return is_array($table) && !empty($table['params']['enable_switch_toggle']['val']) && (int) $table['params']['enable_switch_toggle']['val'] === 1;
  }
  private function _countTables()
  {
    global $wpdb;
    $suppress = $wpdb->suppress_errors(true);
    $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pts_tables WHERE original_id != 0");
    $wpdb->suppress_errors($suppress);
    return $count;
  }
}
