<?php
/**
 *	checkLibary.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 28 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class login_checkLibary
{
    /**
     * Check if the format of the string has a email structure as username
     *
     * @param string $p_sEmail
     * @return boolean true when check succeed , if not false
     */
    public static function checkUserNameAdmin($p_sUserName)
    {
        if(filter_var($p_sUserName, FILTER_VALIDATE_EMAIL))
        {
            //The format of the string ia oke
            return true;
        }else
        {
            return false;
        }
    }
    
    /**
     * check the format of a pass
     * 
     * @param string $p_sPass
     * @return boolean true when it is a valid password format, else false
     */
    public static function checkPass($p_sPass)
    {
       if(preg_match("/^[a-zA-Z0-9!@#&*_-]{8}$/",rtrim($p_sPass)))
        {
            return true;
           
        }else 
            {
                return false;
               
            }
    }
    
    public static function checkIPRegKey($p_sKey)
    {
        return true;
    }
    
    public static function checkSecretKey($p_sKey)
    {
        if(preg_match("/^[a-z0-9]{32}$/",$p_sKey))
        {
            return true;
        }else {
            return false;
        }
    }
    
    
}