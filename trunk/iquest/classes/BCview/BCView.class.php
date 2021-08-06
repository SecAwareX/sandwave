<?php
/**
 *	BCView.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 23 aug. 2018
 *  Project : 
 * 	Package : 
 *  Version : 
 * 
 */

class BCView_BCView
{
    private $sMainTemplate;
    
    private $sModuleMainTemplate;
    
    private $sModuleTemplate;
    
    private $sTitle;
    
    private $aCSSFiles;
    
    private $aJSFiles;
    
    private $aModuleTemplateVars;
    
    public function __construct()
    {
        $this->sMainTemplate = file_get_contents(APPLICATIONREALPATH."..\Templates/mainTemplate.tpl");
        $this->sModuleMainTemplate = "";
        $this->sModuleTemplate = "";
        $this->sTitle = "";
        $this->aCSSFiles = array();
        $this->aJSFiles = array();
        $this->aModuleTemplateVars = array();
    }
    
    
    public function buildScreen($p_sModule,$p_aData,$p_sScreen)
    {
      
       $sScreenBuilderDir =  preg_replace("#classes#", "modules", APPLICATIONREALPATH)."/".$p_sModule."/views/";
       require_once($sScreenBuilderDir."screenBuilder.class.php");
       $sScreenBuilder =  "Modules_".$p_sModule."_screenBuilder";
       $sScreenBuilder::buildScreen($this,$p_sScreen,$p_aData);
       
    }
    
    public function renderModuleView($p_sModule)
    {
        $sTemplateDir =  preg_replace("#classes#", "modules", APPLICATIONREALPATH)."/".$p_sModule."/views/templates/";
        
        //Create module Maintemplate string
        $this->sModuleMainTemplate = file_get_contents($sTemplateDir.$this->sModuleMainTemplate);
        //Create module template string
        $this->sModuleTemplate = file_get_contents($sTemplateDir.$this->sModuleTemplate);
        
        //Combine the two templates
        $this->sModuleMainTemplate = preg_replace("/{moduleContent}/",$this->sModuleTemplate,$this->sModuleMainTemplate);
        
        if(!empty($this->aModuleTemplateVars))
        {
            foreach($this->aModuleTemplateVars AS $sVarName => $mValue)
            {
                $this->sModuleMainTemplate = preg_replace("/{".$sVarName."}/",$mValue,$this->sModuleMainTemplate);
            }
        }
        
        //$this->sModuleTemplate =  $this->sModuleMainTemplate;
       // $this->sModuleTemplate = preg_replace("/{moduleContent}/",$this->sModuleMainTemplate,$this->sModuleTemplate);
    }
    
    public function renderView()
    {
        $this->sMainTemplate = preg_replace("/{screenTitle}/",$this->sTitle,$this->sMainTemplate);
        
        //Adding CSS Files
        if(!empty($this->aCSSFiles))
        {
            $sFiles = "";
            foreach($this->aCSSFiles AS $sFile)
            {
                $sFiles .= "\t";
                $sFiles .= '<link href="'.HOST.APPLICATIONPATH.'layout/'.$sFile.'" rel="stylesheet">';
                $sFiles .= "\r\n";
            }
            
            $this->sMainTemplate = preg_replace("/{CSSFiles}/",$sFiles,$this->sMainTemplate);
            
        }else
            {
                $this->sMainTemplate = preg_replace("/{CSSFiles}/","",$this->sMainTemplate);
            }
            
        //Adding JS files
            if(!empty($this->aJSFiles))
            {
                $sJSFiles = "";
                foreach($this->aJSFiles AS $sFile)
                {
                    $sJSFiles .= "\t";
                    $sJSFiles .= ' <script src="'.HOST.APPLICATIONPATH.'layout/'.$sFile.'"></script>';
                    $sJSFiles .= "\r\n";
                }
                
                $this->sMainTemplate = preg_replace("/{JSFiles}/",$sJSFiles,$this->sMainTemplate);
                
            }else
            {
                $this->sMainTemplate = preg_replace("/{JSFiles}/","",$this->sMainTemplate);
            }
        
       
        $this->sMainTemplate = preg_replace("/{Content}/",$this->sModuleMainTemplate,$this->sMainTemplate);
        echo $this->sMainTemplate;
    }
    
    public function setModuleTemplate($p_sTemplate)
    {
        $this->sModuleTemplate = $p_sTemplate;
    }
    
    public function setModuleMainTemplate($p_sTemplate)
    {
        
        $this->sModuleMainTemplate = $p_sTemplate;
    }
    
    
    public function addModuleTemplateVars($p_sVarName,$p_sVarValue)
    {
        $this->aModuleTemplateVars[$p_sVarName] = $p_sVarValue;
    }
    
    public function setTitle($p_sTitle)
    {
        $this->sTitle = $p_sTitle;
    }
    
    
    public function addCSSFile($p_sFile)
    {
        $this->aCSSFiles[] = $p_sFile;
    }
    
    
    public function addJSFile($p_sFile)
    {
        $this->aJSFiles[] = $p_sFile;
    }
}