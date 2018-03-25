/*
Navicat SQLite Data Transfer

Source Server         : PushUI.mbis_server
Source Server Version : 30808
Source Host           : :0

Target Server Type    : SQLite
Target Server Version : 30808
File Encoding         : 65001

Date: 2017-07-26 13:50:16
*/

PRAGMA foreign_keys = OFF;

-- ----------------------------
-- Table structure for MBIS_Server_Package
-- ----------------------------
DROP TABLE IF EXISTS "MBIS_Server_Package";
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
, ChargeTypeID INT DEFAULT 3 NULL, QrCode TEXT NULL);

-- ----------------------------
-- Records of MBIS_Server_Package
-- ----------------------------
INSERT INTO "MBIS_Server_Package" VALUES (136, '热点视频', '本期影讯为您提供最新最热的新闻视频', 'package136thumb/136_zuqiu.jpg', 3, 0, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package136/136_zuqiu.jpg', 0, 1, 1, 1, 0, 'package136/136_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (137, '本期影讯', '本期影讯部分免费', 'package137thumb/137_movie.jpg', 3, 100, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package137/137_movie.jpg', 0, 1, 2, 2, 1, 'package137/137_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (138, '下期影讯', '下期影讯', 'package138thumb/138_next_movie.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package138/138_next_movie.jpg', 0, 1, 1, 2, 1, 'package138/138_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (139, '动漫天地', '动漫天地', 'package139thumb/139_frozen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package139/139_frozen.jpg', 0, 1, 2, 2, 1, 'package139/139_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (140, '文化传奇', '文化传奇 综艺节目', 'package140thumb/140_shejian.jpg', 3, 0, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package140/140_shejian.jpg', 0, 1, 1, 4, 0, 'package140/140_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (141, '天天好剧', '天天好剧', 'package141thumb/141_laizixingxingdeni.jpg', 3, 100, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package141/141_laizixingxingdeni.jpg', 0, 0, 1, 3, 1, 'package141/141_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (142, '名师讲堂', '名师讲堂', 'package142thumb/142_mingshijiangtang.jpg', 3, 12341, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package142/142_mingshijiangtang.jpg', 0, 0, 1, 1, 1, 'package142/142_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (146, '语文', '语文', 'package146thumb/146_yuwen.jpg', 3, 222, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package146/146_yuwen.jpg', 142, 0, 1, 6, 1, 'package146/146_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (147, '数学', '数学', 'package147thumb/147_shuxue.jpg', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package147/147_shuxue.jpg', 142, 0, 1, 6, 1, 'package147/147_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (148, '英语', '英语', 'package148thumb/148_english.jpg', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package148/148_english.jpg', 142, 0, 0, 6, 1, 'package148/148_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (149, '物理', '物理', 'package149thumb/149_physics.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package149/149_physics.jpg', 142, 0, 0, 6, 1, 'package149/149_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (150, '地理', '地理', 'package150thumb/150_dili.jpg', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package150/150_dili.jpg', 142, 0, 0, 6, 1, 'package150/150_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (151, '政治', '政治', 'package151thumb/151_politics.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package151/151_politics.jpg', 142, 0, 0, 6, 1, 'package151/151_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (152, '中考语文', '中考语文', 'package152thumb/152_zhongkaoyuwen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package152/152_zhongkaoyuwen.jpg', 146, 0, 1, 6, 1, 'package152/152_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (153, '高一语文', '高一语文', 'package153thumb/153_gaoyiyuwen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package153/153_gaoyiyuwen.jpg', 146, 0, 1, 6, 1, 'package153/153_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (154, '高二语文', '高二语文', 'package154thumb/154_gaoyiyuwen.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package154/154_gaoyiyuwen.jpg', 146, 0, 1, 6, 1, 'package154/154_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (155, '张桥林', '张桥林', 'package155thumb/155_zhangqiaolin.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package155/155_zhangqiaolin.jpg', 152, 1, 1, 6, 1, 'package155/155_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (156, '博乐', '博乐', 'package156thumb/156_801.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package156/156_801.jpg', 152, 1, 1, 6, 1, 'package156/156_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (157, '旺仔', '旺仔', 'package157thumb/157_2.png', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package157/157_2.png', 152, 1, 1, 6, 1, 'package157/157_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (158, '刘珊', '刘珊', 'package158thumb/158_8.png', 3, 333, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package158/158_8.png', 152, 1, 1, 6, 1, 'package158/158_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (159, '张晓莉', '张晓莉', 'package159thumb/159_803.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package159/159_803.jpg', 152, 1, 1, 6, 1, 'package159/159_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (160, '吴杰', '吴杰', 'package160thumb/160_wujie.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package160/160_wujie.jpg', 152, 1, 1, 6, 1, 'package160/160_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (161, '天宇', '天宇', 'package161thumb/161_tianyu.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package161/161_tianyu.jpg', 152, 1, 1, 6, 1, 'package161/161_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (162, '刘丹妮', '刘丹妮', 'package162thumb/162_liudanni.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package162/162_liudanni.jpg', 152, 1, 1, 6, 1, 'package162/162_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (163, '申诉', '申诉', 'package163thumb/163_802.jpg', 3, 222, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package163/163_802.jpg', 152, 1, 1, 6, 1, 'package163/163_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (164, 'IT培训', 'IT培训', 'package164thumb/164_nodejs.jpg', 3, 8888, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package164/164_nodejs.jpg', 0, 0, 1, 1, 1, 'package164/164_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (165, '北大青鸟', '北大青鸟', 'package165thumb/165_beidaqingniao.jpg', 3, 18888, null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package165/165_beidaqingniao.jpg', 164, 0, 1, 6, 1, 'package165/165_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (166, '达内教育', '达内教育', 'package166thumb/166_danei.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package166/166_danei.jpg', 164, 0, 1, 6, 1, 'package166/166_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (167, '极客学院', '极客学院', 'package167thumb/167_jikexueyuan.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package167/167_jikexueyuan.jpg', 164, 0, 3, 6, 1, 'package167/167_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (168, '前端开发', '', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package168/168_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (169, '后端开发', '', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package169/169_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (170, '人工智能', '', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package170/170_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (171, '智能硬件', '智能硬件', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package171/171_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (172, '移动开发', '', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package172/172_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (173, '物联网', '物联网', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package173/173_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (174, '设计', '设计', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package174/174_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (175, '产品', '', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package175/175_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (176, '测试', '', null, 3, '', null, null, 165, 0, 1, 6, 1, 'package176/176_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (177, 'HTML', '', null, 3, '', null, null, 168, 0, 1, 6, 1, 'package177/177_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (178, 'CSS', '', null, 3, '', null, null, 168, 0, 1, 6, 1, 'package178/178_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (179, 'javascript', 'javascript', null, 3, '', null, null, 168, 0, 1, 1, 1, 'package179/179_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (180, '张鹏', '张鹏', null, 3, '', null, null, 177, 1, 1, 6, 1, 'package180/180_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (181, '韩顺平', '', null, 3, '', null, null, 177, 1, 1, 1, 1, 'package181/181_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (183, '名医讲堂', '名医讲堂', 'package183thumb/183_p09_thumb_show0001.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package183/183_p09_thumb_show0001.jpg', 0, 0, 1, 5, 1, 'package183/183_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (185, '名医药铺', '名医药铺', 'package185thumb/185_p09_thumb_show0001.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package185/185_p09_thumb_show0001.jpg', 183, 1, 1, 5, 1, 'package185/185_qrcode.png');
INSERT INTO "MBIS_Server_Package" VALUES (187, '名医话养生', '名医话养生', 'package187thumb/187_p09_thumb_show0002.jpg', 3, '', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package/package187/187_p09_thumb_show0002.jpg', 183, 1, 1, 5, 1, 'package187/187_qrcode.png');
