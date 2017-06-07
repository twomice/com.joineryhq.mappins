<?php

class CRM_Mappins_BAO_MappinsRuleProfile extends CRM_Mappins_DAO_MappinsRuleProfile {
  /**
   * Create a new MappinsRuleProfile based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Mappins_DAO_MappinsRuleProfile|NULL
   *
    public static function create($params) {
    $className = 'CRM_Mappins_DAO_MappinsRuleProfile';
    $entityName = 'MappinsRuleProfile';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
    } */
}
