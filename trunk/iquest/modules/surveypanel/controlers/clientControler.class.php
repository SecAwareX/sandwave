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

class surveypanel_clientControler extends surveypanel_dashboardControler
{
    
    private $oFormControler;
    private $oCompanyControler;
    
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        $this->loadUserData($_SESSION["userID"]);
        $this->oFormControler = new surveypanel_formControler($p_oRequest, $p_oDB, $p_oCrypt);
        $this->oCompanyControler = new surveypanel_companyControler($p_oRequest, $p_oDB, $p_oCrypt);
    }
    
    public function showClients()
    {
        $aClients = $this->loadClients();
        if(!empty($aClients))
        {
            foreach($aClients AS $aClient)
            {
                $this->aData["clients"][] = $this->oCrypt->multiDecrypt($aClient);
            }
            
            $this->sStatus = "Ready";
            return true;
        } else {
            $this->aData["clients"] = "Geen clienten gevonden";
        }
    }
    
    public function showClient()
    {
        
        $this->aData["sFormSuccesMessage"] = "";
        $this->aData["sFormMessage"] = "";
        if($this->checkClientByID($this->oRequest->getParameter(2)))
        {
            $this->aData["clientData"] = $this->oCrypt->multiDecrypt($this->loadClientByID($this->oRequest->getParameter(2)));
            $this->aData["clientForms"] = $this->loadClientFullForms($this->oRequest->getParameter(2));
            $this->aData["clientName"] = $this->aData["clientData"]["firstName"]." ".$this->aData["clientData"]["lastName"];
        } else {
            $this->aData["sFormMessage"] = "Client bestaat niet";
        }
    }
    
    public function showformresult()
    {
        
        $this->aData["sFormSuccesMessage"] = "";
        $this->aData["sFormMessage"] = "";
        $this->aData["clientID"] = $this->oRequest->getParameter(2);
        if($this->checkClientByID($this->oRequest->getParameter(2)))
        {
            $this->aData["clientData"] = $this->oCrypt->multiDecrypt($this->loadClientByID($this->oRequest->getParameter(2)));
            
            if($this->checkClientFormResult($this->oRequest->getParameter(2),$this->oRequest->getParameter(3),"closed"))
            {
                
                $this->aData["clientForm"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadFullFormByClientformID($this->oDB, $this->oRequest->getParameter(3));
                
                if($this->aData["clientForm"]->getType() == "Simple")
                {
                    $iScore = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForSimple($this->oDB, $this->aData["clientForm"]);
                    
                    $aScoreRecords = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormScores($this->oDB, $this->aData["clientForm"]);
                    
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
                    
                    $this->aData["formScore"]["score"] = $iScore;
                    $this->aData["formScore"]["scoreDescription"] = $sScoreExplanation;
                }elseif($this->aData["clientForm"]->getType() == "Medium") {
                    //Verry specific for PTTS
                    
                    //There is only one scoreDescription
                    $aScoreRecords = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormScores($this->oDB,$this->aData["clientForm"]);
                    $sScoreExplanation = $aScoreRecords[0]["scoreDescription"];
                    
                    $aScores = array();
                    
                    //getScoreGroups
                    $aScoreGroups = surveypanel_models_iquestforms_iquestFormDatamapper::loadScoreGroups($this->oDB,$this->aData["clientForm"]);
                    
                    foreach($aScoreGroups as $aGroup)
                    {
                        if(!isset($aScores[$aGroup["scoreGroup"]]))
                        {
                            $aScores[$aGroup["scoreGroup"]] = 0;
                        }
                        
                        $aParentQuestions = surveypanel_models_iquestforms_iquestFormDatamapper::loadParentQuestionsWithRange($this->oDB,$this->aData["clientForm"], $aGroup["startRange"], $aGroup["endRange"]);
                        
                        foreach ($aParentQuestions AS $aQuestion)
                        {
                            $iQuestionScore = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB,$this->aData["clientForm"], $aQuestion["questionID"]);
                            
                            $aSubquestions = surveypanel_models_iquestforms_iquestFormDatamapper::loadSubQuestions($this->oDB,$this->aData["clientForm"], $aQuestion["questionID"]);
                            
                            if(!empty($aSubquestions))
                            {
                                $iSubscore = 0;
                                foreach($aSubquestions AS $aSubQuestion)
                                {
                                    $iScore = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB,$this->aData["clientForm"], $aSubQuestion["questionID"]);
                                    $iSubscore = ($iSubscore+$iScore);
                                    
                                }
                            }
                            
                            if( ($iQuestionScore + $iSubscore) > 0)
                            {
                                $aScores[$aGroup["scoreGroup"]]++;
                            }
                        }
                        
                    }
                    
                    $this->aData["formScore"]["score"] = "";
                    foreach($aScores as $sGroup => $iScore)
                    {
                        $this->aData["formScore"]["score"] .= "<strong>".$sGroup." : </strong>".$iScore."<br>";
                    }
                    
                    
                    $this->aData["formScore"]["scoreDescription"] = $sScoreExplanation;
                }elseif($this->aData["clientForm"]->getType() == "Complex")
                {
                    //complex forms zijn unique for the scores so we use its form ID to calculate the score
                    if( $this->aData["clientForm"]->getFormID() == "25" ||$this->aData["clientForm"]->getFormID() == "26" )
                    {   
                        
                        $aQuestionGRoups = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionGroups($this->oDB, $this->aData["clientForm"]);
                        foreach($aQuestionGRoups AS $aGroup)
                        {
                            if($aGroup["groupName"] == "Feedbackgericht")
                            {
                                $aGroupQuestion =  surveypanel_models_iquestforms_iquestFormDatamapper::loadGroupQuestions($this->oDB, $this->aData["clientForm"], $aGroup["questionGroupID"]);
                                $iScore_Feedbackgericht = 0;
                                foreach($aGroupQuestion AS $iQuestionID)
                                {   
                                    $iScoreToCalc = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB, $this->aData["clientForm"], $iQuestionID);
                                    $iScore_Feedbackgericht = $iScore_Feedbackgericht + $iScoreToCalc;
                                    
                                }
                              
                            }elseif($aGroup["groupName"] == "Feedforwardgericht"){
                                $aGroupQuestion =  surveypanel_models_iquestforms_iquestFormDatamapper::loadGroupQuestions($this->oDB, $this->aData["clientForm"], $aGroup["questionGroupID"]);
                                $iScore_Feedforwardgericht = 0;
                                foreach($aGroupQuestion AS $iQuestionID)
                                {
                                    $iScoreToCalc = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB, $this->aData["clientForm"], $iQuestionID);
                                    $iScore_Feedforwardgericht = $iScore_Feedforwardgericht + $iScoreToCalc;
                                    
                                }
                               
                            }elseif($aGroup["groupName"] == "People attached"){
                                $aGroupQuestion =  surveypanel_models_iquestforms_iquestFormDatamapper::loadGroupQuestions($this->oDB, $this->aData["clientForm"], $aGroup["questionGroupID"]);
                                $iScore_Peopleattached = 0;
                                foreach($aGroupQuestion AS $iQuestionID)
                                {
                                    $iScoreToCalc = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB, $this->aData["clientForm"], $iQuestionID);
                                    $iScore_Peopleattached = $iScore_Peopleattached + $iScoreToCalc;
                                    
                                }
                               
                            }elseif($aGroup["groupName"] == "Matter attached"){
                                $aGroupQuestion =  surveypanel_models_iquestforms_iquestFormDatamapper::loadGroupQuestions($this->oDB, $this->aData["clientForm"], $aGroup["questionGroupID"]);
                                $iScore_Matterattached = 0;
                                foreach($aGroupQuestion AS $iQuestionID)
                                {
                                    $iScoreToCalc = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB, $this->aData["clientForm"], $iQuestionID);
                                    $iScore_Matterattached = $iScore_Matterattached + $iScoreToCalc;
                                    
                                }
                                
                               
                            }elseif($aGroup["groupName"] == "Maturity"){
                                $aGroupQuestion =  surveypanel_models_iquestforms_iquestFormDatamapper::loadGroupQuestions($this->oDB, $this->aData["clientForm"], $aGroup["questionGroupID"]);
                                $iScore_Maturity = 0;
                                foreach($aGroupQuestion AS $iQuestionID)
                                {
                                    $iScoreToCalc = surveypanel_models_iquestforms_iquestFormDatamapper::calculateScoreForMedium($this->oDB, $this->aData["clientForm"], $iQuestionID);
                                    $iScore_Maturity = $iScore_Maturity + $iScoreToCalc;
                                    
                                }
                                
                           }
                            
                        }
                        
                        //Vars Corespending with the excelsheet communicatiestijlen
                        //First we inistaiate everthing 
                        
                        //Feedback / Matter column Feedback
                        $iFeedbackMatter_Feedback = 0;
                        $iFeedbackMatter_Feedback = $iFeedbackMatter_Feedback - $iScore_Feedbackgericht;
                        //Feedback / Matter column Matter
                        $iFeedbackMatter_Matter = 0;
                        $iFeedbackMatter_Matter = $iScore_Matterattached;
                        
                        //Feedback / Matter column Oppervlak
                        $iFeedbackMatter_Oppervlak = 0;
                        $iFeedbackMatter_Oppervlak = $iScore_Feedbackgericht * $iScore_Matterattached;
                        //#########################################################
                        //Feedforward / Matter column Feedback
                        $iFeedforwardMatter_Feedback = 0;
                        $iFeedforwardMatter_Feedback = $iFeedforwardMatter_Feedback + $iScore_Feedforwardgericht;
                        
                        //Feedforward / Matter column Matter
                        $iFeedforwardMatter_Matter = 0;
                        $iFeedforwardMatter_Matter = $iScore_Matterattached;
                       
                        //Feedforward / Matter column Oppervlak
                        $iFeedforwardMatter_Oppervlak = 0;
                        $iFeedforwardMatter_Oppervlak = $iScore_Feedforwardgericht * $iScore_Matterattached;
                        //##########################################################
                        
                        //Feedforward / People column Feedback
                        $iFeedforwardPeople_Feedback = 0;
                        $iFeedforwardPeople_Feedback = $iFeedforwardPeople_Feedback + $iScore_Feedforwardgericht;
                        //Feedforward / People column Matter
                        $iFeedforwardPeople_Matter = 0;
                        $iFeedforwardPeople_Matter = $iFeedforwardPeople_Matter - $iScore_Peopleattached;
                        
                        //Feedforward / People column Oppervlak
                        $iFeedforwardPeople_Oppervlak = 0;
                        $iFeedforwardPeople_Oppervlak  = $iScore_Feedforwardgericht * $iScore_Peopleattached;
                        
                        //#########################################################
                        //Feedback / People column Feedback
                        $iFeedbackPeople_Feedback = 0;
                        $iFeedbackPeople_Feedback = $iScore_Feedbackgericht;
                       
                        //Feedback / People column Matter
                        $iFeedbackPeople_Matter = 0;
                        $iFeedbackPeople_Matter = $iFeedbackPeople_Matter - $iScore_Peopleattached;
                        //Feedback / People column Oppervlak
                        $iFeedbackPeople_Oppervlak = 0;
                        $iFeedbackPeople_Oppervlak = $iScore_Feedbackgericht * $iScore_Peopleattached;
                        //#####################################################
                        //Score Feedbackgericht
                        $iFeedbackgericht_Score = 0;
                        $iFeedbackgericht_Score = $iFeedbackMatter_Oppervlak + $iFeedbackPeople_Oppervlak;
                        //Score Feedforwardgericht
                        $iFeedforwardgericht_Score = 0;
                        $iFeedforwardgericht_Score = $iFeedforwardMatter_Oppervlak + $iFeedforwardPeople_Oppervlak;
                        //Score People attached
                        $iPeopleAttached_Score = 0;
                        $iPeopleAttached_Score = $iFeedforwardPeople_Oppervlak + $iFeedbackPeople_Oppervlak;
                        //Score Matter attached
                        $iMatterAttached_Score = 0;
                        $iMatterAttached_Score = $iFeedbackMatter_Oppervlak + $iFeedforwardMatter_Oppervlak;
                        
                        
                        
                        $sTable = '';
                        $sTable .= '<table class="score">';
                            $sTable .= '<tr>';
                                $sTable .= '<th>&nbsp;</th><th>Score</th><th>Som</th>';
                            $sTable .= '</tr>';
                            
                                $sTable .= '<tr><td class="Scorelabel">Score Feedbackgericht : </td><td class="center">'.$iScore_Feedbackgericht.'</td><td class="center">'.$iFeedbackgericht_Score.'</td></tr>';
                                $sTable .= '<tr><td class="Scorelabel">Score Feedforwardgericht : </td><td class="center">'.$iScore_Feedforwardgericht.'</td><td class="center">'.$iFeedforwardgericht_Score.'</td></tr>';
                                $sTable .= '<tr><td class="Scorelabel">Score People attached : </td><td class="center">'.$iScore_Peopleattached.'</td><td class="center">'.$iPeopleAttached_Score.'</td></tr>';
                                $sTable .= '<tr><td class="Scorelabel">Score Matter attached : </td><td class="center">'.$iScore_Matterattached.'</td><td class="center">'.$iMatterAttached_Score.'</td></tr>';
                                $sTable .= '<tr><td class="Scorelabel">Score Score Maturity : </td><td class="center">'.$iScore_Maturity.'</td><td class="center"></td></tr>';
                            
                        $sTable .= '</table>';
                        
                        $sTable .= '<br><br><table class="score">';
                        $sTable .= '<tr>';
                        $sTable .= '<th>&nbsp;</th><th>&nbsp;</th><th>Feedback</th><th>Matter</th><th>Oppervlak</th>';
                        $sTable .= '</tr>';
                        
                        $sTable .= '<tr><td class="Scorelabel">Feedback / Matter : </td><td style="padding-left:5px;padding-right:5px;">analisten</td><td class="center">'.$iFeedbackMatter_Feedback.'</td><td class="center">'.$iFeedbackMatter_Matter.'</td><td class="center">'.$iFeedbackMatter_Oppervlak.'</td></tr>';
                        $sTable .= '<tr><td class="Scorelabel">Feedforward / Matter : </td><td style="padding-left:5px;padding-right:5px;">controleerder</td><td class="center">'.$iFeedforwardMatter_Feedback.'</td><td class="center">'.$iFeedforwardMatter_Matter.'</td><td class="center">'.$iFeedforwardMatter_Oppervlak.'</td></tr>';
                        $sTable .= '<tr><td class="Scorelabel">Feedforward / People : </td><td style="padding-left:5px;padding-right:5px;">idee&euml;nmachine</td><td class="center">'.$iFeedforwardPeople_Feedback.'</td><td class="center">'.$iFeedforwardPeople_Matter.'</td><td class="center">'.$iFeedforwardPeople_Oppervlak.'</td></tr>';
                        $sTable .= '<tr><td class="Scorelabel">Feedback / People : </td><td style="padding-left:5px;padding-right:5px;">ondersteuner</td><td class="center">'.$iFeedbackPeople_Feedback.'</td><td class="center">'.$iFeedbackPeople_Matter.'</td><td class="center">'.$iFeedbackPeople_Oppervlak.'</td></tr>';
                        
                        $sTable .= '</table>';
                        $this->aData["formScore"]["score"] = $sTable;
                        
                        
                    }elseif($this->aData["clientForm"]->getFormID() == "20") {
                        //4dkl
                        
                        //Questions sorted by colums / group
                        $aScoreMatrix = array();
                        $aScoreMatrix["Disstress"]= array();
                        $aScoreMatrix["Depression"]= array();
                        $aScoreMatrix["Fear"]= array();
                        $aScoreMatrix["Somatisation"]= array();
                        //Define the scoregroups with related questions
                        $aScoreGroup_Disstress = array(96,98,99,101,104,105,108,110,111,115,116,117,118,120,126,127);
                        $aScoreGroup_Depression = array(107,109,112,113,114,125);
                        $aScoreGroup_Fear = array(97,100,102,103,106,119,121,122,123,124,128,129);
                        $aScoreGroup_Somatisation = array(80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95);
                        
                        //getForm questions
                        //loop questions and put in corrct array offset of scoreMAtrix
                        $aQuestions = $this->aData["clientForm"]->getQuestions();
                        $iQuestionCounter = 1;
                        
                        //Scores
                        $iScore_Disstress = 0;
                        $iScore_Depression = 0;
                        $iScore_Fear = 0;
                        $iScore_Somatisation = 0;
                        
                        //sort the questions & calculate score
                        foreach($aQuestions AS $oQuestion)
                        {
                            if($oQuestion->getQuestionType() == 3 && ($oQuestion->getQuestionID() != 131 && $oQuestion->getQuestionID() != 132))
                           {
                               $iDBScore = $oQuestion->getScore();
                               
                               if($iDBScore == 22)
                               {
                                   $iCalculateScore = 2;
                                   $bFlag = true;
                               }else {
                                   $iCalculateScore = $iDBScore;
                                   $bFlag = false;
                               }
                               
                               $aScore = array("questionNr"=>$iQuestionCounter,"score"=>$iCalculateScore,"flag"=>$bFlag);
                               
                              
                               if(in_array($oQuestion->getQuestionID(),$aScoreGroup_Disstress)) {
                                   $aScoreMatrix["Disstress"][]= $aScore;
                                   $iScore_Disstress = $iScore_Disstress + $iCalculateScore;
                               }elseif(in_array($oQuestion->getQuestionID(),$aScoreGroup_Depression)) {
                                   $aScoreMatrix["Depression"][]= $aScore;
                                   $iScore_Depression = $iScore_Depression + $iCalculateScore;
                               }elseif(in_array($oQuestion->getQuestionID(),$aScoreGroup_Fear)) {
                                   $aScoreMatrix["Fear"][]= $aScore;
                                   $iScore_Fear = $iScore_Fear + $iCalculateScore;
                               }elseif(in_array($oQuestion->getQuestionID(),$aScoreGroup_Somatisation)) {
                                   $aScoreMatrix["Somatisation"][]= $aScore;
                                   $iScore_Somatisation = $iScore_Somatisation + $iCalculateScore;
                               }
                           }
                           
                           $iQuestionCounter++;
                        }
                        
                        //loop scorematrix build the table
                        $sTable = '';
                        $sTable .= '<table class="score">';
                        $sTable .= '<tr>';
                            $sTable .= '<td class="scoreGroup">';
                            $sTable .= $this->cretaeScore4dkl($aScoreMatrix["Disstress"]);
                            $sTable .= '</td>';
                            $sTable .= '<td class="scoreGroup">';
                            $sTable .= $this->cretaeScore4dkl($aScoreMatrix["Depression"]);
                            $sTable .= '</td>';
                            $sTable .= '<td class="scoreGroup">';
                            $sTable .= $this->cretaeScore4dkl($aScoreMatrix["Fear"]);
                            $sTable .= '</td>';
                            $sTable .= '<td class="scoreGroup">';
                            $sTable .= $this->cretaeScore4dkl($aScoreMatrix["Somatisation"]);
                            $sTable .= '</td>';
                        $sTable .= '<tr>';
                        $sTable .= '<tr>';
                        $sTable .= '<th>Distress</th><th>Depressie</th><th>Angst</th><th>Somatisatie</th>';
                        $sTable .= '</tr>';
                        $sTable .= '<tr>';
                        $sTable .= '<td class="center">'.$iScore_Disstress.'</td>';
                        $sTable .= '<td class="center">'.$iScore_Depression.'</td>';
                        $sTable .= '<td class="center">'.$iScore_Fear.'</td>';
                        $sTable .= '<td class="center">'.$iScore_Somatisation.'</td>';
                        $sTable .= '</tr>';
                        $sTable .= '</table>';
                        
                        
                        $this->aData["formScore"]["score"] = $sTable;
                    }
                }
                
                
                
            }else {
                $this->aData["sFormMessage"] = "Client heeft formulier nog niet ingevuldt";
                $this->aData["clientForm"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadFullFormByClientformID($this->oDB, $this->oRequest->getParameter(3));
                
            }
            
        } else {
            $this->aData["sFormMessage"] = "Client bestaat niet";
        }
        
        
    }
    
    private function cretaeScore4dkl($p_aMatrix)
    {
        $sHtml = "";
        $sHtml .= '<table class="questionScore">';
        $sHtml .= '<tr><th>Vraag</th><th>Score</th></tr>';
        foreach($p_aMatrix AS $aScore)
        {
            $sClass = ($aScore["flag"] === true?"flag":"noflag");
          
            $sHtml .= '<tr>';
            $sHtml .= '<td class="questionNumber '.$sClass.'">'.$aScore["questionNr"].'</td><td class="'.$sClass.'">'.$aScore["score"].'</td>';
            $sHtml .= '</tr>';
        }
        $sHtml .= '</table>';
        
        return $sHtml;
    }
    
    public function addClient()
    {
        $this->prepareForm();
        
        if(BChelpers_formHandler::formSend("doAddClient"))
        {
            
            if(!isset($_POST["gender"]))
            {
                $_POST["gender"] = "unknown";
            }
            
            BChelpers_formHandler::addFieldCheckNew("gender","Geslacht",true,"surveypanel_checkLibary::checkGender","Geef een geldig geslacht op");
            BChelpers_formHandler::addFieldCheckNew("f_firstName","Voornaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige voornaam op");
            BChelpers_formHandler::addFieldCheckNew("f_lastName","Achternaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige achternaam op");
            BChelpers_formHandler::addFieldCheckNew("f_email","Email",true,"surveypanel_checkLibary::checkEmail","Geef een geldig emailadres op");
            BChelpers_formHandler::addFieldCheckNew("f_dateOfBirth","Geboortedatum",true,"surveypanel_checkLibary::checkDate","Geef een geldige datum, datum kan niet gelijk zijn aan de datum van vandaag");
            BChelpers_formHandler::addFieldCheckNew("f_appointment","Volgende afpraak",true,"surveypanel_checkLibary::checkMandatory");
            BChelpers_formHandler::addFieldCheckNew("companyIDSelected","Bedrijf",true,"surveypanel_checkLibary::checkCompanyID","Kies een bedrijf");
            
            
            $this->aData["clientFormFields"]=  BChelpers_formHandler::convertFormValues($this->aData["clientFormFields"]);
            if(!BChelpers_formHandler::handleForm())
            {
                $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
            } else {
                
                //Split the data
                
                //ClientData
                $aClientData = $this->aData["clientFormFields"];
                unset($aClientData["forms"]);
                $aClientData = $this->oCrypt->multiEncrypt($this->aData["clientFormFields"],"",array("f_appointment","companyIDSelected"));
                //Client Forms
                $aClientForms = $this->aData["clientFormFields"]["forms"];
                
                $iClientID = $this->saveNewClient($aClientData);
                if($iClientID > 0)
                {
                    
                    $this->attachClientForms($iClientID,$aClientForms);
                    $this->aData["sFormSuccesMessage"] = "Client is opgeslagen";
                    $this->aData["submit_change_dis"] = "";
                    $this->aData["submit_add_dis"] = "disabled";
                    $this->aData["newClientID"] = $iClientID;
                }
            }
            
        }
    }
    
    public function addClient_old()
    {
        
        BChelpers_formHandler::addFieldCheckNew("gender","Geslacht",true,"surveypanel_checkLibary::checkGender","Geef een geldig geslacht op");
        BChelpers_formHandler::addFieldCheckNew("f_firstName","Voornaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige voornaam op");
        BChelpers_formHandler::addFieldCheckNew("f_lastName","Achternaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige achternaam op");
        BChelpers_formHandler::addFieldCheckNew("f_email","Email",true,"surveypanel_checkLibary::checkEmail","Geef een geldig emailadres op");
        BChelpers_formHandler::addFieldCheckNew("f_dateOfBirth","Geboortedatum",true,"surveypanel_checkLibary::checkDate","Geef een geldige datum op dd-mm-yyyy");
        BChelpers_formHandler::addFieldCheckNew("f_appointment","Volgende afpraak",true,"surveypanel_checkLibary::checkDate","Geef een geldige datum op dd-mm-yyyy");
        
        $this->prepareForm();
        print_r($_POST);
        if(BChelpers_formHandler::formSend("doAddClient"))
        {
            $this->aData["clientFormFields"]=  BChelpers_formHandler::convertFormValues($this->aData["clientFormFields"]);
            if(!BChelpers_formHandler::handleForm())
            {
                $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
            }else 
                {
                    
                    //Check the existence of the email 
                    if($this->checkClientByEmail($this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_email"))))
                    {
                        $this->aData["sFormMessage"] = "Er bestaat al een client met het emailadres ".BChelpers_formHandler::getValue("f_email");
                    }else
                        {
                            
                            //Some fields are not controled wel by the formHandler, so we do a extra check
                          /**  if(!isset($_POST["gender"]))
                            {
                                $this->aData["sFormMessage"] = "Niet alle verplichte velden zijn ingevuld.<br />Geef een geldig geslacht op";
                            }elseif($_POST["companyIDSelected"] == "0")
                                {
                                    $this->aData["sFormMessage"] = "Niet alle verplichte velden zijn ingevuld.<br />Selecteer een bedrijf";
                                }elseif($_POST["f_dateOfBirth"] == date("d/m/Y"))
                                    {
                                        $this->aData["sFormMessage"] = "Niet alle verplichte velden zijn ingevuld.<br />Geef een geboorte datum op";
                               // }else {**/
                            
                            
                                    //Split the data 
                                    
                                    //ClientData
                                    $aClientData = $this->aData["clientFormFields"];
                                    unset($aClientData["forms"]);
                                    $aClientData = $this->oCrypt->multiEncrypt($this->aData["clientFormFields"],"",array("f_appointment","companyIDSelected"));
                                    //Client Forms
                                    $aClientForms = $this->aData["clientFormFields"]["forms"];
                                    
                                    $iClientID = $this->saveNewClient($aClientData);
                                    if($iClientID > 0)
                                    {
                                        
                                       $this->attachClientForms($iClientID,$aClientForms);
                                       $this->aData["sFormSuccesMessage"] = "Client is opgeslagen";
                                       $this->aData["submit_change_dis"] = "";
                                       $this->aData["submit_add_dis"] = "disabled";
                                       $this->aData["ClientID"] = $iClientID;
                                    }
                               // }
                        }
                }
        }else 
            {
                $this->aData["clientFormFields"]=  BChelpers_formHandler::convertFormValues($this->aData["clientFormFields"]);
            }
        
    }
    
    public function changeClient()
    {
        BChelpers_formHandler::addFieldCheckNew("gender","Geslacht",true,"surveypanel_checkLibary::checkGender","Geef een geldig geslacht op");
        BChelpers_formHandler::addFieldCheckNew("f_firstName","Voornaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige voornaam op");
        BChelpers_formHandler::addFieldCheckNew("f_lastName","Achternaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige achternaam op");
        BChelpers_formHandler::addFieldCheckNew("f_email","Email",true,"surveypanel_checkLibary::checkEmail","Geef een geldig emailadres op");
        BChelpers_formHandler::addFieldCheckNew("f_dateOfBirth","Geboortedatum",true,"surveypanel_checkLibary::checkDate","Geef een geldige datum, datum kan niet gelijk zijn aan de datum van vandaag");
        BChelpers_formHandler::addFieldCheckNew("f_appointment","Volgende afpraak",true,"surveypanel_checkLibary::checkMandatory");
        BChelpers_formHandler::addFieldCheckNew("companyIDSelected","Bedrijf",true,"surveypanel_checkLibary::checkCompanyID","Kies een bedrijf");
        
       // $this->prepareForm($this->oRequest->getParameter(2));
        //Check client
        if($this->checkClientByID($this->oRequest->getParameter(2)))
        {
            $this->prepareForm();
            $this->aData["ClientID"] = $this->oRequest->getParameter(2);
            if(BChelpers_formHandler::formSend("doChangeClient"))
            {
                $this->aData["clientFormFields"]=  BChelpers_formHandler::convertFormValues($this->aData["clientFormFields"]);
                if(!BChelpers_formHandler::handleForm())
                {
                    $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                }else {
                    //ClientData
                    $aClientData = $this->aData["clientFormFields"];
                    unset($aClientData["forms"]);
                    $aClientData = $this->oCrypt->multiEncrypt($this->aData["clientFormFields"],"",array("f_appointment","companyIDSelected"));
                    //Client Forms
                    $aClientForms = $this->aData["clientFormFields"]["forms"];
                    
                    $this->saveClient($this->oRequest->getParameter(2),$aClientData);
                    $this->attachClientForms($this->oRequest->getParameter(2),$aClientForms);
                    $this->aData["sFormSuccesMessage"] = "Client is opgeslagen";
                    
                }
            }else 
                {
                    //initial request : prepare form + load ClientData
                    $this->prepareForm($this->oRequest->getParameter(2));
                }
        }else 
            {
                $this->prepareForm();
                $this->aData["sFormMessage"] = "Client bestaat niet";
            }
    }
    
    public function deleteClient()
    {
       
        if($this->checkClientByID($this->oRequest->getParameter(2)))
        {
            if($this->isLockt($this->oRequest->getParameter(2)))
            {
                $this->showClients();
                $this->aData["sFormMessage"] = "Client kan niet verwijderd worden";
            }else 
                {
                    $this->deleteClientForms($this->oRequest->getParameter(2));
                    $this->deleteDBClient($this->oRequest->getParameter(2));
                    $this->showClients();
                    $this->aData["sFormSuccesMessage"] = "Client is verwijderd";
                }
        }else
            {
                $this->showClients();
                $this->aData["sFormMessage"] = "Client bestaat niet";
            }
    }
    
    
    public function sendinvitation()
    {
        
        //Check client
        if($this->checkClientByID($this->oRequest->getParameter(2)))
        {
            //Get ClientData
            $aClientData = $this->oCrypt->multiDecrypt($this->loadClientByID($this->oRequest->getParameter(2)));
            
            //Create Secret keys & Password
            $aloginCredentials = array();
            $aloginCredentials["SecretKey1"] = md5($aClientData["email"].$aClientData["firstName"].$aClientData["lastName"].time());
            $aloginCredentials["SecretKey2"] = md5($aClientData["gender"].$aClientData["dateOfBirth"].login_loginHelper::generateNewPassWord().time());
            $aloginCredentials["Pass"] = login_loginHelper::generateNewPassWord();
            $aloginCredentials["PassWord"] = $this->oCrypt->enCrypt($aloginCredentials["Pass"]);
            $aloginCredentials["userName"] = $this->oCrypt->enCrypt($aClientData["email"]);
            $aloginCredentials["ScreenName"] = $this->oCrypt->enCrypt($aClientData["firstName"]." ".$aClientData["lastName"]);
            $aloginCredentials["URL"] = HOST."/".APPLICATIONPATH."iquestclient/".$aloginCredentials["SecretKey1"]."/".$aloginCredentials["SecretKey2"];
           // echo "<pre>";
            //print_r($aloginCredentials);
           // echo "</pre>";
            if(!$this->checkClientUserRecord($aloginCredentials["userName"]))
            {
                if($this->createClientUserRecord($aloginCredentials) > 0)
                {
                    $this->saveInvitationDate($this->oRequest->getParameter(2));
                    
                    $aMailVars["client"] = $aClientData;
                    $aMailVars["credentials"] = $aloginCredentials;
                    
                    $oMail = new BCMail_BCMail();
                    if($oMail->sendMail("surveypanel","newInvitation",$aMailVars))
                    {
                        $this->sStatus = "Ready";
                    }else {
                        $this->sStatus = "Failed";
                    }
                    
                    $this->showClients();
                    $this->aData["sFormSuccesMessage"] = "Uitnodiging is succesvol verzonden";
                }
            }else 
                {
                    $this->showClients();
                    $this->aData["sFormMessage"] = "Er is al een user aangemaakt voor deze client. Gebruik uitnodiging opnieuw verzenden om de client alsnog een nieuwe uitnodiging te sturen";
                }
            
        }else
            {
                $this->showClients();
                $this->aData["sFormMessage"] = "Client bestaat niet kan uitnodiging niet versturen";
            }
    }
    
    public function resendInvitation()
    {
        //Check client
        if($this->checkClientByID($this->oRequest->getParameter(2)))
        {
            //Get ClientData
            $aClientData = $this->oCrypt->multiDecrypt($this->loadClientByID($this->oRequest->getParameter(2)));
            $aloginCredentials = array();
            $aloginCredentials["userName"] = $this->oCrypt->enCrypt($aClientData["email"]);
            
            
           
            if($this->checkClientUserRecord($aloginCredentials["userName"]))
            {
                $aUserRecord = $this->loadUserRecord($aloginCredentials["userName"]);
                $aloginCredentials["SecretKey1"] = $aUserRecord["secretKey1"];
                $aloginCredentials["SecretKey2"] = $aUserRecord["secretKey2"];
                $aloginCredentials["Pass"] = rtrim($this->oCrypt->deCrypt($aUserRecord["userPass"]));
                $aloginCredentials["URL"] = HOST."/".APPLICATIONPATH."iquestclient/".$aloginCredentials["SecretKey1"]."/".$aloginCredentials["SecretKey2"];
                
                $this->saveInvitationDate($this->oRequest->getParameter(2));
                    
                $aMailVars["client"] = $aClientData;
                $aMailVars["credentials"] = $aloginCredentials;
                    
                $oMail = new BCMail_BCMail();
                 if($oMail->sendMail("surveypanel","newInvitation",$aMailVars))
                    {
                        $this->sStatus = "Ready";
                    }else {
                        $this->sStatus = "Failed";
                    }
                    
                    $this->showClients();
                    $this->aData["sFormSuccesMessage"] = "Uitnodiging is succesvol verzonden";
               
            }else
                {
                    $this->showClients();
                    $this->aData["sFormMessage"] = "User (".$aloginCredentials["userName"].")record van de client niet gevonden,kan uitnodiging niet versturen";
                }
            
        }else
        {
            $this->showClients();
            $this->aData["sFormMessage"] = "Client bestaat niet kan uitnodiging niet versturen";
        }
    }
    
    private function prepareForm($p_iClientID = 0)
    {
        
        //Load Company & FormData 
        $this->aData["iquestForms"] = $this->oFormControler->loadActiveIquestForms();
        $this->oCompanyControler->loadActiveCompanys();
        $this->aData["companys"] = $this->oCompanyControler->getData("companys");
        $this->aData["companysQuestions"] = $this->oCompanyControler->loadCompanyForms();
        $this->aData["sFormSuccesMessage"] = "";
        $this->aData["sFormMessage"] = "";
        $this->aData["submit_change_dis"] = "disabled";
        $this->aData["submit_add_dis"] = "";
        
        $aForms = array_merge($this->aData["iquestForms"],$this->aData["companysQuestions"]);
        
        $aClientData = array();
        $aClientForms = array();
        $aClientFormIDS = array();
        //Load ClientData if clientID not is 0 -> this means that a client exists, otherwise nothing saved yet
        if($p_iClientID !=0)
        {
            
            $aClientData = $this->oCrypt->multiDecrypt($this->loadClientByID($p_iClientID));
            $aClientForms = $this->loadClientForms($p_iClientID);
            
            
            foreach($aClientForms AS $aClientForm)
            {
                array_push($aClientFormIDS, $aClientForm["formID"]);
            }
            
        }
        
        $aFormFields = array();
        $aFormFields["gender"] = (isset($aClientData["gender"])?$aClientData["gender"]:"");
        $aFormFields["f_firstName"] = (isset($aClientData["firstName"])?rtrim($aClientData["firstName"]):"");
        $aFormFields["f_lastName"] = (isset($aClientData["lastName"])?rtrim($aClientData["lastName"]):"");
        $aFormFields["f_dateOfBirth"] = (isset($aClientData["dateOfBirth"])?rtrim($aClientData["dateOfBirth"]):date("d/m/Y"));
        $aFormFields["f_email"] = (isset($aClientData["email"])?rtrim($aClientData["email"]):"");
        if(isset($aClientData["appointment"]))
        {  
            if($aClientData["appointment"] == "0000-00-00")
            {
                $aFormFields["f_appointment"] = date("d/m/Y");
            }else {
                $aFormFields["f_appointment"] =BChelpers_converters::convertDate("view",$aClientData["appointment"]);
            }
        }else {
            $aFormFields["f_appointment"] = date("d/m/Y");
        }
        
        //$aFormFields["f_appointment"] = (isset($aClientData["appointment"])?BChelpers_converters::convertDate("view",$aClientData["appointment"]):date("d/m/Y"));
        $aFormFields["companyIDSelected"] = (isset($aClientData["companyID"])?$aClientData["companyID"]:"");
        
        $aFormFields["forms"] = array();
        foreach($aForms AS $aForm)
        { 
            if(isset($aForm["formID"]))
            {
                if(in_array($aForm["formID"], $aClientFormIDS))
                {   
                    $aFormFields["forms"]["form_".$aForm["formID"]] = "on";
                }else
                    {
                        $aFormFields["forms"]["form_".$aForm["formID"]] = "off";
                    }
            }elseif(isset($aForm["companyID"]))
                {
                   if(!empty($aForm["forms"]))
                   {
                       foreach($aForm["forms"] as $aCompanyForm)
                       {
                           
                           if(in_array($aCompanyForm["formID"], $aClientFormIDS))
                           {
                               $aFormFields["forms"]["form_".$aCompanyForm["formID"]] = "on";
                           }else
                            {
                                $aFormFields["forms"]["form_".$aCompanyForm["formID"]] = "off";
                            }
                           
                           
                           
                       }
                   }
                }
                
        }
        
       $this->aData["clientFormFields"] = $aFormFields;
    
    }
    
    
    private function loadClients()
    {
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID;

";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    private function loadClientByID($p_iClientID)
    {
        $sSQL = "SELECT * FROM clients WHERE clientID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iClientID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    private function checkClientByID($p_iClientID)
    {
        $sSQL = "SELECT count(clientID) AS iClient FROM clients WHERE clientID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iClientID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iClient"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function checkClientByEmail($p_sEmail)
    {
       
        $sSQL = "SELECT count(clientID) AS iClient FROM clients WHERE email=:email";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":email", $p_sEmail);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iClient"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function isLockt($p_iClientID)
    {
        $sSQL = "SELECT COUNT(*)AS lockt FROM clientforms WHERE clientID =:ID AND (status ='closed' OR status ='pending') ";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iClientID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["lockt"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function saveNewClient($p_aClientData)
    {
        if($p_aClientData["f_appointment"] == date("d-m-Y"))
        {
            $p_aClientData["f_appointment"] = "";
        }
        
        $sSQL = "INSERT INTO clients (companyID,gender,firstName,lastName,email,dateOfBirth,appointment) 
                VALUES (:companyID,:gender,:first,:last,:mail,:birth,:appointment)";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":companyID", $p_aClientData["companyIDSelected"]);
        $oQuery->bindParam(":gender", $p_aClientData["gender"]);
        $oQuery->bindParam(":first", $p_aClientData["f_firstName"]);
        $oQuery->bindParam(":last", $p_aClientData["f_lastName"]);
        $oQuery->bindParam(":mail", $p_aClientData["f_email"]);
        $oQuery->bindParam(":birth", $p_aClientData["f_dateOfBirth"]);
        $sAppointment = ($p_aClientData["f_appointment"] !=""?BChelpers_converters::convertDate("SQL",$p_aClientData["f_appointment"]):"");
        $oQuery->bindParam(":appointment", $sAppointment);
        $oQuery->execute();
        
        return $this->oDB->lastInsertID();
    }
    
    private function saveClient($p_iClientID,$p_aClientData)
    {
        $sSQL = "UPDATE clients SET companyID=:companyID,gender=:gender,firstName=:first,lastName=:last,email=:mail,dateOfBirth=:birth,appointment=:appointment
                 WHERE clientID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iClientID);
        $oQuery->bindParam(":companyID", $p_aClientData["companyIDSelected"]);
        $oQuery->bindParam(":gender", $p_aClientData["gender"]);
        $oQuery->bindParam(":first", $p_aClientData["f_firstName"]);
        $oQuery->bindParam(":last", $p_aClientData["f_lastName"]);
        $oQuery->bindParam(":mail", $p_aClientData["f_email"]);
        $oQuery->bindParam(":birth", $p_aClientData["f_dateOfBirth"]);
        $sAppointment = BChelpers_converters::convertDate("SQL",$p_aClientData["f_appointment"]);
        $oQuery->bindParam(":appointment", $sAppointment);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
    
    private function attachClientForms($p_iClientID,$p_aForms)
    {
       
        if(!empty($p_aForms))
        {
            foreach($p_aForms as $iFormID => $sStatus)
            {
                $aIDParts = explode("_",$iFormID);
                $iRealFormID = $aIDParts[1];
                
                if($sStatus == "on")
                {
                    //Check if there is all ready a form with this ID 
                    //If not attach it 
                    if(!$this->checkClientForm($p_iClientID, $iRealFormID))
                    {
                        $this->saveClientForm($p_iClientID, $iRealFormID);
                       
                    }
                }elseif($sStatus == "off")
                    {
                        //check if there is a open form with this id,
                        //if so delete it
                        if($this->checkClientForm($p_iClientID, $iRealFormID))
                        {
                            //delete
                            $this->deleteOpenClientForm($p_iClientID, $iRealFormID );
                        }
                    }
            }
        }
        
        
    }
    
    private function checkClientForm($p_iclientID,$p_iFormID,$p_sStatus = "")
    {
        $sStatus = ($p_sStatus !=""?"AND status='".$p_sStatus."'":"");
        $sSQL = "SELECT count(clientFormID) AS iClientForm FROM clientforms WHERE clientID=:ID AND formID=:formID $sStatus";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iclientID);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      
        if($aResult["iClientForm"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function checkClientFormResult($p_iclientID,$p_iFormID,$p_sStatus = "")
    {
        $sStatus = ($p_sStatus !=""?"AND status='".$p_sStatus."'":"");
        $sSQL = "SELECT count(clientFormID) AS iClientForm FROM clientforms WHERE clientID=:ID AND clientFormID=:formID $sStatus";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iclientID);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["iClientForm"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function saveClientForm($p_iClientID, $p_iFormID)
    {
        
        $sSQL = "INSERT INTO clientforms (clientID,formID) VALUES (:client,:formID)";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        return $this->oDB->lastInsertID();
    }
    
    private function loadClientForms($p_iClientID)
    {
        $sSQL = "SELECT * FROM clientforms WHERE clientID=:client ";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    private function loadClientFullForms($p_iClientID)
    {
        $sSQL = "SELECT *,(SELECT formName FROM forms WHERE formID=clientforms.formID) AS formName FROM clientforms 
                 WHERE clientID=:client ";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    private function deleteOpenClientForm($p_iClientID,$p_iFormID)
    {
        $sSQL = "DELETE FROM clientforms WHERE clientID=:client AND formID=:formID AND status='open'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->bindParam(":formID", $p_iFormID);
        $oQuery->execute();
        
        
    }
    
    public function deleteDBClient($p_iClientID)
    {
        $sSQL = "DELETE FROM clients WHERE clientID=:client";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->execute();
    }
    
    public function deleteClientForms($p_iClientID)
    {
        $sSQL = "DELETE FROM clientforms WHERE clientID=:client";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->execute();
    }
    
    private function checkClientUserRecord($p_sUserName)
    {
        $sSQL = "SELECT count(userID) AS iClient FROM users WHERE userName=:name AND userLevel='Client'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":name", $p_sUserName);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iClient"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    private function createClientUserRecord($p_aloginCredentials)
    {
        $sSQL = "INSERT INTO users (userScreenname,userName,userPass,userLevel,secretKey1,secretKey2)
                 Values (:screenName,:userName,:pass,:level,:key1,:key2)";
        
        $sLevel = "Client";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":screenName", $p_aloginCredentials["ScreenName"]);
        $oQuery->bindParam(":userName", $p_aloginCredentials["userName"]);
        $oQuery->bindParam(":pass", $p_aloginCredentials["PassWord"]);
        $oQuery->bindParam(":level", $sLevel);
        $oQuery->bindParam(":key1", $p_aloginCredentials["SecretKey1"]);
        $oQuery->bindParam(":key2", $p_aloginCredentials["SecretKey2"]);
        
        $oQuery->execute();
        
        return $this->oDB->lastInsertID();
    }
    
    private function loadUserRecord($p_sMail)
    {
        $sSQL = "SELECT * FROM users WHERE userName=:name AND userLevel='Client'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":name", $p_sMail);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    private function saveInvitationDate($p_iClientID)
    {
        $sSQL = "UPDATE clients SET invitation=NOW() WHERE clientID=:client";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":client", $p_iClientID);
        $oQuery->execute();
    }
}

