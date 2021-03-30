CREATE TABLE IF NOT EXISTS `worldCities` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(30) DEFAULT NULL,
  `city_ascii` varchar(30) DEFAULT NULL,
  `lat` varchar(30) DEFAULT NULL,
  `lng` varchar(30) DEFAULT NULL,
  `country` varchar(30) DEFAULT NULL,
  `iso2` varchar(30) DEFAULT NULL,
  `iso3` varchar(30) DEFAULT NULL,
  `admin_name` varchar(30) DEFAULT NULL,
  `capital` varchar(30) DEFAULT NULL,
  `population` varchar(30) DEFAULT NULL,
  `id` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE worldCities;