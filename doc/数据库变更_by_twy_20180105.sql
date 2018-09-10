
ALTER TABLE `xybt_channel_media`
	ADD COLUMN `power_3` INT(11) NOT NULL DEFAULT '0' COMMENT '能力值3（头条指数）' AFTER `power_2`;
	
UPDATE `xybt_channel_media` set `power_2`=`profession`,`power_3`=`age` where `type_id`=8;