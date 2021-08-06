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

abstract class Modules_error_screenBuilder
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
        //$p_oView->addJSFile("js/project.js");
        
        //Add module main template
        $p_oView->setModuleMainTemplate("error_frame.tpl");
        
        switch($p_sScreen)
        {
            case "accesdenied":
                $p_oView->setTitle("iQuest | Acces Denied");
                $p_oView->setModuleTemplate("accesdenied.tpl");
                $p_oView->addModuleTemplateVars("IPadres",$_SERVER["REMOTE_ADDR"]);
                $p_oView->addModuleTemplateVars("IPadresAccesLink",HOST."/".APPLICATIONPATH."login/registerip");
                break;
            case "accesblockt":
                $p_oView->setTitle("iQuest | Acces Blockt");
                $p_oView->setModuleTemplate("accesblockt.tpl");
                break;
            default : echo "Onbekende pagina";
        }
       
    }
}