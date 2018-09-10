#20170911
ALTER TABLE `xybt_article`
	ADD COLUMN `admin_id` INT NOT NULL DEFAULT '39' AFTER `description`;
ALTER TABLE `xybt_goods`
	ADD COLUMN `admin_id` INT NOT NULL DEFAULT '39' AFTER `goods_unit`;
#20170918
ALTER TABLE `xybt_article`
	DROP COLUMN `admin_id`;
ALTER TABLE `xybt_goods`
	CHANGE COLUMN `admin_id` `admin_id` INT(11) NOT NULL DEFAULT '0' AFTER `goods_unit`;