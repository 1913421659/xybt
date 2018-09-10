ALTER TABLE `xybt_channel_media`
	ADD COLUMN `company_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'ËùÊôÆóÒµID' AFTER `old_id`;

update xybt_channel_media_attr_group
set sort_order=100 where id>=100
;