/**
 * PRO feature windows of the free version. Any element with data-pts-upsell="<feature>" opens the window of that
 * feature, if the page has it (templates printed by upsellPts); otherwise the element keeps its own behaviour (a link
 * still goes to the plugin page). The listener runs in the capture phase, so a locked control of the editor opens this
 * window instead of its own panel.
 */
(function () {
  'use strict';

  var dialog = null,
    body = null;

  function setup() {
    if (dialog) {
      return true;
    }
    dialog = document.querySelector('[data-pts-up]');
    body = dialog ? dialog.querySelector('[data-pts-up-body]') : null;
    if (!dialog || !body || typeof dialog.showModal !== 'function') {
      dialog = null;
      return false;
    }
    dialog.addEventListener('click', function (event) {
      // A click on the dimmed backdrop lands on the dialog itself
      if (event.target === dialog || event.target.closest('[data-pts-up-close]')) {
        dialog.close();
      }
    });
    return true;
  }

  /**
   * @param {string} key The feature
   * @return {boolean} Whether the window opened
   */
  function open(key) {
    var template = key ? document.getElementById('pts-up-' + key) : null;
    if (!template || !setup()) {
      return false;
    }
    body.textContent = '';
    body.appendChild(template.content.cloneNode(true));
    if (!dialog.open) {
      dialog.showModal();
    }
    body.scrollTop = 0;
    var cta = body.querySelector('.pts-up-cta');
    if (cta) {
      cta.focus({ preventScroll: true });
    }
    return true;
  }

  document.addEventListener(
    'click',
    function (event) {
      var trigger = event.target.closest ? event.target.closest('[data-pts-upsell]') : null;
      if (trigger && open(trigger.getAttribute('data-pts-upsell'))) {
        event.preventDefault();
        event.stopImmediatePropagation();
      }
    },
    true
  );

  window.PtsUpsell = { open: open };
})();
