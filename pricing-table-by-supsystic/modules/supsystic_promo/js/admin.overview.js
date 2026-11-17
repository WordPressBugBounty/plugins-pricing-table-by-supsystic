jQuery(document).ready(function () {
  jQuery('.supsystic-overview-news-content').slimScroll({
    height: '500px',
    railVisible: true,
    alwaysVisible: true,
    allowPageScroll: true,
  });
  jQuery('.faq-title').click(function () {
    var descBlock = jQuery(this).find('.description:first');
    if (descBlock.is(':visible')) {
      descBlock.slideUp(g_ptsAnimationSpeed);
    } else {
      jQuery('.faq-title .description').slideUp(g_ptsAnimationSpeed);
      descBlock.slideDown(g_ptsAnimationSpeed);
    }
  });
});
// Licenses of Data Tables Pro on the Overview: one tab per license
jQuery(function ($) {
  $('[data-pts-prices]').on('click', '.pts-prices-tab', function () {
    var $root = $(this).closest('[data-pts-prices]'),
      key = $(this).attr('data-licence');
    $root.find('.pts-prices-tab').removeClass('is-active').attr('aria-selected', 'false');
    $(this).addClass('is-active').attr('aria-selected', 'true');
    $root.find('.pts-prices-panel').each(function () {
      this.hidden = $(this).attr('data-licence') !== key;
    });
  });
});
