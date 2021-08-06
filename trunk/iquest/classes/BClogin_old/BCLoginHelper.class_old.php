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
abstract class BClogin_BCLoginHelper
{
	
	/**
	 * isLoggedIn(): checks if al the session vars exists and compare them, if all match we ar logged in
	 * @access static public 
	 * @return boolean True when we are logged in 
	 */
	public static function isLoggedIn()
	{
		$bLoggedIn=FALSE;
		if( (isset($_SESSION["LogedIn"])&& $_SESSION["LogedIn"]==TRUE) && (isset($_SESSION["UserID"])) && 
			(isset($_SESSION["IP"])&& $_SESSION["IP"]== $_SERVER["REMOTE_ADDR"]) && 
			(isset($_SESSION["User_Agent"])&& $_SESSION["User_Agent"] == $_SERVER["HTTP_USER_AGENT"]) )
		{
			$bLoggedIn=TRUE;
			
		}else
			{
				$bLoggedIn=FALSE;
			}
			
		return $bLoggedIn;
	}
	
	
	/**
	 * logOut() : unset alle session vars, so we are logging out
	 * @access static public 
	 * @return void
	 */
	public static function logOut()
	{
		unset($_SESSION["LogedIn"]);
		unset($_SESSION["UserID"]);
		unset($_SESSION["Rights"]);
		unset($_SESSION["IP"]);
		unset($_SESSION["User_Agent"]);
		unset($_SESSION["ATTEMPTS"]);
		session_destroy();
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
		$sPassWord.= substr("!@#&*",mt_rand(0,4),1);
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyz",mt_rand(0,25),1);
		$sPassWord.= substr("!@#&*",mt_rand(0,4),1);
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",mt_rand(0,61),1);
		$sPassWord.= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",mt_rand(0,51),1);
		
		
		return $sPassWord;
	}
	
}