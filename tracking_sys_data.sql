CREATE DATABASE tracking_sys;
USE tracking_sys;

/*Table structure for table user */
CREATE TABLE user (
  uid int NOT NULL AUTO_INCREMENT,
  email varchar(40) NOT NULL,
  username varchar(40) NOT NULL,
  password varchar(40) NOT NULL,
  PRIMARY KEY (uid)
);
/*Data for the table user */
insert  into user(uid, email, username, password) values 
(1, "mike123@gmail.com", "mike123", "eb4fe305f390593017a0f15198080bd9"),
(2, "queen@hotmail.com", "iamqueen", "780f401148b238103fd71150f93e1759"),
(3, "lalaland@yahoo.com", "lala", "2a41f6a8d0c18bc8cb7e9d774ec157e0"),
(4, "yvonne@gamil.com", "yvonne0401", "f0fab86c1f2737404f28d1beec307bd0"),
(5, "lilah@nyu.edu", "lulala", "1db379c5ea7a443fb511d4704d1fa834"),
(6, "jb1964@amazon.com", "jb1964", "a95d57d8d71e5afd96323007545c6c04");

/*Table structure for table status */
CREATE TABLE status (
  sid int NOT NULL AUTO_INCREMENT,
  sname varchar(1000) DEFAULT NULL,
  PRIMARY KEY (sid)
);
insert into status (sid, sname) values
(1, "OPEN"),
(2, "IN PROGRESS"),
(3, "UNDER REVIEW"),
(4, "FINAL APPROVAL"),
(5, "DONE"),
(6, "OPEN"),
(7, "FIXING"),
(8, "TESTING-1"),
(9, "TESTING-2"),
(10, "FINAL ROUND"),
(11, "DONE"),
(12, "OPEN"),
(13, "CONTINUE"),
(14, "MEASURE"),
(15, "DONE");

/*Table structure for table project */
CREATE TABLE project (
  pid int NOT NULL AUTO_INCREMENT,
  pname varchar(1000) DEFAULT NULL,
  pdescription text DEFAULT NULL,
  pcreatTime datetime NOT NULL,
  workflow_start int NOT NULL,
  workflow_end int NOT NULL,
  PRIMARY KEY (pid),
  KEY workflow_start (workflow_start),
  KEY workflow_end (workflow_end),
  CONSTRAINT project_ibfk_1 FOREIGN KEY (workflow_start) REFERENCES status (sid) ON DELETE CASCADE ON UPDATE CASCADE,  
  CONSTRAINT project_ibfk_2 FOREIGN KEY (workflow_end) REFERENCES status (sid) ON DELETE CASCADE ON UPDATE CASCADE
);

insert into project (pid, pname, pdescription, pcreatTime, workflow_start, workflow_end) values
(1, "Wonderful Town", "Create a home, interact with other villagers, and just enjoy life in these charming games", "2017-03-26 10:00:00", 1, 5),
(2, "Crazy Cow", "A bunch of outrageous cows is running toward you. Keep calm and dodge from them.", "2002-01-05 18:30:00", 6, 11),
(3, "Amazon Kindle", "Reduce papers and save the world!", "1994-07-05 12:00:00", 12, 15);

/*Table structure for table issue */
CREATE TABLE issue (
  iid int NOT NULL AUTO_INCREMENT,
  pid int NOT NULL,
  reporter int NOT NULL,
  reportTime datetime NOT NULL,
  title varchar(1000) DEFAULT NULL,
  idescription text DEFAULT NULL,
  currentStatus int NOT NULL,
  PRIMARY KEY (iid),
  KEY pid (pid),
  KEY reporter (reporter),
  KEY currentStatus (currentStatus),
  CONSTRAINT issue_ibfk_1 FOREIGN KEY (pid) REFERENCES project (pid) ON DELETE CASCADE ON UPDATE CASCADE,  
  CONSTRAINT issue_ibfk_2 FOREIGN KEY (reporter) REFERENCES user (uid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT issue_ibfk_3 FOREIGN KEY (currentStatus) REFERENCES status (sid) ON DELETE CASCADE ON UPDATE CASCADE
);

insert into issue (iid, pid, reporter, reportTime, title, idescription, currentStatus) values
(1, 1, 1, "2018-01-01 12:00:00", "account problem", "Some customers complain that their stored values disappear.", 3),
(2, 3, 6, "2000-03-26 08:00:48", "screen flashback", "The screen is flashback after opening. Please fix it.", 1),
(3, 2, 3, "2010-12-25 15:00:34", "system crush", "Figure out why the system crush! ASAP!!!", 5),
(4, 1, 1, "2019-07-11 17:30:00", "text fail", "The text of the message is garbled.", 5);

/*Table structure for table lead */
CREATE TABLE lead (
  adminid int NOT NULL,
  pid int NOT NULL,
  PRIMARY KEY (adminid, pid),
  KEY adminid (adminid),
  KEY pid (pid),
  CONSTRAINT lead_ibfk_1 FOREIGN KEY (adminid) REFERENCES user (uid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lead_ibfk_2 FOREIGN KEY (pid) REFERENCES project (pid) ON DELETE CASCADE ON UPDATE CASCADE
);

insert into lead (adminid, pid) values
(1, 1),
(1, 3),
(2, 1),
(3, 2),
(4, 3),
(5, 3);

/*Table structure for table history */
CREATE TABLE history (
  iid int NOT NULL,
  htitle varchar(1000) DEFAULT NULL,
  hdescription text DEFAULT NULL,
  update_to_sid int NOT NULL, 
  uid int NOT NULL,
  updateTime datetime NOT NULL,
  PRIMARY KEY (iid, update_to_sid, uid, updateTime),
  KEY iid (iid),
  KEY update_to_sid (update_to_sid),
  KEY uid (uid),
  CONSTRAINT report_ibfk_1 FOREIGN KEY (iid) REFERENCES issue (iid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT report_ibfk_2 FOREIGN KEY (update_to_sid) REFERENCES status (sid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT report_ibfk_3 FOREIGN KEY (uid) REFERENCES user (uid) ON DELETE CASCADE ON UPDATE CASCADE
);

insert into history (iid, htitle, hdescription, update_to_sid, uid, updateTime) values
(1, "account problem", "Some customers complain that their stored values disappear.", 3, 1, "2018-01-01 12:00:00"),
(1, "account problem", "Some customers complain that their stored values disappear.", 2, 4, "2017-12-26 10:00:00"),
(1, "account problem", "Some customers complain that their stored values disappear.", 1, 2, "2017-12-25 00:00:00"),
(2, "screen flashback", "The screen is flashback after opening. Please fix it.", 1, 6, "2000-03-26 08:00:48"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 5, 3, "2010-12-25 15:00:34"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 4, 6, "2010-12-25 13:30:00"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 3, 1, "2010-12-25 12:15:32"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 2, 3, "2010-12-25 01:10:26"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 4, 6, "2010-12-25 01:00:00"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 3, 1, "2010-12-24 23:15:32"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 2, 3, "2010-12-24 15:10:32"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 3, 1, "2010-12-24 15:00:32"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 2, 3, "2010-12-24 00:10:00"),
(3, "system crush", "Figure out why the system crush! ASAP!!!", 1, 6, "2010-12-24 00:00:00"),
(4, "text fail", "The text of the message is garbled.", 5, 1, "2019-07-11 17:30:00"),
(4, "text fail", "The text of the message is garbled.", 4, 2, "2019-07-11 10:30:00"),
(4, "text fail", "The text of the message is garbled.", 3, 5, "2019-07-10 17:30:10"),
(4, "text fail", "The text of the message is garbled.", 2, 3, "2019-07-10 13:20:00"),
(4, "text fail", "The text of the message is garbled.", 4, 2, "2019-07-10 12:30:00"),
(4, "text fail", "The text of the message is garbled.", 3, 5, "2019-07-09 16:30:10"),
(4, "text fail", "The text of the message is garbled.", 2, 3, "2019-07-08 13:00:00"),
(4, "text fail", "The text of the message is garbled.", 1, 3, "2019-07-08 12:26:50");


/*Table structure for table assignee */
CREATE TABLE assignee (
  uid int NOT NULL,
  iid int NOT NULL,
  adminid int NOT NULL,
  assignTime datetime NOT NULL,
  PRIMARY KEY (uid, iid),
  KEY uid (uid),
  KEY iid (iid),
  KEY adminid (adminid),
  CONSTRAINT assignee_ibfk_1 FOREIGN KEY (uid) REFERENCES user (uid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT assignee_ibfk_2 FOREIGN KEY (iid) REFERENCES issue (iid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT assignee_ibfk_3 FOREIGN KEY (adminid) REFERENCES lead (adminid) ON DELETE CASCADE ON UPDATE CASCADE
);

insert into assignee (uid, iid, adminid, assignTime) values
(1, 3, 3, "2010-12-24 14:40:00"),
(3, 4, 1, "2019-07-08 12:28:36"),
(4, 1, 2, "2017-12-26 09:00:00"),
(5, 4, 1, "2019-07-08 12:30:50"),
(6, 3, 3, "2010-12-25 00:02:00"),
(6, 2, 5, "2000-03-27 00:00:48");

/*Table structure for table legalTransition  */
CREATE TABLE legalTransition (
  currentStatus int NOT NULL,
  possibleStatus int NOT NULL,
  PRIMARY KEY (currentStatus, possibleStatus),
  KEY currentStatus (currentStatus),
  KEY possibleStatus (possibleStatus),
  CONSTRAINT legalTransition_ibfk_1 FOREIGN KEY (currentStatus) REFERENCES status (sid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT legalTransition_ibfk_2 FOREIGN KEY (possibleStatus) REFERENCES status (sid) ON DELETE CASCADE ON UPDATE CASCADE
);

insert into legalTransition (currentStatus, possibleStatus) values
(1, 2),
(2, 3),
(3, 2),
(3, 4),
(4, 2),
(4, 5),
(6, 7),
(7, 8),
(8, 7),
(8, 9),
(9, 7),
(9, 10),
(10, 7),
(10, 11),
(12, 13),
(13, 14),
(14, 13),
(14, 15);
