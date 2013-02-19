

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `lua_admin`
-- ----------------------------
DROP TABLE IF EXISTS `lua_admin`;
CREATE TABLE `lua_admin` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(20) NOT NULL,
  `logintime` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  `password` char(50) NOT NULL,
  `perm` char(10) NOT NULL,
  `logs` int(10) NOT NULL,
  `loginip` char(20) NOT NULL,
  `channel` char(20) NOT NULL,
  `category_can` text NOT NULL,
  `piece_can` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_admin
-- ----------------------------
INSERT INTO `lua_admin` VALUES ('1', 'admin', '1359724946', '1', '21232f297a57a5a743894a0e4a801fc3', 'admin', '1', '127.0.0.1', 'admin', '', '');

-- ----------------------------
-- Table structure for `lua_category`
-- ----------------------------
DROP TABLE IF EXISTS `lua_category`;
CREATE TABLE `lua_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `systemname` char(20) NOT NULL,
  `vieworder` tinyint(3) NOT NULL,
  `name` char(40) NOT NULL,
  `model_id` int(10) NOT NULL,
  `filename` char(40) NOT NULL,
  `upid` int(10) NOT NULL,
  `add_perm` tinyint(1) NOT NULL,
  `title` char(50) NOT NULL,
  `seokey` char(50) NOT NULL,
  `seoinfo` char(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_category
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_channel`
-- ----------------------------
DROP TABLE IF EXISTS `lua_channel`;
CREATE TABLE `lua_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `path` char(20) NOT NULL,
  `domain` char(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `groupname` char(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  `classname` char(10) NOT NULL,
  `isdefault` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_channel
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_member`
-- ----------------------------
DROP TABLE IF EXISTS `lua_member`;
CREATE TABLE `lua_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(20) NOT NULL,
  `gid` tinyint(3) NOT NULL,
  `regtime` int(10) NOT NULL,
  `regip` char(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `logs` int(10) NOT NULL,
  `lasttime` int(10) NOT NULL,
  `password` char(50) NOT NULL,
  `lastip` char(20) NOT NULL,
  `email` char(100) NOT NULL,
  `credit` int(10) NOT NULL,
  `weibo` char(20) NOT NULL,
  `tencent` char(20) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_member
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_member_model`
-- ----------------------------
DROP TABLE IF EXISTS `lua_member_model`;
CREATE TABLE `lua_member_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modelname` char(20) NOT NULL,
  `tablename` char(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `regtype` tinyint(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  `updatetime` int(10) NOT NULL,
  `systemname` char(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_member_model
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_member_model_field`
-- ----------------------------
DROP TABLE IF EXISTS `lua_member_model_field`;
CREATE TABLE `lua_member_model_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `vieworder` tinyint(3) NOT NULL,
  `fieldtype` char(10) NOT NULL,
  `ismust` tinyint(1) NOT NULL,
  `updatetime` int(10) NOT NULL,
  `fieldname` char(20) NOT NULL,
  `systemname` char(20) NOT NULL,
  `model_id` int(10) NOT NULL,
  `fieldoption` text NOT NULL,
  `relate_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_member_model_field
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_member_model_group`
-- ----------------------------
DROP TABLE IF EXISTS `lua_member_model_group`;
CREATE TABLE `lua_member_model_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `systemname` char(20) NOT NULL,
  `model_id` int(10) NOT NULL,
  `vieworder` tinyint(1) NOT NULL,
  `name` char(20) NOT NULL,
  `credit` int(10) NOT NULL,
  `expiry` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `vieworder` (`vieworder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_member_model_group
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_model`
-- ----------------------------
DROP TABLE IF EXISTS `lua_model`;
CREATE TABLE `lua_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modelname` char(20) NOT NULL,
  `developer` char(20) NOT NULL,
  `contact` char(20) NOT NULL,
  `tablenum` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  `intro` char(100) NOT NULL,
  `prefix` char(20) NOT NULL,
  `mtype` tinyint(1) NOT NULL,
  `cid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_model
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_model_field`
-- ----------------------------
DROP TABLE IF EXISTS `lua_model_field`;
CREATE TABLE `lua_model_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `vieworder` tinyint(3) NOT NULL,
  `fieldtype` char(10) NOT NULL,
  `ismust` tinyint(1) NOT NULL,
  `updatetime` int(10) NOT NULL,
  `fieldname` char(20) NOT NULL,
  `model_id` int(10) NOT NULL,
  `fieldoption` text NOT NULL,
  `table_id` int(10) NOT NULL,
  `relate_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_model_field
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_model_table`
-- ----------------------------
DROP TABLE IF EXISTS `lua_model_table`;
CREATE TABLE `lua_model_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modelname` char(20) NOT NULL,
  `tablename` char(50) NOT NULL,
  `upid` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  `model_id` int(10) NOT NULL,
  `model_type` tinyint(1) NOT NULL,
  `subid` char(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_model_table
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_piece`
-- ----------------------------
DROP TABLE IF EXISTS `lua_piece`;
CREATE TABLE `lua_piece` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `systemname` char(20) NOT NULL,
  `vieworder` tinyint(3) NOT NULL,
  `name` char(40) NOT NULL,
  `model_id` int(10) NOT NULL,
  `upid` int(10) NOT NULL,
  `add_perm` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_piece
-- ----------------------------

-- ----------------------------
-- Table structure for `lua_logs`
-- ----------------------------
DROP TABLE IF EXISTS `lua_logs`;
CREATE TABLE `lua_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `username` char(40) NOT NULL,
  `ip` char(20) NOT NULL,
  `dateline` datetime NOT NULL,
  `actionname` char(20) NOT NULL,
  `content` char(200) NOT NULL,
  `path` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lua_logs
-- ----------------------------