/**
 * Search of the table editor settings (tablesEditAdmin.php): while something is typed, every group shows its matching
 * rows in their cards and the tabs step back; an empty search brings the active tab back. Only classes change, so the
 * fields and the tab script of admin.tables.edit.js stay as they are. The undo buffer puts back the settings markup
 * it copied, so the search is applied again when that happens.
 */
(function ($) {
  'use strict';

  $(function () {
    var $ed = $('[data-pts-ed]');
    if (!$ed.length) {
      return;
    }
    var $search = $ed.find('[data-pts-ed-search]'),
      $empty = $ed.find('[data-pts-ed-empty]'),
      content = $ed.find('.ptsSettingsContent').get(0),
      busy = false;

    function norm(text) {
      return String(text || '')
        .toLowerCase()
        .replace(/\s+/g, ' ')
        .trim();
    }

    function apply() {
      busy = true;
      var query = norm($search.val()),
        $panes = $ed.find('.ptsSettingsContent > div'),
        found = 0;

      $ed.toggleClass('is-searching', query !== '');
      $ed.find('.pts-ed-miss').removeClass('pts-ed-miss');
      $ed.find('.pts-ed-card-miss').removeClass('pts-ed-card-miss');
      $panes.removeClass('pts-ed-pane-miss');

      if (query !== '') {
        $panes.each(function () {
          var paneFound = 0;
          $(this)
            .find('.pts-ed-card')
            .each(function () {
              var $card = $(this),
                cardHit = norm($card.find('.pts-ed-card-title').text()).indexOf(query) !== -1,
                $rows = $card.find('.pts-ed-row'),
                hits = 0;
              $rows.each(function () {
                var $row = $(this),
                  hit = cardHit || norm($row.find('.pts-ed-label').text()).indexOf(query) !== -1;
                $row.toggleClass('pts-ed-miss', !hit);
                if (hit && !$row.hasClass('ptsDisplNone')) {
                  hits++;
                }
              });
              // A card without rows (the switch options) is found by its title
              if (!$rows.length && cardHit) {
                hits = 1;
              }
              $card.toggleClass('pts-ed-card-miss', hits === 0);
              paneFound += hits;
            });
          $(this).toggleClass('pts-ed-pane-miss', paneFound === 0);
          found += paneFound;
        });
      }
      $empty.prop('hidden', query === '' || found > 0);
      busy = false;
    }

    $search.on('input', apply).on('keydown', function (event) {
      if (event.key === 'Escape') {
        $(this).val('');
        apply();
      }
    });
    // A group tab ends the search
    $ed.on('click', '.ptsSettingsTabs a.nav-tab', function () {
      if ($search.val()) {
        $search.val('');
        apply();
      }
    });
    // Undo / redo replace the settings markup
    if (content && window.MutationObserver) {
      new MutationObserver(function () {
        if (!busy && $search.val()) {
          apply();
        }
      }).observe(content, { childList: true });
    }
  });
})(jQuery);
