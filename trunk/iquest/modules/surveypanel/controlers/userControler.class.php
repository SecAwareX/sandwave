<?php
/**
 *	userControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 24 aug. 2018
 *  Project : iQuest
 * 	Package : surveypanel/controlers
 *  Version : 1.0
 * 
 */

class surveypanel_userControler extends surveypanel_dashboardControler
{
    
    /**
     * Constructor
     * @param object  $p_oRequest : Request object
     * @param object $p_oDB : Database object
     * @param object  $p_oCrypt : encyption object
     */
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        $this->loadUserData($_SESSION["userID"]);
    }
    
    /**
     * 
     * showUsers()
     * @description : Selects all the users with the type ADMIN
     * @return true when there are no errors
     */
    public function showUsers()
    {
        //Get the user from the DB 
        $sSQL = "SELECT * FROM users where userLevel='Admin'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            foreach($aResult AS $aUser)
            {
                $this->aData["users"][] = $this->oCrypt->multiDecrypt($aUser);
            }
            
            $this->sStatus = "Ready";
            return true;
        }else
            {
                $this->aData["users"] = "Geen gebruikers gevonden";
            }
    }
    
    /**
     * addUser()
     * @description : adds a new user to the system. When the action succeed!
     *                E-mail the new user the credentials
     *                CC mail to the developmail 
     * @todo : remove the DEV mail after a while in the class surveypanel/mails/mailBuilder ::newUser
     */
    public function addUser()
    {
        //Setting up the form
        BChelpers_formHandler::addFieldCheck("f_ScreenName",true,"surveypanel_checkLibary::checkScreenname","Schermnaam : Geef een geldige naam op");
        BChelpers_formHandler::addFieldCheck("f_UserName",true,"login_checkLibary::checkUserNameAdmin","Gebruikersnaam : Geef een geldig email adres op");
        BChelpers_formHandler::addFieldCheck("f_Pass",true,"login_checkLibary::checkPass");
        
        $this->aData["sFormSuccesMessage"] = "";
        $this->aData["sFormMessage"] = "";
        $this->aData["submit_change_dis"] = "disabled";
        $this->aData["submit_add_dis"] = "";
        
        if(BChelpers_formHandler::formSend("doAddUser"))
        {
            if(!BChelpers_formHandler::handleForm())
            {
                if(count(BChelpers_formHandler::$aFormErrors["mandatoryFields"]) > 0 )
                {
                    $this->aData["sFormMessage"] = "Niet alle verplichte velden zijn ingevuld";
                }elseif(count(BChelpers_formHandler::$aFormErrors["formatErrors"]) > 0 )
                {
                    $this->aData["sFormMessage"] = "<strong>Niet alle velden zijn correct ingevuld!</strong><br />";
                    foreach(BChelpers_formHandler::$aFormErrors["formatErrors"] as $sError)
                    {
                        $this->aData["sFormMessage"] .=$sError."<br />";
                    }
                   
                }
                
                $this->aData["field_screenName"] = BChelpers_formHandler::getValue("f_ScreenName");
                $this->aData["field_userName"] = BChelpers_formHandler::getValue("f_UserName");
                $this->aData["field_pass"] = BChelpers_formHandler::getValue("f_Pass");
                
                $this->aData["field_doMail"] = (BChelpers_formHandler::getValue("doMail")?"checked":"");
            }else 
                {
                    //Form is validated, so save the new user
                    $this->aData["field_screenName"] = BChelpers_formHandler::getValue("f_ScreenName");
                    $this->aData["field_userName"] = BChelpers_formHandler::getValue("f_UserName");
                    $this->aData["field_pass"] = BChelpers_formHandler::getValue("f_Pass");
                    $this->aData["field_doMail"] = (BChelpers_formHandler::getValue("doMail")?"checked":"");
                    
                    if($this->checkUserByMail(BChelpers_formHandler::getValue("f_UserName")))
                    {
                        $iLastINsert = $this->saveNewUser();
                        if($iLastINsert > 0)
                        {
                            $this->aData["iUserID"] = $iLastINsert;
                            $this->aData["sFormSuccesMessage"] = "Gebruiker is succesvol toegevoegd";
                            $this->aData["submit_change_dis"] = "";
                            $this->aData["submit_add_dis"] = "disabled";
                            
                            if(BChelpers_formHandler::getValue("doMail"))
                            {
                                //Send mail
                                $aMailVars = array();
                                $aMailVars["name"] = BChelpers_formHandler::getValue("f_ScreenName");
                                $aMailVars["Email"] = BChelpers_formHandler::getValue("f_UserName");
                                $aMailVars["UserName"] = BChelpers_formHandler::getValue("f_UserName");
                                $aMailVars["Pass"] = BChelpers_formHandler::getValue("f_Pass");
                                $aMailVars["URL"] = HOST."/".APPLICATIONPATH."surveypanel/";
                                
                                $oMail = new BCMail_BCMail();
                                if($oMail->sendMail("surveypanel","newUser",$aMailVars))
                                {
                                    $this->sStatus = "Ready";
                                }else {
                                    $this->sStatus = "Failed";
                                }
                            }
                        }else
                            {
                                $this->aData["sFormMessage"] = "er is een fout opgetreden tijdens het opslaan";
                            }
                    }else{
                        
                        $this->aData["submit_change_dis"] = "disabled";
                        $this->aData["submit_add_dis"] = "";
                        
                        $this->aData["sFormMessage"] = "Er bestaat al een gebruiker met de username : ".BChelpers_formHandler::getValue("f_UserName");
                     }
                    
                }
        }else
            {
                //Form is not send yet, so set empty defaults
                $this->aData["sFormMessage"] = "";
                $this->aData["field_screenName"] = "";
                $this->aData["field_userName"] = "";
                $this->aData["field_pass"] = login_loginHelper::generateNewPassWord();
                $this->aData["field_doMail"] = "";
            }
    }
    
    /**
     * changeUser()
     * @description : changes a user in the system. When the action succeed!
     *                E-mail the new user the credentials
     *                CC mail to the developmail
     * @todo : remove the DEV mail after a while in the class surveypanel/mails/mailBuilder ::changeUser
     */
    public function changeUser()
    {
        //Setting up the form
        BChelpers_formHandler::addFieldCheck("f_ScreenName",true,"surveypanel_checkLibary::checkScreenname","Schermnaam : Geef een geldige naam op");
        BChelpers_formHandler::addFieldCheck("f_UserName",true,"login_checkLibary::checkUserNameAdmin","Gebruikersnaam : Geef een geldig email adres op");
        BChelpers_formHandler::addFieldCheck("f_Pass",true,"login_checkLibary::checkPass","Wachtwoord : Moet uit 8 tekens bestaan. Toegestaan zijn a-z(kleine of hoofdletters) en de speciale tekens !@#&*_-");
        
        $this->aData["sFormSuccesMessage"] = "";
        $this->aData["sFormMessage"] = "";
       
        if($this->checkUserByID($this->oRequest->getParameter(2)))
        {
            $aUser = $this->loadUser($this->oRequest->getParameter(2));
            if(BChelpers_formHandler::formSend("doChangeUser"))
            {
                if(!BChelpers_formHandler::handleForm())
                {
                    if(count(BChelpers_formHandler::$aFormErrors["mandatoryFields"]) > 0 )
                    {
                        $this->aData["sFormMessage"] = "Niet alle verplichte velden zijn ingevuld";
                    }elseif(count(BChelpers_formHandler::$aFormErrors["formatErrors"]) > 0 )
                    {
                        $this->aData["sFormMessage"] = "<strong>Niet alle velden zijn correct ingevuld!</strong><br />";
                        foreach(BChelpers_formHandler::$aFormErrors["formatErrors"] as $sError)
                        {
                            $this->aData["sFormMessage"] .=$sError."<br />";
                        }
                        
                    }
                    
                    $this->aData["userID"] = $this->oRequest->getParameter(2);
                    $this->aData["field_screenName"] = BChelpers_formHandler::getValue("f_ScreenName");
                    $this->aData["field_userName"] = BChelpers_formHandler::getValue("f_UserName");
                    $this->aData["field_pass"] = BChelpers_formHandler::getValue("f_Pass");
                    
                    $this->aData["field_doMail"] = (BChelpers_formHandler::getValue("doMail")?"checked":"");
                }else
                    {
                       $this->saveUser($this->oRequest->getParameter(2));
                       $this->aData["sFormSuccesMessage"] = "Gebruiker is gewijzigd";
                       
                       $this->aData["userID"] = $this->oRequest->getParameter(2);
                       $this->aData["field_screenName"] = BChelpers_formHandler::getValue("f_ScreenName");
                       $this->aData["field_userName"] = BChelpers_formHandler::getValue("f_UserName");
                       $this->aData["field_pass"] = BChelpers_formHandler::getValue("f_Pass");
                       
                       if(BChelpers_formHandler::getValue("doMail"))
                       {
                           //Send mail
                           $aMailVars = array();
                           $aMailVars["name"] = BChelpers_formHandler::getValue("f_ScreenName");
                           $aMailVars["Email"] = BChelpers_formHandler::getValue("f_UserName");
                           $aMailVars["UserName"] = BChelpers_formHandler::getValue("f_UserName");
                           $aMailVars["Pass"] = BChelpers_formHandler::getValue("f_Pass");
                           $aMailVars["URL"] = HOST."/".APPLICATIONPATH."surveypanel/";
                           
                           $oMail = new BCMail_BCMail();
                           if($oMail->sendMail("surveypanel","changeUser",$aMailVars))
                           {
                               $this->sStatus = "Ready";
                           }else {
                               $this->sStatus = "Failed";
                           }
                       }
                    }
            }else{
                //Initial request, so load the user
                $aUser = $this->loadUser($this->oRequest->getParameter(2));
                $this->aData["userID"] = $aUser["userID"];
                $this->aData["field_screenName"] = rtrim($aUser["userScreenname"]);
                $this->aData["field_userName"] = rtrim($aUser["userName"]);
                $this->aData["field_pass"] = rtrim($aUser["userPass"]);
                $this->aData["field_doMail"] = "";
                
            }
        }else
            {
                $this->aData["sFormMessage"] = "Gebruiker bestaat niet! Kan niet wijzigen";
            }
    }
    
    /**
     * deleteUser()
     * @description : Removes a user from the system 
     *                The real remove action / query is in $this->deleteDBUser
     */
    public function deleteUser()
    {
        $iUserID = $this->oRequest->getParameter(2);
        if($iUserID == $_SESSION["userID"])
        {
            $this->aData["sFormMessage"] = "Je kunt niet jezelf verwijderen";
        }elseif(!$this->checkUserByID($iUserID))
            {
                $this->aData["sFormMessage"] = "De gebruiker bestaat niet";
        }else {
            if($this->deleteDBUser($iUserID))
            {
                $this->aData["sFormSuccesMessage"] = "De gebruiker is succesvol verwijderd";
                $this->showUsers();
            }
        }
    }
    
    /**
     * loadUser() : query to get the user data by ID
     * @param integer $p_iUserID
     * @return array
     */
    private function loadUser($p_iUserID)
    {
        $sSQL = "SELECT * FROM users WHERE userID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iUserID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if(!empty($aResult))
        {
            return $this->oCrypt->multiDecrypt($aResult);
         }
    }
    
    /**
     * checkUserByMail()
     * @description : Checks if there is a admin user with the given encrypted email
     * @return boolean
     */
    private function checkUserByMail($p_sEmail)
    {
        $sUser = $this->oCrypt->enCrypt($p_sEmail);
        $sSQL = "SELECT count(userID) AS iUser FROM users WHERE userName=:userName AND userLevel='Admin'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":userName", $sUser);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iUser"] > 0)
        {
            return false;
        }else{
            return true;
        }
    }
    
    private function checkUserByID($p_iID)
    {
       
        $sSQL = "SELECT count(userID) AS iUser FROM users WHERE userID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iUser"] == 1)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function saveNewUser()
    {
        $sUser = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_ScreenName"));
        $sUserName = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_UserName"));
        $sPass = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_Pass"));
        
        $sSQL = "INSERT INTO users (userScreenname,userName,userPass) VALUES (:user,:userName,:pass)";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":user", $sUser);
        $oQuery->bindParam(":userName", $sUserName);
        $oQuery->bindParam(":pass", $sPass);
        $oQuery->execute();
        
        return $this->oDB->lastInsertID();
        
    }
    
    private function saveUser($p_iUserID)
    {
        $sUser = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_ScreenName"));
        $sUserName = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_UserName"));
        $sPass = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_Pass"));
        
        $sSQL = "UPDATE users SET userScreenname=:user,userName=:userName,userPass=:pass WHERE userID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iUserID);
        $oQuery->bindParam(":user", $sUser);
        $oQuery->bindParam(":userName", $sUserName);
        $oQuery->bindParam(":pass", $sPass);
        $oQuery->execute();
    }
    
    private function deleteDBUser($p_iUSerID)
    {
        $sSQL = "DELETE FROM users WHERE userID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iUSerID);
        return $oQuery->execute();
    }
}