CREATE TABLE `xybt_channel_media_collect` (
	`media_id` INT(11) NOT NULL DEFAULT '0' COMMENT '媒体资源ID',
	`user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
	`add_time` INT(11) NOT NULL DEFAULT '0' COMMENT '收藏时间',
	PRIMARY KEY (`media_id`, `user_id`)
)
COMMENT='媒体资源收藏夹'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
