<?php

/**
 * A map, on which we'll set the map images based on our rules.
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

  /**
   * Constructor.
   *
   * @param Object $tpl The Smarty template object.
   */
  public function __construct($tpl = NULL) {
    if (!isset($tpl)) {
      $tpl = CRM_Core_Smarty::singleton();
    }
    $this->tpl = $tpl;
  }

  /**
   * Set the $gid property.
   */
  public function setGid($gid) {
    $this->gid = $gid;
  }

  /**
   * Retrieve all the rules that should apply on this map. If they've not
   * been compiled already, this method will compile them once.
   *
   * @return Array An array of rules, in order of priority.
   */
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

  /**
   * Perform the replacement of map pin images for each location on the map,
   * based on the relevant MappinRules. This method actually changes the
   * src of the pin images within the Smarty template variables.
   */
  public function replaceLocationPins() {
    foreach ($this->tpl->_tpl_vars['locations'] as &$location) {
      $this->setLocationImage($location);
    }
  }

  /**
   * For a given location, set the URL of the image to be used for the map pin.
   *
   * @param array $location A single location, as defined in the Smarty template
   *   variable 'locations'.
   */
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

  /**
   * Check whether the given location is affected by the given rule.
   *
   * @param array $location A single location, as defined in the Smarty template
   *   variable 'locations'.
   * @param array $rule A rule, as returned as an array member from $this->getRules().
   *
   * @return bool
   */
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
        $tags = CRM_Contact_BAO_Contact::buildOptions('tag');
        if (!array_key_exists($rule['value'], $tags)) {
          return FALSE;
        }

        $entity = 'EntityTag';
        $api_params = array(
          'entity_id' => $contact_id,
          'entity_table' => "civicrm_contact",
          'tag_id' => $rule['value'],
        );
        break;

      case 'contact_sub_type':
        $subtypes = CRM_Contact_BAO_Contact::buildOptions('contact_sub_type', 'create');
        if (!in_array($rule['value'], $subtypes)) {
          return FALSE;
        }
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
