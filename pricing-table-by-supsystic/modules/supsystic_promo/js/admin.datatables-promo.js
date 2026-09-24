(function ($) {
  'use strict';

  var STORAGE_KEY = 'ptsDtPromoLastShown';
  var SHOW_INTERVAL = 24 * 60 * 60 * 1000;

  function getLastShown() {
    try {
      return parseInt(window.localStorage.getItem(STORAGE_KEY), 10) || 0;
    } catch (e) {
      return 0;
    }
  }

  function setLastShown() {
    try {
      window.localStorage.setItem(STORAGE_KEY, String(Date.now()));
    } catch (e) {}
  }

  function shouldShow() {
    return Date.now() - getLastShown() >= SHOW_INTERVAL;
  }

  $(function () {
    var $overlay = $('#ptsDtPromoOverlay');
    if (!$overlay.length) {
      return;
    }
    var $video = $('#ptsDtPromoVideo');

    function openPopup() {
      $overlay.addClass('is-open');
      if ($video.length) {
        var video = $video.get(0);
        try {
          video.currentTime = 0;
          video.play();
        } catch (e) {}
      }
    }

    function hidePopup() {
      $overlay.removeClass('is-open');
      if ($video.length) {
        $video.get(0).pause();
      }
    }

    function dismissPopup() {
      hidePopup();
      setLastShown();
    }

    $overlay.on('click', function (e) {
      if (e.target === this) {
        hidePopup();
      }
    });
    $('#ptsDtPromoClose, #ptsDtPromoDismiss').on('click', dismissPopup);
    $(document).on('keydown', function (e) {
      if (e.key === 'Escape' && $overlay.hasClass('is-open')) {
        hidePopup();
      }
    });

    if (shouldShow()) {
      setTimeout(openPopup, 400);
    }
  });
})(jQuery);
