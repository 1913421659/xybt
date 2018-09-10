
ALTER TABLE `xybt_channel_media`
	ADD COLUMN `average_play_number` INT(10) NOT NULL DEFAULT '0' COMMENT '平均播放数' AFTER `sort_order`,
	ADD COLUMN `average_comment_number` INT(10) NOT NULL DEFAULT '0' COMMENT '平均评论数' AFTER `average_play_number`,
	ADD COLUMN `average_favour_number` INT(10) NOT NULL DEFAULT '0' COMMENT '平均点赞数' AFTER `average_comment_number`;