<?php
#[\AllowDynamicProperties]
class uriPts
{
  private static $_oneHtmlEnc = false;
  public static function fileToPageParam($file)
  {
    $file = str_replace(DS, '/', $file);
    return substr($file, strpos($file, PTS_PLUG_NAME));
  }
  public static function _($params)
  {
    global $wp_rewrite;
    $link = '';
    if (is_string($params) && (strpos($params, 'http') === 0 || strpos($params, PTS_PLUG_NAME) !== false)) {
      if (self::isHttps()) {
        $params = self::makeHttps($params);
      }
      return $params;
    } elseif (is_array($params) && isset($params['page_id'])) {
      if (is_null($wp_rewrite)) {
        $wp_rewrite = new WP_Rewrite();
      }
      $link = get_page_link($params['page_id']);
      unset($params['page_id']);
    } elseif (is_array($params) && isset($params['baseUrl'])) {
      $link = $params['baseUrl'];
      unset($params['baseUrl']);
    } else {
      $link = PTS_URL;
    }
    if (!empty($params)) {
      $query = is_array($params) ? http_build_query($params, '', '&') : $params;
      $link .= (strpos($link, '?') === false ? '?' : '&') . $query;
    }
    if (self::$_oneHtmlEnc) {
      $link = str_replace('&', '&amp;', $link);
      self::$_oneHtmlEnc = false;
    }
    return $link;
  }
  public static function _e($params)
  {
    echo self::_($params);
  }
  public static function page($id)
  {
    return get_page_link($id);
  }
  public static function mod($name, $action = '', $data = null)
  {
    $params = [
      'mod' => $name,
    ];
    if ($action) {
      $params['action'] = $action;
    }
    $params['pl'] = PTS_CODE;
    if ($data) {
      if (is_array($data)) {
        $params = array_merge($params, $data);
        if (isset($data['reqType']) && $data['reqType'] == 'ajax') {
          $params['baseUrl'] = admin_url('admin-ajax.php');
        }
      } elseif (is_string($data)) {
        $params = http_build_query($params);
        $params .= '&' . $data;
      }
    }
    return self::_($params);
  }
  public static function getCurrent()
  {
    if (!empty($_SERVER['HTTPS'])) {
      return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    } else {
      return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    }
  }
  public static function getFullUrl()
  {
    $url = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
  }
  public static function oneHtmlEnc()
  {
    self::$_oneHtmlEnc = true;
  }
  public static function makeHttps($link)
  {
    if (strpos($link, 'https:') === false) {
      $link = str_replace('http:', 'https:', $link);
    }
    return $link;
  }
  public static function isHttps()
  {
    return is_ssl();
  }
}
