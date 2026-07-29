<?php
class supsystic_promoPts extends modulePts
{
  private $_mainLink = '';
  private $_specSymbols = [
    'from' => ['?', '&'],
    'to' => ['%', '^'],
  ];
  private $_minDataInStatToSend = 20;
  public function __construct($d)
  {
    parent::__construct($d);
    $this->getMainLink();
  }
  public function init()
  {
    parent::init();
    add_action('admin_footer', [$this, 'displayAdminFooter'], 9);
    if (is_admin()) {
      $this->checkStatisticStatus();
    }
    $this->weLoveYou();
    dispatcherPts::addFilter('mainAdminTabs', [$this, 'addAdminTab']);
    dispatcherPts::addFilter('showTplsList', [$this, 'checkProTpls']);
    // dispatcherPts::addAction('discountMsg', array(
    //    $this,
    //    'getDiscountMsg'
    // ));
    add_action('admin_notices', [$this, 'checkAdminPromoNotices']);
  }
  public function checkAdminPromoNotices()
  {
    if (!framePts::_()->isAdminPlugOptsPage()) {
      return;
    }
    $notices = [];
    $startUsage = (int) framePts::_()->getModule('options')->get('start_usage');
    $currTime = time();
    $day = 24 * 3600;
    if ($startUsage) {
      //  $rateMsg = sprintf(__("<h3>Hey, I noticed you just use %s over a week – that’s awesome!</h3><p>Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.</p>", PTS_LANG_CODE) , PTS_WP_PLUGIN_NAME);
      //  $rateMsg .= '<p><a href="https://wordpress.org/support/view/plugin-reviews/pricing-table-by-supsystic?rate=5#postform" target="_blank" class="button button-primary" data-statistic-code="done">' . __('Ok, you deserve it', PTS_LANG_CODE) . '</a><a href="#" class="button" data-statistic-code="later">' . __('Nope, maybe later', PTS_LANG_CODE) . '</a><a href="#" class="button" data-statistic-code="hide">' . __('I already did', PTS_LANG_CODE) . '</a></p>';
      //  $enbPromoLinkMsg = sprintf(__("<h3>More then eleven days with our %s plugin - Congratulations!</h3>", PTS_LANG_CODE) , PTS_WP_PLUGIN_NAME);;
      //  $enbPromoLinkMsg .= __('<p>On behalf of the entire <a href="https://supsystic.com/" target="_blank">supsystic.com</a> company I would like to thank you for been with us, and I really hope that our software helped you.</p>', PTS_LANG_CODE);
      //  $enbPromoLinkMsg .= __('<p>And today, if you want, - you can help us. This is really simple - you can just add small promo link to our site under your tables. This is small step for you, but a big help for us! Sure, if you don\'t want - just skip this and continue enjoy our software!</p>', PTS_LANG_CODE);
      //  $enbPromoLinkMsg .= '<p><a href="#" class="button button-primary" data-statistic-code="done">' . __('Ok, you deserve it', PTS_LANG_CODE) . '</a><a href="#" class="button" data-statistic-code="later">' . __('Nope, maybe later', PTS_LANG_CODE) . '</a><a href="#" class="button" data-statistic-code="hide">' . __('Skip', PTS_LANG_CODE) . '</a></p>';
      // $checkOtherPlugins = '<p>' . sprintf(__('Check out <a href="%s" target="_blank" class="button button-primary" data-statistic-code="hide">our other Plugins</a>! Years of experience in WordPress plugins developers made those list unbreakable!', PTS_LANG_CODE) , framePts::_()->getModule('options')->getTabUrl('featured-plugins')) . '</p>';
      $notices = [
        // 'rate_msg' => array(
        //    'html' => $rateMsg,
        //    'show_after' => 7 * $day
        // ) ,
        // 'enb_promo_link_msg' => array(
        //    'html' => $enbPromoLinkMsg,
        //    'show_after' => 11 * $day
        // ) ,
        // 'check_other_plugs_msg' => array(
        //    'html' => $checkOtherPlugins,
        //    'show_after' => 1 * $day
        // ) ,
      ];
      foreach ($notices as $nKey => $n) {
        if ($currTime - $startUsage <= $n['show_after']) {
          unset($notices[$nKey]);
          continue;
        }
        $done = (int) framePts::_()
          ->getModule('options')
          ->get('done_' . $nKey);
        if ($done) {
          unset($notices[$nKey]);
          continue;
        }
        $hide = (int) framePts::_()
          ->getModule('options')
          ->get('hide_' . $nKey);
        if ($hide) {
          unset($notices[$nKey]);
          continue;
        }
        $later = (int) framePts::_()
          ->getModule('options')
          ->get('later_' . $nKey);
        if ($later && $currTime - $later <= 2 * $day) {
          // remember each 2 days
          unset($notices[$nKey]);
          continue;
        }
      }
    } else {
      framePts::_()->getModule('options')->getModel()->save('start_usage', $currTime);
    }
    if (!empty($notices)) {
      if (isset($notices['rate_msg']) && isset($notices['enb_promo_link_msg']) && !empty($notices['enb_promo_link_msg'])) {
        unset($notices['rate_msg']);
      }
      $html = '';
      foreach ($notices as $nKey => $n) {
        $this->getModel()->saveUsageStat($nKey . '.' . 'show', true);
        $html .= '<div class="updated notice is-dismissible supsystic-admin-notice" data-code="' . $nKey . '">' . $n['html'] . '</div>';
      }
      echo $html;
    }
  }
  public function addAdminTab($tabs)
  {
    $tabs['overview'] = [
      'label' => __('Overview', PTS_LANG_CODE),
      'callback' => [$this, 'getOverviewTabContent'],
      'fa_icon' => 'fa-info',
      'sort_order' => 5,
    ];
    // $tabs['featured-plugins'] = array(
    //    'label' => __('Featured Plugins', PTS_LANG_CODE) ,
    //    'callback' => array(
    //       $this,
    //       'showFeaturedPluginsPage'
    //    ) ,
    //    'fa_icon' => 'fa-heart',
    //    'sort_order' => 99,
    // );
    return $tabs;
  }
  public function getOverviewTabContent()
  {
    return $this->getView()->getOverviewTabContent();
  }
  private function _encodeSlug($slug)
  {
    return str_replace($this->_specSymbols['from'], $this->_specSymbols['to'], $slug);
  }
  private function _decodeSlug($slug)
  {
    return str_replace($this->_specSymbols['to'], $this->_specSymbols['from'], $slug);
  }
  public function decodeSlug($slug)
  {
    return $this->_decodeSlug($slug);
  }
  public function modifyMainAdminSlug($mainSlug)
  {
    $firstTimeLookedToPlugin = !installerPts::isUsed();
    if ($firstTimeLookedToPlugin) {
      $mainSlug = $this->_getNewAdminMenuSlug($mainSlug);
    }
    return $mainSlug;
  }
  private function _getWelcomMessageMenuData($option, $modifySlug = true)
  {
    return array_merge($option, [
      'page_title' => __('Welcome to Supsystic', PTS_LANG_CODE),
      'menu_slug' => $modifySlug ? $this->_getNewAdminMenuSlug($option['menu_slug']) : $option['menu_slug'],
      'function' => [$this, 'showWelcomePage'],
    ]);
  }
  public function addWelcomePageToMenus($options)
  {
    $firstTimeLookedToPlugin = !installerPts::isUsed();
    if ($firstTimeLookedToPlugin) {
      foreach ($options as $i => $opt) {
        $options[$i] = $this->_getWelcomMessageMenuData($options[$i]);
      }
    }
    return $options;
  }
  private function _getNewAdminMenuSlug($menuSlug)
  {
    $newSlug = $this->_encodeSlug(str_replace('admin.php?page=', '', $menuSlug));
    return 'welcome-to-' . framePts::_()->getModule('adminmenu')->getMainSlug() . '|return=' . $newSlug;
  }
  public function addWelcomePageToMainMenu($option)
  {
    $firstTimeLookedToPlugin = !installerPts::isUsed();
    if ($firstTimeLookedToPlugin) {
      $option = $this->_getWelcomMessageMenuData($option, false);
    }
    return $option;
  }
  public function showWelcomePage()
  {
    $this->getView()->showWelcomePage();
  }
  public function displayAdminFooter()
  {
    if (framePts::_()->isAdminPlugPage()) {
      $this->getView()->displayAdminFooter();
    }
  }
  private function _preparePromoLink($link, $ref = '')
  {
    if (empty($ref)) {
      $ref = 'user';
    }
    return $link;
  }
  public function weLoveYou()
  {
    if (!framePts::_()->getModule(implode('', ['l', 'ic', 'e', 'ns', 'e']))) {
    }
  }
  public function showAdditionalmainAdminShowOnOptions($popup)
  {
    $this->getView()->showAdditionalmainAdminShowOnOptions($popup);
  }
  public function preparePromoLink($link, $ref = '')
  {
    return $this->_preparePromoLink($link, $ref);
  }
  public function checkStatisticStatus()
  {
    $canSend = (int) framePts::_()->getModule('options')->get('send_stats');
    if ($canSend) {
      $this->getModel()->checkAndSend();
    }
  }
  public function getMinStatSend()
  {
    return $this->_minDataInStatToSend;
  }
  public function getMainLink()
  {
    if (empty($this->_mainLink)) {
      $this->_mainLink = 'https://supsystic.com/plugins/pricing-tables/';
    }
    return $this->_mainLink;
  }
  public function isPro()
  {
    return framePts::_()->getModule('tablepro') ? true : false;
  }
  public function generateMainLink($params = '')
  {
    $mainLink = $this->getMainLink();
    if (!empty($params)) {
      return $mainLink . (strpos($mainLink, '?') ? '&' : '?') . $params;
    }
    return $mainLink;
  }
  public function checkProTpls($list)
  {
    if (!$this->isPro()) {
      $imgsPath = framePts::_()->getModule('tables')->getAssetsUrl() . 'img/prev/';
      $promoList = [
        [
          'label' => 'Izar',
          'img' => 'imagine.jpg',
        ],
        [
          'label' => 'Keid',
          'img' => 'iconic.jpg',
        ],
        [
          'label' => 'Wezen',
          'img' => 'winner.jpg',
        ],
        [
          'label' => 'Extended Table',
          'img' => 'extended-table.jpg',
        ],
        [
          'label' => 'Arrakis',
          'img' => 'big-brother.jpg',
        ],
        [
          'label' => 'Alcor',
          'img' => 'ati.jpg',
        ],
        [
          'label' => 'Toliman',
          'img' => 'triangle-header.jpg',
        ],
        [
          'label' => 'Matas',
          'img' => 'plans.jpg',
        ],
        [
          'label' => 'Toliman',
          'img' => 'triangle-header.jpg',
        ],
        [
          'label' => 'Coxa',
          'img' => 'clean.jpg',
        ],
        [
          'label' => 'Atik',
          'img' => 'veggy.jpg',
        ],
        [
          'label' => 'Hihal',
          'img' => 'product-compare.jpg',
        ],
        [
          'label' => 'Deneb',
          'img' => 'easy-columns.jpg',
        ],
        [
          'label' => 'Exponential',
          'img' => 'exponential.jpg',
        ],
        [
          'label' => 'Vote Classic',
          'img' => 'vote-classic.jpg',
        ],
        [
          'label' => 'Team 1',
          'img' => 'team-1.jpg',
        ],
        [
          'label' => 'Team 2',
          'img' => 'team-1.jpg',
        ],
        [
          'label' => 'Comfort',
          'img' => 'comfort.jpg',
        ],
        [
          'label' => 'Dr House',
          'img' => 'dr-house.jpg',
        ],
        [
          'label' => 'Low Price',
          'img' => 'low-price.jpg',
        ],
        [
          'label' => 'Pizza 1',
          'img' => 'pizza1.jpg',
        ],
        [
          'label' => 'Pizza 2',
          'img' => 'pizza2.jpg',
        ],
        [
          'label' => 'Model Agency',
          'img' => 'model-agency.jpg',
        ],
        [
          'label' => 'Mini',
          'img' => 'mini.jpg',
        ],
        [
          'label' => 'Hosting Tools',
          'img' => 'hosting-tools.jpg',
        ],
        [
          'label' => 'Startup',
          'img' => 'startup.jpg',
        ],
        [
          'label' => 'Try',
          'img' => 'try.jpg',
        ],
        [
          'label' => 'Express',
          'img' => 'express.jpg',
        ],
        [
          'label' => 'Retro',
          'img' => 'retro.jpg',
        ],
        [
          'label' => 'Taxi',
          'img' => 'taxi.jpg',
        ],
        [
          'label' => 'Prezzo',
          'img' => 'prezzo.jpg',
        ],
        [
          'label' => 'Server Hosting',
          'img' => 'server-hosting.jpg',
        ],
        [
          'label' => 'Horizontal',
          'img' => 'horisontal-template.png',
        ],
        [
          'label' => 'Swans',
          'img' => 'swans.jpg',
        ],
        [
          'label' => 'Danskin',
          'img' => 'danskin.jpg',
        ],
        [
          'label' => 'Carmen',
          'img' => 'carmen.jpg',
        ],
        [
          'label' => 'Cinnamon',
          'img' => 'cinnamon.jpg',
        ],
        [
          'label' => 'Ancer',
          'img' => 'ancer.jpg',
        ],
      ];
      foreach ($promoList as $i => $t) {
        $promoList[$i]['img_url'] = $imgsPath . $promoList[$i]['img'];
        $promoList[$i]['promo'] = strtolower(str_replace([' ', '!'], '', $t['label']));
        $promoList[$i]['promo_link'] = $this->generateMainLink('utm_source=plugin&utm_medium=' . $promoList[$i]['promo'] . '&utm_campaign=pricing_table');
      }
      foreach ($list as $i => $t) {
        if (isset($t['is_pro']) && (int) $t['is_pro']) {
          unset($list[$i]);
        }
      }
      $list = array_merge($list, $promoList);
    }
    return $list;
  }
  // public function getDiscountMsg() {
  //    if ($this->isPro() && framePts::_()->getModule('options')->getActiveTab() == 'license' && framePts::_()->getModule('license') && framePts::_()->getModule('license')->getModel()->isActive()) {
  //       $proPluginsList = array(
  //          'ultimate-maps-by-supsystic-pro',
  //          'newsletters-by-supsystic-pro',
  //          'contact-form-by-supsystic-pro',
  //          'live-chat-pro',
  //          'digital-publications-supsystic-pro',
  //          'coming-soon-supsystic-pro',
  //          'price-table-supsystic-pro',
  //          'tables-generator-pro',
  //          'social-share-pro',
  //          'popup-by-supsystic-pro',
  //          'supsystic_slider_pro',
  //          'supsystic-gallery-pro',
  //          'google-maps-easy-pro',
  //          'backup-supsystic-pro',
  //       );
  //       $activePluginsList = get_option('active_plugins', array());
  //       $activeProPluginsCount = 0;
  //       foreach ($activePluginsList as $actPl) {
  //          foreach ($proPluginsList as $proPl) {
  //             if (strpos($actPl, $proPl) !== false) {
  //                $activeProPluginsCount++;
  //             }
  //          }
  //       }
  //       if ($activeProPluginsCount === 1) {
  //          $buyLink = $this->getDiscountBuyUrl();
  //          $this->getView()->getDiscountMsg($buyLink);
  //       }
  //    }
  // }
  public function getDiscountBuyUrl()
  {
    $license = framePts::_()->getModule('license')->getModel()->getCredentials();
    $license['key'] = md5($license['key']);
    $license = urlencode(base64_encode(implode('|', $license)));
    $plugin_code = 'pricing_tables_pro';
    return 'http://supsystic.com/?mod=manager&pl=lms&action=applyDiscountBuyUrl&plugin_code=' . $plugin_code . '&lic=' . $license;
  }
}
