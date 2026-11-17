<?php
/**
 * One look for every admin page of the plugin: navigation, header, buttons, fields, selects, checkboxes, toolbars,
 * lists and dialogs, the same as in Data Tables and Photo Gallery. The old plugin-wide stylesheet (css/supsystic-ui.css)
 * draws square black buttons and grey fields without side borders; this stylesheet is printed after it, so the same or
 * stronger selectors win. Nothing in the markup changes.
 */
class designPts extends modulePts
{
  // Bumped when the files change, so browsers do not keep the old ones
  const ASSETS_VERSION = 'ds-19';
  public function init()
  {
    parent::init();
    if (is_admin()) {
      // Late, so it is printed after the plugin's own stylesheets
      add_action('admin_enqueue_scripts', [$this, 'enqueueAssets'], 100);
    }
  }
  public function enqueueAssets()
  {
    if (!framePts::_()->isAdminPlugOptsPage()) {
      return;
    }
    $version = PTS_VERSION . '-' . self::ASSETS_VERSION;
    wp_enqueue_style('pts-admin-design', $this->getModPath() . 'css/admin.css', [], $version);
    wp_enqueue_script('pts-admin-design', $this->getModPath() . 'js/admin.js', ['jquery'], $version, true);
  }
}
