<?php

/**
 * MappinsRule.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_mappins_rule_create_spec(&$spec) {
  // $spec['some_parameter']['api.required'] = 1;
}

/**
 * MappinsRule.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mappins_rule_create($params) {
  $ret = _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);

  // Handle MappinsRuleProfile entities appropriately.
  _civicrm_api3_mappins_rule_create_mappins_rule_profile($params, $ret);
  return $ret;
}

function _civicrm_api3_mappins_rule_create_mappins_rule_profile($params, $rule) {
  if (!empty($rule['id']) && array_key_exists('uf_group_id', $params) && !empty($params['uf_group_id'])) {
    if (is_string($params['uf_group_id'])) {
      $uf_group_ids = json_decode($params['uf_group_id']);
    }
    else {
      $uf_group_ids = $params['uf_group_id'];
    }

    if (is_array($uf_group_ids)) {
      $rule_profile_result = civicrm_api3('MappinsRuleProfile', 'get', array(
        'rule_id' => $rule['id'],
        'return' => array("id", "uf_group_id", "weight"),
        'options' => array('sort' => "weight"),
      ));

      // Compile lists of entities to create.
      $to_create = $uf_group_ids;
      foreach ($rule_profile_result['values'] as $rule_profile_value) {
        if (in_array($rule_profile_value['uf_group_id'], $uf_group_ids)) {
          // Existing entity IS in uf_group_ids.
          // No need create this one, it already exists and should. Just remove it
          // from the list of entities to create.
          $existing_uf_group_id = $rule_profile_value['uf_group_id'];
          if (($index = array_search($existing_uf_group_id, $to_create)) !== false) {
            unset($to_create[$index]);
          }
        }
        elseif ((
          // Handle this odd case: If $rule_profile_value['uf_group_id'] is null
          // or not set, it represents an "all profiles" rule; also, the presence
          // of "0" in $uf_group_ids represents that it should be saved as
          // an "all profiles" rule, so we can leave things alone if those two
          // things are true.
          empty($rule_profile_value['uf_group_id']) && in_array(0, $uf_group_ids)
          )
        ) {
          $existing_uf_group_id = 0;
          if (($index = array_search($existing_uf_group_id, $to_create)) !== false) {
            unset($to_create[$index]);
          }
        }
        else {
          // Existing entity is not in uf_group_ids, so delete it
          civicrm_api3('MappinsRuleProfile', 'delete', array(
            'id' => $rule_profile_value['id'],
          ));
        }
      }
      foreach ($to_create as $uf_group_id) {
        civicrm_api3('MappinsRuleProfile', 'create', array(
          'uf_group_id' => ($uf_group_id > 0 ? $uf_group_id : NULL),
          'weight' => -1,
          'rule_id' => $rule['id'],
        ));
      }

      // Correct new weights by setting them all to the entity ID.  This works
      // because all new MappinsRuleProfile entities should have the highest
      // weight.
      $weighting_ruleprofile_result = civicrm_api3('MappinsRuleProfile', 'get', array(
        'weight' => -1,
        'return' => array("id"),
      ));
      foreach ($weighting_ruleprofile_result['values'] as $weighting_ruleprofile_value) {
        civicrm_api3('MappinsRuleProfile', 'create', array(
          'id' => $weighting_ruleprofile_value['id'],
          'weight' => $weighting_ruleprofile_value['id'],
        ));
      }
    }
  }
}

/**
 * MappinsRule.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mappins_rule_delete($params) {
  return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * MappinsRule.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mappins_rule_get($params) {
  $options = _civicrm_api3_get_options_from_params($params);
  if (empty($options['return']) || array_key_exists('uf_group_id', $options['return'])) {
    return _civicrm_api3_mappins_rule_get_with_uf_group_id($params);
  }
  else {
    return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
  }
}

/**
 * Get DAO name
 */
function _civicrm_api3_mappins_rule_DAO() {
  return 'CRM_Mappins_DAO_MappinsRule';
}

function _civicrm_api3_mappins_rule_get_with_uf_group_id($params) {
  $sql = CRM_Utils_SQL_Select::fragment();
  $sql
    ->join('civicrm_mappins_rule_profile', 'LEFT JOIN civicrm_mappins_rule_profile rp ON rp.rule_id = a.id')
    ->groupBy('a.id')
  ;
  $rule_dao_name = CRM_Core_DAO_AllCoreTables::getClassForTable('civicrm_mappins_rule');
  $result = _civicrm_api3_mappins_rule_basic_get($rule_dao_name, $params, TRUE, "", $sql, FALSE);

  // json-encode the uf_group_id value.
  foreach ($result['values'] as &$value) {
    if (empty($value['uf_group_id'])) {
      $value['uf_group_id'] = array();
    }
    else {
      $value['uf_group_id'] = explode(',', $value['uf_group_id']);
    }
  }
  return $result;
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
function _civicrm_api3_mappins_rule_basic_get($bao_name, $params, $returnAsSuccess = TRUE, $entity = "", $sql = NULL, $uniqueFields = FALSE) {
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
