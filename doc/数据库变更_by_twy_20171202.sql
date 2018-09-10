-- --------------------------------------------------------
-- ����:                           dev.yi-zu.com
-- �������汾:                        5.7.18-log - MySQL Community Server (GPL)
-- ����������ϵͳ:                      Linux
-- HeidiSQL �汾:                  9.3.0.5104
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- ����  �� xybtdev.xybt_channel_media_order_type �ṹ
CREATE TABLE IF NOT EXISTS `xybt_channel_media_order_type` (
  `media_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'ý������',
  `order_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '��������',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '��������',
  `price_id` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'ʹ����һ���۸�',
  `sort_order` tinyint(4) NOT NULL DEFAULT '1' COMMENT '�����',
  PRIMARY KEY (`media_type_id`,`order_type_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='��������';

-- ���ڵ�����  xybtdev.xybt_channel_media_order_type �����ݣ�~4 rows (��Լ)
/*!40000 ALTER TABLE `xybt_channel_media_order_type` DISABLE KEYS */;
INSERT INTO `xybt_channel_media_order_type` (`media_type_id`, `order_type_id`, `name`, `price_id`, `sort_order`) VALUES
	(1, 1, '�ö�', 1, 1),
	(1, 2, '�Ӿ�', 2, 2),
	(1, 3, '�ö�+�Ӿ�', 3, 3),
	(1, 4, '��������', 1, 4),
	(1, 5, '��ҳ�Ƽ�', 2, 5),
	(2, 1, 'ֱ��', 1, 1),
	(2, 2, 'ת��', 2, 2),
	(2, 3, '��΢��', 1, 3),
	(2, 4, 'Ӳ��ֱ��', 1, 4),
	(2, 5, 'Ӳ��ת��', 2, 5),
	(3, 1, '��ͼ��', 1, 1),
	(3, 2, '��ͼ�ĵ�һ��', 2, 2),
	(3, 3, '��ͷ��', 3, 3),
	(4, 1, 'ֱ��', 1, 1),
	(4, 2, 'ת��', 2, 2);
/*!40000 ALTER TABLE `xybt_channel_media_order_type` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;



ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `order_type` `order_type` TINYINT NOT NULL DEFAULT '0' COMMENT '��������' AFTER `price_3`;
ALTER TABLE `xybt_channel_media_order`
	CHANGE COLUMN `price_type` `price_type` TINYINT NOT NULL DEFAULT '1' COMMENT '�����۸����ͣ���Ӧý��������۸�' AFTER `order_type_sub`;
