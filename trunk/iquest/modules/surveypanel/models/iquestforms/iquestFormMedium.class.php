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

class surveypanel_models_iquestforms_iquestFormMedium extends surveypanel_models_iquestforms_iquestForm
{
    
    
    
    public function __construct()
    {
       parent::__construct();
       $this->sType = "Medium";
       
    }
    
    
    public function getQuestionsSorted($p_bLast = false)
    {
       
        $aQuestions =  parent::getQuestions();
        $iQuestionNumber = 1;
        $aNumberextentions = array("a","b","c","d","e","f","g","h");
        $aSortedQuestions = array();
        
        //Firstloop set only the parent questions
        foreach($aQuestions AS $iKey =>$oQuestion)
        {
            if($oQuestion->getParentQuestion() == 0){
                $aSortedQuestions[$oQuestion->getQuestionID()] = array("questionNumberMain"=>$iQuestionNumber,"questionNumber"=>$iQuestionNumber,"subquestions"=>array(),"oQuestion"=>$oQuestion);
                unset($aQuestions[$iKey]);
                $iQuestionNumber++;
            }
        }
        
        //Attach subquestions 
        foreach($aQuestions AS $iKey =>$oQuestion)
        {
            if($oQuestion->getParentQuestion() != 0)
            {
                if(isset($aSortedQuestions[$oQuestion->getParentQuestion()]["subquestions"]))
                {   
                    // first check number of subquestions 
                    $iSubquestions = count($aSortedQuestions[$oQuestion->getParentQuestion()]["subquestions"]);
                    
                    if($iSubquestions == 0)
                    {
                        $aSortedQuestions[$oQuestion->getParentQuestion()]["questionNumber"] =  $aSortedQuestions[$oQuestion->getParentQuestion()]["questionNumberMain"].".".$aNumberextentions[0];
                        
                        $aSubQuestion = array();
                        $aSubQuestion["questionNumber"] =  $aSortedQuestions[$oQuestion->getParentQuestion()]["questionNumberMain"].".".$aNumberextentions[1];
                        $aSubQuestion["oQuestion"] = $oQuestion;
                        $aSortedQuestions[$oQuestion->getParentQuestion()]["subquestions"][]= $aSubQuestion;
                    }
                    
                    
                }
            }
        }
        
        return $aSortedQuestions;
        
    }
   
    
}