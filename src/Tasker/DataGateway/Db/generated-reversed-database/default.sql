
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- configuration
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `configuration`;

CREATE TABLE `configuration`
(
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    `value` TEXT,
    `caption` TEXT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `key` (`key`(64))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- employee
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `employee`;

CREATE TABLE `employee`
(
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(64) DEFAULT '' NOT NULL,
    `email` VARCHAR(64) DEFAULT '' NOT NULL,
    `password` CHAR(60) DEFAULT '' NOT NULL,
    `status` TINYINT(1) DEFAULT 1 NOT NULL,
    `addingDateTime` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    `updatingDateTime` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `email` (`email`(64))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- actions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `action`;

CREATE TABLE `action`
(
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `action` VARCHAR(64) DEFAULT '' NOT NULL,
    `data` VARCHAR(4096) DEFAULT '' NOT NULL,
    `status` TINYINT(1) DEFAULT 1 NOT NULL,
    `startingDateTime` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `action` (`action`(64))
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
