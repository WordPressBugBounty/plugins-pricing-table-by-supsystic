<?php
#[\AllowDynamicProperties]
class reqPts
{
  protected static $_requestData;
  protected static $_requestMethod;
  protected static $_allowedHtml;
  public static function init()
  {
    reqPts::safeStyleWpKses();
  }
  public static function startSession()
  {
    if (!utilsPts::isSessionStarted()) {
      session_start();
    }
  }
  public static function supStrRgbToHex($color)
  {
    preg_match_all('/\((.+?)\)/', $color, $matches);
    if (!empty($matches[1][0])) {
      $rgb = explode(',', $matches[1][0]);
      $size = count($rgb);
      if ($size == 3 || $size == 4) {
        if ($size == 4) {
          $alpha = array_pop($rgb);
          $alpha = floatval(trim($alpha));
          $alpha = ceil(($alpha * (255 * 100)) / 100);
          array_push($rgb, $alpha);
        }

        $result = '#';
        foreach ($rgb as $row) {
          $result .= str_pad(dechex(trim($row)), 2, '0', STR_PAD_LEFT);
        }

        return $result;
      }
    }

    return false;
  }
  public static function sanitizeString($str)
  {
    $allowedHtml = self::getAllowedHtml();
    if (!empty($str) && is_string($str)) {
      $str = htmlspecialchars_decode($str);

      $re = '/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/';
      $str = preg_replace_callback(
        $re,
        function ($m) {
          return self::supStrRgbToHex($m[0]);
        },
        $str,
      );

      $re = '/rgba\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\d*(?:\.\d+)?\)/';
      $str = preg_replace_callback(
        $re,
        function ($m) {
          return self::supStrRgbToHex($m[0]);
        },
        $str,
      );

      $str = wp_kses($str, $allowedHtml);
    }
    return $str;
  }
  public static function getAllowedHtml()
  {
    if (empty(self::$_allowedHtml)) {
      global $allowedposttags, $allowedtags, $allowedentitynames;
      self::$_allowedHtml = array_merge_recursive($allowedposttags, $allowedtags);
      $html_tags = [
        'a',
        'abbr',
        'address',
        'area',
        'article',
        'aside',
        'audio',
        'b',
        'base',
        'bdi',
        'bdo',
        'blockquote',
        'body',
        'br',
        'button',
        'canvas',
        'caption',
        'cite',
        'code',
        'col',
        'colgroup',
        'data',
        'datalist',
        'dd',
        'del',
        'details',
        'dfn',
        'dialog',
        'div',
        'dl',
        'dt',
        'em',
        'embed',
        'fieldset',
        'figcaption',
        'figure',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'head',
        'header',
        'hr',
        'html',
        'i',
        'iframe',
        'img',
        'input',
        'ins',
        'kbd',
        'label',
        'legend',
        'li',
        'link',
        'main',
        'map',
        'mark',
        'menu',
        'meta',
        'meter',
        'nav',
        'noscript',
        'object',
        'ol',
        'optgroup',
        'option',
        'output',
        'p',
        'param',
        'picture',
        'pre',
        'progress',
        'q',
        'rp',
        'rt',
        'ruby',
        's',
        'samp',
        'script',
        'section',
        'select',
        'small',
        'source',
        'span',
        'strong',
        'style',
        'sub',
        'summary',
        'sup',
        'svg',
        'table',
        'tbody',
        'td',
        'template',
        'textarea',
        'tfoot',
        'th',
        'thead',
        'time',
        'title',
        'tr',
        'track',
        'u',
        'ul',
        'var',
        'video',
        'wbr',
      ];
      $html_attributes = [
        'accept',
        'accept-charset',
        'accesskey',
        'action',
        'align',
        'alt',
        'async',
        'autocapitalize',
        'autocomplete',
        'autofocus',
        'autoplay',
        'charset',
        'checked',
        'cite',
        'class',
        'cols',
        'colspan',
        'content',
        'contenteditable',
        'controls',
        'coords',
        'crossorigin',
        'data-*',
        'datetime',
        'decoding',
        'default',
        'defer',
        'dir',
        'dirname',
        'disabled',
        'download',
        'draggable',
        'enctype',
        'enterkeyhint',
        'for',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'headers',
        'height',
        'hidden',
        'high',
        'href',
        'hreflang',
        'http-equiv',
        'id',
        'inputmode',
        'integrity',
        'ismap',
        'kind',
        'label',
        'lang',
        'list',
        'loading',
        'loop',
        'low',
        'max',
        'maxlength',
        'media',
        'method',
        'min',
        'minlength',
        'multiple',
        'muted',
        'name',
        'novalidate',
        'open',
        'optimum',
        'pattern',
        'placeholder',
        'playsinline',
        'poster',
        'preload',
        'readonly',
        'referrerpolicy',
        'rel',
        'required',
        'reversed',
        'rows',
        'rowspan',
        'sandbox',
        'scope',
        'selected',
        'shape',
        'size',
        'sizes',
        'slot',
        'span',
        'spellcheck',
        'src',
        'srcdoc',
        'srclang',
        'srcset',
        'start',
        'step',
        'style',
        'tabindex',
        'target',
        'title',
        'translate',
        'type',
        'usemap',
        'value',
        'width',
        'wrap',
        'resize',
        'aria-controls',
        'aria-describedby',
        'aria-expanded',
        'aria-label',
        'aria-live',
        'aria-sort',
        'data-alpha',
        'data-alpha-enabled',
        'data-confirmation',
        'data-currency_postfix',
        'data-dt-idx',
        'data-from',
        'data-iter-num',
        'data-grid_num',
        'data-html-id',
        'data-maxval',
        'data-minval',
        'data-option-array-index',
        'data-settings-tab',
        'data-show-mobile',
        'data-show-pc',
        'data-skin',
        'data-skin_grid',
        'data-skin_labels_fromto',
        'data-skin_labels_minmax',
        'data-slug',
        'data-tab',
        'data-to',
        'data-toggle-name',
        'data-toggle-val',
        'data-type',
        'data-uniqid',
        'role',
        'data-notice-label',
        'data-notice-label-desc',
      ];
      foreach ($html_tags as $tag) {
        if (!isset(self::$_allowedHtml[$tag])) {
          self::$_allowedHtml[$tag] = [];
        }
        self::$_allowedHtml[$tag] = array_merge(self::$_allowedHtml[$tag], array_fill_keys($html_attributes, true));
        self::$_allowedHtml[$tag]['style'] = true;
      }
    }
    return self::$_allowedHtml;
  }

  public static function safeStyleWpKses()
  {
    add_filter('safe_style_css', function ($styles) {
      $styles[] = 'display';
      $styles[] = 'float';
      $styles[] = 'margin';
      $styles[] = 'padding';
      return $styles;
    });
  }

  public static function sanitize_array(&$array, $parentKey = '')
  {
    $allowed = '<div>,<span>,<pre>,<p>,<small>,<br>,<hr>,<hgroup>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,
        <ul>,<ol>,<li>,<dl>,<dt>,<dd>,<strong>,<em>,<b>,<i>,<u>,
        <img>,<a>,<abbr>,<address>,<blockquote>,<area>,<audio>,<video>,
        <form>,<fieldset>,<label>,<input>,<textarea>,
        <caption>,<table>,<tbody>,<td>,<tfoot>,<th>,<thead>,<tr>,
        <iframe>,<select>,<option>';
    foreach ($array as $key => &$value) {
      // $keys = array(
      //    'txt_item_html',
      //    'img_item_html',
      //    'icon_item_html',
      //    'new_cell_html',
      //    'new_column_html'
      // );
      // if ((in_array($parentKey, $keys) && $key == 'val') || $key == 'html') {
      //    $re = '/data-toggle-[0-9]+=\\\\"(.*?)\\\\"/m';
      //    $newValue = preg_replace_callback($re, function ($matches) {
      //       $patterns[0] = '/</';
      //       $patterns[1] = '/>/';
      //       $replacements[1] = '&lt;';
      //       $replacements[0] = '&gt;';
      //       $string = preg_replace($patterns, $replacements, $matches[0]);
      //       return $string;
      //    }
      //    , $value);
      //    $value = $newValue;
      //    $value = strip_tags($value, $allowed);
      //    $value = self::sanitizeString($value);
      // }
      // else {
      if (!is_array($value)) {
        $value = self::sanitizeString($value);
      } else {
        $parentKey = $key;
        self::sanitize_array($value, $parentKey);
      }
      //}
    }
    return $array;
  }

  public static function getVar($name, $from = 'all', $default = null)
  {
    $from = strtolower($from);
    if ($from == 'all') {
      if (isset($_GET[$name])) {
        $from = 'get';
      } elseif (isset($_POST[$name])) {
        $from = 'post';
      }
    }

    switch ($from) {
      case 'get':
        if (isset($_GET[$name])) {
          if (is_array($_GET[$name])) {
            return self::sanitize_array($_GET[$name]);
          } else {
            return sanitize_text_field($_GET[$name]);
          }
        }
        break;
      case 'post':
        if (isset($_POST[$name])) {
          if (is_array($_POST[$name])) {
            return self::sanitize_array($_POST[$name]);
          } else {
            return sanitize_text_field($_POST[$name]);
          }
        }
        break;
      case 'session':
        if (isset($_SESSION[$name])) {
          if (is_array($_SESSION[$name])) {
            return self::sanitize_array($_SESSION[$name]);
          } else {
            return sanitize_text_field($_SESSION[$name]);
          }
        }
        break;
      case 'server':
        if (isset($_SERVER[$name])) {
          if (is_array($_SERVER[$name])) {
            return self::sanitize_array($_SERVER[$name]);
          } else {
            return sanitize_text_field($_SERVER[$name]);
          }
        }
        break;
      case 'cookie':
        if (isset($_COOKIE[$name])) {
          $value = sanitize_text_field($_COOKIE[$name]);
          if (strpos($value, '_JSON:') === 0) {
            $value = utilsPts::jsonDecode(sanitize_text_field($value), array_pop(explode('_JSON:', sanitize_text_field($value))));
          }
          if (is_array($value)) {
            $value = sanitize_array($value);
          } elseif (is_string($value)) {
            $value = sanitize_text_field($value);
          }
          return $value;
        }
        break;
    }
    return $default;
  }
  public static function isEmpty($name, $from = 'all')
  {
    $val = self::getVar($name, $from);
    return empty($val);
  }
  public static function setVar($name, $val, $in = 'input', $params = [])
  {
    $in = strtolower($in);
    if (is_array($val)) {
      $val = self::sanitize_array($val);
    } else {
      $val = sanitize_text_field($val);
    }
    switch ($in) {
      case 'get':
        $_GET[$name] = $val;
        break;
      case 'post':
        $_POST[$name] = $val;
        break;
      case 'session':
        $_SESSION[$name] = $val;
        break;
      case 'cookie':
        $expire = isset($params['expire']) ? time() + $params['expire'] : 0;
        $path = isset($params['path']) ? $params['path'] : '/';
        if (is_array($val) || is_object($val)) {
          $saveVal = '_JSON:' . utilsPts::jsonEncode($val);
        } else {
          $saveVal = $val;
        }
        setcookie($name, $saveVal, $expire, $path);
        break;
    }
  }
  public static function clearVar($name, $in = 'input', $params = [])
  {
    $in = strtolower($in);
    switch ($in) {
      case 'get':
        if (isset($_GET[$name])) {
          unset($_GET[$name]);
        }
        break;
      case 'post':
        if (isset($_POST[$name])) {
          unset($_POST[$name]);
        }
        break;
      case 'session':
        if (isset($_SESSION[$name])) {
          unset($_SESSION[$name]);
        }
        break;
      case 'cookie':
        $path = isset($params['path']) ? $params['path'] : '/';
        setcookie($name, '', time() - 3600, $path);
        break;
    }
  }
  public static function get($what)
  {
    $what = strtolower($what);
    switch ($what) {
      case 'get':
        if (is_array($_GET)) {
          return self::sanitize_array($_GET);
        } else {
          return sanitize_text_field($_GET);
        }
        break;
      case 'post':
        if (is_array($_POST)) {
          return self::sanitize_array($_POST);
        } else {
          return sanitize_text_field($_POST);
        }
        break;
      case 'session':
        if (is_array($_SESSION)) {
          return self::sanitize_array($_SESSION);
        } else {
          return sanitize_text_field($_SESSION);
        }
        break;
    }
    return null;
  }
  public static function getMethod()
  {
    if (!self::$_requestMethod) {
      self::$_requestMethod = strtoupper(self::getVar('method', 'all', $_SERVER['REQUEST_METHOD']));
    }
    return self::$_requestMethod;
  }
  public static function getAdminPage()
  {
    $pagePath = self::getVar('page');
    if (!empty($pagePath) && strpos($pagePath, '/') !== false) {
      $pagePath = explode('/', $pagePath);
      return str_replace('.php', '', $pagePath[count($pagePath) - 1]);
    }
    return false;
  }
  public static function getRequestUri()
  {
    return $_SERVER['REQUEST_URI'];
  }
  public static function getMode()
  {
    $mod = '';
    if (!($mod = self::getVar('mod'))) {
      //Frontend usage
      $mod = self::getVar('page');
    } //Admin usage
    return $mod;
  }
}
