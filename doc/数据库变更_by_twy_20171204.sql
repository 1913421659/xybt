-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.5.12-log - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win32
-- HeidiSQL 版本:                  9.3.0.5104
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 xybtdev.xybt_channel_media_attr 结构
CREATE TABLE IF NOT EXISTS `xybt_channel_media_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '组ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '属性值名',
  `sort_order` int(11) NOT NULL DEFAULT '1' COMMENT '排序号',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT COLLATE='utf8mb4_general_ci' COMMENT='媒体资源扩展属性';

-- 正在导出表  xybtdev.xybt_channel_media_attr 的数据：0 rows
/*!40000 ALTER TABLE `xybt_channel_media_attr` DISABLE KEYS */;
INSERT INTO `xybt_channel_media_attr` (`id`, `group_id`, `name`, `sort_order`) VALUES
	(1, 1, '门户论坛', 1),
	(2, 1, '垂直论坛', 2),
	(3, 2, '加精', 1),
	(4, 2, '置顶', 2),
	(5, 2, '焦点图', 3),
	(6, 2, '文字链', 4),
	(7, 2, '全站', 5),
	(8, 3, '新浪', 1),
	(9, 3, '腾讯', 2),
	(10, 4, '达人', 1),
	(11, 4, '微博女郎', 2),
	(12, 4, '个人认证', 3),
	(13, 4, '企业认证', 4),
	(14, 4, '官方认证', 5),
	(15, 4, '未认证', 6),
	(16, 4, '金V', 7),
	(17, 5, '认证', 1),
	(18, 5, '未认证', 2),
	(19, 6, '订阅号', 1),
	(20, 6, '服务号', 2),
	(21, 7, '未开通', 1),
	(22, 7, '已开通', 2),
	(23, 8, '不可以', 1),
	(24, 8, '可以', 2),
	(25, 9, '是', 1),
	(26, 9, '否', 2),
	(27, 10, '高', 1),
	(28, 10, '中', 2),
	(29, 10, '低', 3),
	(30, 11, '新浪', 1),
	(31, 11, '网易', 2),
	(32, 11, '搜狐', 3),
	(33, 12, '十万级', 1),
	(34, 12, '百万级', 2),
	(35, 12, '千万级', 3),
	(36, 13, '土豆', 1),
	(37, 13, '优酷', 2),
	(38, 13, '酷6', 3),
	(39, 13, '搜狐', 4),
	(40, 13, '腾讯', 5),
	(41, 13, '新浪', 6),
	(42, 13, '56', 7),
	(43, 13, '爆米花', 8),
	(44, 13, '激动', 9),
	(45, 13, '乐视', 10),
	(46, 13, '播视网', 11),
	(47, 13, '第一视频', 12),
	(48, 13, '爱奇艺', 13),
	(49, 13, '凤凰视频', 14),
	(50, 14, '首页位置', 1),
	(51, 14, '频道位置', 2),
	(52, 14, '三级页面', 3),
	(53, 15, '秒拍', 1),
	(54, 15, '美拍', 2),
	(55, 15, '花椒', 3),
	(56, 15, '映客', 4),
	(57, 15, '斗鱼', 5),
	(58, 15, '快手', 6),
	(59, 15, '一直播', 7);
/*!40000 ALTER TABLE `xybt_channel_media_attr` ENABLE KEYS */;

-- 导出  表 xybtdev.xybt_channel_media_attr_group 结构
CREATE TABLE IF NOT EXISTS `xybt_channel_media_attr_group` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '组名',
  `media_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '所属',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`),
  KEY `media_type_id` (`media_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT COLLATE='utf8mb4_general_ci' COMMENT='媒体资源属性分组';

-- 正在导出表  xybtdev.xybt_channel_media_attr_group 的数据：15 rows
/*!40000 ALTER TABLE `xybt_channel_media_attr_group` DISABLE KEYS */;
INSERT INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES
	(1, '类别', 1, 1),
	(2, '效果', 1, 2),
	(3, '网站', 2, 1),
	(4, '认证', 2, 2),
	(5, '认证', 3, 1),
	(6, '类型', 3, 2),
	(7, '流量主', 3, 3),
	(8, '自定义菜单', 3, 4),
	(9, '是否原创', 3, 5),
	(10, '发布配合度', 3, 6),
	(11, '网站', 5, 1),
	(12, '级别', 5, 2),
	(13, '网站', 6, 1),
	(14, '入口级别', 6, 2),
	(15, '平台', 7, 1);
/*!40000 ALTER TABLE `xybt_channel_media_attr_group` ENABLE KEYS */;

-- 导出  表 xybtdev.xybt_channel_media_in_attr 结构
CREATE TABLE IF NOT EXISTS `xybt_channel_media_in_attr` (
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '媒体ID',
  `attr_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性ID',
  PRIMARY KEY (`media_id`,`attr_id`)
) ENGINE=InnoDB DEFAULT COLLATE='utf8mb4_general_ci' COMMENT='媒体资源的属性关联';


ALTER TABLE `xybt_channel_media`
	ADD COLUMN `province_id` SMALLINT NOT NULL DEFAULT '0' COMMENT '省' AFTER `age`,
	ADD COLUMN `city_id` SMALLINT NOT NULL DEFAULT '0' COMMENT '市' AFTER `province_id`,
	ADD COLUMN `district_id` SMALLINT NOT NULL DEFAULT '0' COMMENT '区' AFTER `city_id`,
	ADD COLUMN `status` TINYINT NOT NULL DEFAULT '1' COMMENT '状态' AFTER `district_id`;

