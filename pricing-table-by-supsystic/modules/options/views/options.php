<?php
class optionsViewPts extends viewPts
{
  private $_news = [];
  public function getNewFeatures()
  {
    $res = [];
    $readmePath = PTS_DIR . 'readme.txt';
    if (file_exists($readmePath)) {
      $readmeContent = @file_get_contents($readmePath);
      if (!empty($readmeContent)) {
        $matchedData = '';
        if (preg_match('/= ' . PTS_VERSION . ' =(.+)=.+=/isU', $readmeContent, $matches)) {
          $matchedData = $matches[1];
        } elseif (preg_match('/= ' . PTS_VERSION . ' =(.+)/is', $readmeContent, $matches)) {
          $matchedData = $matches[1];
        }
        $matchedData = trim($matchedData);
        if (!empty($matchedData)) {
          $res = array_map('trim', explode("\n", $matchedData));
        }
      }
    }
    return $res;
  }
  public function getAdminPage()
  {
    $tabs = $this->getModule()->getTabs();
    $activeTab = $this->getModule()->getActiveTab();
    $content = 'No tab content found - ERROR';
    if (isset($tabs[$activeTab]) && isset($tabs[$activeTab]['callback'])) {
      $content = call_user_func($tabs[$activeTab]['callback']);
    }
    $activeParentTabs = [];
    foreach ($tabs as $tabKey => $tab) {
      if ($tabKey == $activeTab && isset($tab['child_of'])) {
        $activeTab = $tab['child_of'];
        //$activeParentTabs[] = $tab['child_of'];
      }
    }
    framePts::_()->addJSVar('adminOptionsPts', 'ptsActiveTab', $activeTab);
    $this->assign('tabs', $tabs);
    $this->assign('activeTab', $activeTab);
    $this->assign('content', $content);
    $this->assign('mainUrl', $this->getModule()->getTabUrl());
    $this->assign('activeParentTabs', $activeParentTabs);
    $this->assign('breadcrumbs', framePts::_()->getModule('admin_nav')->getView()->getBreadcrumbs());
    $this->assign('mainLink', framePts::_()->getModule('supsystic_promo')->getMainLink());

    parent::display('optionsAdminPage');
  }
  public function sortOptsSet($a, $b)
  {
    if ($a['weight'] > $b['weight']) {
      return -1;
    }
    if ($a['weight'] < $b['weight']) {
      return 1;
    }
    return 0;
  }
  public function getTabContent()
  {
    framePts::_()->addScript('admin.mainoptions', $this->getModule()->getModPath() . 'js/admin.mainoptions.js');
    return parent::getContent('optionsAdminMain');
  }
  public function serverSettings()
  {
    global $wpdb;
    $this->assign('systemInfo', [
      'Operating System' => ['value' => PHP_OS],
      'PHP Version' => ['value' => PHP_VERSION],
      'Server Software' => ['value' => $_SERVER['SERVER_SOFTWARE']],
      'MySQL' => ['value' => $wpdb->db_version()],
      'PHP Allow URL Fopen' => ['value' => ini_get('allow_url_fopen') ? 'Yes' : 'No'],
      'PHP Memory Limit' => ['value' => ini_get('memory_limit')],
      'PHP Max Post Size' => ['value' => ini_get('post_max_size')],
      'PHP Max Upload Filesize' => ['value' => ini_get('upload_max_filesize')],
      'PHP Max Script Execute Time' => ['value' => ini_get('max_execution_time')],
      'PHP EXIF Support' => ['value' => extension_loaded('exif') ? 'Yes' : 'No'],
      'PHP EXIF Version' => ['value' => phpversion('exif')],
      'PHP XML Support' => ['value' => extension_loaded('libxml') ? 'Yes' : 'No', 'error' => !extension_loaded('libxml')],
      'PHP CURL Support' => ['value' => extension_loaded('curl') ? 'Yes' : 'No', 'error' => !extension_loaded('curl')],
    ]);
    return parent::display('_serverSettings');
  }
  public function getSettingsTabContent()
  {
    framePts::_()->addScript('admin.settings', $this->getModule()->getModPath() . 'js/admin.settings.js');
    framePts::_()->getModule('templates')->loadJqueryUi();

    $options = framePts::_()->getModule('options')->getAll();
    $this->assign('options', $options);
    return parent::getContent('optionsSettingsTabContent');
  }
}
