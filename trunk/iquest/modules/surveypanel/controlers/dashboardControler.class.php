<?php
/**
 *	dashboardControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 24 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

class Surveypanel_dashboardControler extends BCcontroler_BCControler
{
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
    }
    
    public function showDashboard()
    {
        $this->loadUserData($_SESSION["userID"]);
        $this->aData["aFormFields"] = array();
        
        $aSearchStatussen  = array("Maak een keuze","Overdue","Nog niet uitgenodigd","Uitgenodigd maar nog niks ingevuld","Met open formulieren","Alles gesloten");
        
        
        
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->aData["aFormFields"]["f_serachQuery"] = $_POST["serachQuery"];
            $this->aData["aFormFields"]["f_searchType"] = $_POST["f_searchType"];
            $this->aData["aFormFields"]["f_searchStatus"] = $_POST["f_searchStatus"];
            
            $_SESSION["search"] = $_POST["f_searchType"];
            $_SESSION["query"] = $_POST["serachQuery"];
            $_SESSION["status"] = $_POST["f_searchStatus"];
            
            if(isset($_SESSION["search"])) {
                
                switch($_SESSION["search"]) {
                    case "default" :
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::loadClientsInOverdue($this->oDB);
                        $_SESSION["status"] = 0;
                        $this->aData["aFormFields"]["f_searchStatus"] = 0;
                        break;
                    case "1" :
                        $this->aData["sFiltername"] = "Voornaam : ".$_POST["serachQuery"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::search($this->oDB,$this->oCrypt,"firstName", $_POST["serachQuery"]);
                        $_SESSION["status"] = 0;
                        $this->aData["aFormFields"]["f_searchStatus"] = 0;
                    break;
                    case "2" :
                        $this->aData["sFiltername"] = "Achternaam : ".$_POST["serachQuery"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::search($this->oDB,$this->oCrypt,"lastName", $_POST["serachQuery"]);
                        $_SESSION["status"] = 0;
                        $this->aData["aFormFields"]["f_searchStatus"] = 0;
                    break;
                    case "3" :
                        $this->aData["sFiltername"] = "Email : ".$_POST["serachQuery"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::search($this->oDB,$this->oCrypt,"email", $_POST["serachQuery"]);
                        $_SESSION["status"] = 0;
                        $this->aData["aFormFields"]["f_searchStatus"] = 0;
                    break;
                    case "4" :
                        $this->aData["sFiltername"] = "Bedrijf : ".$_POST["serachQuery"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchCompany($this->oDB,$this->oCrypt, $_POST["serachQuery"]);
                        $_SESSION["status"] = 0;
                        $this->aData["aFormFields"]["f_searchStatus"] = 0;
                    break;
                    case "5" :
                        $this->aData["sFiltername"] = "Status : ".$aSearchStatussen[$_POST["f_searchStatus"]];
                        
                        switch($_POST["f_searchStatus"]) {
                            
                            case "1" :
                               $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::loadClientsInOverdue($this->oDB);
                            break;
                            case "2" :
                              $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchNoInvitation($this->oDB);
                            break;
                            case "3" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchNoFills($this->oDB);
                            break;
                            case "4" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchWithOpen($this->oDB);
                            break;
                            case "5" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchNoOpen($this->oDB);
                            break;
                        }
                        
                        
                    break;
                }
            }
        }else {
           
            if(isset($_SESSION["search"])) {
               
                $this->aData["aFormFields"]["f_serachQuery"] =  (isset($_SESSION["query"])?$_SESSION["query"]:"");
                $this->aData["aFormFields"]["f_searchType"] = (isset($_SESSION["search"])?$_SESSION["search"]:"");
                $this->aData["aFormFields"]["f_searchStatus"] = (isset($_SESSION["status"])?$_SESSION["status"]:"");
                
                switch($_SESSION["search"]) {
                    case "default" :
                       
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::loadClientsInOverdue($this->oDB);
                    break;
                    case "1" :
                       
                        $this->aData["sFiltername"] = "Voornaam : ".$_SESSION["query"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::search($this->oDB,$this->oCrypt,"firstName", $_SESSION["query"]);
                        break;
                    case "2" :
                       
                        $this->aData["sFiltername"] = "Acternaam : ".$_SESSION["query"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::search($this->oDB,$this->oCrypt,"lastName", $_SESSION["query"]);
                    case "3" :
                       
                        $this->aData["sFiltername"] = "Email : ".$_SESSION["query"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::search($this->oDB,$this->oCrypt,"email", $_SESSION["query"]);
                        break;
                    case "4" :
                       
                        $this->aData["sFiltername"] = "Bedrijf : ".$_SESSION["query"];
                        $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchCompany($this->oDB,$this->oCrypt, $_SESSION["query"]);
                        break;
                   
                    case "5" :
                        $this->aData["sFiltername"] = "Status : ".$aSearchStatussen[$_SESSION["status"]];
                        
                        switch($_SESSION["status"]) {
                            
                            case "1" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::loadClientsInOverdue($this->oDB);
                                break;
                            case "2" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchNoInvitation($this->oDB);
                                break;
                            case "3" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchNoFills($this->oDB);
                                break;
                            case "4" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchWithOpen($this->oDB);
                                break;
                            case "5" :
                                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::searchNoOpen($this->oDB);
                                break;
                        }
                        
                        
                        break;
                }
                
                
                
            }else {
                $_SESSION["search"] = "";
                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::loadClientsInOverdue($this->oDB);
                
                $this->aData["sFiltername"] = "Status : Overdue";
                $aClients = surveypanel_models_iquestforms_iquestFormDatamapper::loadClientsInOverdue($this->oDB);
                $this->aData["aFormFields"]["f_serachQuery"] = "";
                $this->aData["aFormFields"]["f_searchType"] = "5";
                $this->aData["aFormFields"]["f_searchStatus"] = "1";
                
            }
           
        }
        
        
        
        if(!empty($aClients))
        {
            foreach($aClients AS $aClient)
            {
                $this->aData["clients"][] = $this->oCrypt->multiDecrypt($aClient);
            }
            
            $this->sStatus = "Ready";
            return true;
        } else {
            $this->aData["clients"] = "Geen clienten gevonden";
        }
    }
    
    protected function loadUserData($p_iUserID)
    {
        $sSQL = "SELECT * FROM users where userID=:user ";
        
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":user", $p_iUserID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            $aDecrypted = $this->oCrypt-> multiDecrypt($aResult);
            $this->aData["userScreenname"] = $aDecrypted["userScreenname"];
        }
    }
}