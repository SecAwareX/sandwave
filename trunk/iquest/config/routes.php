<?php
/**
 *
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package iQuest
 * @subpackage config 
 * @project  Mareis-BV - iQuest
 * @version  1.0
 * @date 20-08-2018
 * @description  : contains the application routes for the two main modules
 *
 *
 * *
 */

$aRoutes = array();
//Adminpanel
$aRoutes["default"] = array("modulename"=>"surveypanel","controler"=>"controlers_dashboardControler","action"=>"showDashboard","view"=>"Dashboard");
$aRoutes["surveypanel"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_dashboardControler","action"=>"showDashboard","view"=>"Dashboard");

//Adminpanel Users
$aRoutes["surveypanel/users"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_userControler","action"=>"showUsers","view"=>"userOverview");
$aRoutes["surveypanel/adduser"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_userControler","action"=>"addUser","view"=>"addUserForm");
$aRoutes["surveypanel/changeuser"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_userControler","action"=>"changeUser","view"=>"userChangeForm");
$aRoutes["surveypanel/deleteuser"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_userControler","action"=>"deleteUser","view"=>"userOverview");

//Admin panel Companys
$aRoutes["surveypanel/companys"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_companyControler","action"=>"showCompanys","view"=>"companyOverview");
$aRoutes["surveypanel/addcompany"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_companyControler","action"=>"addCompany","view"=>"addCompanyForm");
$aRoutes["surveypanel/changecompany"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_companyControler","action"=>"changeCompany","view"=>"changeCompanyForm");
$aRoutes["surveypanel/changecompanystatus"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_companyControler","action"=>"changeCompanyStatus","view"=>"none");

//Admin panel clients
$aRoutes["surveypanel/clients"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"showClients","view"=>"clientOverview");
$aRoutes["surveypanel/addclient"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"addClient","view"=>"clientForm");
$aRoutes["surveypanel/changeclient"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"changeClient","view"=>"changeClientForm");
$aRoutes["surveypanel/deleteclient"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"deleteClient","view"=>"clientOverview");
$aRoutes["surveypanel/showclient"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"showClient","view"=>"client");
$aRoutes["surveypanel/showformresult"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"showFormResult","view"=>"showformresult");

$aRoutes["surveypanel/sendinvitation"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"sendInvitation","view"=>"clientOverview");
$aRoutes["surveypanel/resendinvitation"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_clientControler","action"=>"resendInvitation","view"=>"clientOverview");


//Admin panel forms
$aRoutes["surveypanel/forms"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"showForms","view"=>"formOverview");
$aRoutes["surveypanel/addform"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"addForm","view"=>"addForm");
$aRoutes["surveypanel/changeform"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"changeForm","view"=>"changeForm");
$aRoutes["surveypanel/changeformstatus"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"changeFormStatus","view"=>"none");

$aRoutes["surveypanel/addquestion"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"addQuestion","view"=>"addQuestion");
$aRoutes["surveypanel/addscore"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"addScore","view"=>"addScore");
$aRoutes["surveypanel/addscoregroup"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"addScoreGroup","view"=>"addScoreGroup");

$aRoutes["surveypanel/deleteform"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"deleteForm","view"=>"formOverview");
$aRoutes["surveypanel/deletequestion"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"deleteQuestion","view"=>"changeForm");
$aRoutes["surveypanel/changequestion"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"changeQuestion","view"=>"changeQuestion");

$aRoutes["surveypanel/deletescore"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"deleteScore","view"=>"changeForm");
$aRoutes["surveypanel/changescore"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"changeScore","view"=>"changeScore");
$aRoutes["surveypanel/deletescoregroup"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"deleteScoreGroup","view"=>"changeForm");
$aRoutes["surveypanel/changescoregroup"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_formControler","action"=>"changeScoreGroup","view"=>"changeScoreGroup");


//Jquery API 
$aRoutes["surveypanel/jqdeleteoption"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_apiFormControler","action"=>"jqDeleteOption","view"=>"");
$aRoutes["surveypanel/jqaddoption"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_apiFormControler","action"=>"jqAddOption","view"=>"");
$aRoutes["surveypanel/jqchangeoption"] = array("modulename"=>"surveypanel","controler"=>"surveypanel_apiFormControler","action"=>"jqChangeOption","view"=>"");


//Login
$aRoutes["surveypanel/login"] = array("modulename"=>"login","controler"=>"login_adminLoginControler","action"=>"login","view"=>"loginForm");
$aRoutes["surveypanel/logout"] = array("modulename"=>"login","controler"=>"login_adminLoginControler","action"=>"logOut","view"=>"loginForm");
$aRoutes["login/registerip"] = array("modulename"=>"login","controler"=>"login_adminLoginControler","action"=>"registerip","view"=>"registerip");
$aRoutes["login/addip"] = array("modulename"=>"login","controler"=>"login_adminLoginControler","action"=>"addip","view"=>"addip");

//ClientLogin
$aRoutes["iquestclient/login"] = array("modulename"=>"login","controler"=>"login_clientLoginControler","action"=>"login","view"=>"clientLoginForm");
$aRoutes["iquestclient/uitloggen"] = array("modulename"=>"login","controler"=>"login_clientLoginControler","action"=>"logOut","view"=>"clientLoginForm");
$aRoutes["iquestclient/wachtwoordvergeten"] = array("modulename"=>"login","controler"=>"login_clientLoginControler","action"=>"lostPass","view"=>"clientLost");




//ClientPanel
$aRoutes["iquestclient"] = array("modulename"=>"clientsurvey","controler"=>"clientsurvey_clientControler","action"=>"showDashboard","view"=>"Dashboard");
$aRoutes["iquestclient/#forms"] = array("modulename"=>"clientsurvey","controler"=>"clientsurvey_clientControler","action"=>"showDashboard","view"=>"Dashboard");
$aRoutes["iquestclient/vragenlijst"] = array("modulename"=>"clientsurvey","controler"=>"clientsurvey_clientControler","action"=>"surveyForm","view"=>"surveyForm");

//Errors
$aRoutes["error/404"] = array("modulename"=>"error","controler"=>"controlers_errorControler","action"=>"show404","view"=>"404");
$aRoutes["error/accesdenied"] = array("modulename"=>"error","controler"=>"error_errorControler","action"=>"accesdenied","view"=>"accesdenied");
$aRoutes["error/accesblockt"] = array("modulename"=>"error","controler"=>"error_errorControler","action"=>"accesBlockt","view"=>"accesblockt");
