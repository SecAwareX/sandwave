<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BClibarys
 * @subpackage BCLogin
 * @project BCApplication framework
 * @version  1.0
 * @date 2012-10-06
 * @description :
 *  - check methods for the loginform
 *
 */

abstract class BClibarys_BClogin_BCCheckLibaryLogin
{
	/**
	 *  checkUserName() : 
	 *  @access public
	 *  @param string $p_sUserName
	 *  @return  boolean
	 */
	public static function checkUserName($p_sUserName)
	{
		if(preg_match("/^[a-z0-9]+$/i",$p_sUserName))
		{
			return TRUE;
		}else
			{
				return FALSE;
			}
	}
	
	/**
	 *  checkUserEmail() : 
	 *  @access public
	 *  @param string $p_sUserEmail
	 *  @return  boolean
	 */
	public static function checkUserEmail($p_sUserEmail)
	{
		if(preg_match("/^[a-zA-Z0-9 -\._]{3,}@[a-zA-Z0-9 -\._]{3,}\.(nl|com|be|eu)$/i",$p_sUserEmail))
		{
			return TRUE;
		}else
			{
				return FALSE;
			}
	}
	
	/**
	 *  checkPassWord() : 
	 *  @access public
	 *  @param string $p_sPassword
	 *  @return  boolean
	 */
	public static function checkPassWord($p_sPassword)
	{
		if(preg_match("/^[a-z0-9!@#&*]+$/i",$p_sPassword))
		{
			return TRUE;
		}else
			{
				return FALSE;
			}
	}
}
?>