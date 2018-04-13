/*
Navicat MySQL Data Transfer

Source Server         : 192.168.10.10
Source Server Version : 50717
Source Host           : 192.168.10.10:3306
Source Database       : rbac

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2018-04-13 20:20:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名称',
  `description` varchar(200) DEFAULT '' COMMENT '角色描述',
  `menu_list` varchar(255) NOT NULL DEFAULT '' COMMENT '角色权限列表',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of admin_role
-- ----------------------------
INSERT INTO `admin_role` VALUES ('1', '超级管理员', '超级管理权限', '1,2,3,4,5,6,7,8', '2018-03-08 20:57:27', '2018-03-12 16:14:46');
INSERT INTO `admin_role` VALUES ('2', '管理员', '低于超管的权限', '1,2,5,6,8', '2018-03-08 20:57:27', '2018-03-12 16:15:10');
INSERT INTO `admin_role` VALUES ('3', 'image_user', '管理图片(只能看到图片菜单按钮)', '3,4', '2018-03-09 10:05:00', '2018-03-15 14:37:18');

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `last_login_time` timestamp NULL DEFAULT NULL COMMENT '上次登录时间',
  `role_id` smallint(4) unsigned NOT NULL DEFAULT '1' COMMENT '角色id',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态 0 正常  1删除',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_index` (`username`) USING BTREE,
  KEY `role_id_index` (`role_id`) USING BTREE,
  KEY `created_at_index` (`created_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('1', 'admin', '$2y$10$IdGgframF7yrMzduSYIiKOp5yajti7toFWO5/ljas/Y4dQmjsL4tC', null, '2', '0', '2018-03-08 17:50:16', '2018-03-12 19:52:01');
INSERT INTO `admin_user` VALUES ('2', 'admin_role', '$2y$10$12fZw02gR79G9.UU/KcLWOZSSgzM9pboKTL6UE6tcgr6JVpm6H0MO', null, '2', '0', '2018-03-08 17:55:45', '2018-03-12 19:52:38');
INSERT INTO `admin_user` VALUES ('3', 'admin_test', '$2y$10$12fZw02gR79G9.UU/KcLWOZSSgzM9pboKTL6UE6tcgr6JVpm6H0MO', '2018-03-10 22:25:57', '4', '0', '2018-03-08 17:55:45', '2018-03-13 11:29:53');
INSERT INTO `admin_user` VALUES ('4', 'super_admin', '$2y$10$jhBW24HGsSlQCsP0oIyNQO5jM4KjAGXlXhRRWJ7HCLq37eocY0lUi', null, '1', '0', '2018-03-09 10:16:47', '2018-03-12 13:21:50');

-- ----------------------------
-- Table structure for common_conf
-- ----------------------------
DROP TABLE IF EXISTS `common_conf`;
CREATE TABLE `common_conf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `column` varchar(30) DEFAULT NULL COMMENT '字段',
  `desc` varchar(30) DEFAULT '' COMMENT '字段描述',
  `value` varchar(100) DEFAULT '' COMMENT '字段对应值',
  `status` tinyint(4) DEFAULT '1' COMMENT '是否正常',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of common_conf
-- ----------------------------
INSERT INTO `common_conf` VALUES ('1', 'page_limit', '每页显示的条数', '10', '1', '2018-04-13 14:10:30', '2018-04-13 10:31:58');
INSERT INTO `common_conf` VALUES ('8', '44', '44', '44', '2', '2018-04-13 16:10:07', '2018-04-13 16:12:37');
INSERT INTO `common_conf` VALUES ('2', 'super_role', 'super_role', '1', '1', '2018-04-13 15:00:13', '2018-04-13 15:00:13');
INSERT INTO `common_conf` VALUES ('6', 'redis_time', '配置信息缓存时间（字段名称不要改）', '7200', '1', '2018-04-13 15:57:24', '2018-04-13 10:21:49');
INSERT INTO `common_conf` VALUES ('4', 'super_role', 'super_role', '1', '2', '2018-04-13 15:01:16', '2018-04-13 15:47:26');
INSERT INTO `common_conf` VALUES ('5', '1', '1', '1', '1', '2018-04-13 15:52:16', '2018-04-13 15:52:16');
INSERT INTO `common_conf` VALUES ('3', 'super_role', 'super_role', '1', '2', '2018-04-13 15:00:52', '2018-04-13 09:23:36');
INSERT INTO `common_conf` VALUES ('7', '333', '33', '333', '2', '2018-04-13 15:57:58', '2018-04-13 09:22:01');
INSERT INTO `common_conf` VALUES ('9', '3333', '333', '333', '1', '2018-04-13 09:23:54', '2018-04-13 09:23:54');

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '导航名称',
  `id_name` varchar(100) DEFAULT '' COMMENT 'id名',
  `menu_icon` varchar(50) DEFAULT NULL COMMENT '图标',
  `menu_url` varchar(255) DEFAULT NULL COMMENT '菜单url',
  `p_id` int(11) DEFAULT NULL COMMENT '导航父id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(11) DEFAULT '1' COMMENT '状态(1:正常,2:停用)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '资讯管理', 'menu-article', '&#xe616;', null, '0', '1', '1', null, null);
INSERT INTO `menu` VALUES ('2', '资讯管理', '', null, 'admin/NewsManage/index', '1', '1', '1', null, null);
INSERT INTO `menu` VALUES ('3', '图片管理', 'menu-picture', '&#xe613;', null, '0', '2', '1', null, null);
INSERT INTO `menu` VALUES ('4', '图片管理', '', null, 'admin/ImageManage/index', '3', '1', '1', null, null);
INSERT INTO `menu` VALUES ('5', '管理员管理', 'menu-admin', '&#xe62d;', null, '0', '3', '1', null, null);
INSERT INTO `menu` VALUES ('6', '角色管理', '', null, 'admin/RoleManage/index', '5', '1', '1', null, null);
INSERT INTO `menu` VALUES ('7', '权限管理', '', null, 'admin/PermissionManage/index', '5', '2', '1', null, null);
INSERT INTO `menu` VALUES ('8', '管理员列表', '', null, 'admin/UserManage/index', '5', '3', '1', null, null);
INSERT INTO `menu` VALUES ('9', '系统管理', 'menu-system', '&#xe62e;', null, '0', '4', '1', null, null);
INSERT INTO `menu` VALUES ('10', '系统设置', '', null, 'admin/SystemManage/index', '9', '1', '1', null, null);
INSERT INTO `menu` VALUES ('11', '菜单管理', '', '', 'admin/MenuManage/index', '9', '2', '2', null, '2018-04-13 12:14:54');
INSERT INTO `menu` VALUES ('12', '测试', '111', 'ceshi 1111ss', '', '0', '0', '1', null, '2018-04-13 12:01:10');
INSERT INTO `menu` VALUES ('13', '测试1', '测试1', '测试1', '', '12', '5', '1', null, '2018-04-13 12:09:36');
