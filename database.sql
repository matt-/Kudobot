-- --------------------------------------------------------

-- 
-- Table structure for table `kudos`
-- 

CREATE TABLE `kudos` (
  `kudo_id` int(11) NOT NULL auto_increment,
  `from_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `thread_id` varchar(50) default NULL,
  `created_at` datetime NOT NULL,
  `received_on` datetime NOT NULL,
  `rekudo` int(11) default NULL,
  `sent` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`kudo_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9332 DEFAULT CHARSET=latin1 AUTO_INCREMENT=9332 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `admin` smallint(6) NOT NULL default '0',
  `email` varchar(50) NOT NULL,
  `avatar` varchar(150) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=latin1 AUTO_INCREMENT=107 ;