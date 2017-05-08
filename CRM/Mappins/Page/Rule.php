<?php

class CRM_Mappins_Page_Rule extends CRM_Core_Page_Basic {

  public $useLivePageJS = TRUE;

  /**
   * The action links that we need to display for the browse screen.
   *
   * @var array
   */
  static $_links = NULL;

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Map Pins: Rules'));

    parent::run();
  }

  public function browse() {
    CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.mappins', 'js/CRM/Mappins/Page/Rule.js');
    CRM_Core_Resources::singleton()->addStyleFile('com.joineryhq.mappins', 'css/CRM/Mappins/Page/Rule.css');
    CRM_Core_Resources::singleton()->addStyleFile('com.joineryhq.mappins', 'css/CRM/Mappins/common.css');

    $this->assign('criteriaOptions', CRM_Mappins_BAO_MappinsRule::getCriteriaOptions());
    // parent::run 2nd argument is $sort, so sort by weight.
    parent::browse(NULL, 'weight');
  }

  /**
   * Get BAO Name.
   *
   * @return string
   *   Classname of BAO.
   */
  public function getBAOName() {
    return 'CRM_Mappins_BAO_MappinsRule';
  }

  /**
   * Get action Links.
   *
   * @return array
   *   (reference) of action links
   */
  public function &links() {
    if (!(self::$_links)) {
      self::$_links = array(
        CRM_Core_Action::VIEW => array(
          'name' => ts('View'),
          'url' => 'civicrm/admin/mappins/rule',
          'qs' => 'action=view&id=%%id%%&reset=1',
          'title' => ts('View Map Pin Rule'),
        ),
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Edit'),
          'url' => 'civicrm/admin/mappins/rule',
          'qs' => 'action=update&id=%%id%%&reset=1',
          'title' => ts('Edit Map Pin Rule'),
        ),
        CRM_Core_Action::DISABLE => array(
          'name' => ts('Disable'),
          'ref' => 'crm-enable-disable',
          'title' => ts('Disable Map Pin Rule'),
        ),
        CRM_Core_Action::ENABLE => array(
          'name' => ts('Enable'),
          'ref' => 'crm-enable-disable',
          'title' => ts('Enable Map Pin Rule'),
        ),
        CRM_Core_Action::DELETE => array(
          'name' => ts('Delete'),
          'url' => 'civicrm/admin/mappins/rule',
          'qs' => 'action=delete&id=%%id%%',
          'title' => ts('Delete Map Pin Rule'),
        ),
      );
    }
    return self::$_links;
  }

  /**
   * Get name of edit form.
   *
   * @return string
   *   Classname of edit form.
   */
  public function editForm() {
    return 'CRM_Mappins_Form_Rule';
  }

  /**
   * Get edit form name.
   *
   * @return string
   *   name of this page.
   */
  public function editName() {
    return 'Map Pins Rules';
  }

  /**
   * Get user context.
   *
   * @param null $mode
   *
   * @return string
   *   user context.
   */
  public function userContext($mode = NULL) {
    return 'civicrm/admin/mappins/rule';
  }

}
