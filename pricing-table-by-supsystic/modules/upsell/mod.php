<?php
/**
 * PRO feature windows of the free version, made like those of Data Tables and Photo Gallery: a window for each PRO
 * feature with a small picture of it, what it gives and where it helps, links to the other PRO features and a Get PRO
 * button. Any element with data-pts-upsell="<feature>" opens the window of that feature; the windows are <template>
 * elements printed in the footer of the plugin pages and one <dialog> shows them.
 *
 * With PRO (an active license) nothing is printed: no windows, no PRO notes, no PRO labels.
 */
class upsellPts extends modulePts
{
  const ASSETS_VERSION = 'up-7';
  // Pricing Table is the classic plugin now: the way forward is Data Tables (tables, charts and pricing tables)
  const DT_SLUG = 'data-tables-generator-by-supsystic';
  const DT_FILE = 'data-tables-generator-by-supsystic/index.php';
  const DT_PRO_FILE = 'tables-generator-pro/index.php';
  // The codes of Pricing Table users on Data Tables Pro: 50% with Pricing Table PRO and its active license (the PRO
  // features are on), 20% otherwise, PRO without a license included. Not offered with a licensed Data Tables Pro.
  const DT_COUPON_PRO = 'PTPRO50';
  const DT_COUPON_FREE = 'PTFREE20';
  const DT_PRO_PID = 3443;
  const PT_PRO_FILE = 'price-table-supsystic-pro/price-table-supsystic-pro.php';
  // The banner about Data Tables cannot be closed, only folded into a strip; the choice of every user (user meta)
  const DT_BANNER_FOLDED = 'pts_dt_banner_folded';
  private $_dt = null;
  public function init()
  {
    parent::init();
    if (is_admin()) {
      add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
      add_action('admin_footer', [$this, 'printWindows']);
      add_action('admin_post_pts_dt_activate', [$this, 'activateDataTables']);
      add_filter('install_plugin_complete_actions', [$this, 'changeDtInstallActions'], 10, 3);
      add_action('wp_ajax_pts_dt_banner_fold', [$this, 'ajaxFoldDtBanner']);
      // The banner goes first at the top of every plugin page, above the review card
      dispatcherPts::addAction('adminContentTop', [$this, 'printDtBanner'], 5);
    }
  }
  /**
   * Does this site run Pricing Table PRO with an active license, as the plugin itself sees it (supsystic_promo->isPro():
   * the license turns the PRO modules on). Then the code is PTPRO50 (-50%); otherwise PTFREE20 (-20%), also when PRO
   * is installed and activated but has no license. The filter is the one of the move page of Data Tables.
   * @return bool
   */
  public function isPtProOwner()
  {
    $promo = framePts::_()->getModule('supsystic_promo');
    return (bool) apply_filters('supsystic_tables_pricing_import_source_pro', $promo && $promo->isPro());
  }
  /**
   * The address of the page of Data Tables that moves the tables of Pricing Table (made by Data Tables 1.17.0+)
   * @return string
   */
  public function getDtImportUrl()
  {
    if (function_exists('supsystic_tables_pricing_import_url')) {
      $url = supsystic_tables_pricing_import_url(['from' => 'pricing-table']);
      if (is_string($url) && $url !== '') {
        return $url;
      }
    }
    return admin_url('admin.php?page=supsystic-tables&module=pricingimport&from=pricing-table');
  }
  /**
   * Data Tables on this site and the next step to it: install, activate, update, move the tables or open them
   * @return array
   */
  public function getDt()
  {
    if ($this->_dt !== null) {
      return $this->_dt;
    }
    if (!function_exists('is_plugin_active')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $installed = file_exists(WP_PLUGIN_DIR . '/' . self::DT_FILE);
    $active = $installed && is_plugin_active(self::DT_FILE);
    $canImport = $active && function_exists('supsystic_tables_pricing_import_url');
    $status = $active && function_exists('supsystic_tables_pricing_import_status') ? supsystic_tables_pricing_import_status() : null;
    $ptPro = $this->isPtProOwner();
    $coupon = $ptPro ? self::DT_COUPON_PRO : self::DT_COUPON_FREE;
    $dt = [
      'installed' => $installed,
      'active' => $active,
      'proActive' => $this->_isDtProActive(),
      'ptPro' => $ptPro,
      'coupon' => $coupon,
      'discount' => $ptPro ? 50 : 20,
      'total' => is_array($status) && isset($status['total']) ? (int) $status['total'] : 0,
      'moved' => is_array($status) && isset($status['imported']) ? (int) $status['imported'] : 0,
      'buyUrl' => 'https://supsystic.com/?mod=user&addQty=1&goto=checkout&lang=en&action=addToCart&pid=' . self::DT_PRO_PID . '&coupon=' . $coupon . '&utm_source=plugin&utm_medium=pt_to_dt&utm_campaign=pricing_table',
      'pageUrl' => 'https://supsystic.com/plugins/pricing-table/?utm_source=plugin&utm_medium=pt_to_dt&utm_campaign=pricing_table',
      'icon' => 'fa-download',
    ];
    if (!$installed) {
      $dt['action'] = 'install';
      $dt['label'] = __('Install Data Tables and move your tables', PTS_LANG_CODE);
      $dt['url'] = current_user_can('install_plugins')
        ? wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . self::DT_SLUG . '&pts_dt=1'), 'install-plugin_' . self::DT_SLUG)
        : 'https://wordpress.org/plugins/' . self::DT_SLUG . '/';
    } elseif (!$active) {
      $dt['action'] = 'activate';
      $dt['label'] = __('Activate Data Tables and move your tables', PTS_LANG_CODE);
      $dt['url'] = wp_nonce_url(admin_url('admin-post.php?action=pts_dt_activate'), 'pts_dt_activate');
      $dt['icon'] = 'fa-plug';
    } elseif (!$canImport) {
      $dt['action'] = 'update';
      $dt['label'] = __('Update Data Tables to move your tables', PTS_LANG_CODE);
      $dt['url'] = self_admin_url('plugins.php?plugin_status=upgrade');
      $dt['icon'] = 'fa-refresh';
    } elseif (is_array($status) && !empty($status['total']) && isset($status['imported']) && (int) $status['imported'] >= (int) $status['total']) {
      $dt['action'] = 'open';
      $dt['label'] = __('Open your tables in Data Tables', PTS_LANG_CODE);
      $dt['url'] = !empty($status['url']) ? $status['url'] : $this->getDtImportUrl();
      $dt['icon'] = 'fa-external-link';
    } else {
      $dt['action'] = 'import';
      $dt['label'] = __('Move your tables to Data Tables', PTS_LANG_CODE);
      $dt['url'] = $this->getDtImportUrl();
      $dt['icon'] = 'fa-arrow-right';
    }
    $this->_dt = $dt;
    return $this->_dt;
  }
  /**
   * The button that leads to Data Tables by its state
   * @param string $class Extra classes
   * @return string
   */
  public function getDtButton($class = '')
  {
    $dt = $this->getDt();
    $external = strpos($dt['url'], 'wordpress.org') !== false;
    return '<a class="pts-dt-btn' . ($class ? ' ' . esc_attr($class) : '') . '" href="' . esc_url($dt['url']) . '" data-pts-dt="' . esc_attr($dt['action']) . '"' . ($external ? ' target="_blank" rel="noopener"' : '') . '>' .
      '<i class="fa ' . esc_attr($dt['icon']) . '" aria-hidden="true"></i>' . esc_html($dt['label']) . '</a>';
  }
  /**
   * The offer on Data Tables Pro with the code of the user (-50% PTPRO50 with Pricing Table PRO, -20% PTFREE20 without) and
   * a copy button; empty when a licensed Data Tables Pro is active
   * @param string $class Extra classes
   * @return string
   */
  public function getDtCoupon($class = '')
  {
    $dt = $this->getDt();
    if ($dt['proActive']) {
      return '';
    }
    return '<div class="pts-coupon' . ($class ? ' ' . esc_attr($class) : '') . '">' .
      '<span class="pts-coupon-badge">−' . (int) $dt['discount'] . '%</span>' .
      '<span class="pts-coupon-text">' . esc_html__('on Data Tables Pro, code', PTS_LANG_CODE) . '</span>' .
      '<span class="pts-coupon-code"><code>' . esc_html($dt['coupon']) . '</code><button type="button" data-pts-copy="' . esc_attr($dt['coupon']) . '" data-pts-copied="' . esc_attr__('Copied', PTS_LANG_CODE) . '"><i class="fa fa-clipboard" aria-hidden="true"></i><span>' . esc_html__('Copy', PTS_LANG_CODE) . '</span></button></span>' .
      '<a class="pts-coupon-buy" href="' . esc_url($dt['buyUrl']) . '" target="_blank" rel="noopener">' . esc_html__('Get Data Tables Pro', PTS_LANG_CODE) . '<i class="fa fa-arrow-right" aria-hidden="true"></i></a>' .
      '</div>';
  }
  /**
   * Is the banner about Data Tables folded into a strip for the current user
   * @return bool
   */
  public function isDtBannerFolded()
  {
    $userId = get_current_user_id();
    return $userId ? (bool) get_user_meta($userId, self::DT_BANNER_FOLDED, true) : false;
  }
  /**
   * AJAX of the fold button of the banner: keeps the choice of the current user. The banner cannot be closed.
   */
  public function ajaxFoldDtBanner()
  {
    check_ajax_referer('pts_dt_banner', 'nonce');
    $userId = get_current_user_id();
    if (!$userId) {
      wp_send_json_error();
    }
    $folded = !empty($_POST['folded']) && sanitize_text_field(wp_unslash($_POST['folded'])) === '1';
    update_user_meta($userId, self::DT_BANNER_FOLDED, $folded ? 1 : 0);
    wp_send_json_success(['folded' => $folded]);
  }
  /**
   * The banner at the top of every Pricing Table page, Free and PRO: pricing tables are now part of Data Tables
   */
  public function printDtBanner()
  {
    if (!$this->_onPluginPage()) {
      return;
    }
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the template escapes its values.
    echo $this->getView()->getDtBanner();
  }
  /**
   * A price of Data Tables Pro with the discount of the user, rounded up to a whole dollar ('$49' -50% -> '$25',
   * -20% -> '$40'). The one place where discounted prices are counted.
   * @param string|float $price The price of the page, '$1,199' or 1199
   * @param int|null $discount Percent; the discount of the current user when null
   * @return string
   */
  public function getDtDiscountPrice($price, $discount = null)
  {
    if ($discount === null) {
      $dt = $this->getDt();
      $discount = $dt['discount'];
    }
    $amount = (float) preg_replace('/[^0-9.]/', '', (string) $price);
    // round() first: 120 * 0.8 is 96.00000000000001 in floats and must not become $97
    return '$' . number_format(ceil(round($amount * (100 - (int) $discount) / 100, 2)));
  }
  /**
   * Activates Data Tables (admin-post.php?action=pts_dt_activate) and opens its page that moves the tables
   */
  public function activateDataTables()
  {
    check_admin_referer('pts_dt_activate');
    if (!current_user_can('activate_plugins')) {
      wp_die(esc_html__('Sorry, you are not allowed to activate plugins on this site.', PTS_LANG_CODE), 403);
    }
    if (!function_exists('activate_plugin')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if (!file_exists(WP_PLUGIN_DIR . '/' . self::DT_FILE)) {
      wp_safe_redirect(admin_url('plugin-install.php?s=' . rawurlencode('Data Tables Generator by Supsystic') . '&tab=search&type=term'));
      exit;
    }
    if (!is_plugin_active(self::DT_FILE)) {
      $result = activate_plugin(self::DT_FILE);
      if (is_wp_error($result)) {
        wp_die(esc_html($result->get_error_message()));
      }
    }
    // The functions of Data Tables are not loaded in this request yet: the address of the contract
    wp_safe_redirect(admin_url('admin.php?page=supsystic-tables&module=pricingimport&from=pricing-table'));
    exit;
  }
  /**
   * After Data Tables is installed from Pricing Table, its Activate link activates it and opens the move of the tables
   * @param array $actions
   * @param object $api
   * @param string $pluginFile
   * @return array
   */
  public function changeDtInstallActions($actions, $api, $pluginFile)
  {
    if ($pluginFile === self::DT_FILE && !empty($_GET['pts_dt']) && is_array($actions) && current_user_can('activate_plugins')) {
      $actions['activate_plugin'] = '<a class="button button-primary" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=pts_dt_activate'), 'pts_dt_activate')) . '">' .
        esc_html__('Activate and move your pricing tables', PTS_LANG_CODE) . '</a>';
    }
    return $actions;
  }
  /**
   * Whether a licensed Data Tables Pro runs on the site (then the coupon is not offered)
   * @return bool
   */
  private function _isDtProActive()
  {
    $active = false;
    if (is_plugin_active(self::DT_PRO_FILE)) {
      $opts = get_option('supsystic_tbl__opts_data');
      $name = is_array($opts) && isset($opts['license_save_name']['value']) ? (string) $opts['license_save_name']['value'] : '';
      $val = is_array($opts) && isset($opts['license_save_val']['value']) ? (string) $opts['license_save_val']['value'] : '';
      if ($name !== '') {
        $saved = get_option($name);
        $active = $saved !== false && (string) $saved === $val;
      }
    }
    return (bool) apply_filters('pts_dt_pro_active', $active);
  }
  /**
   * The windows are needed while PRO is not there (or its license is not active)
   * @return bool
   */
  public function isLocked()
  {
    $promo = framePts::_()->getModule('supsystic_promo');
    return !($promo && $promo->isPro());
  }
  private function _onPluginPage()
  {
    return framePts::_()->isAdminPlugOptsPage();
  }
  public function enqueueAssets()
  {
    if (!$this->_onPluginPage() || !$this->isLocked()) {
      return;
    }
    $version = PTS_VERSION . '-' . self::ASSETS_VERSION;
    wp_enqueue_style('pts-upsell', $this->getModPath() . 'css/upsell.css', [], $version);
    wp_enqueue_script('pts-upsell', $this->getModPath() . 'js/upsell.js', [], $version, true);
  }
  public function printWindows()
  {
    if (!$this->_onPluginPage() || !$this->isLocked()) {
      return;
    }
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the template escapes its values.
    echo $this->getView()->getWindows();
  }
  /**
   * The plugin page on supsystic.com with the utm marks of a feature
   * @param string $medium
   * @return string
   */
  public function getProUrl($medium = '')
  {
    $promo = framePts::_()->getModule('supsystic_promo');
    $url = $promo ? $promo->getMainLink() : 'https://supsystic.com/plugins/pricing-table/';
    return $url . '?utm_source=plugin&utm_medium=' . rawurlencode($medium ? $medium : 'upsell') . '&utm_campaign=pricing_table';
  }
  /**
   * The PRO note of a locked feature: a dark callout with a diamond, a PRO tag, the text and Read more, which opens the
   * window of the feature (its href leads to the plugin page if the script does not run). Empty with PRO.
   * @param string $feature Key of the window (see views/upsell.php)
   * @param string $text Plain text
   * @param string $class Extra classes
   * @return string
   */
  public function getProNote($feature, $text, $class = '')
  {
    if (!$this->isLocked()) {
      return '';
    }
    return '<div class="pts-pro-note' . ($class ? ' ' . esc_attr($class) : '') . '">' .
      '<span class="pts-pro-note-icon"><i class="fa fa-diamond" aria-hidden="true"></i></span>' .
      '<span class="pts-pro-note-text"><b class="pts-pro-note-tag">PRO</b>' . esc_html($text) . '</span>' .
      '<a class="pts-pro-note-more" href="' . esc_url($this->getProUrl('upsell_' . $feature)) . '" target="_blank" rel="noopener" data-pts-upsell="' . esc_attr($feature) . '">' .
      esc_html__('Read more', PTS_LANG_CODE) . '<i class="fa fa-arrow-right" aria-hidden="true"></i></a>' .
      '</div>';
  }
  /**
   * A small PRO tag for a locked control (empty with PRO)
   * @param string $feature
   * @return string
   */
  public function getProTag($feature)
  {
    if (!$this->isLocked()) {
      return '';
    }
    return '<a class="pts-pro-tag" href="' . esc_url($this->getProUrl('upsell_' . $feature)) . '" target="_blank" rel="noopener" data-pts-upsell="' . esc_attr($feature) . '"><i class="fa fa-diamond" aria-hidden="true"></i>PRO</a>';
  }
}
