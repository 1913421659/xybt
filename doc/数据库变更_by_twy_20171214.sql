
ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `add_time` `add_time` INT NOT NULL DEFAULT '0' COMMENT '派单时间' AFTER `user_id`;
	
ALTER TABLE `xybt_channel_media`
	CHANGE COLUMN `desc` `description` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '简介' AFTER `link`;
	
	
ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `desc` `description` VARCHAR(400) NOT NULL DEFAULT '' COMMENT '摘要' AFTER `cover_in`;