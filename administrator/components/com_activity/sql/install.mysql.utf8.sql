----------------------------------------------------------
-- Tables used for this component
-- users
-- client extended from users
-- activity for events
-- activity_type for categorizing activities
-- activity_activity_type joining type and activity
-- activity_assets for keeping images/videos
-- activity_talent for joining event and talent

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Table structure for table`joomla_client`
--

DROP TABLE IF EXISTS`joomla_client`;

CREATE TABLE IF NOT EXISTS`joomla_client` (
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
-- Table structure for table `joomla_activity_type`
--

DROP TABLE IF EXISTS `joomla_activity_type`;

CREATE TABLE IF NOT EXISTS `joomla_activity_type` (
`id` int(10) unsigned NOT NULL COMMENT 'PK',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_assets table.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` varchar(5120) NOT NULL,
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
  `params` varchar(1024) NOT NULL DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `joomla_activity_type` (`id`, `catid`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `images`, `urls`, `attribs`, `published`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `metakey`, `metadesc`, `metadata`, `ordering`, `introtext`, `fulltext`, `hits`, `language`, `params`) VALUES
(1, 0, 0, 0, 0, 21, 0, '', 'system', 'ROOT', 'root', '', '', '', 1, '2011-01-01 00:00:01', 547, '', '0000-00-00 00:00:00', 0, '', '', '{}', 0, '', '', 0, '*', '{}'),
(2, 0, 139, 1, 0, 21, 1, '', 'com_activity', 'Dance', 'dance', '{"image_intro":"images\\/joomla_black.gif","image_intro_alt":"asda","image_intro_caption":"sd","image_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}', '', '', 1, '2011-01-01 00:00:01', 547, '', '2015-04-18 11:30:45', 547, 'asd as', 'sdasasd', '{"robots":"","author":"","rights":"","xreference":""}', 0, '<p>Description</p>', '', 0, '*', '{}'),
(3, 0, 141, 1, 0, 21, 1, '', 'com_activity', 'R&B', 'r_b', '{"image_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}', '', '', 1, '2011-01-01 00:00:01', 547, '', '2015-04-18 16:25:32', 547, '', '', '{"robots":"","author":"","rights":"","xreference":""}', 0, '', '', 0, '*', '{}'),
(4, 0, 140, 1, 0, 21, 1, '', 'com_activity', 'POP', 'pop', '{"image_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}', '', '', 1, '2011-01-01 00:00:01', 547, '', '2015-04-18 16:25:26', 547, '', '', '{"robots":"","author":"","rights":"","xreference":""}', 0, '', '', 0, '*', '{}');

ALTER TABLE `joomla_activity_type` ADD PRIMARY KEY (`id`);
ALTER TABLE `joomla_activity_type`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',AUTO_INCREMENT=5;

-- --------------------------------------------------------

--
-- Table structure for table `joomla_activity`
--

DROP TABLE IF EXISTS `joomla_activity`;

CREATE TABLE IF NOT EXISTS `joomla_activity` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`catid` int(10) unsigned NOT NULL DEFAULT '0',
	`asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_assets table.',
	`title` varchar(255) NOT NULL DEFAULT '',
	`alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
	`images` text NOT NULL,
	`urls` text NOT NULL,
	`attribs` varchar(5120) NOT NULL,
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
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `joomla_activity_activity_type`
--

DROP TABLE IF EXISTS `joomla_activity_activity_type`;

CREATE TABLE IF NOT EXISTS `joomla_activity_activity_type` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_activity table.',
	`activity_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_activity_type table.',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `joomla_activity_assets`
--

DROP TABLE IF EXISTS `joomla_activity_assets`;

CREATE TABLE IF NOT EXISTS `joomla_activity_assets` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the joomla_activity table.',
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

