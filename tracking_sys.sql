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

/*Table structure for table status */
CREATE TABLE status (
  sid int NOT NULL AUTO_INCREMENT,
  sname varchar(1000) DEFAULT NULL,
  PRIMARY KEY (sid)
);

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
