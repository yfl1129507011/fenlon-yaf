# 创建数据库
create database if not exists fenlon character set=utf8;

# 创建数据表
create table if not exists `fenlon_user` (
    `uid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uName` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '用户名',
    `uPassword` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
    `uEmail` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '邮箱',
    `uCreated` INT NOT NULL DEFAULT '0' COMMENT '添加时间',
    `uUpdated` INT NOT NULL DEFAULT '0' COMMENT '更新时间',
    `uDeleted` INT NOT NULL DEFAULT '0' COMMENT '删除时间',
    `uStatus` TINYINT(3) NOT NULL DEFAULT '1' COMMENT '用户状态：1-正常，9-删除',
    PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

