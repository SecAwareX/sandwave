<?php
/**
 *	screenBuilder.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 23 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class Modules_surveypanel_screenBuilder
{
    public static function buildScreen($p_oView,$p_sScreen,$p_oControler)
    {
        //First adding some default CSS
        $p_oView->addCSSFile("/vendors/bootstrap/dist/css/bootstrap.min.css");
        $p_oView->addCSSFile("/vendors/font-awesome/css/font-awesome.min.css");
        $p_oView->addCSSFile("/vendors/nprogress/nprogress.css");
        $p_oView->addCSSFile("/vendors/animate.css/animate.min.css");
        $p_oView->addCSSFile("/css/custom.css");
        $p_oView->addCSSFile("/css/project.css");
        
        //Add JS Files
        $p_oView->addJSFile("vendors/jquery/dist/jquery.min.js");
        $p_oView->addJSFile("vendors/bootstrap/dist/js/bootstrap.min.js");
        $p_oView->addJSFile("vendors/fastclick/lib/fastclick.js");
        $p_oView->addJSFile("vendors/nprogress/nprogress.js");
        $p_oView->addJSFile("vendors/jquery-sparkline/dist/jquery.sparkline.min.js");
        $p_oView->addJSFile("vendors/Chart.js/dist/Chart.min.js");
        $p_oView->addJSFile("vendors/Flot/jquery.flot.js");
        $p_oView->addJSFile("vendors/Flot/jquery.flot.pie.js");
        $p_oView->addJSFile("vendors/Flot/jquery.flot.time.js");
        $p_oView->addJSFile("vendors/Flot/jquery.flot.stack.js");
        $p_oView->addJSFile("vendors/Flot/jquery.flot.resize.js");
        $p_oView->addJSFile("vendors/flot.orderbars/js/jquery.flot.orderBars.js");
        $p_oView->addJSFile("vendors/flot-spline/js/jquery.flot.spline.min.js");
        $p_oView->addJSFile("vendors/flot.curvedlines/curvedLines.js");
        $p_oView->addJSFile("vendors/DateJS/build/date.js");
        $p_oView->addJSFile("vendors/moment/min/moment.min.js");
        $p_oView->addJSFile("vendors/bootstrap-daterangepicker/daterangepicker.js");
        $p_oView->addJSFile("vendors/iCheck/icheck.min.js");
        $p_oView->addJSFile("vendors/select2/dist/js/select2.full.min.js");
        $p_oView->addJSFile("vendors/switchery/dist/switchery.min.js");
        $p_oView->addJSFile("js/custom.js");
        $p_oView->addJSFile("js/project.js");
       
        //Add module main template
        $p_oView->setModuleMainTemplate("surveypanel.tpl");
        
        //Add the application basedir
        $p_oView->addModuleTemplateVars("baseURL",HOST."/".APPLICATIONPATH."surveypanel/");
        
        //MenuActiveNavVars class="current-page"
        $p_oView->addModuleTemplateVars("usersactive","");
        $p_oView->addModuleTemplateVars("companyactive","");
        $p_oView->addModuleTemplateVars("clientactive","");
        $p_oView->addModuleTemplateVars("surveyactive","");
        $p_oView->addModuleTemplateVars("datactive","");
        
        
        switch($p_sScreen)
        {
            case "Dashboard":
                $p_oView->setTitle("iQuest | Dashboard");
                $p_oView->setModuleTemplate("dashboard.tpl");
                $p_oView->addModuleTemplateVars("logoutURL",HOST."/".APPLICATIONPATH."surveypanel/logout");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("clients",self::createClientRows($p_oControler->getData("clients")));
                $p_oView->addModuleTemplateVars("searchForm",self::createSearchFormFields($p_oView,$p_oControler->getData("aFormFields")));
                $p_oView->addModuleTemplateVars("filterName",$p_oControler->getData("sFiltername"));
                break;
            case "userOverview":
                    $p_oView->setTitle("iQuest | Overzicht gebruikers");
                    $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                    $p_oView->addModuleTemplateVars("users",self::createUserRows($p_oControler->getData("users")));
                    $p_oView->setModuleTemplate("useroverview.tpl");
                    
                    //Vars for deleting a user
                    $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                    $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                    break;
            case "addUserForm":
                    $p_oView->setTitle("iQuest | Gebruiker toevoegen");
                    $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                    //FormValues
                    $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                    $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                    $p_oView->addModuleTemplateVars("Field_screenname",$p_oControler->getData("field_screenName"));
                    $p_oView->addModuleTemplateVars("Field_username",$p_oControler->getData("field_userName"));
                    $p_oView->addModuleTemplateVars("Field_pass",$p_oControler->getData("field_pass"));
                    $p_oView->addModuleTemplateVars("checked",$p_oControler->getData("field_doMail"));
                    $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                    $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
                    $p_oView->addModuleTemplateVars("newUserID",($p_oControler->getData("iUserID")!=null?$p_oControler->getData("iUserID"):""));
                   
                    $p_oView->setModuleTemplate("userform.tpl");
                    $p_oView->addModuleTemplateVars("usersactive",'class="current-page"');
            break;
            case "userChangeForm":
                    $p_oView->setTitle("iQuest | Gebruiker wijzigen");
                    $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                    $p_oView->setModuleTemplate("userChangeform.tpl");
                    $p_oView->addModuleTemplateVars("userID",$p_oControler->getData("userID"));
                    $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                    $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                    $p_oView->addModuleTemplateVars("Field_screenname",$p_oControler->getData("field_screenName"));
                    $p_oView->addModuleTemplateVars("Field_username",$p_oControler->getData("field_userName"));
                    $p_oView->addModuleTemplateVars("Field_pass",$p_oControler->getData("field_pass"));
                    $p_oView->addModuleTemplateVars("checked",$p_oControler->getData("field_doMail"));
                    $p_oView->addModuleTemplateVars("usersactive",'class="current-page"');
            break;
            case "companyOverview":
                $p_oView->setTitle("iQuest | Overzicht bedrijven");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("companyoverview.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("companys",self::createCompanyRows($p_oControler->getData("companys")));
                break;
            case "addCompanyForm" :
                $p_oView->setTitle("iQuest | Bedrijf toevoegen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("companyform.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
                $p_oView->addModuleTemplateVars("Field_CompanyName",$p_oControler->getData("f_CompanyName"));
                $p_oView->addModuleTemplateVars("newCompanyID",$p_oControler->getData("ID"));
                
                $p_oView->addModuleTemplateVars("companyactive",'class="current-page"');
                 break;
            case "changeCompanyForm" :
                $p_oView->setTitle("iQuest | Bedrijf toevoegen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("companychangeform.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
                $p_oView->addModuleTemplateVars("Field_CompanyName",$p_oControler->getData("f_CompanyName"));
                $p_oView->addModuleTemplateVars("CompanyID",$p_oControler->getData("CompanyID"));
                $p_oView->addModuleTemplateVars("companyactive",'class="current-page"');
                break;
            case "clientOverview":
                $p_oView->setTitle("iQuest | Overzicht clienten");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("clientoverview.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("clients",self::createClientRows($p_oControler->getData("clients")));
                break;
            case "clientForm" :
                $p_oView->setTitle("iQuest | Client toevoegen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("clientform.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("iquestForms",self::createIquestFormRows($p_oControler->getData("iquestForms"),$p_oControler->getData("clientFormFields")));
                $p_oView->addModuleTemplateVars("companys",self::createCompanyFieldRows($p_oControler->getData("companys"),$p_oControler->getData("clientFormFields")));
                $p_oView->addModuleTemplateVars("companysQuestions",self::createCompanyQuestionRows($p_oControler->getData("companysQuestions"),$p_oControler->getData("clientFormFields")));
                self::fillFormFields($p_oView,$p_oControler->getData("clientFormFields"));
                
                $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
               
                $p_oView->addModuleTemplateVars("newClientID",$p_oControler->getData("newClientID"));
                
                $p_oView->addModuleTemplateVars("clientactive",'class="current-page"');
                break;
            case "changeClientForm" :
                $p_oView->setTitle("iQuest | Client wijzigen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("clientchangeform.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
                $p_oView->addModuleTemplateVars("iquestForms",self::createIquestFormRows($p_oControler->getData("iquestForms"),$p_oControler->getData("clientFormFields")));
                $p_oView->addModuleTemplateVars("companys",self::createCompanyFieldRows($p_oControler->getData("companys"),$p_oControler->getData("clientFormFields")));
                $p_oView->addModuleTemplateVars("companysQuestions",self::createCompanyQuestionRows($p_oControler->getData("companysQuestions"),$p_oControler->getData("clientFormFields")));
                self::fillFormFields($p_oView,$p_oControler->getData("clientFormFields"));
                $p_oView->addModuleTemplateVars("ClientID",$p_oControler->getData("ClientID"));
                
                $p_oView->addModuleTemplateVars("clientactive",'class="current-page"');
                break;
            case "client" :
                $p_oView->setTitle("iQuest | Client Dossier");
                $p_oView->setModuleTemplate("clientchow.tpl");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("name",$p_oControler->getData("clientName"));
                $p_oView->addModuleTemplateVars("clientData",self::createClientFile($p_oControler->getData("clientData"),$p_oControler->getData("clientForms")));
                $p_oView->addModuleTemplateVars("clientactive",'class="current-page"');
                
                if($_SERVER["HTTP_REFERER"] == "https://iquest.mareis.nl/surveypanel/")
                {
                    $p_oView->addModuleTemplateVars("targeturl",'https://iquest.mareis.nl/surveypanel/');
                }else {
                    $p_oView->addModuleTemplateVars("targeturl",'https://iquest.mareis.nl/surveypanel/clients');
                }
            break;
            case "showformresult" :
                $p_oView->setTitle("iQuest | Client formulier resultaat");
                $p_oView->setModuleTemplate("formresult.tpl");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->addModuleTemplateVars("formName",(is_object($p_oControler->getData("clientForm"))?$p_oControler->getData("clientForm")->getName():""));
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("name",$p_oControler->getData("clientName"));
                $p_oView->addModuleTemplateVars("clientID",$p_oControler->getData("clientID"));
                $p_oView->addModuleTemplateVars("clientData",self::createFormResult($p_oControler->getData("clientData"),$p_oControler->getData("clientForm"),$p_oControler->getData("formScore")));
                $p_oView->addModuleTemplateVars("clientactive",'class="current-page"');
                break;
            case "formOverview":
                $p_oView->setTitle("iQuest | Overzicht Vragenlijsten");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("formoverview.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("forms",self::createFormRows($p_oControler->getData("forms")));
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
                break;
            case "addForm" :
                $p_oView->setTitle("iQuest | Vragenlijst toevoegen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->setModuleTemplate("addform.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("companys",self::createCompanyFieldRows($p_oControler->getData("companys"),$p_oControler->getData("formFields")));
                
                $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
                $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                $p_oView->addModuleTemplateVars("step2_dis",$p_oControler->getData("submit_step2_dis"));
                $p_oView->addModuleTemplateVars("destination",$p_oControler->getData("destination"));
                self::fillFormFieldsStep1Fields($p_oView,$p_oControler->getData("formFields"));
                
                $p_oView->addModuleTemplateVars("newFormID",$p_oControler->getData("formID"));
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
                break;
            case "changeForm" :
                $p_oView->addJSFile("js/questionScoreForms.js");
                $p_oView->setTitle("iQuest | Vragenlijst wijzigen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                //Set the current score template
                if($p_oControler->getData("oQuestForm")->getType() == "Simple")
                {
                    $p_oView->setModuleTemplate("changeform.tpl");
                }elseif($p_oControler->getData("oQuestForm")->getType() == "Medium") {
                    $p_oView->setModuleTemplate("changeformmedium.tpl");
                    $p_oView->addModuleTemplateVars("scoreGroups",self::createScoreGroupRows($p_oControler->getData("scoreGroups"),$p_oControler->getData("oQuestForm")));
                }elseif($p_oControler->getData("oQuestForm")->getType() == "Complex") {
                    $p_oView->setModuleTemplate("changeformcomplex.tpl");
                }
                
                
                //The default (Simple vars)
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("companys",self::createCompanyFieldRows($p_oControler->getData("companys"),$p_oControler->getData("formFields")));
                
                $p_oView->addModuleTemplateVars("doAdd_dis",$p_oControler->getData("submit_add_dis"));
                $p_oView->addModuleTemplateVars("change_dis",$p_oControler->getData("submit_change_dis"));
                $p_oView->addModuleTemplateVars("step2_dis",$p_oControler->getData("submit_step2_dis"));
                $p_oView->addModuleTemplateVars("FormID",$p_oControler->getData("formID"));
                $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
                $p_oView->addModuleTemplateVars("questions",self::createQuestionRows($p_oControler->getData("oQuestForm")));
                $p_oView->addModuleTemplateVars("scores",self::createScoreRows($p_oControler->getData("scores"),$p_oControler->getData("oQuestForm")));
                $p_oView->addModuleTemplateVars("formIntro",$p_oControler->getData("oQuestForm")->getDescription(true));
                $p_oView->addModuleTemplateVars("surveyForm",self::createSurveyFormPreview($p_oControler->getData("oQuestForm")));
                
                $sTab = "";
                if(isset($_SESSION["currentTab"]))
                {
                    $sTab = $_SESSION["currentTab"];
                    unset($_SESSION["currentTab"]);
                }
                $p_oView->addModuleTemplateVars("currentTab",$sTab);
                
                
                self::fillFormFieldsStep1Fields($p_oView,$p_oControler->getData("formFields"));
                
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
                
                break;
            case "addQuestion" :
                $p_oView->addJSFile("js/questionScoreForms.js");
                $p_oView->setTitle("iQuest | Vraag toevoegen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                switch($p_oControler->getData("oQuestForm")->getType()) {
                    
                    case "Simple" :
                        $p_oView->setModuleTemplate("addquestion.tpl");
                        self::fillFormFieldsAddQuestion($p_oView,$p_oControler->getData("formFields"));
                    break;
                    case "Medium" :
                        $p_oView->setModuleTemplate("addquestionmedium.tpl");
                        self::fillFormFieldsAddQuestion($p_oView,$p_oControler->getData("formFields"),$p_oControler->getData("parentQuestions"));
                    break;
                    case "Complex" : 
                        $p_oView->setModuleTemplate("addquestioncomplex.tpl");
                        self::fillFormFieldsAddQuestion($p_oView,$p_oControler->getData("formFields"),$p_oControler->getData("parentQuestions"),$p_oControler->getData("extraFields"));
                    break;
                }
               
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("formID",$p_oControler->getData("oQuestForm")->getFormID());
                $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
            break;
            case "changeQuestion" :
                $p_oView->addJSFile("js/questionScoreForms.js");
                $p_oView->setTitle("iQuest | Vraag wijzigen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                if($p_oControler->getData("oQuestForm")->getType() == "Simple") {
                    $p_oView->setModuleTemplate("changequestion.tpl");
                    self::fillFormFieldsChangeQuestion($p_oView, $p_oControler->getData("aQuestion"));
                }elseif($p_oControler->getData("oQuestForm")->getType() == "Medium"){
                    $p_oView->setModuleTemplate("changequestionmedium.tpl");
                    self::fillFormFieldsChangeQuestion($p_oView, $p_oControler->getData("aQuestion"));
                }elseif($p_oControler->getData("oQuestForm")->getType() == "Complex") {
                    $p_oView->setModuleTemplate("changequestioncomplex.tpl");
                    self::fillFormFieldsChangeQuestion($p_oView, $p_oControler->getData("aQuestion"));
                }
                
               $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
               $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
               $p_oView->addModuleTemplateVars("formID",$p_oControler->getData("oQuestForm")->getFormID());
               $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
               $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
                break;
            case "addScore" :
                $p_oView->addJSFile("js/questionScoreForms.js");
                $p_oView->setTitle("iQuest | Score toevoegen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                
                $p_oView->setModuleTemplate("addscore.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("formID",$p_oControler->getData("oQuestForm")->getFormID());
                $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
                $p_oView->addModuleTemplateVars("f_scoreDesription",$p_oControler->getData("f_scoreDesription"));
                $p_oView->addModuleTemplateVars("f_scoreLow",$p_oControler->getData("f_scoreLow"));
                $p_oView->addModuleTemplateVars("f_scoreHigh",$p_oControler->getData("f_scoreHigh"));
                $p_oView->addModuleTemplateVars("options",self::createComparisonOptions($p_oControler->getData("comparison")));
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
                break;
            case "changeScore" :
                $p_oView->addJSFile("js/questionScoreForms.js");
                $p_oView->setTitle("iQuest | Score wijzigen");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                
                $p_oView->setModuleTemplate("changescore.tpl");
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("formID",$p_oControler->getData("oQuestForm")->getFormID());
                $p_oView->addModuleTemplateVars("scoreID",$p_oControler->getData("scoreID"));
                $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
                $p_oView->addModuleTemplateVars("f_scoreDesription",$p_oControler->getData("f_scoreDesription"));
                $p_oView->addModuleTemplateVars("f_scoreLow",$p_oControler->getData("f_scoreLow"));
                $p_oView->addModuleTemplateVars("f_scoreHigh",$p_oControler->getData("f_scoreHigh"));
                $p_oView->addModuleTemplateVars("options",self::createComparisonOptions($p_oControler->getData("comparison")));
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
            break;
                
            case "addScoreGroup" :
                $p_oView->setTitle("iQuest | Scoregroep toevoegen");
                $p_oView->setModuleTemplate("addscoregroup.tpl");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
                $p_oView->addModuleTemplateVars("formID",$p_oControler->getData("oQuestForm")->getFormID());
                $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
                $p_oView->addModuleTemplateVars("f_scoreGroup",$p_oControler->getData("f_scoreGroup"));
                $p_oView->addModuleTemplateVars("f_groupStartRange",$p_oControler->getData("f_groupStartRange"));
                $p_oView->addModuleTemplateVars("f_groupEndRange",$p_oControler->getData("f_groupEndRange"));
                $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
           break;
           case "changeScoreGroup": 
               $p_oView->setTitle("iQuest | Scoregroep wijzigen");
               $p_oView->setModuleTemplate("changescoregroup.tpl");
               $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
               $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
               $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSuccesMessage"));
               $p_oView->addModuleTemplateVars("formID",$p_oControler->getData("oQuestForm")->getFormID());
               $p_oView->addModuleTemplateVars("groupID",$p_oControler->getData("iGroupID"));
               $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("oQuestForm")->getName());
               $p_oView->addModuleTemplateVars("f_scoreGroup",$p_oControler->getData("f_scoreGroup"));
               $p_oView->addModuleTemplateVars("f_groupStartRange",$p_oControler->getData("f_groupStartRange"));
               $p_oView->addModuleTemplateVars("f_groupEndRange",$p_oControler->getData("f_groupEndRange"));
               $p_oView->addModuleTemplateVars("surveyactive",'class="current-page"');
           break;
                
            
            default : echo "Onbekende pagina";
        }
       
    }
    
    
    public static function createSearchFormFields($p_oView,$aFormFields) {
        $p_oView->addModuleTemplateVars("f_serachQuery",$aFormFields["f_serachQuery"]);
        
        $sSearchTypeHTML = "";
        $aSearchTypes = array("Maak een keuze","Voornaam","Achternaam","email","Bedrijf","Status");
        foreach($aSearchTypes AS $iKey => $sType){
            
            $sSelected = ($iKey == $aFormFields["f_searchType"]?"selected":"");
            $sSearchTypeHTML .= '<option value="'.$iKey.'" '.$sSelected.'>'.$sType.'</option>';
        }
        $p_oView->addModuleTemplateVars("searchTypes",$sSearchTypeHTML);
        
        $sSearchStatusHTML = "";
        $aSearchStatussen  = array("Maak een keuze","Overdue","Nog niet uitgenodigd","Uitgenodigd maar nog niks ingevuld","Met open formulieren","Alles gesloten");
        foreach($aSearchStatussen AS $iKey => $sStatus){
            
            $sSelected = ($iKey == $aFormFields["f_searchStatus"]?"selected":"");
            $sSearchStatusHTML .= '<option value="'.$iKey.'" '.$sSelected.'>'.$sStatus.'</option>';
        }
        $p_oView->addModuleTemplateVars("searchStatus",$sSearchStatusHTML);
    }
    
    public static function createClientFile($p_aClient,$p_aClientForms)
    {
        $sHtml = "";
        $sHtml .= "<table>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Geslacht : </td><td>&nbsp;".($p_aClient["gender"] == "female"?"vrouw":"man")."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Naam : </td><td>&nbsp;".$p_aClient["firstName"]." ".$p_aClient["lastName"]."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Geboorte datum : </td><td>&nbsp;".$p_aClient["dateOfBirth"]."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td>&nbsp;</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>E-mail : </td><td>&nbsp;".$p_aClient["email"]."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Volgende afspraak : </td><td>&nbsp;".BChelpers_converters::convertDate("view",$p_aClient["appointment"])."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Uitgenodigd op : </td><td>&nbsp;".BChelpers_converters::convertDate("view",$p_aClient["invitation"])."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "</table>";
        $sHtml .= '<br><br><a class="btn btn-primary" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeclient/'.$p_aClient["clientID"].'" role="button">Client wijzigen</a>';
        $sHtml .= "<hr />";
        
        $sHtml .= ' <table class="table table-striped">';
        $sHtml .= ' <thead>';
        $sHtml .= ' <tr>';
        $sHtml .= ' <th scope="col">Vragenlijstnaam</th>';
        $sHtml .= '<th scope="col">Toegevoegd op</th>';
        $sHtml .= '<th scope="col">Status</th>';
        $sHtml .= ' <th scope="col"></th>';
        $sHtml .= '</tr>';
        $sHtml .= '</thead>';
        $sHtml .= '<tbody>';
        
        if(!empty($p_aClientForms))
        {
            foreach($p_aClientForms AS $aForm)
            {
                $sHtml .= ' <tr>';
                $sHtml .= ' <td>'.$aForm["formName"].'</td><td>'.$aForm["creationDate"].'</td>';
                $sHtml .= '<td>';
                if($aForm["status"] == "closed")
                {
                    $sHtml .= '<span class="label label-success">Voltooid</span>';
                }else 
                    {
                        $sHtml .= '<span class="label label-danger">Openstaand</span>';
                    }
                
                    $sHtml .= '<td><a href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/showformresult/'.$aForm["clientID"].'/'.$aForm["clientFormID"].'">Bekijk formulier</td>';
                $sHtml .= '</td>';
                $sHtml .= ' </tr>';
            }
        }else {
            $sHtml .= '<tr class="errorrow"><td>Nog geen formulieren toegewezen aan client</td></tr>';
        }
        
        
        $sHtml .= ' </tbody>';
        $sHtml .= '</table>';
        
        return $sHtml;
    }
    
    public static function createFormResult($p_aClient,$p_aClientForm, $p_aFormScore) 
    {
        
        $sHtml = "";
        $sHtml .= "<table>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Geslacht : </td><td>&nbsp;".($p_aClient["gender"] == "female"?"vrouw":"man")."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Naam : </td><td>&nbsp;".$p_aClient["firstName"]." ".$p_aClient["lastName"]."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Geboorte datum : </td><td>&nbsp;".$p_aClient["dateOfBirth"]."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td>&nbsp;</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>E-mail : </td><td>&nbsp;".$p_aClient["email"]."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Volgende afspraak : </td><td>&nbsp;".BChelpers_converters::convertDate("view",$p_aClient["appointment"])."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "<tr>";
        $sHtml .= "<td><strong>Uitgenodigd op : </td><td>&nbsp;".BChelpers_converters::convertDate("view",$p_aClient["invitation"])."</td>";
        $sHtml .= "</tr>";
        $sHtml .= "</table>";
        $sHtml .= '<br><br><a class="btn btn-primary" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeclient/'.$p_aClient["clientID"].'" role="button">Client wijzigen</a>';
        $sHtml .= "<hr />";
        
        $sHtml .= '<div id=scoreContainer><h1>Score</h1><button id="interBut" type="button" class="btn btn-primary">interpretatie</button>';
        $sHtml .= "<h2>".$p_aFormScore["score"]."</h2>";
        if(isset($p_aFormScore["scoreDescription"])) {
            $sHtml .= $p_aFormScore["scoreDescription"];
        }
        $sHtml .= "</div>";
        
        
        $sHtml .= '<h1>'.(is_object($p_aClientForm)?$p_aClientForm->getName():"").'</h1>';
        
        if(is_object($p_aClientForm)){
            $sHtml .= $p_aClientForm->getDescription(true).'<br><br>';
            
            $sHtml .= '<div id="inter" class="hidden">'.$p_aClientForm->getInter(true).'</div>';
            
            $aQuestions = $p_aClientForm->getQuestions();
        }else {
            $sHtml .= "";
            $aQuestions = array();
        }
        
        foreach($aQuestions AS $iKey => $oQuestion)
        {
            $sHtml .= '<strong>'.($iKey+1).'&nbsp;&nbsp;'.$oQuestion->getQuestion().'<strong><br>';
            
            //There two questions 131 & 132 the have choices but no score, zo we exlude these
            if($oQuestion->getQuestionID() == 131 || $oQuestion->getQuestionID() == 132)
            {
                $sHtml .= '<strong>Score : </strong>n.v.t.<br>';
            }else {
                
                //Some questions have the same score, so we have renamed them to a score of 22, but this must be 2 
                //This only in 4dkl form 
                $sHtml .= '<strong>Score : </strong>'.($oQuestion->getscore() == 22?"2":$oQuestion->getscore()).'<br>';
            }
            
            
            
            if(is_array($oQuestion->getAnswer()))
            {
                $aAnswers = $oQuestion->getAnswer();
                $sHtml .= '<strong>Antwoorden :</strong><br>';
              
                foreach($aAnswers AS $aAnswer)
                {
                    $sHtml .= '<li>'.$aAnswer["answer"].'</li>';
                }
                
                $sHtml .= '</ul><br>';
                
            }else {
                $sHtml .= '<strong>Antwoord :</strong> '.$oQuestion->getAnswer().'<br><br>';
            }
            
            
        }
        
        return $sHtml;
    }
    
    
    public function createFormRows($p_mForms)
    {
        if(is_array($p_mForms))
        {
            $sHtml = "";
            foreach($p_mForms AS $oForm)
            {
                $sHtml .="<tr>";
                $sHtml .= "<td>".$oForm->getFormID()."</td>";
                $sHtml .= "<td>".$oForm->getName()."</td>";
                $sHtml .= "<td>".$oForm->getOwner()."</td>";
                $sHtml .= "<td>".$oForm->getType()."</td>";
                
                //Check if visability is changeable 
                
                if($oForm->getActive() == 1)
                {
                    if($oForm->getFormID() == 20 || $oForm->getFormID() == 25 || $oForm->getFormID() == 26) {
                         $sLockt = " blockt";
                    }else {
                        $sLockt = "";
                    }
                    //Form is active
                    $sHtml .= '<td><a class="editLink change'.$sLockt.'" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeformstatus/'.$oForm->getFormID().'/0">Ja</a></td>';
                }else
                {
                    //Form is not active
                    $sHtml .= '<td><a class="editLink delete" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeformstatus/'.$oForm->getFormID().'/1">Nee</a></td>';
                }
                
                $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeform/'.$oForm->getFormID().'">Wijzigen</a></td>';
               
                
                if($oForm->getFormID() == 20 || $oForm->getFormID() == 25 || $oForm->getFormID() == 26 ||  $oForm->getLockt()) {
                    $sLockt = " blockt";
                }else {
                    $sLockt = " delete";
                }
                $sHtml .= '<td><a class="editLink'.$sLockt.'" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deleteform/'.$oForm->getFormID().'">Verwijderen</a></td>';
                $sHtml .="</tr>";
            }
            
            return $sHtml;
        }else{
            return '<tr class="errorrow"><td><br />'.$p_mForms.'</td></tr>';
        }
    }
    
    
    public static function createUserRows($p_mUsers)
    {
        if(is_array($p_mUsers))
        {
            $iNumberOfUsers = count($p_mUsers);
            $sHtml = "";
            foreach($p_mUsers AS $aUser)
            {
                $sHtml .="<tr>";
                    $sHtml .= "<td>".$aUser["userID"]."</td>";
                    $sHtml .= "<td>".$aUser["userScreenname"]."</td>";
                    $sHtml .= "<td>".$aUser["userName"]."</td>";
                    $sHtml .= "<td>********</td>";
                    $sHtml .= "<td>".$aUser["userLevel"]."</td>";
                    $sHtml .= "<td>".$aUser["userBlock"]."</td>";
                    $sHtml .= "<td>".$aUser["userSessions"]."</td>";
                    $sHtml .= "<td>".$aUser["lastLogin"]."</td>";
                    $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeuser/'.$aUser["userID"].'">Wijzigen</a></td>';
                    
                    if($aUser["userID"] == $_SESSION["userID"])
                    {
                        $sDeleteLinkClass ="blockt";
                    }else{
                        $sDeleteLinkClass ="delete";
                    }
                    $sHtml .= '<td><a class="editLink '.$sDeleteLinkClass.'" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deleteuser/'.$aUser["userID"].'">Verwijder</a></td>';
                $sHtml .="</tr>";
            }
            
            return $sHtml;
        } else {
            return '<tr class="errorrow"><td><br />'.$p_mUsers.'</td></tr>';
        }
    }
    
    public static function createCompanyRows($p_mCompanys)
    {
        if(is_array($p_mCompanys))
        {
            $sHtml = "";
            foreach($p_mCompanys AS $aCompany)
            {
                $sHtml .="<tr>";
                $sHtml .= "<td>".$aCompany["companyID"]."</td>";
                $sHtml .= "<td>".$aCompany["companyName"]."</td>";
                $sHtml .= "<td>".$aCompany["clientCount"]."</td>";
                $sHtml .= "<td>".$aCompany["formCount"]."</td>";
                
                if($aCompany["active"] == 1)
                {
                    //Company is active
                    $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changecompanystatus/'.$aCompany["companyID"].'/0">Ja</a></td>';
                }else
                    {
                        //Company is not active
                        $sHtml .= '<td><a class="editLink delete" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changecompanystatus/'.$aCompany["companyID"].'/1">Nee</a></td>';
                    }
                
                
                
                $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changecompany/'.$aCompany["companyID"].'">Wijzigen</a></td>';
                $sHtml .="</tr>";
            }
            
            return $sHtml;
        } else {
            return '<tr class="errorrow"><td><br />'.$p_mCompanys.'</td></tr>';
        }
    }
    
    public function createClientRows($p_mClients)
    {
        if(is_array($p_mClients))
        {
            $sHtml = "";
            foreach($p_mClients AS $aClient)
            {
                $sHtml .="<tr>";
                $sHtml .= "<td>".$aClient["clientID"]."</td>";
                $sHtml .= '<td><a href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/showclient/'.$aClient["clientID"].'">'.$aClient["firstName"].' '.$aClient["lastName"].'</a></td>';
                $sHtml .= '<td><a href="mailto:'.rtrim($aClient["email"]).'">'.rtrim($aClient["email"]).'</a></td>';
                $sHtml .= "<td>".$aClient["companyName"]."</td>";
                $sHtml .= "<td>".$aClient["formCountOpen"]."</td>";
                $sHtml .= "<td>".$aClient["formCountPending"]."</td>";
                $sHtml .= "<td>".$aClient["formCountClosed"]."</td>";
                //$sHtml .= "<td>".$aClient["invitation"]."</td>";
                if($aClient["invitation"] == "")
                {
                    $sHtml .= '<td><a href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/sendinvitation/'.$aClient["clientID"].'"><span class="label label-danger">Uitnodigen</span></a></td>';
                }else
                    {
                        $sHtml .= '<td><a href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/resendinvitation/'.$aClient["clientID"].'"><span class="label label-success">'.BChelpers_converters::convertDate("view",$aClient["invitation"]).'</span></a></td>';
                    }
                    
                    
                    
                $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changeclient/'.$aClient["clientID"].'">Wijzigen</a></td>';
                if($aClient["lockt"] == "0")
                {
                    $sHtml .= '<td><a class="editLink delete" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deleteclient/'.$aClient["clientID"].'">Verwijderen</a></td>';
                }else {
                    $sHtml .= '<td><a class="editLink blockt" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deleteclient/'.$aClient["clientID"].'">Verwijderen</a></td>';
                }
                    
                $sHtml .= "</tr>";
            }
            
            
            return $sHtml;
        }else{
            return '<tr class="errorrow"><td><br />'.$p_mClients.'</td></tr>';
        }
    }
    
    
    public static function createIquestFormRows($p_mForms,$p_aFormFields = array())
    {   
        if(is_array($p_mForms))
        {
            $sHtml = "";
            foreach($p_mForms AS $aFormCheckBox)
            {
                $sHtml .='<div class="form-group">';
                 $sHtml .='<div class="col-md-6 col-sm-6 col-xs-12">';
                 $sHtml .='<div id="form_'.$aFormCheckBox["formID"].'" class="btn-group" data-toggle="buttons">';
                 
                 $sClassActiveON = ($p_aFormFields["forms"]["form_".$aFormCheckBox["formID"]] == "on"?"active":"");
                 $sChekedON = ($p_aFormFields["forms"]["form_".$aFormCheckBox["formID"]] == "on"?"checked":"");
                 
                 $sHtml .='<label class="btn btn-default '.$sClassActiveON.'" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">';
                 $sHtml .=' <input type="radio" name="form_'.$aFormCheckBox["formID"].'" value="on" '.$sChekedON.'> &nbsp; Aan &nbsp;';
                 $sHtml .='</label>';
                 
                 $sClassActiveOFF = ($p_aFormFields["forms"]["form_".$aFormCheckBox["formID"]] == "off"?"active":"");
                 $sChekedOFF = ($p_aFormFields["forms"]["form_".$aFormCheckBox["formID"]] == "off"?"checked":"");
                 
                 $sHtml .=' <label class="btn btn-default '.$sClassActiveOFF.'" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">';
                 $sHtml .='<input type="radio" name="form_'.$aFormCheckBox["formID"].'" value="off" '.$sChekedOFF.'> Uit';
                 $sHtml .='</label>';
                 $sHtml .='</div><label class="control-label right">&nbsp;&nbsp;&nbsp;'.$aFormCheckBox["formName"].'</label>';
                 $sHtml .='</div>';
                 $sHtml .='</div>';
                 $sHtml .="\r\n";
            }
            
            
            return $sHtml;
        }else{
            return 'Geen forms gevonden';
        }
    }
    
    
    public static function createCompanyFieldRows($p_mCompanys,$p_aFormFields = array())
    {  
        if(is_array($p_mCompanys))
        {
            $sHtml = "";
            $aSandbox = array();
            
            foreach($p_mCompanys AS $aCompany)
            {
                if(!in_array($aCompany["companyID"], $aSandbox))
                {
                    $sSelected = ($p_aFormFields["companyIDSelected"] == $aCompany["companyID"]?'selected="selected"':"");
                    $sHtml .='<option  value="'.$aCompany["companyID"].'" '.$sSelected.'>'.$aCompany["companyName"].'</option>';
                    
                    array_push($aSandbox, $aCompany["companyID"]);
                }
            }
            
            return $sHtml;
        }
    }
    
    public static function createCompanyQuestionRows($p_mCompanys,$p_aFormFields = array())
    {
        if(is_array($p_mCompanys))
        {
            $sHtml = "";
            foreach($p_mCompanys AS $aCompany)
            {
                $sHtml .='<div class="companyQuestions" id="CQuestions_'.$aCompany["companyID"].'">';
               
                $sHtml .= self::createIquestFormRows($aCompany["forms"],$p_aFormFields);
                
                $sHtml .='</div>';
                $sHtml .="\r\n";
            }
            
            return $sHtml;
        }
    }
    
    
    public static function fillFormFields($p_oView,$p_aFormFields)
    {
        foreach($p_aFormFields AS $sFieldName => $sFieldValue)
        {
            if($sFieldName == "gender")
            {
                if($sFieldValue == "male")
                {
                    $p_oView->addModuleTemplateVars("maleActive","active");
                    $p_oView->addModuleTemplateVars("malechecked","checked");
                    $p_oView->addModuleTemplateVars("femaleActive","");
                    $p_oView->addModuleTemplateVars("femalechecked","");
                }elseif($sFieldValue == "female")
                    {
                        $p_oView->addModuleTemplateVars("maleActive","");
                        $p_oView->addModuleTemplateVars("femaleActive","active");
                        $p_oView->addModuleTemplateVars("femalechecked","checked");
                        $p_oView->addModuleTemplateVars("malechecked","");
                }else{
                    $p_oView->addModuleTemplateVars("femaleActive","");
                    $p_oView->addModuleTemplateVars("femalechecked","");
                    $p_oView->addModuleTemplateVars("maleActive","");
                    $p_oView->addModuleTemplateVars("malechecked","");
                }
            }elseif(!is_array($sFieldValue)) 
                {
                    $p_oView->addModuleTemplateVars($sFieldName,$sFieldValue);
                   
                }
        }
    }
    
    
    public function fillFormFieldsStep1Fields($p_oView,$p_aFormFields)
    {
        foreach($p_aFormFields AS $sFieldName => $sFieldValue)
        {
            if($sFieldName == "complex")
            {
                if($sFieldValue == "simple")
                {
                    $p_oView->addModuleTemplateVars("simpleActive","active");
                    $p_oView->addModuleTemplateVars("simplechecked","checked");
                    $p_oView->addModuleTemplateVars("mediumActive","");
                    $p_oView->addModuleTemplateVars("mediumchecked","");
                    $p_oView->addModuleTemplateVars("complexActive","");
                    $p_oView->addModuleTemplateVars("complexchecked","");
                }elseif($sFieldValue == "medium")
                    {
                        $p_oView->addModuleTemplateVars("mediumActive","active");
                        $p_oView->addModuleTemplateVars("mediumchecked","checked");
                        $p_oView->addModuleTemplateVars("simpleActive","");
                        $p_oView->addModuleTemplateVars("simplechecked","");
                        $p_oView->addModuleTemplateVars("complexActive","");
                        $p_oView->addModuleTemplateVars("complexchecked","");
                }elseif($sFieldValue == "complex")
                        {
                            $p_oView->addModuleTemplateVars("complexActive","active");
                            $p_oView->addModuleTemplateVars("complexchecked","checked");
                            $p_oView->addModuleTemplateVars("simpleActive","");
                            $p_oView->addModuleTemplateVars("simplechecked","");
                            $p_oView->addModuleTemplateVars("mediumActive","");
                            $p_oView->addModuleTemplateVars("mediumchecked","");
                        }
            }elseif(!is_array($sFieldValue))
            {
                $p_oView->addModuleTemplateVars($sFieldName,$sFieldValue);
                
            }
        }
    }
    
    public static function fillFormFieldsChangeQuestion($p_oView,$p_aQuestion) {
        
        $p_oView->addModuleTemplateVars("questionID",$p_aQuestion["questionID"]);
        $p_oView->addModuleTemplateVars("f_question",$p_aQuestion["question"]);
        $p_oView->addModuleTemplateVars("f_possibleAnswers",$p_aQuestion["answers"]);
        $p_oView->addModuleTemplateVars("f_questionGroupNew",$p_aQuestion["f_questionGroupNew"]);
        
        if(isset($p_aQuestion["aQuestionGroups"])) {
            $sHtml = "";
            if(empty($p_aQuestion["aQuestionGroups"])){
                $sHtml .='<option  value="0">Er zijn nog geen vraag groepen om uit te kiezen</option>';
            } else {
               
                $sHtml .='<option  value="0">Kies een vraaggroep indien van toepassing</option>';
                foreach($p_aQuestion["aQuestionGroups"] AS $iKey => $aGroup)
                {
                    $sSelected = ($aGroup["questionGroupID"] == $p_aQuestion["questionGroupID"]?'selected="selected"':"");
                    $sHtml .='<option  value="'.$aGroup["questionGroupID"].'" '.$sSelected.'>'.$aGroup["groupName"].'</option>';
                }
            }
            
            $p_oView->addModuleTemplateVars("questionGroups",$sHtml);
        }
        
        
        $aOptions = array("0"=>"Kies een type antwoord","1"=>"Korte text (1 regel)","2"=>"Lange text","3"=>"Keuzelijst");
            $sHtml = "";
            foreach($aOptions AS $iKey => $sOption)
            {
                $sSelected = ($iKey == $p_aQuestion["questionType"]?'selected="selected"':"");
                $sHtml .='<option  value="'.$iKey.'" '.$sSelected.'>'.$sOption.'</option>';
            }
            
            $p_oView->addModuleTemplateVars("questionTypes",$sHtml);
            
            if(isset($p_aQuestion["aParentQuestions"]) ) {
                if(!empty($p_aQuestion["aParentQuestions"]))
                {
                    $sHtml = "";
                    $sHtml .='<option  value="0">Kies een vraag indien van toepassing</option>';
                    foreach($p_aQuestion["aParentQuestions"] AS $iKey => $aParent)
                    {
                        $sSelected = ($aParent["questionID"] == $p_aQuestion["parentQuestion"]?'selected="selected"':"");
                        $sHtml .='<option  value="'.$aParent["questionID"].'" '.$sSelected.'>'.$aParent["question"].'</option>';
                    }
                }else {
                    $sHtml .='<option  value="0" selected>Geen vragen gevonden, kan geen parent aanwijzen</option>';
                }
                
                $p_oView->addModuleTemplateVars("parentQuestions",$sHtml);
            }
            
            if($p_aQuestion["questionType"] == 3){
            $soptionFieldsHtml = "";
                if(isset($p_aQuestion["aOptions"]) && !empty($p_aQuestion["aOptions"])) {
                    
                    foreach($p_aQuestion["aOptions"] AS $iKey => $aOption) {
                    $soptionFieldsHtml .=  '<div id="optionWrap_'.$iKey.'" class="optionWrapper">';
                    $soptionFieldsHtml .=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
                    $soptionFieldsHtml .= '<label for="option" class="control-label">Optie</label>';
                    $soptionFieldsHtml .=  '<input type="text" class="form-control id="option'.$iKey.'" placeholder="Keuze optie" name="optionField_'.$iKey.'" value="'.$aOption["questionOption"].'" >';
                    $soptionFieldsHtml .=  '</div>';
                    $soptionFieldsHtml .=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
                    $soptionFieldsHtml .=  '<label for="option" class="control-label">Score</label>';
                    $soptionFieldsHtml .= '<input type="text" class="form-control id="optionScore_'.$iKey.'" placeholder="Optie score" name="optionScoreField_'.$iKey.'" value="'.$aOption["optionScore"].'" >';
                    $soptionFieldsHtml .= '</div>';
                    $soptionFieldsHtml .= '<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField buttonGroup">';
                    $soptionFieldsHtml .= '<div id="change_'.$iKey.'_'.$aOption["optionID"].'" class="btn btn-success changeOptionDB" role="button">Wijzig optie</div>';
                    $soptionFieldsHtml .= '<div id="delete_'.$iKey.'_'.$aOption["optionID"].'" class="btn btn-danger deleteOptionDB" role="button">Verwijder optie</div>';
                    $soptionFieldsHtml .= '</div>';
                    $soptionFieldsHtml .= '<div id="clear_'.$iKey.'" class="clearfix"></div>';
                    $soptionFieldsHtml .= '</div>';
                    }
                    
                }
                
                $p_oView->addModuleTemplateVars("optionFields",$soptionFieldsHtml);
            
            }else {
                $p_oView->addModuleTemplateVars("optionFields","");
            }
        
    }
    
    
    public static function fillFormFieldsAddQuestion($p_oView,$p_aFormFields,$p_aParentQuestions = array(),$p_extraFields = array())
    {
        //echo "<pre>";
       // print_r($p_aFormFields);
        //echo "</pre>";
        $bOptionFields = false;
        $soptionFieldsHtml = "";
        foreach($p_aFormFields AS $sFieldName => $sFieldValue)
        {
            if($sFieldName == "f_answerTypes")
            {
                $aOptions = array("0"=>"Kies een type antwoord","1"=>"Korte text (1 regel)","2"=>"Lange text","3"=>"Keuzelijst");
                $sHtml = "";
                foreach($aOptions AS $iKey => $sOption)
                {
                    $sSelected = ($iKey == $sFieldValue?'selected="selected"':"");
                    $sHtml .='<option  value="'.$iKey.'" '.$sSelected.'>'.$sOption.'</option>';
                }
                
                    $p_oView->addModuleTemplateVars("questionTypes",$sHtml);
                
            }elseif(preg_match("/^optionField/",$sFieldName) && $sFieldValue !="")
            {
                
                $iOptionNr = explode("_",$sFieldName);
                
                $bOptionFields = true;
               
                $soptionFieldsHtml .=  '<div id="optionWrap_'.$iOptionNr[1].'" class="optionWrapper">';
                $soptionFieldsHtml .=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
                $soptionFieldsHtml .= '<label for="option" class="control-label">Optie</label>';
                $soptionFieldsHtml .=  '<input type="text" class="form-control id="option'.$iOptionNr[1].'" placeholder="Keuze optie" name="optionField_'.$iOptionNr[1].'" value="'.$sFieldValue.'" >';
                $soptionFieldsHtml .=  '</div>';
                $soptionFieldsHtml .=   '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
                $soptionFieldsHtml .=  '<label for="option" class="control-label">Score</label>';
                $soptionFieldsHtml .= '<input type="text" class="form-control id="optionScore_'.$iOptionNr[1].'" placeholder="Optie score" name="optionScoreField_'.$iOptionNr[1].'" value="" >';
                $soptionFieldsHtml .= '</div>';
                $soptionFieldsHtml .= '<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField buttonGroup">';
                $soptionFieldsHtml .= '<div id="'.$iOptionNr[1].'" class="btn btn-danger deleteOption" role="button">Verwijder optie</div>';
                $soptionFieldsHtml .= '</div>';
                $soptionFieldsHtml .= '<div id="clear_'.$iOptionNr[1].'" class="clearfix"></div>';
                $soptionFieldsHtml .= '</div>';
                
               
            }elseif($sFieldName == "f_subQuestion") {
                $sHtml = "";
                if(!empty($p_aParentQuestions))
                {
                    $sHtml .='<option  value="0">Kies een vraag indien van toepassing</option>';
                    foreach($p_aParentQuestions AS $iKey => $aParent)
                    {
                        $sSelected = ($aParent["questionID"] == $sFieldValue?'selected="selected"':"");
                        $sHtml .='<option  value="'.$aParent["questionID"].'" '.$sSelected.'>'.$aParent["question"].'</option>';
                    }
                }else {
                    $sHtml .='<option  value="0" selected>Geen vragen gevonden, kan geen parent aanwijzen</option>';
                }
                
                $p_oView->addModuleTemplateVars("parentQuestions",$sHtml);
            
            }elseif($sFieldName == "f_QuestionGroup"){
                $sHtml = "";
               
                if(!empty($p_extraFields["questionGroups"]))
                {
                    $sHtml .='<option  value="0" selected>Kies een vraaggroep indien van toepassing</option>';
                    foreach($p_extraFields["questionGroups"] AS $aGroup)
                    {
                        $sSelected = ($aGroup["questionGroupID"] == $sFieldValue?'selected="selected"':"");
                        $sHtml .='<option  value="'.$aGroup["questionGroupID"].'" '.$sSelected.'>'.$aGroup["groupName"].'</option>';
                    }
                }else {
                    $sHtml .='<option  value="0" selected>Geen vraaggroepen gevonden, voeg er teminste 1 toe</option>';
                }
                
                $p_oView->addModuleTemplateVars("questionGroups",$sHtml);
                
            }elseif($sFieldName == "f_answerOptions") {
                $sHtml = "";
                if(!empty($p_extraFields["options"]))
                {
                    $sHtml .='<option  value="0">Kies een vraaggroep of voeg een nieuwe toe</option>';
                    foreach($p_extraFields["options"] AS $aOptions)
                    {
                        $sSelected = ($aOptions == $sFieldValue?'selected="selected"':"");
                        $sHtml .='<option  value="'.$aOptions.'" '.$sSelected.'>'.$aOptions.'</option>';
                    }
                }else {
                    $sHtml .='<option  value="0" selected>Geen antwoord opties gevonden, voeg opties toe</option>';
                }
                
                
               
                $p_oView->addModuleTemplateVars("answerOptions",$sHtml);
            }else {
               
               $p_oView->addModuleTemplateVars($sFieldName,$sFieldValue);
            }
            
           
            
        }
        
        if($bOptionFields === false)
        {
           $p_oView->addModuleTemplateVars("optionFields","");
        }else {
            $p_oView->addModuleTemplateVars("optionFields",$soptionFieldsHtml);
        }
    }
    
    public static function createQuestionRows($p_oForm)
    {
        $aQuestions = $p_oForm->getQuestions();
        if(!empty($aQuestions))
        {
            $sHtml ="";
            foreach($aQuestions as $oQuestion)
            {
                $sHtml .="<tr>";
                $sHtml .= "<td>".$oQuestion->getQuestionID()."</td>";
                $sHtml .= "<td>".$oQuestion->getQuestion()."</td>";
                $sHtml .= "<td>".$oQuestion->getQuestionType(true)."</td>";
                $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changequestion/'.$p_oForm->getFormID().'/'.$oQuestion->getQuestionID().'">Wijzigen</a></td>';
                
                if($oQuestion->getLockt())
                {
                    $locket = " blockt";
                }else {
                    $locket = " delete";
                }
                $sHtml .= '<td><a class="editLink'.$locket.'" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deletequestion/'.$p_oForm->getFormID().'/'.$oQuestion->getQuestionID().'#Questions">Verwijderen</a></td>';
                $sHtml .= "</tr>";
            }
            
            return $sHtml;
        }else {
            return '<tr class="errorrow"><td><br />Er zijn nog geen vragen toegevoegd</td></tr>';
        }
    }
    
    public static function createScoreRows($p_aScores,$p_oForm)
    {
        if(!empty($p_aScores))
        {
            $sHtml ="";
            foreach($p_aScores as $aScores)
            {
            $sHtml .="<tr>";
            $sHtml .= "<td>".$aScores["formScoreID"]."</td>";
            $sHtml .= "<td>".$aScores["scoreDescription"]."</td>";
            $sHtml .= "<td>".$aScores["scoreLow"]."</td>";
            $sHtml .= "<td>".$aScores["scoreHigh"]."</td>";
            $sHtml .= "<td>".$aScores["comparison"]."</td>";
            $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changescore/'.$p_oForm->getFormID().'/'.$aScores["formScoreID"].'">Wijzigen</a></td>';
            $sHtml .= '<td><a class="editLink delete" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deletescore/'.$p_oForm->getFormID().'/'.$aScores["formScoreID"].'">Verwijderen</a></td>';
            $sHtml .= "</tr>";
            }
            
            
            return $sHtml;
            
        }else {
            return '<tr class="errorrow"><td><br />Er zijn nog geen scores toegevoegd</td></tr>';
        }
    }
    
    public static function createScoreGroupRows($p_aGroups,$p_oForm)
    {
        if(!empty($p_aGroups))
        {
            $sHtml ="";
            foreach($p_aGroups as $aGroup)
            {
                $sHtml .="<tr>";
                $sHtml .= "<td>".$aGroup["scoregroupID"]."</td>";
                $sHtml .= "<td>".$aGroup["scoreGroup"]."</td>";
                $sHtml .= "<td>".$aGroup["startRange"]."</td>";
                $sHtml .= "<td>".$aGroup["endRange"]."</td>";
                $sHtml .= '<td><a class="editLink change" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/changescoregroup/'.$p_oForm->getFormID().'/'.$aGroup["scoregroupID"].'">Wijzigen</a></td>';
                $sHtml .= '<td><a class="editLink delete" href="'.HOST.'/'.APPLICATIONPATH.'surveypanel/deletescoregroup/'.$p_oForm->getFormID().'/'.$aGroup["scoregroupID"].'">Verwijderen</a></td>';
                $sHtml .= "</tr>";
            }
            
            
            return $sHtml;
        }else {
             return '<tr class="errorrow"><td><br />Er is nog geen scoregroep toegevoegd</td></tr>';
            
        }
    }
    
    public static function createComparisonOptions($p_sFieldValue)
    {
        $sHtml = "";
        
        $sSelected1 = ($p_sFieldValue == "Kleiner dan"?"selected":"");
        $sHtml .= '<option value="Kleiner dan" '.$sSelected1.'>Kleiner dan</option>';
        
        $sSelected2 = ($p_sFieldValue == "Groter dan"?"selected":"");
        $sHtml .= '<option value="Groter dan" '.$sSelected2.'>Groter dan</option>';
        
        $sSelected3 = ($p_sFieldValue == "Kleiner of gelijk aan"?"selected":"");
        $sHtml .= '<option value="Kleiner of gelijk aan" '.$sSelected3.'>Kleiner of gelijk aan</option>';
        
        $sSelected4 = ($p_sFieldValue == "Groter of gelijk aan"?"selected":"");
        $sHtml .= '<option value="Groter of gelijk aan" '.$sSelected4.'>Groter of gelijk aan</option>';
        
        $sSelected5 = ($p_sFieldValue == "Range"?"selected":"");
        $sHtml .= '<option value="Range" '.$sSelected5.'>Range tussen score laag &amp score hoog</option>';
        
        return $sHtml;
    }
    
    
    public static function createSurveyFormPreview($p_oForm) {
        
        $sHtml = "";
        $sHtml .= '<form id="clientForm" method="POST" action="">';
        $sHtml .= '<h1>'.$p_oForm->getName().'</h1><br /><br />';
        $sHtml .= '<div class="alert alert-succes normal" role="alert">
 				<p id="formSuccesMessage">{sFormSucces}</p>
  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    				<span aria-hidden="true">&times;</span>
  				</button>
  			</div><br />';
        
        $sHtml .= '<input type="hidden" name="f_iFormID" value="'.$p_oForm->getFormID().'">';
        
        if($p_oForm->getType() == "Simple") {
            
            $aQuestions = $p_oForm->getQuestions();
        }elseif($p_oForm->getType() == "Medium") {
            $aQuestions = $p_oForm->getQuestionsSorted();
        }elseif($p_oForm->getType() == "Complex") {
            $aQuestions = $p_oForm->getQuestionsSorted();
            $iQuestionNumber = 1;
            $aGroups = array();
            $iGroupNumber = 0;
        }
        
        
        
        //print_r($p_oForm);
        foreach($aQuestions AS $iKey => $oQuestion )
        {
            if($p_oForm->getType() == "Simple")
            {
                $sHtml .= self::createSimpleQuestion($iKey+1,$oQuestion);
            }elseif($p_oForm->getType() == "Medium") {
                
                $sHtml .= self::createSimpleQuestion($oQuestion["questionNumber"],$oQuestion["oQuestion"]);
                if(!empty($oQuestion["subquestions"]))
                {
                    foreach($oQuestion["subquestions"] AS $aQuestion)
                    {   
                        $sHtml .= self::createSimpleQuestion($aQuestion["questionNumber"],$aQuestion["oQuestion"]);
                        $sHtml .= '<hr />';
                    }
                }
                
            }elseif($p_oForm->getType() == "Complex") {
                if(is_array($oQuestion))
                {
                    if(isset($oQuestion["groupName"]))
                    {
                        $iGroupNumber++;
                        $sHtml .= '<h2 class="questionGroupName" id="groupNumber_'.$iGroupNumber.'">'.preg_replace("/\[[A-Z]\]/","",$oQuestion["groupName"]).'</h2>';
                        
                        if(isset($oQuestion["questions"]))
                        {
                            $sHtml .= '<div class="hidden" id="group_'.$iGroupNumber.'">'.count($oQuestion["questions"]).'</div>';
                            foreach($oQuestion["questions"] AS $oQuestion)
                            {
                                $sHtml .= self::createSimpleQuestion($iQuestionNumber,$oQuestion);
                                $iQuestionNumber++;
                            }
                        }
                    }
                }
            
            }else {
                $sHtml .= "Formulier type ".$p_oForm->getType()." nog niet beschikbaar<br>";
            }
            
            
        }
        $sHtml .= '<button type="submit" class="btn btn-primary disabled" name="sendForm">Voeg vragenlijst toe</button>';
        $sHtml .= '<button type="reset" class="btn btn-primary disabled" name="reset">Wis gegegevens</button>';
        $sHtml .= '</form>';
        return $sHtml;
    }
    
    private static function createSimpleQuestion($p_iQuestionNUmber, $p_oQuestion)
    {
        $sHtml = "";
        switch($p_oQuestion->getQuestionType()) {
            case "1" :
                $sHtml = '<div class="form-group">
                      <label class="control-label"><span class="number">'.$p_iQuestionNUmber.'</span> '.$p_oQuestion->getQuestion().'</label><br>
                       <input type="text" class="form-control" id="question_'.$p_oQuestion->getQuestionID().'" placeholder="Uw antwoord" name="question_'.$p_oQuestion->getQuestionID().'" value="">
                      </div>';
                break;
            case "2" :
                $sHtml = '<div class="form-group"">
    					<br /><label for="exampleFormControlTextarea1"><span class="number">'.$p_iQuestionNUmber.'</span> '.$p_oQuestion->getQuestion().' Beschrijving</label>
    					<textarea class="form-control rounded-0" id="question_'.$p_oQuestion->getQuestionID().'" rows="5" name="question_'.$p_oQuestion->getQuestionID().'" placeholder="'.$p_oQuestion->getQuestion().'"></textarea>
						</div>';
                break;
            case "3" :
                $aOptions = $p_oQuestion->getOptions();
                if(!empty($aOptions)) {
                    
                    if($p_oQuestion->getNumberOfAnswers()> 1) 
                    {
                        $sHtml .= '<div class="form-group">
						<label><span class="number">'.$p_iQuestionNUmber.'</span> '.$p_oQuestion->getQuestion().'</label>
						<select class="form-control" id="question_'.$p_oQuestion->getQuestionID().'" name="question_'.$p_oQuestion->getQuestionID().'">
							<option value="">Maak een keuze</option>';
                        foreach($aOptions AS $sOption => $iOptionScore)
                        {
                            $sValue = BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID());
                            $sSelected = ($iOptionScore == $sValue?"selected":"");
                            $sHtml .= '<option value="'.$iOptionScore.'" '.$sSelected.' >'.$sOption.'</option>';
                        }
                        
                        $sHtml .= '</select></div>';
                        
                        $iAnswers = $p_oQuestion->getNumberOfAnswers();
                        for($i=1;$i<=$iAnswers;$i++)
                        {
                            $sHtml .= '<div class="form-group">
						<label><small>Maak indien nodig nog een keuze</small></label>
						<select class="form-control" id="question_'.$p_oQuestion->getQuestionID().'_'.$i.'" name="question_'.$p_oQuestion->getQuestionID().'_'.$i.'">
							<option value="">Maak een keuze</option>';
                            
                            foreach($aOptions AS $sOption => $iOptionScore)
                            {
                                $sValue = BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID());
                                $sSelected = ($iOptionScore == $sValue?"selected":"");
                                $sHtml .= '<option value="'.$iOptionScore.'" '.$sSelected.' >'.$sOption.'</option>';
                            }
                            
                            $sHtml .= '</select></div>';
                        }
                    }else {
                    
                    $sHtml .= '<div class="form-group">
						<label><span class="number">'.$p_iQuestionNUmber.'</span> '.$p_oQuestion->getQuestion().'</label>
						<select class="form-control" id="question_'.$p_oQuestion->getQuestionID().'" name="question_'.$p_oQuestion->getQuestionID().'">
							<option value="">Maak een keuze</option>';
                    foreach($aOptions AS $sOption => $iOptionScore)
                    {
                        $sValue = BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID());
                        $sSelected = ($iOptionScore == $sValue?"selected":"");
                        $sHtml .= '<option value="'.$iOptionScore.'" '.$sSelected.' >'.$sOption.'</option>';
                    }
                    
                    $sHtml .= '</select></div>';
                    
                    }
                } else {
                    $sHtml = "Antwoorden ontbreken<br>";
                }
                break;
            default: $sHtml = "Onbekend type vraag<br>";
            
        }
        
        return $sHtml;
    }
}