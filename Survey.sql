-- MySQL version of the database schema for the Survey extension.
-- Licence: GNU GPL v3+
-- Author: Jeroen De Dauw < jeroendedauw@gmail.com >

-- Surveys
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/surveys (
  survey_id                SMALLINT unsigned   NOT NULL auto_increment PRIMARY KEY,
  survey_name              VARCHAR(255)        NOT NULL,
  survey_enabled           TINYINT             NOT NULL default '0'
) /*$wgDBTableOptions*/;

-- Questions
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/survey_questions (
  question_id              INT(10) unsigned    NOT NULL auto_increment PRIMARY KEY,
  question_survey_id       SMALLINT unsigned   NOT NULL, -- Foreign key: surveys.survey_id
  question_text            TEXT                NOT NULL,
  question_type            INT(2) unsigned     NOT NULL,
  question_required        INT(2) unsigned     NOT NULL,
  question_answers         BLOB                NOT NULL,
  question_removed         TINYINT             NOT NULL default '0'
) /*$wgDBTableOptions*/;

-- Submissions
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/survey_submissions (
  submission_id            INT(10) unsigned    NOT NULL auto_increment PRIMARY KEY,
  submission_survey_id     SMALLINT unsigned   NOT NULL, -- Foreign key: surveys.survey_id
  submission_user_name     VARCHAR(255)        NOT NULL, -- The person that made the submission (account name or ip)
  submission_page_id       INT(10) unsigned    NULL, -- The id of the page the submission was made on 
  submission_time          CHAR(14) binary     NOT NULL default '' -- The time the submission was made  
) /*$wgDBTableOptions*/;

-- Answers
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/survey_answers (
  answer_id                SMALLINT unsigned   NOT NULL auto_increment PRIMARY KEY,
  answer_submission_id     INT(10) unsigned    NOT NULL, -- Foreign key: survey_submissions.submission_id
  answer_question_id       INT(10) unsigned    NOT NULL, -- Foreign key: survey_questions.question_id
  answer_text              BLOB                NOT NULL
) /*$wgDBTableOptions*/;