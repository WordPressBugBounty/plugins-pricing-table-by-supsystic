<?php
#[\AllowDynamicProperties]
class datePts
{
  public static function _($time = null)
  {
    if (is_null($time)) {
      $time = time();
    }
    return date(PTS_DATE_FORMAT_HIS, $time);
  }
}
