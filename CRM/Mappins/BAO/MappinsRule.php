<?php

class CRM_Mappins_BAO_MappinsRule extends CRM_Mappins_DAO_MappinsRule {

  /**
   * Create a new MappinsRule based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Mappins_DAO_MappinsRule|NULL
   */
  public static function create($params) {
    $className = 'CRM_Mappins_DAO_MappinsRule';
    $entityName = 'MappinsRule';

    // Compress uf_group_id to a padded string for storage.
    $params['uf_group_id'] = CRM_Utils_Array::implodePadded(CRM_Utils_Array::value($params, 'uf_group_id'));

    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, $params['id'] ?? NULL, $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  public static function getCriteriaOptions() {
    return array(
      'contact_sub_type' => ts('Contact Sub Type'),
      'group' => ts('Group ID'),
      'tag' => ts('Tag ID'),
    );
  }

  public static function getUFGroupOptions() {
    $uf_group_options = array();
    $result = civicrm_api3('UFGroup', 'get', array(
      'options' => array('limit' => 0),
    ));
    foreach ($result['values'] as $value) {
      $uf_group_options[$value['id']] = $value['title'];
    }
    return $uf_group_options;
  }

  /**
   * Fetch object based on array of properties.
   *
   * @param array $params
   *   (reference ) an assoc array of name/value pairs.
   * @param array $defaults
   *   (reference ) an assoc array to hold the flattened values.
   *
   * @return CRM_Contact_BAO_RelationshipType
   */
  public static function retrieve(&$params, &$defaults) {
    $mappinsRule = new CRM_Mappins_DAO_MappinsRule();
    $mappinsRule->copyValues($params);
    if ($mappinsRule->find(TRUE)) {
      CRM_Core_DAO::storeValues($mappinsRule, $defaults);
      $mappinsRule->free();
      return $mappinsRule;
    }
    return NULL;
  }

}
