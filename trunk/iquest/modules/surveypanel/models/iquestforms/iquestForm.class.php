<?php
/**
 *	iquestForm.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 12 sep. 2018
 *  Project : 
 * 	Package : 
 *  Version : 
 * 
 */

class surveypanel_models_iquestforms_iquestForm
{
    
    private $iFormID;
    
    private $iClientFormID;
    
    private $iActive;
    
    protected $sType;
    
    private $iOwner;
    
    private $sFormName;
    
    private $sFormDescription;
    
    private $sFormInter;
    
    private $aQuestions;
    
    protected $bLockt;
    
    public function __construct()
    {
       $this->iFormID = 0;
       $this->iClientFormID = 0;
       $this->iActive = 1;
       $this->sType = "Simple";
       $this->iOwner = 0;
       $this->sFormName = "";
       $this->sFormDescription = "";
       $this->sFormInter = "";
       $this->aQuestions = array();
       $this->bLockt = false;
    }
    
    public function setFormID($p_iID)
    {
        $this->iFormID = $p_iID;
    }
    
    public function setClientFormID($p_iID)
    {
        $this->iClientFormID = $p_iID;
    }
    
    public function setActive($p_iActive)
    {
        $this->iActive = $p_iActive;
    }
    
    public function setType($p_sType)
    {
        $this->sType = $p_sType;
    }
    
    public function setOwner($p_iOwner)
    {
        $this->iOwner = $p_iOwner;
    }
    
    public function setName($p_sName)
    {
        $this->sFormName = $p_sName; 
    }
    
    public function setDescription($p_sDescription)
    {
        $this->sFormDescription = $p_sDescription; 
    }
    
    public function setInter($p_sInter)
    {
        $this->sFormInter = $p_sInter;
    }
    
    public function addQuestion($p_OQuestion)
    {
        if($p_OQuestion instanceof surveypanel_models_iquestforms_iquestQuestion)
        {
            array_push($this->aQuestions, $p_OQuestion);
        }
    }
    
    public function setLockt($p_bLock)
    {
        if(is_bool($p_bLock))
        {
            $this->bLockt = $p_bLock;
        }
    }
    
    public function getFormID()
    {
        return $this->iFormID;
    }
    
    public function getClientFormID()
    {
        return $this->iClientFormID;
    }
    
    public function getActive()
    {
        return $this->iActive;
    }
    
    public function getOwner()
    {
        return $this->iOwner;
    }
    
    
    public function getName()
    {
        return $this->sFormName;
    }
    
    
    public function getType()
    {
        return $this->sType;
    }
    
    
    public function getDescription($p_bHtml = false)
    {
        if($p_bHtml)
        {
            $this->sFormDescription = preg_replace("#\r\n#","<br />",$this->sFormDescription);
            $this->sFormDescription = preg_replace("#\[b\]#","<strong>",$this->sFormDescription);
            $this->sFormDescription = preg_replace("#\[/b\]#","</strong>",$this->sFormDescription);
            $this->sFormDescription = preg_replace("#\[u\]#","<u>",$this->sFormDescription);
            $this->sFormDescription = preg_replace("#\[/u\]#","</u>",$this->sFormDescription);
        }
        
        return $this->sFormDescription;
    }
    
    
    public function getInter($p_bHtml = false)
    {
        if($p_bHtml)
        {
            $this->sFormInter = preg_replace("#\r\n#","<br />",$this->sFormInter);
            $this->sFormInter = preg_replace("#\[b\]#","<strong>",$this->sFormInter);
            $this->sFormInter = preg_replace("#\[/b\]#","</strong>",$this->sFormInter);
            $this->sFormInter = preg_replace("#\[u\]#","<u>",$this->sFormInter);
            $this->sFormInter = preg_replace("#\[/u\]#","</u>",$this->sFormInter);
        }
        
        return $this->sFormInter;
       
    }
    
    public function getQuestions($p_bLast = false)
    {
        if($p_bLast)
        {
            return end($this->aQuestions);
        }else{
            return $this->aQuestions;
        }
       
    }
    
    public function getData()
    {
        $aData = array();
        
        
        return $aData;
    }
    
    public function getLockt()
    {
        return $this->bLockt;
    }
    
    
    
}