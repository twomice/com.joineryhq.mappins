<?php

//TODO: make the per-profile stuff work like this:
// * 1. Weight is per-profile-per-rule
// * 2. a rule can apply to more than one profile, and have a different weight per profile
// * 3. "all profiles" = "profile 0"
// * 4. The UI includes two lists:
// *    rules-per-profile: shows only rules for a given profile; allows drag-and-drop weight settings
// *    all rules: shows all rules; includes a column for "profiles" (clickable to rules-per-profile list); doesn't allow drag-and-drop weight settings.
// * 5. new table: mappins_rule_profile(id, rule_id, uf_group_id, weight)
// * 6. remove `weight` and `uf_group_id` columns from `mappins` table.
// *
// * NOTE: ignore back-compatibility; this is alpha, so breaking changes without smooth upgrade path are fine.

require_once 'mappins.civix.php';

/**
 * Implements hook_civicrm_buildForm().
 */
function mappins_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contact_Form_Task_Map') {
    // Determine profile gid.
    parse_str(html_entity_decode($form->controller->_entryURL), $entryURL);
    $gid = CRM_Utils_Array::value('gid', $entryURL);

    // Instantiate a map object and replace the pins, being sure to convey the
    // Profile gid so that only rules for this profile are applied.
    $map = new CRM_Mappins_MappinsMap();
    $map->setGid($gid);
    $rules = $map->getRules();
    $map->replaceLocationPins();
  }
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
 */
// function mappins_civicrm_preProcess($formName, &$form) {

// } // */

/**
 * Implements hook_civicrm_entityTypes().
 */
function mappins_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Mappins_DAO_MapppinsRule'] = array(
    'name' => 'MappinsRule',
    'class' => 'CRM_Mappins_DAO_MappinsRule',
    'table' => 'civicrm_mappins_rule',
  );
  $entityTypes['CRM_Mappins_DAO_MapppinsRuleProfile'] = array(
    'name' => 'MappinsRuleProfile',
    'class' => 'CRM_Mappins_DAO_MappinsRuleProfile',
    'table' => 'civicrm_mappins_rule_profile',
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
    'url' => 'civicrm/a/#/mappins/rules',
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
