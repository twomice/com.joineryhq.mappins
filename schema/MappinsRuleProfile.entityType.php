<?php
use CRM_Mappins_ExtensionUtil as E;

return [
  'name' => 'MappinsRuleProfile',
  'table' => 'civicrm_mappins_rule_profile',
  'class' => 'CRM_Mappins_DAO_MappinsRuleProfile',
  'getInfo' => fn() => [
    'title' => E::ts('Mappins Rule Profile'),
    'title_plural' => E::ts('Mappins Rule Profiles'),
    'description' => E::ts('FIXME'),
    'log' => TRUE,
    'add' => '4.7',
  ],
  'getIndices' => fn() => [
    'index_rule_id' => [
      'fields' => [
        'rule_id' => TRUE,
      ],
    ],
    'index_uf_group_id' => [
      'fields' => [
        'uf_group_id' => TRUE,
      ],
    ],
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('Unique MappinsRuleProfile ID'),
      'add' => '4.7',
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'rule_id' => [
      'title' => E::ts('Rule'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'required' => TRUE,
      'description' => E::ts('FK to civicrm_mappins_rule.id'),
      'add' => '4.7',
      'entity_reference' => [
        'entity' => 'MappinsRule',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'uf_group_id' => [
      'title' => E::ts('Profile'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'description' => E::ts('Rule applies only to these profiles. Implicit FK to civicrm_uf_group.id'),
      'add' => '4.7',
      'pseudoconstant' => [
        'callback' => 'CRM_Mappins_BAO_MappinsRule::getUFGroupOptions',
      ],
      'entity_reference' => [
        'entity' => 'UFGroup',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'weight' => [
      'title' => E::ts('Order'),
      'sql_type' => 'int',
      'input_type' => 'Number',
      'description' => E::ts('Relative order of this mappins_rule; lowest weights sort first.'),
      'add' => '4.7',
      'default' => 1,
    ],
  ],
];
