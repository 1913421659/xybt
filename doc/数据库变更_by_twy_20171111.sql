CREATE TABLE `xybt_channel` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '大分类ID',
	`old_id` INT(11) NOT NULL DEFAULT '0' COMMENT '采集源网站上的ID',
	`title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '论坛名/微博昵称/公众号名称/微信昵称/博客昵称',
	`title_sub` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '论坛版块/微信号/公众号',
	`logo` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '头像',
	`qr_img` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '二维码',
	`link` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '链接',
	`desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '简介',
	`power` INT(11) NOT NULL DEFAULT '0' COMMENT '能力值（粉数、好友数）',
	`price_1` INT(11) NOT NULL DEFAULT '0' COMMENT '报价一（分）',
	`price_2` INT(11) NOT NULL DEFAULT '0' COMMENT '报价二（分）',
	`price_3` INT(11) NOT NULL DEFAULT '0' COMMENT '报价三（分）',
	`history` INT(11) NOT NULL DEFAULT '0' COMMENT '接单数',
	`profession` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '职业',
	`age` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '年龄',
	PRIMARY KEY (`id`),
	INDEX `old_id` (`old_id`),
	INDEX `type_id` (`type_id`)
)
COMMENT='资源（渠道）'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `xybt_channel_area` (
	`id` INT(11) NOT NULL DEFAULT '0',
	`name` VARCHAR(50) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)
COMMENT='资源地区表'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

INSERT INTO `xybt_channel_area` (`id`, `name`) VALUES
	(1, '北京'),
	(2, '上海'),
	(3, '广东'),
	(4, '重庆'),
	(5, '四川'),
	(6, '山西'),
	(7, '天津'),
	(8, '辽宁'),
	(9, '吉林'),
	(10, '河北'),
	(11, '江苏'),
	(12, '浙江'),
	(13, '安徽'),
	(14, '福建'),
	(15, '江西'),
	(16, '山东'),
	(17, '河南'),
	(18, '湖北'),
	(19, '湖南'),
	(20, '陕西'),
	(21, '甘肃'),
	(22, '黑龙江'),
	(23, '贵州'),
	(24, '海南'),
	(25, '云南'),
	(26, '青海'),
	(27, '台湾'),
	(28, '广西'),
	(29, '西藏'),
	(30, '宁夏'),
	(31, '新疆'),
	(32, '内蒙古'),
	(33, '澳门'),
	(34, '香港');


CREATE TABLE IF NOT EXISTS `xybt_channel_cat` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名',
  `parent_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `sort_order` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '排序号（时间）',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=836 DEFAULT CHARSET=utf8mb4 COMMENT='资源（渠道）分类';


INSERT INTO `xybt_channel_cat` (`id`, `name`, `parent_id`, `sort_order`) VALUES
	(1, '论坛资源', 0, '0000-00-00 00:00:00'),
	(2, '微博资源', 0, '2017-11-08 11:28:06'),
	(3, '微信资源', 0, '2017-11-08 11:28:15'),
	(4, '朋友圈资源', 0, '2017-11-08 11:28:23'),
	(5, '博客资源', 0, '2017-11-08 11:28:30'),
	(6, '视频资源', 0, '2017-11-08 11:30:10'),
	(101, '汽车', 1, '2017-11-08 14:02:45'),
	(102, '时尚', 1, '2017-11-08 14:02:45'),
	(103, 'IT', 1, '2017-11-08 14:02:45'),
	(104, '娱乐', 1, '2017-11-08 14:02:45'),
	(105, '旅游', 1, '2017-11-08 14:02:45'),
	(106, '女性', 1, '2017-11-08 14:02:45'),
	(107, '健康', 1, '2017-11-08 14:02:45'),
	(108, '亲子', 1, '2017-11-08 14:02:45'),
	(109, '摄影', 1, '2017-11-08 14:02:45'),
	(110, '游戏', 1, '2017-11-08 14:02:45'),
	(111, '房产', 1, '2017-11-08 14:02:45'),
	(112, '体育', 1, '2017-11-08 14:02:45'),
	(113, '财经', 1, '2017-11-08 14:02:45'),
	(114, '家居', 1, '2017-11-08 14:02:45'),
	(115, '杂谈', 1, '2017-11-08 14:02:45'),
	(116, '校园', 1, '2017-11-08 14:02:45'),
	(117, '全站', 1, '2017-11-08 14:02:45'),
	(118, '动漫', 1, '2017-11-08 14:02:45'),
	(119, '地区', 1, '2017-11-08 14:02:45'),
	(120, '美食', 1, '2017-11-08 14:02:45'),
	(121, '科技数码', 1, '2017-11-08 14:02:45'),
	(122, '音乐', 1, '2017-11-08 14:02:45'),
	(123, '文学', 1, '2017-11-08 14:02:45'),
	(124, '生活', 1, '2017-11-08 14:02:45'),
	(125, '宠物', 1, '2017-11-08 14:02:45'),
	(126, '购物', 1, '2017-11-08 14:02:45'),
	(127, '情感', 1, '2017-11-08 14:02:45'),
	(128, '美容护肤', 1, '2017-11-08 14:02:45'),
	(129, '综合', 1, '2017-11-08 14:02:45'),
	(130, '手机', 1, '2017-11-08 14:02:45'),
	(131, '教育', 1, '2017-11-08 14:02:45'),
	(201, '段子手', 2, '2017-11-08 14:35:55'),
	(202, '地区号', 2, '2017-11-08 14:35:55'),
	(203, '美容护肤', 2, '2017-11-08 14:35:55'),
	(204, '主持人', 2, '2017-11-08 14:35:55'),
	(205, '网络红人', 2, '2017-11-08 14:35:55'),
	(206, '记者', 2, '2017-11-08 14:35:55'),
	(207, '歌手', 2, '2017-11-08 14:35:55'),
	(208, '评论人', 2, '2017-11-08 14:35:55'),
	(209, '财经', 2, '2017-11-08 14:35:55'),
	(210, '汽车', 2, '2017-11-08 14:35:55'),
	(211, '校园', 2, '2017-11-08 14:35:55'),
	(212, '教育', 2, '2017-11-08 14:35:55'),
	(213, '家居', 2, '2017-11-08 14:35:55'),
	(214, '家电', 2, '2017-11-08 14:35:55'),
	(215, '美食', 2, '2017-11-08 14:35:55'),
	(216, '建材', 2, '2017-11-08 14:35:55'),
	(217, '房产', 2, '2017-11-08 14:35:55'),
	(218, '体育', 2, '2017-11-08 14:35:55'),
	(219, '综合', 2, '2017-11-08 14:35:55'),
	(220, '电商', 2, '2017-11-08 14:35:55'),
	(221, '文学', 2, '2017-11-08 14:35:55'),
	(222, '动漫', 2, '2017-11-08 14:35:55'),
	(223, '游戏', 2, '2017-11-08 14:35:55'),
	(224, '职场', 2, '2017-11-08 14:35:55'),
	(225, '营销', 2, '2017-11-08 14:35:55'),
	(226, '科技数码', 2, '2017-11-08 14:35:55'),
	(227, '搞笑', 2, '2017-11-08 14:35:55'),
	(228, '女性', 2, '2017-11-08 14:35:55'),
	(229, '旅游', 2, '2017-11-08 14:35:55'),
	(230, '影视', 2, '2017-11-08 14:35:55'),
	(231, '医疗健康', 2, '2017-11-08 14:35:55'),
	(232, '娱乐', 2, '2017-11-08 14:35:55'),
	(233, '母婴育儿', 2, '2017-11-08 14:35:55'),
	(234, '摄影', 2, '2017-11-08 14:35:55'),
	(235, '创意', 2, '2017-11-08 14:35:55'),
	(236, '生活', 2, '2017-11-08 14:35:55'),
	(237, '时尚', 2, '2017-11-08 14:35:55'),
	(238, '新闻资讯', 2, '2017-11-08 14:35:55'),
	(239, '互联网', 2, '2017-11-08 14:35:55'),
	(240, 'IT', 2, '2017-11-08 14:35:55'),
	(241, '奢侈品', 2, '2017-11-08 14:35:55'),
	(242, '婚纱', 2, '2017-11-08 14:35:55'),
	(243, '公益', 2, '2017-11-08 14:35:55'),
	(244, '宠物', 2, '2017-11-08 14:35:55'),
	(245, '语录', 2, '2017-11-08 14:35:55'),
	(246, '英语', 2, '2017-11-08 14:35:55'),
	(247, '购物', 2, '2017-11-08 14:35:55'),
	(248, '音乐', 2, '2017-11-08 14:35:55'),
	(249, '百科', 2, '2017-11-08 14:35:55'),
	(250, '情感', 2, '2017-11-08 14:35:55'),
	(251, '星座', 2, '2017-11-08 14:35:55'),
	(301, '段子手', 3, '2017-11-08 14:38:46'),
	(302, '地区号', 3, '2017-11-08 14:38:46'),
	(303, '网络红人', 3, '2017-11-08 14:38:46'),
	(304, '汽车', 3, '2017-11-08 14:38:46'),
	(305, '主持人', 3, '2017-11-08 14:38:46'),
	(306, '歌手', 3, '2017-11-08 14:38:46'),
	(307, '记者', 3, '2017-11-08 14:38:46'),
	(308, '评论人', 3, '2017-11-08 14:38:46'),
	(309, '模特', 3, '2017-11-08 14:38:46'),
	(310, '明星', 3, '2017-11-08 14:38:46'),
	(311, '建材', 3, '2017-11-08 14:38:46'),
	(312, '房产', 3, '2017-11-08 14:38:46'),
	(313, '财经', 3, '2017-11-08 14:38:46'),
	(314, '百科', 3, '2017-11-08 14:38:46'),
	(315, '体育', 3, '2017-11-08 14:38:46'),
	(316, '校园', 3, '2017-11-08 14:38:46'),
	(317, '教育', 3, '2017-11-08 14:38:46'),
	(318, '家电', 3, '2017-11-08 14:38:46'),
	(319, '家居', 3, '2017-11-08 14:38:46'),
	(320, '美食', 3, '2017-11-08 14:38:46'),
	(321, '政府', 3, '2017-11-08 14:38:46'),
	(322, '情感', 3, '2017-11-08 14:38:46'),
	(323, '星座', 3, '2017-11-08 14:38:46'),
	(324, '微商', 3, '2017-11-08 14:38:46'),
	(325, '影视', 3, '2017-11-08 14:38:46'),
	(326, '美容护肤', 3, '2017-11-08 14:38:46'),
	(327, '搞笑', 3, '2017-11-08 14:38:46'),
	(328, '健康', 3, '2017-11-08 14:38:46'),
	(329, '母婴育儿', 3, '2017-11-08 14:38:46'),
	(330, '娱乐', 3, '2017-11-08 14:38:46'),
	(331, '摄影', 3, '2017-11-08 14:38:46'),
	(332, '旅游', 3, '2017-11-08 14:38:46'),
	(333, '女性', 3, '2017-11-08 14:38:46'),
	(334, '时尚', 3, '2017-11-08 14:38:46'),
	(335, '营销', 3, '2017-11-08 14:38:46'),
	(336, '动漫', 3, '2017-11-08 14:38:46'),
	(337, '游戏', 3, '2017-11-08 14:38:46'),
	(338, '职场', 3, '2017-11-08 14:38:46'),
	(339, '生活', 3, '2017-11-08 14:38:46'),
	(340, '创意', 3, '2017-11-08 14:38:46'),
	(341, '电商', 3, '2017-11-08 14:38:46'),
	(342, '新闻资讯', 3, '2017-11-08 14:38:46'),
	(343, '科技数码', 3, '2017-11-08 14:38:46'),
	(344, '互联网', 3, '2017-11-08 14:38:46'),
	(345, 'IT', 3, '2017-11-08 14:38:46'),
	(346, '奢侈品', 3, '2017-11-08 14:38:46'),
	(347, '婚纱', 3, '2017-11-08 14:38:46'),
	(348, '公益', 3, '2017-11-08 14:38:46'),
	(349, '宠物', 3, '2017-11-08 14:38:46'),
	(350, '媒体', 3, '2017-11-08 14:38:46'),
	(351, '语录', 3, '2017-11-08 14:38:46'),
	(352, '英语', 3, '2017-11-08 14:38:46'),
	(353, '购物', 3, '2017-11-08 14:38:46'),
	(354, '音乐', 3, '2017-11-08 14:38:46'),
	(401, '化妆品微商', 4, '2017-11-08 14:40:46'),
	(402, '美容护肤达人', 4, '2017-11-08 14:40:46'),
	(403, '网络营销', 4, '2017-11-08 14:40:46'),
	(404, '微商', 4, '2017-11-08 14:40:46'),
	(405, '网络红人', 4, '2017-11-08 14:40:46'),
	(406, '白领', 4, '2017-11-08 14:40:46'),
	(407, '学生', 4, '2017-11-08 14:40:46'),
	(408, '电商', 4, '2017-11-08 14:40:46'),
	(409, '企业家', 4, '2017-11-08 14:40:46'),
	(410, '自媒体', 4, '2017-11-08 14:40:46'),
	(411, '自由职业', 4, '2017-11-08 14:40:46'),
	(412, '导游', 4, '2017-11-08 14:40:46'),
	(413, '模特', 4, '2017-11-08 14:40:46'),
	(414, '个体商户', 4, '2017-11-08 14:40:46'),
	(415, '房产', 4, '2017-11-08 14:40:46'),
	(416, '其它职业', 4, '2017-11-08 14:40:46'),
	(501, '汽车', 5, '2017-11-08 14:42:10'),
	(502, '搞笑', 5, '2017-11-08 14:42:10'),
	(503, '时尚', 5, '2017-11-08 14:42:10'),
	(504, '女性', 5, '2017-11-08 14:42:10'),
	(505, '健康/养生/医疗', 5, '2017-11-08 14:42:10'),
	(506, '游戏', 5, '2017-11-08 14:42:10'),
	(507, '旅游/摄影', 5, '2017-11-08 14:42:10'),
	(508, '娱乐', 5, '2017-11-08 14:42:10'),
	(509, '地方', 5, '2017-11-08 14:42:10'),
	(510, '政府', 5, '2017-11-08 14:42:10'),
	(511, '新闻/资讯', 5, '2017-11-08 14:42:10'),
	(512, '财经', 5, '2017-11-08 14:42:10'),
	(513, '校园', 5, '2017-11-08 14:42:10'),
	(514, '教育', 5, '2017-11-08 14:42:10'),
	(515, '家居', 5, '2017-11-08 14:42:10'),
	(516, 'IT/科技', 5, '2017-11-08 14:42:10'),
	(517, '家电', 5, '2017-11-08 14:42:10'),
	(518, '美食', 5, '2017-11-08 14:42:10'),
	(519, '育儿', 5, '2017-11-08 14:42:10'),
	(520, '建材', 5, '2017-11-08 14:42:10'),
	(521, '动漫', 5, '2017-11-08 14:42:10'),
	(522, '房产', 5, '2017-11-08 14:42:10'),
	(523, '体育', 5, '2017-11-08 14:42:10'),
	(524, '宠物', 5, '2017-11-08 14:42:10'),
	(525, '综合', 5, '2017-11-08 14:42:10'),
	(526, '理财', 5, '2017-11-08 14:42:10'),
	(527, '杂谈', 5, '2017-11-08 14:42:10'),
	(528, '草根', 5, '2017-11-08 14:42:10'),
	(529, '奢侈品', 5, '2017-11-08 14:42:10'),
	(530, '婚纱', 5, '2017-11-08 14:42:10'),
	(531, '公益', 5, '2017-11-08 14:42:10'),
	(532, '宠物', 5, '2017-11-08 14:42:10'),
	(533, '媒体', 5, '2017-11-08 14:42:10'),
	(534, '语录', 5, '2017-11-08 14:42:10'),
	(535, '母婴', 5, '2017-11-08 14:42:10'),
	(536, '创意/生活', 5, '2017-11-08 14:42:10'),
	(537, '英语', 5, '2017-11-08 14:42:10'),
	(538, '购物', 5, '2017-11-08 14:42:10'),
	(539, '音乐', 5, '2017-11-08 14:42:10'),
	(540, '百科', 5, '2017-11-08 14:42:10'),
	(541, '情感', 5, '2017-11-08 14:42:10'),
	(542, '星座', 5, '2017-11-08 14:42:10'),
	(543, '模特', 5, '2017-11-08 14:42:10'),
	(601, '汽车', 6, '2017-11-08 14:43:31'),
	(602, '科技', 6, '2017-11-08 14:43:31'),
	(603, '时尚', 6, '2017-11-08 14:43:31'),
	(604, '生活', 6, '2017-11-08 14:43:31'),
	(605, '女性', 6, '2017-11-08 14:43:31'),
	(606, '医疗', 6, '2017-11-08 14:43:31'),
	(607, '游戏', 6, '2017-11-08 14:43:31'),
	(608, '旅游', 6, '2017-11-08 14:43:31'),
	(609, '娱乐', 6, '2017-11-08 14:43:31'),
	(610, '地方', 6, '2017-11-08 14:43:31'),
	(611, '政府', 6, '2017-11-08 14:43:31'),
	(612, '新闻', 6, '2017-11-08 14:43:31'),
	(613, '资讯', 6, '2017-11-08 14:43:31'),
	(614, '财经', 6, '2017-11-08 14:43:31'),
	(615, '校园', 6, '2017-11-08 14:43:31'),
	(616, '教育', 6, '2017-11-08 14:43:31'),
	(617, '家居', 6, '2017-11-08 14:43:31'),
	(618, '健康', 6, '2017-11-08 14:43:31'),
	(619, 'IT', 6, '2017-11-08 14:43:31'),
	(620, '家电', 6, '2017-11-08 14:43:31'),
	(621, '美食', 6, '2017-11-08 14:43:31'),
	(622, '育儿', 6, '2017-11-08 14:43:31'),
	(623, '建材', 6, '2017-11-08 14:43:31'),
	(624, '动漫', 6, '2017-11-08 14:43:31'),
	(625, '房产', 6, '2017-11-08 14:43:31'),
	(626, '体育', 6, '2017-11-08 14:43:31'),
	(627, '音乐', 6, '2017-11-08 14:43:31'),
	(628, '搞笑', 6, '2017-11-08 14:43:31'),
	(629, '原创', 6, '2017-11-08 14:43:31'),
	(7, '直播资源', 0, '2017-11-08 14:44:15'),
	(8, '头条资源', 0, '2017-11-08 14:45:04'),
	(701, '段子手', 7, '2017-11-08 14:46:03'),
	(702, '地区号', 7, '2017-11-08 14:46:03'),
	(703, '美容护肤达人', 7, '2017-11-08 14:46:03'),
	(704, 'IT/互联网名人', 7, '2017-11-08 14:46:03'),
	(705, '新闻/资讯', 7, '2017-11-08 14:46:03'),
	(706, '记者', 7, '2017-11-08 14:46:03'),
	(707, '主持人', 7, '2017-11-08 14:46:03'),
	(708, '网络红人', 7, '2017-11-08 14:46:03'),
	(709, '旅游/摄影', 7, '2017-11-08 14:46:03'),
	(710, '评论人', 7, '2017-11-08 14:46:03'),
	(711, '歌手', 7, '2017-11-08 14:46:03'),
	(712, '明星', 7, '2017-11-08 14:46:03'),
	(713, '娱乐/影视', 7, '2017-11-08 14:46:03'),
	(714, '创意/生活', 7, '2017-11-08 14:46:03'),
	(715, '搞笑/笑话', 7, '2017-11-08 14:46:03'),
	(716, '时尚/女性', 7, '2017-11-08 14:46:03'),
	(717, '财经', 7, '2017-11-08 14:46:03'),
	(718, '汽车', 7, '2017-11-08 14:46:03'),
	(719, '科技/数码', 7, '2017-11-08 14:46:03'),
	(720, '校园', 7, '2017-11-08 14:46:03'),
	(721, '母婴/育儿', 7, '2017-11-08 14:46:03'),
	(722, '教育', 7, '2017-11-08 14:46:03'),
	(723, '医疗/健康', 7, '2017-11-08 14:46:03'),
	(724, '家居', 7, '2017-11-08 14:46:03'),
	(725, '游戏/动漫', 7, '2017-11-08 14:46:03'),
	(726, '家电', 7, '2017-11-08 14:46:03'),
	(727, '美食', 7, '2017-11-08 14:46:03'),
	(728, '建材', 7, '2017-11-08 14:46:03'),
	(729, '房产', 7, '2017-11-08 14:46:03'),
	(730, '体育', 7, '2017-11-08 14:46:03'),
	(731, '时尚', 7, '2017-11-08 14:46:03'),
	(732, '综合', 7, '2017-11-08 14:46:03'),
	(733, '电商', 7, '2017-11-08 14:46:03'),
	(734, '文学', 7, '2017-11-08 14:46:03'),
	(735, '奢侈品', 7, '2017-11-08 14:46:03'),
	(736, '婚纱', 7, '2017-11-08 14:46:03'),
	(737, '公益', 7, '2017-11-08 14:46:03'),
	(738, '宠物', 7, '2017-11-08 14:46:03'),
	(739, '语录', 7, '2017-11-08 14:46:03'),
	(740, '英语', 7, '2017-11-08 14:46:03'),
	(741, '购物', 7, '2017-11-08 14:46:03'),
	(742, '音乐', 7, '2017-11-08 14:46:03'),
	(743, '百科', 7, '2017-11-08 14:46:03'),
	(744, '情感', 7, '2017-11-08 14:46:03'),
	(745, '星座', 7, '2017-11-08 14:46:03'),
	(801, '汽车', 8, '2017-11-08 14:47:06'),
	(802, '时尚', 8, '2017-11-08 14:47:06'),
	(803, 'IT', 8, '2017-11-08 14:47:06'),
	(804, '娱乐', 8, '2017-11-08 14:47:06'),
	(805, '旅游', 8, '2017-11-08 14:47:06'),
	(806, '女性', 8, '2017-11-08 14:47:06'),
	(807, '健康', 8, '2017-11-08 14:47:06'),
	(808, '亲子', 8, '2017-11-08 14:47:06'),
	(809, '摄影', 8, '2017-11-08 14:47:06'),
	(810, '游戏', 8, '2017-11-08 14:47:06'),
	(811, '房产', 8, '2017-11-08 14:47:06'),
	(812, '体育', 8, '2017-11-08 14:47:06'),
	(813, '财经', 8, '2017-11-08 14:47:06'),
	(814, '家居', 8, '2017-11-08 14:47:06'),
	(815, '杂谈', 8, '2017-11-08 14:47:06'),
	(816, '校园', 8, '2017-11-08 14:47:06'),
	(817, '全站', 8, '2017-11-08 14:47:06'),
	(818, '动漫', 8, '2017-11-08 14:47:06'),
	(819, '地区', 8, '2017-11-08 14:47:06'),
	(820, '美食', 8, '2017-11-08 14:47:06'),
	(821, '搞笑', 8, '2017-11-08 14:47:06'),
	(822, '新闻资讯', 8, '2017-11-08 14:47:06'),
	(823, '电商', 8, '2017-11-08 14:47:06'),
	(824, '互联网', 8, '2017-11-08 14:47:06'),
	(825, '科技数码', 8, '2017-11-08 14:47:06'),
	(826, '音乐', 8, '2017-11-08 14:47:06'),
	(827, '文学', 8, '2017-11-08 14:47:06'),
	(828, '生活', 8, '2017-11-08 14:47:06'),
	(829, '宠物', 8, '2017-11-08 14:47:06'),
	(830, '购物', 8, '2017-11-08 14:47:06'),
	(831, '情感', 8, '2017-11-08 14:47:06'),
	(832, '美容护肤', 8, '2017-11-08 14:47:06'),
	(833, '综合', 8, '2017-11-08 14:47:06'),
	(834, '手机', 8, '2017-11-08 14:47:06'),
	(835, '教育', 8, '2017-11-08 14:47:06');
	
CREATE TABLE `xybt_channel_in_cat` (
	`channel_id` INT(11) NOT NULL DEFAULT '0',
	`cat_id` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`channel_id`, `cat_id`)
)
COMMENT='渠道-分类'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
