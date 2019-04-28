CREATE TABLE IF NOT EXISTS `cp_vfp_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sites_id` int(11) NOT NULL,
  `timestamp_expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `timestamp_voted` timestamp NULL,
  `ipaddress` varchar(155) NOT NULL,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;
