<?php
/**
 *	iquestFormDatamapper.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 12 sep. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class surveypanel_models_iquestForms_iquestFormDatamapper
{
    public static function mapFormFields($p_oForm,$p_aFormFields)
    {
        $p_oForm->setType(ucfirst($p_aFormFields["complex"]));
        $p_oForm->setOwner($p_aFormFields["companyIDSelected"]);
        $p_oForm->setName($p_aFormFields["f_listName"]);
        $p_oForm->setDescription($p_aFormFields["f_decription"]);
        $p_oForm->setInter($p_aFormFields["f_inter"]);
        
    }
    
    public static function mapFormToFieldArray($p_oForm)
    {
        $aFormFields = array();
        $aFormFields["formID"] = $p_oForm->getFormID();
        $aFormFields["complex"] = strtolower($p_oForm->getType());
        $aFormFields["companyIDSelected"] = $p_oForm->getOwner();
        $aFormFields["f_listName"] = $p_oForm->getName();
        $aFormFields["f_decription"] = $p_oForm->getDescription();
        $aFormFields["f_inter"] = $p_oForm->getInter();
        
        return $aFormFields;
    }
    
    
    //DAtabase Querys
    public static function loadForms($p_oEncryption, $p_oDB)
    {
        $sSQL = "SELECT *,
                 (SELECT companyName FROM companys WHERE companys.companyID = forms.companyID) AS companyName 
                 FROM forms";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($aResult))
        {
            $aForms = array();
            
            foreach($aResult as $aForm)
            {
                $oQuestForm = surveypanel_models_iquestforms_iquestFormFactory::createForm($aForm["formType"]);
                if($oQuestForm instanceof surveypanel_models_iquestforms_iquestForm)
                {
                    $oQuestForm->setformID($aForm["formID"]);
                    $oQuestForm->setActive($aForm["active"]);
                    $oQuestForm->setType(ucfirst($aForm["formType"]));
                    $oQuestForm->setOwner(($aForm["companyName"] !="" ?$p_oEncryption->deCrypt($aForm["companyName"]):"Mareis B.V."));
                    $oQuestForm->setName($aForm["formName"]);
                    $oQuestForm->setDescription($aForm["formDescription"]);
                    $oQuestForm->setInter($aForm["formInterpretation"]);
                    
                    //Check if someone is uses the form
                    //If this is the case than lock the form 
                    $sSQL = "SELECT count(formID) AS iCheck FROM clientforms WHERE formID=:ID";
                    $oQuery = $p_oDB->prepare($sSQL);
                    
                    $oQuery->bindParam(":ID", $aForm["formID"]);
                    $oQuery->execute();
                    $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
                    if($aResult["iCheck"] > 0)
                    {
                        $oQuestForm->setLockt(true);
                    }
                    
                    $aForms[] = $oQuestForm;
                }
            }
            
            return $aForms;
            
            
            
        }else {
            return false;
        }
        
        
    }
    
    public static function checkFormByName($p_oForm, $p_oDB, $p_iExcludeID = 0)
    {
       
        if($p_iExcludeID > 0)
        {
            $sSQL = "SELECT count(formID) AS iForm FROM forms WHERE formName=:name AND formID <>:ID";
            $oQuery = $p_oDB->prepare($sSQL);
            
            $sName = $p_oForm->getName();
            $oQuery->bindParam(":name",$sName);
            $oQuery->bindParam(":ID", $p_iExcludeID);
        }else{
            $sSQL = "SELECT count(formID) AS iForm FROM forms WHERE formName=:name";
            $oQuery = $p_oDB->prepare($sSQL);
            $sName = $p_oForm->getName();
            $oQuery->bindParam(":name",$sName);
        }
        
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iForm"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    public static function checkFormByID($p_iID, $p_oDB)
    {
        
        
       $sSQL = "SELECT count(formID) AS iForm FROM forms WHERE formID=:ID";
       $oQuery = $p_oDB->prepare($sSQL);
       $oQuery->bindParam(":ID", $p_iID);
       
       $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iForm"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    
    public static function saveNewForm($p_oForm, $p_oDB)
    {
        $sSQL = "INSERT INTO forms (companyID,formType,formName,formDescription,formInterpretation) VALUES (:company,:type,:name,:description,:inter)";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $sOwner = $p_oForm->getOwner(); 
        $sType = $p_oForm->getType();
        $sName = $p_oForm->getName();
        $sDescription = $p_oForm->getDescription();
        $sInter = $p_oForm->getInter();
        
        $oQuery->bindParam(":company", $sOwner);
        $oQuery->bindParam(":type", $sType);
        $oQuery->bindParam(":name", $sName);
        $oQuery->bindParam(":description", $sDescription);
        $oQuery->bindParam(":inter", $sInter);
        
        $oQuery->execute();
        $p_oForm->setFormID($p_oDB->lastInsertID());
        return true;
    }
    
    
    public static function saveForm($p_oForm, $p_oDB)
    {
        $sSQL = "UPDATE forms SET companyID=:company,formType=:type,formName=:name,formDescription=:description,formInterpretation=:inter WHERE formID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iID = $p_oForm->getFormID();
        $sOwner = $p_oForm->getOwner();
        $sType = strtolower($p_oForm->getType());
        $sName = $p_oForm->getName();
        $sDescription = $p_oForm->getDescription();
        $sInter = $p_oForm->getInter();
        
        $oQuery->bindParam(":ID", $iID);
        $oQuery->bindParam(":company", $sOwner);
        $oQuery->bindParam(":type", $sType);
        $oQuery->bindParam(":name", $sName);
        $oQuery->bindParam(":description", $sDescription);
        $oQuery->bindParam(":inter", $sInter);
        
        $oQuery->execute();
        return $oQuery->rowCount();
    }

    public static function loadFormByID($p_iID, $p_oDB, $p_iClientFormID = 0)
    {
        $sSQL = "SELECT * FROM forms WHERE formID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            $oQuestForm = surveypanel_models_iquestforms_iquestFormFactory::createForm($aResult["formType"]);
            
            if($oQuestForm != null)
            {
                $oQuestForm->setFormID($aResult["formID"]);
                $oQuestForm->setClientFormID($p_iClientFormID); 
                $oQuestForm->setOwner($aResult["companyID"]);
                $oQuestForm->setName($aResult["formName"]);
                $oQuestForm->setDescription($aResult["formDescription"]);
                $oQuestForm->setInter($aResult["formInterpretation"]);
                
                //Check if someone is uses the form
                //If this is the case than lock the form
                $sSQL = "SELECT count(formID) AS iCheck FROM clientforms WHERE formID=:ID";
                $oQuery = $p_oDB->prepare($sSQL);
                
                $oQuery->bindParam(":ID", $aResult["formID"]);
                $oQuery->execute();
                $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
                if($aResult["iCheck"] > 0)
                {
                    $oQuestForm->setLockt(true);
                }
                
                
                
                //LoadQuestions
                $sSQL = "SELECT * FROM formquestions WHERE formID =:ID";
                $oQuery = $p_oDB->prepare($sSQL);
                $oQuery->bindParam(":ID", $p_iID);
                $oQuery->execute();
                
                $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($aResult))
                {
                    foreach($aResult as $aQuestion)
                    {
                        $oQuestion = new surveypanel_models_iquestforms_iquestQuestion();
                        $oQuestion->setQuestionID($aQuestion["questionID"]);
                        $oQuestion->setQuestionType($aQuestion["questionType"]);
                        $oQuestion->setQuestion($aQuestion["question"]);
                        
                        //Check if this question was used by a client
                        $sSQL = "SELECT count(answerID) AS iCheck FROM clientanswers WHERE questionID=:ID";
                        $oQuery = $p_oDB->prepare($sSQL);
                        
                        $oQuery->bindParam(":ID", $aQuestion["questionID"]);
                        $oQuery->execute();
                        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
                        if($aResult["iCheck"] > 0)
                        {
                            $oQuestion->setLockt(true);
                        }
                        
                        if($oQuestForm->getType() == "Medium")
                        {
                            $oQuestion->setParentQuestionID($aQuestion["parentQuestion"]);
                            $oQuestion->setNumberOfAnswers($aQuestion["answers"]);
                        }
                        
                        
                        if($aQuestion["questionType"] == "3")
                        {
                        //When question type is option thanm load the options
                        $sSQL = "SELECT * FROM questionoptions WHERE questionID =:ID";
                        $oQuery = $p_oDB->prepare($sSQL);
                        $oQuery->bindParam(":ID", $aQuestion["questionID"]);
                        $oQuery->execute();
                        
                        $aOptions = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                        if(!empty($aOptions))
                            
                            foreach($aOptions AS $aOption)
                            {
                                $oQuestion->addOption($aOption["questionOption"], $aOption["optionScore"]);
                            }
                            
                        }
                        
                        
                        $oQuestForm->addQuestion($oQuestion);
                    }
                }
               
                return $oQuestForm;
            }else 
                {
                    return false;
                }
        }else{
            return false;
        }
    }
    
    
    public static function loadFullFormByClientformID($p_oDB, $p_iClientFormID)
    {
        //first get form record
        $sSQL = "SELECT * FROM forms WHERE formID =(SELECT formID FROM clientforms WHERE clientFormID = :ID)";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iClientFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            $oQuestForm = surveypanel_models_iquestforms_iquestFormFactory::createForm($aResult["formType"]);
            if($oQuestForm != null)
            {
                $oQuestForm->setFormID($aResult["formID"]);
                $oQuestForm->setClientFormID($p_iClientFormID);
                $oQuestForm->setOwner($aResult["companyID"]);
                $oQuestForm->setName($aResult["formName"]);
                $oQuestForm->setDescription($aResult["formDescription"]);
                $oQuestForm->setInter($aResult["formInterpretation"]);
                
                //LoadQuestions
                $sSQL = "SELECT * FROM formquestions WHERE formID =:ID";
                $oQuery = $p_oDB->prepare($sSQL);
                $oQuery->bindParam(":ID", $aResult["formID"]);
                $oQuery->execute();
                
                $aQuestions = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($aQuestions))
                {
                    foreach($aQuestions as $aQuestion)
                    {
                        $oQuestion = new surveypanel_models_iquestforms_iquestQuestion();
                        $oQuestion->setQuestionID($aQuestion["questionID"]);
                        $oQuestion->setQuestionType($aQuestion["questionType"]);
                        $oQuestion->setQuestion($aQuestion["question"]);
                        $oQuestion->setNumberOfAnswers($aQuestion["answers"]);
                        
                        //LoadAnswer(s)
                        $sSQL = "SELECT * FROM clientanswers WHERE clientFormID=:clientFormID AND questionID =:QuestionID";
                        $oQuery = $p_oDB->prepare($sSQL);
                        $oQuery->bindParam(":clientFormID", $p_iClientFormID);
                        $oQuery->bindParam(":QuestionID", $aQuestion["questionID"]);
                        $oQuery->execute();
                        
                        if($oQuestion->getNumberOfAnswers() > 1) {
                            
                            $aAnswer = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                            if($aQuestion["questionType"] == "3")
                            {
                                //Load the question OPtions
                                $sSQL = "SELECT * FROM questionoptions WHERE questionID =:ID";
                                $oQuery = $p_oDB->prepare($sSQL);
                                $oQuery->bindParam(":ID", $aQuestion["questionID"]);
                                $oQuery->execute();
                                
                                $aOptions = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                                if(!empty($aOptions))
                                {
                                    foreach($aOptions AS $aOption)
                                    {
                                        $oQuestion->addOption($aOption["questionOption"], $aOption["optionScore"]);
                                    }
                                }
                                
                                foreach($aAnswer AS $iKey => $aQuestionAnswer)
                                {
                                    $oQuestion->setMultiScore($aQuestionAnswer["questionScore"],$aQuestionAnswer["answer"]);
                                }
                                
                            }else {
                                foreach($aAnswer AS $iKey => $aQuestionAnswer)
                                {
                                    $oQuestion->setScore("n.v.t.");
                                    $oQuestion->setMultiAnswer($aQuestionAnswer["answer"]);
                                }
                            }
                            
                            
                        }else {
                            $aAnswer = $oQuery->fetch(PDO::FETCH_ASSOC);
                            
                            if($aQuestion["questionType"] == "3")
                            {
                                //When question type is option thanm load the options
                                $sSQL = "SELECT * FROM questionoptions WHERE questionID =:ID";
                                $oQuery = $p_oDB->prepare($sSQL);
                                $oQuery->bindParam(":ID", $aQuestion["questionID"]);
                                $oQuery->execute();
                                
                                $aOptions = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                                if(!empty($aOptions))
                                {
                                    foreach($aOptions AS $aOption)
                                    {
                                        $oQuestion->addOption($aOption["questionOption"], $aOption["optionScore"]);
                                    }
                                }
                                
                                $oQuestion->setScore($aAnswer["questionScore"])->setScoreAnswer();
                            }else 
                                {
                                    $oQuestion->setScore("n.v.t.");
                                    $oQuestion->setAnswer($aAnswer["answer"]);
                                }
                        }
                            
                            
                        
                        
                        $oQuestForm->addQuestion($oQuestion);
                       
                    }
                }
            }
            
            return $oQuestForm;
        }else {
            return false;
        }
            
    }
    
    public static function changeStatus($p_oDB,$p_iFormID,$p_iStatus)
    {
        $sSQL = "UPDATE forms SET active=:State WHERE formID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iFormID);
        $oQuery->bindParam(":State", $p_iStatus);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
    public static function mapFormQuestionFields($p_oForm,$p_aFormFields)
    {
        
        $oQuestion = new surveypanel_models_iquestforms_iquestQuestion();
        $oQuestion->setQuestionType($p_aFormFields["f_answerTypes"]);
        $oQuestion->setQuestion($p_aFormFields["f_question"]);
        
        foreach($p_aFormFields as $fieldName => $field)
        {
            if(preg_match("/optionField_/",$fieldName) && $field !="")
            {
                $aField = explode("_",$fieldName);
                $oQuestion->addOption($field, $p_aFormFields["optionScoreField_".$aField[1]]);
            }elseif($fieldName == "f_answerOptions" && $field !="0")
            {
                $aOptions = explode("::",$field);
                foreach($aOptions AS $aOption)
                {
                    $aSplittedOption = explode("=>",$aOption);
                    $oQuestion->addOption($aSplittedOption[0],$aSplittedOption[1]);
                }
            }
        }
        
        if($p_oForm->getType() == "Medium")
        {
            $oQuestion->setParentQuestionID($p_aFormFields["f_subQuestion"]);
            $oQuestion->setNumberOfAnswers($p_aFormFields["f_possibleAnswers"]);
        }
        
        $p_oForm->addQuestion($oQuestion);
        
     }
     
     public static function loadParentQuestions($p_oDB,$p_oForm) {
         
         $sSQL = "SELECT * FROM formquestions WHERE formID=:ID AND parentQuestion = '0'";
         $oQuery = $p_oDB->prepare($sSQL);
         
         $iFormID = $p_oForm->getFormID();
         $oQuery->bindParam(":ID", $iFormID);
         
         $oQuery->execute();
         
         $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
         return $aResult;
     }
     
     public static function loadParentQuestionsWithRange($p_oDB,$p_oForm, $p_iSart, $p_iEnd) {
         
         $sSQL = "SELECT * FROM formquestions WHERE formID=:ID AND questionType= '3' AND parentQuestion = '0' AND questionID between :start AND :end ";
         $oQuery = $p_oDB->prepare($sSQL);
         
         $iFormID = $p_oForm->getFormID();
         $oQuery->bindParam(":ID", $iFormID);
         $oQuery->bindParam(":start", $p_iSart);
         $oQuery->bindParam(":end", $p_iEnd);
         
         $oQuery->execute();
         
         $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
         return $aResult;
     }
     
     public static function loadSubQuestions($p_oDB,$p_oForm,$p_iParent) {
         
         $sSQL = "SELECT * FROM formquestions WHERE formID=:ID AND questionType= '3' AND parentQuestion =:parent";
         $oQuery = $p_oDB->prepare($sSQL);
         $iFormID = $p_oForm->getFormID();
         $oQuery->bindParam(":ID", $iFormID);
         $oQuery->bindParam(":parent", $p_iParent);
         
         $oQuery->execute();
         
         $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
         return $aResult;
     }
     
     public static function loadQuestionByID($p_oDB, $p_iQuestionID) 
     {
         $sSQL = "SELECT * FROM formquestions WHERE questionID=:ID";
         $oQuery = $p_oDB->prepare($sSQL);
        
         $oQuery->bindParam(":ID", $p_iQuestionID);
         $oQuery->execute();
         
         $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
         return $aResult;
     }
     
    public static function checkQuestion($p_oDB,$p_oForm)
    {
        //at this point we need only the last / only question in the form so we can use $aQuestion[0]
        $aQuestion = $p_oForm->getQuestions(true);
        
        $sSQL = "SELECT count(questionID) AS iCheck FROM formquestions WHERE formID=:ID AND question=:question";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_oForm->getFormID());
        $oQuery->bindParam(":question", $aQuestion->getQuestion());
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
        
   }
   
   public static function checkQuestionByID($p_oDB,$p_iQuestionID)
   {
      
       
       $sSQL = "SELECT count(questionID) AS iCheck FROM formquestions WHERE questionID=:question";
       $oQuery = $p_oDB->prepare($sSQL);
       $oQuery->bindParam(":question", $p_iQuestionID);
       $oQuery->execute();
       
       $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
       if($aResult["iCheck"] > 0)
       {
           return true;
       }else {
           return false;
       }
       
   }
   
   public static function checkQuestionByFormID($p_oDB,$p_iFormID,$p_iQuestionID)
   {
       
       
       $sSQL = "SELECT count(questionID) AS iCheck FROM formquestions WHERE formID=:ID AND questionID=:question";
       $oQuery = $p_oDB->prepare($sSQL);
       $oQuery->bindParam(":ID", $p_iFormID);
       $oQuery->bindParam(":question", $p_iQuestionID);
       $oQuery->execute();
       
       $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
       if($aResult["iCheck"] > 0)
       {
           return true;
       }else {
           return false;
       }
       
   }
   
   public static function loadQuestionOptionsByQuestionID($p_oDB,$p_iQuestionID)
   {
       $sSQL = "SELECT * FROM questionoptions WHERE questionID=:question";
       $oQuery = $p_oDB->prepare($sSQL);
       $oQuery->bindParam(":question", $p_iQuestionID);
       $oQuery->execute();
       
       $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
       return $aResult;
   }
    
    public static function saveNewQuestion($p_oDB,$p_oForm, $p_iGroupID = 0)
    {
        //at this point we need only the last / only question in the form so we can use $aQuestion[0]
        $aQuestion = $p_oForm->getQuestions(true);
        
        $sSQL = "INSERT INTO formquestions (formID,questionGroupID,parentQuestion,answers,questionType,question) VALUES (:formID,:groupID,:parent,:answers,:type,:question)";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":formID", $p_oForm->getFormID());
        $oQuery->bindParam(":groupID", $p_iGroupID);
        $oQuery->bindParam(":type", $aQuestion->getQuestionType());
        $oQuery->bindParam(":question", $aQuestion->getQuestion());
        $oQuery->bindParam(":parent", $aQuestion->getParentQuestion());
        $oQuery->bindParam(":answers", $aQuestion->getNumberOfAnswers());
        $oQuery->execute();
        
        $aQuestion->setQuestionID($p_oDB->lastInsertID());
        
        if($aQuestion->getQuestionType() == 3)
        {
            $aOptions = $aQuestion->getOptions();
            
            foreach($aOptions as $sOption => $iScore)
            {
              
                $sSQL = "INSERT INTO questionoptions (questionID,questionOption,optionScore) VALUES (:ID,:option,:score)";
                $oQuery = $p_oDB->prepare($sSQL);
                $oQuery->bindParam(":ID", $aQuestion->getQuestionID());
                $oQuery->bindParam(":option", $sOption);
                $oQuery->bindParam(":score", $iScore);
                $oQuery->execute();
            }
            
        }
    }
    
    public static function checkScoreByID($p_oDB,$p_iScoreID)
    {
        $sSQL = "SELECT count(formScoreID) AS iCheck FROM formscores WHERE formScoreID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_iScoreID);
        $oQuery->execute();
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function deleteScore($p_oDB,$p_iScoreID) {
        $sSQL = "DELETE FROM formscores WHERE formScoreID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_iScoreID);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
    public function loadScore($p_oDB,$p_iScoreID)
    {
        $sSQL = "SELECT * FROM formscores WHERE formScoreID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_iScoreID);
        $oQuery->execute();
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    public static function checkScore($p_oDB,$p_oForm,$p_iGroupID,$p_sScoreDesription ,$p_iScoreLow, $p_iScoreHigh, $p_sComparison)
    {
        $sSQL = "SELECT count(formScoreID) AS iCheck FROM formscores WHERE formID=:formID AND groupID=:groupID AND scoreDescription=:scoreDescription 
                 AND scoreLow=:scoreLow AND scoreHigh=:scoreHigh AND comparison=:comparison";
        
       
        
        $oQuery = $p_oDB->prepare($sSQL);
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->bindParam(":groupID", $p_iGroupID);
        $oQuery->bindParam(":scoreDescription", $p_sScoreDesription);
        $oQuery->bindParam(":scoreLow", $p_iScoreLow);
        $oQuery->bindParam(":scoreHigh", $p_iScoreHigh);
        $oQuery->bindParam(":comparison", $p_sComparison);
        $oQuery->execute();
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function saveNewScore($p_oDB,$p_oForm,$p_iGroupID,$p_sScoreDesription ,$p_iScoreLow, $p_iScoreHigh, $p_sComparison)
    {
        $sSQL = "INSERT INTO formscores (formID,groupID,scoreDescription,scoreLow,scoreHigh,comparison)
                 VALUES (:formID,:groupID,:scoreDescription,:scoreLow,:scoreHigh,:comparison)";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->bindParam(":groupID", $p_iGroupID);
        $oQuery->bindParam(":scoreDescription", $p_sScoreDesription);
        $oQuery->bindParam(":scoreLow", $p_iScoreLow);
        $oQuery->bindParam(":scoreHigh", $p_iScoreHigh);
        $oQuery->bindParam(":comparison", $p_sComparison);
        
        $oQuery->execute();
    }
    
    public static function saveScore($p_oDB,$p_oForm,$p_iScoreID,$p_iGroupID,$p_sScoreDesription ,$p_iScoreLow, $p_iScoreHigh, $p_sComparison)
    {
        $sSQL = "UPDATE formscores SET formID=:formID,groupID=:groupID,scoreDescription=:scoreDescription,scoreLow=:scoreLow,scoreHigh=:scoreHigh,comparison=:comparison
                 WHERE formScoreID=:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iScoreID);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->bindParam(":groupID", $p_iGroupID);
        $oQuery->bindParam(":scoreDescription", $p_sScoreDesription);
        $oQuery->bindParam(":scoreLow", $p_iScoreLow);
        $oQuery->bindParam(":scoreHigh", $p_iScoreHigh);
        $oQuery->bindParam(":comparison", $p_sComparison);
        
        $oQuery->execute();
    }
    
    public static function checkScoreGroup($p_oDB,$p_oForm,$p_sGroupName)
    {
        $sSQL = "SELECT count(scoreGRoupID) as iCheck FROM scoregroups WHERE formID= :formID AND scoreGroup = :group";
          
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormnID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormnID);
        $oQuery->bindParam(":group", $p_sGroupName);
        $oQuery->execute();
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function checkScoreGroupByID($p_oDB,$p_iGroupID) {
        $sSQL = "SELECT count(scoreGRoupID) as iCheck FROM scoregroups WHERE scoreGroupID = :ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_iGroupID);
        $oQuery->execute();
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public function deleteScoreGroup($p_oDB,$p_iGroupID)
    {
        $sSQL = "DELETE FROM scoregroups WHERE scoreGroupID = :ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_iGroupID);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
    public static function saveNewScoreGroup($p_oDB,$p_oForm,$p_sGroupName, $p_iStart, $p_iEnd)
    {
        $sSQL = "INSERT INTO scoregroups (formID,scoreGroup,startRange, endRange) 
                 VALUES (:formID,:group,:start,:end)";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":formID", $p_oForm->getFormID());
        $oQuery->bindParam(":group", $p_sGroupName);
        $oQuery->bindParam(":start", $p_iStart);
        $oQuery->bindParam(":end", $p_iEnd);
        
        $oQuery->execute();
        
    }
    
    public static function saveScoreGroup($p_oDB,$p_iGroupID,$p_sGroupName, $p_iStart, $p_iEnd)
    {
        $sSQL = "UPDATE scoregroups SET scoreGroup=:group,startRange=:start, endRange=:end
                 WHERE scoreGroupID=:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_iGroupID);
        $oQuery->bindParam(":group", $p_sGroupName);
        $oQuery->bindParam(":start", $p_iStart);
        $oQuery->bindParam(":end", $p_iEnd);
        
        $oQuery->execute();
        return $oQuery->rowCount();
        
    }
    
    public static function loadScoreGroupByID($p_oDB,$p_iGroupID) {
        $sSQL = "SELECT * FROM scoregroups WHERE scoreGroupID = :ID ";
        $oQuery = $p_oDB->prepare($sSQL);
        
       
        $oQuery->bindParam(":ID",$p_iGroupID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    
    public static function loadScoreGroups($p_oDB,$p_oForm)
    {
        $sSQL = "SELECT * FROM scoregroups WHERE formID=:formID ";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    public static function loadFormScores($p_oDB,$p_oForm)
    {
        $sSQL = "SELECT * FROM formscores WHERE formID=:formID ORDER BY scoreLow ASC";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    public static function saveClientAnswers($p_oDB, $p_oForm)
    {
       
        
        $iClientFormID = $p_oForm->getClientFormID();
        $aQuestions = $p_oForm->getQuestions();
        
        $sSQL = "INSERT INTO clientanswers (clientFormID, questionID,questionScore,answer) 
                 VALUES (:formID,:questionID,:score,:answer)";
        
        $oQuery = $p_oDB->prepare($sSQL);
       
        foreach($aQuestions as $oQuestion)
        {
            $iQuestionID = $oQuestion->getQuestionID();
            
            if($oQuestion->getQuestionType() == "3" && isset($_POST["question_".$iQuestionID]))
            {
                if($oQuestion->getNumberOfAnswers() > 1)
                {
                    
                    
                    $aOptions = array();
                    //First save the parent question
                    array_push($aOptions, $_POST["question_".$iQuestionID]);
                    $aScore = explode("_",$_POST["question_".$iQuestionID]);
                    $iScore = $aScore[0];
                    $sAnswer = $aScore[1];
                    
                    $oQuery->bindParam(":formID", $iClientFormID);
                    $oQuery->bindParam(":questionID", $iQuestionID);
                    $oQuery->bindParam(":score", $iScore);
                    $oQuery->bindParam(":answer", $sAnswer);
                    $oQuery->execute();
                    
                    //Save the subquestions
                    for($i=1;$i<=$oQuestion->getNumberOfAnswers();$i++)
                    {
                        if(isset( $_POST["question_".$iQuestionID."_".$i] ) && $_POST["question_".$iQuestionID."_".$i] !="")
                        {
                            if(!in_array($_POST["question_".$iQuestionID."_".$i], $aOptions)) {
                                
                                $aScore = explode("_",$_POST["question_".$iQuestionID."_".$i] );
                                $iScore = $aScore[0];
                                $sAnswer = $aScore[1];
                                
                                $oQuery->bindParam(":formID", $iClientFormID);
                                $oQuery->bindParam(":questionID", $iQuestionID);
                                $oQuery->bindParam(":score", $iScore);
                                $oQuery->bindParam(":answer", $sAnswer);
                                $oQuery->execute();
                                
                            }
                        }
                    }
                   
                }else {
                    $iScore = $_POST["question_".$iQuestionID];
                    $sAnswer = 0;
                    
                    $oQuery->bindParam(":formID", $iClientFormID);
                    $oQuery->bindParam(":questionID", $iQuestionID);
                    $oQuery->bindParam(":score", $iScore);
                    $oQuery->bindParam(":answer", $sAnswer);
                    $oQuery->execute();
                }
            }elseif($oQuestion->getQuestionType() != "3" && isset($_POST["question_".$iQuestionID])) {
                $iScore = 0;
                $sAnswer = $_POST["question_".$iQuestionID];
                
                $oQuery->bindParam(":formID", $iClientFormID);
                $oQuery->bindParam(":questionID", $iQuestionID);
                $oQuery->bindParam(":score", $iScore);
                $oQuery->bindParam(":answer", $sAnswer);
                $oQuery->execute();
            }
            
           
            
        }
        
        
    }
    
    public static function closeClientForm($p_oDB, $p_oForm,$p_iClient) {
        
        $sSQL = "UPDATE clientforms SET status = 'closed' WHERE clientFormID = :ID AND formID=:formID
                 AND clientforms.clientID = (SELECT clients.clientID FROM clients WHERE email=:client)";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iClientFormID = $p_oForm->getClientFormID();
        $iFormID = $p_oForm->getFormID();
       
        $oQuery->bindParam(":ID", $iClientFormID);
        $oQuery->bindParam(":formID",$iFormID );
        $oQuery->bindParam(":client", $p_iClient);
        $oQuery->execute();
    }
    
    public static function calculateScoreForSimple($p_oDB, $p_oForm)
    {
        $sSQL = "SELECT SUM(questionScore) AS scoreTotal FROM clientanswers WHERE clientFormID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iClientFormID = $p_oForm->getClientFormID();
        $oQuery->bindParam(":ID",$iClientFormID );
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        return $aResult["scoreTotal"];
    }
    
    public static function calculateScoreForMedium($p_oDB, $p_oForm,$p_iQuestionID)
    {
        $sSQL = "SELECT SUM(questionScore) AS scoreTotal FROM clientanswers WHERE clientFormID=:ID AND questionID = :question";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getClientFormID();
        $oQuery->bindParam(":ID", $iFormID);
        $oQuery->bindParam(":question", $p_iQuestionID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        return $aResult["scoreTotal"];
    }
    
   
    
    public static function checkQuestionGroup($p_oDB, $p_oForm,$p_sName)
    {
        $sSQL = "SELECT count(questionGroupID) as iCheck FROM questiongroups WHERE formID= :formID AND groupName = :group";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->bindParam(":group", $p_sName);
        $oQuery->execute();
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function saveNewQuestionGroup($p_oDB, $p_oForm,$p_sName)
    {
        $sSQL = "INSERT INTO questiongroups (formID,groupName) VALUES (:formID,:group)";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->bindParam(":group", $p_sName);
        $oQuery->execute();
        
        return $p_oDB->lastInsertId();
    }
    
    public static function loadQuestionGroups($p_oDB, $p_oForm)
    {
        $sSQL = "SELECT * FROM questiongroups WHERE formID =:formID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public static function loadGroupQuestions($p_oDB, $p_oForm, $p_iGroupID)
    {
        $sSQL = "SELECT questionID FROM formquestions WHERE formID=:formID AND questionGroupID=:ID AND parentQuestion='0'";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->bindParam(":ID", $p_iGroupID);
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_COLUMN);
        
        return $aResult;
        
    }
    
    public static function loadPossibleOptions($p_oDB, $p_oForm)
    {
        $sSQL = "SELECT questionID FROM formquestions WHERE formID =:formID AND questionType='3'";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $iFormID = $p_oForm->getFormID();
        $oQuery->bindParam(":formID", $iFormID);
        $oQuery->execute();
        $aQuestionResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        
        if(!empty($aQuestionResult))
        {
            $aOptions = array();
            foreach($aQuestionResult AS $aQuestion) 
            {
                $sSQL = "SELECT questionOption,optionScore FROM questionoptions WHERE questionID=:ID";
                $oQuery = $p_oDB->prepare($sSQL);
                $oQuery->bindParam(":ID", $aQuestion["questionID"]);
                $oQuery->execute();
                $aOptionResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
                
                $sOption = "";
                if(!empty($aOptionResult))
                {
                    foreach($aOptionResult AS $aOption)
                    {
                        $sOption .= ($sOption !=""?"::":"");
                        $sOption .= $aOption["questionOption"]."=>".$aOption["optionScore"];
                       
                    }
                    
                    if(!in_array($sOption, $aOptions))
                    {
                         $aOptions[] = $sOption;
                    }
                }
            }
            
            
           return $aOptions;
            
        }else {
            return false;
        }
    }
    
    
    public function deleteFormByID($p_oDB, $p_oFormID)
    {
        $sSQL = "DELETE FROM forms WHERE formID =:formID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":formID", $p_oFormID);
        $oQuery->execute();
    }
    
    public function deleteFormScoreByFormID($p_oDB, $p_oFormID)
    {
        $sSQL = "DELETE FROM formscores WHERE formID =:formID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":formID", $p_oFormID);
        $oQuery->execute();
    }
    
    public function deleteQuestionGroupByFormID($p_oDB, $p_oFormID) 
    {
        $sSQL = "DELETE FROM questiongroups WHERE formID =:formID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":formID", $p_oFormID);
        $oQuery->execute();
    }
    
    public function deleteOptionByQuestionID($p_oDB, $p_oQuestionID) {
        $sSQL = "DELETE FROM questionoptions WHERE questionID =:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_oQuestionID);
        $oQuery->execute();
    }
    
    public function deleteQuestionByID($p_oDB, $p_iQuestionID) {
        $sSQL = "DELETE FROM formquestions WHERE questionID =:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iQuestionID);
        $oQuery->execute();
    }
    
    public function loadQuestionIdsByFormID($p_oDB, $p_oFormID)
    {
        $sSQL = "SELECT questionID FROM formquestions WHERE formID =:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_oFormID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public static function checkQuestionLock($p_oDB, $p_iQuestionID)
    {
        $sSQL = "SELECT COUNT(questionID) AS iCheck FROM clientanswers WHERE questionID =:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iQuestionID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
        
        
    }
    
    public static function saveQuestion($p_oDB,$p_aQuestion)
    {
        $sSQL = "UPDATE formquestions SET question=:question,questionType=:type,parentQuestion=:parent,answers=:answers,questionGroupID=:group WHERE questionID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_aQuestion["questionID"]);
        $oQuery->bindParam(":question", $p_aQuestion["question"]);
        $oQuery->bindParam(":type", $p_aQuestion["questionType"]);
        $oQuery->bindParam(":parent", $p_aQuestion["parentQuestion"]);
        $oQuery->bindParam(":answers", $p_aQuestion["answers"]);
        $oQuery->bindParam(":group", $p_aQuestion["questionGroupID"]);
        
        $oQuery->execute();
        return $oQuery->rowCount();
    }
    
    public static function deleteQuestionOptions($p_oDB,$p_aQuestion)
    {
        $sSQL = "DELETE FROM questionoptions WHERE questionID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->bindParam(":ID", $p_aQuestion["questionID"]);
        
        $oQuery->execute();
        return $oQuery->rowCount();
    }
    
    public static function checkQuestionID($p_oDB,$p_iQuestionID) {
        
        $sSQL = "SELECT COUNT(questionID) AS iCheck FROM formquestions WHERE questionID =:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iQuestionID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function checkOptionRecord($p_oDB,$p_iQuestionID,$p_sOption,$p_nScore)
    {
        $sSQL = "SELECT COUNT(optionID) AS iCheck FROM questionoptions WHERE questionID=:ID AND questionOption=:option AND optionScore=:score";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iQuestionID);
        $oQuery->bindParam(":option", $p_sOption);
        $oQuery->bindParam(":score", $p_nScore);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function saveOption($p_oDB,$p_iQuestionID,$p_sOption,$p_nScore) {
        $sSQL = "INSERT INTO questionoptions (questionID,questionOption,optionScore) VALUES (:ID,:option,:score)";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iQuestionID);
        $oQuery->bindParam(":option", $p_sOption);
        $oQuery->bindParam(":score", $p_nScore);
        $oQuery->execute();
        
       return $p_oDB->lastInsertID();
    }
    
    public static function changeOptionByID($p_oDB,$p_iOPtionID,$p_sOption,$p_nScore) {
        
        
      $sSQL = "UPDATE questionoptions SET questionOption=:option,optionScore=:score WHERE optionID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iOPtionID);
        $oQuery->bindParam(":option", $p_sOption);
        $oQuery->bindParam(":score", $p_nScore);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
    public static function checkOptionByID($p_oDB,$p_iOptionID)
    {
        $sSQL = "SELECT COUNT(optionID) AS iCheck FROM questionoptions WHERE optionID=:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iOptionID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCheck"] > 0)
        {
            return true;
        }else {
            return false;
        }
    }
    
    public static function deleteOptionByID($p_oDB,$p_iOptionID)
    {
       $sSQL = "DELETE FROM questionoptions WHERE optionID=:ID";
        
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID",$p_iOptionID);
        $oQuery->execute();
        
        
        return $oQuery->rowCount();
    }
    
    
    public static function loadClientsInOverdue($p_oDB)
    {
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID 
                WHERE invitation < CURRENT_DATE - INTERVAL 14 DAY AND clientID IN (SELECT clientID FROM clientforms WHERE status = 'open')";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        return $aResult;
    }
    
    
    public static function search($p_oDB,$p_oCrypt,$p_sColumn, $p_sQuery)
    {
        
        $sQueryCrypt = $p_oCrypt->enCrypt($p_sQuery);
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID  WHERE ".$p_sColumn." =:Query";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":Query",$sQueryCrypt);
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($aResult)) {
            
            if($p_sQuery == ucfirst($p_sQuery))
            {
                //The original was with a capital start
                $p_sQuery = lcfirst ($p_sQuery);
            }else {
                //The original starts with a lowercase char
                $p_sQuery = ucfirst($p_sQuery);
            }
            
            $sQueryCrypt = $p_oCrypt->enCrypt($p_sQuery);
            $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID  WHERE ".$p_sColumn." =:Query";
            $oQuery = $p_oDB->prepare($sSQL);
            $oQuery->bindParam(":Query",$sQueryCrypt);
            $oQuery->execute();
            $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
            
            return $aResult;
            
            
        }else {
            return $aResult;
        }
        
    }
    
    public static function searchCompany($p_oDB,$p_oCrypt, $p_sQuery) {
        
        $sQueryCrypt = $p_oCrypt->enCrypt($p_sQuery);
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID  
                WHERE clients.companyID = (SELECT companys.companyID FROM companys WHERE companyName =:Query)";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":Query",$sQueryCrypt);
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($aResult)) {
            
            if($p_sQuery == ucfirst($p_sQuery))
            {
                //The original was with a capital start
                $p_sQuery = lcfirst ($p_sQuery);
            }else {
                //The original starts with a lowercase char
                $p_sQuery = ucfirst($p_sQuery);
            }
            
            $sQueryCrypt = $p_oCrypt->enCrypt($p_sQuery);
            $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID  
                WHERE clients.companyID = (SELECT companys.companyID FROM companys WHERE companyName =:Query)";
            $oQuery = $p_oDB->prepare($sSQL);
            $oQuery->bindParam(":Query",$sQueryCrypt);
            $oQuery->execute();
            $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
            
            return $aResult;
            
            
        }else {
            return $aResult;
        }
        
        
    }
    
    
    public static function searchNoInvitation($p_oDB) {
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID
                WHERE invitation IS NULL";
        
        $oQuery = $p_oDB->prepare($sSQL);
       
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public static function searchNoFills($p_oDB) {
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID
                WHERE  (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') = 0 AND invitation IS NOT NULL";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public static function searchWithOpen($p_oDB) {
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID
                WHERE  (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') > 0 AND invitation IS NOT NULL";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }
    
    public static function searchNoOpen($p_oDB) {
        $sSQL = "SELECT *,companyName,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') AS formCountOpen,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='pending') AS formCountPending,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='closed') AS formCountClosed,
                (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND (status ='closed' OR status ='pending')) AS lockt
                 FROM clients
                 LEFT JOIN companys ON clients.companyID = companys.companyID
                WHERE  (SELECT COUNT(*) FROM clientforms WHERE clientID = clients.clientID AND status='open') = 0 AND invitation IS NOT NULL";
        
        $oQuery = $p_oDB->prepare($sSQL);
        
        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        return $aResult;
    }

}


