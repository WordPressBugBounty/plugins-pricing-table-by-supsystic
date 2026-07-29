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
