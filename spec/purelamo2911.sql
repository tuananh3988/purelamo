/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : purelamo

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-11-29 00:20:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for devices
-- ----------------------------
DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '1: ios, 2: aos',
  `device_token` varchar(255) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of devices
-- ----------------------------
INSERT INTO `devices` VALUES ('1', '123', '2', 'eQWpexZYvOE:APA91bHiyiIaa9FtkQd26xetqKII-omWnvzUanu1X-8b1V8mHlZoCr34qcpgHe4YZuiX7BYSJ7ugX4EmfbhliiQv12L8eJmHvI99W14DJB3Mm-eQJj2-UPPyYrYFMzvfLbbgITwmq2Yu', null, null);

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of notification
-- ----------------------------
INSERT INTO `notification` VALUES ('1', 'ffff', 'fffff', '1', '0', null, null, null, null, null);
INSERT INTO `notification` VALUES ('3', 'push cai choi', 'co nhan dc ko?', '0', '1', '2016-05-05 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('5', 'ddd', 'dd', '0', '1', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('6', 'ddd', 'dd', '0', '1', '2016-09-13 00:00:00', null, null, null, '2016-09-29 19:20:17');
INSERT INTO `notification` VALUES ('7', 'ddd', 'dd', '0', '0', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('8', 'ddd', 'dd', '0', '0', '2016-09-13 00:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('9', 'ddddd', 'dddd', '0', '0', '2016-09-28 17:00:00', null, null, null, null);
INSERT INTO `notification` VALUES ('10', 'dsdd', 'ddd', '0', '0', '2016-09-30 00:00:00', null, null, '2016-09-29 18:32:14', '2016-09-29 18:33:09');

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
