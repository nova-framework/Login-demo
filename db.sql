# Dump of table smvc_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `smvc_users`;

CREATE TABLE `smvc_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `smvc_users` WRITE;
/*!40000 ALTER TABLE `smvc_users` DISABLE KEYS */;

INSERT INTO `smvc_users` (`id`, `username`, `password`)
VALUES
	(1,'demo','$2y$12$7fJZYOvUPm5S1/TeppeKFu9xxIZT877xYIrXPpkwrTTwFed6xDrZq');

/*!40000 ALTER TABLE `smvc_users` ENABLE KEYS */;
UNLOCK TABLES;