<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BCLibarys
 * @subpackage BCLogin
 * @project BCApplication framework
 * @version  1.0
 * @date 2012-10-05
 * @description :
 *  - class holds some abstact static function for helping the login 
 *
 */
abstract class login_loginHelper
{
	
	/**
	 * isLoggedIn(): checks if al the session vars exists and compare them, if all match we ar logged in
	 * @access static public 
	 * @return boolean True when we are logged in 
	 */
	public static function isLoggedInOld($p_sUserLevel)
	{
	   
	    if($p_sUserLevel == "Admin")
	    {
	    
    	    if( 
    	           (isset($_SESSION["loggedIN"]) && $_SESSION["loggedIN"] ===true) && 
    	            (isset($_SESSION["fingerPrint"]) && $_SESSION["fingerPrint"] == md5(session_id().$_SERVER["REMOTE_ADDR"].$_SERVER['HTTP_USER_AGENT']))
    	        ) {
    	          return true;  
    	        } else {
    	            return false;
    	        }
	    }else{
	        //clientLogin
	    }
	}
	
	public static function isLoggedIn($p_sUserLevel)
	{
	    
	    if(
	        (isset($_SESSION["loggedIN"]) && $_SESSION["loggedIN"] ===true) && 
	        (isset($_SESSION["userLevel"]) && $_SESSION["userLevel"] == $p_sUserLevel) &&
	        (isset($_SESSION["fingerPrint"]) && $_SESSION["fingerPrint"] == md5(session_id().$_SERVER["REMOTE_ADDR"].$_SERVER['HTTP_USER_AGENT']))
	        ) {
	            return true;
	        }else {
	            return false;
	        }
	}
	
	public static function addLoginvarsClient($p_iUserID,$p_sUserName)
	{
	    $_SESSION["loggedIN"] = true;
	    $_SESSION["userID"] = $p_iUserID;
	    $_SESSION["userName"] = $p_sUserName;
	    $_SESSION["userLevel"] = "Client";
	    $_SESSION["fingerPrint"] = md5(session_id().$_SERVER["REMOTE_ADDR"].$_SERVER['HTTP_USER_AGENT']);
	}
	
	public static function addLoginvarsAdmin($p_iUserID)
	{
	    $_SESSION["loggedIN"] = true;
	    $_SESSION["userID"] = $p_iUserID;
	    $_SESSION["userLevel"] = "Admin";
	    $_SESSION["fingerPrint"] = md5(session_id().$_SERVER["REMOTE_ADDR"].$_SERVER['HTTP_USER_AGENT']);
	}
	
	public static function unsetSessionVars()
	{
	    foreach($_SESSION AS $sKey => $sSessionVar)
	    {
	        unset($_SESSION[$sKey]);
	    }
	    
	   session_regenerate_id();
	   session_destroy();
	   return true;
	}
	
	/**
	 * generateNewPassWord() : creates a new password
	 * @access static public 
	 * @return string
	 */
	public static function generateNewPassWord()
	{
		$sPassWord = "";
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",mt_rand(0,51),1);
		$sPassWord.= substr("0123456789",mt_rand(0,9),1);
		$sPassWord.= substr("ABCDEFGHIJKLMNOPQRSTUVWXYZ",mt_rand(0,25),1);
		$sPassWord.= substr("!@#&*_-",mt_rand(0,4),1);
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyz",mt_rand(0,25),1);
		$sPassWord.= substr("!@#&*_-",mt_rand(0,4),1);
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",mt_rand(0,61),1);
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",mt_rand(0,51),1);
		
	    return $sPassWord;
	}
	
}