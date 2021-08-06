<?php
/**
 *	apiFormControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 21 jan. 2019
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */


class surveypanel_apiFormControler extends surveypanel_formControler
{
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        
        
    }
    
    public function jqDeleteOption()
    {
        
        $this->aData["APIAction"] = "jqDeleteOption";
      
        
        if(isset($_POST["optionID"])) {
            $this->aData["APIStatus"] = true;
           
            if(surveypanel_models_iquestforms_iquestFormDatamapper::checkOptionByID($this->oDB, $_POST["optionID"])) {
                
                if(surveypanel_models_iquestforms_iquestFormDatamapper::deleteOptionByID($this->oDB,$_POST["optionID"]) == 1){
                    $this->aData["APIMessage"] = "Optie ( ".$_POST["optionID"]." ) is succesvol verwijderd ";
                }else {
                    $this->aData["APIStatus"] = false;
                    $this->aData["APIMessage"] = "Optie ( ".$_POST["optionID"]." ) is niet verwijderd ";
                }
                
            }else {
                $this->aData["APIStatus"] = false;
                $this->aData["APIMessage"] = "Optie ( ".$_POST["optionID"].") bestaat niet kan deze niet verwijderen ";
            }
        }else {
            $this->aData["APIStatus"] = false;
            $this->aData["APIMessage"] = "ID ontbreekt in request! Kan optie niet verwijderen";
        }
        
        
        echo json_encode($this->aData);
        exit();
    }
    
    public function jqChangeOption()
    {
        
        $this->aData["APIAction"] = "jqChangeOption";
        
        
        if(isset($_POST["optionID"]) && isset($_POST["option"]) && isset($_POST["optionScore"])) {
            $this->aData["APIStatus"] = true;
            if(surveypanel_models_iquestforms_iquestFormDatamapper::checkOptionByID($this->oDB, $_POST["optionID"])) {
                
                if(surveypanel_models_iquestforms_iquestFormDatamapper::changeOptionByID($this->oDB,$_POST["optionID"],$_POST["option"],$_POST["optionScore"]) == 1){
                    $this->aData["APIMessage"] = "Optie ( ".$_POST["optionID"]." ) is succesvol gewijzigd ";
                }else {
                    $this->aData["APIStatus"] = false;
                    $this->aData["APIMessage"] = "Optie ( ".$_POST["optionID"]." ) is niet gewijzigd =>>".$_POST["optionScore"];
                }
                
            }else {
                $this->aData["APIStatus"] = false;
                $this->aData["APIMessage"] = "Optie ( ".$_POST["optionID"].") bestaat niet kan deze niet wijzigen ";
            }
        }else {
            $this->aData["APIStatus"] = false;
            $this->aData["APIMessage"] = "ID ontbreekt in request! Kan optie niet wijzigen";
        }
        
        
        echo json_encode($this->aData);
        exit();
    }
    
    public function jqAddOption()
    {
        
        $this->aData["APIAction"] = "jqAddOption";
        $this->aData["APIStatus"] = true;
        $this->aData["APIMessage"] = "Geldig Request kan toevoegen";
        
        if(isset($_POST["option"]) && isset($_POST["optionScore"]) && isset($_POST["questionID"])) {
           
            if(!is_numeric($_POST["optionScore"]))
            {
                $this->aData["APIStatus"] = false;
                $this->aData["APIMessage"] = "Score moet een geheel getal zijn ";
            }
            
            if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestionID($this->oDB, $_POST["questionID"])) {
                $this->aData["APIStatus"] = false;
                $this->aData["APIMessage"] = "Er bestaat geen vraag met de ID ".$_POST["questionID"];
            }
            
            if(surveypanel_models_iquestforms_iquestFormDatamapper::checkOptionRecord($this->oDB, $_POST["questionID"],$_POST["option"],$_POST["optionScore"])) {
                $this->aData["APIStatus"] = false;
                $this->aData["APIMessage"] = "De opgegeven optie bestaat al! VraagID ".$_POST["questionID"]." => Option : ".$_POST["option"]." Score :".$_POST["optionScore"];
            }
            
            if( $this->aData["APIStatus"]) {
                $iOptionID = surveypanel_models_iquestforms_iquestFormDatamapper::saveOption($this->oDB, $_POST["questionID"],$_POST["option"],$_POST["optionScore"]);
                if($iOptionID > 0)
                {
                    $this->aData["APIMessage"] = "Optie ".$_POST["option"]." is opgeslagen";
                    $this->aData["newOptionID"] = $iOptionID;
                }else {
                    $this->aData["APIStatus"] = false;
                    $this->aData["APIMessage"] = "Optie ".$_POST["option"]." is NIET opgeslagen";
                }
            }
            
        }else {
            $this->aData["APIStatus"] = false;
            $this->aData["APIMessage"] = "Er ontbreken 1 of meer parameters! Kan optie niet toevoegen";
        }
        
        
        echo json_encode($this->aData);
        exit();
    }
}