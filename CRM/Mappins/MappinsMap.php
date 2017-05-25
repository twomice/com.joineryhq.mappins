<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * A map, on which we'll set the map images.
 */
class CRM_Mappins_MappinsMap {
  /**
   * The Smarty template object that powers this map page.
   *
   * @var Object
   */
  protected $tpl;

  /**
   * The CiviCRM Profile gid (civicrm_uf_group.id) for the profile that
   * powers this map.
   *
   * @var Int
   */
  protected $gid;

  /**
   * The MappinsRules that should apply on this map.
   *
   * @var Array
   */
  protected $rules;

  public function __construct($tpl = NULL) {
    if (!isset($tpl)) {
      $tpl = CRM_Core_Smarty::singleton();
    }
    $this->tpl = $tpl;
  }

  public function setGid($gid) {
    $this->gid = $gid;
  }

  public function getRules() {
    if (!isset($this->rules)) {
      $this->rules = array();
      $params = array(
        'is_active' => 1,
        'options' => array(
          'limit' => 0,
          'sort' => "weight",
        ),
      );
      if ($this->gid) {
        $params['uf_group_id'] = array(
          'LIKE' => '%' . CRM_Utils_Array::implodePadded(array($this->gid)) . '%',
        );
        $result = civicrm_api3('MappinsRule', 'get', $params);
        $this->rules = $result['values'];

        $params['uf_group_id'] = array(
          'IS NULL' => 1,
        );
        $result = civicrm_api3('MappinsRule', 'get', $params);
        $this->rules = array_merge($this->rules, $result['values']);
      }
      else {
        $result = civicrm_api3('MappinsRule', 'get', $params);
        $this->rules = $result['values'];
      }
    }
    return $this->rules;
  }

  public function replaceLocationPins() {
    foreach ($this->tpl->_tpl_vars['locations'] as &$location) {
      $this->setLocationImage($location);
    }
  }

  protected function setLocationImage(&$location) {
    foreach ($this->getRules() as $rule) {
      if (static::doesLocationMatchRule($location, $rule)) {
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

  protected static function doesLocationMatchRule($location, $rule) {
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
    }
    $result = civicrm_api3($entity, 'get', $api_params);
    $is_match = (CRM_Utils_Array::value('count', $result, 0) == 1);
    return $is_match;
  }

}
