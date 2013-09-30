CREATE TABLE IF NOT EXISTS `qitz3_tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL DEFAULT '1',
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(125) NOT NULL,
  `description` varchar(512) NOT NULL,
  `severity` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;