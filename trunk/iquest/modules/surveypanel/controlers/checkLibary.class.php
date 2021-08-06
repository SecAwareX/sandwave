<?php
/**
 *	checkLibary.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 4 sep. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class surveypanel_checkLibary
{
    public static function checkScreenname($p_sScreenName)
    {
        return true;
    }
    
    public static function checkCompanyname($p_sCompanyName)
    {
        return true;
    }
    
    
    public static function checkMandatory($p_mValue)
    {
        return true;
    }
    
    public static function checkGender($p_sGender)
    {
        if($p_sGender == "male" || $p_sGender == "female")
        {
            return true;
        }else 
        {
            return false;
        }
        
    }
    
    
    public static function checkCompanyID($p_iCompanyID)
    {
        if($p_iCompanyID == "0")
        {
            return false;
        }else{
            return true;
        }
    }
    
    public static function checkEmail($p_sEmail)
    {
        if(filter_var($p_sEmail, FILTER_VALIDATE_EMAIL))
        {
            //The format of the string ia oke
            return true;
        }else
        {
            return false;
        }
    }
    
    public static function checkDate($p_sDate)
    {
        if($p_sDate == date("d-m-Y"))
        {
            return false;
        }else {
            return true;
        }
    }
    
    public static function checkQuestionType($p_iType)
    {
        if($p_iType > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    public static function checkPlainText($p_sText)
    {
        return true;
    }
    
    public static function checkScore($p_nScore)
    {
        if(preg_match("/_/",$p_nScore)) {
            $nScore = explode("_",$p_nScore);
            if(is_numeric($nScore[0]))
            {
                return true;
            }else{
                return false;
            }
            
        }else {
            if(is_numeric($p_nScore))
            {
                return true;
            }else{
                return false;
            }
        }
    }
}