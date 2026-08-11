<?php
class supsystic_promoPts extends modulePts
{
  private $_mainLink = '';
  private $_specSymbols = [
    'from' => ['?', '&'],
    'to' => ['%', '^'],
  ];
  public function __construct($d)
  {
    parent::__construct($d);
    $this->getMainLink();
  }
  public function init()
  {
    parent::init();
    dispatcherPts::addFilter('mainAdminTabs', [$this, 'addAdminTab']);
    dispatcherPts::addFilter('showTplsList', [$this, 'checkProTpls']);
  }
  public function addAdminTab($tabs)
  {
    $tabs['overview'] = [
      'label' => __('Overview', PTS_LANG_CODE),
      'callback' => [$this, 'getOverviewTabContent'],
      'fa_icon' => 'fa-info',
      'sort_order' => 5,
    ];
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
  private function _preparePromoLink($link, $ref = '')
  {
    if (empty($ref)) {
      $ref = 'user';
    }
    return $link;
  }
  public function preparePromoLink($link, $ref = '')
  {
    return $this->_preparePromoLink($link, $ref);
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
}
