<?php
/**
 *	converters.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 28 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class BChelpers_converters
{
    
   public static function convertDate($p_sType,$p_sDate)
   {
       if($p_sType == "SQL")
       {
           $aDate = explode("-",$p_sDate);
           
            $sDate = $aDate[2]."-".$aDate[1]."-".$aDate[0];
            return $sDate;
          
       }elseif($p_sType == "view")
       {
           $aDate = explode("-",$p_sDate);
           
           if(isset($aDate[0]) && isset($aDate[1]) && isset($aDate[2]))
           {
                $sDate = $aDate[2]."-".$aDate[1]."-".$aDate[0];
           }else {
               $sDate ="";
           }
           return $sDate;
           
       }
       
       
   }
}