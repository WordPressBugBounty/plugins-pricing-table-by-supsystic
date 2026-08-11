<?php
#[\AllowDynamicProperties]
class dispatcherPts
{
  protected static $_pref = 'PTS_';

  public static function addAction($tag, $function_to_add, $priority = 10, $accepted_args = 1)
  {
    if (strpos($tag, 'PTS_') === false) {
      $tag = self::$_pref . $tag;
    }
    return add_action($tag, $function_to_add, $priority, $accepted_args);
  }
  public static function doAction($tag)
  {
    $allArgs = func_get_args();
    if (strpos($tag, 'PTS_') === false) {
      $tag = self::$_pref . $tag;
    }
    $numArgs = count($allArgs);
    if ($numArgs > 2) {
      $args = array_slice($allArgs, 1);
    } elseif ($numArgs == 2) {
      $args = $allArgs[1];
    } else {
      $args = null;
    }
    return do_action($tag, $args);
  }
  public static function addFilter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
  {
    if (strpos($tag, 'PTS_') === false) {
      $tag = self::$_pref . $tag;
    }
    return add_filter($tag, $function_to_add, $priority, $accepted_args);
  }
  public static function applyFilters($tag, $value)
  {
    $allArgs = func_get_args();
    if (strpos($tag, 'PTS_') === false) {
      $tag = self::$_pref . $tag;
    }
    if (count($allArgs) > 2) {
      $args = $allArgs;
      $args[0] = $tag;
      return call_user_func_array('apply_filters', $args);
    } else {
      return apply_filters($tag, $value);
    }
  }
}
