
update xybt_channel set price_1=price_1/100,price_2=price_2/100,price_3=price_3/100;

ALTER TABLE `xybt_channel`
	CHANGE COLUMN `price_1` `price_1` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '报价一（元）' AFTER `power`,
	CHANGE COLUMN `price_2` `price_2` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '报价二（元）' AFTER `price_1`,
	CHANGE COLUMN `price_3` `price_3` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '报价三（元）' AFTER `price_2`;

CREATE TABLE `xybt_channel_order` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '下单者ID',
	`add_time` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '派单时间',
	`type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '资源类型',
	`channel_id` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '资源ID（本平台）',
	`status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '状态（0派单中；1接受派单；2拒绝派单；3超时失效）',
	`price_1` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '价格1（下单时）（元）',
	`price_2` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '价格2（下单时）（元）',
	`price_3` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '价格3（下单时）（元）',
	`order_type` INT(11) NOT NULL DEFAULT '0' COMMENT '订单类型sum(2^(n-1))',
	`days` INT(11) NOT NULL DEFAULT '1' COMMENT '天数',
	`price_sum` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '派单总金额（元）',
	`begin_date` DATE NOT NULL DEFAULT '1970-01-01' COMMENT '开始日期',
	`begin_hour` INT(11) NOT NULL DEFAULT '0' COMMENT '开始时',
	`begin_minute` INT(11) NOT NULL DEFAULT '0' COMMENT '开始分',
	`title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '标题',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '链接',
	`remarks` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '备注',
	`data` TEXT NOT NULL COMMENT '其它信息',
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	INDEX `type_id` (`type_id`),
	INDEX `channel_id` (`channel_id`(191))
)
COMMENT='资源派单'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
