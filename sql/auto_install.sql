DROP TABLE IF EXISTS `civicrm_mappins_rule_profile`;
DROP TABLE IF EXISTS `civicrm_mappins_rule`;


-- /*******************************************************
-- *
-- * civicrm_mappins_rule
-- *
-- * Rules for map pins
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mappins_rule` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MappinsRule ID',
     `criteria` varchar(255) NOT NULL   COMMENT 'Type of rule (e.g., group, tag)',
     `value` varchar(255) NOT NULL   COMMENT 'Value to filter on',
     `image_url` varchar(255) NOT NULL   COMMENT 'URL for pin image',
     `is_active` tinyint   DEFAULT 1 COMMENT 'Is this mappins_rule enabled'
,
        PRIMARY KEY (`id`)



)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;


-- /*******************************************************
-- *
-- * civicrm_mappins_rule_profile
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mappins_rule_profile` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MappinsRuleProfile ID',
     `rule_id` int unsigned NOT NULL   COMMENT 'FK to civicrm_mappins_rule.id',
     `uf_group_id` int unsigned NOT NULL   COMMENT 'Rule applies only to these profiles. Implicit FK to civicrm_uf_group.id',
     `weight` int   DEFAULT 1 COMMENT 'Relative order of this mappins_rule; lowest weights sort first.'
,
        PRIMARY KEY (`id`)

    ,     INDEX `index_rule_id`(
        rule_id
  )
  ,     INDEX `index_uf_group_id`(
        uf_group_id
  )

,          CONSTRAINT FK_civicrm_mappins_rule_profile_rule_id FOREIGN KEY (`rule_id`) REFERENCES `civicrm_mappins_rule`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_mappins_rule_profile_uf_group_id FOREIGN KEY (`uf_group_id`) REFERENCES `civicrm_uf_group`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;
