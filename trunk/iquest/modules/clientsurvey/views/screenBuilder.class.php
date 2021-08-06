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

abstract class Modules_clientsurvey_screenBuilder
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
        $p_oView->setModuleMainTemplate("clientdashboard.tpl");
        
        //Add the application basedir
        $p_oView->addModuleTemplateVars("baseURL",HOST."/".APPLICATIONPATH."iquestclient/");
        switch($p_sScreen)
        {
            case "Dashboard":
                $p_oView->setTitle("iQuest | Client Dashboard");
                $p_oView->setModuleTemplate("dashboard.tpl");
                $p_oView->addModuleTemplateVars("logoutURL",HOST."/".APPLICATIONPATH."iquestclient/logout");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                $p_oView->addModuleTemplateVars("formsNav",self::createFormsNav($p_oControler->getData("clientForms")));
                $p_oView->addModuleTemplateVars("forms",self::createForm2sOverview($p_oControler->getData("clientForms")));
                $p_oView->addModuleTemplateVars("sFormOutput","");
                break;
            case "surveyForm":
                $p_oView->setTitle("iQuest | Vragenlijst ".$p_oControler->getData("surveyForm")->getName());
                $p_oView->setModuleTemplate("surveyform.tpl");
                $p_oView->addModuleTemplateVars("logoutURL",HOST."/".APPLICATIONPATH."iquestclient/logout");
                $p_oView->addModuleTemplateVars("userName",$p_oControler->getData("userScreenname"));
                
                $p_oView->addModuleTemplateVars("formsNav",self::createFormsNav($p_oControler->getData("clientForms")));
                
                $p_oView->addModuleTemplateVars("sFormOutput",$p_oControler->getData("sFormMessage"));
                $p_oView->addModuleTemplateVars("formname",$p_oControler->getData("surveyForm")->getName());
                $p_oView->addModuleTemplateVars("formIntro",$p_oControler->getData("surveyForm")->getDescription(true));
                $p_oView->addModuleTemplateVars("surveyForm",self::createSurveyForm($p_oControler->getData("surveyForm"),$p_oControler->getData("sFormStatus")) );
                $p_oView->addModuleTemplateVars("sFormSucces",$p_oControler->getData("sFormSucces"));
                break;
           
            
            default : echo "Onbekende pagina";
        }
       
    }
    
    
   public static function createFormsNav($p_aForms)
   {
       $sHtml = "";
       if(is_array($p_aForms)) {
           foreach($p_aForms AS $aForm)
           {    
               $sBadge = ($aForm["status"] == "closed"?'<span class="label label-success pull-right">Voltooid</span>':'<span class="label label-primary pull-right">Open</span>');
               $sHtml .= '<li><a href="'.HOST.'/'.APPLICATIONPATH.'iquestclient/vragenlijst/'.$aForm["clientFormID"].'">'.$aForm["formName"].$sBadge.'</a></li>';
           }
       }
       
       return $sHtml;
   }
   
   public function createFormsOverview($p_aForms)
   {
       $sHtml ="";
       if(is_array($p_aForms)) {
           foreach($p_aForms AS $aForm)
           {
               $sHtml .= '<tr>';
               $sEditclass = ($aForm["status"] == "closed"?'class="editLink blockt"':"");
               
               $sHtml .= '<td><a href="'.HOST.'/'.APPLICATIONPATH.'iquestclient/vragenlijst/'.$aForm["clientFormID"].'" '.$sEditclass.'>'.$aForm["formName"].'</a></td>';
               $sHtml .= '<td>'.date("d-m-Y",strtotime($aForm["creationDate"])).'</td>';
               $datetime1 = new DateTime("now");
               
               $datetime2 = new DateTime(date('Y-m-d', strtotime($aForm["creationDate"]."+14 days")));
               $Interval = $datetime1->diff($datetime2);
               $sTimeLeft = $Interval->format('%R%a days');
               
               $sTimeLeft = preg_replace("/days/","dag(en)",$sTimeLeft);
               if(preg_match("/^\+/",$sTimeLeft))
               {
                   $sClass = "onTime";
                   $sTimeLeft =  preg_replace("/\+/","",$sTimeLeft);
               }else 
                    {
                        $sClass = "overdue";
                        $sTimeLeft =  preg_replace("/\-/","",$sTimeLeft);
                    }
               
               $sEditclass = ($aForm["status"] == "closed"?'disabled':"");
               $sHtml .= '<td class="'.$sClass.'">'.$sTimeLeft.'</td>';
               $sHtml .= '<td>'.$aForm["status"].'</td>';
               $sHtml .= '<td><a class="btn btn-primary '.$sEditclass.'" href="'.HOST.'/'.APPLICATIONPATH.'iquestclient/vragenlijst/'.$aForm["clientFormID"].'">Invullen</a></td>';
               $sHtml .= '</tr>';
           }
       }else {
           $sHtml .= '<tr class="errorrow"><td><br />Nog geen formulieren toegevoegd</td></tr>';
       }
       
       return $sHtml;
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
                $sHtml .= "<td>0</td>";
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
                $sHtml .= "<td>".$aClient["firstName"]." ".$aClient["lastName"]."</td>";
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
            }
            
            
            return $sHtml;
        }else{
            return 'Geen forms gevonden';
        }
    }
    
    
    public static function createCompanyFieldRows($p_mCompanys,$p_aClientFormFields = array())
    {  
        if(is_array($p_mCompanys))
        {
            $sHtml = "";
            foreach($p_mCompanys AS $aCompany)
            {
                $sSelected = ($p_aClientFormFields["companyIDSelected"] == $aCompany["companyID"]?'selected="selected"':"");
                $sHtml .='<option  value="'.$aCompany["companyID"].'" '.$sSelected.'>'.$aCompany["companyName"].'</option>';
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
    
    
    public static function createSurveyForm($p_oForm,$p_sFormStatus) {
         
        $sFormURL = HOST.'/'.APPLICATIONPATH.'iquestclient/vragenlijst/'.$p_oForm->getClientFormID();
        
        $aQuestions = $p_oForm->getQuestions();
        $sHtml = "";
        $sHtml .= '<div id="formStatus" class="hidden">'.$p_sFormStatus.'</div>';
        $sHtml .= '<div id="formType" class="hidden">'.$p_oForm->getType().'</div>';
        $sHtml .= '<form id="clientForm" method="POST" action="'.$sFormURL.'">';
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
           
        }elseif($p_oForm->getType() == "Complex"){
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
                       
                    }
                }
                
            }elseif($p_oForm->getType() == "Complex") {
              
                if(is_array($oQuestion))
                {
                    if(isset($oQuestion["groupName"]))
                    {
                        $iGroupNumber++;
                        $sHtml .= '<h2 class="hidden questionGroupName" id="groupNumber_'.$iGroupNumber.'">'.preg_replace("/\[[A-Z]\]/","",$oQuestion["groupName"]).'</h2>';
                        
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
        
        if($p_oForm->getType() == "Medium" || $p_oForm->getType() == "Complex" )
        {
            $sHtml .= "<hr>";
            $sHtml .= '<div id="questionsTotal" class="hidden">'.count($p_oForm->getQuestions()).'</div>';
            $sHtml .= '<div id="questionsFilled" ">Aantal vragen <span id="filled">0</span> / '.count($p_oForm->getQuestions()).'</div><br><br>';
        }
        $sHtml .= '<button type="submit" id="butSend" class="btn btn-primary" name="sendForm" disabled>Voeg vragenlijst toe</button>';
        $sHtml .= '<button type="reset" id="butreset" class="btn btn-primary" name="reset">Wis gegegevens</button>';
        $sHtml .= '</form>';
        return $sHtml;
    }
    
    private static function createSimpleQuestion($p_iQuestionNUmber, $p_oQuestion)
    {
       $sHtml = "";
        switch($p_oQuestion->getQuestionType()) {
            case "1" :
                $sHtml = '<div class="form-group main">
                      <label class="control-label">'.$p_iQuestionNUmber.' '.$p_oQuestion->getQuestion().'</label><br>
                       <input type="text" class="form-control" id="question_'.$p_oQuestion->getQuestionID().'" placeholder="'.$p_oQuestion->getQuestion().'" name="question_'.$p_oQuestion->getQuestionID().'" value="'.BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID()).'">
                      </div>';
            break;
            case "2" :
                $sHtml = '<div class="form-group main">
    					<br /><label for="exampleFormControlTextarea1">'.$p_iQuestionNUmber.' '.$p_oQuestion->getQuestion().' Beschrijving</label>
    					<textarea class="form-control rounded-0" id="question_'.$p_oQuestion->getQuestionID().'" rows="5" name="question_'.$p_oQuestion->getQuestionID().'" placeholder="'.$p_oQuestion->getQuestion().'">'.BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID()).'</textarea>
						</div>';
             break;
            case "3" :
                $aOptions = $p_oQuestion->getOptions();
                if(!empty($aOptions)) {
                    
                    if($p_oQuestion->getNumberOfAnswers()> 1)  {
                        $sHtml .= '<div class="form-group main">
						<label><span class="number">'.$p_iQuestionNUmber.'</span> '.$p_oQuestion->getQuestion().'</label>
						<select class="form-control main hassubs" id="question_'.$p_oQuestion->getQuestionID().'" name="question_'.$p_oQuestion->getQuestionID().'">
							<option value="">Maak een keuze</option>';
                        foreach($aOptions AS $sOption => $iOptionScore)
                        {
                            $sValue = BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID());
                            $sSelected = ($iOptionScore.'_'.$sOption == $sValue?"selected":"");
                            $sHtml .= '<option value="'.$iOptionScore.'_'.$sOption.'" '.$sSelected.' >'.$sOption.'</option>';
                        }
                        
                        $sHtml .= '</select></div>';
                        
                        $iAnswers = $p_oQuestion->getNumberOfAnswers();
                        for($i=1;$i<=$iAnswers;$i++)
                        {
                            $sHtml .= '<div class="form-group sub">
						<label><small>Maak indien nodig nog een keuze</small></label>
						<select class="form-control sub" id="question_'.$p_oQuestion->getQuestionID().'_'.$i.'" name="question_'.$p_oQuestion->getQuestionID().'_'.$i.'">
							<option value="">Maak een keuze indien van toepassing</option>';
                           
                            foreach($aOptions AS $sOption => $iOptionScore)
                            {
                                $sValue = BChelpers_formHandler::getValue('question_'.$p_oQuestion->getQuestionID().'_'.$i);
                                $sSelected = ($iOptionScore.'_'.$sOption == $sValue?"selected":"");
                                $sHtml .= '<option value="'.$iOptionScore.'_'.$sOption.'" '.$sSelected.' >'.$sOption.'</option>';
                            }
                            
                            $sHtml .= '</select></div>';
                        }
                    }else {
                $sHtml .= '<div class="form-group main">
						<label>'.$p_iQuestionNUmber.' '.$p_oQuestion->getQuestion().'</label>
						<select class="form-control main" id="question_'.$p_oQuestion->getQuestionID().'" name="question_'.$p_oQuestion->getQuestionID().'">
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