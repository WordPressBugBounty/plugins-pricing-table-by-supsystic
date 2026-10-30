<?php

/**
 * Plugin Name: Pricing Table by Supsystic
 * Plugin URI: https://supsystic.com/plugins/pricing-tables/
 * Description: Responsive pricing tables and comparison tables with a drag-and-drop builder. Templates, badges, icons and buttons included.
 * Version: 1.12.4
 * Author: supsystic.com
 * Author URI: http://supsystic.com/
 * Text Domain: pricing-table-by-supsystic
 * Domain Path: /languages
 **/
/**
 * Base config constants and functions
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';
/**
 * Connect all required core classes
 */
importClassPts('dbPts');
importClassPts('installerPts');
importClassPts('baseObjectPts');
importClassPts('modulePts');
importClassPts('modelPts');
importClassPts('viewPts');
importClassPts('controllerPts');
importClassPts('helperPts');
importClassPts('dispatcherPts');
importClassPts('fieldPts');
importClassPts('tablePts');
importClassPts('framePts');
importClassPts('reqPts');
importClassPts('uriPts');
importClassPts('htmlPts');
importClassPts('responsePts');
importClassPts('fieldAdapterPts');
importClassPts('validatorPts');
importClassPts('errorsPts');
importClassPts('utilsPts');
importClassPts('modInstallerPts');
importClassPts('installerDbUpdaterPts');
importClassPts('datePts');
/**
 * Check plugin version - maybe we need to update database, and check global errors in request
 */
installerPts::update();
errorsPts::init();
/**
 * Start application
 */
framePts::_()->parseRoute();
framePts::_()->init();
framePts::_()->exec();

/**
 * Spooky Sale banner on the plugin admin pages
 */
if (is_admin()) {
  require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'promo' . DIRECTORY_SEPARATOR . 'spooky-sale.php';
  SupsysticSpookySale2026::register('tables-supsystic', plugin_dir_url(__FILE__) . 'promo/', [
    'proPid' => 6029,
    'proUrl' => 'https://supsystic.com/plugins/pricing-table/',
    'isPro' => function () {
      $promo = framePts::_()->getModule('supsystic_promo');
      return $promo && $promo->isPro();
    },
    'utmCampaign' => 'pricing-table',
  ]);
}

//var_dump(framePts::_()->getActivationErrors()); exit();
