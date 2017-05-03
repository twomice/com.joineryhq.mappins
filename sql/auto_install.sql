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
     `criteria` varchar(255)    COMMENT 'Type of rule (e.g., group, tag)',
     `value` varchar(255)    COMMENT 'Value to filter on',
     `image_url` varchar(255)    COMMENT 'URL for pin image',
     `is_active` tinyint   DEFAULT 1 COMMENT 'Is this mappins_rule enabled',
     `weight` int   DEFAULT 1 COMMENT 'Relative order of this mappins_rule; lowest weights sort first.' 
,
        PRIMARY KEY (`id`)
 
 
 
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;
