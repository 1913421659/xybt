-- --------------------------------------------------------
-- 主机:                           dev.yi-zu.com
-- 服务器版本:                        5.7.18-log - MySQL Community Server (GPL)
-- 服务器操作系统:                      Linux
-- HeidiSQL 版本:                  9.3.0.5104
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 xybtdev.xybt_channel_media_order_type 结构
CREATE TABLE IF NOT EXISTS `xybt_channel_media_order_type` (
  `media_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '媒体类型',
  `order_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单类型',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '类型名称',
  `price_id` tinyint(4) NOT NULL DEFAULT '1' COMMENT '使用哪一个价格',
  `sort_order` tinyint(4) NOT NULL DEFAULT '1' COMMENT '排序号',
  PRIMARY KEY (`media_type_id`,`order_type_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单类型';

-- 正在导出表  xybtdev.xybt_channel_media_order_type 的数据：~4 rows (大约)
/*!40000 ALTER TABLE `xybt_channel_media_order_type` DISABLE KEYS */;
INSERT INTO `xybt_channel_media_order_type` (`media_type_id`, `order_type_id`, `name`, `price_id`, `sort_order`) VALUES
	(1, 1, '置顶', 1, 1),
	(1, 2, '加精', 2, 2),
	(1, 3, '置顶+加精', 3, 3),
	(1, 4, '版主发稿', 1, 4),
	(1, 5, '首页推荐', 2, 5),
	(2, 1, '直发', 1, 1),
	(2, 2, '转发', 2, 2),
	(2, 3, '长微博', 1, 3),
	(2, 4, '硬广直发', 1, 4),
	(2, 5, '硬广转发', 2, 5),
	(3, 1, '单图文', 1, 1),
	(3, 2, '多图文第一条', 2, 2),
	(3, 3, '非头条', 3, 3),
	(4, 1, '直发', 1, 1),
	(4, 2, '转发', 2, 2);
/*!40000 ALTER TABLE `xybt_channel_media_order_type` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;



ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `order_type` `order_type` TINYINT NOT NULL DEFAULT '0' COMMENT '订单类型' AFTER `price_3`;
ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `price_type` `price_type` TINYINT NOT NULL DEFAULT '1' COMMENT '订单价格类型（对应媒体的三个价格）' AFTER `order_type_sub`;
