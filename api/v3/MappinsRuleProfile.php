<?php

/**
 * MappinsRuleProfile.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_mappins_rule_profile_create_spec(&$spec) {
  // $spec['some_parameter']['api.required'] = 1;
}

/**
 * MappinsRuleProfile.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mappins_rule_profile_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * MappinsRuleProfile.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mappins_rule_profile_delete($params) {
  return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * MappinsRuleProfile.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mappins_rule_profile_get($params) {
  if (empty($params['return'])) {
    return _civicrm_api3_mappins_rule_profile_get_with_rules($params);
  }
  else {
    return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
  }
}

/**
 * Get DAO name
 */
function _civicrm_api3_mappins_rule_profile_DAO() {
  return 'CRM_Mappins_DAO_MappinsRuleProfile';
}

function _civicrm_api3_mappins_rule_profile_get_with_rules($params) {
  $sql = CRM_Utils_SQL_Select::fragment();
  $sql
                                                                                      ->join('civicrm_mappins_rule', 'INNER JOIN civicrm_mappins_rule r ON r.id = a.rule_id')
  ;
  $ruleprofile_dao_name = CRM_Core_DAO_AllCoreTables::getClassForTable('civicrm_mappins_rule_profile');
  return _civicrm_api3_mappins_rule_profile_basic_get($ruleprofile_dao_name, $params, TRUE, "", $sql, FALSE);
}

/**
 * Modified copy of _civicrm_api3_basic_get(). This version uses \Civi\API\Mappins\Api3SelectQuery
 * instead of \Civi\API\Api3SelectQuery, so we can pull in fields from
 * civicrm_mappins_rule, including support for `where` parameters.
 *
 * @param string $bao_name
 *   Name of BAO.
 * @param array $params
 *   Params from api.
 * @param bool $returnAsSuccess
 *   Return in api success format.
 * @param string $entity
 * @param CRM_Utils_SQL_Select|NULL $sql
 *   Extra SQL bits to add to the query. For filtering current events, this might be:
 *   CRM_Utils_SQL_Select::fragment()->where('(start_date >= CURDATE() || end_date >= CURDATE())');
 * @param bool $uniqueFields
 *   Should unique field names be returned (for backward compatibility)
 *
 * @return array
 */
function _civicrm_api3_mappins_rule_profile_basic_get($bao_name, $params, $returnAsSuccess = TRUE, $entity = "", $sql = NULL, $uniqueFields = FALSE) {
  $entity = CRM_Core_DAO_AllCoreTables::getBriefName(str_replace('_BAO_', '_DAO_', $bao_name));
  $options = _civicrm_api3_get_options_from_params($params);

  require_once('Civi/API/Mappins/Api3SelectQuery.php');
  $query = new \Civi\API\Mappins\Api3SelectQuery($entity, CRM_Utils_Array::value('check_permissions', $params, FALSE));
  $query->where = $params;
  if ($options['is_count']) {
    $query->select = array('count_rows');
  }
  else {
    $query->select = array_keys(array_filter($options['return']));
    $query->orderBy = $options['sort'];
    $query->isFillUniqueFields = $uniqueFields;
  }
  $query->limit = $options['limit'];
  $query->offset = $options['offset'];
  $query->merge($sql);
  $result = $query->run();

  if ($returnAsSuccess) {
    return civicrm_api3_create_success($result, $params, $entity, 'get');
  }
  return $result;
}
