<?php
/**
 *	iquestForm.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 12 sep. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

class surveypanel_models_iquestforms_iquestFormComplex extends surveypanel_models_iquestforms_iquestFormMedium
{
    
    
    
    public function __construct()
    {
       parent::__construct();
       $this->sType = "Complex";
    }
    
    public function getQuestionsSorted($p_bLast = false)
    {
        $aQuestions =  parent::getQuestions();
        $aQuestionsSorted = array();
        $iQuestionNumber = 0;
        //Setting the Database
        $oDB = new BCdatabase_BCDB();
        $oDB->setDBName(DBNAME);
        $oDB->setHost(DBHOST);
        $oDB->setPort(DBPORT);
        $oDB->setUser(DBUSER);
        $oDB->setPass(DBPASS);
        $oDB->connect();
        $aQuestionGroups = surveypanel_models_iquestforms_iquestFormDatamapper::loadQuestionGroups($oDB, $this);
       
        if(!empty($aQuestionGroups))
        {
            foreach($aQuestionGroups AS $aGroup)
            {
                if(!isset($aQuestionsSorted[$aGroup["questionGroupID"]]))
                {
                    $aQuestionsSorted[$aGroup["questionGroupID"]] = array();
                    $aQuestionsSorted[$aGroup["questionGroupID"]]["groupName"] = $aGroup["groupName"];
                    $aQuestionsSorted[$aGroup["questionGroupID"]]["questions"] = array();
                    $aGroupQuestions = surveypanel_models_iquestforms_iquestFormDatamapper::loadGroupQuestions($oDB, $this, $aGroup["questionGroupID"]); 
                    
                    //Firstloop set only the parent questions
                    foreach($aQuestions AS $iKey =>$oQuestion)
                    {
                        if($oQuestion->getParentQuestion() == 0 && in_array($oQuestion->getQuestionID(),$aGroupQuestions)){
                            $aQuestionsSorted[$aGroup["questionGroupID"]]["questions"][$oQuestion->getQuestionID()] = $oQuestion;
                            unset($aQuestions[$iKey]);
                            $iQuestionNumber++;
                        }
                    }
                    
                }
            }
            
           
            return $aQuestionsSorted;
        }else {
            return parent::getQuestionsSorted();
        }
        
       
       
    }
    
   
    
}