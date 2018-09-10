DROP TABLE IF EXISTS `xybt_lottery`;
CREATE TABLE IF NOT EXISTS `xybt_lottery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '�����',
  `begin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '�����ʱ��',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '�����ʱ��',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '״̬��1������0�ر�',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='�齱�';

DROP VIEW IF EXISTS `xybt_lottery_1_top`;
CREATE TABLE `xybt_lottery_1_top` (
	`user_id` INT(11) NOT NULL COMMENT '�û�ID',
	`times` BIGINT(21) NOT NULL,
	`total` DOUBLE NULL,
	`last_time` INT(11) NULL COMMENT '�齱ʱ��'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `xybt_lottery_awards`;
CREATE TABLE IF NOT EXISTS `xybt_lottery_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lottery_id` int(11) NOT NULL DEFAULT '0' COMMENT '�����ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '�������',
  `intro` varchar(500) NOT NULL DEFAULT '' COMMENT '�������',
  `inventory` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '��������н�������',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '�����',
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`),
  KEY `lottery_id` (`lottery_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='����';

DROP TABLE IF EXISTS `xybt_lottery_user_awards`;
CREATE TABLE IF NOT EXISTS `xybt_lottery_user_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lottery_id` int(11) NOT NULL DEFAULT '0' COMMENT '�齱�id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '�齱ʱ��',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '�û�ID',
  `awards_id` int(11) NOT NULL DEFAULT '0' COMMENT '����ID',
  `prize_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ѡȡ��Ʒ��ID',
  `prize_time` int(11) NOT NULL DEFAULT '0' COMMENT '��Ʒ��ȡʱ��',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬��0δ����1�Ѿ�ȷ�ϣ�2�ѷ���',
  PRIMARY KEY (`id`),
  KEY `awrds_id` (`awards_id`),
  KEY `user_id` (`user_id`),
  KEY `prize_id` (`prize_id`),
  KEY `status` (`status`),
  KEY `lottery_id` (`lottery_id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COMMENT='�û��н���¼';

DROP VIEW IF EXISTS `xybt_lottery_1_top`;
DROP TABLE IF EXISTS `xybt_lottery_1_top`;
CREATE ALGORITHM=UNDEFINED DEFINER=`xybt`@`localhost` SQL SECURITY DEFINER VIEW `xybt_lottery_1_top` AS select t1.user_id,count(*) times,sum(t2.name) total,max(t1.add_time) as last_time
from xybt_lottery_user_awards as t1
left join xybt_lottery_awards as t2 on t2.id = t1.awards_id
where t1.lottery_id=1
group by t1.user_id ;

DELETE FROM `xybt_lottery`;
INSERT INTO `xybt_lottery` (`id`, `title`, `begin`, `end`, `status`) VALUES
	(1, '����齱', '2018-02-01 00:00:00', '2018-02-28 23:59:59', 1);

DELETE FROM `xybt_lottery_awards`;
INSERT INTO `xybt_lottery_awards` (`id`, `lottery_id`, `name`, `intro`, `inventory`, `sort_order`) VALUES
	(1, 1, '0.2', '����', 1000, 100),
	(2, 1, '0.4', '', 1000, 100),
	(3, 1, '0.6', '', 1000, 100),
	(4, 1, '0.8', '', 1000, 100),
	(5, 1, '1', '', 1000, 100),
	(6, 1, '2', '', 1000, 100),
	(7, 1, '5', '', 1000, 100),
	(8, 1, '10', '', 100, 100);