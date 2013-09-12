<?php

// AUTO-GENERATED FILE -- Civix may overwrite any changes made to this file

/**
 * (Delegated) Implementation of hook_civicrm_config
 */
function _parseaddressnl_civix_civicrm_config(&$config = NULL) {
  static $configured = FALSE;
  if ($configured) return;
  $configured = TRUE;

  $template =& CRM_Core_Smarty::singleton();

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'templates';

  if ( is_array( $template->template_dir ) ) {
      array_unshift( $template->template_dir, $extDir );
  } else {
      $template->template_dir = array( $extDir, $template->template_dir );
  }

  $include_path = $extRoot . PATH_SEPARATOR . get_include_path( );
  set_include_path( $include_path );
}

/**
 * (Delegated) Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function _parseaddressnl_civix_civicrm_xmlMenu(&$files) {
  foreach (_parseaddressnl_civix_glob(__DIR__ . '/xml/Menu/*.xml') as $file) {
    $files[] = $file;
  }
}

/**
 * Implementation of hook_civicrm_install
 */
function _parseaddressnl_civix_civicrm_install() {
  _parseaddressnl_civix_civicrm_config();
  if ($upgrader = _parseaddressnl_civix_upgrader()) {
    return $upgrader->onInstall();
  }
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function _parseaddressnl_civix_civicrm_uninstall() {
  _parseaddressnl_civix_civicrm_config();
  if ($upgrader = _parseaddressnl_civix_upgrader()) {
    return $upgrader->onUninstall();
  }
}

/**
 * (Delegated) Implementation of hook_civicrm_enable
 */
function _parseaddressnl_civix_civicrm_enable() {
  _parseaddressnl_civix_civicrm_config();
  if ($upgrader = _parseaddressnl_civix_upgrader()) {
    if (is_callable(array($upgrader, 'onEnable'))) {
      return $upgrader->onEnable();
    }
  }
}

/**
 * (Delegated) Implementation of hook_civicrm_disable
 */
function _parseaddressnl_civix_civicrm_disable() {
  _parseaddressnl_civix_civicrm_config();
  if ($upgrader = _parseaddressnl_civix_upgrader()) {
    if (is_callable(array($upgrader, 'onDisable'))) {
      return $upgrader->onDisable();
    }
  }
}

/**
 * (Delegated) Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function _parseaddressnl_civix_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  if ($upgrader = _parseaddressnl_civix_upgrader()) {
    return $upgrader->onUpgrade($op, $queue);
  }
}

function _parseaddressnl_civix_upgrader() {
  if (!file_exists(__DIR__.'/CRM/Parseaddressnl/Upgrader.php')) {
    return NULL;
  } else {
    return CRM_Parseaddressnl_Upgrader_Base::instance();
  }
}

/**
 * Search directory tree for files which match a glob pattern
 *
 * Note: Dot-directories (like "..", ".git", or ".svn") will be ignored.
 * Note: In Civi 4.3+, delegate to CRM_Utils_File::findFiles()
 *
 * @param $dir string, base dir
 * @param $pattern string, glob pattern, eg "*.txt"
 * @return array(string)
 */
function _parseaddressnl_civix_find_files($dir, $pattern) {
  if (is_callable(array('CRM_Utils_File', 'findFiles'))) {
    return CRM_Utils_File::findFiles($dir, $pattern);
  }

  $todos = array($dir);
  $result = array();
  while (!empty($todos)) {
    $subdir = array_shift($todos);
    foreach (_parseaddressnl_civix_glob("$subdir/$pattern") as $match) {
      if (!is_dir($match)) {
        $result[] = $match;
      }
    }
    if ($dh = opendir($subdir)) {
      while (FALSE !== ($entry = readdir($dh))) {
        $path = $subdir . DIRECTORY_SEPARATOR . $entry;
        if ($entry{0} == '.') {
        } elseif (is_dir($path)) {
          $todos[] = $path;
        }
      }
      closedir($dh);
    }
  }
  return $result;
}
/**
 * (Delegated) Implementation of hook_civicrm_managed
 *
 * Find any *.mgd.php files, merge their content, and return.
 */
function _parseaddressnl_civix_civicrm_managed(&$entities) {
  $mgdFiles = _parseaddressnl_civix_find_files(__DIR__, '*.mgd.php');
  foreach ($mgdFiles as $file) {
    $es = include $file;
    foreach ($es as $e) {
      if (empty($e['module'])) {
        $e['module'] = 'org.civicoop.parseaddressnl';
      }
      $entities[] = $e;
    }
  }
}

/**
 * Glob wrapper which is guaranteed to return an array.
 *
 * The documentation for glob() says, "On some systems it is impossible to
 * distinguish between empty match and an error." Anecdotally, the return
 * result for an empty match is sometimes array() and sometimes FALSE.
 * This wrapper provides consistency.
 *
 * @see http://php.net/glob
 * @param string $pattern
 * @return array, possibly empty
 */
function _parseaddressnl_civix_glob($pattern) {
  $result = glob($pattern);
  return is_array($result) ? $result : array();
}

/**
 * Inserts a navigation menu item at a given place in the hierarchy
 *
 * $menu - menu hierarchy
 * $path - path where insertion should happen (ie. Administer/System Settings)
 * $item - menu you need to insert (parent/child attributes will be filled for you)
 * $parentId - used internally to recurse in the menu structure
 */
function _parseaddressnl_civix_insert_navigation_menu(&$menu, $path, $item, $parentId = NULL) {
  static $navId;

  // If we are done going down the path, insert menu
  if (empty($path)) {
    if (!$navId) $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
    $navId ++;
    $menu[$navId] = array (
      'attributes' => array_merge($item, array(
        'label'      => CRM_Utils_Array::value('name', $item),
        'active'     => 1,
        'parentID'   => $parentId,
        'navID'      => $navId,
      ))
    );
    return true;
  } else {
    // Find an recurse into the next level down
    $found = false;
    $path = explode('/', $path);
    $first = array_shift($path);
    foreach ($menu as $key => &$entry) {
      if ($entry['attributes']['name'] == $first) {
        if (!$entry['child']) $entry['child'] = array();
        $found = _parseaddressnl_civix_insert_navigation_menu($entry['child'], implode('/', $path), $item, $key);
      }
    }
    return $found;
  }
}

/**
  * Function to glue street_address in NL_nl format from components
  * (street_name, street_number, street_unit)
  * @author Erik Hommel (erik.hommel@civicoop.org)
  * @params params array
  * @return result array
  */
function _parseaddressnl_civix_gluestreetaddressnl( $params ) {
  
  $result = array( );
  /*
   * error if no street_name in array
   */
  if ( !isset( $params['street_name'] ) ) {
      $result['is_error'] = 1;
      $result['error_message'] = "Glueing of street address requires street_name in params";
      return $result;
  }
  $parsedStreetAddressNl = trim( $params['street_name'] );
  if ( isset( $params['street_number'] ) && !empty( $params['street_number'] ) ) {
      $parsedStreetAddressNl .= " ".$params['street_number'];
  }
  if ( isset( $params['street_unit'] ) && !empty( $params['street_unit'] ) ) {
      $parsedStreetAddressNl .= " ".$params['street_unit'];
  }
  $result['is_error'] = 0;
  $result['parsed_street_address'] = $parsedStreetAddressNl;
  return $result;
}

/**
 * Static function to split street_address in components street_name,
 * street_number and street_unit
 * @author Erik Hommel (erik.hommel@civicoop.org)
 * @params street_address
 * @return $result array
 */
function _parseaddressnl_civix_splitStreetAddressNl ( $streetAddress ) {
    $result = array( );
    $result['is_error'] = 0;
    $result['street_name'] = null;
    $result['street_number'] = null;
    $result['street_unit'] = null;
    /*
     * empty array in return if streetAddress is empty
     */
    if ( empty( $streetAddress ) ) {
        return $result;
    }
    $foundNumber = false;
    $parts = explode( " ", $streetAddress );
    $splitFields = array ( );
    $splitFields['street_name'] = null;
    $splitFields['street_number'] = null;
    $splitFields['street_unit'] = null;
    /*
     * check all parts
     */
    foreach ( $parts as $key => $part ) {
        /*
         * if part is numeric
         */
        if ( is_numeric( $part ) ) {
            /*
             * if key = 0, add to street_name
             */
            if ( $key == 0 ) {
                $splitFields['street_name'] .= $part;
            } else {
                /*
                 * else add to street_number if not found, else add to unit
                 */
                if ( $foundNumber == false ) {
                    $splitFields['street_number'] .= $part;
                    $foundNumber = true;
                } else {
                    $splitFields['street_unit'] .= " ".$part;
                }
            }
        } else {
            /*
             * if not numeric and no foundNumber, add to street_name
             */
            if ( $foundNumber == false ) {
                /*
                 * if part is first part, set to street_name
                 */
                if ( $key == 0 ) {
                    $splitFields['street_name'] .= " ".$part;
                } else {
                    /*
                     * if part has numbers first and non-numbers later put number
                     * into street_number and rest in unit and set foundNumber = true
                     */
                    $length = strlen( $part );
                    if ( is_numeric( substr( $part, 0, 1 ) ) ) {
                        for ( $i=0; $i<$length; $i++ ) {
                            if ( is_numeric( substr( $part, $i, 1 ) ) ) {
                                $splitFields['street_number'] .= substr( $part, $i, 1 );
                                $foundNumber = true;
                            } else {
                                $splitFields['street_unit'] .= substr( $part, $i, 1 );
                            }
                        }
                    } else {
                        $splitFields['street_name'] .= " ".$part;
                    }
                }
            } else {
                $splitFields['street_unit'] .= " ".$part;
            }
        }
    }
    $result['street_name'] = trim( $splitFields['street_name'] );
    $result['street_number'] = $splitFields['street_number'];
    $result['street_unit'] = $splitFields['street_unit'];
    
    return $result;
}
