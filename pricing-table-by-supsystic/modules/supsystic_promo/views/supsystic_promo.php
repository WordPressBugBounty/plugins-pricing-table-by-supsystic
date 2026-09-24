<?php
class supsystic_promoViewPts extends viewPts
{
  public function showWelcomePage()
  {
    $this->assign('askOptions', [
      1 => ['label' => 'Google'],
      2 => ['label' => 'Worptsess.org'],
      3 => ['label' => 'Refer a friend'],
      4 => ['label' => 'Find on the web'],
      5 => ['label' => 'Other way...'],
    ]);
    $this->assign('originalPage', uriPts::getFullUrl());
    parent::display('welcomePage');
  }
  public function getOverviewTabContent()
  {
    framePts::_()->getModule('templates')->loadJqueryUi();
    framePts::_()->getModule('templates')->loadSlimscroll();
    framePts::_()->addScript('admin.overview', $this->getModule()->getModPath() . 'js/admin.overview.js');
    framePts::_()->addStyle('admin.overview', $this->getModule()->getModPath() . 'css/admin.overview.css');
    $this->assign('mainLink', $this->getModule()->getMainLink());
    $this->assign('faqList', $this->getFaqList());
    $this->assign('serverSettings', $this->getServerSettings());
    return parent::getContent('overviewTabContent');
  }
  public function getDataTablesPromoPopup()
  {
    $module = $this->getModule();
    $isPro = $module->isPro();
    $utmCampaign = $isPro ? 'pt_dt_pro_upsell' : 'pt_dt_free_upsell';
    $couponCode = $isPro ? 'PT50' : 'PTF2P49';
    $pidYearly = 3443;
    $pid3Years = 22663;
    $utmBase = 'utm_source=plugin&utm_medium=pricing_table_popup&utm_campaign=' . $utmCampaign;
    if ($isPro) {
      $plans = [
        [
          'term' => __('1 Year', PTS_LANG_CODE),
          'priceOld' => '$79',
          'priceNew' => '$39.50',
          'buyUrl' => $module->getDataTablesBuyLink($pidYearly, $couponCode, $utmBase . '&utm_content=1_year'),
        ],
        [
          'term' => __('3 Years', PTS_LANG_CODE),
          'priceOld' => '$149',
          'priceNew' => '$74.50',
          'buyUrl' => $module->getDataTablesBuyLink($pid3Years, $couponCode, $utmBase . '&utm_content=3_years'),
          'highlight' => true,
        ],
      ];
    } else {
      $plans = [
        [
          'term' => __('Personal, 1 Year', PTS_LANG_CODE),
          'priceOld' => '$79',
          'priceNew' => '$49',
          'buyUrl' => $module->getDataTablesBuyLink($pidYearly, $couponCode, $utmBase . '&utm_content=1_year'),
        ],
      ];
    }
    $this->assign('isPro', $isPro);
    $this->assign('couponCode', $couponCode);
    $this->assign('plans', $plans);
    $this->assign('videoUrl', $module->getModPath() . 'video/pricing-table-styles.mp4');
    $this->assign('themesImgUrl', $module->getModPath() . 'img/datatables-themes-preview.gif');
    $this->assign('ctaLink', $module->getDataTablesLink('utm_source=plugin&utm_medium=pricing_table_popup&utm_campaign=' . $utmCampaign . '&coupon=' . $couponCode));
    return parent::getContent('dataTablesPromo');
  }
  public function getFaqList()
  {
    return [];
  }
  public function getServerSettings()
  {
    global $wpdb;
    return [
      'Operating System' => ['value' => PHP_OS],
      'PHP Version' => ['value' => PHP_VERSION],
      'Server Software' => ['value' => $_SERVER['SERVER_SOFTWARE']],
      'MySQL' => ['value' => $wpdb->db_version()],
      'PHP Allow URL Fopen' => ['value' => ini_get('allow_url_fopen') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE)],
      'PHP Memory Limit' => ['value' => ini_get('memory_limit')],
      'PHP Max Post Size' => ['value' => ini_get('post_max_size')],
      'PHP Max Upload Filesize' => ['value' => ini_get('upload_max_filesize')],
      'PHP Max Script Execute Time' => ['value' => ini_get('max_execution_time')],
      'PHP EXIF Support' => ['value' => extension_loaded('exif') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE)],
      'PHP EXIF Version' => ['value' => phpversion('exif')],
      'PHP XML Support' => ['value' => extension_loaded('libxml') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE), 'error' => !extension_loaded('libxml')],
      'PHP CURL Support' => ['value' => extension_loaded('curl') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE), 'error' => !extension_loaded('curl')],
    ];
  }
}
