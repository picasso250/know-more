CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(145) NOT NULL,
  `password` varchar(255) NOT NULL,
  
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pdate_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一条心跳包的时间',

  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uni_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
CREATE TABLE `know_more`.`question` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `create_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `know_more`.`answer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `qid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `create_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `know_more`.`question` 
CHANGE COLUMN `description` `detail` TEXT NULL ;
ALTER TABLE `know_more`.`user` 
ADD COLUMN `show_name` VARCHAR(45) NULL AFTER `pdate_time`;
ALTER TABLE `know_more`.`user` 
CHANGE COLUMN `show_name` `show_name` VARCHAR(45) NOT NULL AFTER `email`;
ALTER TABLE `know_more`.`user` 
CHANGE COLUMN `pdate_time` `update_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一条心跳包的时间' ;
ALTER TABLE `know_more`.`user` 
ADD COLUMN `detail` VARCHAR(255) NULL AFTER `update_time`;
