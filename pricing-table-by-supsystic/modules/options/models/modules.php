<?php
class modulesModelPts extends modelPts
{
  public function get($d = [])
  {
    global $wpdb;
    $res = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}pts_modules AS sup_m WHERE code = %s", $d['code']), ARRAY_A);
    return $res;
  }
  public function put($d = [])
  {
    $res = new responsePts();
    $id = $this->_getIDFromReq($d);
    $d = prepareParamsPts($d);
    if (is_numeric($id) && $id) {
      if (isset($d['active'])) {
        $d['active'] = (is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1 ? 1 : 0;
      }
      global $wpdb;
      $tableName = $wpdb->prefix . 'pts_modules';
      $data_where = [
        'id' => $id,
      ];
      // $wpdb->update() returns an int (row count, possibly 0 if nothing actually
      // changed) on success or false on failure - never reassign $res to it, that
      // clobbers the responsePts object and a falsy-but-valid 0 would wrongly be
      // treated as an error below.
      $updateResult = $wpdb->update($tableName, $d, $data_where);
      if ($updateResult !== false) {
        $mod = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pts_modules WHERE " . $wpdb->prepare('id = %s', $id), ARRAY_A);
        $mod = !empty($mod) ? $mod : false;
        if (is_array($mod) && !isset($mod['type_id'])) {
          $mod = $mod[0];
        }
        if ($mod) {
          $newType = $wpdb->get_results("SELECT label FROM {$wpdb->prefix}pts_modules_type WHERE " . $wpdb->prepare('id = %s', $mod['type_id']), ARRAY_A);
          if (is_array($newType) && !isset($newType['label'])) {
            $newType = $newType[0];
          }
          $newType = $newType['label'];
        }
      } else {
        if ($tableErrors = framePts::_()->getTable('modules')->getErrors()) {
          $res->errors = array_merge($res->errors, $tableErrors);
        } else {
          $res->errors[] = __('Module Update Failed', PTS_LANG_CODE);
        }
      }
    } else {
      $res->errors[] = __('Error module ID', PTS_LANG_CODE);
    }
    return $res;
  }
  protected function _getIDFromReq($d = [])
  {
    $id = 0;
    if (isset($d['id'])) {
      $id = $d['id'];
    } elseif (isset($d['code'])) {
      $fromDB = $this->get([
        'code' => $d['code'],
      ]);
      if (isset($fromDB[0]) && $fromDB[0]['id']) {
        $id = $fromDB[0]['id'];
      }
    }
    return $id;
  }
}
