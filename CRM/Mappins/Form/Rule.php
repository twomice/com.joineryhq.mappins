<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2017
 */

/**
 * This class generates form components for Mappins Rules.
 */
class CRM_Mappins_Form_Rule extends CRM_Admin_Form {

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
    $this->setPageTitle(ts('Map Pins Rule'));

    $this->assign('id', $this->_id);

    $this->applyFilter('__ALL__', 'trim');

    $this->add('select', 'criteria', ts('Criteria'),
      CRM_Mappins_BAO_MappinsRule::getCriteriaOptions()
    );
    
    $this->add('text', 'value', ts('Value'),
      CRM_Core_DAO::getAttribute('CRM_Mappins_DAO_MappinsRule', 'value')
    );

    $this->add('hidden', 'image_url', ts('Image'),
      CRM_Core_DAO::getAttribute('CRM_Mappins_DAO_MappinsRule', 'image_url')
    );

    $isActive = &$this->add('checkbox', 'is_active', ts('Enabled?'));

    if ($this->_action & CRM_Core_Action::VIEW) {
      $this->freeze();
    }

    $this->assign('mappins_rule_id', $this->_id);
    
    // Assign image_url from defaultValues; this is required because the image_url
    // field is hidden, thus its value isn't available to the smarty template;
    // and we want it so we can display the image.
    $defaults = $this->setDefaultValues();
    $this->assign('image_url', $defaults['image_url']);

    CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.mappins', 'js/CRM/Mappins/Form/Rule.js');
    CRM_Core_Resources::singleton()->addStyleFile('com.joineryhq.mappins', 'css/CRM/Mappins/common.css');
  }

  /**
   * @return array
   */
  public function setDefaultValues() {
    static $defaults;
    if (!isset($defaults)) {
      if ($this->_action != CRM_Core_Action::DELETE &&
        isset($this->_id)
      ) {
        $defaults = $params = array();
        $params = array('id' => $this->_id);
        $baoName = $this->_BAOName;
        $baoName::retrieve($params, $defaults);
      }
      else {
        $defaults = parent::setDefaultValues();
      }
    }
    return $defaults;
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      $result = civicrm_api3('MappinsRule', 'delete', array(
        'id' => $this->_id,
      ));
      CRM_Core_Session::setStatus(ts('Selected Map Pins Rule has been deleted.'), ts('Record Deleted'), 'success');
    }
    else {

      // store the submitted values in an array
      $params = $this->exportValues();
      $params['is_active'] = CRM_Utils_Array::value('is_active', $params, FALSE);

      if ($this->_action & CRM_Core_Action::UPDATE) {
        $params['id'] = $this->_id;
      }

      $result = civicrm_api3('MappinsRule', 'create', $params);

      CRM_Core_Session::setStatus(ts('The Map Pins Rule has been saved.'), ts('Saved'), 'success');
    }
  }

}
