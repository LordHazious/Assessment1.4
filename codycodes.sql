CREATE TABLE `cart` (
  `uid` int(11) default NULL,
  `pid` int(11) default NULL,
  `qty` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `post` (
  `pid` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `post` varchar(255) default NULL,
  `date_created` datetime default NULL,
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

CREATE TABLE `post_comment` (
  `cid` int(11) NOT NULL auto_increment,
  `pid` int(11) default NULL,
  `uid` int(11) default NULL,
  `comment` varchar(255) default NULL,
  `date_created` datetime default NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

CREATE TABLE `product` (
  `pid` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `price` double default NULL,
  `image` varchar(255) default NULL,
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `uid` int(11) NOT NULL auto_increment,
  `email` varchar(255) default NULL,
  `pass` varchar(255) default NULL,
  `first_name` varchar(255) default NULL,
  `last_name` varchar(255) default NULL,
  `admin` int(11) default NULL,
  `avatar` varchar(255) default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;