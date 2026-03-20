<?php
use CRM_Mappins_ExtensionUtil as E;

return [
  'name' => 'MappinsRule',
  'table' => 'civicrm_mappins_rule',
  'class' => 'CRM_Mappins_DAO_MappinsRule',
  'getInfo' => fn() => [
    'title' => E::ts('Mappins Rule'),
    'title_plural' => E::ts('Mappins Rules'),
    'description' => E::ts('Rules for map pins'),
    'log' => TRUE,
    'add' => '4.7',
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('Unique MappinsRule ID'),
      'add' => '4.7',
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'criteria' => [
      'title' => E::ts('Criteria'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('Type of rule (e.g., group, tag)'),
      'add' => '4.7',
    ],
    'value' => [
      'title' => E::ts('Value'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('Value to filter on'),
      'add' => '4.7',
    ],
    'image_url' => [
      'title' => E::ts('Image Url'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('URL for pin image'),
      'add' => '4.7',
    ],
    'is_active' => [
      'title' => E::ts('Is Enabled'),
      'sql_type' => 'boolean',
      'input_type' => 'CheckBox',
      'description' => E::ts('Is this mappins_rule enabled'),
      'add' => '4.7',
      'default' => TRUE,
    ],
  ],
];
