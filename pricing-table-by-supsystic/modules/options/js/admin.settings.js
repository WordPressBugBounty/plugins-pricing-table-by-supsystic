jQuery(document).ready(function () {
  // The roles as chips, like the other Supsystic plugins (the select stays the field that is saved)
  if (jQuery.fn.chosen) {
    jQuery('#ptsSettingsForm select[multiple]').chosen({ width: '100%', placeholder_text_multiple: ' ' });
  }
  jQuery('#ptsSettingsSaveBtn').click(function () {
    jQuery('#ptsSettingsForm').submit();
    return false;
  });
  jQuery('#ptsSettingsForm').submit(function () {
    jQuery(this).sendFormPts({
      btn: jQuery('#ptsSettingsSaveBtn'),
    });
    return false;
  });
});
