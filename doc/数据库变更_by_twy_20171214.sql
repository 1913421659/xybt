
ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `add_time` `add_time` INT NOT NULL DEFAULT '0' COMMENT '�ɵ�ʱ��' AFTER `user_id`;
	
ALTER TABLE `xybt_channel_media`
	CHANGE COLUMN `desc` `description` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '���' AFTER `link`;
	
	
ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `desc` `description` VARCHAR(400) NOT NULL DEFAULT '' COMMENT 'ժҪ' AFTER `cover_in`;