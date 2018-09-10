
update xybt_channel set price_1=price_1/100,price_2=price_2/100,price_3=price_3/100;

ALTER TABLE `xybt_channel`
	CHANGE COLUMN `price_1` `price_1` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '����һ��Ԫ��' AFTER `power`,
	CHANGE COLUMN `price_2` `price_2` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '���۶���Ԫ��' AFTER `price_1`,
	CHANGE COLUMN `price_3` `price_3` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '��������Ԫ��' AFTER `price_2`;

CREATE TABLE `xybt_channel_order` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '�µ���ID',
	`add_time` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '�ɵ�ʱ��',
	`type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '��Դ����',
	`channel_id` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '��ԴID����ƽ̨��',
	`status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '״̬��0�ɵ��У�1�����ɵ���2�ܾ��ɵ���3��ʱʧЧ��',
	`price_1` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '�۸�1���µ�ʱ����Ԫ��',
	`price_2` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '�۸�2���µ�ʱ����Ԫ��',
	`price_3` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '�۸�3���µ�ʱ����Ԫ��',
	`order_type` INT(11) NOT NULL DEFAULT '0' COMMENT '��������sum(2^(n-1))',
	`days` INT(11) NOT NULL DEFAULT '1' COMMENT '����',
	`price_sum` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '�ɵ��ܽ�Ԫ��',
	`begin_date` DATE NOT NULL DEFAULT '1970-01-01' COMMENT '��ʼ����',
	`begin_hour` INT(11) NOT NULL DEFAULT '0' COMMENT '��ʼʱ',
	`begin_minute` INT(11) NOT NULL DEFAULT '0' COMMENT '��ʼ��',
	`title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '����',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '����',
	`remarks` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '��ע',
	`data` TEXT NOT NULL COMMENT '������Ϣ',
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	INDEX `type_id` (`type_id`),
	INDEX `channel_id` (`channel_id`(191))
)
COMMENT='��Դ�ɵ�'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
