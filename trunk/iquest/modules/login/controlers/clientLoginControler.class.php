<?php
/**
 *	adminLoginControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 24 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

class login_clientLoginControler extends BCcontroler_BCControler
{
    private $oAdminLoginControler;
    
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        $this->oAdminLoginControler = new  login_adminLoginControler($p_oRequest, $p_oDB, $p_oCrypt);
    }
    
    public function login()
    {
        //Comfirm or / and set the secret keys. This is a pre control, not the main login procedure. 
        $aKeyConfirmation = $this->comfirmSecretKeys();
        if(!$aKeyConfirmation)
        {
            $this->aData["sFormMessage"] = "Gebruik de unieke url die u per mail heeft ontvangen om in te loggen";
          
        }else 
        {
            BChelpers_formHandler::addFieldCheckNew("Username","Gebruikersnaam / email",true,"login_checkLibary::checkUserNameAdmin","Geef een geldig email adres op");
            BChelpers_formHandler::addFieldCheckNew("Pass","Wachtwoord",true,"login_checkLibary::checkPass","Wachtwoord moet tenminste 8 karakters lang zijn");
            
            if(BChelpers_formHandler::formSend("doLogin"))
            {
                //Check if current sessionIP is lockt ( to manny attempts)
                
                //Create SessionLOg
                $this->oAdminLoginControler->createLoginSession();
                //CheckAttempts
                if(!$this->oAdminLoginControler->checkLoginAttempts())
                {
                    //There are to many attempts, account is locked
                    $this->oRequest->redirect(HOST."/".APPLICATIONPATH."error/accesblockt");
                }
                
                
                
                if(!BChelpers_formHandler::handleForm())
                {
                  
                    $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                    //Check Attempts
                    $this->oAdminLoginControler->updateLoginSession(trim(BChelpers_formHandler::getValue("Username")));
                }else 
                    {
                        //login
                        
                        if($this->checkUser(trim(BChelpers_formHandler::getValue("Username")),trim( BChelpers_formHandler::getValue("Pass")),$aKeyConfirmation))
                        {
                            //User is validated, so create session vars and redirect to the dashboard
                            $this->oAdminLoginControler->updateLoginSession(trim(BChelpers_formHandler::getValue("Username")));
                            $this->oRequest->redirect(HOST."/".APPLICATIONPATH."iquestclient");
                            return true;
                            
                        }else{
                            
                            //Attempt failed, so update loginsession
                            $this->oAdminLoginControler->updateLoginSession(trim(BChelpers_formHandler::getValue("Username")));
                            $this->aData["sFormMessage"] = "Combinatie gebruikersnaam en wachtwoord is onjuist";
                            return false;
                        }
                    }
                
                
            }else 
                {
                
                }
            
            
        }
    }
    
    public function logOut()
    {
        if(login_loginHelper::unsetSessionVars())
        {
            $this->oRequest->redirect(HOST."/".APPLICATIONPATH."iquestclient/login");
        }
    }
    
    
    private function comfirmSecretKeys()
    {
        $aKeys = array();
        if(login_checkLibary::checkSecretKey($this->oRequest->getparameter(2)) && login_checkLibary::checkSecretKey($this->oRequest->getparameter(3)))
        {
           //Format keys are valid, so create a cookkie so we can use short urls after registering the keys
           // This is step one in the user validation, later on we check for a match in the DB for the user
            $sCookie =  $this->oCrypt->enCrypt($_SERVER["REMOTE_ADDR"]."|".$this->oRequest->getparameter(2)); 
            $sCookie2 =  $this->oCrypt->enCrypt($_SERVER["REMOTE_ADDR"]."|".$this->oRequest->getparameter(3)); 
           
           setcookie("iQuestAuth", $sCookie, time() + (86400 * 30), "/"); // 86400 = 1 day
           setcookie("iQuestAuth1", $sCookie2, time() + (86400 * 30), "/"); // 86400 = 1 day
           
           $aKeys["key1"] = $this->oRequest->getparameter(2);
           $aKeys["key2"] = $this->oRequest->getparameter(3);
           
           return $aKeys; 
          
        }elseif(isset($_COOKIE["iQuestAuth"]) && $_COOKIE["iQuestAuth1"])
            {
                
                $sCookie = $this->oCrypt->deCrypt($_COOKIE["iQuestAuth"]);
                $sCookie2 = $this->oCrypt->deCrypt($_COOKIE["iQuestAuth1"]);
                
               $aKeyIP = explode('|',$sCookie);
               $aKeyIP1 = explode('|',$sCookie2);
               
               $aKeys["key1"] = $aKeyIP[1];
               $aKeys["key2"] = $aKeyIP1[1];
               
               if($aKeyIP[0] == $_SERVER["REMOTE_ADDR"] && $aKeyIP1[0] == $_SERVER["REMOTE_ADDR"])
               {
                  return $aKeys;
                   
               }else{
                   return false;
               }
            }else 
                {
                    return false;
                }
    }
    
    public function lostPass()
    {  
        //Check if the form already is send / one time / session
        if(isset($_SESSION["pass_send"]) && $_SESSION["pass_send"] == 1)
        {
            //Form was already send
            echo "formulier kan maar 1 x verstuurd worden";
        }else
            {
                //Handle form
                BChelpers_formHandler::addFieldCheckNew("email","Email",true,"login_checkLibary::checkUserNameAdmin","Geef een geldig email adres op");
                
                if(BChelpers_formHandler::formSend("doPassReset"))
                {
                    if(!BChelpers_formHandler::handleForm())
                    {
                        $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                    }else 
                        {
                            if($this->checkClientByMail(trim(BChelpers_formHandler::getValue("email"))))
                            {
                                //Load clientdata
                                $aClientData = $this->loadClientDataByEmail($this->oCrypt->enCrypt(trim(BChelpers_formHandler::getValue("email"))));
                                $aloginCredentials = array();
                                $aloginCredentials["userName"] = $this->oCrypt->enCrypt($aClientData["email"]);
                                //load userrecorddata
                                $aUserRecord = $this->loadUserRecordByEmail($aloginCredentials["userName"]);
                                $aloginCredentials["SecretKey1"] = $aUserRecord["secretKey1"];
                                $aloginCredentials["SecretKey2"] = $aUserRecord["secretKey2"];
                                $aloginCredentials["Pass"] = rtrim($this->oCrypt->deCrypt($aUserRecord["userPass"]));
                                $aloginCredentials["URL"] = HOST."/".APPLICATIONPATH."iquestclient/".$aloginCredentials["SecretKey1"]."/".$aloginCredentials["SecretKey2"];
                                
                                $aMailVars["client"] = $aClientData;
                                $aMailVars["credentials"] = $aloginCredentials;
                                
                                $oMail = new BCMail_BCMail();
                                if($oMail->sendMail("login","reInvitation",$aMailVars))
                                {
                                    $this->sStatus = "Ready";
                                }else {
                                    $this->sStatus = "Failed";
                                    
                                }
                                
                              
                            }else 
                                {
                                    $this->aData["sFormMessage"] = "Emailadres is niet bekend";
                                }
                        }
                }
            
            }
    }
    
    private function loadClientDataByEmail($p_sEmail)
    {
        $sSQL = "SELECT * FROM clients where email=:email ";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":email", $p_sEmail);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            $aDecrypted = $this->oCrypt-> multiDecrypt($aResult);
            return $aDecrypted;
        }
    }
    
    private function loadUserRecordByEmail($p_sEmail)
    {
        $sSQL = "SELECT * FROM users where userName=:email AND userLevel='Çlient' ";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":email", $p_sEmail);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            $aDecrypted = $this->oCrypt-> multiDecrypt($aResult);
            return $aDecrypted;
        }
    }
    
    private function checkClientByMail($P_sMail)
    {
        $sMail = $this->oCrypt->enCrypt($P_sMail);
        
        $sSQL = "SELECT userID FROM users where userName=:user AND userBlock='0' AND userLevel='Client'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":user", $sMail);
       
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if(!isset($aResult["userID"]))
        {
            return false;
        }else {
            return true;
        }
    }
    
    
    private  function checkUser($P_sUserName, $p_sPassWord,$p_aKeys)
    {
        $sUser = $this->oCrypt->enCrypt($P_sUserName);
        $sPass = $this->oCrypt->enCrypt($p_sPassWord);
      
        $sSQL = "SELECT * FROM users where userName=:user AND userPass=:pass AND userBlock='0' AND userLevel='Client' AND secretKey1=:key1 AND secretKey2=:key2";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":user", $sUser);
        $oQuery->bindParam(":pass", $sPass);
        
        $sKey1 = rtrim($p_aKeys["key1"]);
        $sKey2 = rtrim($p_aKeys["key2"]);
        $oQuery->bindParam(":key1",$sKey1 );
        $oQuery->bindParam(":key2", $sKey2);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!isset($aResult["userID"]))
        {
            return false;
        }else {
            login_loginHelper::addLoginvarsClient($aResult["userID"],$sUser);
            
            // $iTime = time();
            $sIP = $_SERVER["REMOTE_ADDR"];
            //Update USerrecord
            $sSQL = "UPDATE users SET lastLoginIP=:IP,userSessions=(userSessions+1) WHERE userID=:ID";
            $oQuery = $this->oDB->prepare($sSQL);
            $oQuery->bindParam(":ID", $aResult["userID"]);
            // $oQuery->bindParam(":last",$iTime);
            $oQuery->bindParam(":IP", $sIP);
            $oQuery->execute();
            return true;
        }
        
   }
   
   
   
}