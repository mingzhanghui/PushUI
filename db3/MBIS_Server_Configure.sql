/*
Navicat SQLite Data Transfer

Source Server         : PushUI.mbis_server
Source Server Version : 30808
Source Host           : :0

Target Server Type    : SQLite
Target Server Version : 30808
File Encoding         : 65001

Date: 2017-08-25 09:15:41
*/

PRAGMA foreign_keys = OFF;

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
INSERT INTO "main"."MBIS_Server_Configure" VALUES (5, 'content_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/media', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (6, 'part_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/part', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (7, 'part_size', 524288, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (8, 'channel_num', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (9, 'thumb_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/thumb', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (10, 'appendix_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/appendix', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (11, 'background_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/background', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (12, 'channel_sync', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (13, 'content_order', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (14, 'packagechannel_num', 4, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (15, 'start_time', null, '08:01', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (16, 'web_port', 1080, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (18, 'package_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/package', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (19, 'channel_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/channel', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (20, 'system_dir', null, 'F:/subversion/PUSH_2.0/PushUI/resource/system', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (21, 'mip_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/mip', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (22, 'sync_port', 15011, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (23, 'sync_dir', null, 'F:/subversion/PUSH_2.0/PushUI/resource/sync', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (24, 'adv_dir', null, 'F:/subversion/PUSH_2.0/PushUI/resource/adv', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (25, 'round_adv_start', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (26, 'round_adv_preroll', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (27, 'round_adv_role', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (28, 'round_adv_pause', 3, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (29, 'round_adv_text', 5, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (30, 'uPid', 102, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (31, 'xmltemplate_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/UiMip/Templates', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (32, 'xml_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/UiMip', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (33, 'log_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/logcontent', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (34, 'thumb_width', 132, 132, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (35, 'thumb_height', 96, 96, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (36, 'background_width', 178, 178, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (37, 'background_height', 250, 250, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (38, 'backuptime', 30, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (39, 'adlog_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/logadv', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (40, 'totalsource', 4, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (41, 'reviewed', 0, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (42, 'login', 0, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (43, 'daily_record', 0, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (44, 'qrcode_path', null, 'F:/subversion/PUSH_2.0/PushUI/resource/qrcode', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (45, 'boss_server_host', null, '192.168.10.171', null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (46, 'boss_server_port', 6789, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (47, 'srt_ratio', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (48, 'drt_ratio', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (49, 'srt_last_time', 1503473156, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (50, 'drt_last_time', 1503473156, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (51, 'uat_ratio', 1, null, null);
INSERT INTO "main"."MBIS_Server_Configure" VALUES (52, 'uat_last_time', 1503473156, null, null);
