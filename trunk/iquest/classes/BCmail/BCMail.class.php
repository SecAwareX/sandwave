<?php
/**
 *	BCMail.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 29 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
require_once(APPLICATIONREALPATH."PHPMailer-master/PHPMailerAutoload.php");


class BCMail_BCMail extends PHPMailer
{
    public $sMailTemplate;
    
    private $aMailTemplateVars;
    
    public function __construct()
    {
        
        parent::__construct(true);
        $this->sMailTemplate = "";
        $this->aMailTemplateVars = array();
        $this->isSMTP(); 
        $this->Host = "mail259.sohosted.com"; 
        
       
     }
     
     
     public function sendMail($p_sModule,$p_sMail,$p_aData = array())
     {
         $sMailBuilderDir =  preg_replace("#classes#", "modules", APPLICATIONREALPATH)."/".$p_sModule."/mails/";
         require_once($sMailBuilderDir."mailBuilder.class.php");
         $sMailBuilder =  "Modules_".$p_sModule."_mailBuilder";
         $sMailBuilder::buildMail($this,$p_sMail,$p_aData);
       
         //Parse Template
         $this->renderMail($p_sModule);
        
         $this->IsHTML(true);
         return $this->send();
     }
     
     
     public function addMailTemplateVars($p_sVarName,$p_mValue)
     {
         $this->aMailTemplateVars[$p_sVarName] = $p_mValue;
     }
     
     
     private function renderMail($p_sModule)
     {
         $sTemplateDir =  preg_replace("#classes#", "modules", APPLICATIONREALPATH)."/".$p_sModule."/mails/templates/";
         $this->sMailTemplate = file_get_contents($sTemplateDir.$this->sMailTemplate);
         
         if(!empty($this->aMailTemplateVars))
         {
             foreach($this->aMailTemplateVars AS $sVarName => $mValue)
             {
                 $this->sMailTemplate = preg_replace("/{".$sVarName."}/",$mValue,$this->sMailTemplate);
             }
         }
         
         $this->Body  = $this->sMailTemplate;
     }
}