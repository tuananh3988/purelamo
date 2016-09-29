/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : purelamo

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-09-29 18:24:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for notification
-- ----------------------------
DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `delete_flag` tinyint(4) DEFAULT '0',
  `reserve_date` datetime DEFAULT NULL,
  `send_begin_date` datetime DEFAULT NULL,
  `send_end_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of notification
-- ----------------------------
INSERT INTO `notification` VALUES ('1', 'ffff', 'fffff', '1', '0', null, null, null, null, null);
INSERT INTO `notification` VALUES ('3', 'push cai choi', 'co nhan dc ko?', '0', '1', '2016-05-05 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('5', 'ddd', 'dd', '0', '1', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('6', 'ddd', 'dd', '0', '0', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('7', 'ddd', 'dd', '0', '0', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('8', 'ddd', 'dd', '0', '0', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('9', 'ddddd', 'dddd', '0', '0', '2016-09-28 17:00:00', null, null, null, null);

-- ----------------------------
-- Table structure for staffs
-- ----------------------------
DROP TABLE IF EXISTS `staffs`;
CREATE TABLE `staffs` (
  `id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth_key` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of staffs
-- ----------------------------
INSERT INTO `staffs` VALUES ('1', 'john', 'John Howard', '$2y$13$XuHdQKk83B.mEiSLfPjUaOnVIW.l.oEhGgr0OEvlamtPyOYF7qpf2', null);
INSERT INTO `staffs` VALUES ('2', 'anhctasdfasdfasdf', 'anhct admin', '$2y$13$oZWGOKCtmkYFMA5uJ295muKDHPT0jpqESYjNKaIA77oa4Z3zKcTEO', null);
