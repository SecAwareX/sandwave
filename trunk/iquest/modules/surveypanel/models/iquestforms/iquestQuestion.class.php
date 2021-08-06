<?php
/**
 *	iquestQuestion.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 18 sep. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

class surveypanel_models_iquestforms_iquestQuestion
{
    private $iQuestionID;
    
    private $iQuestionType;
    
    private $aQuestionTypes;
    
    private $sQuestion;
    
    private $aAnwerOptions;
    
    private $iScore;
    
    private $mAnswer;
    
    private $iNumberOfAnswers;
    
    private $iParentQuestion;
    
    private $bLocket;
    
    public function __construct()
    {
        $this->iQuestionID = 0;
        $this->iQuestionType = 0;
        $this->aQuestionTypes = array("1"=>"Korte text (1 regel)","2"=>"Lange text","3"=>"Keuzelijst");
        $this->sQuestion = "";
        $this->aAnwerOptions = array();
        $this->iScore = 0;
        $this->mAnswer = "";
        $this->iNumberOfAnswers = 1;
        $this->iParentQuestion = 0;
        $this->bLocket = false;
    }
    
    public function setQuestionID($p_iQuestionID)
    {
        $this->iQuestionID = $p_iQuestionID; 
    }
    
    public function setParentQuestionID($p_iParentID) {
        $this->iParentQuestion = $p_iParentID;
    }
    
    public function setNumberOfAnswers($p_iAnswers){
        $this->iNumberOfAnswers = $p_iAnswers;
    }
    
    public function setQuestionType($p_iQuestionType)
    {
        $this->iQuestionType = $p_iQuestionType; 
    }
    
    public function setQuestion($p_sQuestion)
    {
        $this->sQuestion = $p_sQuestion;
    }
    
    public function setScore($p_iScore)
    {
        $this->iScore = $p_iScore;
        return $this;
    }
    
    public function setScoreAnswer()
    {
        $this->mAnswer = array_search($this->iScore, $this->aAnwerOptions);
    }
    
    public function setMultiScore($p_iQuestionScore,$p_sAnswer)
    {
        if(!is_array($this->mAnswer))
        {
            $this->mAnswer = array();
        }
        $this->iScore =  1;
        $this->mAnswer[] = array("score"=>$p_iQuestionScore, "answer"=>$p_sAnswer);
        
    }
    
    public function setAnswer($p_sAnswer, $p_bMultiple = false)
    {
        if($p_bMultiple)
        {
            if(!is_array($this->mAnswer))
            {
                $this->mAnswer = array();
            }
            
            $this->mAnswer[] = $p_sAnswer;
            
        }else {
            $this->mAnswer = $p_sAnswer;
        }
       
    }
    
    public function setLockt($p_bLock)
    {
        if(is_bool($p_bLock))
        {
            $this->bLocket = $p_bLock;
        }
    }
    
    public function getLockt()
    {
        return $this->bLocket;
    }
    
    public function addOption($p_sOption,$p_iOptionScore)
    {
        if(!isset($this->aAnwerOptions[$p_sOption]))
        {
            $this->aAnwerOptions[$p_sOption] = $p_iOptionScore;
         }
    }
    
    public function getQuestionID()
    {
        return $this->iQuestionID;
    }
    
    public function getParentQuestion()
    {
        return $this->iParentQuestion;
    }
    
    public function getNumberOfAnswers()
    {
        return $this->iNumberOfAnswers;
    }
    
    public function getQuestionType($p_bTypeString = false)
    {
        if($p_bTypeString)
        {
            return $this->aQuestionTypes[$this->iQuestionType];
        }else {
            return $this->iQuestionType;
        }
        
    }
    
    public function getQuestion()
    {
        return $this->sQuestion;
    }
    
    public function getOptions()
    {
        return $this->aAnwerOptions;
    }
    
    public function getScore()
    {
        return $this->iScore;
    }
    
    public function getAnswer()
    {
        return $this->mAnswer;
    }
    
}