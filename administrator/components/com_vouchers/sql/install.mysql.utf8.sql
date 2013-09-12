CREATE TABLE IF NOT EXISTS `#__vouchers` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`created_by` INT(11)  NOT NULL,
`date_created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
`number` VARCHAR(10)  NOT NULL,
`item_cost_id` INT(11) NOT NULL,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`property_id` VARCHAR(255)  NOT NULL ,
`end_date` DATE NOT NULL,

PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

