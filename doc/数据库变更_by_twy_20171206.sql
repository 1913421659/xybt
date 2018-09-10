REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (100, '����', 1, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (200, '����', 2, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (300, '����', 3, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (400, '����', 4, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (500, '����', 5, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (600, '����', 6, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (700, '����', 7, 0);
REPLACE INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES (800, '����', 8, 0);

insert into xybt_channel_media_attr
(id,group_id,name,sort_order)
select t1.id, floor(t1.id/100)*100, t1.name, t1.id%100
from xybt_channel_media_cat as t1
where 1
and t1.id > 100
;

insert into xybt_channel_media_in_attr
(media_id,attr_id)
select media_id,cat_id from xybt_channel_media_in_cat
;

update xybt_channel_media_cat set sort_order = 0;

ALTER TABLE `xybt_channel_media_cat`
	CHANGE COLUMN `sort_order` `sort_order` SMALLINT NOT NULL DEFAULT '0' COMMENT '�����' AFTER `parent_id`
;
