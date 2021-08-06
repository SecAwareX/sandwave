<?php
/**
 *	adminLoginControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 24 aug. 2018
 *  Project : 
 * 	Package : 
 *  Version : 
 * 
 */

class login_adminLoginControler extends BCcontroler_BCControler
{
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
    }
    
    public function login()
    {
        
        //Check if the request IP is allowed
        if($this->checkIP())
        {
            
          
            //Setting UP The form 
            BChelpers_formHandler::addFieldCheck("Username",true,"login_checkLibary::checkUserNameAdmin");
            BChelpers_formHandler::addFieldCheck("Pass",true,"login_checkLibary::checkPass");
            if(BChelpers_formHandler::formSend("doAdminLogin"))
            {
                //Check if current sessionIP is lockt ( to manny attempts)
                
                //Create SessionLOg
                $this->createLoginSession();
                //CheckAttempts
                if(!$this->checkLoginAttempts())
                {
                    //There are to many attempts, account is locked
                    $this->oRequest->redirect(HOST."/".APPLICATIONPATH."error/accesblockt");
                }
                
                
                if(!BChelpers_formHandler::handleForm())
                {
                   
                    if(count(BChelpers_formHandler::$aFormErrors["mandatoryFields"]) > 0 )
                    {
                        $this->aData["sFormMessage"] = "Niet alle verplichte velden zijn ingevuld";
                    }elseif(count(BChelpers_formHandler::$aFormErrors["formatErrors"]) > 0 )
                        {
                            $this->aData["sFormMessage"] = "Niet alle velden zijn correct ingevuld";
                        }
                        
                    //Check Attempts
                   $this->updateLoginSession(trim(BChelpers_formHandler::getValue("Username")));
                        
                }else 
                    {
                       if($this->checkUser(trim(BChelpers_formHandler::getValue("Username")),trim( BChelpers_formHandler::getValue("Pass"))))
                        {
                            //User is validated, so create session vars and redirect to the dashboard
                            $this->updateLoginSession(trim(BChelpers_formHandler::getValue("Username")));
                            $this->oRequest->redirect("https://iquest.mareis.nl/surveypanel/");
                           //$this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel");
                            return true;
                            
                        }else{
                            echo "test";
                            //Attempt failed, so update loginsession
                            $this->updateLoginSession(trim(BChelpers_formHandler::getValue("Username")));
                            $this->aData["sFormMessage"] = "Combinatie gebruikersnaam en wachtwoord is onjuist";
                            return false;
                        }
                        
                    }
            }else
                 {
                   //$this->aData["sFormMessage"] = "Form not submityted";
                     $this->aData["sFormMessage"] = "";
                  }
                  
        }else 
            {
                $oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
                $oRequest->redirect(HOST."/".APPLICATIONPATH."error/accesdenied");
            }
    }
    
    
    public function logOut()
    {
        if(login_loginHelper::unsetSessionVars())
        {
            $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/login");
            //echo HOST."/".APPLICATIONPATH."surveypanel/login";
        }
    }
    
    public function checkLoginAttempts()
    {
        $sSessionID = session_id();
        $sSessionIP = $_SERVER["REMOTE_ADDR"];
        
        $sSQL = "SELECT sessionAttempts FROM loginsessions WHERE sessionID=:sessionID AND sessionIP=:sessionIP";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":sessionID", $sSessionID);
        $oQuery->bindParam(":sessionIP", $sSessionIP);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["sessionAttempts"] >= MAX_ATTEMPTS)
        {
            $sSQL = "UPDATE loginsessions SET userBlock='1' WHERE sessionID=:ID AND sessionIP=:ip";
            $oQuery = $this->oDB->prepare($sSQL);
            $oQuery->bindParam(":ID", $sSessionID);
            $oQuery->bindParam(":ip", $sSessionIP);
            $oQuery->execute();
            
            return false;
        }else {
            return true;
        }
    }
    
    public function createLoginSession()
    {
        $sSessionID = session_id();
        $sSessionIP = $_SERVER["REMOTE_ADDR"];
      
        $sSQL = "SELECT COUNT(loginSessionID) AS session FROM loginsessions WHERE sessionID=:sessionID AND sessionIP=:sessionIP";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":sessionID", $sSessionID);
        $oQuery->bindParam(":sessionIP", $sSessionIP);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if($aResult["session"] == 0)
        {
            $sSQL = "INSERT INTO loginsessions (sessionID,sessionIP) VALUES (:sessionID,:sessionIP)";
            $oQuery = $this->oDB->prepare($sSQL);
            
            $oQuery->bindParam(":sessionID", $sSessionID);
            $oQuery->bindParam(":sessionIP", $sSessionIP);
            $oQuery->execute();
        }
    }
    
    public function updateLoginSession($p_sUserName)
    {
        $sUser = $this->oCrypt->enCrypt($p_sUserName);
        $sSQL = "UPDATE loginsessions SET lastUserName=:user,sessionAttempts=(sessionAttempts+1) WHERE sessionID=:ID AND sessionIP=:ip";
        $oQuery = $this->oDB->prepare($sSQL);
        
        $sSessionID = session_id();
        $sSessionIP = $_SERVER["REMOTE_ADDR"];
        $oQuery->bindParam(":ID",$sSessionID);
        $oQuery->bindParam(":ip",$sSessionIP);
        $oQuery->bindParam(":user",$sUser);
        $oQuery->execute();
        
    }
    
    private  function checkUser($P_sUserName, $p_sPassWord)
    {
        $sUser = $this->oCrypt->enCrypt($P_sUserName);
        $sPass = $this->oCrypt->enCrypt($p_sPassWord);
        $sSQL = "SELECT userID FROM users where userName=:user AND userPass=:pass AND userLevel='Admin' AND userBlock='0'";
      
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":user", $sUser);
        $oQuery->bindParam(":pass", $sPass);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      
        if(!isset($aResult["userID"]))
        {
            return false;
        }else {
            login_loginHelper::addLoginvarsAdmin($aResult["userID"]);
            
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
    
    public function registerIP()
    {
        
       //Check if there is already a registration request
        $sIP = $_SERVER["REMOTE_ADDR"];
        $sSQL = "SELECT IP FROM ips where IP='$sIP'";
        $oQuery = $this->oDB->query($sSQL);
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(empty($aResult))
        {
            //There is no registration request, so we can register
            $sRegKey = md5($_SERVER["REMOTE_ADDR"].time().md5(time()));
            $sSQL = 'INSERT INTO ips(ip,regKey,ipBlock)VALUES(:IP,:REG,1)';
            $oQuery = $this->oDB->prepare($sSQL);
            $oQuery->bindParam(":IP", $sIP);
            $oQuery->bindParam(":REG", $sRegKey);
           $oQuery->execute();
            
            if($oQuery->rowCount() > 0)
            {
                //Insert succeeded
                //Send mail
                $aMailVars = array();
                $aMailVars["IP"] = $_SERVER["REMOTE_ADDR"];
                $aMailVars["URL"] = HOST.APPLICATIONPATH."/login/addip/".$sRegKey;
                $oMail = new BCMail_BCMail();
                if($oMail->sendMail("login","registerIP",$aMailVars))
                {
                    $this->sStatus = "Ready";
                }else {
                    $this->sStatus = "Failed";
                }
                
            }
        }else 
            {
                //There is already a registration request, 
                $this->sStatus = "Failed";
            }
        
    }
    
    public function addIP()
    {
        $oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
        $sRegKey = $oRequest->getParaMeter(2);
        $sIP = $_SERVER["REMOTE_ADDR"];
        
        //check the request
        //check the format of the regKey, if oke further processing els redirect to errorpage 
        if(!login_checkLibary::checkIPRegKey($sRegKey))
        {
            $oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
            $oRequest->redirect(HOST."/".APPLICATIONPATH."error/accesdenied");
        }
        
        
        $sSQL = "SELECT ipID FROM ips where IP='$sIP' AND ipBlock = 1 AND regKey='$sRegKey' ";
        $oQuery = $this->oDB->query($sSQL);
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        //If the request is correct , redirect to the login
        //If not correct redirect to a errorpage
        if(!empty($aResult))
        {
            //There was a match, so update the record for unlocking the IP and redirect to the login
            $sSQL = "UPDATE ips SET ipBlock = 0 WHERE ipID =:ID";
            $oQuery = $this->oDB->prepare($sSQL);
            $oQuery->bindParam(":ID", $aResult["ipID"]);
            $oQuery->execute();
            
            $oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
            $oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/login");
        }else 
            {
                //There was nog request that matches so redirect to error page 
                $oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
                $oRequest->redirect(HOST."/".APPLICATIONPATH."error/accesdenied");
            }
        
            
        
    }
    
    
    private function checkIP()
    {
        
        $sIP = $_SERVER["REMOTE_ADDR"];
        $sSQL = "SELECT IP FROM ips where IP='$sIP' AND ipBlock = 0 ";
        $oQuery = $this->oDB->query($sSQL);
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if(empty($aResult))
        {
            return false;
        }else
            {
                return true;
            }
     }
}