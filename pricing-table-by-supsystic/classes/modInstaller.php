<?php
#[\AllowDynamicProperties]
class modInstallerPts
{
  private static $_current = [];
  public static function install($module, $path)
  {
    $exPlugDest = explode('plugins', $path);
    if (!empty($exPlugDest[1])) {
      $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
    }
    $path = $path . DS . $module['code'];
    if (!empty($module) && !empty($path) && is_dir($path)) {
      if (self::isModule($path)) {
        $filesMoved = false;
        if (empty($module['ex_plug_dir'])) {
          $filesMoved = self::moveFiles($module['code'], $path);
        } else {
          $filesMoved = true;
        }
        if ($filesMoved) {
          if (framePts::_()->getTable('modules')->exists($module['code'], 'code')) {
            framePts::_()
              ->getTable('modules')
              ->delete([
                'code' => $module['code'],
              ]);
          }
          if ($module['code'] != 'license') {
            $module['active'] = 0;
          }
          global $wpdb;
          $tableName = $wpdb->prefix . 'pts_modules';
          $res = $wpdb->insert($tableName, $module);
          self::_runModuleInstall($module);
          self::_installTables($module);
          return true;
        } else {
          errorsPts::push(sprintf(__('Move files for %s failed'), $module['code']), errorsPts::MOD_INSTALL);
        }
      } else {
        errorsPts::push(sprintf(__('%s is not plugin module'), $module['code']), errorsPts::MOD_INSTALL);
      }
    }
    return false;
  }
  protected static function _runModuleInstall($module, $action = 'install')
  {
    $moduleLocationDir = PTS_MODULES_DIR;
    if (!empty($module['ex_plug_dir'])) {
      $moduleLocationDir = utilsPts::getPluginDir($module['ex_plug_dir']);
    }
    if (is_dir($moduleLocationDir . $module['code'])) {
      if (!class_exists($module['code'] . strFirstUp(PTS_CODE))) {
        importClassPts($module['code'], $moduleLocationDir . $module['code'] . DS . 'mod.php');
      }
      $moduleClass = toeGetClassNamePts($module['code']);
      $moduleObj = new $moduleClass($module);
      if ($moduleObj) {
        $moduleObj->$action();
      }
    }
  }
  public static function isModule($path)
  {
    return true;
  }
  public static function moveFiles($code, $path)
  {
    if (!is_dir(PTS_MODULES_DIR . $code)) {
      if (mkdir(PTS_MODULES_DIR . $code)) {
        utilsPts::copyDirectories($path, PTS_MODULES_DIR . $code);
        return true;
      } else {
        errorsPts::push(__('Can not create module directory. Try to set permission to ' . PTS_MODULES_DIR . ' directory 755 or 777', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
      }
    } else {
      return true;
    }
    return false;
  }
  private static function _getPluginLocations()
  {
    $locations = [];
    $plug = reqPts::getVar('plugin');
    if (empty($plug)) {
      $plug = reqPts::getVar('checked');
      $plug = $plug[0];
    }
    $locations['plugPath'] = plugin_basename(trim($plug));
    $locations['plugDir'] = dirname(WP_PLUGIN_DIR . DS . $locations['plugPath']);
    $locations['plugMainFile'] = WP_PLUGIN_DIR . DS . $locations['plugPath'];
    $locations['xmlPath'] = $locations['plugDir'] . DS . 'install.xml';
    $locations['extendModPath'] = $locations['plugDir'] . DS . 'install.php';
    return $locations;
  }
  private static function _getModulesFromXml($xmlPath)
  {
    if ($xml = utilsPts::getXml($xmlPath)) {
      if (isset($xml->modules) && isset($xml->modules->mod)) {
        $modules = [];
        $xmlMods = $xml->modules->children();
        foreach ($xmlMods->mod as $mod) {
          $modules[] = $mod;
        }
        if (empty($modules)) {
          errorsPts::push(__('No modules were found in XML file', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
        } else {
          return $modules;
        }
      } else {
        errorsPts::push(__('Invalid XML file', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
      }
    } else {
      errorsPts::push(__('No XML file were found', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
    }
    return false;
  }
  private static function _getExtendModules($locations)
  {
    $modules = [];
    $isExtendModPath = file_exists($locations['extendModPath']);
    $modulesList = $isExtendModPath ? include $locations['extendModPath'] : self::_getModulesFromXml($locations['xmlPath']);
    if (!empty($modulesList)) {
      foreach ($modulesList as $mod) {
        $modData = $isExtendModPath ? $mod : utilsPts::xmlNodeAttrsToArr($mod);
        array_push($modules, $modData);
      }
      if (empty($modules)) {
        errorsPts::push(__('No modules were found in installation file', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
      } else {
        return $modules;
      }
    } else {
      errorsPts::push(__('No installation file were found', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
    }
    return false;
  }
  public static function check($extPlugName = '')
  {
    $locations = self::_getPluginLocations();
    if ($modules = self::_getExtendModules($locations)) {
      // Resolve "license" first: activate() below only lets any other module
      // in this extension come back on if a currently valid license exists,
      // so license itself must already be up to date by the time we get there.
      usort($modules, function ($a, $b) {
        $aCode = is_array($a) ? $a['code'] ?? '' : '';
        $bCode = is_array($b) ? $b['code'] ?? '' : '';
        return ($bCode === 'license' ? 1 : 0) - ($aCode === 'license' ? 1 : 0);
      });
      foreach ($modules as $m) {
        if (!empty($m)) {
          if (framePts::_()->getTable('modules')->exists($m['code'], 'code')) {
            self::activate($m);
          } else {
            if (!self::install($m, $locations['plugDir'])) {
              errorsPts::push(sprintf(__('Install %s failed'), $m['code']), errorsPts::MOD_INSTALL);
            } else {
              self::activate($m);
            }
          }
        }
      }
    } else {
      errorsPts::push(__('Error Activate module', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
    }
    if (errorsPts::haveErrors(errorsPts::MOD_INSTALL)) {
      self::displayErrors();
      return false;
    }
    update_option(PTS_CODE . '_full_installed', 1);
    return true;
  }
  public static function checkActivationMessages() {}
  /**
   * True only when this extension has a "license" module row and it is not
   * currently active -- i.e. when activate() below should withhold every
   * other module until a valid license re-enables them. Extensions that have
   * no license concept at all (no "license" row) are unaffected. Read
   * directly from the table (not via getModule('license'), which would
   * require that module to already be loaded in this request) so it reflects
   * any activation this same check() pass just performed.
   */
  private static function _licenseGateApplies()
  {
    // Query $wpdb directly - dbPts has no get() method at all.
    global $wpdb;
    $active = $wpdb->get_var("SELECT active FROM {$wpdb->prefix}pts_modules WHERE code = 'license'");
    return $active !== null && (int) $active !== 1;
  }
  public static function deactivate()
  {
    $locations = self::_getPluginLocations();
    if ($modules = self::_getExtendModules($locations)) {
      foreach ($modules as $m) {
        if (framePts::_()->moduleActive($m['code'])) {
          global $wpdb;
          $tableName = $wpdb->prefix . 'pts_modules';
          $id = framePts::_()->getModule($m['code'])->getID();
          $data = [
            'id' => $id,
            'active' => 0,
          ];
          $data_where = [
            'id' => $id,
          ];
          // $wpdb->update() returns 0 (falsy but not an error) when the row already
          // had active = 0 - only `false` means the query itself failed.
          $res = $wpdb->update($tableName, $data, $data_where);
          if ($res === false) {
            errorsPts::push(__('Error Deactivation module', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
          }
        }
      }
    }
    if (errorsPts::haveErrors(errorsPts::MOD_INSTALL)) {
      self::displayErrors(false);
      return false;
    }
    return true;
  }
  public static function activate($modDataArr)
  {
    if (!empty($modDataArr['code']) && !framePts::_()->moduleActive($modDataArr['code'])) {
      // Only "license" comes back automatically just because the extension
      // plugin itself was (re)activated. Every other of its modules must only
      // be reactivated once a currently valid license exists -- otherwise a
      // bare deactivate/reactivate of the plugin would silently re-enable
      // every paid feature regardless of license state.
      if ($modDataArr['code'] !== 'license' && self::_licenseGateApplies()) {
        return;
      }
      if (!framePts::_()->getModule('options')) {
        // 'options' is a core module of the base plugin; without it we can't
        // reach the modules table model at all. Bail instead of fataling on a
        // null method call.
        errorsPts::push(__('Core "options" module is not active, cannot activate modules', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
        return;
      }
      $res = framePts::_()
        ->getModule('options')
        ->getModel('modules')
        ->put([
          'code' => $modDataArr['code'],
          'active' => 1,
        ]);
      if (!$res) {
        errorsPts::push(__('Error Activating module', PTS_LANG_CODE), errorsPts::MOD_INSTALL);
      } else {
        $dbModData = framePts::_()
          ->getModule('options')
          ->getModel('modules')
          ->get([
            'code' => $modDataArr['code'],
          ]);
        if (!empty($dbModData) && !empty($dbModData[0])) {
          $modDataArr['ex_plug_dir'] = $dbModData[0]['ex_plug_dir'];
        }
        self::_runModuleInstall($modDataArr, 'activate');
      }
    }
  }
  public static function displayErrors($exit = true)
  {
    $errors = errorsPts::get(errorsPts::MOD_INSTALL);
    foreach ($errors as $e) {
      echo '<b style="color: red;">' . $e . '</b><br />';
    }
    if ($exit) {
      exit();
    }
  }
  public static function uninstall()
  {
    $locations = self::_getPluginLocations();
    $optionsModule = framePts::_()->getModule('options');
    if ($modules = self::_getExtendModules($locations)) {
      foreach ($modules as $m) {
        self::_uninstallTables($m);
        if ($optionsModule) {
          $optionsModule->getModel('modules')->delete([
            'code' => $m['code'],
          ]);
        }
        utilsPts::deleteDir(PTS_MODULES_DIR . $m['code']);
      }
    }
  }
  protected static function _uninstallTables($module)
  {
    if (is_dir(PTS_MODULES_DIR . $module['code'] . DS . 'tables')) {
      $tableFiles = utilsPts::getFilesList(PTS_MODULES_DIR . $module['code'] . DS . 'tables');
      if (!empty($tableNames)) {
        foreach ($tableFiles as $file) {
          $tableName = str_replace('.php', '', $file);
          if (framePts::_()->getTable($tableName)) {
            framePts::_()->getTable($tableName)->uninstall();
          }
        }
      }
    }
  }
  public static function _installTables($module, $action = 'install')
  {
    $modDir = empty($module['ex_plug_dir']) ? PTS_MODULES_DIR . $module['code'] . DS : utilsPts::getPluginDir($module['ex_plug_dir']) . $module['code'] . DS;
    if (is_dir($modDir . 'tables')) {
      $tableFiles = utilsPts::getFilesList($modDir . 'tables');
      if (!empty($tableFiles)) {
        framePts::_()->extractTables($modDir . 'tables' . DS);
        foreach ($tableFiles as $file) {
          $tableName = str_replace('.php', '', $file);
          if (framePts::_()->getTable($tableName)) {
            framePts::_()->getTable($tableName)->$action();
          }
        }
      }
    }
  }
}
