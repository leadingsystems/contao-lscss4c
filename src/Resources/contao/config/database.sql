CREATE TABLE `tl_layout` (
	`lscss4c_scssFileToLoad` blob NULL,
	`lscss4c_debugMode` char(1) NOT NULL default '',
	`lscss4c_noCache` char(1) NOT NULL default '',
	`lscss4c_noMinifier` char(1) NOT NULL default '',
	`lscss4c_pathsToConsiderForHash` text NULL,
	`lscss4c_cacheHash` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
