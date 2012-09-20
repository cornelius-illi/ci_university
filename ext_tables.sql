# TYPO3 Extension Manager dump 1.1
#
# Host: localhost    Database: hpitypo3
#--------------------------------------------------------

#
# Table structure for table "tx_ciuniversity_domain_model_course"
#
CREATE TABLE tx_ciuniversity_domain_model_course (
  uid int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  tstamp int(11) unsigned NOT NULL default '0',
  crdate int(11) unsigned NOT NULL default '0',
  cruser_id int(11) unsigned NOT NULL default '0',
  sorting int(10) unsigned NOT NULL default '0',
  deleted tinyint(4) unsigned NOT NULL default '0',
  hidden tinyint(4) unsigned NOT NULL default '0',
  fe_group int(11) NOT NULL default '0',
  
  unit_id int(11) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  modules int(11) unsigned NOT NULL default '0',
  semester varchar(255) NOT NULL default '',
  credits int(11) unsigned NOT NULL default '0',
  sws int(11) unsigned NOT NULL default '0',
  program varchar(255) NOT NULL default '',
  lecturer int(11) unsigned NOT NULL default '0',
  teaching_form varchar(255) NOT NULL default '',
  graded tinyint(4) unsigned NOT NULL default '0',
  enrolment_deadline varchar(255) NOT NULL default '',
  enrolment_type varchar(255) NOT NULL default '',
  
  max_participants int(11) unsigned NOT NULL default '0',
  other_lecturers int(11) unsigned NOT NULL default '0',
  chair int(11) unsigned NOT NULL default '0',
  tutors int(11) unsigned NOT NULL default '0',
  description text NOT NULL,
  requirements text NOT NULL,
  examination text NOT NULL,
  literature text NOT NULL,
  learning text NOT NULL,
  dates text NOT NULL,
  url tinytext NOT NULL,
  only_use_course_page_contents tinyint(4) unsigned NOT NULL default '0',
  year int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (uid),
  KEY parent (pid),
);


#
# Table structure for table "tx_ciuniversity_domain_model_chair"
#
CREATE TABLE tx_ciuniversity_domain_model_chair (
  uid int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  tstamp int(11) unsigned NOT NULL default '0',
  crdate int(11) unsigned NOT NULL default '0',
  cruser_id int(11) unsigned NOT NULL default '0',
  sorting int(10) unsigned NOT NULL default '0',
  deleted tinyint(4) unsigned NOT NULL default '0',
  hidden tinyint(4) unsigned NOT NULL default '0',
  fe_group int(11) NOT NULL default '0',
  
  title varchar(255) NOT NULL default '',
  head int(11) unsigned NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  
  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table "tx_ciuniversity_domain_model_person"
#
CREATE TABLE tx_ciuniversity_domain_model_person (
  uid int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  tstamp int(11) unsigned NOT NULL default '0',
  crdate int(11) unsigned NOT NULL default '0',
  cruser_id int(11) unsigned NOT NULL default '0',
  sorting int(10) unsigned NOT NULL default '0',
  deleted tinyint(4) unsigned NOT NULL default '0',
  hidden tinyint(4) unsigned NOT NULL default '0',
  fe_group int(11) NOT NULL default '0',
  
  lastname varchar(255) NOT NULL default '',
  firstname varchar(255) NOT NULL default '',
  customline varchar(255) NOT NULL default '',
  actitle varchar(255) NOT NULL default '',
  phone varchar(255) NOT NULL default '',
  fax varchar(255) NOT NULL default '',
  room varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  sections tinytext NOT NULL,
  chairs int(11) unsigned NOT NULL default '0',
  customtext text NOT NULL,
  image blob NOT NULL,
  url tinytext NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table "tx_ciuniversity_domain_model_module"
#
CREATE TABLE tx_ciuniversity_domain_model_module (
  uid int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  tstamp int(11) unsigned NOT NULL default '0',
  crdate int(11) unsigned NOT NULL default '0',
  cruser_id int(11) unsigned NOT NULL default '0',
  sorting int(10) unsigned NOT NULL default '0',
  deleted tinyint(4) unsigned NOT NULL default '0',
  hidden tinyint(4) unsigned NOT NULL default '0',
  fe_group int(11) NOT NULL default '0',
  
  title tinytext NOT NULL,
  identifier varchar(50) NOT NULL default '',
  modulegroup varchar(255) NOT NULL default '',
  
  PRIMARY KEY (uid),
  KEY parent (pid)
 );
 
 #
# Table structure for table "tx_ciuniversity_course_person_mm"
#
CREATE TABLE tx_ciuniversity_course_person_mm (
  uid_local int(11) unsigned NOT NULL default '0',
  uid_foreign int(11) unsigned NOT NULL default '0',
  type varchar(60) NOT NULL default '',
  
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table "tx_ciuniversity_course_module_mm"
#
CREATE TABLE tx_ciuniversity_course_module_mm (
  uid_local int(11) unsigned NOT NULL default '0',
  uid_foreign int(11) unsigned NOT NULL default '0',
 
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table "tx_ciuniversity_person_chair_mm"
#
CREATE TABLE tx_ciuniversity_person_chair_mm (
  uid_local int(11) unsigned NOT NULL default '0',
  uid_foreign int(11) unsigned NOT NULL default '0',
 
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);