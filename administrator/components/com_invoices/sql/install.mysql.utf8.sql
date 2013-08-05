CREATE TABLE IF NOT EXISTS `#__invoices` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`created_by` INT(11)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`date_created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
`currency` VARCHAR(3)  NOT NULL ,
`exchange_rate` VARCHAR(1)  NOT NULL ,
`invoice_type` VARCHAR(1)  NOT NULL ,
`journal_memo` TEXT NOT NULL ,
`total_net` VARCHAR(10)  NOT NULL ,
`vat` VARCHAR(9)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`property_id` VARCHAR(255)  NOT NULL ,
`due_date` DATE NOT NULL ,
`salutation` VARCHAR(5)  NOT NULL ,
`first_name` VARCHAR(25)  NOT NULL ,
`surname` VARCHAR(25)  NOT NULL ,
`address` VARCHAR(255)  NOT NULL ,
`town` VARCHAR(255)  NOT NULL ,
`county` VARCHAR(255)  NOT NULL ,
`postcode` VARCHAR(8)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

