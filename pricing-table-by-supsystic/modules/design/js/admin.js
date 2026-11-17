/**
 * The old jQuery UI dialogs of the plugin draw all their buttons the same. When a dialog opens, the first button that
 * does something is marked as the main action (navy); the buttons that only close it (Cancel, Close, No) stay plain.
 * It runs a tick later, after the plugin's own handlers of the same event.
 */
(function ($) {
  'use strict';

  var plain = /^(cancel|close|no|back|not now|maybe later|отмена|закрыть|нет|назад)$/i;

  $(document).on('dialogopen', function (event) {
    var $dialog = $(event.target).closest('.ui-dialog');
    setTimeout(function () {
      var main = false;
      $dialog.find('.ui-dialog-buttonset button').each(function () {
        var $button = $(this),
          isPlain = plain.test($.trim($button.text()));
        $button.toggleClass('pts-dlg-plain', isPlain);
        $button.toggleClass('pts-dlg-main', !isPlain && !main);
        if (!isPlain) {
          main = true;
        }
      });
    }, 0);
  });
})(jQuery);

/**
 * Copy buttons of the plugin pages (the coupon of Data Tables Pro): [data-pts-copy] puts its text on the clipboard
 * and shows [data-pts-copied] for a moment.
 */
(function () {
  'use strict';

  function copy(text) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text).catch(function () {});
    }
    var area = document.createElement('textarea');
    area.value = text;
    area.setAttribute('readonly', '');
    area.style.position = 'absolute';
    area.style.left = '-9999px';
    document.body.appendChild(area);
    area.select();
    try {
      document.execCommand('copy');
    } catch (e) {}
    document.body.removeChild(area);
  }

  document.addEventListener('click', function (event) {
    var button = event.target.closest ? event.target.closest('[data-pts-copy]') : null;
    if (!button) {
      return;
    }
    event.preventDefault();
    copy(button.getAttribute('data-pts-copy'));
    var label = button.querySelector('span'),
      icon = button.querySelector('.fa');
    if (!label || button.classList.contains('is-copied')) {
      return;
    }
    var text = label.textContent;
    button.classList.add('is-copied');
    label.textContent = button.getAttribute('data-pts-copied') || text;
    if (icon) {
      icon.className = 'fa fa-check';
    }
    setTimeout(function () {
      button.classList.remove('is-copied');
      label.textContent = text;
      if (icon) {
        icon.className = 'fa fa-clipboard';
      }
    }, 1600);
  });
})();

/**
 * The banner about Data Tables folds into a one-line strip and cannot be closed. The page comes folded or not from the
 * server (user meta of the current user); the button switches it and saves the choice.
 */
(function () {
  'use strict';

  document.addEventListener('click', function (event) {
    var button = event.target.closest ? event.target.closest('[data-pts-dt-fold]') : null;
    if (!button) {
      return;
    }
    var banner = button.closest('[data-pts-dt-banner]');
    if (!banner) {
      return;
    }
    event.preventDefault();
    var folded = !banner.classList.contains('is-folded');
    banner.classList.toggle('is-folded', folded);
    button.setAttribute('aria-expanded', folded ? 'false' : 'true');
    button.setAttribute('title', button.getAttribute(folded ? 'data-title-more' : 'data-title-less') || '');
    var body = new FormData();
    body.append('action', 'pts_dt_banner_fold');
    body.append('nonce', banner.getAttribute('data-nonce') || '');
    body.append('folded', folded ? '1' : '0');
    if (window.fetch) {
      window.fetch(banner.getAttribute('data-ajax-url'), { method: 'POST', credentials: 'same-origin', body: body }).catch(function () {});
    }
  });
})();
