-- --------------------------------------------------------
-- ����:                           127.0.0.1
-- �������汾:                        5.5.12-log - MySQL Community Server (GPL)
-- ����������ϵͳ:                      Win32
-- HeidiSQL �汾:                  9.3.0.5104
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- ����  �� xybtdev.xybt_channel_media_attr �ṹ
CREATE TABLE IF NOT EXISTS `xybt_channel_media_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '��ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '����ֵ��',
  `sort_order` int(11) NOT NULL DEFAULT '1' COMMENT '�����',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT COLLATE='utf8mb4_general_ci' COMMENT='ý����Դ��չ����';

-- ���ڵ�����  xybtdev.xybt_channel_media_attr �����ݣ�0 rows
/*!40000 ALTER TABLE `xybt_channel_media_attr` DISABLE KEYS */;
INSERT INTO `xybt_channel_media_attr` (`id`, `group_id`, `name`, `sort_order`) VALUES
	(1, 1, '�Ż���̳', 1),
	(2, 1, '��ֱ��̳', 2),
	(3, 2, '�Ӿ�', 1),
	(4, 2, '�ö�', 2),
	(5, 2, '����ͼ', 3),
	(6, 2, '������', 4),
	(7, 2, 'ȫվ', 5),
	(8, 3, '����', 1),
	(9, 3, '��Ѷ', 2),
	(10, 4, '����', 1),
	(11, 4, '΢��Ů��', 2),
	(12, 4, '������֤', 3),
	(13, 4, '��ҵ��֤', 4),
	(14, 4, '�ٷ���֤', 5),
	(15, 4, 'δ��֤', 6),
	(16, 4, '��V', 7),
	(17, 5, '��֤', 1),
	(18, 5, 'δ��֤', 2),
	(19, 6, '���ĺ�', 1),
	(20, 6, '�����', 2),
	(21, 7, 'δ��ͨ', 1),
	(22, 7, '�ѿ�ͨ', 2),
	(23, 8, '������', 1),
	(24, 8, '����', 2),
	(25, 9, '��', 1),
	(26, 9, '��', 2),
	(27, 10, '��', 1),
	(28, 10, '��', 2),
	(29, 10, '��', 3),
	(30, 11, '����', 1),
	(31, 11, '����', 2),
	(32, 11, '�Ѻ�', 3),
	(33, 12, 'ʮ��', 1),
	(34, 12, '����', 2),
	(35, 12, 'ǧ��', 3),
	(36, 13, '����', 1),
	(37, 13, '�ſ�', 2),
	(38, 13, '��6', 3),
	(39, 13, '�Ѻ�', 4),
	(40, 13, '��Ѷ', 5),
	(41, 13, '����', 6),
	(42, 13, '56', 7),
	(43, 13, '���׻�', 8),
	(44, 13, '����', 9),
	(45, 13, '����', 10),
	(46, 13, '������', 11),
	(47, 13, '��һ��Ƶ', 12),
	(48, 13, '������', 13),
	(49, 13, '�����Ƶ', 14),
	(50, 14, '��ҳλ��', 1),
	(51, 14, 'Ƶ��λ��', 2),
	(52, 14, '����ҳ��', 3),
	(53, 15, '����', 1),
	(54, 15, '����', 2),
	(55, 15, '����', 3),
	(56, 15, 'ӳ��', 4),
	(57, 15, '����', 5),
	(58, 15, '����', 6),
	(59, 15, 'һֱ��', 7);
/*!40000 ALTER TABLE `xybt_channel_media_attr` ENABLE KEYS */;

-- ����  �� xybtdev.xybt_channel_media_attr_group �ṹ
CREATE TABLE IF NOT EXISTS `xybt_channel_media_attr_group` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '����',
  `media_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '����',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '�����',
  PRIMARY KEY (`id`),
  KEY `media_type_id` (`media_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT COLLATE='utf8mb4_general_ci' COMMENT='ý����Դ���Է���';

-- ���ڵ�����  xybtdev.xybt_channel_media_attr_group �����ݣ�15 rows
/*!40000 ALTER TABLE `xybt_channel_media_attr_group` DISABLE KEYS */;
INSERT INTO `xybt_channel_media_attr_group` (`id`, `name`, `media_type_id`, `sort_order`) VALUES
	(1, '���', 1, 1),
	(2, 'Ч��', 1, 2),
	(3, '��վ', 2, 1),
	(4, '��֤', 2, 2),
	(5, '��֤', 3, 1),
	(6, '����', 3, 2),
	(7, '������', 3, 3),
	(8, '�Զ���˵�', 3, 4),
	(9, '�Ƿ�ԭ��', 3, 5),
	(10, '������϶�', 3, 6),
	(11, '��վ', 5, 1),
	(12, '����', 5, 2),
	(13, '��վ', 6, 1),
	(14, '��ڼ���', 6, 2),
	(15, 'ƽ̨', 7, 1);
/*!40000 ALTER TABLE `xybt_channel_media_attr_group` ENABLE KEYS */;

-- ����  �� xybtdev.xybt_channel_media_in_attr �ṹ
CREATE TABLE IF NOT EXISTS `xybt_channel_media_in_attr` (
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ý��ID',
  `attr_id` int(11) NOT NULL DEFAULT '0' COMMENT '����ID',
  PRIMARY KEY (`media_id`,`attr_id`)
) ENGINE=InnoDB DEFAULT COLLATE='utf8mb4_general_ci' COMMENT='ý����Դ�����Թ���';


ALTER TABLE `xybt_channel_media`
	ADD COLUMN `province_id` SMALLINT NOT NULL DEFAULT '0' COMMENT 'ʡ' AFTER `age`,
	ADD COLUMN `city_id` SMALLINT NOT NULL DEFAULT '0' COMMENT '��' AFTER `province_id`,
	ADD COLUMN `district_id` SMALLINT NOT NULL DEFAULT '0' COMMENT '��' AFTER `city_id`,
	ADD COLUMN `status` TINYINT NOT NULL DEFAULT '1' COMMENT '״̬' AFTER `district_id`;

