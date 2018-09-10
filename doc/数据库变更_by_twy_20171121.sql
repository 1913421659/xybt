
ALTER TABLE `xybt_media_area`
	ADD COLUMN `region_id` INT NOT NULL DEFAULT '0' COMMENT '对应region表的ID' AFTER `name`,
	ADD INDEX `region_id` (`region_id`);

update xybt_media_area as t1
left join xybt_region as t2 on t2.region_name = t1.name and t2.region_type=1
set t1.region_id = t2.region_id;

RENAME TABLE `xybt_media` TO `xybt_channel_media`;
RENAME TABLE `xybt_media_area` TO `xybt_channel_media_area`;
RENAME TABLE `xybt_media_cat` TO `xybt_channel_media_cat`;
RENAME TABLE `xybt_media_in_cat` TO `xybt_channel_media_in_cat`;
RENAME TABLE `xybt_media_order` TO `xybt_channel_media_order`;