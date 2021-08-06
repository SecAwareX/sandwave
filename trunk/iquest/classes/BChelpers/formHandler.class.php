<?php
/**
 *	formHandler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 28 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class BChelpers_formHandler
{
    
    public static $aFormErrors = array("mandatoryFields" => array(),"formatErrors" => array());
    
    private static $aFieldChecks = array();
    
   public static function formSend($p_sSubmitName)
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST[$p_sSubmitName]))
        {
            return true;
        }else 
            {
                return false;
            }
    }
    
    public static function handleForm()
    {
       $bHandled = true;
       foreach($_POST As $sValueName => $mValue)
       {
              //Loop the POST vars
              //When exists in field checks, do some controls
                if(isset(self::$aFieldChecks[$sValueName]))
                {
                    //Check mandatory
                    if(self::$aFieldChecks[$sValueName]["mandatory"] && $mValue == "")
                    {
                        $bHandled = false;
                        self::$aFormErrors["mandatoryFields"]["$sValueName"] = "Is een verplicht veld";
                    }
                    
                    //CheckFormat
                    if(self::$aFieldChecks[$sValueName]["checkfunction"] !="")
                    {
                        
                        $aCheckFuntionParts = explode("::",self::$aFieldChecks[$sValueName]["checkfunction"]);
                       
                       if(!call_user_func(self::$aFieldChecks[$sValueName]["checkfunction"],$mValue))
                        //if(!$aCheckFuntionParts[0]::$aCheckFuntionParts[1]($mValue))
                        {
                            $bHandled = false;
                            
                            if(isset(self::$aFieldChecks[$sValueName]["error"]) && self::$aFieldChecks[$sValueName]["error"] !="")
                            {
                                self::$aFormErrors["formatErrors"]["$sValueName"] = self::$aFieldChecks[$sValueName]["error"];
                            } else {
                                self::$aFormErrors["formatErrors"]["$sValueName"] = $sValueName." bevat ongeldige tekens";
                            }
                        }
                    }
                 }
       }
          
            return $bHandled;
       
    }
    
    /**
     * Deprecated :
     * 
     * @param string  $p_sFieldName
     * @param string  $p_bMandatory
     * @param string $p_sCheckFunction
     * @param string $p_sCheckErrorMessage
     * @todo : change all the calls in the scripts
     */
    public static function addFieldCheck($p_sFieldName,$p_bMandatory,$p_sCheckFunction = "",$p_sCheckErrorMessage = "")
    {
        self::$aFieldChecks[$p_sFieldName] = array("mandatory"=>$p_bMandatory,"checkfunction"=>$p_sCheckFunction,"error"=>$p_sCheckErrorMessage);
    }
    
    public static function addFieldCheckNew($p_sFieldName,$p_sScreenFieldName,$p_bMandatory,$p_sCheckFunction = "",$p_sCheckErrorMessage = "")
    {
        self::$aFieldChecks[$p_sFieldName] = array("fieldname" =>$p_sScreenFieldName,"mandatory"=>$p_bMandatory,"checkfunction"=>$p_sCheckFunction,"error"=>$p_sCheckErrorMessage);
    }
    
    public static function getValue($p_sValueName)
    {
        if(isset($_POST[$p_sValueName]))
        {
            return $_POST[$p_sValueName];
        }
    }
    
    public static function getFormErrorsAsString()
    {
        $sErrorString = "";
        if(count(self::$aFormErrors["mandatoryFields"]) > 0 )
        {
            $sErrorString .= "Niet alle verplichte velden zijn ingevuld<br />";
            foreach(self::$aFormErrors["mandatoryFields"] as $sField => $sError)
            {
                $sErrorString .= self::$aFieldChecks[$sField]["fieldname"]." : is een verplicht veld.<br />";
            }
            
        }elseif(count(self::$aFormErrors["formatErrors"]) > 0 )
        {
            $sErrorString = "<strong>Niet alle velden zijn correct ingevuld!</strong><br />";
            foreach(self::$aFormErrors["formatErrors"] as $sField => $sError)
            {
                $sErrorString .= self::$aFieldChecks[$sField]["fieldname"]." : ".$sError."<br />";
            }
            
        }
        
        return $sErrorString;
    }
    
    public function getFormErrors()
    {
        return self::$aFormErrors;
    }
    
    
    public static function convertFormValues($p_aValueArray)
    {
       
        if(is_array($p_aValueArray) && !empty($p_aValueArray))
        {
            foreach($p_aValueArray as $sFieldName =>$sFieldValue)
            {
                if(is_array($sFieldValue))
                {
                   
                    foreach($sFieldValue as $sField => $sVlaue)
                    {
                      
                       if(isset($_POST[$sField]))
                       {
                           $p_aValueArray[$sFieldName][$sField] = $_POST[$sField];
                       }
                    }
                }else 
                    {
                        if(isset($_POST[$sFieldName]))
                        {
                            $p_aValueArray[$sFieldName] = $_POST[$sFieldName];
                        }
                    }
            }
        }
        
       return $p_aValueArray;
    }
}