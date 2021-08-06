<?php
/**
 * 
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package iQuest 
 * @subpackage Config 
 * @project  Mareis-BV - iQuest 
 * @version  1.0
 * @date 20-08-2018
 * @description  : ConfigurationFile devlopment environment
 * 
 * 	- Domain
 * 	- Application path
 *  - Database 
 *  - Encrytion
 *
 * *
 */


/**
 * Application BasePath
 */
define("HOST","http://projecten.balancecoding.nl/");
define("APPLICATIONPATH","mareis-b.v/iquest/web/");
define("APPLICATIONREALPATH","C:\\domains\\balancecoding.nl\\subdomeinen\\projecten\\wwwroot\\mareis-b.v\\iquest\\iquest\\classes\\");


/**
 * Database
 */
define("DBHOST","localhost");
define("DBPORT",3306);
define("DBNAME","<secret>");
define("DBUSER","<secret>");
define("DBPASS","<secret>");

/**
 * Notification EmailAdresses
 */
define("ADMINEMAIL","<secret>.nl");
define("DEVEMAIL","<secret>.nl");

/**
 * Secret Keys
 */
define("KEY", "<secret>");
define("Vector", "<secret>");

/**
 * Max login attempts
 * */
define("MAX_ATTEMPTS",20);

/**
 * Mails
 * 
 */
define("SMPT","smtp.<secret>.nl");
define("MAILHOST","<secret>.nl");
define("MAILFROMNAME","<secret>");
