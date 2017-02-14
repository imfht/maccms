-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2012 年 01 月 05 日 07:55
-- 服务器版本: 5.1.37
-- PHP 版本: 5.2.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `maccms7`
--

-- --------------------------------------------------------

--
-- 表的结构 `mac_art`
--

CREATE TABLE IF NOT EXISTS `mac_art` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_title` varchar(255) DEFAULT NULL,
  `a_subtitle` varchar(255) DEFAULT NULL,
  `a_entitle` varchar(255) DEFAULT NULL,
  `a_letter` char(1) DEFAULT NULL,
  `a_from` varchar(64) DEFAULT NULL,
  `a_color` varchar(8) DEFAULT NULL,
  `a_type` int(11) DEFAULT '0',
  `a_topic` int(11) DEFAULT '0',
  `a_level` int(11) DEFAULT '0',
  `a_pic` varchar(255) DEFAULT NULL,
  `a_author` varchar(255) DEFAULT NULL,
  `a_content` text,
  `a_hits` int(11) DEFAULT '0',
  `a_dayhits` int(11) DEFAULT '0',
  `a_weekhits` int(11) DEFAULT '0',
  `a_monthhits` int(11) DEFAULT '0',
  `a_hide` int(11) DEFAULT '0',
  `a_addtime` datetime DEFAULT NULL,
  `a_time` datetime DEFAULT NULL,
  `a_hitstime` datetime DEFAULT NULL,
  `a_maketime` datetime DEFAULT NULL,
  PRIMARY KEY (`a_id`),
  KEY `a_type` (`a_type`),
  KEY `a_topic` (`a_topic`),
  KEY `a_level` (`a_level`),
  KEY `a_hits` (`a_hits`),
  KEY `a_dayhits` (`a_dayhits`),
  KEY `a_weekhits` (`a_weekhits`),
  KEY `a_monthhits` (`a_monthhits`),
  KEY `a_time` (`a_time`),
  KEY `a_addtime` (`a_addtime`),
  KEY `a_maketime` (`a_maketime`),
  KEY `a_hide` (`a_hide`),
  KEY `a_letter` (`a_letter`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_art_topic`
--

CREATE TABLE IF NOT EXISTS `mac_art_topic` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) DEFAULT NULL,
  `t_enname` varchar(128) DEFAULT NULL,
  `t_sort` int(11) DEFAULT '0',
  `t_template` varchar(128) DEFAULT NULL,
  `t_pic` varchar(255) DEFAULT NULL,
  `t_des` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_art_type`
--

CREATE TABLE IF NOT EXISTS `mac_art_type` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) DEFAULT NULL,
  `t_enname` varchar(128) DEFAULT NULL,
  `t_sort` int(11) DEFAULT '0',
  `t_pid` int(11) DEFAULT '0',
  `t_key` varchar(255) DEFAULT NULL,
  `t_des` varchar(255) DEFAULT NULL,
  `t_template` varchar(64) DEFAULT NULL,
  `t_arttemplate` varchar(64) DEFAULT NULL,
  `t_hide` int(11) DEFAULT '0',
  `t_union` text,
  PRIMARY KEY (`t_id`),
  KEY `t_pid` (`t_pid`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_comment`
--

CREATE TABLE IF NOT EXISTS `mac_comment` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_type` int(11) DEFAULT '0',
  `c_vid` int(11) DEFAULT '0',
  `c_rid` int(11) DEFAULT '0',
  `c_audit` int(11) DEFAULT '0',
  `c_name` varchar(64) DEFAULT NULL,
  `c_ip` varchar(32) DEFAULT NULL,
  `c_content` varchar(128) DEFAULT NULL,
  `c_time` datetime DEFAULT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_vid` (`c_vid`),
  KEY `c_type` (`c_type`),
  KEY `c_rid` (`c_rid`),
  KEY `c_time` (`c_time`),
  KEY `c_audit` (`c_audit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_gbook`
--

CREATE TABLE IF NOT EXISTS `mac_gbook` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_vid` int(11) DEFAULT '0',
  `g_audit` int(11) DEFAULT '0',
  `g_name` varchar(64) DEFAULT NULL,
  `g_content` varchar(255) DEFAULT NULL,
  `g_reply` varchar(255) DEFAULT NULL,
  `g_ip` varchar(32) DEFAULT NULL,
  `g_time` datetime DEFAULT NULL,
  `g_replytime` datetime DEFAULT NULL,
  PRIMARY KEY (`g_id`),
  KEY `g_vid` (`g_vid`),
  KEY `g_time` (`g_time`),
  KEY `g_audit` (`g_audit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_link`
--

CREATE TABLE IF NOT EXISTS `mac_link` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_name` varchar(64) DEFAULT NULL,
  `l_type` varchar(8) DEFAULT NULL,
  `l_url` varchar(255) DEFAULT NULL,
  `l_sort` int(11) DEFAULT '0',
  `l_logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`l_id`),
  KEY `l_sort` (`l_sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_manager`
--

CREATE TABLE IF NOT EXISTS `mac_manager` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_name` varchar(32) DEFAULT NULL,
  `m_password` varchar(32) DEFAULT NULL,
  `m_levels` text,
  `m_status` int(11) DEFAULT '0',
  `m_logintime` datetime DEFAULT NULL,
  `m_loginip` varchar(32) DEFAULT NULL,
  `m_random` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_mood`
--

CREATE TABLE IF NOT EXISTS `mac_mood` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_type` int(11) DEFAULT '0',
  `m_vid` int(11) DEFAULT '0',
  `mood1` int(11) DEFAULT '0',
  `mood2` int(11) DEFAULT '0',
  `mood3` int(11) DEFAULT '0',
  `mood4` int(11) DEFAULT '0',
  `mood5` int(11) DEFAULT '0',
  `mood6` int(11) DEFAULT '0',
  `mood7` int(11) DEFAULT '0',
  `mood8` int(11) DEFAULT '0',
  `mood9` int(11) DEFAULT '0',
  PRIMARY KEY (`m_id`),
  KEY `m_type` (`m_type`),
  KEY `m_vid` (`m_vid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_user`
--

CREATE TABLE IF NOT EXISTS `mac_user` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_qid` varchar(32) DEFAULT NULL,
  `u_name` varchar(32) DEFAULT NULL,
  `u_group` int(11) DEFAULT '0',
  `u_password` varchar(32) DEFAULT NULL,
  `u_qq` varchar(16) DEFAULT NULL,
  `u_email` varchar(32) DEFAULT NULL,
  `u_phone` varchar(16) DEFAULT NULL,
  `u_status` int(11) DEFAULT '0',
  `u_question` varchar(255) DEFAULT NULL,
  `u_answer` varchar(255) DEFAULT NULL,
  `u_points` int(11) DEFAULT '0',
  `u_regtime` datetime DEFAULT NULL,
  `u_logintime` datetime DEFAULT NULL,
  `u_loginnum` int(11) DEFAULT '0',
  `u_tj` int(11) DEFAULT '0',
  `u_ip` varchar(32) DEFAULT NULL,
  `u_random` varchar(64) DEFAULT NULL,
  `u_fav` text,
  `u_plays` text,
  `u_downs` text,
  `u_flag` int(11) DEFAULT '0',
  `u_start` varchar(64) DEFAULT NULL,
  `u_end` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_user_card`
--

CREATE TABLE IF NOT EXISTS `mac_user_card` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_number` varchar(32) DEFAULT NULL,
  `c_pass` varchar(32) DEFAULT NULL,
  `c_money` int(11) DEFAULT '0',
  `c_point` int(11) DEFAULT '0',
  `c_used` int(11) DEFAULT '0',
  `c_sale` int(11) DEFAULT '0',
  `c_user` int(11) DEFAULT '0',
  `c_addtime` datetime DEFAULT NULL,
  `c_usetime` datetime DEFAULT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_user_group`
--

CREATE TABLE IF NOT EXISTS `mac_user_group` (
  `ug_id` int(11) NOT NULL AUTO_INCREMENT,
  `ug_name` varchar(32) DEFAULT NULL,
  `ug_type` varchar(255) DEFAULT NULL,
  `ug_popedom` varchar(32) DEFAULT NULL,
  `ug_upgrade` int(11) DEFAULT '0',
  `ug_popvalue` int(11) DEFAULT '0',
  PRIMARY KEY (`ug_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_user_visit`
--

CREATE TABLE IF NOT EXISTS `mac_user_visit` (
  `uv_id` int(11) NOT NULL AUTO_INCREMENT,
  `uv_uid` int(11) DEFAULT '0',
  `uv_ip` varchar(32) DEFAULT NULL,
  `uv_ly` varchar(128) DEFAULT NULL,
  `uv_time` datetime DEFAULT NULL,
  PRIMARY KEY (`uv_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_vod`
--

CREATE TABLE IF NOT EXISTS `mac_vod` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `d_name` varchar(255) DEFAULT NULL,
  `d_subname` varchar(255) DEFAULT NULL,
  `d_enname` varchar(255) DEFAULT NULL,
  `d_type` int(11) DEFAULT '0',
  `d_letter` char(1) DEFAULT NULL,
  `d_state` int(11) DEFAULT '0',
  `d_color` varchar(8) DEFAULT NULL,
  `d_pic` varchar(255) DEFAULT NULL,
  `d_picthumb` varchar(255) DEFAULT NULL,
  `d_picslide` varchar(255) DEFAULT NULL,
  `d_starring` varchar(255) DEFAULT NULL,
  `d_directed` varchar(255) DEFAULT NULL,
  `d_area` varchar(32) DEFAULT NULL,
  `d_year` varchar(32) DEFAULT NULL,
  `d_language` varchar(32) DEFAULT NULL,
  `d_level` int(11) DEFAULT '0',
  `d_stint` int(11) DEFAULT '0',
  `d_stintdown` int(11) DEFAULT '0',
  `d_hits` int(11) DEFAULT '0',
  `d_dayhits` int(11) DEFAULT '0',
  `d_weekhits` int(11) DEFAULT '0',
  `d_monthhits` int(11) DEFAULT '0',
  `d_topic` int(11) DEFAULT '0',
  `d_duration` int(11) DEFAULT '0',
  `d_content` text,
  `d_remarks` varchar(255) DEFAULT NULL,
  `d_hide` int(11) DEFAULT '0',
  `d_good` int(11) DEFAULT '0',
  `d_bad` int(11) DEFAULT '0',
  `d_usergroup` int(11) DEFAULT '0',
  `d_score` int(11) DEFAULT '0',
  `d_scorecount` int(11) DEFAULT '0',
  `d_addtime` datetime DEFAULT NULL,
  `d_time` datetime DEFAULT NULL,
  `d_hitstime` datetime DEFAULT NULL,
  `d_maketime` datetime DEFAULT NULL,
  `d_playfrom` varchar(255) DEFAULT NULL,
  `d_playserver` varchar(255) DEFAULT NULL,
  `d_playurl` longtext,
  `d_downfrom` varchar(255) DEFAULT NULL,
  `d_downserver` varchar(255) DEFAULT NULL,
  `d_downurl` longtext,
  PRIMARY KEY (`d_id`),
  KEY `d_type` (`d_type`),
  KEY `d_state` (`d_state`),
  KEY `d_level` (`d_level`),
  KEY `d_hits` (`d_hits`),
  KEY `d_dayhits` (`d_dayhits`),
  KEY `d_weekhits` (`d_weekhits`),
  KEY `d_monthhits` (`d_monthhits`),
  KEY `d_stint` (`d_stint`),
  KEY `d_stintdown` (`d_stintdown`),
  KEY `d_hide` (`d_hide`),
  KEY `d_usergroup` (`d_usergroup`),
  KEY `d_score` (`d_score`),
  KEY `d_time` (`d_time`),
  KEY `d_addtime` (`d_addtime`),
  KEY `d_maketime` (`d_maketime`),
  KEY `d_topic` (`d_topic`),
  KEY `d_letter` (`d_letter`),
  KEY `d_name` (`d_name`),
  KEY `d_enname` (`d_enname`),
  KEY `d_year` (`d_year`),
  KEY `d_area` (`d_area`),
  KEY `d_language` (`d_language`),
  KEY `d_starring` (`d_starring`),
  KEY `d_directed` (`d_directed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_vod_topic`
--

CREATE TABLE IF NOT EXISTS `mac_vod_topic` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) DEFAULT NULL,
  `t_enname` varchar(128) DEFAULT NULL,
  `t_sort` int(11) DEFAULT '0',
  `t_template` varchar(128) DEFAULT NULL,
  `t_pic` varchar(255) DEFAULT NULL,
  `t_des` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_vod_type`
--

CREATE TABLE IF NOT EXISTS `mac_vod_type` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) DEFAULT NULL,
  `t_enname` varchar(128) DEFAULT NULL,
  `t_sort` int(11) NOT NULL,
  `t_pid` int(11) DEFAULT '0',
  `t_key` varchar(255) DEFAULT NULL,
  `t_des` varchar(255) DEFAULT NULL,
  `t_template` varchar(64) DEFAULT NULL,
  `t_vodtemplate` varchar(64) DEFAULT NULL,
  `t_playtemplate` varchar(64) DEFAULT NULL,
  `t_hide` int(11) DEFAULT '0',
  `t_union` text,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_pid` (`t_pid`),
  KEY `t_hide` (`t_hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- 表的结构 `mac_cj_art`
--

CREATE TABLE IF NOT EXISTS `mac_cj_art` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_pid` int(11) NOT NULL DEFAULT '0',
  `m_title` varchar(255) DEFAULT NULL,
  `m_type` varchar(128) DEFAULT NULL,
  `m_typeid` int(11) NOT NULL DEFAULT '0',
  `m_author` varchar(500) DEFAULT NULL,
  `m_content` text,
  `m_addtime` varchar(64) DEFAULT NULL,
  `m_urltest` varchar(255) DEFAULT NULL,
  `m_zt` int(11) NOT NULL DEFAULT '0',
  `m_hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`m_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_art_projects`
--

CREATE TABLE IF NOT EXISTS `mac_cj_art_projects` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(128) DEFAULT NULL,
  `p_coding` varchar(64) DEFAULT NULL,
  `p_pagetype` int(11) NOT NULL DEFAULT '0',
  `p_url` varchar(255) DEFAULT NULL,
  `p_pagebatchurl` varchar(255) DEFAULT NULL,
  `p_manualurl` varchar(255) DEFAULT NULL,
  `p_pagebatchid1` varchar(128) DEFAULT NULL,
  `p_pagebatchid2` varchar(128) DEFAULT NULL,
  `p_script` int(11) NOT NULL DEFAULT '0',
  `p_showtype` int(11) NOT NULL DEFAULT '0',
  `p_collecorder` int(11) NOT NULL DEFAULT '0',
  `p_savefiles` int(11) NOT NULL DEFAULT '0',
  `p_intolib` int(11) NOT NULL DEFAULT '0',
  `p_ontime` int(11) NOT NULL DEFAULT '0',
  `p_listcodestart` text,
  `p_listcodeend` text,
  `p_classtype` int(11) NOT NULL DEFAULT '0',
  `p_collect_type` int(11) NOT NULL DEFAULT '0',
  `p_time` datetime DEFAULT NULL,
  `p_listlinkstart` text,
  `p_listlinkend` text,
  `p_authortype` int(11) NOT NULL DEFAULT '0',
  `p_authorstart` text,
  `p_authorend` text,
  `p_titletype` int(11) NOT NULL DEFAULT '0',
  `p_titlestart` text,
  `p_titleend` text,
  `p_timestart` text,
  `p_timeend` text,
  `p_typestart` text,
  `p_typeend` text,
  `p_contentstart` text,
  `p_contentend` text,
  `p_hitsstart` int(11) NOT NULL DEFAULT '0',
  `p_hitsend` int(11) NOT NULL DEFAULT '0',
  `p_cpagetype` int(11) NOT NULL DEFAULT '0',
  `p_cpagecodestart` text,
  `p_cpagecodeend` text,
  `p_cpagestart` text,
  `p_cpageend` text,
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_change`
--

CREATE TABLE IF NOT EXISTS `mac_cj_change` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_name` varchar(64) DEFAULT NULL,
  `c_toid` int(11) NOT NULL DEFAULT '0',
  `c_pid` int(11) NOT NULL DEFAULT '0',
  `c_type` int(4) NOT NULL DEFAULT '0',
  `c_sys` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `i_c_projectid` (`c_pid`),
  KEY `i_c_type` (`c_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_filters`
--

CREATE TABLE IF NOT EXISTS `mac_cj_filters` (
  `f_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(64) DEFAULT NULL,
  `f_object` int(11) NOT NULL DEFAULT '0',
  `f_type` int(11) NOT NULL DEFAULT '0',
  `f_content` varchar(64) DEFAULT NULL,
  `f_strstart` text,
  `f_strend` text,
  `f_rep` varchar(255) DEFAULT NULL,
  `f_flag` int(11) NOT NULL DEFAULT '0',
  `f_pid` int(11) NOT NULL DEFAULT '0',
  `f_sys` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`f_id`),
  KEY `i_f_type` (`f_type`),
  KEY `i_f_object` (`f_object`),
  KEY `i_f_projectid` (`f_pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_vod`
--

CREATE TABLE IF NOT EXISTS `mac_cj_vod` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_pid` int(11) NOT NULL DEFAULT '0',
  `m_name` varchar(255) DEFAULT NULL,
  `m_type` varchar(64) DEFAULT NULL,
  `m_typeid` int(11) NOT NULL DEFAULT '0',
  `m_area` varchar(64) DEFAULT NULL,
  `m_playfrom` varchar(64) DEFAULT NULL,
  `m_starring` varchar(255) DEFAULT NULL,
  `m_directed` varchar(255) DEFAULT NULL,
  `m_pic` varchar(255) DEFAULT NULL,
  `m_content` text,
  `m_year` varchar(64) DEFAULT NULL,
  `m_addtime` varchar(64) DEFAULT NULL,
  `m_urltest` varchar(255) DEFAULT NULL,
  `m_zt` int(11) NOT NULL DEFAULT '0',
  `m_playserver` int(11) NOT NULL DEFAULT '0',
  `m_hits` int(11) NOT NULL DEFAULT '0',
  `m_state` int(11) NOT NULL DEFAULT '0',
  `m_language` varchar(64) DEFAULT NULL,
  `m_remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_vod_projects`
--

CREATE TABLE IF NOT EXISTS `mac_cj_vod_projects` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(128) DEFAULT NULL,
  `p_coding` varchar(64) DEFAULT NULL,
  `p_playtype` varchar(11) DEFAULT NULL,
  `p_pagetype` int(11) NOT NULL DEFAULT '0',
  `p_url` varchar(255) DEFAULT NULL,
  `p_pagebatchurl` varchar(255) DEFAULT NULL,
  `p_manualurl` varchar(255) DEFAULT NULL,
  `p_pagebatchid1` varchar(128) DEFAULT NULL,
  `p_pagebatchid2` varchar(128) DEFAULT NULL,
  `p_script` int(11) NOT NULL DEFAULT '0',
  `p_showtype` int(11) NOT NULL DEFAULT '0',
  `p_collecorder` int(11) NOT NULL DEFAULT '0',
  `p_savefiles` int(11) NOT NULL DEFAULT '0',
  `p_intolib` int(11) NOT NULL DEFAULT '0',
  `p_ontime` int(11) NOT NULL DEFAULT '0',
  `p_listcodestart` text,
  `p_listcodeend` text,
  `p_classtype` int(11) NOT NULL DEFAULT '0',
  `p_collect_type` int(11) NOT NULL DEFAULT '0',
  `p_time` datetime DEFAULT NULL,
  `p_listlinkstart` text,
  `p_listlinkend` text,
  `p_starringtype` int(11) NOT NULL DEFAULT '0',
  `p_starringstart` text,
  `p_starringend` text,
  `p_titletype` int(11) NOT NULL DEFAULT '0',
  `p_titlestart` text,
  `p_titleend` text,
  `p_pictype` int(11) NOT NULL DEFAULT '0',
  `p_picstart` text,
  `p_picend` text,
  `p_timestart` text,
  `p_timeend` text,
  `p_areastart` text,
  `p_areaend` text,
  `p_typestart` text,
  `p_typeend` text,
  `p_contentstart` text,
  `p_contentend` text,
  `p_playcodetype` int(11) NOT NULL DEFAULT '0',
  `p_playcodestart` text,
  `p_playcodeend` text,
  `p_playurlstart` text,
  `p_playurlend` text,
  `p_playlinktype` int(11) NOT NULL DEFAULT '0',
  `p_playlinkstart` text,
  `p_playlinkend` text,
  `p_playspecialtype` int(11) NOT NULL DEFAULT '0',
  `p_playspecialrrul` text,
  `p_playspecialrerul` text,
  `p_server` varchar(128) DEFAULT NULL,
  `p_hitsstart` int(11) NOT NULL DEFAULT '0',
  `p_hitsend` int(11) NOT NULL DEFAULT '0',
  `p_lzstart` text,
  `p_lzend` text,
  `p_colleclinkorder` int(11) NOT NULL DEFAULT '0',
  `p_lzcodetype` int(11) NOT NULL DEFAULT '0',
  `p_lzcodestart` text,
  `p_lzcodeend` text,
  `p_languagestart` text,
  `p_languageend` text,
  `p_remarksstart` text,
  `p_remarksend` text,
  `p_directedstart` text,
  `p_directedend` text,
  `p_setnametype` int(11) NOT NULL DEFAULT '0',
  `p_setnamestart` text,
  `p_setnameend` text,
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_vod_url`
--

CREATE TABLE IF NOT EXISTS `mac_cj_vod_url` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_url` varchar(255) DEFAULT NULL,
  `u_weburl` varchar(255) DEFAULT NULL,
  `u_movieid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

