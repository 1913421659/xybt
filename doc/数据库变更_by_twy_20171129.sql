DROP TABLE `xybt_media`;
DROP TABLE `xybt_media_area`;
DROP TABLE `xybt_media_cat`;
DROP TABLE `xybt_media_in_cat`;
DROP TABLE `xybt_media_order`;

CREATE TABLE `xybt_channel_media` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '大分类ID',
	`old_id` INT(11) NOT NULL DEFAULT '0' COMMENT '采集源网站上的ID',
	`title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '论坛名/微博昵称/公众号名称/微信昵称/博客昵称',
	`title_sub` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '论坛版块/微信号/公众号',
	`logo` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '头像',
	`qr_img` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '二维码',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '链接',
	`desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '简介',
	`power` INT(11) NOT NULL DEFAULT '0' COMMENT '能力值（粉数、好友数）',
	`power_2` INT(11) NOT NULL DEFAULT '0' COMMENT '能力值2（平均阅读量）',
	`price_1` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价一（元）',
	`price_2` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价二（元）',
	`price_3` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价三（元）',
	`history` INT(11) NOT NULL DEFAULT '0' COMMENT '接单数',
	`profession` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '职业',
	`age` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '年龄',
	PRIMARY KEY (`id`),
	INDEX `old_id` (`old_id`),
	INDEX `type_id` (`type_id`),
	INDEX `history` (`history`)
)
COMMENT='资源（渠道）'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `xybt_channel_media_area` (
	`id` INT(11) NOT NULL DEFAULT '0',
	`name` VARCHAR(50) NOT NULL DEFAULT '',
	`region_id` INT(11) NOT NULL DEFAULT '0' COMMENT '对应region表的ID',
	PRIMARY KEY (`id`),
	INDEX `region_id` (`region_id`)
)
COMMENT='资源地区表'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `xybt_channel_media_cat` (
	`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '分类名',
	`parent_id` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT '父级ID',
	`sort_order` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '排序号（时间）',
	PRIMARY KEY (`id`),
	INDEX `parent_id` (`parent_id`),
	INDEX `sort_order` (`sort_order`)
)
COMMENT='资源（渠道）分类'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `xybt_channel_media_in_cat` (
	`media_id` INT(11) NOT NULL DEFAULT '0',
	`cat_id` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`media_id`, `cat_id`)
)
COMMENT='渠道-分类'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `xybt_channel_media_order` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '下单者ID',
	`add_time` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '派单时间',
	`media_type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '资源类型',
	`media_id` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '资源ID（本平台）',
	`status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '状态（0派单中；1接受派单；2拒绝派单；3超时失效）',
	`price_1` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '价格1（下单时）（元）',
	`price_2` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '价格2（下单时）（元）',
	`price_3` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '价格3（下单时）（元）',
	`order_type` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '订单类型',
	`order_type_sub` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '订单子类型',
	`price_type` INT(11) NOT NULL DEFAULT '1' COMMENT '订单价格类型sum(2^(n-1))',
	`price_sum` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '派单总金额（元）',
	`begin_date` DATE NOT NULL DEFAULT '1970-01-01' COMMENT '开始日期',
	`begin_hour` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '开始时',
	`begin_minute` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '开始分',
	`timeout` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '派单失效时间',
	`days` INT(11) NOT NULL DEFAULT '1' COMMENT '天数',
	`kpi_time` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT 'KPI上传时间（时）',
	`title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '标题',
	`cover` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '封面图片',
	`cover_in` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '封面在内容中',
	`desc` VARCHAR(400) NOT NULL DEFAULT '' COMMENT '摘要',
	`author` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '作者',
	`content` TEXT NOT NULL COMMENT '正文内容',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '链接',
	`accessory` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '附件',
	`remarks` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '备注',
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	INDEX `type_id` (`media_type_id`),
	INDEX `channel_id` (`media_id`(191))
)
COMMENT='资源派单'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
