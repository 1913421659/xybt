
ALTER TABLE `xybt_channel_media`
	ADD COLUMN `average_play_number` INT(10) NOT NULL DEFAULT '0' COMMENT 'ƽ��������' AFTER `sort_order`,
	ADD COLUMN `average_comment_number` INT(10) NOT NULL DEFAULT '0' COMMENT 'ƽ��������' AFTER `average_play_number`,
	ADD COLUMN `average_favour_number` INT(10) NOT NULL DEFAULT '0' COMMENT 'ƽ��������' AFTER `average_comment_number`;