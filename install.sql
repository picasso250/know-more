CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(145) NOT NULL,
  `password` varchar(255) NOT NULL,
  
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pdate_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一条心跳包的时间',

  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uni_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
