<?php
class upsellViewPts extends viewPts
{
  /**
   * The PRO features of Pricing Table and the window of each one. The keys are used by data-pts-upsell.
   * @return array
   */
  public function getFeatures()
  {
    return [
      'templates' => [
        'icon' => 'fa-th-large',
        'chip' => __('37 PRO templates', PTS_LANG_CODE),
        'title' => __('37 more pricing table templates', PTS_LANG_CODE),
        'text' => __('Ready designs for hosting, SaaS, restaurants, agencies, teams and more. Pick one, type your prices and publish.', PTS_LANG_CODE),
        'points' => [
          __('37 PRO templates on top of the 7 free ones', PTS_LANG_CODE),
          __('Horizontal, comparison and team layouts', PTS_LANG_CODE),
          __('Switch a table to another template at any time', PTS_LANG_CODE),
          __('Every text, color, icon and button stays editable', PTS_LANG_CODE),
        ],
        'uses' => [__('Hosting & SaaS', PTS_LANG_CODE), __('Restaurants', PTS_LANG_CODE), __('Agencies', PTS_LANG_CODE)],
        'dt' => __('Data Tables has 3 free pricing themes and 100+ more in Data Tables Pro, from SaaS to holiday sales.', PTS_LANG_CODE),
      ],
      'toggle' => [
        'icon' => 'fa-toggle-on',
        'chip' => __('Monthly / yearly switch', PTS_LANG_CODE),
        'title' => __('Let visitors switch the prices', PTS_LANG_CODE),
        'text' => __('One switch above the table changes every price: monthly and yearly, or any periods and options you name.', PTS_LANG_CODE),
        'points' => [
          __('A drop-down, radio buttons or a toggle button', PTS_LANG_CODE),
          __('Your own names: Monthly, Yearly, Lifetime and more', PTS_LANG_CODE),
          __('Choose the option that shows first', PTS_LANG_CODE),
          __('Own text, colors, position and alignment', PTS_LANG_CODE),
        ],
        'uses' => [__('Subscriptions', PTS_LANG_CODE), __('Memberships', PTS_LANG_CODE), __('Services', PTS_LANG_CODE)],
        'dt' => __('Data Tables Pro has the Monthly / yearly switch with your own periods, and your switch moves there with the table.', PTS_LANG_CODE),
      ],
      'schedule' => [
        'icon' => 'fa-calendar',
        'chip' => __('Scheduled columns', PTS_LANG_CODE),
        'title' => __('Show a plan only when you want', PTS_LANG_CODE),
        'text' => __('Give any column a show-from and a show-until date: a holiday offer appears on time and goes away on its own.', PTS_LANG_CODE),
        'points' => [
          __('A start and an end date for every column', PTS_LANG_CODE),
          __('Works on the live page while you are away', PTS_LANG_CODE),
          __('Prepare your offers in advance', PTS_LANG_CODE),
          __('The other columns stay as they are', PTS_LANG_CODE),
        ],
        'uses' => [__('Black Friday', PTS_LANG_CODE), __('Seasonal offers', PTS_LANG_CODE), __('Launch deals', PTS_LANG_CODE)],
        'dt' => __('Data Tables Pro shows a plan between its show-from and show-until dates; your dates move there with the table.', PTS_LANG_CODE),
      ],
      'ajax' => [
        'icon' => 'fa-bolt',
        'chip' => __('Load on click', PTS_LANG_CODE),
        'title' => __('Load the table on click', PTS_LANG_CODE),
        'text' => __('The table waits behind its title and loads only when a visitor opens it, so long pages stay fast.', PTS_LANG_CODE),
        'points' => [
          __('The table name or your own title on the button', PTS_LANG_CODE),
          __('The table loads in place when it is clicked', PTS_LANG_CODE),
          __('A lighter first load of the page', PTS_LANG_CODE),
          __('Works with the shortcode on any page', PTS_LANG_CODE),
        ],
        'uses' => [__('Long pages', PTS_LANG_CODE), __('Several tables on a page', PTS_LANG_CODE), __('FAQ and docs', PTS_LANG_CODE)],
        'dt' => __('Data Tables Pro loads a table on click too, and adds a Gutenberg block and an Elementor widget.', PTS_LANG_CODE),
      ],
      'access' => [
        'icon' => 'fa-users',
        'chip' => __('Access roles', PTS_LANG_CODE),
        'title' => __('Let your team edit the pricing tables', PTS_LANG_CODE),
        'text' => __('Choose the user roles that can open Pricing Table in the admin area, not only administrators.', PTS_LANG_CODE),
        'points' => [
          __('Any WordPress role, custom roles too', PTS_LANG_CODE),
          __('Administrators always keep their access', PTS_LANG_CODE),
          __('Editors update prices without full admin rights', PTS_LANG_CODE),
        ],
        'uses' => [__('Agencies', PTS_LANG_CODE), __('Shops with a team', PTS_LANG_CODE), __('Client sites', PTS_LANG_CODE)],
        'dt' => __('Data Tables Pro lets you choose the roles that work with tables in its Settings.', PTS_LANG_CODE),
      ],
    ];
  }
  public function getWindows()
  {
    $this->assign('features', $this->getFeatures());
    $this->assign('upsell', $this->getModule());
    return parent::getContent('upsellWindows');
  }
  /**
   * The banner about Data Tables at the top of every plugin page (Free and PRO)
   * @return string
   */
  public function getDtBanner()
  {
    $upsell = $this->getModule();
    $this->assign('upsell', $upsell);
    $this->assign('dt', $upsell->getDt());
    $this->assign('folded', $upsell->isDtBannerFolded());
    $this->assign('foldNonce', wp_create_nonce('pts_dt_banner'));
    $this->assign('ajaxUrl', admin_url('admin-ajax.php'));
    $this->assign('overviewUrl', framePts::_()->getModule('options')->getTabUrl('overview'));
    return parent::getContent('dtBanner');
  }
}
