/*
Navicat SQLite Data Transfer

Source Server         : PushUI.mbis_server
Source Server Version : 30808
Source Host           : :0

Target Server Type    : SQLite
Target Server Version : 30808
File Encoding         : 65001

Date: 2017-07-21 12:17:05
*/

PRAGMA foreign_keys = OFF;

-- ----------------------------
-- Table structure for MBIS_Server_Admin
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Admin";
CREATE TABLE "MBIS_Server_Admin" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"UserName"  VARCHAR,
"Password"  VARCHAR,
"RoleID"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_Admin
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Admin" VALUES (1, 'admin', 'admin', 1);

-- ----------------------------
-- Table structure for MBIS_Server_Ad_AccessData
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_AccessData";
CREATE TABLE "MBIS_Server_Ad_AccessData" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"LastAccessTime"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_Ad_AccessData
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_AccessData" VALUES (1, 1469688914);

-- ----------------------------
-- Table structure for MBIS_Server_Ad_AdFileType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_AdFileType";
CREATE TABLE "MBIS_Server_Ad_AdFileType" (
"FileTypeID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"Value"  VARCHAR,
"RelativeAppendix"  VARCHAR DEFAULT 0
);

-- ----------------------------
-- Records of MBIS_Server_Ad_AdFileType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_AdFileType" VALUES (0, '未定义', null);
INSERT INTO "main"."MBIS_Server_Ad_AdFileType" VALUES (1, 'video', 'ts,flv,mp4,swf,rmvb,wmv,avi,rm,vnd.dlna.mpeg-tts');
INSERT INTO "main"."MBIS_Server_Ad_AdFileType" VALUES (2, 'picture', 'jpg,png,jpeg,gif,bmp');
INSERT INTO "main"."MBIS_Server_Ad_AdFileType" VALUES (3, 'text', null);

-- ----------------------------
-- Table structure for MBIS_Server_Ad_AdType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_AdType";
CREATE TABLE "MBIS_Server_Ad_AdType" (
"AdTypeID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"Value"  
);

-- ----------------------------
-- Records of MBIS_Server_Ad_AdType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_AdType" VALUES (0, '未定义');
INSERT INTO "main"."MBIS_Server_Ad_AdType" VALUES (1, '片头广告');
INSERT INTO "main"."MBIS_Server_Ad_AdType" VALUES (2, '挂角广告');
INSERT INTO "main"."MBIS_Server_Ad_AdType" VALUES (3, '暂停广告');
INSERT INTO "main"."MBIS_Server_Ad_AdType" VALUES (4, '滚动字幕广告');

-- ----------------------------
-- Table structure for MBIS_Server_Ad_IP
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_IP";
CREATE TABLE "MBIS_Server_Ad_IP" (
"IPID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"Name"  VARCHAR,
"IP"  VARCHAR NOT NULL,
"Port"  INTEGER NOT NULL DEFAULT 0,
"Location"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_IP
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_IP" VALUES (4, '本地服务器', '127.0.0.1', 15011, '北京');

-- ----------------------------
-- Table structure for MBIS_Server_Ad_Log
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_Log";
CREATE TABLE "MBIS_Server_Ad_Log" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"ContentID"  VARCHAR NOT NULL,
"Status"  INTEGER NOT NULL DEFAULT 0,
"AdTypeID"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_Ad_Log
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_Log" VALUES (22, '32EABE4CE88CD10107BA0E78854A462E', 100, 1);
INSERT INTO "main"."MBIS_Server_Ad_Log" VALUES (23, '1A39FAFCD06990A3EDE1194FFB5F22AE', 85, 1);
INSERT INTO "main"."MBIS_Server_Ad_Log" VALUES (24, '98EF95A6F1487B5ECACD4618F3CCEA88', 100, 3);

-- ----------------------------
-- Table structure for MBIS_Server_Ad_MoveTxtAdMedia
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_MoveTxtAdMedia";
CREATE TABLE "MBIS_Server_Ad_MoveTxtAdMedia" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"Content"  VARCHAR NOT NULL,
"FileTypeID"  INTEGER NOT NULL,
"AddedDate"  VARCHAR NOT NULL,
"MediaStatus"  INTEGER NOT NULL,
"Description"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_MoveTxtAdMedia
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Ad_MoveTxtAdPushStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_MoveTxtAdPushStatus";
CREATE TABLE "MBIS_Server_Ad_MoveTxtAdPushStatus" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"AdID"  INTEGER NOT NULL,
"Status"  INTEGER NOT NULL,
"StartDate"  VARCHAR,
"EndDate"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_MoveTxtAdPushStatus
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Ad_PauseAdMedia
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_PauseAdMedia";
CREATE TABLE "MBIS_Server_Ad_PauseAdMedia" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"ContentID"  VARCHAR,
"Size"  INTEGER DEFAULT 0,
"Name"  VARCHAR,
"URL"  VARCHAR,
"FileTypeID"  INTEGER,
"Time"  INTEGER,
"AddedDate"  VARCHAR,
"MediaStatus"  INTEGER,
"Description"  VARCHAR,
"AdvertiseName"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_PauseAdMedia
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_PauseAdMedia" VALUES (22, '98EF95A6F1487B5ECACD4618F3CCEA88', 4499968, 'lining.ts', 'e:/test_pushed/resource/adv/lining.ts', 1, 5, '2016-12-28', 1, '', '李宁3');
INSERT INTO "main"."MBIS_Server_Ad_PauseAdMedia" VALUES (24, '32EABE4CE88CD10107BA0E78854A462E', 18536048, 'BMW.ts', 'D:/software/wamp/www/pushui/resource/adv/BMW.ts', 1, 5, '2017-05-04', 1, 'BMW集团在探索汽车未来领域有着悠久的历史，我们相信预测未来，不如创造未来。今天，我们用BMW 7系重新诠释了未来的豪华，划时代的创新实现了极富现代感的设计以及骄人而高效的动力。', '宝马730');
INSERT INTO "main"."MBIS_Server_Ad_PauseAdMedia" VALUES (26, '83E5323A832B01ABBC7DB8D11A14F2DE', 3065, 'mozilla.gif', 'D:/software/wamp/www/PushUI/resource/adv/mozilla.gif', 1, 5, '2017-05-17', 0, '李宁图片xxx', '李宁图片');

-- ----------------------------
-- Table structure for MBIS_Server_Ad_PauseAdPushStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_PauseAdPushStatus";
CREATE TABLE "MBIS_Server_Ad_PauseAdPushStatus" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"AdID"  INTEGER NOT NULL,
"PackageID"  INTEGER,
"Status"  INTEGER NOT NULL,
"StartDate"  VARCHAR,
"EndDate"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_PauseAdPushStatus
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_PauseAdPushStatus" VALUES (43, 22, 124, 1, '2016-12-28', '2017-05-17');
INSERT INTO "main"."MBIS_Server_Ad_PauseAdPushStatus" VALUES (45, 22, 100, 1, '2016-12-28', '2017-05-08');
INSERT INTO "main"."MBIS_Server_Ad_PauseAdPushStatus" VALUES (47, 24, 16, 1, '2017-05-04', '2017-05-04');
INSERT INTO "main"."MBIS_Server_Ad_PauseAdPushStatus" VALUES (48, 22, 100, 1, '2017-05-05', '2017-05-17');

-- ----------------------------
-- Table structure for MBIS_Server_Ad_PreRollAdMedia
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_PreRollAdMedia";
CREATE TABLE "MBIS_Server_Ad_PreRollAdMedia" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"ContentID"  VARCHAR,
"Name"  VARCHAR,
"Size"  INTEGER DEFAULT 0,
"URL"  VARCHAR,
"FileTypeID"  INTEGER,
"Time"  INTEGER,
"AddedDate"  VARCHAR,
"MediaStatus"  INTEGER,
"Description"  VARCHAR,
"AdvertiseName"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_PreRollAdMedia
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdMedia" VALUES (52, '32EABE4CE88CD10107BA0E78854A462E', 'BMW.ts', 18536048, 'D:/software/wamp/www/pushui/resource/adv/BMW.ts', 1, 5, '2017-05-03', 1, '宝马汽车股份公司是世界上以生产豪华汽车、摩托车和高性能发动机闻名的汽车公司,名列世界汽车公司前20名。创建于1916年创始人是卡尔·拉普和马克斯·弗里茨。BMW...', 'BMW');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdMedia" VALUES (53, 'A02CD63A26E3103B13A546BA5121923C', 'luhu.ts', 25824432, 'D:/software/wamp/www/pushui/resource/adv/luhu.ts', 1, 5, '2017-05-04', 2, '路虎广告描述', '路虎广告');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdMedia" VALUES (56, '98EF95A6F1487B5ECACD4618F3CCEA88', 'lining.ts', 4499968, 'D:/software/wamp/www/pushui/resource/adv/lining.ts', 1, 5, '2017-05-04', 0, '李宁描述', '李宁');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdMedia" VALUES (57, '86C3DA198E1587FDF0A4262F47415656', 'kuqiao.ts', 9551152, 'D:/software/wamp/www/pushui/resource/adv/kuqiao.ts', 1, 5, '2017-05-04', 1, '苦荞描述', '苦荞2');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdMedia" VALUES (58, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 'Huawei.ts', 7959544, 'D:/software/wamp/www/pushui/resource/adv/Huawei.ts', 1, 15, '2017-05-05', 1, '无华为', '华为');

-- ----------------------------
-- Table structure for MBIS_Server_Ad_PreRollAdPushStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_PreRollAdPushStatus";
CREATE TABLE "MBIS_Server_Ad_PreRollAdPushStatus" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"AdID"  INTEGER NOT NULL,
"PackageID"  INTEGER,
"Status"  INTEGER NOT NULL,
"StartDate"  VARCHAR,
"EndDate"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_PreRollAdPushStatus
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdPushStatus" VALUES (135, 52, 130, 1, '2017-05-03', '2017-05-11');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdPushStatus" VALUES (136, 53, 124, 1, '2017-05-04', '2017-05-06');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdPushStatus" VALUES (137, 57, 49, 1, '2017-05-05', '2017-05-24');
INSERT INTO "main"."MBIS_Server_Ad_PreRollAdPushStatus" VALUES (138, 58, 50, 1, '2017-05-05', '2017-05-24');

-- ----------------------------
-- Table structure for MBIS_Server_Ad_RoleAdMedia
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_RoleAdMedia";
CREATE TABLE "MBIS_Server_Ad_RoleAdMedia" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"ContentID"  VARCHAR NOT NULL,
"Name"  VARCHAR NOT NULL,
"URL"  VARCHAR NOT NULL,
"Size"  INTEGER NOT NULL DEFAULT 0,
"FileTypeID"  INTEGER NOT NULL,
"Time"  INTEGER NOT NULL,
"AddedDate"  VARCHAR NOT NULL,
"MediaStatus"  INTEGER NOT NULL,
"Description"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_RoleAdMedia
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Ad_RoleAdPushStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_RoleAdPushStatus";
CREATE TABLE "MBIS_Server_Ad_RoleAdPushStatus" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"AdID"  INTEGER NOT NULL,
"Status"  INTEGER NOT NULL,
"StartDate"  VARCHAR,
"EndDate"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Ad_RoleAdPushStatus
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Ad_RuleConfigure
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Ad_RuleConfigure";
CREATE TABLE "MBIS_Server_Ad_RuleConfigure" (
"RoleID"  INTEGER,
"RoleDescription"  VARCHAR,
"RoleValue"  VARCHAR DEFAULT 0,
"AdTypeID"  INTEGER DEFAULT 0
);

-- ----------------------------
-- Records of MBIS_Server_Ad_RuleConfigure
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (1, '每轮播发个数', 1, 1);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (2, '每个广告持续时间', 5, 2);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (3, '不同广告间隔时长', 300, 2);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (4, '广告播放位置', 'topright', 2);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (5, '每个广告播放轮次（次）', 10, 4);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (6, '不同广告间隔时长（秒）', 300, 4);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (7, '广告滚动开始位置', 'topright', 4);
INSERT INTO "main"."MBIS_Server_Ad_RuleConfigure" VALUES (8, '广告滚动结束位置', 'topright', 4);

-- ----------------------------
-- Table structure for MBIS_Server_Appendix
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Appendix";
CREATE TABLE [MBIS_Server_Appendix] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [AttachOID] VARCHAR, 
  [AppendixTypeID] INTEGER, 
  [URL] VARCHAR, 
  [Filename] VARCHAR, 
  [Size] INT64);

-- ----------------------------
-- Records of MBIS_Server_Appendix
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (745, '799379F2E27390788DA1A47B5F7F4A4A', 1, '799379F2E27390788DA1A47B5F7F4A4A/yangyong.jpg', 'yangyong.jpg', 19983);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (746, '799379F2E27390788DA1A47B5F7F4A4A', 2, '799379F2E27390788DA1A47B5F7F4A4A/dieyong.jpg', 'dieyong.jpg', 14470);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (748, 'E2281DCA86A340892C5602C7D920FDF5', 1, 'E2281DCA86A340892C5602C7D920FDF5/laizixingxingdeni.jpg', 'laizixingxingdeni.jpg', 12486);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (749, 'E2281DCA86A340892C5602C7D920FDF5', 2, 'E2281DCA86A340892C5602C7D920FDF5/laizixingxingdeni_thumb.jpg', 'laizixingxingdeni_thumb.jpg', 39183);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (772, '8BC02E29BBC9584DD2264DDAFB51FA31', 1, '8BC02E29BBC9584DD2264DDAFB51FA31/12yearaslave_thumb.jpeg', '12yearaslave_thumb.jpeg', 11272);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (773, '8BC02E29BBC9584DD2264DDAFB51FA31', 2, '8BC02E29BBC9584DD2264DDAFB51FA31/12yearaslave.jpg', '12yearaslave.jpg', 11272);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (786, 'S2017032200000000000000000000574', 1, 'S2017032200000000000000000000574/kuailedabenying_thumb.jpeg', 'kuailedabenying_thumb.jpeg', 5017);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (787, 'S2017032200000000000000000000574', 2, 'S2017032200000000000000000000574/kuailedabenying.jpg', 'kuailedabenying.jpg', 11109);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (799, 'FC1D25E6626669E91897D2D5083AEFF8', 1, 'FC1D25E6626669E91897D2D5083AEFF8/laoxiangjiangzuo_thumb.png', 'laoxiangjiangzuo_thumb.png', 23156);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (800, 'FC1D25E6626669E91897D2D5083AEFF8', 2, 'FC1D25E6626669E91897D2D5083AEFF8/laoxiangjiangzuo.png', 'laoxiangjiangzuo.png', 550684);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (812, 'F120573BE8305B646D00293E27D496BF', 1, 'F120573BE8305B646D00293E27D496BF/ziyouyong_thumb.jpeg', 'ziyouyong_thumb.jpeg', 5694);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (813, 'F120573BE8305B646D00293E27D496BF', 2, 'F120573BE8305B646D00293E27D496BF/ziyouyong.jpg', 'ziyouyong.jpg', 16817);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (874, 'S2017032900000000000000000000593', 2, 'S2017032900000000000000000000593/php.jpg', 'php.jpg', 11423);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (881, 'S2017033000000000000000000000595', 1, 'S2017033000000000000000000000595/deyidianqi_thumb.jpeg', 'deyidianqi_thumb.jpeg', 2061);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (882, 'S2017033000000000000000000000595', 2, 'S2017033000000000000000000000595/deyidianqi2.jpg', 'deyidianqi2.jpg', 11031);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (883, '169E832C168E4C85BC5ECD717657D04C', 1, '169E832C168E4C85BC5ECD717657D04C/deyidianqi_thumb.jpeg', 'deyidianqi_thumb.jpeg', 2061);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (884, '169E832C168E4C85BC5ECD717657D04C', 2, '169E832C168E4C85BC5ECD717657D04C/deyidianqi2.jpg', 'deyidianqi2.jpg', 11031);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (885, 'S2017033100000000000000000000596', 1, 'S2017033100000000000000000000596/anime_thumb.jpeg', 'anime_thumb.jpeg', 4926);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (886, 'S2017033100000000000000000000596', 2, 'S2017033100000000000000000000596/doreamon.jpg', 'doreamon.jpg', 2967);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (887, '98EF95A6F1487B5ECACD4618F3CCEA88', 1, '98EF95A6F1487B5ECACD4618F3CCEA88/lining_thumb.jpeg', 'lining_thumb.jpeg', 3173);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (888, '98EF95A6F1487B5ECACD4618F3CCEA88', 2, '98EF95A6F1487B5ECACD4618F3CCEA88/lining.jpg', 'lining.jpg', 9040);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (889, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 1, '4FCAF5DE62AD92911EDEE6CF67E0E4DA/huawei_thumb.jpeg', 'huawei_thumb.jpeg', 3225);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (891, '752CD57A4E74B7E354680DECD4B769BC', 1, '752CD57A4E74B7E354680DECD4B769BC/animation_thumb.png', 'animation_thumb.png', 18831);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (892, '752CD57A4E74B7E354680DECD4B769BC', 2, '752CD57A4E74B7E354680DECD4B769BC/animation.png', 'animation.png', 1132942);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (900, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 2, '4FCAF5DE62AD92911EDEE6CF67E0E4DA/mozilla.gif', 'mozilla.gif', 3065);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (907, 'S2017032900000000000000000000593', 1, 'S2017032900000000000000000000593/mozilla_thumb.gif', 'mozilla_thumb.gif', 2538);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (909, '00577066B8130B47DBAEF8B889E41B48', 1, '00577066B8130B47DBAEF8B889E41B48/tiaoshui_poster_thumb.jpeg', 'tiaoshui_poster_thumb.jpeg', 4489);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (928, 'S2017031400000000000000000000539', 1, 'S2017031400000000000000000000539/e02_thumb.jpeg', 'e02_thumb.jpeg', 4991);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (929, 'S2017031400000000000000000000539', 2, 'S2017031400000000000000000000539/e02.jpg', 'e02.jpg', 12486);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (936, '00577066B8130B47DBAEF8B889E41B48', 2, '00577066B8130B47DBAEF8B889E41B48/tiaoshui_poster.jpg', 'tiaoshui_poster.jpg', 89373);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (937, 'S2017032100000000000000000000572', 2, 'S2017032100000000000000000000572/p01_thumb_show0004.jpg', 'p01_thumb_show0004.jpg', 18206);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (938, 'S2017032100000000000000000000572', 1, 'S2017032100000000000000000000572/p01_appendix_laoxiang0001_thumb.jpeg', 'p01_appendix_laoxiang0001_thumb.jpeg', 5062);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (939, '734CCD42DC810E9AD0012B1FE6836D36', 1, '734CCD42DC810E9AD0012B1FE6836D36/E01_thumb.jpeg', 'E01_thumb.jpeg', 2837);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (940, '734CCD42DC810E9AD0012B1FE6836D36', 2, '734CCD42DC810E9AD0012B1FE6836D36/E01.jpg', 'E01.jpg', 9429);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (941, '0B4A4FDA862E6B8DD304E3C880EA6EE5', 1, '0B4A4FDA862E6B8DD304E3C880EA6EE5/p01_thumb_show0004_thumb.jpeg', 'p01_thumb_show0004_thumb.jpeg', 3801);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (942, '0B4A4FDA862E6B8DD304E3C880EA6EE5', 2, '0B4A4FDA862E6B8DD304E3C880EA6EE5/p01_thumb_show0001.jpg', 'p01_thumb_show0001.jpg', 19812);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (944, '9420DFBAF9264DE215CF257429EEA7EE', 2, '9420DFBAF9264DE215CF257429EEA7EE/daopisanguan2.jpg', 'daopisanguan2.jpg', 15657);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (947, '9420DFBAF9264DE215CF257429EEA7EE', 1, '9420DFBAF9264DE215CF257429EEA7EE/134-1_thumb.jpeg', '134-1_thumb.jpeg', 4860);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (948, 'S2017041100000000000000000000603', 1, 'S2017041100000000000000000000603/huojin_thumb.jpeg', 'huojin_thumb.jpeg', 4972);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (949, 'S2017041100000000000000000000603', 2, 'S2017041100000000000000000000603/tvshopping.jpg', 'tvshopping.jpg', 26188);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (950, '04E499965116B22252C558410B3A025A', 2, '04E499965116B22252C558410B3A025A/A.Simple.Noodle.Story.jpg', 'A.Simple.Noodle.Story.jpg', 22951);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (951, '04E499965116B22252C558410B3A025A', 1, '04E499965116B22252C558410B3A025A/A.Simple.Noodle.Story_thumb.jpeg', 'A.Simple.Noodle.Story_thumb.jpeg', 7164);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (952, '518BC5A6066C54E67ECF5224A04F2F8C', 1, '518BC5A6066C54E67ECF5224A04F2F8C/romancing.the.stone_thumb.jpeg', 'romancing.the.stone_thumb.jpeg', 5538);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (953, '518BC5A6066C54E67ECF5224A04F2F8C', 2, '518BC5A6066C54E67ECF5224A04F2F8C/romancing.the.stone.jpg', 'romancing.the.stone.jpg', 43759);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (954, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 1, 'E8F1FCC9F5B9C7177D75110F0EAE17E1/jinshajiangpan_thumb.jpeg', 'jinshajiangpan_thumb.jpeg', 4511);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (955, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 2, 'E8F1FCC9F5B9C7177D75110F0EAE17E1/jinshajiangpan.jpg', 'jinshajiangpan.jpg', 4772);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (956, 'E05C63468AC7991F54C68B8C4516429C', 1, 'E05C63468AC7991F54C68B8C4516429C/E03_thumb.jpeg', 'E03_thumb.jpeg', 4671);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (957, 'E05C63468AC7991F54C68B8C4516429C', 2, 'E05C63468AC7991F54C68B8C4516429C/E03.jpg', 'E03.jpg', 12165);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (960, 'S2017032100000000000000000000573', 1, 'S2017032100000000000000000000573/p01_appendix_laoxiang0003_thumb.jpeg', 'p01_appendix_laoxiang0003_thumb.jpeg', 4523);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (961, 'S2017032100000000000000000000573', 2, 'S2017032100000000000000000000573/p01_appendix_laoxiang0003.jpg', 'p01_appendix_laoxiang0003.jpg', 32695);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (963, '57DD8412C26BF9DE9ACC186233E153E8', 1, '57DD8412C26BF9DE9ACC186233E153E8/animation_2_thumb.png', 'animation_2_thumb.png', 16482);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (964, '57DD8412C26BF9DE9ACC186233E153E8', 2, '57DD8412C26BF9DE9ACC186233E153E8/animation_2.png', 'animation_2.png', 313074);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (965, 'S2017042000000000000000000000608', 1, 'S2017042000000000000000000000608/animation_2_thumb.png', 'animation_2_thumb.png', 16482);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (966, 'S2017042000000000000000000000608', 2, 'S2017042000000000000000000000608/animation_2.png', 'animation_2.png', 313074);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (967, '676513298BD859BF32AF1411F8E50C86', 1, '676513298BD859BF32AF1411F8E50C86/p03_thumb_show0001_eps01_thumb.jpeg', 'p03_thumb_show0001_eps01_thumb.jpeg', 3881);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (973, 'CF47F40C5B9676F1CEE45F1021BA9800', 1, 'CF47F40C5B9676F1CEE45F1021BA9800/duizhengxiayao_thumb.jpeg', 'duizhengxiayao_thumb.jpeg', 5226);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (974, 'CF47F40C5B9676F1CEE45F1021BA9800', 2, 'CF47F40C5B9676F1CEE45F1021BA9800/duizhengxiayao.jpg', 'duizhengxiayao.jpg', 11409);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (986, 'C9C1DA14F1BEF370395BB58D375C5FD0', 1, 'C9C1DA14F1BEF370395BB58D375C5FD0/12yearaslave_thumb.jpeg', '12yearaslave_thumb.jpeg', 4187);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (987, 'C9C1DA14F1BEF370395BB58D375C5FD0', 2, 'C9C1DA14F1BEF370395BB58D375C5FD0/12yearslave.jpg', '12yearslave.jpg', 20468);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (989, 'A9D34CA7CBEC64161EB99E5A88E51733', 2, 'A9D34CA7CBEC64161EB99E5A88E51733/casino_royale.jpg', 'casino_royale.jpg', 11444);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (990, 'A9D34CA7CBEC64161EB99E5A88E51733', 1, 'A9D34CA7CBEC64161EB99E5A88E51733/casino_royale.jpg', 'casino_royale.jpg', 4406);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (991, '58ADC1F4E5641253F5B86FB581EFFD0C', 1, '58ADC1F4E5641253F5B86FB581EFFD0C/mononoke.jpg', 'mononoke.jpg', 4214);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (992, '58ADC1F4E5641253F5B86FB581EFFD0C', 2, '58ADC1F4E5641253F5B86FB581EFFD0C/mononoke.jpg', 'mononoke.jpg', 10875);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (993, '43DF00415D3450BA42BEC5644F3C153F', 1, '43DF00415D3450BA42BEC5644F3C153F/star04.png', 'star04.png', 16591);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (994, '43DF00415D3450BA42BEC5644F3C153F', 2, '43DF00415D3450BA42BEC5644F3C153F/star04.png', 'star04.png', 46798);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (995, '287A3939C180E1F5B06A5D1B16140B85', 1, '287A3939C180E1F5B06A5D1B16140B85/p01_appendix_laoxiang0001.jpg', 'p01_appendix_laoxiang0001.jpg', 4140);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (996, '287A3939C180E1F5B06A5D1B16140B85', 2, '287A3939C180E1F5B06A5D1B16140B85/p01_appendix_laoxiang0001.jpg', 'p01_appendix_laoxiang0001.jpg', 35236);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (997, 'E46E6A25448315083AB12A6F8CCFDD9C', 1, 'E46E6A25448315083AB12A6F8CCFDD9C/p03_thumb_show0001_eps01.jpg', 'p03_thumb_show0001_eps01.jpg', 4981);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (998, 'E46E6A25448315083AB12A6F8CCFDD9C', 2, 'E46E6A25448315083AB12A6F8CCFDD9C/p03_thumb_show0001_eps01.jpg', 'p03_thumb_show0001_eps01.jpg', 10705);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1002, '09CFE2AE19B64A682E2BEA3734123C48', 1, '09CFE2AE19B64A682E2BEA3734123C48/qiankunfushoujing.jpg', 'qiankunfushoujing.jpg', 3289);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1003, '09CFE2AE19B64A682E2BEA3734123C48', 2, '09CFE2AE19B64A682E2BEA3734123C48/qiankunfushoujing.jpg', 'qiankunfushoujing.jpg', 7733);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1004, '11D75C3E61ADBF36F755FA7A868D69F4', 1, '11D75C3E61ADBF36F755FA7A868D69F4/meifeixuanduan.jpg', 'meifeixuanduan.jpg', 4337);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1005, '11D75C3E61ADBF36F755FA7A868D69F4', 2, '11D75C3E61ADBF36F755FA7A868D69F4/134-1.jpg', '134-1.jpg', 9833);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1006, 'A02CD63A26E3103B13A546BA5121923C', 1, 'A02CD63A26E3103B13A546BA5121923C/lining_thumb.png', 'lining_thumb.png', 17551);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1008, 'A02CD63A26E3103B13A546BA5121923C', 2, 'A02CD63A26E3103B13A546BA5121923C/2008.01.jpg', '2008.01.jpg', 15476);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1009, '753865F1EAA65D5861CB00CCE7E6BDF4', 1, '753865F1EAA65D5861CB00CCE7E6BDF4/car_thumb.jpeg', 'car_thumb.jpeg', 3479);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1011, '753865F1EAA65D5861CB00CCE7E6BDF4', 2, '753865F1EAA65D5861CB00CCE7E6BDF4/advertisement.jpg', 'advertisement.jpg', 8171);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1012, 'S2017032000000000000000000000571', 1, 'S2017032000000000000000000000571/lvyoumeishi.jpg', 'lvyoumeishi.jpg', 5296);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1013, 'S2017032000000000000000000000571', 2, 'S2017032000000000000000000000571/p01_thumb_hot0002.jpg', 'p01_thumb_hot0002.jpg', 12586);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1014, '7DA0A9BDB14B9DCE504837C563E7C021', 1, '7DA0A9BDB14B9DCE504837C563E7C021/wenwang.jpg', 'wenwang.jpg', 4434);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1015, '7DA0A9BDB14B9DCE504837C563E7C021', 2, '7DA0A9BDB14B9DCE504837C563E7C021/wenwang.jpg', 'wenwang.jpg', 10742);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1016, '31B2292BF57C39427BA2CC4C292E0DC6', 1, '31B2292BF57C39427BA2CC4C292E0DC6/yuwen_thumb.jpeg', 'yuwen_thumb.jpeg', 3145);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1017, 'A00B796120BA02243CE152801DCD7F16', 1, 'A00B796120BA02243CE152801DCD7F16/5_thumb.png', '5_thumb.png', 14327);
INSERT INTO "main"."MBIS_Server_Appendix" VALUES (1018, 'A00B796120BA02243CE152801DCD7F16', 2, 'A00B796120BA02243CE152801DCD7F16/6.png', '6.png', 388732);

-- ----------------------------
-- Table structure for MBIS_Server_AppendixType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_AppendixType";
CREATE TABLE [MBIS_Server_AppendixType] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [Appendixtype] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_AppendixType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_AppendixType" VALUES (1, '缩略图');
INSERT INTO "main"."MBIS_Server_AppendixType" VALUES (2, '海报');

-- ----------------------------
-- Table structure for mbis_server_attr
-- ----------------------------
DROP TABLE IF EXISTS "main"."mbis_server_attr";
CREATE TABLE "mbis_server_attr" (
  id integer primary key AUTOINCREMENT,
  name text not null
);

-- ----------------------------
-- Records of mbis_server_attr
-- ----------------------------
INSERT INTO "main"."mbis_server_attr" VALUES (1, '科目');
INSERT INTO "main"."mbis_server_attr" VALUES (2, '行业');
INSERT INTO "main"."mbis_server_attr" VALUES (3, '类型');
INSERT INTO "main"."mbis_server_attr" VALUES (4, '培训类型');
INSERT INTO "main"."mbis_server_attr" VALUES (5, '学科');
INSERT INTO "main"."mbis_server_attr" VALUES (6, '年级');
INSERT INTO "main"."mbis_server_attr" VALUES (8, '视频类型');
INSERT INTO "main"."mbis_server_attr" VALUES (13, 'test name');
INSERT INTO "main"."mbis_server_attr" VALUES (14, '客户');
INSERT INTO "main"."mbis_server_attr" VALUES (15, 1234);
INSERT INTO "main"."mbis_server_attr" VALUES (16, '市场');
INSERT INTO "main"."mbis_server_attr" VALUES (17, '专题');
INSERT INTO "main"."mbis_server_attr" VALUES (18, '名称');

-- ----------------------------
-- Table structure for MBIS_Server_BackUp
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_BackUp";
CREATE TABLE "MBIS_Server_BackUp" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"Asset_Name"  VARCHAR,
"Round"  INTEGER,
"Size"  INTEGER,
"Date"  VARCHAR,
"MissionName"  VARCHAR,
"PackageName"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_BackUp
-- ----------------------------
INSERT INTO "main"."MBIS_Server_BackUp" VALUES (1, 1, 1, 1, '2015-08-10', 1, 1);
INSERT INTO "main"."MBIS_Server_BackUp" VALUES (2, 2, 2, 2, '2015-08-10', 2, 2);

-- ----------------------------
-- Table structure for MBIS_Server_BalanceRecord
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_BalanceRecord";
CREATE TABLE [MBIS_Server_BalanceRecord] (
  [CustomerID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [Item] VARCHAR NOT NULL, 
  [Fee] INTEGER NOT NULL, 
  [Balance] INTEGER NOT NULL, 
  [Date] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_BalanceRecord
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Bill
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Bill";
CREATE TABLE [MBIS_Server_Bill] (
  [BillID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [SubscribeSubtotal] INTEGER NOT NULL, 
  [VODSubtotal] INTEGER NOT NULL, 
  [DiscountSubtotal] INTEGER NOT NULL, 
  [Total] INTEGER NOT NULL, 
  [CustomerID] INTEGER NOT NULL, 
  [DateStart] VARCHAR NOT NULL, 
  [DateEnd] VARCHAR NOT NULL, 
  [DateGenerate] VARCHAR NOT NULL, 
  [BillStatus] INTEGER NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_Bill
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_BillMission
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_BillMission";
CREATE TABLE [MBIS_Server_BillMission] (
  [BillMissionID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [BillID] INTEGER NOT NULL, 
  [MissionUsedID] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_BillMission
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_BillOnDemand
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_BillOnDemand";
CREATE TABLE [MBIS_Server_BillOnDemand] (
  [BillOnDemandID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [BillID] INTEGER NOT NULL, 
  [CustomerOnDemandID] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_BillOnDemand
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_BillStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_BillStatus";
CREATE TABLE [MBIS_Server_BillStatus] (
  [BillStatusID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [BillStatusName] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_BillStatus
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Channel
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Channel";
CREATE TABLE "MBIS_Server_Channel" (
"ChannelID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"Name"  VARCHAR(256) NOT NULL,
"Image"  VARCHAR,
"Description"  VARCHAR,
"State"  INTEGER,
"Thumb"  VARCHAR,
"OriginalDir"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Channel
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Channel" VALUES (1, 'CCTV-1', null, '中央卫视-1', 1, null, null);
INSERT INTO "main"."MBIS_Server_Channel" VALUES (2, 'BTV-1', null, 'Beijing', 1, null, null);
INSERT INTO "main"."MBIS_Server_Channel" VALUES (3, 'CCTV-11', null, '戏曲频道', 1, null, null);

-- ----------------------------
-- Table structure for MBIS_Server_Charge
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Charge";
CREATE TABLE [MBIS_Server_Charge] (
  [ChargeID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [CustomerID] INTEGER NOT NULL, 
  [PayMethod] INTEGER NOT NULL, 
  [Total] INTEGER NOT NULL, 
  [ChargeTime] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_Charge
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Charge" VALUES (2, 13, 1, 1, '2015-08-11');

-- ----------------------------
-- Table structure for MBIS_Server_ChargeType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_ChargeType";
CREATE TABLE [MBIS_Server_ChargeType] (
  [ID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [ChargeTypeID] INTEGER NOT NULL, 
  [ChargeTypeName] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_ChargeType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_ChargeType" VALUES (1, 0, '免费');
INSERT INTO "main"."MBIS_Server_ChargeType" VALUES (2, 1, '年');
INSERT INTO "main"."MBIS_Server_ChargeType" VALUES (3, 2, '月');
INSERT INTO "main"."MBIS_Server_ChargeType" VALUES (4, 3, '期');

-- ----------------------------
-- Table structure for MBIS_Server_Configure
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Configure";
CREATE TABLE "MBIS_Server_Configure" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"Name"  VARCHAR,
"IntValue"  INTEGER,
"StringValue"  VARCHAR,
"BlobValue"  BLOB
);

-- ----------------------------
-- Records of MBIS_Server_Configure
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Configure" VALUES (1, 'port', 15015, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (2, 'ip_addr', null, '127.0.0.1', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (3, 'local_bind', null, '127.0.0.1', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (4, 'send_ratio', 1500, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (5, 'content_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/media', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (6, 'part_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/part', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (7, 'part_size', 524288, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (8, 'channel_num', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (9, 'thumb_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/thumb', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (10, 'appendix_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/appendix', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (11, 'background_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/background', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (12, 'channel_sync', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (13, 'content_order', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (14, 'packagechannel_num', 4, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (15, 'start_time', null, '08:01', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (16, 'web_port', 1080, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (18, 'package_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/package', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (19, 'channel_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/channel', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (20, 'system_dir', null, 'F:\subversion\PUSH_2.0\PushUI/resource/system', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (21, 'mip_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/mip', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (22, 'sync_port', 15011, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (23, 'sync_dir', null, 'F:\subversion\PUSH_2.0\PushUI/resource/sync', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (24, 'adv_dir', null, 'F:\subversion\PUSH_2.0\PushUI/resource/adv', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (25, 'round_adv_start', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (26, 'round_adv_preroll', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (27, 'round_adv_role', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (28, 'round_adv_pause', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (29, 'round_adv_text', 5, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (30, 'uPid', 102, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (31, 'xmltemplate_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/UiMip/Templates', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (32, 'xml_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/UiMip', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (33, 'log_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/logcontent', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (34, 'thumb_width', 132, 132, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (35, 'thumb_height', 96, 96, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (36, 'background_width', 178, 178, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (37, 'background_height', 250, 250, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (38, 'backuptime', 30, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (39, 'adlog_path', null, 'F:\subversion\PUSH_2.0\PushUI/resource/logadv', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (40, 'totalsource', 4, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (41, 'reviewed', 0, '', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (42, 'login', 0, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (43, 'daily_record', 1, null, null);

-- ----------------------------
-- Table structure for MBIS_Server_ContentArchiecture
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_ContentArchiecture";
CREATE TABLE [MBIS_Server_ContentArchiecture] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [ParentOID] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_ContentArchiecture
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Country
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Country";
CREATE TABLE [MBIS_Server_Country] (
  [ID] INTEGER PRIMARY KEY, 
  [Country] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Country
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Country" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_Country" VALUES (1, '大陆');
INSERT INTO "main"."MBIS_Server_Country" VALUES (2, '港台');
INSERT INTO "main"."MBIS_Server_Country" VALUES (3, '日本');
INSERT INTO "main"."MBIS_Server_Country" VALUES (4, '美国');
INSERT INTO "main"."MBIS_Server_Country" VALUES (5, '东南亚');
INSERT INTO "main"."MBIS_Server_Country" VALUES (6, '法国');
INSERT INTO "main"."MBIS_Server_Country" VALUES (7, '德国');
INSERT INTO "main"."MBIS_Server_Country" VALUES (8, '意大利');
INSERT INTO "main"."MBIS_Server_Country" VALUES (9, '英国');
INSERT INTO "main"."MBIS_Server_Country" VALUES (10, '欧洲');
INSERT INTO "main"."MBIS_Server_Country" VALUES (11, '南美');
INSERT INTO "main"."MBIS_Server_Country" VALUES (12, '加拿大');
INSERT INTO "main"."MBIS_Server_Country" VALUES (13, '澳大利亚');
INSERT INTO "main"."MBIS_Server_Country" VALUES (14, '新西兰');
INSERT INTO "main"."MBIS_Server_Country" VALUES (15, '韩国');

-- ----------------------------
-- Table structure for MBIS_Server_Customer
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Customer";
CREATE TABLE "MBIS_Server_Customer" (
"CustomerID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"CustomerName"  VARCHAR NOT NULL,
"Gender"  INTEGER,
"Birthday"  VARCHAR,
"Tel"  VARCHAR NOT NULL,
"Phone"  VARCHAR,
"IDCard"  VARCHAR NOT NULL,
"Email"  VARCHAR,
"Location"  VARCHAR NOT NULL,
"ZoneID"  INTEGER NOT NULL,
"CustomerGroupID"  INTEGER NOT NULL,
"CustomerGroupExpDate"  VARCHAR,
"Balance"  INTEGER NOT NULL,
"PaymentType"  INTEGER NOT NULL,
"AnalogTVID"  VARCHAR NOT NULL,
"STBID"  VARCHAR NOT NULL,
"SmartCardID"  VARCHAR NOT NULL,
"CustomerDateAdded"  VARCHAR,
"CustomerState"  INTEGER NOT NULL,
"CustomerIDDisplay "  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Customer
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Customer" VALUES (20, 1, 1, 1, 1, 1, 1, 1, 1, 6, 6, null, 0, '预付费', 1, 1, 1, '2015-08-12', 1, null);
INSERT INTO "main"."MBIS_Server_Customer" VALUES (21, '郝文琪', 0, '1993-3-30', 110, 21312312312, '1.11111111213123e+20', '', 21312, 6, 6, null, 0, '预付费', 3123123123, 32131233, 123123213, '2015-08-12', 1, null);

-- ----------------------------
-- Table structure for MBIS_Server_CustomerGroup
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_CustomerGroup";
CREATE TABLE [MBIS_Server_CustomerGroup] (
  [CustomerGroupID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [CustomerGroupName] VARCHAR NOT NULL, 
  [CustomerGroupDiscount] INTEGER NOT NULL, 
  [CustomerGroupDiscription] VARCHAR NOT NULL, 
  [Res] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_CustomerGroup
-- ----------------------------
INSERT INTO "main"."MBIS_Server_CustomerGroup" VALUES (6, 'VIP', 5000, 50, 'NB');

-- ----------------------------
-- Table structure for MBIS_Server_CustomerSubscribe
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_CustomerSubscribe";
CREATE TABLE "MBIS_Server_CustomerSubscribe" (
"SubscribeID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"PackageID"  INTEGER NOT NULL,
"STBID"  VARCHAR NOT NULL,
"StartDate"  VARCHAR NOT NULL,
"EndDate"  VARCHAR,
"StartTime"  VARCHAR,
"EndTime"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_CustomerSubscribe
-- ----------------------------
INSERT INTO "main"."MBIS_Server_CustomerSubscribe" VALUES (14, 22, 1, '2015-08-11', '2015-08-12', null, null);

-- ----------------------------
-- Table structure for MBIS_Server_CustomerZone
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_CustomerZone";
CREATE TABLE [MBIS_Server_CustomerZone] (
  [ZoneID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [ZoneName] VARCHAR NOT NULL, 
  [ZoneDescription] VARCHAR, 
  [DateAdded] VARCHAR NOT NULL, 
  [DateModified] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_CustomerZone
-- ----------------------------
INSERT INTO "main"."MBIS_Server_CustomerZone" VALUES (6, '北京朝阳', null, '2015-08-11', null);
INSERT INTO "main"."MBIS_Server_CustomerZone" VALUES (7, '河北唐山', null, '2015-08-11', null);
INSERT INTO "main"."MBIS_Server_CustomerZone" VALUES (8, '北京海淀', null, '2015-08-11', null);

-- ----------------------------
-- Table structure for MBIS_Server_Discount
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Discount";
CREATE TABLE [MBIS_Server_Discount] (
  [DiscountID] INTEGER, 
  [DiscountName] VARCHAR, 
  [DiscountType] INTEGER, 
  [DiscountSet] INTEGER, 
  [DiscountValue] INTEGER, 
  [DiscountTotal] INTEGER, 
  [DiscountStatus] INTEGER, 
  [DiscountDateStart] VARCHAR, 
  [DiscountDateEnd] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Discount
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_DiscountPackage
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_DiscountPackage";
CREATE TABLE [MBIS_Server_DiscountPackage] (
  [DiscountPackageRuleID] INTEGER, 
  [DiscountID] INTEGER, 
  [PackageID] INTEGER, 
  CONSTRAINT [] PRIMARY KEY ([DiscountPackageRuleID], [DiscountID]));

-- ----------------------------
-- Records of MBIS_Server_DiscountPackage
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_EditStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_EditStatus";
CREATE TABLE "MBIS_Server_EditStatus" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR,
"EditStatus"  INTEGER,
"SliceStatus"  INTEGER,
"PackageStatus"  INTEGER,
"ChannelStatus"  INTEGER,
"DateStatus"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_EditStatus
-- ----------------------------
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (418, '8BC02E29BBC9584DD2264DDAFB51FA31', 2, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (419, '9420DFBAF9264DE215CF257429EEA7EE', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (420, '00577066B8130B47DBAEF8B889E41B48', 2, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (421, 'F120573BE8305B646D00293E27D496BF', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (422, '04E499965116B22252C558410B3A025A', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (423, '734CCD42DC810E9AD0012B1FE6836D36', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (424, 'E2281DCA86A340892C5602C7D920FDF5', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (425, 'FC398B1EF3ECE07E71E42D871CD25C7D', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (427, 'F424859A1FD7CA03DAF23A3350207B76', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (428, '470F17FCE2FCEC46B901306B3DBB6C1D', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (429, '8865CDCCAD8B5F7656719E49AD22274B', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (431, '7B8D936E2B24DC1038A878D13D5EFB8F', 0, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (432, '7DA0A9BDB14B9DCE504837C563E7C021', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (435, 'CF47F40C5B9676F1CEE45F1021BA9800', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (437, '287A3939C180E1F5B06A5D1B16140B85', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (438, '0B4A4FDA862E6B8DD304E3C880EA6EE5', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (440, 'FC1D25E6626669E91897D2D5083AEFF8', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (441, '11D75C3E61ADBF36F755FA7A868D69F4', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (442, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (443, '09CFE2AE19B64A682E2BEA3734123C48', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (446, '169E832C168E4C85BC5ECD717657D04C', 2, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (447, '98EF95A6F1487B5ECACD4618F3CCEA88', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (448, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (450, '752CD57A4E74B7E354680DECD4B769BC', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (452, '5BCBA3BAE6F3D25DB9B1269AC59CCD35', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (453, '518BC5A6066C54E67ECF5224A04F2F8C', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (454, 'E05C63468AC7991F54C68B8C4516429C', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (455, '43DF00415D3450BA42BEC5644F3C153F', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (456, '57DD8412C26BF9DE9ACC186233E153E8', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (457, '676513298BD859BF32AF1411F8E50C86', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (458, 'E46E6A25448315083AB12A6F8CCFDD9C', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (459, '62D91C030B1F94ABAC7EF29317600D56', 3, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (460, '753865F1EAA65D5861CB00CCE7E6BDF4', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (461, 'A9D34CA7CBEC64161EB99E5A88E51733', 1, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (462, '58ADC1F4E5641253F5B86FB581EFFD0C', 2, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (463, 'C9C1DA14F1BEF370395BB58D375C5FD0', 2, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (465, '799379F2E27390788DA1A47B5F7F4A4A', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (466, 'A02CD63A26E3103B13A546BA5121923C', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (467, '32EABE4CE88CD10107BA0E78854A462E', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (468, '0684D30C29E56200D2239A7FC50D7E53', 0, 0, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (469, '31B2292BF57C39427BA2CC4C292E0DC6', 2, 1, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_EditStatus" VALUES (470, 'A00B796120BA02243CE152801DCD7F16', 2, 1, 0, 0, 0);

-- ----------------------------
-- Table structure for MBIS_Server_File
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_File";
CREATE TABLE [MBIS_Server_File] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [Url] VARCHAR, 
  [Size] INT64(256));

-- ----------------------------
-- Records of MBIS_Server_File
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_FreeContent
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_FreeContent";
CREATE TABLE "MBIS_Server_FreeContent" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR,
"Round"  INTEGER,
"Date"  VARCHAR,
"PushTime"  VARCHAR,
"State"  VARCHAR,
"PushID"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_FreeContent
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Genre
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Genre";
CREATE TABLE [MBIS_Server_Genre] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [Genre] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Genre
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Genre" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (1, '爱情');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (2, '恐怖');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (3, '喜剧');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (4, '动画');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (5, '家庭');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (6, '科幻');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (7, '冒险');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (8, '纪录');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (9, '访谈');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (10, '体育');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (11, '犯罪');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (13, '动作');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (14, '惊悚');
INSERT INTO "main"."MBIS_Server_Genre" VALUES (15, '剧情');

-- ----------------------------
-- Table structure for MBIS_Server_HP
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_HP";
CREATE TABLE [MBIS_Server_HP] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [HP] VARCHAR, 
  [HPDescription] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_HP
-- ----------------------------
INSERT INTO "main"."MBIS_Server_HP" VALUES (0, '待定', null);
INSERT INTO "main"."MBIS_Server_HP" VALUES (1, '天', '终端存储到第2天23:59.');
INSERT INTO "main"."MBIS_Server_HP" VALUES (2, '周', '终端存储到下一周周日23:59.');
INSERT INTO "main"."MBIS_Server_HP" VALUES (3, '2周', '终端存储到后周周日23:59.');
INSERT INTO "main"."MBIS_Server_HP" VALUES (4, '月', '终端存储到月底23:59.');
INSERT INTO "main"."MBIS_Server_HP" VALUES (5, '季', '终端存储到季末23:59.');
INSERT INTO "main"."MBIS_Server_HP" VALUES (6, '年', '终端存储到年底23:59.');

-- ----------------------------
-- Table structure for MBIS_Server_Language
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Language";
CREATE TABLE [MBIS_Server_Language] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [Language] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Language
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Language" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_Language" VALUES (1, '英语中字');
INSERT INTO "main"."MBIS_Server_Language" VALUES (2, '中英双语');
INSERT INTO "main"."MBIS_Server_Language" VALUES (3, '中语中字');
INSERT INTO "main"."MBIS_Server_Language" VALUES (4, '粤语中字');
INSERT INTO "main"."MBIS_Server_Language" VALUES (5, '法语');
INSERT INTO "main"."MBIS_Server_Language" VALUES (6, '日语');

-- ----------------------------
-- Table structure for MBIS_Server_Media
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Media";
CREATE TABLE "MBIS_Server_Media" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR,
"Title"  VARCHAR,
"Introduction"  VARCHAR,
"Rating"  VARCHAR,
"MediaTypeID"  INTEGER,
"LanguageID"  INTEGER,
"Thumb"  VARCHAR,
"Background"  VARCHAR,
"ChannelID"  INTEGER,
"HPID"  INTEGER,
"StartTime"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Media
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Media" VALUES (223, '8BC02E29BBC9584DD2264DDAFB51FA31', '为奴十二年', '电影讲述一个生活在纽约的自由的黑人，受过教育且已婚。随后遇到两个人，他们许诺在华盛顿帮他找一份工作', 9.6, 1, 1, null, null, 1, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (224, '9420DFBAF9264DE215CF257429EEA7EE', '刀劈杨藩1', '又名《白马关》、《马前血》。取材于《薛丁山征西演义》。写樊梨花之未婚夫杨藩，因恨梨花投唐嫁与薛丁山而兴兵犯边。唐王命梨花为帅，丁山为先行御敌。梨花白马关观杨貌美，有悔意。杨逼她向唐营连射三箭以示决绝。梨花徘徊间，见丁山来战，对比二人武艺、相貌，杨不如薛。在三人追赶时，樊梨花撇开丁山刀劈杨藩。', 9.2, 7, 3, null, null, 3, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (225, '04E499965116B22252C558410B3A025A', '三枪拍案惊奇', '片讲述了，荒漠中的麻子面馆，风情万种的老板娘（闫妮 饰）向波斯商人订购了一把左轮手枪，后者还展示了大炮的威力。炮声引来了巡逻队，老板娘跟情人李四（小沈阳 饰）将枪藏匿后，插科打诨，好饭招待，这才稳住了精明的队长（赵本山 饰）。队长的得力助手张三（孙红雷 饰）知悉老板娘与李四的暧昧关系，但未说破。原来，他跟老板（倪大红 饰）之间还有勾当。店员六暗恋店员七，二人被老板克扣工钱，同命相连，六曾试图向老板讨薪，惨遭痛骂。老板因老板娘未生儿育女，对她动用私刑（扮成儿子）。密室中，张三告知老板真相，老板听后，怒不可遏，前去痛扁二人，被老板娘用手枪喝退。事后，老板与张三密谋买凶杀人，由此引发一场令人啼笑皆非的血案[5] ', 2, 1, 3, null, null, 1, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (226, 'S2016122800000000000000000000491', 'From the Star you come', '一部韩国偶像剧From the Star you come1', 8, 5, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (227, 'FC398B1EF3ECE07E71E42D871CD25C7D', 'shijiebei.ts', null, null, 1, null, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (228, '98EF95A6F1487B5ECACD4618F3CCEA88', '李宁广告1', '运动会 足球 &amp;amp;amp;amp;amp;amp;amp;amp; 羽毛球', 9.6, 1, 1, null, null, 1, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (230, '799379F2E27390788DA1A47B5F7F4A4A', '山东运动会', '山东运动会简介', 9.6, 1, 4, null, null, 1, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (231, 'S2017031400000000000000000000539', '来自星星的你1', '《来自星星的你》是由张太侑导演，朴智恩编剧，金秀贤、全智贤、刘仁娜、朴海镇等主演的爱情科幻喜剧，于2013年12月18日在韩国SBS电视台首播。该剧讲述了从外星来到朝鲜时代至400年后的现代的神秘男人都敏俊（金秀贤饰），与身为国民顶级女...', 9, 5, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (232, 'S2017031500000000000000000000555', '生活大爆炸', 'The Big Bang Theory is an American television sitcom created by Chuck Lorre and Bill Prady, both of whom serve as executive producers on the series, along with Steven Molaro. All three also serve as head writers. The show premiered on CBS on September 24, 2007.[3] In March 2014, the show was renewed for three more years through a tenth season, which premiered on September 19, 2016.[4][5]', 2, 5, 1, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (243, 'S2017032000000000000000000000571', '舌尖上的中国', '《舌尖上的中国》是由陈晓卿执导，中国中央电视台出品的一部美食类纪录片。《舌尖上的中国》主题围绕中国人对美食和生活的美好追求，用具体人物故事串联起讲述了中国各地的美食生态。该片于2012年5月14日在CCTV1《魅力记录》栏目首播。', 8, 6, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (244, 'S2017032100000000000000000000572', '老乡会1', '山东老乡会简介2', 3, 6, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (245, 'S2017032100000000000000000000573', '老乡门诊', '山东数字电视台老乡门诊 2', 2, 6, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (246, 'S2017032200000000000000000000574', '快乐大本营', '《快乐大本营》节目内容以游戏为主，辅以歌舞及各种形式的节目，强调贴近生活、贴近观众，以较高品味的娱乐形式给广大观众带来快乐，节目不搞阳春白雪，也不为了取悦个别观众而庸俗化。栏目中有众多的明星出现，但没有追求明星效应，甚至武警战士、下岗女工也请为座上嘉宾，栏目中安排了种类繁多的游戏，不以哗众取宠为目的，着重注重观众的参与，包括现场观众和电视机前观众的参与。', 9, 6, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (252, '11D75C3E61ADBF36F755FA7A868D69F4', 'baogongshengui_tanyinshan.ts', '', '', 7, 1, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (253, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', '金沙江畔', '沙江畔,是由傅超武,导演,主演的剧情,电影。乐视网为您提供金沙江畔在线观看、红军长征途经藏区的故事演员表、影片下载等相关信息,更多精彩内容尽在乐视网。', 7, 7, 3, null, null, 1, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (254, '09CFE2AE19B64A682E2BEA3734123C48', '乾坤福寿镜', '讲的是颍州知府梅俊次的妻子胡氏怀孕，妾徐氏嫉恨非常，诬胡将产妖，梅欲杀妻。丫环寿春领胡氏逃出，在破窑中产一子。途中遇大盗丢失儿子，后此子被人收养，长成得中状元，母子重聚。尚小云在《失子惊疯》一折中，唱做均别具风格', 3, 7, 1, null, null, 3, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (258, 'S2017032900000000000000000000593', 'php教学视频', 'ｐｈｐは、世界で最高の言語です。', null, 8, 6, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (261, 'S2017033100000000000000000000596', '动画片', 'animation 动画片', null, 8, 6, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (264, 'S2017041100000000000000000000603', '广告', '广告简介', null, 8, 1, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (265, '518BC5A6066C54E67ECF5224A04F2F8C', '绿宝石', '言情小说作家琼经常陶醉在自已创造的世界中与外界分离，一日收到妹妹的急电谓遭人绑架，义不容辞赶往南美拯救。原来歹徒的目的是一张她偶然发现的藏宝图，黑白两道都想将宝藏抢到手。琼幸得游侠柯顿相救，历险了种种险阻逃生，却原来整..', 3, 1, 1, null, null, 2, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (266, 'S2017042000000000000000000000608', 'animation', 'animation direcotor', 3, 5, 1, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (267, 'A9D34CA7CBEC64161EB99E5A88E51733', 'Casino Royale', 'Armed with a licence to kill, Secret Agent James Bond sets out on his first mission as 007 and must defeat a weapons dealer in a high stakes game of poker at Casino Royale, but things are not what they seem.', 6, 1, 1, null, null, 2, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (268, '58ADC1F4E5641253F5B86FB581EFFD0C', 'もののけ姫', '『もののけ姫』は、宮崎駿によるスタジオジブリの長編アニメーション映画作品。1997年7月12日公開。宮崎が構想16年、制作に3年をかけた大作であり、興行収入193億円を記録し当時の日本映画の興行記録を塗り替えた。 映画のキャッチコピーは「生きろ」。 ウィキペディア', 8, 1, 6, null, null, 2, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (269, 'C9C1DA14F1BEF370395BB58D375C5FD0', '为奴十二年', '电影讲述一个生活在纽约的自由的黑人，受过教育且已婚。随后遇到两个人，他们许诺在华盛顿帮他找一份工作', 9.6, 1, 1, null, null, 1, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (271, '7DA0A9BDB14B9DCE504837C563E7C021', '温布尔登网球锦标赛', '温布尔登网球锦标赛（Wimbledon Championships，或简称“温网”）是网球运动中最古老和最具声望的赛事，是网球四大满贯之一。温网举办地在英国伦敦郊区的温布尔登，通常举办于6月或7月，是每年度网球大满贯的第3项赛事，排在澳大利亚网球公开赛和法国网球公开赛之后，美国网球公开赛之前，也是四大满贯中唯一的草地比赛。整个赛事通常历时两周，但会因雨延时。男子单打、女子单打、男子双打、女子双打和男女混合双打比赛在不同场地同时进行。温布尔登还举办有男子单打、女子单打、男子双打、女子双打的青年比赛。此外，温布尔登还为退役球员举办特别邀请赛。', null, 4, null, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (272, '5BCBA3BAE6F3D25DB9B1269AC59CCD35', 'swimming.ts', '', null, 4, null, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (273, '0684D30C29E56200D2239A7FC50D7E53', 'Totoro.ts', null, null, 1, null, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (274, 'S2017072100000000000000000000623', '中考语文', '中考语文', null, 8, 3, null, null, null, null, null);
INSERT INTO "main"."MBIS_Server_Media" VALUES (275, 'S2017072100000000000000000000625', '网站建设', '网站建设', null, 8, 0, null, null, null, null, null);

-- ----------------------------
-- Table structure for MBIS_Server_MediaDiscount
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MediaDiscount";
CREATE TABLE "MBIS_Server_MediaDiscount" (
"MediaDiscountID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR,
"OriginalPrice"  INTEGER,
"DateStart"  VARCHAR,
"DateEnd"  VARCHAR,
"DiscountRate"  INTEGER,
"Price"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_MediaDiscount
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (354, '98EF95A6F1487B5ECACD4618F3CCEA88', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (355, '98EF95A6F1487B5ECACD4618F3CCEA88', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (356, '0E4473337AC8B14E0F1E5FA9B427DF7B', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (359, '093DBB914F571623386271C436FB993C', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (360, '8BC02E29BBC9584DD2264DDAFB51FA31', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (361, '8BC02E29BBC9584DD2264DDAFB51FA31', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (362, '04E499965116B22252C558410B3A025A', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (363, '04E499965116B22252C558410B3A025A', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (364, '734CCD42DC810E9AD0012B1FE6836D36', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (365, '734CCD42DC810E9AD0012B1FE6836D36', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (366, 'E2281DCA86A340892C5602C7D920FDF5', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (367, 'E2281DCA86A340892C5602C7D920FDF5', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (368, 'S2016122700000000000000000000480', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (369, '9420DFBAF9264DE215CF257429EEA7EE', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (372, '8BC02E29BBC9584DD2264DDAFB51FA31', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (373, '8BC02E29BBC9584DD2264DDAFB51FA31', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (374, '9420DFBAF9264DE215CF257429EEA7EE', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (375, '00577066B8130B47DBAEF8B889E41B48', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (376, 'F120573BE8305B646D00293E27D496BF', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (377, '04E499965116B22252C558410B3A025A', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (378, '04E499965116B22252C558410B3A025A', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (379, '734CCD42DC810E9AD0012B1FE6836D36', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (380, '734CCD42DC810E9AD0012B1FE6836D36', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (381, 'E2281DCA86A340892C5602C7D920FDF5', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (382, 'E2281DCA86A340892C5602C7D920FDF5', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (383, 'S2016122800000000000000000000491', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (384, 'FC398B1EF3ECE07E71E42D871CD25C7D', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (385, 'FC398B1EF3ECE07E71E42D871CD25C7D', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (386, 'S2017031400000000000000000000539', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (387, 'S2017031500000000000000000000555', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (398, 'S2017032000000000000000000000571', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (399, 'S2017032100000000000000000000572', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (400, 'S2017032100000000000000000000573', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (401, 'S2017032200000000000000000000574', 0, null, null, 0, 0);
INSERT INTO "main"."MBIS_Server_MediaDiscount" VALUES (407, 'S2017042000000000000000000000608', 0, null, null, 0, 0);

-- ----------------------------
-- Table structure for MBIS_Server_MedialinkCountry
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MedialinkCountry";
CREATE TABLE [MBIS_Server_MedialinkCountry] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [CountryID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_MedialinkCountry
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (222, '8BC02E29BBC9584DD2264DDAFB51FA31', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (223, '9420DFBAF9264DE215CF257429EEA7EE', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (224, '04E499965116B22252C558410B3A025A', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (225, 'S2016122800000000000000000000491', 15);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (226, 'FC398B1EF3ECE07E71E42D871CD25C7D', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (227, '98EF95A6F1487B5ECACD4618F3CCEA88', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (228, '7B8D936E2B24DC1038A878D13D5EFB8F', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (229, '799379F2E27390788DA1A47B5F7F4A4A', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (230, 'S2017031400000000000000000000539', 15);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (231, 'S2017031500000000000000000000555', 4);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (243, 'S2017032100000000000000000000572', 0);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (244, 'S2017032100000000000000000000573', 0);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (245, 'S2017032200000000000000000000574', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (251, '11D75C3E61ADBF36F755FA7A868D69F4', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (252, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (253, '09CFE2AE19B64A682E2BEA3734123C48', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (257, 'S2017032900000000000000000000593', 3);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (259, 'S2017033000000000000000000000595', 4);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (260, 'S2017033100000000000000000000596', 3);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (263, 'S2017041100000000000000000000603', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (264, '518BC5A6066C54E67ECF5224A04F2F8C', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (265, 'S2017042000000000000000000000608', 8);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (266, 'S2017032000000000000000000000571', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (267, 'A9D34CA7CBEC64161EB99E5A88E51733', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (268, '58ADC1F4E5641253F5B86FB581EFFD0C', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (269, 'C9C1DA14F1BEF370395BB58D375C5FD0', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (270, '0684D30C29E56200D2239A7FC50D7E53', null);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (271, 'S2017072100000000000000000000623', 1);
INSERT INTO "main"."MBIS_Server_MedialinkCountry" VALUES (272, 'S2017072100000000000000000000625', 0);

-- ----------------------------
-- Table structure for MBIS_Server_MedialinkGenre
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MedialinkGenre";
CREATE TABLE [MBIS_Server_MedialinkGenre] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [GenreID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_MedialinkGenre
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (274, '8BC02E29BBC9584DD2264DDAFB51FA31', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (275, '9420DFBAF9264DE215CF257429EEA7EE', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (276, '04E499965116B22252C558410B3A025A', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (277, 'S2016122800000000000000000000491', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (278, 'FC398B1EF3ECE07E71E42D871CD25C7D', null);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (279, '98EF95A6F1487B5ECACD4618F3CCEA88', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (280, '7B8D936E2B24DC1038A878D13D5EFB8F', null);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (281, '799379F2E27390788DA1A47B5F7F4A4A', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (282, 'S2017031400000000000000000000539', 1);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (283, 'S2017031500000000000000000000555', 3);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (295, '11D75C3E61ADBF36F755FA7A868D69F4', null);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (296, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 13);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (297, '09CFE2AE19B64A682E2BEA3734123C48', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (301, 'S2017032900000000000000000000593', 6);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (303, 'S2017033000000000000000000000595', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (304, 'S2017033100000000000000000000596', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (307, 'S2017041100000000000000000000603', 8);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (308, '518BC5A6066C54E67ECF5224A04F2F8C', 1);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (309, 'S2017042000000000000000000000608', 4);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (310, 'A9D34CA7CBEC64161EB99E5A88E51733', 13);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (311, '58ADC1F4E5641253F5B86FB581EFFD0C', 7);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (312, 'C9C1DA14F1BEF370395BB58D375C5FD0', 15);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (313, '0684D30C29E56200D2239A7FC50D7E53', null);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (314, 'S2017072100000000000000000000623', 8);
INSERT INTO "main"."MBIS_Server_MedialinkGenre" VALUES (315, 'S2017072100000000000000000000625', 0);

-- ----------------------------
-- Table structure for MBIS_Server_MedialinkLanguage
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MedialinkLanguage";
CREATE TABLE [MBIS_Server_MedialinkLanguage] (
  [ID] INTEGER, 
  [OID] VARCHAR, 
  [LanguageID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_MedialinkLanguage
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017031400000000000000000000539', 3);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017031500000000000000000000555', 1);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017032900000000000000000000593', 6);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017033000000000000000000000595', 1);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017033100000000000000000000596', 6);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017041100000000000000000000603', 1);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017042000000000000000000000608', 1);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017072100000000000000000000623', 3);
INSERT INTO "main"."MBIS_Server_MedialinkLanguage" VALUES (null, 'S2017072100000000000000000000625', 0);

-- ----------------------------
-- Table structure for MBIS_Server_MedialinkPeriod
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MedialinkPeriod";
CREATE TABLE [MBIS_Server_MedialinkPeriod] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [PeriodID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_MedialinkPeriod
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_MedialinkTag
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MedialinkTag";
CREATE TABLE [MBIS_Server_MedialinkTag] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [TagID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_MedialinkTag
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (273, '8BC02E29BBC9584DD2264DDAFB51FA31', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (274, '9420DFBAF9264DE215CF257429EEA7EE', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (275, '04E499965116B22252C558410B3A025A', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (276, 'S2016122800000000000000000000491', 1);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (277, 'FC398B1EF3ECE07E71E42D871CD25C7D', null);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (278, '98EF95A6F1487B5ECACD4618F3CCEA88', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (279, '7B8D936E2B24DC1038A878D13D5EFB8F', null);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (280, '799379F2E27390788DA1A47B5F7F4A4A', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (281, 'S2017031400000000000000000000539', 4);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (282, 'S2017031500000000000000000000555', 2);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (294, '11D75C3E61ADBF36F755FA7A868D69F4', null);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (295, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 13);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (296, '09CFE2AE19B64A682E2BEA3734123C48', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (299, '518BC5A6066C54E67ECF5224A04F2F8C', 4);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (300, 'S2017042000000000000000000000608', 2);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (301, 'A9D34CA7CBEC64161EB99E5A88E51733', 2);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (302, '58ADC1F4E5641253F5B86FB581EFFD0C', 16);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (303, 'C9C1DA14F1BEF370395BB58D375C5FD0', 8);
INSERT INTO "main"."MBIS_Server_MedialinkTag" VALUES (304, '0684D30C29E56200D2239A7FC50D7E53', null);

-- ----------------------------
-- Table structure for MBIS_Server_MedialinkYear
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MedialinkYear";
CREATE TABLE [MBIS_Server_MedialinkYear] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [YearID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_MedialinkYear
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (239, '8BC02E29BBC9584DD2264DDAFB51FA31', 2);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (240, '9420DFBAF9264DE215CF257429EEA7EE', 2);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (241, '04E499965116B22252C558410B3A025A', 21);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (242, 'S2016122800000000000000000000491', 1);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (243, 'FC398B1EF3ECE07E71E42D871CD25C7D', null);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (244, '98EF95A6F1487B5ECACD4618F3CCEA88', 15);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (245, '7B8D936E2B24DC1038A878D13D5EFB8F', null);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (246, '799379F2E27390788DA1A47B5F7F4A4A', 8);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (247, 'S2017031400000000000000000000539', 1);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (248, 'S2017031500000000000000000000555', 7);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (260, '11D75C3E61ADBF36F755FA7A868D69F4', null);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (261, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 17);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (262, '09CFE2AE19B64A682E2BEA3734123C48', 14);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (265, '518BC5A6066C54E67ECF5224A04F2F8C', 15);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (266, 'S2017042000000000000000000000608', 23);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (267, 'A9D34CA7CBEC64161EB99E5A88E51733', 8);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (268, '58ADC1F4E5641253F5B86FB581EFFD0C', 15);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (269, 'C9C1DA14F1BEF370395BB58D375C5FD0', 2);
INSERT INTO "main"."MBIS_Server_MedialinkYear" VALUES (270, '0684D30C29E56200D2239A7FC50D7E53', null);

-- ----------------------------
-- Table structure for MBIS_Server_MediaPrice
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MediaPrice";
CREATE TABLE MBIS_Server_MediaPrice
(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    oid TEXT NOT NULL,
    price INTEGER DEFAULT 0 NOT NULL
);

-- ----------------------------
-- Records of MBIS_Server_MediaPrice
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (3, '04E499965116B22252C558410B3A025A', 0);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (4, '8BC02E29BBC9584DD2264DDAFB51FA31', 123);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (5, '518BC5A6066C54E67ECF5224A04F2F8C', 100);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (6, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 100);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (7, '9420DFBAF9264DE215CF257429EEA7EE', 200);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (8, 'E2281DCA86A340892C5602C7D920FDF5', 33);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (9, '734CCD42DC810E9AD0012B1FE6836D36', 100);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (10, 'S2017031400000000000000000000539', 100);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (11, 'S2017042000000000000000000000608', 50);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (12, 'S2017041100000000000000000000603', 90);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (13, 'S2017033100000000000000000000596', 12);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (17, '752CD57A4E74B7E354680DECD4B769BC', 12);
INSERT INTO "main"."MBIS_Server_MediaPrice" VALUES (18, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 90);

-- ----------------------------
-- Table structure for MBIS_Server_MediaSynLog
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MediaSynLog";
CREATE TABLE "MBIS_Server_MediaSynLog" (
"ID"  INTEGER NOT NULL,
"OID"  VARCHAR NOT NULL,
"MediaTypeID"  INTEGER NOT NULL,
"Size"  INTEGER NOT NULL,
"PartID"  INTEGER DEFAULT 0,
"PartNum"  INTEGER DEFAULT 0,
"Status"  INTEGER DEFAULT 0,
"SynMark"  INTEGER DEFAULT 0,
PRIMARY KEY ("ID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_MediaSynLog
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (1, '31B2292BF57C39427BA2CC4C292E0DC6', 4, 8450976, 17, 17, 100, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (2, '986D1FF8A1D3BE4876F531D50E25BAAF', 4, 10991984, 21, 21, 100, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (3, '8BC02E29BBC9584DD2264DDAFB51FA31', 1, 385108224, 735, 735, 88, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (4, '9420DFBAF9264DE215CF257429EEA7EE', 7, 138056672, 264, 264, 100, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (5, '00577066B8130B47DBAEF8B889E41B48', 4, 33451216, 64, 64, 100, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (6, 'F120573BE8305B646D00293E27D496BF', 4, 196378032, 375, 375, 35, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (7, '04E499965116B22252C558410B3A025A', 1, 379308048, 724, 724, 100, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (8, '734CCD42DC810E9AD0012B1FE6836D36', 2, 105071696, 201, 201, 85, 1);
INSERT INTO "main"."MBIS_Server_MediaSynLog" VALUES (9, 'E2281DCA86A340892C5602C7D920FDF5', 2, 106663680, 204, 204, 100, 1);

-- ----------------------------
-- Table structure for MBIS_Server_MediaType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MediaType";
CREATE TABLE [MBIS_Server_MediaType] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [MediaType] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_MediaType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (1, '电影');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (2, '电视剧分集');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (3, '电视节目分集');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (4, '热点推送视频');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (5, '电视剧总集');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (6, '电视节目总集');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (7, '戏曲');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (8, '专题节目总集');
INSERT INTO "main"."MBIS_Server_MediaType" VALUES (9, '专题节目分集');

-- ----------------------------
-- Table structure for MBIS_Server_Mission
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Mission";
CREATE TABLE "MBIS_Server_Mission" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"MissionName"  VARCHAR,
"MissionDescription"  VARCHAR,
"StartDate"  VARCHAR,
"EndDate"  VARCHAR,
"State"  INTEGER,
"VersionID"  INTEGER DEFAULT 1,
"SynVersionID"  INTEGER DEFAULT 0
);

-- ----------------------------
-- Records of MBIS_Server_Mission
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Mission" VALUES (186, 20161227, '', '2016-12-28', '2016-12-31', 0, 2, 2);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (203, '2017/02', '2017/02 classic movie', '2017-02-20', '2017-02-28', 0, 15, 10);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (204, '2017/02', '2017/02 news', '2017-02-20', '2017-02-28', 0, 9, 7);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (205, '2017/02', '2017/02', '2017-02-20', '2017-02-20', 0, 2, 1);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (206, '2017/02', '2017/02 drama', '2017-02-20', '2017-02-20', 0, 2, 1);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (207, '2017/02', 'animation 201702', '2017-02-20', '2017-02-28', 0, 15, 9);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (209, 'news03', '', '2017-03-01', '2017-03-31', 0, 3, 1);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (210, 'show02', '', '2017-02-24', '2017-03-31', 0, 12, 5);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (211, 'drama02', '', '2017-02-24', '2017-03-31', 0, 6, 3);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (212, 'adsf', 'fadsfasd', '2017-03-01', '2017-03-01', 2, 6, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (213, 2017.03, '戏曲动漫2017.03', '2017-03-06', '2017-03-31', 2, 3, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (273, '2017-05-02', 'video.2017-05-02', '2017-05-02', '2017-05-02', 0, 2, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (276, '热点视频2017', '热点视频2017-2018', '2017-07-21', '2018-12-31', 0, 3, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (277, '本期影讯2017', '本期影讯2017', '2017-07-21', '2018-12-31', 0, 3, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (278, '下期影讯2017', '下期影讯2017', '2017-07-21', '2018-12-31', 0, 2, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (279, '动漫天地2017', '动漫天地2017', '2017-07-21', '2018-12-31', 0, 2, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (280, '文化传奇2017', '文化传奇2017', '2017-07-21', '2018-12-31', 0, 1, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (281, '天天好剧', '天天好剧2017', '2017-07-21', '2018-12-31', 0, 3, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (282, '语文2017', '语文2017', '2017-07-21', '2018-12-31', 0, 2, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (283, 201707, 201707, '2017-07-21', '2019-01-01', 0, 2, 0);
INSERT INTO "main"."MBIS_Server_Mission" VALUES (284, 201707, 201707, '2017-07-14', '2018-03-21', 0, 2, 0);

-- ----------------------------
-- Table structure for MBIS_Server_MissionlinkMedia
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MissionlinkMedia";
CREATE TABLE "MBIS_Server_MissionlinkMedia" (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [MissionID] INTEGER, 
  [MediaOID] VARCHAR, 
  [Round] INTEGER, 
  [Priority] INTEGER, 
  [Date] VARCHAR, 
  [State] INTEGER DEFAULT 0);

-- ----------------------------
-- Records of MBIS_Server_MissionlinkMedia
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (914, 276, '00577066B8130B47DBAEF8B889E41B48', 3, 1, '2017-07-21', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (915, 276, 'F120573BE8305B646D00293E27D496BF', 3, 1, '2017-07-21', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (916, 277, '8BC02E29BBC9584DD2264DDAFB51FA31', 3, 1, '2017-07-22', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (917, 277, '04E499965116B22252C558410B3A025A', 3, 1, '2017-07-22', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (918, 278, '518BC5A6066C54E67ECF5224A04F2F8C', 5, 1, '2017-07-21', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (919, 279, '58ADC1F4E5641253F5B86FB581EFFD0C', 1, 1, '2017-07-23', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (920, 281, '734CCD42DC810E9AD0012B1FE6836D36', 2, 1, '2017-07-25', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (921, 281, 'E2281DCA86A340892C5602C7D920FDF5', 2, 1, '2017-07-25', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (922, 282, '31B2292BF57C39427BA2CC4C292E0DC6', 5, 1, '2017-07-26', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (923, 283, '31B2292BF57C39427BA2CC4C292E0DC6', 1, 1, '2017-07-22', 0);
INSERT INTO "main"."MBIS_Server_MissionlinkMedia" VALUES (924, 284, 'A00B796120BA02243CE152801DCD7F16', 1, 1, '2017-07-21', 0);

-- ----------------------------
-- Table structure for MBIS_Server_MissionPrice
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MissionPrice";
CREATE TABLE "MBIS_Server_MissionPrice" (
"MissionPriceID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"MissionID"  INTEGER,
"Price"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_MissionPrice
-- ----------------------------
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (173, 181, 0);
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (174, 182, 0);
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (175, 183, 0);
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (176, 184, 0);
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (177, 185, 0);
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (178, 186, 0);
INSERT INTO "main"."MBIS_Server_MissionPrice" VALUES (179, 187, 0);

-- ----------------------------
-- Table structure for MBIS_Server_MissionUsed
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_MissionUsed";
CREATE TABLE [MBIS_Server_MissionUsed] (
  [MissionUsedID] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
  [CustomerID] INTEGER NOT NULL, 
  [MissionID] INTEGER NOT NULL, 
  [MissionName] VARCHAR NOT NULL, 
  [Price] INTEGER NOT NULL, 
  [Date] VARCHAR NOT NULL);

-- ----------------------------
-- Records of MBIS_Server_MissionUsed
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Movie
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Movie";
CREATE TABLE [MBIS_Server_Movie] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [Director] VARCHAR, 
  [Actor] VARCHAR, 
  [Runtime] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_Movie
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Movie" VALUES (151, '8BC02E29BBC9584DD2264DDAFB51FA31', 1234, 'lining_actors', 12);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (152, '04E499965116B22252C558410B3A025A', '&lt;script&gt;alert(1)&lt;/script&gt;', 'qqqq1', 66);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (153, 'FC398B1EF3ECE07E71E42D871CD25C7D', null, null, null);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (154, '98EF95A6F1487B5ECACD4618F3CCEA88', '李宁', 'lining_actors', 12);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (155, '799379F2E27390788DA1A47B5F7F4A4A', 'qwer', 134, 1234);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (158, '518BC5A6066C54E67ECF5224A04F2F8C', '罗伯特·泽米吉斯', '迈克尔·道格拉斯', 123);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (159, 'A9D34CA7CBEC64161EB99E5A88E51733', 'Martin Campbell', 'Daniel Craig/Eva Green/Judi Dench ', 120);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (160, '58ADC1F4E5641253F5B86FB581EFFD0C', '宮崎 駿', ' 石田 ゆり子、 松田 洋治、 田中 裕子、 美輪 明宏、 島本 須美、 もっと見る', 135);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (161, 'C9C1DA14F1BEF370395BB58D375C5FD0', 1234, 'lining_actors', 12);
INSERT INTO "main"."MBIS_Server_Movie" VALUES (162, '0684D30C29E56200D2239A7FC50D7E53', null, null, null);

-- ----------------------------
-- Table structure for MBIS_Server_OnDemand
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_OnDemand";
CREATE TABLE [MBIS_Server_OnDemand] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [STBID] VARCHAR, 
  [OID] VARCHAR, 
  [Date] VARCHAR, 
  [Time] VARCHAR, 
  [State] INTEGER, 
  [PushID] INTEGER, 
  [Price] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_OnDemand
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_OnLineMedia
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_OnLineMedia";
CREATE TABLE "MBIS_Server_OnLineMedia" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR,
"OnLineDate"  VARCHAR,
"OffLineDate"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_OnLineMedia
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Opera
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Opera";
CREATE TABLE "MBIS_Server_Opera" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  STRING,
"Director"  STRING,
"Actor"  STRING,
"Runtime"  INT
);

-- ----------------------------
-- Records of MBIS_Server_Opera
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Opera" VALUES (5, '9420DFBAF9264DE215CF257429EEA7EE', '自导', '自演', 45);
INSERT INTO "main"."MBIS_Server_Opera" VALUES (6, '7B8D936E2B24DC1038A878D13D5EFB8F', null, null, null);
INSERT INTO "main"."MBIS_Server_Opera" VALUES (7, '11D75C3E61ADBF36F755FA7A868D69F4', '', '', '');
INSERT INTO "main"."MBIS_Server_Opera" VALUES (8, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', '傅超武', '王冠丽', 50);
INSERT INTO "main"."MBIS_Server_Opera" VALUES (9, '09CFE2AE19B64A682E2BEA3734123C48', '乾坤福寿镜导演', '乾坤福寿镜演员', 45);

-- ----------------------------
-- Table structure for MBIS_Server_Package
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Package";
CREATE TABLE 'MBIS_Server_Package' (
'ID'  INTEGER PRIMARY KEY AUTOINCREMENT,
'PackageName'  VARCHAR,
'PackageDescription'  VARCHAR,
'Thumb'  VARCHAR,
'UpdateCycleTypeID'  INTEGER,
'Price'  INTEGER,
'StartTime'  VARCHAR,
'OriginalDir'  VARCHAR,
'ParentID' INTEGER DEFAULT 0,  -- 父业务包ID，默认为0, 表示无父业务包
'IsNode' TINYINT DEFAULT 1,    -- 是否是叶子节点，默认为1,  0 - 否, 1 - 是
'PackageTypeID'  INTEGER DEFAULT 0,
'PackageTemplateID' INTEGER
, ChargeTypeID INT DEFAULT 3 NULL);

-- ----------------------------
-- Records of MBIS_Server_Package
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Package" VALUES (135, '热点视频', '本期影讯为您提供最新最热的新闻视频', 'package135thumb/135_zuqiu.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package135/135_zuqiu.jpg', 134, 1, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (136, '热点视频', '本期影讯为您提供最新最热的新闻视频', 'package136thumb/136_zuqiu.jpg', 3, 0, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package136/136_zuqiu.jpg', 0, 1, 1, 1, 0);
INSERT INTO "main"."MBIS_Server_Package" VALUES (137, '本期影讯', '本期影讯部分免费', 'package137thumb/137_movie.jpg', 3, 100, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package137/137_movie.jpg', 0, 1, 2, 2, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (138, '下期影讯', '下期影讯', 'package138thumb/138_next_movie.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package138/138_next_movie.jpg', 0, 1, 1, 2, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (139, '动漫天地', '动漫天地', 'package139thumb/139_frozen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package139/139_frozen.jpg', 0, 1, 2, 2, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (140, '文化传奇', '文化传奇 综艺节目', 'package140thumb/140_shejian.jpg', 3, 0, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package140/140_shejian.jpg', 0, 1, 1, 4, 0);
INSERT INTO "main"."MBIS_Server_Package" VALUES (141, '天天好剧', '天天好剧', 'package141thumb/141_laizixingxingdeni.jpg', 3, 100, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package141/141_laizixingxingdeni.jpg', 0, 1, 1, 3, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (142, '名师讲堂', '名师讲堂', 'package142thumb/142_mingshijiangtang.jpg', 3, 12341, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package142/142_mingshijiangtang.jpg', 0, 0, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (143, '新东方教育', '新东方教育', 'package143thumb/143_xindongfang.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package143/143_xindongfang.jpg', 142, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (144, '高斯教育', '高斯教育', 'package144thumb/144_gaosijiaoyu.jpg', 3, 1234, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package144/144_gaosijiaoyu.jpg', 142, 0, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (145, '学而思教育', '学而思教育', 'package145thumb/145_xueersi.jpg', 3, 888, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package145/145_xueersi.jpg', 142, 0, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (146, '语文', '语文', 'package146thumb/146_yuwen.jpg', 3, 222, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package146/146_yuwen.jpg', 143, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (147, '数学', '数学', 'package147thumb/147_shuxue.jpg', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package147/147_shuxue.jpg', 143, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (148, '英语', '英语', 'package148thumb/148_english.jpg', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package148/148_english.jpg', 143, 0, 0, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (149, '物理', '物理', 'package149thumb/149_physics.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package149/149_physics.jpg', 143, 0, 0, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (150, '地理', '地理', 'package150thumb/150_dili.jpg', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package150/150_dili.jpg', 143, 0, 0, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (151, '政治', '政治', 'package151thumb/151_politics.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package151/151_politics.jpg', 143, 0, 0, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (152, '中考语文', '中考语文', 'package152thumb/152_zhongkaoyuwen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package152/152_zhongkaoyuwen.jpg', 146, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (153, '高一语文', '高一语文', 'package153thumb/153_gaoyiyuwen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package153/153_gaoyiyuwen.jpg', 146, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (154, '高二语文', '高二语文', 'package154thumb/154_gaoyiyuwen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package154/154_gaoyiyuwen.jpg', 146, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (155, '张桥林', '张桥林', 'package155thumb/155_zhangqiaolin.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package155/155_zhangqiaolin.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (156, '博乐', '博乐', 'package156thumb/156_801.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package156/156_801.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (157, '旺仔', '旺仔', 'package157thumb/157_2.png', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package157/157_2.png', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (158, '刘珊', '刘珊', 'package158thumb/158_8.png', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package158/158_8.png', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (159, '张晓莉', '张晓莉', 'package159thumb/159_803.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package159/159_803.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (160, '吴杰', '吴杰', 'package160thumb/160_wujie.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package160/160_wujie.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (161, '天宇', '天宇', 'package161thumb/161_tianyu.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package161/161_tianyu.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (162, '刘丹妮', '刘丹妮', 'package162thumb/162_liudanni.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package162/162_liudanni.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (163, '申诉', '申诉', 'package163thumb/163_802.jpg', 3, 222, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package163/163_802.jpg', 152, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (164, 'IT培训', 'IT培训', 'package164thumb/164_nodejs.jpg', 3, 8888, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package164/164_nodejs.jpg', 0, 0, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (165, '北大青鸟', '北大青鸟', 'package165thumb/165_beidaqingniao.jpg', 3, 18888, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package165/165_beidaqingniao.jpg', 164, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (166, '达内教育', '达内教育', 'package166thumb/166_danei.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package166/166_danei.jpg', 164, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (167, '极客学院', '极客学院', 'package167thumb/167_jikexueyuan.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package167/167_jikexueyuan.jpg', 164, 0, 3, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (168, '前端开发', '', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (169, '后端开发', '', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (170, '人工智能', '', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (171, '智能硬件', '智能硬件', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (172, '移动开发', '', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (173, '物联网', '物联网', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (174, '设计', '设计', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (175, '产品', '', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (176, '测试', '', null, 3, '', null, null, 165, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (177, 'HTML', '', null, 3, '', null, null, 168, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (178, 'CSS', '', null, 3, '', null, null, 168, 0, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (179, 'javascript', 'javascript', null, 3, '', null, null, 168, 0, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (180, '张鹏', '张鹏', null, 3, '', null, null, 177, 1, 1, 6, 1);
INSERT INTO "main"."MBIS_Server_Package" VALUES (181, '韩顺平', '', null, 3, '', null, null, 177, 1, 1, 1, 1);

-- ----------------------------
-- Table structure for MBIS_Server_PackageDiscount
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PackageDiscount";
CREATE TABLE [MBIS_Server_PackageDiscount] (
  [PackageDiscountID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [PackageID] INTEGER, 
  [CustomerGroupID] INTEGER, 
  [Price] INTEGER, 
  [DateStart] VARCHAR, 
  [DateEnd] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_PackageDiscount
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_PackagelinkMission
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PackagelinkMission";
CREATE TABLE [MBIS_Server_PackagelinkMission] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [PackageID] INTEGER, 
  [MissionID] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_PackagelinkMission
-- ----------------------------
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (203, 18, 203);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (204, 17, 204);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (205, 20, 205);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (206, 22, 206);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (207, 26, 207);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (209, 17, 209);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (210, 20, 210);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (211, 22, 211);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (212, 38, 212);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (213, 26, 213);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (275, 136, 276);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (276, 137, 277);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (277, 138, 278);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (278, 139, 279);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (279, 140, 280);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (280, 141, 281);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (281, 146, 282);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (282, 155, 283);
INSERT INTO "main"."MBIS_Server_PackagelinkMission" VALUES (283, 180, 284);

-- ----------------------------
-- Table structure for MBIS_Server_PackagePrice
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PackagePrice";
CREATE TABLE "MBIS_Server_PackagePrice" (
"ID"             INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"PackageID"      INTEGER,
"Price"          INTEGER,
"ChargeTypeID"   INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_PackagePrice
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_PackageTemplate
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PackageTemplate";
CREATE TABLE 'MBIS_Server_PackageTemplate' (
'ID'  INTEGER PRIMARY KEY AUTOINCREMENT,
'PackageTemplateID' INTEGER,
'Description'  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_PackageTemplate
-- ----------------------------
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (1, 0, '未知');
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (2, 1, '新闻类');
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (3, 2, '电影戏曲类');
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (4, 3, '电视剧类');
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (5, 4, '综艺节目类');
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (6, 5, '专题节目类');
INSERT INTO "main"."MBIS_Server_PackageTemplate" VALUES (7, 6, '教育类');

-- ----------------------------
-- Table structure for MBIS_Server_PackageType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PackageType";
CREATE TABLE "MBIS_Server_PackageType" (
"ID"             INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"PackageTypeID"  INTEGER,
"DescripTion"    VARCHAR,
CONSTRAINT "PackageTypeID" UNIQUE ("PackageTypeID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_PackageType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_PackageType" VALUES (1, 0, '未知');
INSERT INTO "main"."MBIS_Server_PackageType" VALUES (2, 1, '节目阅览');
INSERT INTO "main"."MBIS_Server_PackageType" VALUES (3, 2, '影视院线');
INSERT INTO "main"."MBIS_Server_PackageType" VALUES (4, 3, '业务推送');

-- ----------------------------
-- Table structure for MBIS_Server_Path
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Path";
CREATE TABLE "MBIS_Server_Path" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR(64),
"URL"  VARCHAR,
"Asset_ID"  VARCHAR,
"MediaTypeID"  INTEGER,
"Asset_Name"  VARCHAR,
"Size"  INT64 NOT NULL DEFAULT 0,
"Date"  VARCHAR,
"State"  INTEGER,
"ContentType"  INTEGER NOT NULL DEFAULT 1
);

-- ----------------------------
-- Records of MBIS_Server_Path
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Path" VALUES (484, '8BC02E29BBC9584DD2264DDAFB51FA31', '12yearaslave.ts', null, 1, '为奴十二年', 385108224, '2016-12-27', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (485, '9420DFBAF9264DE215CF257429EEA7EE', 'daopiyangfan.ts', null, 7, '刀劈杨藩1', 138056672, '2016-12-27', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (486, '00577066B8130B47DBAEF8B889E41B48', 'tiaoshui.ts', null, 4, '国际泳联-跳水1', 33451216, '2016-12-28', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (487, 'F120573BE8305B646D00293E27D496BF', 'ziyouyong.ts', null, 4, '国际泳联-自由泳', 196378032, '2016-12-28', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (488, '04E499965116B22252C558410B3A025A', 'A.Simple.Noodle.Story.ts', null, 1, '三枪拍案惊奇', 379308048, '2016-12-28', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (535, '7DA0A9BDB14B9DCE504837C563E7C021', 'wenwang.ts', null, 4, '温布尔登网球锦标赛', 8450412, '2017-03-09', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (537, '734CCD42DC810E9AD0012B1FE6836D36', 'Stars.E01.ts', null, 2, '来自星星的你第一集１', 105071696, '2017-03-10', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (538, 'E2281DCA86A340892C5602C7D920FDF5', 'Stars.E02.ts', null, 2, '来自星星的你第2集', 106663680, '2017-03-10', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (554, 'S2017031400000000000000000000539', null, null, 5, '来自星星的你1', 0, '2017-03-14', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (555, 'S2017031500000000000000000000555', null, null, 5, '生活大爆炸', 0, '2017-03-15', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (567, 'CF47F40C5B9676F1CEE45F1021BA9800', 'duizhengxiayao_shouldok.ts', null, 3, '对症下药', 97891788, '2017-03-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (569, '287A3939C180E1F5B06A5D1B16140B85', 'kangwangyaocanyanzhong.ts', null, 3, '抗旺药残太严重', 92814660, '2017-03-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (570, '0B4A4FDA862E6B8DD304E3C880EA6EE5', 'yimudiseegood.ts', null, 3, '老乡帮老乡一亩地见分晓1', 93723828, '2017-03-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (571, 'S2017032000000000000000000000571', null, null, 6, '舌尖上的中国', 0, '2017-03-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (572, 'S2017032100000000000000000000572', null, null, 6, '老乡会1', 0, '2017-03-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (573, 'S2017032100000000000000000000573', null, null, 6, '老乡门诊', 0, '2017-03-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (574, 'S2017032200000000000000000000574', null, null, 6, '快乐大本营', 0, '2017-03-22', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (585, 'FC1D25E6626669E91897D2D5083AEFF8', 'jiangzuo_enter_weicheng.ts', null, 3, '老乡讲座进潍城 ', 78520268, '2017-03-23', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (586, '11D75C3E61ADBF36F755FA7A868D69F4', 'baogongshengui_tanyinshan.ts', null, 7, 'baogongshengui_tanyinshan.ts', 659479184, '2017-03-24', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (587, 'E8F1FCC9F5B9C7177D75110F0EAE17E1', 'jinshajaingpan.ts', null, 7, '金沙江畔', 322080472, '2017-03-24', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (588, '09CFE2AE19B64A682E2BEA3734123C48', 'qiankunfushou.ts', null, 7, '乾坤福寿镜', 128185168, '2017-03-24', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (593, 'S2017032900000000000000000000593', null, null, 8, 'php教学视频', 0, '2017-03-29', null, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (596, 'S2017033100000000000000000000596', null, null, 8, '动画片', 0, '2017-03-31', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (597, '98EF95A6F1487B5ECACD4618F3CCEA88', 'lining.ts', null, 9, '李宁广告', 4499968, '2017-03-31', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (598, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 'Huawei.ts', null, 9, '华为手机', 7959544, '2017-04-01', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (600, '752CD57A4E74B7E354680DECD4B769BC', 'animation.ts', null, 9, '示例专题节目动画片', 77192800, '2017-04-01', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (602, '5BCBA3BAE6F3D25DB9B1269AC59CCD35', 'swimming.ts', null, 4, 'swimming.ts', 14140608, '2017-04-06', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (603, 'S2017041100000000000000000000603', null, null, 8, '广告', 0, '2017-04-11', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (604, '518BC5A6066C54E67ECF5224A04F2F8C', 'Romancing.the.Stone.ts', null, 1, '绿宝石', 385749680, '2017-04-19', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (605, 'E05C63468AC7991F54C68B8C4516429C', 'Stars.E03.ts', null, 2, '来自星星的你(3)', 111097472, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (606, '43DF00415D3450BA42BEC5644F3C153F', 'Stars.E04.ts', null, 2, '来自星星的你4', 107760096, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (607, '57DD8412C26BF9DE9ACC186233E153E8', 'animation_2.ts', null, 2, '动画片2', 97138848, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (608, 'S2017042000000000000000000000608', null, null, 5, 'animation', 0, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (609, '676513298BD859BF32AF1411F8E50C86', 'A.Bite.of.China.1.01.ts', null, 3, '舌尖上的中国(1)', 109208448, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (610, 'E46E6A25448315083AB12A6F8CCFDD9C', 'A.Bite.of.China.2.01.ts', null, 3, '舌尖上的中国2', 108529392, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (611, '62D91C030B1F94ABAC7EF29317600D56', 'ANTA_English.ts', null, 9, '安踏体育', 8859876, '2017-04-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (612, '753865F1EAA65D5861CB00CCE7E6BDF4', 'kebiSprite.ts', null, 9, 'kebiSprite.ts', 10275140, '2017-04-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (613, 'A9D34CA7CBEC64161EB99E5A88E51733', 'Casino_Royale.ts', null, 1, 'Casino Royale', 424222000, '2017-04-25', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (614, '58ADC1F4E5641253F5B86FB581EFFD0C', 'Princess.Mononoke.ts', null, 1, 'もののけ姫', 429041380, '2017-04-25', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (615, 'C9C1DA14F1BEF370395BB58D375C5FD0', 'shuaiqin.ts', null, 1, '为奴十二年', 851670832, '2017-04-26', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (619, 'A02CD63A26E3103B13A546BA5121923C', 'luhu.ts', null, 9, 'luhu.ts', 25824432, '2017-05-17', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (620, '32EABE4CE88CD10107BA0E78854A462E', 'BMW.ts', null, 4, 'BMW.ts', 18536048, '2017-07-20', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (621, '0684D30C29E56200D2239A7FC50D7E53', 'Totoro.ts', null, 1, 'Totoro.ts', 431575808, '2017-07-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (622, '31B2292BF57C39427BA2CC4C292E0DC6', 'wenwang.ts', null, 9, '中考语文1', 8450976, '2017-07-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (623, 'S2017072100000000000000000000623', null, null, 8, '中考语文', 0, '2017-07-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (624, 'A00B796120BA02243CE152801DCD7F16', 'petkit.ts', null, 9, '征战项目开发布局搭建', 7102076, '2017-07-21', 0, 1);
INSERT INTO "main"."MBIS_Server_Path" VALUES (625, 'S2017072100000000000000000000625', null, null, 8, '网站建设', 0, '2017-07-21', 0, 1);

-- ----------------------------
-- Table structure for MBIS_Server_Pay
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Pay";
CREATE TABLE "MBIS_Server_Pay" (
"PayID"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"BillID"  INTEGER NOT NULL,
"CustomerID"  INTEGER NOT NULL,
"PayMethod"  INTEGER NOT NULL,
"Total"  INTEGER NOT NULL,
"PayTime"  VARCHAR NOT NULL,
"Abstract"  VARCHAR NOT NULL
);

-- ----------------------------
-- Records of MBIS_Server_Pay
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_Period
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Period";
CREATE TABLE [MBIS_Server_Period] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [Period] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Period
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Period" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_Period" VALUES (1, '古代');
INSERT INTO "main"."MBIS_Server_Period" VALUES (2, '明清');
INSERT INTO "main"."MBIS_Server_Period" VALUES (3, '民国');
INSERT INTO "main"."MBIS_Server_Period" VALUES (4, '解放战争');
INSERT INTO "main"."MBIS_Server_Period" VALUES (5, '抗日战争');
INSERT INTO "main"."MBIS_Server_Period" VALUES (6, '五十年代');
INSERT INTO "main"."MBIS_Server_Period" VALUES (7, '六十年代');
INSERT INTO "main"."MBIS_Server_Period" VALUES (8, '改革开放');
INSERT INTO "main"."MBIS_Server_Period" VALUES (9, '当代');
INSERT INTO "main"."MBIS_Server_Period" VALUES (10, '未来');

-- ----------------------------
-- Table structure for MBIS_Server_Plan
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Plan";
CREATE TABLE [MBIS_Server_Plan] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [OID] VARCHAR, 
  [ParentOID] VARCHAR, 
  [ChannelID] INTEGER, 
  [MediaTypeID] INTEGER, 
  [Date] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Plan
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_PushDisplay
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PushDisplay";
CREATE TABLE "MBIS_Server_PushDisplay" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"TotalRate"  INTEGER,
"CpuRate"  INTEGER,
"RamRate"  INTEGER,
"HDiskRate"  INTEGER,
"BCNState"  INTEGER,
"IPState"  INTEGER,
"BBCounter"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_PushDisplay
-- ----------------------------
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (1, 0, 0, 0, 0, 20, 20, 28);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (2, 0, 0, 0, 0, 20, 20, 28);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (3, 0, 0, 0, 0, 12, 12, 44);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (4, 0, 0, 0, 0, 17, 17, 36);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (5, 0, 0, 0, 0, 21, 21, 27);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (6, 0, 0, 0, 0, 13, 13, 43);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (7, 0, 0, 0, 0, 18, 18, 35);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (8, 0, 0, 0, 0, 22, 22, 26);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (9, 0, 0, 0, 0, 14, 14, 42);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (10, 0, 0, 0, 0, 18, 18, 33);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (11, 0, 0, 0, 0, 23, 23, 50);
INSERT INTO "main"."MBIS_Server_PushDisplay" VALUES (12, 3789, 30, 8, 67, 15, 15, 41);

-- ----------------------------
-- Table structure for MBIS_Server_PushStatus
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_PushStatus";
CREATE TABLE "MBIS_Server_PushStatus" (
"ID"  INTEGER NOT NULL,
"OID"  VARCHAR NOT NULL,
"PushTime"  INTEGER NOT NULL DEFAULT 0,
"RoundCount"  INTEGER NOT NULL DEFAULT 0,
"State"  INTEGER NOT NULL DEFAULT 0,
"Ratio"  INTEGER NOT NULL DEFAULT 0,
"PackageID"  INTEGER,
"MissionID"  INTEGER,
"MissionLinkMediaID"  INTEGER,
PRIMARY KEY ("ID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_PushStatus
-- ----------------------------
INSERT INTO "main"."MBIS_Server_PushStatus" VALUES (1, '04E499965116B22252C558410B3A025A', 0, 1, 1, 9012, 50, 187, 869);
INSERT INTO "main"."MBIS_Server_PushStatus" VALUES (2, '734CCD42DC810E9AD0012B1FE6836D36', 0, 2, 1, 10000, 51, 185, 864);

-- ----------------------------
-- Table structure for MBIS_Server_Schedule
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Schedule";
CREATE TABLE [MBIS_Server_Schedule] (
  [Name] VARCHAR, 
  [ID] INTEGER, 
  [Type] INTEGER, 
  [PeriodSec] INTEGER, 
  [RefHour] INTEGER, 
  [RefMin] INTEGER, 
  [RefSec] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_Schedule
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Schedule" VALUES ('SelfCheck', 1, 1, 0, 0, 1, 0);
INSERT INTO "main"."MBIS_Server_Schedule" VALUES ('TaskMonitor', 2, 0, 13, 0, 0, 0);
INSERT INTO "main"."MBIS_Server_Schedule" VALUES ('SendUi', 3, 1, 0, 0, 2, 0);

-- ----------------------------
-- Table structure for MBIS_Server_Series
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Series";
CREATE TABLE "MBIS_Server_Series" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"OID"  VARCHAR,
"Episodes"  INTEGER,
"Director"  VARCHAR,
"Actor"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_Series
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Series" VALUES (38, 'S2016122800000000000000000000491', 20, '金大中', '全秀贤');
INSERT INTO "main"."MBIS_Server_Series" VALUES (39, 'S2017031400000000000000000000539', 21, '张太侑', '金秀贤、全智贤、刘仁娜、朴海镇');
INSERT INTO "main"."MBIS_Server_Series" VALUES (40, 'S2017031500000000000000000000555', 24, 'Chuck Lorre and Bill Prady', '吉姆·帕森斯, 卡蕾·库奥科');
INSERT INTO "main"."MBIS_Server_Series" VALUES (52, 'S2017042000000000000000000000608', 2, 'animation direcotor', 'animation actors');

-- ----------------------------
-- Table structure for MBIS_Server_SeriesEpisode
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_SeriesEpisode";
CREATE TABLE "MBIS_Server_SeriesEpisode" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"SeriesOID"  VARCHAR,
"EpisodeOID"  VARCHAR,
"Title"  VARCHAR,
"Introduction"  VARCHAR,
"Actor"  VARCHAR,
"Runtime"  INTEGER,
"EpisodeIndex"  INTEGER,
"Thumb"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_SeriesEpisode
-- ----------------------------
INSERT INTO "main"."MBIS_Server_SeriesEpisode" VALUES (29, 'S2017031400000000000000000000539', '734CCD42DC810E9AD0012B1FE6836D36', '来自星星的你第一集１', '１ 来自星星的你 谱写传奇恋曲据朝鲜王朝记录,光海一年(1609),江陵山区上空曾出现不明飞行物。正值此时,十五岁的少女千颂伊在仆人的陪同下前往自己的夫君...1', null, 40, 1, null);
INSERT INTO "main"."MBIS_Server_SeriesEpisode" VALUES (33, 'S2017031400000000000000000000539', 'E2281DCA86A340892C5602C7D920FDF5', '来自星星的你第2集', '第2集 - 颂伊敏俊摩擦不断 世美钟情初恋辉景 时光倒退到四百年前，都敏俊来到一个集市闲逛，一个赌徒押上给女儿买药的钱，参与下注。都敏俊于心不忍，出手帮他赢了奖金。千颂伊在书房抄写经书，突然出现一个黑衣人掳走了她，将她带至山林欲行不轨，都敏俊利用自己的超能力，救下了她。', null, 45, 2, null);
INSERT INTO "main"."MBIS_Server_SeriesEpisode" VALUES (35, 'S2017031400000000000000000000539', 'E05C63468AC7991F54C68B8C4516429C', '来自星星的你(3)', '来自星星的你(3)
xxxx', null, 45, 3, null);
INSERT INTO "main"."MBIS_Server_SeriesEpisode" VALUES (36, 'S2017031400000000000000000000539', '43DF00415D3450BA42BEC5644F3C153F', '来自星星的你4', '来自星星的你4 第4集asdfdas', null, 45, 4, null);
INSERT INTO "main"."MBIS_Server_SeriesEpisode" VALUES (37, 'S2017042000000000000000000000608', '57DD8412C26BF9DE9ACC186233E153E8', '动画片2', 'animation 简介', null, 2, 2, null);

-- ----------------------------
-- Table structure for MBIS_Server_ServiceType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_ServiceType";
CREATE TABLE "MBIS_Server_ServiceType" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"ServiceName"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_ServiceType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_ServiceType" VALUES (0, '点播');
INSERT INTO "main"."MBIS_Server_ServiceType" VALUES (1, '订阅');
INSERT INTO "main"."MBIS_Server_ServiceType" VALUES (2, '免费');

-- ----------------------------
-- Table structure for MBIS_Server_Special
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Special";
CREATE TABLE "MBIS_Server_Special" (
"ID"  INTEGER,
"OID"  VARCHAR,
"Episodes"  INTEGER,
PRIMARY KEY ("ID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_Special
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Special" VALUES (3, 'S2017032900000000000000000000593', 12);
INSERT INTO "main"."MBIS_Server_Special" VALUES (6, 'S2017033100000000000000000000596', 100);
INSERT INTO "main"."MBIS_Server_Special" VALUES (7, 'S2017041100000000000000000000603', 10);
INSERT INTO "main"."MBIS_Server_Special" VALUES (8, 'S2017072100000000000000000000623', 3);
INSERT INTO "main"."MBIS_Server_Special" VALUES (9, 'S2017072100000000000000000000625', '');

-- ----------------------------
-- Table structure for MBIS_Server_SpecialEpisode
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_SpecialEpisode";
CREATE TABLE "MBIS_Server_SpecialEpisode" (
"ID"  INTEGER,
"SpecialOID"  VARCHAR,
"EpisodeOID"  VARCHAR,
"EpisodeIndex"  INTEGER, introduction TEXT NULL,
PRIMARY KEY ("ID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_SpecialEpisode
-- ----------------------------
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (4, null, '799379F2E27390788DA1A47B5F7F4A4A', null, null);
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (6, 'S2017041100000000000000000000603', '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 1, '2016年10月,华为手机迅速超过三星,夺得芬兰市场占有率的头把交椅。 今天,漫步在赫尔辛基,你根本躲不开华为的广告牌。芬兰的一支顶级冰球队也配上了... ');
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (7, 'S2017033100000000000000000000596', '752CD57A4E74B7E354680DECD4B769BC', 1, '示例专题节目动画片');
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (8, 'S2017041100000000000000000000603', '62D91C030B1F94ABAC7EF29317600D56', 1, '安踏体育简介');
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (9, null, '753865F1EAA65D5861CB00CCE7E6BDF4', null, null);
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (10, 'S2017041100000000000000000000603', '98EF95A6F1487B5ECACD4618F3CCEA88', '', '李宁广告简介');
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (11, null, 'A02CD63A26E3103B13A546BA5121923C', null, null);
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (12, 'S2017072100000000000000000000623', '31B2292BF57C39427BA2CC4C292E0DC6', '', '中考语文');
INSERT INTO "main"."MBIS_Server_SpecialEpisode" VALUES (13, 'S2017072100000000000000000000625', 'A00B796120BA02243CE152801DCD7F16', '', '征战项目开发布局搭建');

-- ----------------------------
-- Table structure for MBIS_Server_SpecialLinkAttr
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_SpecialLinkAttr";
CREATE TABLE MBIS_Server_SpecialLinkAttr (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  "oid" text(32) not null,
  "attrid" integer not null,
  "attrval" text(32) not null
);

-- ----------------------------
-- Records of MBIS_Server_SpecialLinkAttr
-- ----------------------------
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (5, 'S2017032900000000000000000000592', 5, '数学');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (6, 'S2017032900000000000000000000592', 4, '家教');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (78, '169E832C168E4C85BC5ECD717657D04C', 2, '商业');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (79, '169E832C168E4C85BC5ECD717657D04C', 3, '广告');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (97, 'S2017033000000000000000000000595', 2, '广告1');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (98, 'S2017033000000000000000000000595', 17, '视频');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (104, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 2, '通信');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (105, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 3, '手机');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (106, '4FCAF5DE62AD92911EDEE6CF67E0E4DA', 16, '海外');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (118, 'S2017041100000000000000000000603', 2, '广告');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (119, 'S2017041100000000000000000000603', 18, 'orc1');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (124, 'S2017032900000000000000000000593', 4, 'php');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (125, 'S2017032900000000000000000000593', 2, '互联网');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (136, 'S2017033100000000000000000000596', 14, '儿童');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (137, 'S2017033100000000000000000000596', 3, '漫画');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (140, '62D91C030B1F94ABAC7EF29317600D56', 2, '体育');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (142, '752CD57A4E74B7E354680DECD4B769BC', 3, '动画片');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (146, '98EF95A6F1487B5ECACD4618F3CCEA88', 2, '体育');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (147, '98EF95A6F1487B5ECACD4618F3CCEA88', 3, '广告');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (148, 'S2017072100000000000000000000623', 5, '语文');
INSERT INTO "main"."MBIS_Server_SpecialLinkAttr" VALUES (151, '31B2292BF57C39427BA2CC4C292E0DC6', 5, '语文');

-- ----------------------------
-- Table structure for MBIS_Server_TableField
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_TableField";
CREATE TABLE [MBIS_Server_TableField] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [TableName] VARCHAR, 
  [Name] VARCHAR, 
  [DisplayName] VARCHAR, 
  [Sequence] INTEGER);

-- ----------------------------
-- Records of MBIS_Server_TableField
-- ----------------------------
INSERT INTO "main"."MBIS_Server_TableField" VALUES (1, 'Media', 'OID', null, 1);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (2, 'Media', 'Title', '片名', 2);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (3, 'Media', 'Introduction', '简介', 3);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (4, 'Movie', 'Director', '导演', 8);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (5, 'Movie', 'Cast', '演员', 7);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (6, 'Movie', 'Year', '上映年份', 6);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (9, 'Movie', 'Runtime', '片长', 10);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (14, 'Media', 'Tag', '标签', 4);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (15, 'Media', 'Rating', '评分', 5);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (16, 'Media', 'Country', '国家/地区', 6);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (17, 'series', 'Cast', '演员', 7);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (18, 'Series', 'Episodes', '集数', 1);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (19, 'Series', 'Director', '导演', 2);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (22, 'Series', 'Cast', '演员', 5);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (25, 'TVShow', 'OID', null, 1);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (26, 'TVShow', 'Episodes', '集数', 2);
INSERT INTO "main"."MBIS_Server_TableField" VALUES (27, 'TVShow', 'Host', '主持人', 2);

-- ----------------------------
-- Table structure for MBIS_Server_Tag
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Tag";
CREATE TABLE MBIS_Server_Tag (
  ID INTEGER,
  TagName VARCHAR,
  PRIMARY KEY ("ID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_Tag
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Tag" VALUES (1, '梦工厂');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (2, '美国');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (3, '3D');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (4, '奇幻');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (5, 2010);
INSERT INTO "main"."MBIS_Server_Tag" VALUES (6, '童话');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (7, '科幻');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (8, '大陆');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (9, '谍战');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (10, 2009);
INSERT INTO "main"."MBIS_Server_Tag" VALUES (11, '保健');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (12, 'BTV');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (13, '大陆');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (14, '养生');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (15, '访谈');
INSERT INTO "main"."MBIS_Server_Tag" VALUES (16, '动画');

-- ----------------------------
-- Table structure for MBIS_Server_TagType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_TagType";
CREATE TABLE [MBIS_Server_TagType] (
  [ID] INTEGER, 
  [TagType] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_TagType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_TagType" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_TagType" VALUES (1, '出品方');
INSERT INTO "main"."MBIS_Server_TagType" VALUES (2, '国家');
INSERT INTO "main"."MBIS_Server_TagType" VALUES (3, '年代');
INSERT INTO "main"."MBIS_Server_TagType" VALUES (4, '类型');
INSERT INTO "main"."MBIS_Server_TagType" VALUES (5, '特色');
INSERT INTO "main"."MBIS_Server_TagType" VALUES (6, '人');

-- ----------------------------
-- Table structure for MBIS_Server_Task
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Task";
CREATE TABLE "MBIS_Server_Task" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"PushID"  INTEGER,
"OID"  VARCHAR,
"ChannelID"  INTEGER,
"FileID"  INTEGER,
"Priority"  INTEGER DEFAULT 1,
"Date"  VARCHAR,
"PushTime"  VARCHAR,
"Round"  INTEGER DEFAULT 1,
"State"  INTEGER,
"Sequence"  INTEGER,
"IsAppendix"  INTEGER,
"ServiceTypeID"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_Task
-- ----------------------------

-- ----------------------------
-- Table structure for MBIS_Server_TVShow
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_TVShow";
CREATE TABLE "MBIS_Server_TVShow" (
"ID"  INTEGER,
"OID"  VARCHAR,
"Episodes"  INTEGER,
"Host"  VARCHAR,
"SourceFrom"  VARCHAR,
"TVShowType"  VARCHAR,
PRIMARY KEY ("ID" ASC)
);

-- ----------------------------
-- Records of MBIS_Server_TVShow
-- ----------------------------
INSERT INTO "main"."MBIS_Server_TVShow" VALUES (1, 'S2017032000000000000000000000571', null, '李立宏', 'CCTV1', '记录片');
INSERT INTO "main"."MBIS_Server_TVShow" VALUES (2, 'S2017032100000000000000000000572', null, '老乡会主持人', '山东电视台2', '电视节目');
INSERT INTO "main"."MBIS_Server_TVShow" VALUES (3, 'S2017032100000000000000000000573', null, '老乡门诊主持人', '山东数字电视', '电视节目');
INSERT INTO "main"."MBIS_Server_TVShow" VALUES (4, 'S2017032200000000000000000000574', null, '何炅 谢娜', '湖南卫视', '综艺');

-- ----------------------------
-- Table structure for MBIS_Server_TVShowEpisode
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_TVShowEpisode";
CREATE TABLE "MBIS_Server_TVShowEpisode"
(
    ID INTEGER PRIMARY KEY,
    TVShowOID TEXT,
    EpisodeOID TEXT,
    Actor TEXT,
    Runtime INTEGER,
    Theme TEXT,
    introduction TEXT
, EpisodeIndex INT NULL);

-- ----------------------------
-- Records of MBIS_Server_TVShowEpisode
-- ----------------------------
INSERT INTO "main"."MBIS_Server_TVShowEpisode" VALUES (20, 'S2017032100000000000000000000572', '0B4A4FDA862E6B8DD304E3C880EA6EE5', '子路x', 6, '产品好不好 一亩地见分晓', '一亩地 简介 产品好不好 一亩地见分晓2', null);
INSERT INTO "main"."MBIS_Server_TVShowEpisode" VALUES (21, 'S2017032100000000000000000000572', '287A3939C180E1F5B06A5D1B16140B85', '董汉英', 5, '抗旺药残太严重 土豆不好长 找老乡', '简介 抗旺药残太严重 土豆不好长 找老乡', null);
INSERT INTO "main"."MBIS_Server_TVShowEpisode" VALUES (22, 'S2017032100000000000000000000572', 'FC1D25E6626669E91897D2D5083AEFF8', 'guestさん', 5, '老乡讲座进潍城 传授知识 利农惠农', '传授知识 利农惠农123413', null);
INSERT INTO "main"."MBIS_Server_TVShowEpisode" VALUES (23, 'S2017032200000000000000000000574', 'CF47F40C5B9676F1CEE45F1021BA9800', '老乡门诊嘉宾', 45, '老乡门诊', '对症下药老乡门诊', null);
INSERT INTO "main"."MBIS_Server_TVShowEpisode" VALUES (24, 'S2017032000000000000000000000571', '676513298BD859BF32AF1411F8E50C86', '', 12, '祖先的智慧', '舌尖上的中国1 =&amp;amp;amp;gt; 祖先的智慧', null);
INSERT INTO "main"."MBIS_Server_TVShowEpisode" VALUES (25, 'S2017032000000000000000000000571', 'E46E6A25448315083AB12A6F8CCFDD9C', '舌尖上的中国2嘉宾', 61, '舌尖上的中国2主题', '舌尖上的中国2

    简介:', null);

-- ----------------------------
-- Table structure for MBIS_Server_UiManager
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_UiManager";
CREATE TABLE "MBIS_Server_UiManager" (
"ID"  INTEGER,
"URL"  VARCHAR,
"ContentID"  VARCHAR,
"Status"  INTEGER,
PRIMARY KEY ("ID")
);

-- ----------------------------
-- Records of MBIS_Server_UiManager
-- ----------------------------
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (1, 'UI_1.MIP', 'DC5D57D38C8583D7B290A7367552986B', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (2, 'UI_2.MIP', '9C18FB45475E97A0B60936C423209896', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (3, 'UI_3.MIP', '8C891BAE404482BC571FBDB61A599695', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (4, 'UI_4.MIP', '8C891BAE404482BC571FBDB61A599695', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (5, 'UI_5.MIP', '8C891BAE404482BC571FBDB61A599695', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (6, 'UI_6.MIP', '8C891BAE404482BC571FBDB61A599695', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (7, 'UI_7.MIP', '88A5208432C930C5AE39E5C7196D3EC4', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (8, 'UI_8.MIP', '88A5208432C930C5AE39E5C7196D3EC4', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (9, 'UI_9.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (10, 'UI_10.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (11, 'UI_11.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (12, 'UI_12.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (13, 'UI_13.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (14, 'UI_14.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (15, 'UI_15.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (16, 'UI_16.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (17, 'UI_17.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (18, 'UI_18.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (19, 'UI_19.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (20, 'UI_20.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (21, 'UI_21.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (22, 'UI_22.MIP', '602330C10E374447AEF0D687625B0778', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (23, 'UI_23.MIP', '95BEB386C5C6A49E3BAA7C18155759B9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (24, 'UI_24.MIP', '95BEB386C5C6A49E3BAA7C18155759B9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (25, 'UI_25.MIP', '95BEB386C5C6A49E3BAA7C18155759B9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (26, 'UI_26.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (27, 'UI_27.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (28, 'UI_28.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (29, 'UI_29.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (30, 'UI_30.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (31, 'UI_31.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (32, 'UI_32.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (33, 'UI_33.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (34, 'UI_34.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (35, 'UI_35.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (36, 'UI_36.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (37, 'UI_37.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (38, 'UI_38.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (39, 'UI_39.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (40, 'UI_40.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (41, 'UI_41.MIP', '091CAAE2CD40FB49523088C1BD87E1D9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (42, 'UI_42.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (43, 'UI_43.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (44, 'UI_44.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (45, 'UI_45.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (46, 'UI_46.MIP', '45A590E1EFE8B89BFE691735729FB890', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (47, 'UI_47.MIP', 'F1987C2FC9873425D58BA539DDCE7CBA', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (48, 'UI_48.MIP', '3C9991E06A08A4972DDDFD55157D16D2', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (49, 'UI_49.MIP', '3C9991E06A08A4972DDDFD55157D16D2', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (50, 'UI_50.MIP', '3C9991E06A08A4972DDDFD55157D16D2', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (51, 'UI_51.MIP', '3C9991E06A08A4972DDDFD55157D16D2', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (52, 'UI_52.MIP', '3C9991E06A08A4972DDDFD55157D16D2', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (53, 'UI_53.MIP', '3C9991E06A08A4972DDDFD55157D16D2', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (54, 'UI_54.MIP', 'CF8875939FA7170DEB15BF8B1333C0F1', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (55, 'UI_55.MIP', '82309C02C63FA69BC1DA16EEDC61FDED', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (56, 'UI_56.MIP', '82309C02C63FA69BC1DA16EEDC61FDED', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (57, 'UI_57.MIP', '39985272C36953016B6F12E144290AA7', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (58, 'UI_58.MIP', '496301297AC916971BCB3462828E7331', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (59, 'UI_59.MIP', 'C2A031625A1C531C50601962E3F427E6', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (60, 'UI_60.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (61, 'UI_61.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (62, 'UI_62.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (63, 'UI_63.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (64, 'UI_64.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (65, 'UI_65.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (66, 'UI_66.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (67, 'UI_67.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (68, 'UI_68.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (69, 'UI_69.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (70, 'UI_70.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (71, 'UI_71.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (72, 'UI_72.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (73, 'UI_73.MIP', 'E3D820D4B5FBBC0B0F53EEF591F5C901', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (74, 'UI_74.MIP', '92987A17CB13128EB1849B6C18221096', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (75, 'UI_75.MIP', 'D109B02B61656CE881E139EAF32BAC2E', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (76, 'UI_76.MIP', '92987A17CB13128EB1849B6C18221096', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (77, 'UI_77.MIP', 'FC0D5426C07B9F0419AFA63F44823CB9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (78, 'UI_78.MIP', 'FC0D5426C07B9F0419AFA63F44823CB9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (79, 'UI_79.MIP', 'FC0D5426C07B9F0419AFA63F44823CB9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (80, 'UI_80.MIP', 'FC0D5426C07B9F0419AFA63F44823CB9', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (81, 'UI_81.MIP', '7BF64568F7508FE9D8845F645723CAFC', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (82, 'UI_82.MIP', '7BF64568F7508FE9D8845F645723CAFC', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (83, 'UI_83.MIP', '7BF64568F7508FE9D8845F645723CAFC', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (84, 'UI_84.MIP', '7BF64568F7508FE9D8845F645723CAFC', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (85, 'UI_85.MIP', '7BF64568F7508FE9D8845F645723CAFC', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (86, 'UI_86.MIP', '68005BC2B9EA578C76D800361E6F1A17', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (87, 'UI_87.MIP', '68005BC2B9EA578C76D800361E6F1A17', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (88, 'UI_88.MIP', '68005BC2B9EA578C76D800361E6F1A17', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (89, 'UI_89.MIP', '68005BC2B9EA578C76D800361E6F1A17', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (90, 'UI_90.MIP', '68005BC2B9EA578C76D800361E6F1A17', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (91, 'UI_91.MIP', '19EEC4EB50A54CD51D1F5ED0C3E548BE', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (92, 'UI_92.MIP', '19EEC4EB50A54CD51D1F5ED0C3E548BE', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (93, 'UI_93.MIP', '935BD1B6789658ED5E370A87A68E1C5D', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (94, 'UI_94.MIP', '19EEC4EB50A54CD51D1F5ED0C3E548BE', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (95, 'UI_95.MIP', '86B4882877C457A563DE96C9ADFBEF9F', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (96, 'UI_96.MIP', '19EEC4EB50A54CD51D1F5ED0C3E548BE', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (97, 'UI_97.MIP', '19EEC4EB50A54CD51D1F5ED0C3E548BE', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (98, 'UI_98.MIP', 'EAB799148B21D1D46B70649E9B0465D1', 0);
INSERT INTO "main"."MBIS_Server_UiManager" VALUES (99, 'UI_99.MIP', 'EAB799148B21D1D46B70649E9B0465D1', 0);

-- ----------------------------
-- Table structure for MBIS_Server_UpdateCycleType
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_UpdateCycleType";
CREATE TABLE "MBIS_Server_UpdateCycleType" (
"ID"  INTEGER PRIMARY KEY AUTOINCREMENT,
"UpdateCycleType"  VARCHAR,
"UpdateCycleDescription"  VARCHAR
);

-- ----------------------------
-- Records of MBIS_Server_UpdateCycleType
-- ----------------------------
INSERT INTO "main"."MBIS_Server_UpdateCycleType" VALUES (1, '年', '有效截至为自然年最后一天23点59分59秒');
INSERT INTO "main"."MBIS_Server_UpdateCycleType" VALUES (2, '月', '有效截至为自然月最后一天23点59分59秒');
INSERT INTO "main"."MBIS_Server_UpdateCycleType" VALUES (3, '期', '有效截至为1期的最后1天');

-- ----------------------------
-- Table structure for MBIS_Server_User
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_User";
CREATE TABLE "MBIS_Server_User" (
"uid"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"yonghuming"  VARCHAR(50),
"mima"  VARCHAR(50),
"pushcontrol"  INTEGER,
"content"  INTEGER,
"subscribe"  INTEGER,
"advertise"  INTEGER,
"customer"  INTEGER,
"permission"  INTEGER,
"super"  INTEGER,
"verifier"  INTEGER
);

-- ----------------------------
-- Records of MBIS_Server_User
-- ----------------------------
INSERT INTO "main"."MBIS_Server_User" VALUES (32, 'root', 123, 0, null, null, 1, 1, 0, null, 0);
INSERT INTO "main"."MBIS_Server_User" VALUES (120, 'll', 123, 0, 1, 0, 0, null, 0, 2, 1);
INSERT INTO "main"."MBIS_Server_User" VALUES (127, 'admin', '1d413b2945b2f1f06c93e2c7414968dfcc0bc8cd', 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO "main"."MBIS_Server_User" VALUES (129, 'jack', '5f7333bd58eb4d1a6897f26c52eb3993172563a8', null, 0, 0, 1, 1, 0, 2, 1);
INSERT INTO "main"."MBIS_Server_User" VALUES (138, 'test', '5f7333bd58eb4d1a6897f26c52eb3993172563a8', null, 1, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for MBIS_Server_Video
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Video";
CREATE TABLE "MBIS_Server_Video"
(
    ID INTEGER PRIMARY KEY,
    OID TEXT,
    Title TEXT,
    Resource TEXT,
    BFTime TEXT,
    Introduction TEXT
);

-- ----------------------------
-- Records of MBIS_Server_Video
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Video" VALUES (86, '00577066B8130B47DBAEF8B889E41B48', '国际泳联-跳水', 'CCTV1', '2017-03-24 10:00:00', '跳水是一项优美的水上运动，它是从高处用各种姿势跃入水中或是从跳水器械上起跳，在空中完成一定动作姿势，并以特定动作入水的运动。3');
INSERT INTO "main"."MBIS_Server_Video" VALUES (87, 'F120573BE8305B646D00293E27D496BF', '国际泳联-自由泳', 'cctv5', '2017-03-25 18:00:00', '自由泳.是竞技游泳比赛项目之一。严格的来说不是一种游泳姿势，它的竞赛规则几乎没有任何的限制.');
INSERT INTO "main"."MBIS_Server_Video" VALUES (90, '7DA0A9BDB14B9DCE504837C563E7C021', '温布尔登网球锦标赛', 'cctv1', '2017-03-25 10:00:00 ', '温布尔登网球锦标赛（Wimbledon Championships，或简称“温网”）是网球运动中最古老和最具声望的赛事，是网球四大满贯之一。温网举办地在英国伦敦郊区的温布尔登，通常举办于6月或7月，是每年度网球大满贯的第3项赛事，排在澳大利亚网球公开赛和法国网球公开赛之后，美国网球公开赛之前，也是四大满贯中唯一的草地比赛。整个赛事通常历时两周，但会因雨延时。男子单打、女子单打、男子双打、女子双打和男女混合双打比赛在不同场地同时进行。温布尔登还举办有男子单打、女子单打、男子双打、女子双打的青年比赛。此外，温布尔登还为退役球员举办特别邀请赛。');
INSERT INTO "main"."MBIS_Server_Video" VALUES (91, '5BCBA3BAE6F3D25DB9B1269AC59CCD35', 'swimming.ts', '', '', '');
INSERT INTO "main"."MBIS_Server_Video" VALUES (92, '799379F2E27390788DA1A47B5F7F4A4A', 'shandong_sports2.ts', null, null, null);
INSERT INTO "main"."MBIS_Server_Video" VALUES (93, '32EABE4CE88CD10107BA0E78854A462E', 'BMW.ts', null, null, null);

-- ----------------------------
-- Table structure for MBIS_Server_Year
-- ----------------------------
DROP TABLE IF EXISTS "main"."MBIS_Server_Year";
CREATE TABLE [MBIS_Server_Year] (
  [ID] INTEGER PRIMARY KEY AUTOINCREMENT, 
  [Year] VARCHAR);

-- ----------------------------
-- Records of MBIS_Server_Year
-- ----------------------------
INSERT INTO "main"."MBIS_Server_Year" VALUES (0, '待定');
INSERT INTO "main"."MBIS_Server_Year" VALUES (1, 2013);
INSERT INTO "main"."MBIS_Server_Year" VALUES (2, 2012);
INSERT INTO "main"."MBIS_Server_Year" VALUES (3, 2011);
INSERT INTO "main"."MBIS_Server_Year" VALUES (4, 2010);
INSERT INTO "main"."MBIS_Server_Year" VALUES (5, 2009);
INSERT INTO "main"."MBIS_Server_Year" VALUES (6, 2008);
INSERT INTO "main"."MBIS_Server_Year" VALUES (7, 2007);
INSERT INTO "main"."MBIS_Server_Year" VALUES (8, 2006);
INSERT INTO "main"."MBIS_Server_Year" VALUES (9, 2005);
INSERT INTO "main"."MBIS_Server_Year" VALUES (10, 2004);
INSERT INTO "main"."MBIS_Server_Year" VALUES (11, 2003);
INSERT INTO "main"."MBIS_Server_Year" VALUES (12, 2002);
INSERT INTO "main"."MBIS_Server_Year" VALUES (13, 2001);
INSERT INTO "main"."MBIS_Server_Year" VALUES (14, 2000);
INSERT INTO "main"."MBIS_Server_Year" VALUES (15, '90年代');
INSERT INTO "main"."MBIS_Server_Year" VALUES (16, '80年代');
INSERT INTO "main"."MBIS_Server_Year" VALUES (17, '70年代');
INSERT INTO "main"."MBIS_Server_Year" VALUES (18, '60年代');
INSERT INTO "main"."MBIS_Server_Year" VALUES (21, 2014);
INSERT INTO "main"."MBIS_Server_Year" VALUES (23, 2017);

-- ----------------------------
-- Table structure for sqlite_sequence
-- ----------------------------
DROP TABLE IF EXISTS "main"."sqlite_sequence";
CREATE TABLE sqlite_sequence(name,seq);

-- ----------------------------
-- Records of sqlite_sequence
-- ----------------------------
INSERT INTO "main"."sqlite_sequence" VALUES ('', 704);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Admin', 1);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_AccessData', 1);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_AdFileType', 3);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_AdType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_IP', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_Log', 24);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PauseAdMedia', 26);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PauseAdPushStatus', 48);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PreRollAdMedia', 58);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PreRollAdPushStatus', 138);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Appendix', 1018);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_AppendixType', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('mbis_server_attr', 18);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_BackUp', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Channel', 3);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Charge', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_ChargeType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Configure', 43);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Customer', 21);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_CustomerGroup', 6);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_CustomerSubscribe', 14);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_CustomerZone', 8);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_EditStatus', 470);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Genre', 15);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_HP', 6);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Language', 6);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Media', 275);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MediaDiscount', 407);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkCountry', 272);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkGenre', 315);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkTag', 304);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkYear', 270);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MediaPrice', 18);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MediaType', 9);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Mission', 284);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MissionlinkMedia', 924);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MissionPrice', 179);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Movie', 162);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Opera', 9);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Package', 181);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PackagelinkMission', 283);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PackageTemplate', 7);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PackageType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Path', 625);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Period', 10);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PushDisplay', 12);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Series', 52);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_SeriesEpisode', 37);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_ServiceType', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_SpecialLinkAttr', 151);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_TableField', 27);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_UpdateCycleType', 3);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_User', 138);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Year', 23);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_File', 7);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Year', 24);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Genre', 26);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Period', 11);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_ContentArchiecture', 16);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Language', 7);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_TableField', 27);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MediaType', 19);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_AppendixType', 5);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Plan', 76);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PackagelinkMission', 282);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_HP', 6);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Task', 105);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_ServiceType', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_OnLineMedia', 0);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PushDisplay', 12);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Admin', 1);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Customer', 43);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_CustomerSubscribe', 14);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MediaDiscount', 407);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MissionPrice', 179);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MissionUsed', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_CustomerZone', 8);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_CustomerGroup', 6);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_BillMission', 1);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_BillOnDemand', 12);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Channel', 9);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_FreeContent', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Pay', 0);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Charge', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_AdType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_MoveTxtAdPushStatus', 23);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_RoleAdPushStatus', 29);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_AdFileType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_AccessData', 1);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_IP', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_MoveTxtAdMedia', 12);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_RoleAdMedia', 12);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_Log', 24);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Mission', 283);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PauseAdPushStatus', 48);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PreRollAdPushStatus', 138);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Media', 274);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Path', 623);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Movie', 162);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Series', 52);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_SeriesEpisode', 37);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_EditStatus', 469);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkCountry', 271);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkGenre', 314);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkTag', 304);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MedialinkYear', 270);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Appendix', 1016);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MissionlinkMedia', 923);
INSERT INTO "main"."sqlite_sequence" VALUES ('', 704);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Opera', 9);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PreRollAdMedia', 59);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Ad_PauseAdMedia', 26);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_User', 140);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Package', 167);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PackageTemplate', 7);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_PackageType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_UpdateCycleType', 10);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_BackUp', 2);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_ChargeType', 4);
INSERT INTO "main"."sqlite_sequence" VALUES ('mbis_server_attr', 18);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_SpecialLinkAttr', 151);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_MediaPrice', 18);
INSERT INTO "main"."sqlite_sequence" VALUES ('MBIS_Server_Configure', 43);
