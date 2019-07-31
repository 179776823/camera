-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2019-07-05 05:10:33
-- 服务器版本： 5.7.26
-- PHP 版本： 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `camera`
--

-- --------------------------------------------------------

--
-- 表的结构 `device`
--

DROP TABLE IF EXISTS `device`;
CREATE TABLE IF NOT EXISTS `device` (
  `deviceId` int(30) NOT NULL AUTO_INCREMENT,
  `deviceCode` varchar(64) NOT NULL COMMENT '设备编号 填的编号',
  `deviceNumber` varchar(65) DEFAULT NULL COMMENT '自动生成的码',
  `deviceType` tinyint(1) DEFAULT NULL COMMENT '1-人脸抓拍 2-视频安防监控 3-USB防插拔 4-出入口控制 5-停车库 6-入侵和紧急报警 7-实时电子巡检 8-状态感知检测 9-状态采集检测',
  `deviceObjId` int(30) DEFAULT NULL COMMENT '设备对应的设备类型所属的表主键Id  比如 出入口控制对应entrance表的主键   停车库对应parking表的主键',
  `villageId` int(30) DEFAULT NULL COMMENT '设备对应的小区',
  `villageCode` varchar(64) DEFAULT NULL COMMENT '设备对应的小区',
  `lon` double(11,6) DEFAULT NULL,
  `lat` double(11,6) DEFAULT NULL,
  `alt` double(11,2) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `state` tinyint(1) DEFAULT '1' COMMENT '设备状态 1-可用 2-禁用',
  `floor` varchar(64) DEFAULT NULL,
  `gisType` tinyint(2) DEFAULT NULL,
  `gisArea` varchar(1024) DEFAULT NULL,
  `isHidden` tinyint(4) DEFAULT '0',
  `isCloudDevice` tinyint(1) DEFAULT '0',
  `isRegister` tinyint(1) DEFAULT '0' COMMENT '0 未在公安注册 1 在公安注册',
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`deviceId`),
  KEY `deviceCode` (`deviceCode`) USING BTREE,
  KEY `villageId` (`villageId`) USING BTREE,
  KEY `villageCode` (`villageCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='设备表';

--
-- 转存表中的数据 `device`
--

INSERT INTO `device` (`deviceId`, `deviceCode`, `deviceNumber`, `deviceType`, `deviceObjId`, `villageId`, `villageCode`, `lon`, `lat`, `alt`, `address`, `note`, `state`, `floor`, `gisType`, `gisArea`, `isHidden`, `isCloudDevice`, `isRegister`, `createTime`, `updateTime`) VALUES
(1, '01234567890123456789', '1', 1, 22, 11, '11', 3.114440, 120.114400, 2.50, 'cvdevedv', 'ccde', 1, NULL, NULL, NULL, 0, 0, 1, NULL, '2019-05-31 18:10:27'),
(2, '1101110', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, 0, 1, NULL, '2019-05-31 15:34:28'),
(3, 'm0000001', '1', 1, 2, 10, '10', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, 0, 1, NULL, '2019-06-27 21:42:23');

-- --------------------------------------------------------

--
-- 表的结构 `deviceserver`
--

DROP TABLE IF EXISTS `deviceserver`;
CREATE TABLE IF NOT EXISTS `deviceserver` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) DEFAULT NULL COMMENT '服务器所在地址',
  `server_ip` varchar(64) DEFAULT NULL COMMENT '小区服务器ip',
  `server_port` varchar(64) DEFAULT NULL COMMENT '小区服务器端口',
  `data_ip` varchar(64) DEFAULT NULL COMMENT '数据服务器ip',
  `data_port` varchar(64) DEFAULT NULL COMMENT '数据服务器端口',
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='服务器配置信息';


-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(60) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `salt` varchar(32) NOT NULL COMMENT '盐值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `salt`) VALUES
(1, 'admin', '3fa925675347ce26fc7e55c37b07a9a4', '1476');

-- --------------------------------------------------------

--
-- 表的结构 `user_client`
--

DROP TABLE IF EXISTS `user_client`;
CREATE TABLE IF NOT EXISTS `user_client` (
  `uid` int(30) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `client_id` varchar(200) DEFAULT NULL COMMENT '该用户的客户端id',
  `createTime` datetime NOT NULL,
  `updateTime` datetime NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


-- ----------------------------
--  Table structure for `camera`
-- ----------------------------
DROP TABLE IF EXISTS `camera`;
CREATE TABLE `camera` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `deviceCode` varchar(64) NOT NULL COMMENT '设备编号 填的编号',
  `IP` varchar(64) DEFAULT NULL COMMENT '摄像机IP',
  `port` varchar(64) DEFAULT NULL COMMENT '摄像机端口', 
  `lon` double(11,6) DEFAULT NULL,
  `lat` double(11,6) DEFAULT NULL,
  `alt` double(11,2) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `gisType` tinyint(2) DEFAULT NULL,
  `direction` tinyint(2) DEFAULT '0' COMMENT '安装方向0-入口 1-出口',
  `gisArea` varchar(1024) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `deviceCode` (`deviceCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='摄像机管理表';


DROP TABLE IF EXISTS `faceEvent`;
CREATE TABLE `faceEvent` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `deviceId` varchar(255) DEFAULT NULL COMMENT '设备id',
  `deviceCode` varchar(64) DEFAULT NULL COMMENT '设备编号',
  `channel` int(10) DEFAULT NULL COMMENT '设备通道  0为主机/控制器  >0为摄像机通道',
  `credentialType` int(11) DEFAULT NULL COMMENT '证件类型 1-身份证、2-护照、3-港澳通行证 4-台湾居民来往大陆通行证',
  `credentialNo` varchar(64) DEFAULT NULL COMMENT '证件号码',
  `eventCode` int(11) DEFAULT NULL,
  `eventType` int(11) DEFAULT NULL COMMENT '事件类型',
  `eventTime` datetime DEFAULT NULL COMMENT '发生时间',
  `dataTime` datetime DEFAULT NULL COMMENT '数据接收时间',
  `faceContrast` int(11) DEFAULT NULL COMMENT '人脸对比结果',
  `personCode` varchar(64) DEFAULT NULL COMMENT '人员类型',
  `featureInfo` int(11) DEFAULT NULL COMMENT '特征信息',
  `note` varchar(256) DEFAULT NULL COMMENT '消息备注',
  `lon` double DEFAULT NULL COMMENT '经度',
  `lat` double DEFAULT NULL COMMENT '纬度',
  `alt` double DEFAULT NULL COMMENT '高度',
  `floor` varchar(64) DEFAULT NULL COMMENT '楼层',
  `gisType` int(11) DEFAULT NULL,
  `SourceID` varchar(64) DEFAULT NULL,
  `FaceID` varchar(64) DEFAULT NULL,
  `count` int(11) DEFAULT NULL COMMENT '人脸数量',
  `target` varchar(1024) DEFAULT NULL COMMENT '目标坐标 json',
  `groupId` varchar(255) DEFAULT NULL COMMENT '图片集标签',
  `similarity` int(10) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `deviceId` (`deviceId`) USING BTREE,
  KEY `deviceCode` (`deviceCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='人脸抓拍系统事件信息表';


DROP TABLE IF EXISTS `faceImage`;
CREATE TABLE `faceImage` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `faceEventId` varchar(255) DEFAULT NULL COMMENT '事件id',
  `deviceCode` varchar(64) DEFAULT NULL COMMENT '设备编号',
  `eventSort` int(10) DEFAULT NULL COMMENT '',
  `triggerTime` datetime DEFAULT NULL COMMENT '触发时间',
  `fileFormat` varchar(64) DEFAULT NULL COMMENT '图片格式',
  `imageID` varchar(64) DEFAULT NULL,
  `sub` tinyint(4) DEFAULT NULL COMMENT '1 子图片 0 全景图',
  `height` varchar(64) DEFAULT NULL COMMENT '图片高度',
  `width` varchar(64) DEFAULT NULL COMMENT '图片宽度',
  `eventPicUrl` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faceEventId` (`faceEventId`) USING BTREE,
  KEY `deviceCode` (`deviceCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='人脸抓拍事件关联图片表';

DROP TABLE IF EXISTS `camerakeep`;
CREATE TABLE `camerakeep` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `deviceCode` varchar(64) DEFAULT NULL COMMENT '设备编号',
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deviceCode` (`deviceCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='摄像机注册保活表';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
