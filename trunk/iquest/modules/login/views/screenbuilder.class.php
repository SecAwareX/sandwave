<?php
/**
 *	screenbuilder.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 23 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class Modules_login_screenBuilder
{
    public static function buildScreen($p_oView,$p_sScreen,$p_oControler)
    {
        //First adding some default CSS
        $p_oView->addCSSFile("vendors/bootstrap/dist/css/bootstrap.min.css");
        $p_oView->addCSSFile("vendors/font-awesome/css/font-awesome.min.css");
        $p_oView->addCSSFile("vendors/nprogress/nprogress.css");
        $p_oView->addCSSFile("vendors/animate.css/animate.min.css");
        $p_oView->addCSSFile("css/custom.css");
        $p_oView->addCSSFile("css/project.css");
        
        //Add JS Files
        $p_oView->addJSFile("vendors/jquery/dist/jquery.min.js");
        $p_oView->addJSFile("vendors/bootstrap/dist/js/bootstrap.min.js");
        $p_oView->addJSFile("js/custom.js");
        $p_oView->addJSFile("js/project.js");
        
        //Add module main template
        $p_oView->setModuleMainTemplate("login_frame.tpl");
        
        switch($p_sScreen)
        {
            case "loginForm":
                $p_oView->setTitle("iQuest | Login Surveypannel");
                $p_oView->setModuleTemplate("loginForm.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                break;
            case "clientLoginForm":
                $p_oView->setTitle("iQuest | Client Login");
                $p_oView->setModuleTemplate("clientLoginForm.tpl");
                $p_oView->addModuleTemplateVars("baseURL",HOST."/".APPLICATIONPATH."iquestclient/");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                break;
            case "addIP":
                $p_oView->setTitle("iQuest | IP adres toegevoegd");
                break;
            case "registerip":
                $p_oView->setTitle("iQuest | IP adres registreren");
                $sTemplate = ($p_oControler->getStatus() == "Ready"?"registerIP":"registerIP_failed");
                $p_oView->addModuleTemplateVars("IP",$_SERVER["REMOTE_ADDR"]);
                $p_oView->setModuleTemplate($sTemplate.".tpl");
                
                
                break;
            case "clientLost":
                $p_oView->setTitle("iQuest | Wachtwoord vergeten");
                $sTemplate = ($p_oControler->getStatus() == "Ready"?"registerIP":"registerIP_failed");
                $p_oView->addModuleTemplateVars("baseURL",HOST."/".APPLICATIONPATH."iquestclient/");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $sTemplate = ($p_oControler->getStatus() == "Ready"?"invitation_succes":"clientLoginForm");
                $p_oView->setModuleTemplate($sTemplate.".tpl");
                
                
                break;
            default : echo "Onbekende pagina";
        }
       
    }
}