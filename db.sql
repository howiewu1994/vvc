CREATE DATABASE IF NOT EXISTS vvc DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE mysql;

DELETE FROM user WHERE password = '' AND user != 'root';

USE vvc;

CREATE USER IF NOT EXISTS vvc_admin@localhost identified by '123';
GRANT SELECT, INSERT, UPDATE, DELETE
ON vvc.* to vvc_admin@localhost identified by '123';

USE `vvc`;

/*Table structure for table `drug` */

DROP TABLE IF EXISTS `drug`;

CREATE TABLE `drug` (
  `drug_id` int(11) NOT NULL AUTO_INCREMENT,
  `drug_name` varchar(12) DEFAULT NULL,
  `drug_text` varchar(200) DEFAULT NULL,
  `drug_picture` varchar(200) DEFAULT NULL,
  `drug_cost` float DEFAULT NULL,
  PRIMARY KEY (`drug_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `drug` */

insert  into `drug`(`drug_id`,`drug_name`,`drug_text`,`drug_picture`,`drug_cost`) values (1,'金开灵注射液','用于治疗犬瘟热','web\\img\\jkl.jpg',5.5),(2,'口服补盐液','补充水分电解质和营养','web\\img\\kfby.jpg',6.2);

/*Table structure for table `illdrug` */

DROP TABLE IF EXISTS `illdrug`;

CREATE TABLE `illdrug` (
  `ill_id` int(11) NOT NULL,
  `drug_id` int(11) NOT NULL,
  PRIMARY KEY (`ill_id`,`drug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `illdrug` */

/*Table structure for table `illness` */

DROP TABLE IF EXISTS `illness`;

CREATE TABLE `illness` (
  `ill_id` int(11) NOT NULL AUTO_INCREMENT,
  `ill_name` varchar(12) DEFAULT NULL,
  `class_name` varchar(12) DEFAULT NULL,
  `ill_describe` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `illness` */

/*Table structure for table `illpic` */

DROP TABLE IF EXISTS `illpic`;

CREATE TABLE `illpic` (
  `ill_id` int(11) NOT NULL,
  `step_num` int(11) NOT NULL,
  `pic_path` text,
  PRIMARY KEY (`ill_id`,`step_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `illpic` */

/*Table structure for table `illvid` */

DROP TABLE IF EXISTS `illvid`;

CREATE TABLE `illvid` (
  `ill_id` int(11) DEFAULT NULL,
  `step_num` int(11) DEFAULT NULL,
  `vid_path` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `illvid` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `ill_id` int(11) DEFAULT NULL,
  `pay_name` varchar(12) DEFAULT NULL,
  `pay_cost` float DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  PRIMARY KEY (`pay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `payments` */

/*Table structure for table `stepname` */

DROP TABLE IF EXISTS `stepname`;

CREATE TABLE `stepname` (
  `step_num` int(11) NOT NULL,
  `step_name` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`step_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `stepname` */

/*Table structure for table `steps` */

DROP TABLE IF EXISTS `steps`;

CREATE TABLE `steps` (
  `ill_id` int(11) NOT NULL,
  `step_num` int(11) NOT NULL,
  `step_text` text,
  PRIMARY KEY (`ill_id`,`step_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `steps` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(12) DEFAULT NULL,
  `password` varchar(12) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `createdAt` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`user_id`,`user_name`,`password`,`role_id`,`createdAt`) values (1,'user1','123',2,0),(2,'admin1','123',1,0);
