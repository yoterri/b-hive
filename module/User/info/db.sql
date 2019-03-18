
-- DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user`(
`id` BIGINT NOT NULL AUTO_INCREMENT 
,`first_name` VARCHAR(150)
,`last_name` VARCHAR(150)
,`email` CHAR(200)
,`password` VARCHAR(50)
,`status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=disabled,1=enabled,2=unconfirmed'
,`created_on` DATETIME NOT NULL

,PRIMARY KEY (`id`)
,KEY(`email`)
)ENGINE = INNODB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS `user_reset_password`;
CREATE TABLE IF NOT EXISTS `user_reset_password`(
`email` CHAR(200)
,`key` CHAR(50)
,`created_on` DATETIME NOT NULL

,PRIMARY KEY(`email`)
,KEY(`key`)
)ENGINE = INNODB DEFAULT CHARSET=utf8;
