CREATE TABLE `xybt_channel_media_collect` (
	`media_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'ý����ԴID',
	`user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '�û�ID',
	`add_time` INT(11) NOT NULL DEFAULT '0' COMMENT '�ղ�ʱ��',
	PRIMARY KEY (`media_id`, `user_id`)
)
COMMENT='ý����Դ�ղؼ�'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
