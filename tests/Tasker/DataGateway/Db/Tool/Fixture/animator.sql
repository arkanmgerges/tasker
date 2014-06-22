DROP TABLE IF EXISTS `tasker_lock`;
CREATE TABLE `tasker_lock` (
  `id` varchar(65) NOT NULL,
  `creatingDateTime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This will be used by the processes to reserve the action that needed to be done';

DROP TABLE IF EXISTS `tasker_task`;
CREATE TABLE `tasker_task` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `server` VARCHAR(45) DEFAULT "",
  `statusId` TINYINT DEFAULT 0,
  `typeId` TINYINT DEFAULT 1,
  `repeatingInterval`  INT(11) UNSIGNED DEFAULT 0,
  `creatingDateTime` DATETIME NULL,
  `startingDateTime` DATETIME NULL,
  `endingDateTime` DATETIME NULL,
  `modifyingDateTime` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `priority` SMALLINT UNSIGNED DEFAULT 0,
  `externalTypeId` VARCHAR (128) DEFAULT "",
  `externalId` VARCHAR (128) DEFAULT "",
  `externalData` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `server_idx` (`server` ASC),
  INDEX `repeatingInterval_idx` (`repeatingInterval` ASC),
  INDEX `startingDateTime_idx` (`startingDateTime` ASC),
  INDEX `modifyingDateTime_idx` (`modifyingDateTime` ASC),
  INDEX `statusId_idx` (`statusId` ASC),
  INDEX `priority_idx` (`priority` ASC),
  INDEX `externalId_idx` (`externalId` ASC),
  INDEX `externalTypeId_idx` (`externalTypeId` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tasker_process`;
CREATE TABLE `tasker_process` (
  `id` INT UNSIGNED NOT NULL,
  `server` VARCHAR(45) NULL,
  `extra` VARCHAR(20) NULL,
  `creatingDateTime` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `creatingDateTime_idx` (`creatingDateTime` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;