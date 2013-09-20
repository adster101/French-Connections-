CREATE TABLE IF NOT EXISTS `qitz3_featured_properties` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `featured_property_type` int(11) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `notes` varchar(512) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

