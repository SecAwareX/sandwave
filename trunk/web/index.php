<?php
session_start();
/**
 *
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package iQuest
 * @subpackage surveydashboard
 * @project  Mareis-BV - iQuest
 * @version  1.0
 * @description  : Application bootstrap page
 * 
 * - Includes The core scripts
 *   Config 
 *   AutoLoad 
 *   Application
 *
 * *
 */


/** 
 * Set error reporting for some testing 
 * We excluded Depracated notes, because this ones are triggerd by the encryption module. 
 */
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set("display_errors", 1);

//Config =>change this when environment changes
require_once("../iquest/config/config_dev.inc.php");


/**
 * start autoloader , this now ready for use
 */
require_once("../iquest/classes/BCautoload/BCAutoLoader.class.php");
require_once("../iquest/classes/BCautoload/BCAutoLoadException.class.php");
BCcore_BCAutoload_BCAutoLoader::Register("BCcore_BCautoload_BCAutoloader::Autoload");

/**
 * Start Application
 */

try {
    
   BCApplication_BCApplication::run();
	
}catch(Exception $e)
{
	echo $e->getMessage();
}
