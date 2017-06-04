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
        if (!in_array($rule_profile_value['uf_group_id'], $uf_group_ids)) {
          // Existing entity is not in uf_group_ids, so delete it
          civicrm_api3('MappinsRuleProfile', 'delete', array(
            'id' => $rule_profile_value['id'],
          ));
        }
        else {
          // Existing entity IS in uf_group_ids, so no need create it. Remove it
          // from the list of entities to create.
          $existing_uf_group_id = $rule_profile_value['uf_group_id'];
          if(($index = array_search($existing_uf_group_id, $to_create)) !== false) {
            unset($to_create[$index]);
          }          
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
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Get DAO name
 */
function _civicrm_api3_mappins_rule_DAO() {
  return 'CRM_Mappins_DAO_MappinsRule';
}
