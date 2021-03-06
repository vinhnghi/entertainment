----------------------------------------------------------
-- Tables used for this component
-- users
-- talent extended from users
-- talent_type for categorizing talent
-- talent_assets for keeping images/videos
-- activity_talent for keeping event talent attended
-- agent extended from users
-- agent_favourite for keeping favourite talents of agents


-- --------------------------------------------------------

--
-- Table structure for table`joomla_agent`
--

DROP TABLE IF EXISTS`joomla_agent`;

CREATE TABLE IF NOT EXISTS`joomla_agent` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
`catid` int(10) unsigned NOT NULL DEFAULT '0',
`user_id` int(10) unsigned NOT NULL DEFAULT '0',
`images` text NOT NULL,
`published` tinyint(1) NOT NULL DEFAULT '0',
`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` int(10) unsigned NOT NULL DEFAULT '0',
`metakey` text NOT NULL,
`metadesc` text NOT NULL,
`metadata` text NOT NULL,
`ordering` int(11) NOT NULL DEFAULT '0',
`introtext` mediumtext NOT NULL,
`fulltext` mediumtext NOT NULL,
`language` char(7) NOT NULL,
`params`  VARCHAR(1024) NOT NULL DEFAULT '',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table`joomla_agent_favourite`
--

DROP TABLE IF EXISTS`joomla_agent_favourite`;

CREATE TABLE IF NOT EXISTS`joomla_agent_favourite` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
`agent_id` int(10) unsigned NOT NULL DEFAULT '0',
`talent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_talent table.',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table`joomla_talent`
--

DROP TABLE IF EXISTS`joomla_talent`;

CREATE TABLE IF NOT EXISTS`joomla_talent` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
`catid` int(10) unsigned NOT NULL DEFAULT '0',
`user_id` int(10) unsigned NOT NULL DEFAULT '0',
`images` text NOT NULL,
`published` tinyint(1) NOT NULL DEFAULT '0',
`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` int(10) unsigned NOT NULL DEFAULT '0',
`metakey` text NOT NULL,
`metadesc` text NOT NULL,
`metadata` text NOT NULL,
`ordering` int(11) NOT NULL DEFAULT '0',
`introtext` mediumtext NOT NULL,
`fulltext` mediumtext NOT NULL,
`hits` int(10) unsigned NOT NULL DEFAULT '0',
`language` char(7) NOT NULL,
`params`  VARCHAR(1024) NOT NULL DEFAULT '',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table`joomla_talent_type`
--

DROP TABLE IF EXISTS`joomla_talent_type`;

CREATE TABLE IF NOT EXISTS`joomla_talent_type` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
`catid` int(10) unsigned NOT NULL DEFAULT '0',
`images` text NOT NULL,
`title` varchar(255) NOT NULL,
`alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
`published` tinyint(1) NOT NULL DEFAULT '0',
`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` int(10) unsigned NOT NULL DEFAULT '0',
`created_by_alias` varchar(255) NOT NULL DEFAULT '',
`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` int(10) unsigned NOT NULL DEFAULT '0',
`metakey` text NOT NULL,
`metadesc` text NOT NULL,
`metadata` text NOT NULL,
`ordering` int(11) NOT NULL DEFAULT '0',
`introtext` mediumtext NOT NULL,
`fulltext` mediumtext NOT NULL,
`hits` int(10) unsigned NOT NULL DEFAULT '0',
`language` char(7) NOT NULL,
`params` varchar(1024) NOT NULL DEFAULT '',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table`joomla_talent_type_talent`
--

DROP TABLE IF EXISTS`joomla_talent_type_talent`;

CREATE TABLE IF NOT EXISTS`joomla_talent_type_talent` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
`talent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_talent table.',
`talent_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_talent_type table.',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table`joomla_talent_assets`
--

DROP TABLE IF EXISTS`joomla_talent_assets`;

CREATE TABLE IF NOT EXISTS`joomla_talent_assets` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
`talent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_talent table.',
`src` mediumtext NOT NULL,
`alt` mediumtext NOT NULL,
`caption` mediumtext NOT NULL,
`media_type` char(4) NOT NULL,
`ordering` int(11) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `joomla_activity_talent`
--

DROP TABLE IF EXISTS `joomla_activity_talent`;

CREATE TABLE IF NOT EXISTS `joomla_activity_talent` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_activity table.',
	`talent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_talent table.'  
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

