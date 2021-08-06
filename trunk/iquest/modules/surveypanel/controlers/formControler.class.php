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

class surveypanel_formControler extends surveypanel_dashboardControler
{
    private $oCompanyControler;
    
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        $this->loadUserData($_SESSION["userID"]);
        $this->oCompanyControler = new surveypanel_companyControler($p_oRequest, $p_oDB, $p_oCrypt);
    }
    
    public function showForms()
    {
        
        $aForms =  surveypanel_models_iquestforms_iquestFormDatamapper::loadForms($this->oCrypt, $this->oDB);
       if(!empty($aForms))
       {
           $this->aData["forms"] = $aForms;
       }else
            {
                $this->aData["forms"] = "Er zijn nog geen formulieren toegevoegd";
            }
    }
    
    
    public function loadIquestForms()
    {
        $sSQL = "SELECT * FROM forms WHERE companyID='0'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public function loadActiveIquestForms()
    {
        $sSQL = "SELECT * FROM forms WHERE companyID='0' AND active='1'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public function addForm()
    {
        //Prepare the form 
        $this->aData["submit_add_dis"] = "";
        $this->aData["submit_change_dis"] = "disabled";
        $this->aData["submit_step2_dis"] = "disabled";
        $this->aData["destination"] = "addform";
        
        if(!isset($_POST["complex"]))
        {
            $_POST["complex"] = "unknown";
        }
        
        //Default formValues
        $aFormFields = array("companyIDSelected"=>0);
        $this->aData["formFields"] = $aFormFields;
        $this->aData["formFields"]["f_listName"] = "";
        $this->aData["formFields"]["f_decription"] = "";
        $this->aData["formFields"]["f_inter"] = "";
        $this->aData["formFields"]["complex"] = "simple";
        
       
        //Load the companys
        $this->oCompanyControler->loadActiveCompanys();
        $this->aData["companys"] = $this->oCompanyControler->getData("companys");
        
        if(BChelpers_formHandler::formSend("doAddForm"))
        {
            $this->aData["formFields"]=  BChelpers_formHandler::convertFormValues($this->aData["formFields"]);
            BChelpers_formHandler::addFieldCheckNew("f_listName","Vragenlijstnaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige naam op ");
            if(!BChelpers_formHandler::handleForm())
            {
                $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
            } else
                  {
                     //Form is validated So save it
                     
                      //Create iQuestForm object
                      $oQuestForm = surveypanel_models_iquestforms_iquestFormFactory::createForm(BChelpers_formHandler::getValue("complex"));
                      surveypanel_models_iquestforms_iquestFormDatamapper::mapFormFields($oQuestForm, $this->aData["formFields"]);
                      if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByName($oQuestForm, $this->oDB))
                      {
                          //Save Form
                          surveypanel_models_iquestforms_iquestFormDatamapper::saveNewForm($oQuestForm, $this->oDB);
                          if($oQuestForm->getFormID() > 0)
                          {
                              $this->aData["submit_add_dis"] = "disabled";
                              $this->aData["submit_change_dis"] = "";
                              $this->aData["submit_step2_dis"] = "";
                              $this->aData["sFormSuccesMessage"] = "Vragenlijst is succesvol opgeslagen";
                              $this->aData["destination"] = "changeform/".$oQuestForm->getFormID();
                              $this->aData["formID"] = $oQuestForm->getFormID();
                          }else
                              {
                                  $this->aData["sFormMessage"] = "Vragenlijst is niet opgeslagen";
                              }
                      }else
                           {
                               $this->aData["sFormMessage"] = "Er bestaat al een vragenlijst met de naam ".BChelpers_formHandler::getValue("complex")." kies een andere";
                           }
                  }
        }else
            {
            
            }
       
        
        $oQuestForm = new surveypanel_models_iquestforms_iquestForm();
    }
    
    public function changeForm()
    {
        
        //Prepare the form
        $this->aData["submit_add_dis"] = "";
        $this->aData["submit_change_dis"] = "";
        $this->aData["submit_step2_dis"] = "";
        
        if(!isset($_POST["complex"]))
        {
            $_POST["complex"] = "unknown";
        }
        
        //Default formValues
        $aFormFields = array("companyIDSelected"=>0);
        $this->aData["formFields"] = $aFormFields;
        $this->aData["formFields"]["f_listName"] = "";
        $this->aData["formFields"]["f_decription"] = "";
        $this->aData["formFields"]["f_inter"] = "";
        $this->aData["formFields"]["complex"] = "simple";
        
        
        //Load the companys
        $this->oCompanyControler->loadActiveCompanys();
        $this->aData["companys"] = $this->oCompanyControler->getData("companys");
        
        $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
        if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
        {
            $this->aData["formFields"]["formID"] = $oQuestForm->getFormID();
            $this->aData["oQuestForm"] = $oQuestForm;
            $this->aData["scores"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormScores($this->oDB, $oQuestForm);
            $this->aData["scoreGroups"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadScoreGroups($this->oDB, $oQuestForm);
            if(BChelpers_formHandler::formSend("dochangeForm"))
            {
                BChelpers_formHandler::addFieldCheckNew("f_listName","Vragenlijstnaam",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige naam op ");
                $this->aData["formFields"]=  BChelpers_formHandler::convertFormValues($this->aData["formFields"]);
                if(!BChelpers_formHandler::handleForm())
                {
                    $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                }else
                    {
                        //save
                        surveypanel_models_iquestforms_iquestFormDatamapper::mapFormFields($oQuestForm, $this->aData["formFields"]);
                        
                        //Checkname
                        if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByName($oQuestForm, $this->oDB,$oQuestForm->getFormID()))
                        {
                            //Save form
                            if(surveypanel_models_iquestforms_iquestFormDatamapper::saveForm($oQuestForm, $this->oDB))
                            {
                                $this->aData["sFormSuccesMessage"] = "Vragenlijst is succesvol opgeslagen";
                            }else
                                {
                                    $this->aData["sFormMessage"] = "Er is niets gewijzigd in de vragenlijst of de vragenlijst is niet correct opgeslagen";
                                }
                        }else 
                            {
                                $this->aData["sFormMessage"] = "Er bestaat al een vragenlijst met de naam ".$oQuestForm->getName();
                            }
                        
                    }
            }else
                {
                    $this->aData["formFields"] =  surveypanel_models_iquestforms_iquestFormDatamapper::mapFormToFieldArray($oQuestForm);
                }
        }else
            {
                $this->aData["sFormMessage"] = "Kon vragenlijst niet laden";
                $this->aData["formFields"]["formID"] = "";
                $this->aData["formFields"]["complex"] = "simple";
            }
     }
     
     
     public function changeFormStatus()
     {
         if( surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             if($this->oRequest->getParameter(3) == 0 || $this->oRequest->getParameter(3) == 1)
             {
                 surveypanel_models_iquestforms_iquestFormDatamapper::changeStatus($this->oDB, $this->oRequest->getParameter(2),$this->oRequest->getParameter(3));
                 $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
             }
         }
     }
     
     public function addQuestion() {
         
         if( surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
             if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
             {
                 $this->aData["oQuestForm"] = $oQuestForm;
                 switch($oQuestForm->getType()) {
                     
                     case "Simple" :
                         $this->addQuestionSimple($oQuestForm);
                     break;
                     case "Medium" :
                         $this->addQuestionMedium($oQuestForm);
                     break;
                     case "Complex" :
                         $this->addQuestionComplex($oQuestForm);
                     break;
                 }
                 
                 
             }else {
                 $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen vraag worden toegevoegd";
             }
             
         }else {
             $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen vraag worden toegevoegd";
         }
     }
     
     
     private function addQuestionSimple($oQuestForm) {
         
         $this->aData["formFields"]["f_question"] = "";
         $this->aData["formFields"]["f_answerTypes"] = "0";
         
         //Create some dummyFields for the option fields for now 20
         for($i =1;$i<=20;$i++)
         {
             $this->aData["formFields"]["optionField_".$i] = "";
             $this->aData["formFields"]["optionScoreField_".$i] = "";
         }
         
         if(BChelpers_formHandler::formSend("addquestion"))
         {
             BChelpers_formHandler::addFieldCheckNew("f_question","Vraag / Stelling",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige vraag op ");
             BChelpers_formHandler::addFieldCheckNew("f_answerTypes","Type antwoord",true,"surveypanel_checkLibary::checkQuestionType","Kies een geldig type antwoord ");
             
             
             $this->aData["formFields"]=  BChelpers_formHandler::convertFormValues($this->aData["formFields"]);
             
             if(!BChelpers_formHandler::handleForm())
             {
                 $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
             }else {
                 
                 surveypanel_models_iquestforms_iquestFormDatamapper::mapFormQuestionFields($oQuestForm, $this->aData["formFields"]);
                 if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestion($this->oDB,$oQuestForm))
                 {
                     surveypanel_models_iquestforms_iquestFormDatamapper::saveNewQuestion($this->oDB,$oQuestForm);
                     
                     $_SESSION["currentTab"] = "Questions";
                     $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                 }else
                 {
                     $this->aData["sFormMessage"] = "de vraag bestaat al";
                 }
             }
         }
     }
     
     private function addQuestionMedium($oQuestForm) {
         
         $this->aData["formFields"]["f_question"] = "";
         $this->aData["formFields"]["f_answerTypes"] = "0";
         $this->aData["formFields"]["f_possibleAnswers"] = "1";
         $this->aData["formFields"]["f_subQuestion"] = "0";
         
         $this->aData["parentQuestions"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadParentQuestions($this->oDB,$oQuestForm);
         //Create some dummyFields for the option fields for now 20
         for($i =1;$i<=20;$i++)
         {
             $this->aData["formFields"]["optionField_".$i] = "";
             $this->aData["formFields"]["optionScoreField_".$i] = "";
         }
         
         if(BChelpers_formHandler::formSend("addquestion"))
         {
             BChelpers_formHandler::addFieldCheckNew("f_question","Vraag / Stelling",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige vraag op ");
             BChelpers_formHandler::addFieldCheckNew("f_answerTypes","Type antwoord",true,"surveypanel_checkLibary::checkQuestionType","Kies een geldig type antwoord ");
             
             $this->aData["formFields"]=  BChelpers_formHandler::convertFormValues($this->aData["formFields"]);
             
             if(!BChelpers_formHandler::handleForm())
             {
                 $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
             }else {
                 
                 surveypanel_models_iquestforms_iquestFormDatamapper::mapFormQuestionFields($oQuestForm, $this->aData["formFields"]);
                 if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestion($this->oDB,$oQuestForm))
                 {
                     surveypanel_models_iquestforms_iquestFormDatamapper::saveNewQuestion($this->oDB,$oQuestForm);
                     
                     $_SESSION["currentTab"] = "Questions";
                     $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                 }else
                 {
                     $this->aData["sFormMessage"] = "de vraag bestaat al";
                 }
                 
             }
         }
     }
     
     private function addQuestionComplex($oQuestForm) {
         
         $this->aData["formFields"]["f_question"] = "";
         $this->aData["formFields"]["f_answerTypes"] = "0";
         $this->aData["formFields"]["f_possibleAnswers"] = "1";
         $this->aData["formFields"]["f_subQuestion"] = "0";
         $this->aData["formFields"]["f_answerOptions"] = "0";
         $this->aData["formFields"]["f_QuestionGroup"] = "0";
         $this->aData["formFields"]["f_questionGroupNew"] = "";
         
         $this->aData["extraFields"] = array();
         $this->aData["extraFields"]["questionGroups"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionGroups($this->oDB,$oQuestForm);
         $this->aData["extraFields"]["options"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadPossibleOptions($this->oDB,$oQuestForm);
         
         
         $this->aData["parentQuestions"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadParentQuestions($this->oDB,$oQuestForm);
         //Create some dummyFields for the option fields for now 20
         for($i =1;$i<=20;$i++)
         {
             $this->aData["formFields"]["optionField_".$i] = "";
             $this->aData["formFields"]["optionScoreField_".$i] = "";
         }
         
         if(BChelpers_formHandler::formSend("addquestion"))
         {
             BChelpers_formHandler::addFieldCheckNew("f_question","Vraag / Stelling",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige vraag op ");
             BChelpers_formHandler::addFieldCheckNew("f_answerTypes","Type antwoord",true,"surveypanel_checkLibary::checkQuestionType","Kies een geldig type antwoord ");
             
             $this->aData["formFields"]=  BChelpers_formHandler::convertFormValues($this->aData["formFields"]);
             
             if(!BChelpers_formHandler::handleForm())
             {
                 $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                 //echo "<pre>";
                 //print_r( $this->aData["formFields"]);
                //echo "</pre>";
             }else {
                 
                 surveypanel_models_iquestforms_iquestFormDatamapper::mapFormQuestionFields($oQuestForm, $this->aData["formFields"]);
                 if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestion($this->oDB,$oQuestForm))
                 {
                     
                     //Check if there is a new QuestionGroup
                     if(BChelpers_formHandler::getValue("f_questionGroupNew") != "")
                     {
                         if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestionGroup($this->oDB,$oQuestForm, BChelpers_formHandler::getValue("f_questionGroupNew")))
                         {
                             $iGroupID = surveypanel_models_iquestforms_iquestFormDatamapper::saveNewQuestionGroup($this->oDB,$oQuestForm, BChelpers_formHandler::getValue("f_questionGroupNew"));
                             surveypanel_models_iquestforms_iquestFormDatamapper::saveNewQuestion($this->oDB,$oQuestForm, $iGroupID);
                             $_SESSION["currentTab"] = "Questions";
                             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                         
                         }else {
                             $this->aData["sFormMessage"] = "De groep ".BChelpers_formHandler::getValue("f_questionGroupNew")." bestaat al";
                         }
                     }else {
                         
                         surveypanel_models_iquestforms_iquestFormDatamapper::saveNewQuestion($this->oDB,$oQuestForm, BChelpers_formHandler::getValue("f_QuestionGroup"));
                         $_SESSION["currentTab"] = "Questions";
                         $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                     }
                     //If this is the case, first save the questionGroup
                     
                     //If not the case get the ID out the formfield f_QuestionGroup
                     
                     //
                 }else
                 {
                     $this->aData["sFormMessage"] = "de vraag bestaat al";
                 }
                 
             }
         }
     }
     
     
     public function addQuestion_old()
     {
         if( surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
             if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
             {
                
                 $this->aData["oQuestForm"] = $oQuestForm;
                 
                 $this->aData["formFields"]["f_question"] = "";
                 $this->aData["formFields"]["f_answerTypes"] = "0";
                 
                 //Create some dummyFields for the option fields for now 20
                 for($i =1;$i<=20;$i++)
                 {
                     $this->aData["formFields"]["optionField_".$i] = "";
                     $this->aData["formFields"]["optionScoreField_".$i] = "";
                 }
                
                 if(BChelpers_formHandler::formSend("addquestion"))
                 {
                     BChelpers_formHandler::addFieldCheckNew("f_question","Vraag / Stelling",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige vraag op ");
                     BChelpers_formHandler::addFieldCheckNew("f_answerTypes","Type antwoord",true,"surveypanel_checkLibary::checkQuestionType","Kies een geldig type antwoord ");
                     
                     
                     $this->aData["formFields"]=  BChelpers_formHandler::convertFormValues($this->aData["formFields"]);
                    
                     if(!BChelpers_formHandler::handleForm())
                     {
                         $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                     }else {
                         
                              surveypanel_models_iquestforms_iquestFormDatamapper::mapFormQuestionFields($oQuestForm, $this->aData["formFields"]);
                              if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestion($this->oDB,$oQuestForm))
                              {
                                    surveypanel_models_iquestforms_iquestFormDatamapper::saveNewQuestion($this->oDB,$oQuestForm);
                                    
                                    $_SESSION["currentTab"] = "Questions";
                                    $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                              }else 
                                    {
                                        $this->aData["sFormMessage"] = "de vraag bestaat al";
                                    }
                     }
                 }//end
             }else
                 {
                     $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen vraag worden toegevoegd";
                 }
         }else 
             {
                 $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen vraag worden toegevoegd";
             }
     }
     
     /**
      * De diverse vraag typen hebben verschillende soorten scores 
      * Dus dat moeten we opsplitsen 
      */
     public function addScore()
     {
         if( surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
             if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
             {
                 
                 $this->aData["oQuestForm"] = $oQuestForm;
                 
                
                 //Handle the scores for the forms with types of Simple
                 if(BChelpers_formHandler::formSend("addscore"))
                 {
                     if(!BChelpers_formHandler::handleForm())
                     {
                         $this->aData["f_scoreDesription"] = BChelpers_formHandler::getValue("scoreDesription");
                         $this->aData["f_scoreLow"] = BChelpers_formHandler::getValue("scoreLow");
                         $this->aData["f_scoreHigh"] = BChelpers_formHandler::getValue("scoreHigh");
                         $this->aData["comparison"] = BChelpers_formHandler::getValue("comparison");
                         
                     }else {
                         
                         $bCheck = surveypanel_models_iquestforms_iquestFormDatamapper::checkScore(
                             $this->oDB,
                             $oQuestForm,
                             0,
                             BChelpers_formHandler::getValue("scoreDesription"),
                             BChelpers_formHandler::getValue("scoreLow"),
                             BChelpers_formHandler::getValue("scoreHigh"),
                             BChelpers_formHandler::getValue("comparison")
                             );
                         
                         if(!$bCheck)
                         {
                                 surveypanel_models_iquestforms_iquestFormDatamapper::saveNewScore(
                                     $this->oDB, 
                                     $oQuestForm, 
                                     0, 
                                     BChelpers_formHandler::getValue("scoreDesription"), 
                                     BChelpers_formHandler::getValue("scoreLow"), 
                                     BChelpers_formHandler::getValue("scoreHigh"), 
                                     BChelpers_formHandler::getValue("comparison"));
                                 
                                
                                 $_SESSION["currentTab"] = "Scores";
                                 $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                         
                         }else 
                         {
                             
                             $this->aData["sFormMessage"] = "Score record is al toegevoegd";
                             $this->aData["f_scoreDesription"] = BChelpers_formHandler::getValue("scoreDesription");
                             $this->aData["f_scoreLow"] = BChelpers_formHandler::getValue("scoreLow");
                             $this->aData["f_scoreHigh"] = BChelpers_formHandler::getValue("scoreHigh");
                             $this->aData["comparison"] = BChelpers_formHandler::getValue("comparison");
                         }
                     }
                     
               } else {
                     
                   $this->aData["formFields"]["f_scoreDesription"] = "";
                   $this->aData["formFields"]["f_scoreLow"] = "";
                   $this->aData["formFields"]["f_scoreHigh"] = "";
                   $this->aData["comparison"] = "0";
                 }
                 
                 
                 //Action finish redirect back to changeform
                 //Using session for current tab
                // $_SESSION["currentTab"] = "Scores";
                // $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
         }else
             {
                 $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen score worden toegevoegd";
             }
         }else
         {
             $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen score worden toegevoegd";
         }
     }
     
     /**
      * @todo : form controle
      */
     public function addScoreGroup()
     {
         if( surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $_SESSION["currentTab"] = "Scores";
             $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
             if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
             {
                 $this->aData["oQuestForm"] = $oQuestForm;
                 
                 if(BChelpers_formHandler::formSend("addscoregroup"))
                 {
                     BChelpers_formHandler::addFieldCheckNew("scoreGroup","Groepnaam",true,"surveypanel_checkLibary::checkPlainText","Geef een geldige groepnaam op ");
                     BChelpers_formHandler::addFieldCheckNew("groupStartRange","Start van de vraag range",true,"surveypanel_checkLibary::checkScore","Geef een geldig getal op ");
                     BChelpers_formHandler::addFieldCheckNew("groupEndRange","Eind van de vraag range",true,"surveypanel_checkLibary::checkScore","Geef een geldig getal op");
                     
                     if(!BChelpers_formHandler::handleForm())
                     {
                         $this->aData["f_scoreGroup"] = BChelpers_formHandler::getValue("scoreGroup");
                         $this->aData["f_groupStartRange"] = BChelpers_formHandler::getValue("groupStartRange");
                         $this->aData["f_groupEndRange"] = BChelpers_formHandler::getValue("groupEndRange");
                         $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                     }else {
                         
                         
                         $bCheck = surveypanel_models_iquestforms_iquestFormDatamapper::checkScoreGroup(
                             $this->oDB,
                             $oQuestForm,
                             BChelpers_formHandler::getValue("scoreGroup")
                            );
                         
                         if(!$bCheck)
                         {
                             surveypanel_models_iquestforms_iquestFormDatamapper::saveNewScoreGroup(
                                 $this->oDB,
                                 $oQuestForm,
                                 BChelpers_formHandler::getValue("scoreGroup"), 
                                 BChelpers_formHandler::getValue("groupStartRange"), 
                                 BChelpers_formHandler::getValue("groupEndRange")
                                 );
                             
                             $_SESSION["currentTab"] = "Scores";
                             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/changeform/".$oQuestForm->getFormID());
                             
                         }else {
                         
                         $this->aData["sFormMessage"] =  "Er bestaat al een groep met de naam : ".BChelpers_formHandler::getValue("scoreGroup");
                         $this->aData["f_scoreGroup"] = BChelpers_formHandler::getValue("scoreGroup");
                         $this->aData["f_groupStartRange"] = BChelpers_formHandler::getValue("groupStartRange");
                         $this->aData["f_groupEndRange"] = BChelpers_formHandler::getValue("groupEndRange");
                         }
                         
                     }
                 }else {
                     
                     $this->aData["f_scoreGroup"] = "";
                     $this->aData["f_groupStartRange"] = "";
                     $this->aData["f_groupEndRange"] = "";
                 }
                 
             }else {
                 
                 $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen scoregroep worden toegevoegd";
             }
         }else {
             
             $this->aData["sFormMessage"] = "Kon vragenlijst niet laden. Er kan geen scoregroep worden toegevoegd";
         }
     }
     
    
     
     
     public function deleteForm() {
         
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
             
             if($oQuestForm->getLockt())
             {
                 $this->aData["sFormMessage"] = "Het formulier ".$oQuestForm->getName()." kan niet verwijderd worden. Deze is al gekoppeld aan een client";
             }else {
                 
                 //First load FormQuestions 
                 $aQuestions = surveypanel_models_iquestForms_iquestFormDatamapper::loadQuestionIdsByFormID($this->oDB, $this->oRequest->getParameter(2));
                 
                 //Delete question & question options
                 foreach($aQuestions AS $ikey => $iQuestionID) {
                     
                     surveypanel_models_iquestForms_iquestFormDatamapper::deleteOptionByQuestionID($this->oDB,$iQuestionID["questionID"]);
                     surveypanel_models_iquestForms_iquestFormDatamapper::deleteQuestionByID($this->oDB,$iQuestionID["questionID"]);
                 }
                 
               surveypanel_models_iquestForms_iquestFormDatamapper::deleteQuestionGroupByFormID($this->oDB, $this->oRequest->getParameter(2));
               surveypanel_models_iquestForms_iquestFormDatamapper::deleteFormScoreByFormID($this->oDB, $this->oRequest->getParameter(2));
               surveypanel_models_iquestForms_iquestFormDatamapper::deleteFormByID($this->oDB, $this->oRequest->getParameter(2));
                 
                 $this->aData["sFormSuccesMessage"] = "Het formulier ".$oQuestForm->getName()." is succesvol verwijderd";
             }
             
             
         }else {
             $this->aData["sFormMessage"] = "Formulier niet gevonden. Kan deze niet verwijderen";
         }
         
         $this->showForms();
     }
    
     
     public function deleteQuestion()
     {
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             
             if(surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestionByID($this->oDB, $this->oRequest->getParameter(3))){
                 
                 if(surveypanel_models_iquestForms_iquestFormDatamapper::checkQuestionLock($this->oDB, $this->oRequest->getParameter(3))) {
                     $this->aData["sFormMessage"] = "Vraag <strong>".$this->oRequest->getParameter(3)."</strong> is gelockt! Kan niet verwijderd worden";
                 }else {
                     
                     surveypanel_models_iquestForms_iquestFormDatamapper::deleteOptionByQuestionID($this->oDB,$this->oRequest->getParameter(3));
                     surveypanel_models_iquestForms_iquestFormDatamapper::deleteQuestionByID($this->oDB,$this->oRequest->getParameter(3));
                     $this->aData["sFormSuccesMessage"] = "De vraag is succesvol verwijderd";
                 }
                 
                 
             }else {
                 $this->aData["sFormMessage"] = "Vraag <strong>".$this->oRequest->getParameter(3)."</strong> bestaat niet! Kan niet verwijderd worden";
             }
             
             $this->changeForm();
         }else {
             
             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
         }
     }
     
     public function changeQuestion()
     {
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
             if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
             {
                 $this->aData["oQuestForm"] = $oQuestForm;
             }
             if(surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestionByFormID($this->oDB, $this->oRequest->getParameter(2) ,$this->oRequest->getParameter(3)))
             {
                 $aQuestion = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionByID($this->oDB, $this->oRequest->getParameter(3));
                 $this->aData["aQuestion"] = $aQuestion;
                 $iType = $aQuestion["questionType"];
                 if($aQuestion["questionType"] == 3) {
                     //Its a choicelist type form, so get the choices
                     $this->aData["aQuestion"]["aOptions"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionOptionsByQuestionID($this->oDB, $this->oRequest->getParameter(3));
                    
                 }
                 
                 if($oQuestForm->getType() !="Simple")
                 {
                     //load parent questions 
                     $this->aData["aQuestion"]["aParentQuestions"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadParentQuestions($this->oDB,$oQuestForm);
                 }
                 
                 //loadQuestionGroups
                 if($oQuestForm->getType() == "Complex")
                 {
                     $this->aData["aQuestion"]["aQuestionGroups"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionGroups($this->oDB,$oQuestForm);
                    
                     
                 }
                 
                 $this->aData["aQuestion"]["f_questionGroupNew"] ="";
                 if(BChelpers_formHandler::formSend("changequestion"))
                 {
                     BChelpers_formHandler::addFieldCheckNew("f_question","Vraag / Stelling",true,"surveypanel_checkLibary::checkMandatory","Geef een geldige vraag op ");
                     BChelpers_formHandler::addFieldCheckNew("f_answerTypes","Type antwoord",true,"surveypanel_checkLibary::checkQuestionType","Kies een geldig type antwoord ");
                     
                     if(!BChelpers_formHandler::handleForm())
                     {
                         $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                         $this->aData["aQuestion"]["question"] =  $_POST["f_question"];
                         $this->aData["aQuestion"]["questionType"] =  $_POST["f_answerTypes"];
                         
                         if($oQuestForm->getType() == "Complex")
                         {
                             $this->aData["aQuestion"]["questionGroupID"] =  $_POST["f_QuestionGroup"];
                             
                         }
                         
                         if($oQuestForm->getType() !="Simple")
                         {
                             $this->aData["aQuestion"]["question"] =  $_POST["f_question"];
                             $this->aData["aQuestion"]["parentQuestions"] =  $_POST["f_subQuestion"];
                             $this->aData["aQuestion"]["answers"] =  $_POST["f_possibleAnswers"];
                         }
                         
                     }else {
                         $this->aData["aQuestion"]["question"] =  $_POST["f_question"];
                         $this->aData["aQuestion"]["questionType"] =  $_POST["f_answerTypes"];
                         
                         if($oQuestForm->getType() !="Simple")
                         {
                             $this->aData["aQuestion"]["question"] =  $_POST["f_question"];
                             $this->aData["aQuestion"]["parentQuestion"] =  $_POST["f_subQuestion"];
                             $this->aData["aQuestion"]["answers"] =  $_POST["f_possibleAnswers"];
                         }
                         
                         if($oQuestForm->getType() =="Complex")
                         {
                             $this->aData["aQuestion"]["questionGroupID"] =  $_POST["f_QuestionGroup"];
                             
                             //check QuestionGRoup if Mew than save
                             if($_POST["f_questionGroupNew"] !="" ) {
                                 
                                 //checkQuestionGroup($p_oDB, $p_oForm,$p_sName)
                                 if(!surveypanel_models_iquestforms_iquestFormDatamapper::checkQuestionGroup($this->oDB,$oQuestForm,$_POST["f_questionGroupNew"])) {
                                     $this->aData["aQuestion"]["questionGroupID"] =  surveypanel_models_iquestForms_iquestFormDatamapper::saveNewQuestionGroup($this->oDB,$oQuestForm,$_POST["f_questionGroupNew"]);
                                     $this->aData["aQuestion"]["aQuestionGroups"] = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionGroups($this->oDB,$oQuestForm);
                                     
                                 }
                                 
                             }
                                 
                             
                         }
                         
                         //save Form
                         if(surveypanel_models_iquestforms_iquestFormDatamapper::saveQuestion($this->oDB, $this->aData["aQuestion"]) == 1) {
                             $this->aData["sFormSuccesMessage"] = "De vraag is gewijzigd";
                         }else {
                             $this->aData["sFormMessage"] = "De vraag is NIET gewijzigd! Probeer opnieuw";
                         }
                         
                         //Check if the type is changed if, see if the options must be deleted
                         if($_POST["f_answerTypes"] != 3 && $iType == 3)
                         {
                             //delete questionOptions
                             surveypanel_models_iquestforms_iquestFormDatamapper::deleteQuestionOptions($this->oDB, $this->aData["aQuestion"]);
                         }else {
                             
                         }
                     }
                     
                 }else {
                    
                 }
                 
                 
                 
                 
                
             }else {
                  
                    $this->aData["sFormMessage"] = "Vraag <strong>".$this->oRequest->getParameter(3)."</strong> bestaat niet of hoort niet bij het opgegeven formulier! Kan vraag niet wijzigen";
                }
         }else {
             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
         }
     }
     
     
     public function changeScore() {
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $_SESSION["currentTab"] = "Scores";
             
             if(surveypanel_models_iquestforms_iquestFormDatamapper::checkScoreByID($this->oDB, $this->oRequest->getParameter(3))) {
                 
                 $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
                 if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
                 {
                     $this->aData["oQuestForm"] = $oQuestForm;
                 }
                 
                 $aScore = surveypanel_models_iquestforms_iquestFormDatamapper::loadScore($this->oDB, $this->oRequest->getParameter(3));
                 $this->aData["scoreID"] = $this->oRequest->getParameter(3);
                 //Handle the scores for the forms with types of Simple
                 if(BChelpers_formHandler::formSend("changescore"))
                 {
                     
                     if(!BChelpers_formHandler::handleForm())
                     {
                         $this->aData["f_scoreDesription"] = BChelpers_formHandler::getValue("scoreDesription");
                         $this->aData["f_scoreLow"] = BChelpers_formHandler::getValue("scoreLow");
                         $this->aData["f_scoreHigh"] = BChelpers_formHandler::getValue("scoreHigh");
                         $this->aData["comparison"] = BChelpers_formHandler::getValue("comparison");
                         
                     }else {
                         
                         $bCheck = surveypanel_models_iquestforms_iquestFormDatamapper::checkScore(
                             $this->oDB,
                             $oQuestForm,
                             0,
                             BChelpers_formHandler::getValue("scoreDesription"),
                             BChelpers_formHandler::getValue("scoreLow"),
                             BChelpers_formHandler::getValue("scoreHigh"),
                             BChelpers_formHandler::getValue("comparison")
                             );
                         
                         if(!$bCheck)
                         {
                             surveypanel_models_iquestforms_iquestFormDatamapper::saveScore(
                                 $this->oDB,
                                 $oQuestForm,
                                 $this->oRequest->getParameter(3),
                                 0,
                                 BChelpers_formHandler::getValue("scoreDesription"),
                                 BChelpers_formHandler::getValue("scoreLow"),
                                 BChelpers_formHandler::getValue("scoreHigh"),
                                 BChelpers_formHandler::getValue("comparison"));
                             
                             
                             $_SESSION["currentTab"] = "Scores";
                            
                             $this->aData["sFormSuccesMessage"] = "De score is succesvol gewijzigd";
                             $this->aData["f_scoreDesription"] = BChelpers_formHandler::getValue("scoreDesription");
                             $this->aData["f_scoreLow"] = BChelpers_formHandler::getValue("scoreLow");
                             $this->aData["f_scoreHigh"] = BChelpers_formHandler::getValue("scoreHigh");
                             $this->aData["comparison"] = BChelpers_formHandler::getValue("comparison");
                             
                         }else
                         {
                             
                             $this->aData["sFormMessage"] = "Score record is al toegevoegd";
                             $this->aData["f_scoreDesription"] = BChelpers_formHandler::getValue("scoreDesription");
                             $this->aData["f_scoreLow"] = BChelpers_formHandler::getValue("scoreLow");
                             $this->aData["f_scoreHigh"] = BChelpers_formHandler::getValue("scoreHigh");
                             $this->aData["comparison"] = BChelpers_formHandler::getValue("comparison");
                         }
                     }
                     
                 } else {
                    
                     $this->aData["f_scoreDesription"] = $aScore["scoreDescription"];
                     $this->aData["f_scoreLow"] = $aScore["scoreLow"];
                     $this->aData["f_scoreHigh"] = $aScore["scoreHigh"];
                     $this->aData["comparison"] = $aScore["comparison"];
                 }
                 
                 //if(surveypanel_models_iquestforms_iquestFormDatamapper::changeScore($this->oDB, $this->oRequest->getParameter(3)) == 1) {
                    // $this->aData["sFormSuccesMessage"] = "De score is succesvol gewijzigd";
                 //}else {
                    // $this->aData["sFormMessage"] = "De score ( ".$this->oRequest->getParameter(3)." ) is niet gewijzig";
                // }
             }else {
                 $this->aData["sFormMessage"] = "De score ( ".$this->oRequest->getParameter(3)." ) bestaat niet! Kan deze niet wijzigen";
             }
             
             $this->changeForm();
         } else {
             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
             
         }
     }
     
     public function deleteScore() {
         
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $_SESSION["currentTab"] = "Scores";
             
             if(surveypanel_models_iquestforms_iquestFormDatamapper::checkScoreByID($this->oDB, $this->oRequest->getParameter(3))) {
                 
                 if(surveypanel_models_iquestforms_iquestFormDatamapper::deleteScore($this->oDB, $this->oRequest->getParameter(3)) == 1) {
                    $this->aData["sFormSuccesMessage"] = "De score is succesvol verwijderd";
                 }else {
                     $this->aData["sFormMessage"] = "De score ( ".$this->oRequest->getParameter(3)." ) is niet verwijderd";
                 }
             }else {
                 $this->aData["sFormMessage"] = "De score ( ".$this->oRequest->getParameter(3)." ) bestaat niet! Kan deze niet verwijderen";
             }
             
             $this->changeForm();
         } else {
             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
       
         }
     }
     
     
     public function deleteScoreGroup()
     {
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $_SESSION["currentTab"] = "Scores";
             if(surveypanel_models_iquestforms_iquestFormDatamapper::checkScoreGroupByID($this->oDB, $this->oRequest->getParameter(3))) {
                
                 if(surveypanel_models_iquestforms_iquestFormDatamapper::deleteScoreGroup($this->oDB, $this->oRequest->getParameter(3)) == 1) {
                     $this->aData["sFormSuccesMessage"] = "De scoregroep is succesvol verwijderd";
                 }else {
                     $this->aData["sFormMessage"] = "De scoregroep ( ".$this->oRequest->getParameter(3)." ) is niet verwijderd";
                 }
             }else {
                 $this->aData["sFormMessage"] = "De scoregroep ( ".$this->oRequest->getParameter(3)." ) bestaat niet! Kan deze niet verwijderen";
             }
             
             $this->changeForm();
             
             
         }else {
             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
         }
     }
     
     public function changeScoreGroup() {
        
         if(surveypanel_models_iquestforms_iquestFormDatamapper::checkFormByID($this->oRequest->getParameter(2), $this->oDB))
         {
             $_SESSION["currentTab"] = "Scores";
             if(surveypanel_models_iquestforms_iquestFormDatamapper::checkScoreGroupByID($this->oDB, $this->oRequest->getParameter(3))) {
                 
                 $oQuestForm = surveypanel_models_iquestforms_iquestFormDatamapper::loadFormByID($this->oRequest->getParameter(2), $this->oDB);
                 if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
                 {
                     $this->aData["oQuestForm"] = $oQuestForm;
                 }
                 $this->aData["iGroupID"] = $this->oRequest->getParameter(3);
                 $aScoreGroup = surveypanel_models_iquestforms_iquestFormDatamapper::loadScoreGroupByID($this->oDB, $this->oRequest->getParameter(3));
                 
                 if(BChelpers_formHandler::formSend("changescoregroup"))
                 {
                     BChelpers_formHandler::addFieldCheckNew("scoreGroup","Groepnaam",true,"surveypanel_checkLibary::checkPlainText","Geef een geldige groepnaam op ");
                     BChelpers_formHandler::addFieldCheckNew("groupStartRange","Start van de vraag range",true,"surveypanel_checkLibary::checkScore","Geef een geldig getal op ");
                     BChelpers_formHandler::addFieldCheckNew("groupEndRange","Eind van de vraag range",true,"surveypanel_checkLibary::checkScore","Geef een geldig getal op");
                     
                     if(!BChelpers_formHandler::handleForm())
                     {
                         $this->aData["f_scoreGroup"] = BChelpers_formHandler::getValue("scoreGroup");
                         $this->aData["f_groupStartRange"] = BChelpers_formHandler::getValue("groupStartRange");
                         $this->aData["f_groupEndRange"] = BChelpers_formHandler::getValue("groupEndRange");
                         $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                     }else {
                         
                         
                         $bCheck = surveypanel_models_iquestforms_iquestFormDatamapper::checkScoreGroup(
                             $this->oDB,
                             $oQuestForm,
                             BChelpers_formHandler::getValue("scoreGroup")
                             );
                         
                         if(!$bCheck)
                         {
                            if( surveypanel_models_iquestforms_iquestFormDatamapper::saveScoreGroup(
                                 $this->oDB,
                                 $this->oRequest->getParameter(3),
                                 BChelpers_formHandler::getValue("scoreGroup"),
                                 BChelpers_formHandler::getValue("groupStartRange"),
                                 BChelpers_formHandler::getValue("groupEndRange")
                                ) == 1) {
                                    $this->aData["sFormSuccesMessage"] = "De scoregroep is succesvol gewijzigd";
                                }else {
                                    $this->aData["sFormMessage"] =  "De score groep is niet gewijzigd";
                                }
                                
                                $this->aData["f_scoreGroup"] = BChelpers_formHandler::getValue("scoreGroup");
                                $this->aData["f_groupStartRange"] = BChelpers_formHandler::getValue("groupStartRange");
                                $this->aData["f_groupEndRange"] = BChelpers_formHandler::getValue("groupEndRange");
                                
                        }else {
                             
                             $this->aData["sFormMessage"] =  "Er bestaat al een groep met de naam : ".BChelpers_formHandler::getValue("scoreGroup");
                             $this->aData["f_scoreGroup"] = BChelpers_formHandler::getValue("scoreGroup");
                             $this->aData["f_groupStartRange"] = BChelpers_formHandler::getValue("groupStartRange");
                             $this->aData["f_groupEndRange"] = BChelpers_formHandler::getValue("groupEndRange");
                         }
                         
                     }
                 }else {
                     
                     $this->aData["f_scoreGroup"] = $aScoreGroup["scoreGroup"];
                     $this->aData["f_groupStartRange"] = $aScoreGroup["startRange"];
                     $this->aData["f_groupEndRange"] = $aScoreGroup["endRange"];
                 }
                
                 
             }else {
                 $this->aData["sFormMessage"] = "De scoregroep ( ".$this->oRequest->getParameter(3)." ) bestaat niet! Kan deze niet wijzigen";
             }
             
             $this->changeForm();
             
             
         }else {
             $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/forms");
         }
     }
    
}

