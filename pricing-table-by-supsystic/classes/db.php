<?php
#[\AllowDynamicProperties]
class dbPts
{
  public static $query = '';
  public static function query($query) {}
  public static function insertID()
  {
    global $wpdb;
    return $wpdb->insert_id;
  }
  public static function numRows()
  {
    global $wpdb;
    return $wpdb->num_rows;
  }
  public static function prepareQuery($query)
  {
    global $wpdb;
    return str_replace(['#__', '^__', '@__'], [$wpdb->prefix, PTS_DB_PREF, $wpdb->prefix . PTS_DB_PREF], $query);
  }
  public static function getError()
  {
    global $wpdb;
    return $wpdb->last_error;
  }
  public static function lastID()
  {
    global $wpdb;
    return $wpdb->insert_id;
  }
  public static function timeToDate($timestamp = 0)
  {
    if ($timestamp) {
      if (!is_numeric($timestamp)) {
        $timestamp = dateToTimestampPts($timestamp);
      }
      return date('Y-m-d', $timestamp);
    } else {
      return date('Y-m-d');
    }
  }
  public static function dateToTime($date)
  {
    if (empty($date)) {
      return '';
    }
    if (strpos($date, PTS_DATE_DL)) {
      return dateToTimestampPts($date);
    }
    $arr = explode('-', $date);
    return dateToTimestampPts($arr[2] . PTS_DATE_DL . $arr[1] . PTS_DATE_DL . $arr[0]);
  }
  public static function exist($table)
  {
    global $wpdb;
    switch ($table) {
      case 'pts_tables':
        $res = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}pts_tables'");
        break;
      case 'pts_modules':
        $res = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}pts_modules'");
        break;
      case 'pts_modules_type':
        $res = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}pts_modules_type'");
        break;
      case 'pts_usage_stat':
        $res = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}pts_usage_stat'");
        break;
    }
    return !empty($res);
  }
  public static function prepareHtml($d)
  {
    if (is_array($d)) {
      foreach ($d as $i => $el) {
        $d[$i] = self::prepareHtml($el);
      }
    } else {
      $d = esc_html($d);
    }
    return $d;
  }
  public static function prepareHtmlIn($d)
  {
    if (is_array($d)) {
      foreach ($d as $i => $el) {
        $d[$i] = self::prepareHtml($el);
      }
    } else {
      $d = wp_filter_nohtml_kses($d);
    }
    return $d;
  }
  public static function escape($data)
  {
    global $wpdb;
    return $wpdb->_escape($data);
  }
}
