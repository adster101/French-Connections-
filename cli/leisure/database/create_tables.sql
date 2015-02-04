SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `accos`
-- ----------------------------
DROP TABLE IF EXISTS `accos`;
CREATE TABLE `accos` (
  `code` char(14) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `parkCode` char(14) DEFAULT NULL,
  `maxNumberOfPersons` smallint(6) DEFAULT NULL,
  `numberOfPets` tinyint(4) DEFAULT NULL,
  `numberOfBabies` tinyint(4) DEFAULT NULL,
  `numberOfStars` tinyint(4) DEFAULT NULL,
  `postalcode` varchar(20) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `regionCode` varchar(10) DEFAULT NULL,
  `skiRegionCode` varchar(10) DEFAULT NULL,
  `countryCode` char(2) DEFAULT NULL,
  `creationDate` date DEFAULT NULL,
  `longitude` double(8,5) DEFAULT NULL,
  `latitude` double(8,5) DEFAULT NULL,
  `numberOfEnquetes` smallint(6) DEFAULT NULL,
  `enquetePoints` smallint(6) DEFAULT NULL,
  `numberOfBedRooms` tinyint(4) DEFAULT '0',
  `numberOfBathRooms` tinyint(4) DEFAULT NULL,
  `optionsAllowed` enum('1','0') DEFAULT NULL,
  `dimension` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `huis_plaats` (`city`),
  KEY `huis_aant_hd` (`numberOfPets`),
  KEY `huis_ster` (`numberOfStars`),
  KEY `land_code` (`countryCode`),
  KEY `regi_code` (`regionCode`),
  KEY `huis_tm` (`maxNumberOfPersons`),
  KEY `huis_comp` (`numberOfBathRooms`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of accos
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_brands`
-- ----------------------------
DROP TABLE IF EXISTS `acco_brands`;
CREATE TABLE `acco_brands` (
  `housecode` char(14) NOT NULL DEFAULT '',
  `brand` enum('ar','c4a','jv','bv') NOT NULL,
  `sequenceNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`housecode`,`brand`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_brands
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_city`
-- ----------------------------
DROP TABLE IF EXISTS `acco_city`;
CREATE TABLE `acco_city` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `city` varchar(255) DEFAULT NULL,
  `subcity` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`houseCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_city
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_costs_on_site`
-- ----------------------------
DROP TABLE IF EXISTS `acco_costs_on_site`;
CREATE TABLE `acco_costs_on_site` (
  `houseCode` char(14) DEFAULT NULL,
  `itemNumber` int(11) DEFAULT NULL,
  `language` char(2) DEFAULT NULL,
  `description` text,
  `value` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_costs_on_site
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_descriptions`
-- ----------------------------
DROP TABLE IF EXISTS `acco_descriptions`;
CREATE TABLE `acco_descriptions` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`houseCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_descriptions
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_distances`
-- ----------------------------
DROP TABLE IF EXISTS `acco_distances`;
CREATE TABLE `acco_distances` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `to` varchar(255) NOT NULL DEFAULT '',
  `distanceInKm` double DEFAULT NULL,
  PRIMARY KEY (`houseCode`,`to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_distances
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_guestbooks`
-- ----------------------------
DROP TABLE IF EXISTS `acco_guestbooks`;
CREATE TABLE `acco_guestbooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `houseCode` char(14) DEFAULT NULL,
  `language` char(2) DEFAULT NULL,
  `postDate` date DEFAULT NULL,
  `clientTitle` char(3) DEFAULT NULL,
  `clientInitials` varchar(255) DEFAULT NULL,
  `clientSurname` varchar(255) DEFAULT NULL,
  `clientCountry` char(2) DEFAULT NULL,
  `arrivalDate` date DEFAULT NULL,
  `departureDate` date DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_guestbooks
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_houseowner_tips`
-- ----------------------------
DROP TABLE IF EXISTS `acco_houseowner_tips`;
CREATE TABLE `acco_houseowner_tips` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `language` enum('nl','de','fr','en','es','it','pl') NOT NULL,
  `tip` text,
  PRIMARY KEY (`houseCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of acco_houseowner_tips
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_layout`
-- ----------------------------
DROP TABLE IF EXISTS `acco_layout`;
CREATE TABLE `acco_layout` (
  `housecode` char(14) NOT NULL DEFAULT '',
  `layoutid` int(11) NOT NULL DEFAULT '0',
  `sequencenumber` int(11) NOT NULL DEFAULT '0',
  `numberofitems` int(11) DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `parentsequencenumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`housecode`,`layoutid`,`sequencenumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_layout
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_layout_details`
-- ----------------------------
DROP TABLE IF EXISTS `acco_layout_details`;
CREATE TABLE `acco_layout_details` (
  `housecode` char(14) NOT NULL DEFAULT '',
  `layoutid` int(11) NOT NULL DEFAULT '0',
  `sequencenumber` int(11) NOT NULL DEFAULT '0',
  `numberofitems` int(11) DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `parentsequencenumber` int(11) DEFAULT NULL,
  `detailnumber` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`housecode`,`layoutid`,`sequencenumber`,`detailnumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of acco_layout_details
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_layout_simple`
-- ----------------------------
DROP TABLE IF EXISTS `acco_layout_simple`;
CREATE TABLE `acco_layout_simple` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `layout` text,
  PRIMARY KEY (`houseCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of acco_layout_simple
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_photos`
-- ----------------------------
DROP TABLE IF EXISTS `acco_photos`;
CREATE TABLE `acco_photos` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `sequenceNumber` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tag` char(100) DEFAULT NULL,
  `height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url` text,
  PRIMARY KEY (`houseCode`,`sequenceNumber`,`height`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_photos
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_properties`
-- ----------------------------
DROP TABLE IF EXISTS `acco_properties`;
CREATE TABLE `acco_properties` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `typeNumber` smallint(11) unsigned NOT NULL,
  `typeContents` smallint(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`houseCode`,`typeNumber`,`typeContents`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of acco_properties
-- ----------------------------

-- ----------------------------
-- Table structure for `at_leisure_json_rpc_errors`
-- ----------------------------
DROP TABLE IF EXISTS `at_leisure_json_rpc_errors`;
CREATE TABLE `at_leisure_json_rpc_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call` varchar(255) DEFAULT NULL,
  `errorCode` int(11) DEFAULT NULL,
  `errorText` text,
  `file` text,
  `line` int(11) DEFAULT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `uri` text,
  `exception` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of at_leisure_json_rpc_errors
-- ----------------------------

-- ----------------------------
-- Table structure for `avail_by_night`
-- ----------------------------
DROP TABLE IF EXISTS `avail_by_night`;
CREATE TABLE `avail_by_night` (
  `HouseCode` char(14) NOT NULL DEFAULT '',
  `PlanningNumber` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `BasePriceV1` float(11,2) DEFAULT NULL,
  `BasePricePerNightV1` float(11,2) unsigned DEFAULT NULL,
  `MinimumNightString` text,
  `AvailString` text,
  `PriceString` text,
  `MinPriceString` text,
  `ArrivalPriceString` text,
  `DeparturePriceString` text,
  `testString` text,
  PRIMARY KEY (`HouseCode`,`PlanningNumber`),
  FULLTEXT KEY `Avail` (`AvailString`),
  FULLTEXT KEY `minNight` (`MinimumNightString`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of avail_by_night
-- ----------------------------

-- ----------------------------
-- Table structure for `avail_by_period`
-- ----------------------------
DROP TABLE IF EXISTS `avail_by_period`;
CREATE TABLE `avail_by_period` (
  `houseCode` char(14) NOT NULL DEFAULT '',
  `arrivalDate` date NOT NULL DEFAULT '0000-00-00',
  `nights` smallint(6) NOT NULL DEFAULT '0',
  `periodId` enum('1w','2w','3w','wk','lw','mw') DEFAULT NULL,
  `onRequest` enum('1','0') DEFAULT NULL,
  `priceExclDiscount` smallint(6) DEFAULT NULL,
  `price` smallint(6) DEFAULT NULL,
  `lastMinute` enum('1','0') DEFAULT NULL,
  `arrivalTimeFrom` time DEFAULT NULL,
  `arrivalTimeUntil` time DEFAULT NULL,
  `departureTimeFrom` time DEFAULT NULL,
  `departureTimeUntil` time DEFAULT NULL,
  PRIMARY KEY (`houseCode`,`arrivalDate`,`nights`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of avail_by_period
-- ----------------------------

-- ----------------------------
-- Table structure for `countries`
-- ----------------------------
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `countryCode` char(2) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`countryCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of countries
-- ----------------------------

-- ----------------------------
-- Table structure for `discount_nights`
-- ----------------------------
DROP TABLE IF EXISTS `discount_nights`;
CREATE TABLE `discount_nights` (
  `HouseCode` char(14) NOT NULL DEFAULT '',
  `FromDate` date NOT NULL DEFAULT '0000-00-00',
  `UntilDate` date NOT NULL DEFAULT '0000-00-00',
  `FromNumberOfNights` tinyint(11) NOT NULL DEFAULT '0',
  `UntilNumberOfNights` tinyint(11) NOT NULL DEFAULT '0',
  `DiscountPercentage` tinyint(11) NOT NULL DEFAULT '0',
  `DepartureDay` char(3) NOT NULL DEFAULT '',
  `ArrivalDay` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`HouseCode`,`FromDate`,`UntilDate`,`FromNumberOfNights`,`UntilNumberOfNights`,`DiscountPercentage`,`DepartureDay`,`ArrivalDay`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of discount_nights
-- ----------------------------

-- ----------------------------
-- Table structure for `discount_nights_lm`
-- ----------------------------
DROP TABLE IF EXISTS `discount_nights_lm`;
CREATE TABLE `discount_nights_lm` (
  `HouseCode` char(14) NOT NULL DEFAULT '',
  `NumberOfNightsBeforeArrival` tinyint(11) NOT NULL DEFAULT '0',
  `DiscountPercentage` tinyint(11) DEFAULT NULL,
  PRIMARY KEY (`HouseCode`,`NumberOfNightsBeforeArrival`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of discount_nights_lm
-- ----------------------------

-- ----------------------------
-- Table structure for `list_of_houses`
-- ----------------------------
DROP TABLE IF EXISTS `list_of_houses`;
CREATE TABLE `list_of_houses` (
  `housecode` char(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`housecode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of list_of_houses
-- ----------------------------

-- ----------------------------
-- Table structure for `property_categories`
-- ----------------------------
DROP TABLE IF EXISTS `property_categories`;
CREATE TABLE `property_categories` (
  `typeNumber` smallint(3) unsigned NOT NULL,
  `language` char(2) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`typeNumber`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of property_categories
-- ----------------------------

-- ----------------------------
-- Table structure for `property_properties`
-- ----------------------------
DROP TABLE IF EXISTS `property_properties`;
CREATE TABLE `property_properties` (
  `id` smallint(3) unsigned NOT NULL,
  `typeNumber` smallint(3) unsigned NOT NULL,
  `language` char(2) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`id`,`typeNumber`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of property_properties
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_layout_details`
-- ----------------------------
DROP TABLE IF EXISTS `reference_layout_details`;
CREATE TABLE `reference_layout_details` (
  `id` int(11) NOT NULL DEFAULT '0',
  `lang` enum('nl','en','fr','de','es','it','pl') NOT NULL DEFAULT 'nl',
  `description` text,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of reference_layout_details
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_layout_subtypes`
-- ----------------------------
DROP TABLE IF EXISTS `reference_layout_subtypes`;
CREATE TABLE `reference_layout_subtypes` (
  `typeid` int(11) NOT NULL,
  `id` int(11) NOT NULL DEFAULT '0',
  `lang` enum('pl','it','es','fr','de','en','nl') NOT NULL DEFAULT 'pl',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`typeid`,`id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of reference_layout_subtypes
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_layout_types`
-- ----------------------------
DROP TABLE IF EXISTS `reference_layout_types`;
CREATE TABLE `reference_layout_types` (
  `id` int(11) NOT NULL DEFAULT '0',
  `lang` enum('pl','it','es','fr','de','en','nl') NOT NULL DEFAULT 'pl',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of reference_layout_types
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_parks`
-- ----------------------------
DROP TABLE IF EXISTS `reference_parks`;
CREATE TABLE `reference_parks` (
  `Code` char(14) NOT NULL DEFAULT '',
  `CountryCode` char(2) DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `NumberOfHouses` int(11) DEFAULT NULL,
  `NumberOfStars` int(11) DEFAULT NULL,
  `Language` enum('nl','de','fr','en','es','it','pl') DEFAULT NULL,
  `WebsiteURL` text,
  `WGS84Longitude` double(8,5) DEFAULT NULL,
  `WGS84Latitude` double(8,5) DEFAULT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of reference_parks
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_parks_facilities`
-- ----------------------------
DROP TABLE IF EXISTS `reference_parks_facilities`;
CREATE TABLE `reference_parks_facilities` (
  `Code` char(14) NOT NULL DEFAULT '',
  `Language` enum('nl','en','fr','de','es','it','pl') NOT NULL DEFAULT 'nl',
  `Facility` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Code`,`Language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of reference_parks_facilities
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_parks_texts`
-- ----------------------------
DROP TABLE IF EXISTS `reference_parks_texts`;
CREATE TABLE `reference_parks_texts` (
  `Code` char(14) NOT NULL DEFAULT '',
  `Type` enum('descr','sdescr','details') DEFAULT NULL,
  `Language` enum('nl','de','fr','en','es','it','pl') DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of reference_parks_texts
-- ----------------------------

-- ----------------------------
-- Table structure for `reference_ski_areas`
-- ----------------------------
DROP TABLE IF EXISTS `reference_ski_areas`;
CREATE TABLE `reference_ski_areas` (
  `Code` char(14) DEFAULT NULL,
  `CountryCode` char(2) DEFAULT NULL,
  `language` enum('nl','de','fr','en','es','it','pl') DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of reference_ski_areas
-- ----------------------------

-- ----------------------------
-- Table structure for `regions`
-- ----------------------------
DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `countryCode` char(2) NOT NULL DEFAULT '',
  `regionCode` char(5) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`countryCode`,`regionCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of regions
-- ----------------------------

-- ----------------------------
-- Table structure for `acco_remarks`
-- ----------------------------
DROP TABLE IF EXISTS `acco_remarks`;
CREATE TABLE `acco_remarks` (
  `houseCode` char(14) NOT NULL default '',
  `language` char(2) NOT NULL default '',
  `remark` text,
  PRIMARY KEY  (`houseCode`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acco_remarks
-- ----------------------------