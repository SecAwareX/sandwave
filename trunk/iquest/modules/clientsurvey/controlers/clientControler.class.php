<?php
/**
 *	dashboardControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 24 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

class clientsurvey_clientControler  extends BCcontroler_BCControler
{
    
    
    
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        $this->loadUserData($_SESSION["userID"]);
        
    }
    
    public function showDashboard()
    {
        $this->loadForms($_SESSION["userName"]);
    }
    
    
    protected function loadUserData($p_iUserID)
    {
        $sSQL = "SELECT * FROM users where userID=:user ";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":user", $p_iUserID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            $aDecrypted = $this->oCrypt-> multiDecrypt($aResult);
            $this->aData["userScreenname"] = $aDecrypted["userScreenname"];
        }
    }
    
   
    
    public function surveyForm()
    {  
        $this->loadForms($_SESSION["userName"]);
        
        $this->aData["sFormMessage"] = "";
        $this->aData["sFormSucces"] = "";
        $this->aData["sFormStatus"] = "init";
        
        //Check if there is a surveyID and if this is a existing one
        if($this->checkClientFormOpen($this->oRequest->getParameter(2),$_SESSION["userName"] ))
        {
            $oQuestForm = $this->loadSurveyForm($this->oRequest->getParameter(2));
            if($oQuestForm instanceof Surveypanel_Models_iquestForms_iquestForm)
            {
              
                if(BChelpers_formHandler::formSend("sendForm"))
                {
                   
                    $this->prepareFormValidation($oQuestForm);
                    if(!BChelpers_formHandler::handleForm())
                    {
                        $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                        $this->aData["surveyForm"] = $oQuestForm;
                        $this->aData["sFormStatus"] = "send";
                        
                    }else {
                       
                        //Save answers
                        Surveypanel_Models_iquestForms_iquestFormDatamapper::saveClientAnswers($this->oDB,$oQuestForm);
                        Surveypanel_Models_iquestForms_iquestFormDatamapper::closeClientForm($this->oDB, $oQuestForm, $_SESSION["userName"]);
                        $this->aData["surveyForm"] = $oQuestForm;
                        
                        $iScore = 0;
                        $sScoreExplanation = "";
                        if($oQuestForm->getType() == "Simple") {
                            $iScore = Surveypanel_Models_iquestForms_iquestFormDatamapper::calculateScoreForSimple($this->oDB,$oQuestForm);
                        
                            $aScoreRecords = Surveypanel_Models_iquestForms_iquestFormDatamapper::loadFormScores($this->oDB,$oQuestForm);
                            
                            foreach($aScoreRecords AS $aScoreRecord)
                            {
                                if($aScoreRecord["comparison"] == "range")
                                {
                                    if($iScore >= $aScoreRecord["scoreLow"] && $iScore <= $aScoreRecord["scoreHigh"])
                                    {
                                        $sScoreExplanation = $aScoreRecord["scoreDescription"];
                                        break;
                                    }
                                    
                                } else {
                                    if($aScoreRecord["comparison"] == "Kleiner dan")
                                    {
                                        if($iScore < $aScoreRecord["scoreLow"])
                                        {
                                            $sScoreExplanation = $aScoreRecord["scoreDescription"];
                                            break;
                                        }
                                    }elseif($aScoreRecord["comparison"] == "Groter dan") {
                                        
                                        if($iScore > $aScoreRecord["scoreLow"])
                                        {
                                            $sScoreExplanation = $aScoreRecord["scoreDescription"];
                                            break;
                                        }
                                    }elseif($aScoreRecord["comparison"] == "Kleiner of gelijk aan") {
                                        if($iScore <= $aScoreRecord["scoreLow"])
                                        {
                                            $sScoreExplanation = $aScoreRecord["scoreDescription"];
                                            break;
                                        }
                                    }elseif($aScoreRecord["comparison"] == "Groter of gelijk aan") {
                                        if($iScore >= $aScoreRecord["scoreLow"])
                                        {
                                            $sScoreExplanation = $aScoreRecord["scoreDescription"];
                                            break;
                                        }
                                    }elseif ($aScoreRecord["comparison"] == "Gelijk aan") {
                                        if($iScore == $aScoreRecord["scoreLow"])
                                        {
                                            $sScoreExplanation = $aScoreRecord["scoreDescription"];
                                            break;
                                        }
                                    }
                                }
                            }
                            $this->aData["sFormStatus"] = "ready";
                            $this->aData["sFormSucces"] = "Formulier is succesvol verstuurd <br >
                        <h2>Uw score : ".$iScore."</h2>
                        <h3>Toelichting</h3>".$sScoreExplanation
                        ;
                        }elseif($oQuestForm->getType() == "Medium") {
                            //Verry specific for PTTS
                            
                            //There is only one scoreDescription
                            //$aScoreRecords = Surveypanel_Models_iquestForms_iquestFormDatamapper::loadFormScores($this->oDB,$oQuestForm);
                            //$sScoreExplanation = $aScoreRecords[0]["scoreDescription"];
                            
                            $aScores = array(); 
                            
                            //getScoreGroups
                            $aScoreGroups = Surveypanel_Models_iquestForms_iquestFormDatamapper::loadScoreGroups($this->oDB,$oQuestForm);
                           
                            foreach($aScoreGroups as $aGroup)
                            {
                                if(!isset($aScores[$aGroup["scoreGroup"]]))
                                {
                                    $aScores[$aGroup["scoreGroup"]] = 0;
                                }
                                
                                $aParentQuestions = Surveypanel_Models_iquestForms_iquestFormDatamapper::loadParentQuestionsWithRange($this->oDB,$oQuestForm, $aGroup["startRange"], $aGroup["endRange"]);
                                
                                foreach ($aParentQuestions AS $aQuestion)
                                {
                                    $iQuestionScore = Surveypanel_Models_iquestForms_iquestFormDatamapper::calculateScoreForMedium($this->oDB,$oQuestForm, $aQuestion["questionID"]);
                                    
                                    $aSubquestions = Surveypanel_Models_iquestForms_iquestFormDatamapper::loadSubQuestions($this->oDB,$oQuestForm, $aQuestion["questionID"]);
                                   
                                    if(!empty($aSubquestions))
                                    {
                                        $iSubscore = 0;
                                        foreach($aSubquestions AS $aSubQuestion)
                                        {
                                            $iScore = Surveypanel_Models_iquestForms_iquestFormDatamapper::calculateScoreForMedium($this->oDB,$oQuestForm, $aSubQuestion["questionID"]);
                                            $iSubscore = ($iSubscore+$iScore);
                                            
                                        }
                                    }
                                    
                                    if( ($iQuestionScore + $iSubscore) > 0)
                                    {
                                        $aScores[$aGroup["scoreGroup"]]++;
                                    }
                                }
                            
                            }
                            
                            $this->aData["sFormStatus"] = "ready";
                            $this->aData["sFormSucces"] = "Formulier is succesvol verstuurd";
                            foreach($aScores as $sGroup => $iScore)
                            {
                                //$this->aData["sFormSucces"] .= "<strong>".$sGroup." : </strong>".$iScore."<br>";
                            }
                            
                           // $this->aData["sFormSucces"] .= "<h3>Toelichting</h3>".$sScoreExplanation;
                            $this->aData["sFormSucces"] .= "<h3>Toelichting</h3>De uitslag van het formulier wordt op uw eerstvolgende afspraak met de arts besproken";
                            
                        }elseif($oQuestForm->getType() == "Complex") {
                            $this->aData["sFormStatus"] = "ready";
                            $this->aData["sFormSucces"] = "Formulier is succesvol verstuurd";
                            $this->aData["sFormSucces"] .= "<h3>Toelichting</h3>De uitslag van het formulier wordt op uw eerstvolgende afspraak met de arts besproken";
                            
                        }
                        
                        
                        
                    }
                }else {
                    $this->aData["surveyForm"] = $oQuestForm;
                }
             
            }else 
                {
                    $this->aData["sFormMessage"] = "Er is een probleem ontstaan bij het laden van het formulier";
                }
        }else
            {
                $this->oRequest->redirect(HOST."/".APPLICATIONPATH."iquestclient/#forms");
            }
    }
    
    private function prepareFormValidation($p_oQuestForm)
    {
        $aQuestions = $p_oQuestForm->getQuestions();
       // echo "<pre>";
       // print_r($aQuestions);
       // echo "</pre>";
        
        foreach($aQuestions AS $oQuestion)
        {
            switch($oQuestion->getQuestionType()){
                
                case "1":
                    $sValidation = "surveypanel_checkLibary::checkPlainText";
                break;
                case "2":
                    $sValidation = "surveypanel_checkLibary::checkPlainText";
                break;
                case "3":
                    $sValidation = "surveypanel_checkLibary::checkScore";
                break;
            }
            BChelpers_formHandler::addFieldCheckNew("question_".$oQuestion->getQuestionID(),$oQuestion->getQuestion(),true,$sValidation," Geef een geldig antwoord");
        }
        
    }
    
    
    private function loadForms($p_sUser)
    {
        
        $sSQL = "SELECT * FROM clientforms
                 LEFT JOIN forms ON clientforms.formID = forms.formID
                 where clientforms.clientID=(SELECT clientID FROM clients WHERE email=:client)";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_sUser);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
       
        if(!empty($aResult))
        {  
            $this->aData["clientForms"] = $aResult;
        }
        
    }
    
    private function checkClientForm($p_iFormID, $p_iClient)
    {
        $sSQL = "SELECT count(clientFormID) As iCheck FROM clientforms
                 LEFT JOIN forms ON clientforms.formID = forms.formID
                 where clientFormID =:formID AND clientforms.clientID=(SELECT clientID FROM clients WHERE email=:client)";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClient);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["iCheck"] == 1)
        {
            return true;
        }else {
            return false;
        }
       
    }
    
    private function checkClientFormOpen($p_iFormID, $p_iClient)
    {
        $sSQL = "SELECT count(clientFormID) As iCheck FROM clientforms
                 LEFT JOIN forms ON clientforms.formID = forms.formID
                 where clientFormID =:formID AND status = 'open' AND clientforms.clientID=(SELECT clientID FROM clients WHERE email=:client)";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClient);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["iCheck"] == 1)
        {
            return true;
        }else {
            return false;
        }
        
    }
    
    private function loadSurveyForm($p_iFormID)
    {
        $sSQL = " SELECT formID FROM clientforms WHERE clientFormID =:formID";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
       
        if(!empty($aResult))
        {
            return surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($aResult["formID"], $this->oDB, $p_iFormID);
        }
        
    }
}

