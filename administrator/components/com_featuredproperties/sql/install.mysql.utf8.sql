CREATE TABLE IF NOT EXISTS `#__featured_property_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(25) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__featured_properties` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`property_id` int(11) NOT NULL,
`created_by` INT(11)  NOT NULL,
`date_created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
`state` TINYINT(1)  NOT NULL DEFAULT '0',
`start_date` DATE NOT NULL,
`end_date` DATE NOT NULL,
`notes` varchar(512) NULL DEFAULT '',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

