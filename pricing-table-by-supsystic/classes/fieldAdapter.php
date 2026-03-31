<?php
#[\AllowDynamicProperties]
class fieldAdapterPts
{
  const DB = 'dbPts';
  const HTML = 'htmlPts';
  const STR = 'str';
  public static $userfieldDest = ['registration', 'shipping', 'billing'];
  public static $countries = [];
  public static $states = [];
  public static function _($fieldOrValue, $method, $type)
  {
    if (method_exists('fieldAdapterPts', $method)) {
      switch ($type) {
        case self::DB:
          return self::$method($fieldOrValue);
          break;
        case self::HTML:
          self::$method($fieldOrValue);
          break;
        case self::STR:
          return self::$method($fieldOrValue);
          break;
      }
    }
    return $fieldOrValue;
  }
  public static function userFieldDestHtml($field)
  {
    $field->htmlParams['optionsPts'] = [];
    if (!is_array($field->value)) {
      if (empty($field->value)) {
        $field->value = [];
      } else {
        $field->value = json_decode($field->value);
      }
    }
    foreach (self::$userfieldDest as $d) {
      $field->htmlParams['optionsPts'][] = [
        'id' => $d,
        'text' => $d,
        'checked' => in_array($d, $field->value),
      ];
    }
  }
  public static function userFieldDestToDB($value)
  {
    return utilsPts::jsonEncode($value);
  }
  public static function userFieldDestFromDB($value)
  {
    return utilsPts::jsonDecode($value);
  }
  public static function taxDataHtml($field)
  {
    $listOfDest = [];
    if (!is_array($field->value)) {
      if (empty($field->value)) {
        $field->value = [];
      } else {
        $field->value = (array) json_decode($field->value, true);
      }
    }
    foreach (self::$userfieldDest as $d) {
      $listOfDest[] = [
        'id' => $d,
        'text' => $d,
        'checked' => is_array($field->value['dest']) && in_array($d, $field->value['dest']),
      ];
    }
    $categories = framePts::_()->getModule('products')->getCategories();
    $brands = framePts::_()->getModule('products')->getBrands();
    $cOptions = [];
    $bOptions = [];
    if (!empty($categories)) {
      if (!is_array($field->value['categories'])) {
        $field->value['categories'] = [];
      }
      foreach ($categories as $c) {
        $cOptions[] = [
          'id' => $c->term_taxonomy_id,
          'text' => $c->cat_name,
          'checked' => in_array($c->term_taxonomy_id, $field->value['categories']),
        ];
      }
    }
    if (!empty($brands)) {
      if (!is_array($field->value['brands'])) {
        $field->value['brands'] = [];
      }
      foreach ($brands as $b) {
        $bOptions[] = [
          'id' => $b->term_taxonomy_id,
          'text' => $b->cat_name,
          'checked' => in_array($b->term_taxonomy_id, $field->value['brands']),
        ];
      }
    }
    return '<div>' .
      __('Apply To', PTS_LANG_CODE) .
      '
            <div id="tax_address">
                <b>' .
      __('Address', PTS_LANG_CODE) .
      '</b><br />
                ' .
      __('Destination', PTS_LANG_CODE) .
      ':' .
      htmlPts::checkboxlist('params[dest]', [
        'optionsPts' => $listOfDest,
      ]) .
      '<br />
                ' .
      __('Country', PTS_LANG_CODE) .
      ':' .
      htmlPts::countryList('params[country]', [
        'notSelected' => true,
        'value' => $field->value['country'],
      ]) .
      '<br />
            </div>
            <div id="tax_category">
                <b>' .
      __('Categories', PTS_LANG_CODE) .
      '</b><br />
                ' .
      (empty($cOptions)
        ? __('You have no categories', PTS_LANG_CODE)
        : htmlPts::checkboxlist('params[categories][]', [
          'optionsPts' => $cOptions,
        ])) .
      '<br />
                    <b>' .
      __('Brands', PTS_LANG_CODE) .
      '</b><br />
                ' .
      (empty($bOptions)
        ? __('You have no brands', PTS_LANG_CODE)
        : htmlPts::checkboxlist('params[brands][]', [
          'optionsPts' => $bOptions,
        ])) .
      '<br />
            </div>
            <div>' .
      __('Tax Rate', PTS_LANG_CODE) .
      ': ' .
      htmlPts::text('params[rate]', [
        'value' => $field->value['rate'],
      ]) .
      '</div>
            <div>' .
      __('Absolute', PTS_LANG_CODE) .
      ': ' .
      htmlPts::checkbox('params[absolute]', [
        'checked' => $field->value['absolute'],
      ]) .
      '</div>
        </div>';
  }
  public static function displayCountry($cid, $key = 'name')
  {
    if ($key == 'name') {
      $countries = self::getCountries();
      return $countries[$cid];
    } else {
      if (empty(self::$countries)) {
        self::$countries = self::getCachedCountries();
      }
      foreach (self::$countries as $c) {
        if ($c['id'] == $cid) {
          return $c[$key];
        }
      }
    }
    return false;
  }
  public static function displayState($sid, $key = 'name')
  {
    $states = self::getStates();
    return empty($states[$sid]) ? $sid : $states[$sid][$key];
  }
  public static function getCountries($notSelected = false)
  {
    static $options = [];
    if (empty($options[$notSelected])) {
      $options[$notSelected] = [];
      if (empty(self::$countries)) {
        self::$countries = self::getCachedCountries();
      }
      if ($notSelected) {
        $options[$notSelected][0] = is_bool($notSelected) ? __('Not selected', PTS_LANG_CODE) : __($notSelected);
      }
      foreach (self::$countries as $c) {
        $options[$notSelected][$c['id']] = $c['name'];
      }
    }
    return $options[$notSelected];
  }
  public static function getStates($notSelected = false)
  {
    static $options = [];
    if (empty($options[$notSelected])) {
      $options[$notSelected] = [];
      if (empty(self::$states)) {
        self::$states = self::getCachedStates();
      }
      if ($notSelected) {
        $notSelectedLabel = is_bool($notSelected) ? 'Not selected' : $notSelected;
        $options[$notSelected][0] = [
          'name' => __($notSelectedLabel),
          'country_id' => null,
        ];
      }
      foreach (self::$states as $s) {
        $options[$notSelected][$s['id']] = $s;
      }
    }
    return $options[$notSelected];
  }
  public static function getExtraFieldOptions($field_id)
  {
    $output = '';
    if ($field_id == 0) {
      return '';
    }
    $options = framePts::_()->getModule('optionsPts')->getHelper()->getOptions($field_id);
    if (!empty($options)) {
      foreach ($options as $key => $value) {
        $output .= '<p>' . $value . '<span class="delete_option" rel="' . $key . '"></span></p>';
      }
    }
    return $output;
  }
  public static function getFieldAttributes($params)
  {
    $output = '';
    if (!empty($params->attr)) {
      foreach ($params->attr as $key => $value) {
        $output .= __($key) . ':<br />';
        $output .=
          htmlPts::text('params[attr][' . $key . ']', [
            'value' => $value,
          ]) . '<br />';
      }
    } else {
      $output .= __('class', PTS_LANG_CODE) . ':<br />';
      $output .=
        htmlPts::text('params[attr][class]', [
          'value' => '',
        ]) . '<br />';
      $output .= __('id', PTS_LANG_CODE) . ':<br />';
      $output .=
        htmlPts::text('params[attr][id]', [
          'value' => '',
        ]) . '<br />';
    }
    return $output;
  }
  static function productFieldCategories($field)
  {
    if (!empty($field->htmlParams['optionsPts'])) {
      return;
    }
  }
  public static function intToDB($val)
  {
    return intval($val);
  }
  public static function floatToDB($val)
  {
    return floatval($val);
  }
  public static function getCachedCountries($clearCache = false)
  {
    if (empty(self::$countries) || $clearCache) {
      self::$countries = framePts::_()->getTable('countries')->getAll('id, name, iso_code_2, iso_code_3');
    }
    return self::$countries;
  }
  public static function getCachedStates($clearCache = false)
  {
    if (empty(self::$states) || $clearCache) {
      self::$states = framePts::_()
        ->getTable('states')
        ->leftJoin(framePts::_()->getTable('countries'), 'country_id')->getAll('toe_states.id,
					toe_states.name,
					toe_states.code,
					toe_states.country_id,
					toe_cry.name AS c_name,
					toe_cry.iso_code_2 AS c_iso_code_2,
					toe_cry.iso_code_3 AS c_iso_code_3');
    }
    return self::$states;
  }
}
