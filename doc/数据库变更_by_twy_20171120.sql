
RENAME TABLE `xybt_channel` TO `xybt_media`;
RENAME TABLE `xybt_channel_area` TO `xybt_media_area`;
RENAME TABLE `xybt_channel_cat` TO `xybt_media_cat`;
RENAME TABLE `xybt_channel_in_cat` TO `xybt_media_in_cat`;
RENAME TABLE `xybt_channel_order` TO `xybt_media_order`;


ALTER TABLE `xybt_media_order`
	CHANGE COLUMN `channel_id` `media_id` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '资源ID（本平台）' AFTER `type_id`;
	

ALTER TABLE `xybt_media_in_cat`
	CHANGE COLUMN `channel_id` `media_id` INT(11) NOT NULL DEFAULT '0' FIRST;
	
