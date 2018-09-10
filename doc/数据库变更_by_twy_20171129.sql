DROP TABLE `xybt_media`;
DROP TABLE `xybt_media_area`;
DROP TABLE `xybt_media_cat`;
DROP TABLE `xybt_media_in_cat`;
DROP TABLE `xybt_media_order`;

CREATE TABLE `xybt_channel_media` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '�����ID',
	`old_id` INT(11) NOT NULL DEFAULT '0' COMMENT '�ɼ�Դ��վ�ϵ�ID',
	`title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '��̳��/΢���ǳ�/���ں�����/΢���ǳ�/�����ǳ�',
	`title_sub` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '��̳���/΢�ź�/���ں�',
	`logo` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'ͷ��',
	`qr_img` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '��ά��',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '����',
	`desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '���',
	`power` INT(11) NOT NULL DEFAULT '0' COMMENT '����ֵ����������������',
	`power_2` INT(11) NOT NULL DEFAULT '0' COMMENT '����ֵ2��ƽ���Ķ�����',
	`price_1` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '����һ��Ԫ��',
	`price_2` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '���۶���Ԫ��',
	`price_3` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '��������Ԫ��',
	`history` INT(11) NOT NULL DEFAULT '0' COMMENT '�ӵ���',
	`profession` VARCHAR(50) NOT NULL DEFAULT '' COMMENT 'ְҵ',
	`age` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '����',
	PRIMARY KEY (`id`),
	INDEX `old_id` (`old_id`),
	INDEX `type_id` (`type_id`),
	INDEX `history` (`history`)
)
COMMENT='��Դ��������'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `xybt_channel_media_area` (
	`id` INT(11) NOT NULL DEFAULT '0',
	`name` VARCHAR(50) NOT NULL DEFAULT '',
	`region_id` INT(11) NOT NULL DEFAULT '0' COMMENT '��Ӧregion���ID',
	PRIMARY KEY (`id`),
	INDEX `region_id` (`region_id`)
)
COMMENT='��Դ������'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `xybt_channel_media_cat` (
	`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '������',
	`parent_id` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT '����ID',
	`sort_order` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����ţ�ʱ�䣩',
	PRIMARY KEY (`id`),
	INDEX `parent_id` (`parent_id`),
	INDEX `sort_order` (`sort_order`)
)
COMMENT='��Դ������������'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `xybt_channel_media_in_cat` (
	`media_id` INT(11) NOT NULL DEFAULT '0',
	`cat_id` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`media_id`, `cat_id`)
)
COMMENT='����-����'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `xybt_channel_media_order` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '�µ���ID',
	`add_time` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '�ɵ�ʱ��',
	`media_type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '��Դ����',
	`media_id` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '��ԴID����ƽ̨��',
	`status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '״̬��0�ɵ��У�1�����ɵ���2�ܾ��ɵ���3��ʱʧЧ��',
	`price_1` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '�۸�1���µ�ʱ����Ԫ��',
	`price_2` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '�۸�2���µ�ʱ����Ԫ��',
	`price_3` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '�۸�3���µ�ʱ����Ԫ��',
	`order_type` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '��������',
	`order_type_sub` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '����������',
	`price_type` INT(11) NOT NULL DEFAULT '1' COMMENT '�����۸�����sum(2^(n-1))',
	`price_sum` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '�ɵ��ܽ�Ԫ��',
	`begin_date` DATE NOT NULL DEFAULT '1970-01-01' COMMENT '��ʼ����',
	`begin_hour` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '��ʼʱ',
	`begin_minute` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '��ʼ��',
	`timeout` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '�ɵ�ʧЧʱ��',
	`days` INT(11) NOT NULL DEFAULT '1' COMMENT '����',
	`kpi_time` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT 'KPI�ϴ�ʱ�䣨ʱ��',
	`title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '����',
	`cover` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '����ͼƬ',
	`cover_in` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '������������',
	`desc` VARCHAR(400) NOT NULL DEFAULT '' COMMENT 'ժҪ',
	`author` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '����',
	`content` TEXT NOT NULL COMMENT '��������',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '����',
	`accessory` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '����',
	`remarks` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '��ע',
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	INDEX `type_id` (`media_type_id`),
	INDEX `channel_id` (`media_id`(191))
)
COMMENT='��Դ�ɵ�'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
