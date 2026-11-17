jQuery(document).ready(function () {
  var tblId = 'ptsPagesTbl';
  // The list of tables (not the export list, which shares this script) gets the shortcode and the row actions
  var isTablesList = jQuery('#ptsPagesRemoveGroupBtn').length > 0,
    colNames = [toeLangPts('ID'), toeLangPts('Label'), toeLangPts('Date Created')],
    colModel = [
      { name: 'id', index: 'id', searchoptions: { sopt: ['eq'] }, width: '50', align: 'center' },
      { name: 'label', index: 'label', searchoptions: { sopt: ['eq'] }, align: 'center' },
      { name: 'date_created', index: 'date_created', searchoptions: { sopt: ['eq'] }, align: 'center' },
    ];
  if (isTablesList) {
    colNames.splice(2, 0, toeLangPts('Shortcode'));
    colModel.splice(2, 0, { name: 'pts_shortcode', index: 'pts_shortcode', sortable: false, search: false, align: 'center', formatter: ptsListShortcodeCell });
    colNames.push(toeLangPts('Actions'));
    colModel.push({ name: 'pts_actions', index: 'pts_actions', sortable: false, search: false, width: '122', fixed: true, align: 'center', formatter: ptsListActionsCell });
  }
  jQuery('#' + tblId).jqGrid({
    url: ptsTblDataUrl,
    datatype: 'json',
    autowidth: true,
    shrinkToFit: true,
    colNames: colNames,
    colModel: colModel,
    // Clicks on the shortcode and the row actions do not select the row
    beforeSelectRow: function (rowid, e) {
      return !(e && e.target && jQuery(e.target).closest('.pts-row-actions, .pts-code').length);
    },
    postData: {
      search: {
        text_like: jQuery('#' + tblId + 'SearchTxt').val(),
      },
    },
    rowNum: 10,
    rowList: [10, 20, 30, 1000],
    pager: '#' + tblId + 'Nav',
    sortname: 'id',
    viewrecords: true,
    sortorder: 'desc',
    jsonReader: { repeatitems: false, id: '0' },
    caption: toeLangPts('Current Table'),
    height: '100%',
    emptyrecords: '', //toeLangPts('You have no Tables for now.')
    multiselect: true,
    multiselectWidth: 44,
    onSelectRow: function (rowid, e) {
      var tblId = jQuery(this).attr('id'),
        selectedRowIds = jQuery('#' + tblId).jqGrid('getGridParam', 'selarrrow'),
        totalRows = jQuery('#' + tblId).getGridParam('reccount'),
        totalRowsSelected = selectedRowIds.length;
      if (totalRowsSelected) {
        jQuery('#ptsPagesRemoveGroupBtn').removeAttr('disabled');
        if (totalRowsSelected == totalRows) {
          jQuery('#cb_' + tblId).prop('indeterminate', false);
          jQuery('#cb_' + tblId).attr('checked', 'checked');
        } else {
          jQuery('#cb_' + tblId).prop('indeterminate', true);
        }
      } else {
        jQuery('#ptsPagesRemoveGroupBtn').attr('disabled', 'disabled');
        jQuery('#cb_' + tblId).prop('indeterminate', false);
        jQuery('#cb_' + tblId).removeAttr('checked');
      }
    },
    gridComplete: function (a, b, c) {
      var tblId = jQuery(this).attr('id');
      jQuery('#ptsPagesRemoveGroupBtn').attr('disabled', 'disabled');
      jQuery('#cb_' + tblId).prop('indeterminate', false);
      jQuery('#cb_' + tblId).removeAttr('checked');
    },
    loadComplete: function () {
      var tblId = jQuery(this).attr('id');
      if (this.p.reccount === 0) {
        jQuery(this).hide();
        jQuery('#' + tblId + 'Nav_right').hide();
        jQuery('#' + tblId + 'EmptyMsg').show();
      } else {
        jQuery(this).show();
        jQuery('#' + tblId + 'EmptyMsg').hide();
      }
    },
  });
  // The grid follows the width of its card; on small screens the list keeps the label and the actions
  var ptsFitGrid = function () {
    var $grid = jQuery('#' + tblId),
      width = $grid.closest('#containerWrapper, .supsystic-imex-page').width();
    if (!width || !$grid.length || !$grid[0].grid) {
      return;
    }
    // The date goes first, so the shortcode keeps room for its whole text; on phones the label and the actions stay
    $grid.jqGrid(width < (isTablesList ? 1100 : 600) ? 'hideCol' : 'showCol', 'date_created');
    if (isTablesList) {
      $grid.jqGrid(width < 600 ? 'hideCol' : 'showCol', 'pts_shortcode');
    }
    // On a phone the name of the table needs the room of the ID column
    $grid.jqGrid(width < 360 ? 'hideCol' : 'showCol', 'id');
    $grid.jqGrid('setGridWidth', width, true);
  };
  var ptsFitTimer = null;
  jQuery(window).on('resize', function () {
    clearTimeout(ptsFitTimer);
    ptsFitTimer = setTimeout(ptsFitGrid, 150);
  });
  setTimeout(ptsFitGrid, 0);
  jQuery('#' + tblId + 'NavShell').append(jQuery('#' + tblId + 'Nav'));
  jQuery('#' + tblId + 'Nav')
    .find('.ui-pg-selbox')
    .insertAfter(jQuery('#' + tblId + 'Nav').find('.ui-paging-info'));
  jQuery('#' + tblId + 'Nav')
    .find('.ui-pg-table td:first')
    .remove();
  // Make navigation tabs to be with our additional buttons - in one row
  jQuery('#' + tblId + 'Nav_center')
    .prepend(jQuery('#' + tblId + 'NavBtnsShell'))
    .css({
      width: '80%',
      'white-space': 'normal',
      'padding-top': '8px',
    });
  jQuery('#' + tblId + 'SearchTxt').keyup(function () {
    var searchVal = jQuery.trim(jQuery(this).val());
    if (searchVal && searchVal != '') {
      ptsGridDoListSearch(
        {
          text_like: searchVal,
        },
        tblId
      );
    }
  });

  jQuery('#' + tblId + 'EmptyMsg').insertAfter(jQuery('#' + tblId + '').parent());
  jQuery('#' + tblId + '').jqGrid('navGrid', '#' + tblId + 'Nav', { edit: false, add: false, del: false });
  jQuery('#cb_' + tblId + '').change(function () {
    jQuery(this).attr('checked') ? jQuery('#ptsPagesRemoveGroupBtn').removeAttr('disabled') : jQuery('#ptsPagesRemoveGroupBtn').attr('disabled', 'disabled');
  });
  // Row actions: copy the shortcode, delete one table
  jQuery('#' + tblId).on('click', '.pts-act-copy', function () {
    var $btn = jQuery(this),
      code = $btn.attr('data-code');
    ptsListCopyText(code);
    $btn.addClass('is-done').find('.fa').removeClass('fa-clipboard').addClass('fa-check');
    setTimeout(function () {
      $btn.removeClass('is-done').find('.fa').removeClass('fa-check').addClass('fa-clipboard');
    }, 1400);
    return false;
  });
  jQuery('#' + tblId).on('click', '.pts-act-delete', function () {
    var id = jQuery(this).attr('data-id'),
      label = jQuery(this).attr('data-label');
    if (confirm(toeLangPts('Are you sure want to remove "' + label + '" Table?'))) {
      jQuery.sendFormPts({
        btn: this,
        data: { mod: 'tables', action: 'removeGroup', pts_nonce: PTS_NONCE['pts_nonce'], listIds: [id] },
        onSuccess: function (res) {
          if (!res.error) {
            jQuery('#ptsPagesTbl').trigger('reloadGrid');
          }
        },
      });
    }
    return false;
  });
  jQuery('#ptsPagesRemoveGroupBtn').click(function () {
    var selectedRowIds = jQuery('#ptsPagesTbl').jqGrid('getGridParam', 'selarrrow'),
      listIds = [];
    for (var i in selectedRowIds) {
      var rowData = jQuery('#ptsPagesTbl').jqGrid('getRowData', selectedRowIds[i]);
      listIds.push(rowData.id);
    }
    var popupLabel = '';
    if (listIds.length == 1) {
      // In table label cell there can be some additional links
      var labelCellData = ptsGetGridColDataById(listIds[0], 'label', 'ptsPagesTbl');
      popupLabel = jQuery(labelCellData).text();
    }
    var confirmMsg = listIds.length > 1 ? toeLangPts('Are you sur want to remove ' + listIds.length + ' Tables?') : toeLangPts('Are you sure want to remove "' + popupLabel + '" Table?');
    if (confirm(confirmMsg)) {
      jQuery.sendFormPts({
        btn: this,
        data: { mod: 'tables', action: 'removeGroup', pts_nonce: PTS_NONCE['pts_nonce'], listIds: listIds },
        onSuccess: function (res) {
          if (!res.error) {
            jQuery('#ptsPagesTbl').trigger('reloadGrid');
          }
        },
      });
    }
    return false;
  });
});

function ptsListShortcodeCell(cellValue, options, row) {
  var code = '[supsystic-price-table id=' + parseInt(row.id, 10) + ']';
  return '<span class="pts-code"><code>' + code + '</code></span>';
}
function ptsListActionsCell(cellValue, options, row) {
  var id = parseInt(row.id, 10),
    $label = jQuery('<div>' + (row.label || '') + '</div>'),
    editUrl = $label.find('a').attr('href') || '',
    label = jQuery.trim($label.text()),
    code = '[supsystic-price-table id=' + id + ']',
    esc = function (str) {
      return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    };
  return (
    '<span class="pts-row-actions">' +
    (editUrl ? '<a class="button pts-act-edit" href="' + esc(editUrl) + '" title="' + esc(toeLangPts('Edit')) + '" aria-label="' + esc(toeLangPts('Edit')) + '"><i class="fa fa-pencil" aria-hidden="true"></i></a>' : '') +
    '<button type="button" class="button pts-act-copy" data-code="' + esc(code) + '" title="' + esc(toeLangPts('Copy shortcode')) + '" aria-label="' + esc(toeLangPts('Copy shortcode')) + '"><i class="fa fa-clipboard" aria-hidden="true"></i></button>' +
    '<button type="button" class="button pts-act-delete pts-act-danger" data-id="' + id + '" data-label="' + esc(label) + '" title="' + esc(toeLangPts('Delete')) + '" aria-label="' + esc(toeLangPts('Delete')) + '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>' +
    '</span>'
  );
}
function ptsListCopyText(text) {
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).catch(function () {});
    return;
  }
  var $tmp = jQuery('<textarea readonly style="position:absolute;left:-9999px;"></textarea>').val(text).appendTo('body');
  $tmp.get(0).select();
  try {
    document.execCommand('copy');
  } catch (e) {}
  $tmp.remove();
}
