<?php

require_once 'mappins.civix.php';

function mappins_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contact_Form_Task_Map') {    
    _mappins_replace_tpl_pins();
  }
}

/**
 * Apply rules to customize display pins in Smarty template variables.
 */
function _mappins_replace_tpl_pins() {
  $tpl = CRM_Core_Smarty::singleton();   
  foreach($tpl->_tpl_vars['locations'] as &$location) {
    _mappins_set_location_image($location);
  }
}

function _mappins_set_location_image(&$location) {
  $rules = _mappins_get_rules();
  foreach ($rules as $rule) {
    if (_mappins_location_matches_rule($location, $rule)) {
      /*
       * Each location is an array with these keys:
       *   contactID (String)
       *   displayName (String)
       *   city (String)
       *   state (String)
       *   postal_code (String)
       *   lat (String)
       *   lng (String)
       *   marker_class (String)
       *   address (String)
       *   displayAddress (String)
       *   url (String)
       *   location_type (String)
       *   image (String)
       * 
       * We can set the pin URL in the 'image' key.
       */
      $location['image'] = $rule['image_url'];
      break;
    }
  }
}

function _mappins_get_rules() {
  static $rules;
  if(!isset($rules)) {
    $result = civicrm_api3('MappinsRule', 'get', array(
      'is_active' => 1,
      'options' => array(
        'limit' => 10000, 
        'sort' => "weight"
      ),
    ));
    $rules = $result['values'];
  }
  return $rules;
}

function _mappins_location_matches_rule($location, $rule) {
  $contact_id = $location['contactID'];
  $entity = '';
  $api_params = array();
  switch ($rule['criteria']) {
    case 'group':
      // Use the Contact api to check group membership, so that we're also
      // including smart group membership.
      // References:
      //  https://issues.civicrm.org/jira/browse/CRM-20144 (note "funky syntax" comment from Coleman)
      //  https://issues.civicrm.org/jira/browse/CRM-11903
      //  https://issues.civicrm.org/jira/browse/CRM-9021
      $entity = 'Contact';
      $api_params = array(
        'status' => "Added",
        'contact_id' => $contact_id,
        'group' => array($rule['value'] => 1),
      );
      break;
    case 'tag':
      $entity = 'EntityTag';
      $api_params = array(
        'entity_id' => $contact_id,
        'entity_table' => "civicrm_contact",
        'tag_id' => $rule['value'],
      );
      break;
    case 'contact_sub_type':
      $entity = 'Contact';
      $api_params = array(
        'status' => "Added",
        'id' => $contact_id,
        'contact_sub_type' => $rule['value'],
      );
      break;
    default:
      return FALSE;
      break;
  }
  $result = civicrm_api3($entity, 'get', $api_params);  
  return (CRM_Utils_Array::value('count', $result, 0) == 1);
  
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mappins_civicrm_config(&$config) {
  _mappins_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mappins_civicrm_xmlMenu(&$files) {
  _mappins_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mappins_civicrm_install() {
  _mappins_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function mappins_civicrm_postInstall() {
  _mappins_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mappins_civicrm_uninstall() {
  _mappins_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mappins_civicrm_enable() {
  _mappins_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mappins_civicrm_disable() {
  _mappins_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mappins_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mappins_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mappins_civicrm_managed(&$entities) {
  _mappins_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mappins_civicrm_caseTypes(&$caseTypes) {
  _mappins_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mappins_civicrm_angularModules(&$angularModules) {
  _mappins_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mappins_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mappins_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function mappins_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function mappins_civicrm_navigationMenu(&$menu) {
  _mappins_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.joineryhq.mappins')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _mappins_civix_navigationMenu($menu);
} // */


/**
 * Implements hook_civicrm_entityTypes().
 */
function mappins_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Mappins_DAO_MapppinsRule'] = array(
    'name' => 'MappinsRule',
    'class' => 'CRM_Mappins_DAO_MappinsRule',
    'table' => 'civicrm_mappins_rule',
  );
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function mappins_civicrm_navigationMenu(&$menu) {
  _mappins_get_max_navID($menu, $max_navID);
  _mappins_civix_insert_navigation_menu($menu, 'Administer/Customize Data and Screens', array(
    'label' => ts('Map Pins', array('domain' => 'com.joineryhq.mappins')),
    'name' => 'Map Pins',
    'url' => 'civicrm/admin/mappins/rule?reset=1',
    'permission' => 'administer CiviCRM',
    'operator' => 'AND',
    'separator' => NULL,
    'navID' => ++$max_navID,
  ));
  _mappins_civix_navigationMenu($menu);
}


/**
 * For an array of menu items, recursively get the value of the greatest navID
 * attribute.
 * @param <type> $menu
 * @param <type> $max_navID
 */
function _mappins_get_max_navID(&$menu, &$max_navID = NULL) {
  foreach ($menu as $id => $item) {
    if (!empty($item['attributes']['navID'])) {
      $max_navID = max($max_navID, $item['attributes']['navID']);
    }
    if (!empty($item['child'])) {
      _mappins_get_max_navID($item['child'], $max_navID);
    }
  }
}