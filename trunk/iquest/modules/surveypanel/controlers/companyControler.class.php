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

class surveypanel_companyControler extends surveypanel_dashboardControler
{
    public function __construct($p_oRequest, $p_oDB, $p_oCrypt)
    {
        parent::__construct($p_oRequest, $p_oDB, $p_oCrypt);
        $this->loadUserData($_SESSION["userID"]);
    }
    
    public function showCompanys()
    {
        
        $sSQL = "SELECT *,(SELECT COUNT(*) FROM forms WHERE companyID = companys.companyID) AS formCount,
                (SELECT COUNT(*) FROM clients WHERE companyID = companys.companyID) AS clientCount
                 FROM companys ";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            foreach($aResult AS $aUser)
            {
                $this->aData["companys"][] = $this->oCrypt->multiDecrypt($aUser);
            }
            
            $this->sStatus = "Ready";
            return true;
        
        }else
            {
                $this->aData["companys"] = "Geen bedrijven gevonden";
            }
    }
    
    public function addCompany()
    {
        BChelpers_formHandler::addFieldCheckNew("f_CompanyName","bedrijfsnaam",true,"surveypanel_checkLibary::checkCompanyname","Geef een geldige bedrijfsnaam op");
    
        $this->aData["sFormSuccesMessage"] = "";
        $this->aData["sFormMessage"] = "";
        $this->aData["submit_change_dis"] = "disabled";
        $this->aData["submit_add_dis"] = "";
        
        if(BChelpers_formHandler::formSend("doAddCompany"))
        {
            if(!BChelpers_formHandler::handleForm())
            {
                $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                $this->aData["f_CompanyName"] = BChelpers_formHandler::getValue("f_CompanyName");
            }else
                {
                    //Form is validated, so save the new company
                    
                    //Check if ther eis a company with the same name, if not save
                    if(!$this->checkCompanyByName(BChelpers_formHandler::getValue("f_CompanyName")))
                    {
                        $iCompanyID = $this->saveNewCompany(BChelpers_formHandler::getValue("f_CompanyName"));
                        if($iCompanyID > 0)
                        {
                            $this->aData["sFormSuccesMessage"] = "Bedrijf is succesvol toegevoegd";
                            $this->aData["ID"] = $iCompanyID;
                        }else
                            {
                                $this->aData["sFormMessage"]= "Het opslaan is niet gelukt";
                            }
                            
                            $this->aData["submit_change_dis"] = "";
                            $this->aData["submit_add_dis"] = "disabled";
                    }else {
                        $this->aData["sFormMessage"]= "Er bestaat al een bedrijf met de naam ".BChelpers_formHandler::getValue("f_CompanyName");
                        $this->aData["submit_change_dis"] = "disabled";
                        $this->aData["submit_add_dis"] = "";
                    }
                    
                    
                    $this->aData["f_CompanyName"] = BChelpers_formHandler::getValue("f_CompanyName");
                   
                }
        }else
            {
                $this->aData["f_CompanyName"] = "";
            }
    }
    
    
    public function changeCompany()
    {
        //Check if company exists
        if($this->checkCompanyByID($this->oRequest->getParameter(2)))
        {
            //Setting up the form
            BChelpers_formHandler::addFieldCheckNew("f_CompanyName","Bedrijfsnaam",true,"surveypanel_checkLibary::checkCompanyname","Bedrijfsnaam : Geef een geldige bedrijfsnaam op");
            
            $this->aData["sFormSuccesMessage"] = "";
            $this->aData["sFormMessage"] = "";
            $this->aData["submit_change_dis"] = "";
            $this->aData["submit_add_dis"] = "";
            
            if(BChelpers_formHandler::formSend("doChangeCompany"))
            {
                if(!BChelpers_formHandler::handleForm())
                {
                    $this->aData["sFormMessage"] = BChelpers_formHandler::getFormErrorsAsString();
                    $this->aData["f_CompanyName"] = BChelpers_formHandler::getValue("f_CompanyName");
                    $this->aData["CompanyID"] = $this->oRequest->getParameter(2);
                }else
                    {
                        //form is validated, so save the company
                       if($this->saveCompany($this->oRequest->getParameter(2)) == 1)
                       {
                           $this->aData["sFormSuccesMessage"] = "Bedrijf is succesvol gewijzigd";
                       }
                       
                       $this->aData["f_CompanyName"] = BChelpers_formHandler::getValue("f_CompanyName");
                       $this->aData["CompanyID"] = $this->oRequest->getParameter(2);
                    }
            }else
                {
                    //Form is not send yet
                    $aCompany = $this->loadCompany($this->oRequest->getParameter(2));
                    $this->aData["f_CompanyName"] = rtrim($aCompany["companyName"]);
                    $this->aData["CompanyID"] = $aCompany["companyID"];
                }
            
        }else {
            $this->aData["sFormMessage"] = "Bedrijf bestaat niet";
        }
    }
    
    
    public function changeCompanyStatus()
    {
        if($this->checkCompanyByID($this->oRequest->getParameter(2)))
        {
            if($this->oRequest->getParameter(3) == 0 || $this->oRequest->getParameter(3) == 1)
            {
                $this->changeStatus($this->oRequest->getParameter(2),$this->oRequest->getParameter(3));
                $this->oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/companys");
            }
        }
    }
    
    public function loadActiveCompanys()
    {
        $sSQL = "SELECT * FROM companys WHERE active='1'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($aResult))
        {
            foreach($aResult AS $aCompany)
            {
                $this->aData["companys"][] = $this->oCrypt->multiDecrypt($aCompany);
            }
           
            $this->sStatus = "Ready";
            return true;
            
        }
    }
    
    public function loadCompanyForms()
    {
        $sSQL = "SELECT * FROM companys WHERE active='1'";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->execute();
        
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($aResult AS $iKey =>$aCompany)
        {
            $sSQL = "SELECT * FROM forms WHERE companyID=:ID AND active='1'";
            $oQuery = $this->oDB->prepare($sSQL);
            $oQuery->bindParam(":ID", $aCompany["companyID"]);
            $oQuery->execute();
            
            $aForms = $oQuery->fetchAll(PDO::FETCH_ASSOC);
            $aResult[$iKey]["forms"] = $aForms;
        }
        
        return $aResult;
    }
    
    private function changeStatus($p_iCompanyID,$p_iStatus)
    {
        $sSQL = "UPDATE companys SET active=:State WHERE companyID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iCompanyID);
        $oQuery->bindParam(":State", $p_iStatus);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
    private function loadCompany($p_iCompanyID)
    {
        $sSQL = "SELECT * FROM companys WHERE companyID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iCompanyID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        
        return $this->oCrypt->multiDecrypt($aResult);
    }
    
    private function checkCompanyByID($p_iCompanyID)
    {
        
        $sSQL = "SELECT count(companyID) AS iCompany FROM companys WHERE companyID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iCompanyID);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCompany"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function checkCompanyByName($p_sName)
    {
        $sCompany = $this->oCrypt->enCrypt($p_sName);
        $sSQL = "SELECT count(companyID) AS iCompany FROM companys WHERE companyName=:Name";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":Name", $sCompany);
        $oQuery->execute();
        
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
        if($aResult["iCompany"] > 0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    private function saveNewCompany($p_sName)
    {
        $sCompany = $this->oCrypt->enCrypt($p_sName);
        $sSQL = "INSERT INTO companys (companyName) VALUES (:Name)";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":Name", $sCompany);
        $oQuery->execute();
        
        return $this->oDB->lastInsertID();
    }
    
    private function saveCompany($p_iCompanyID)
    {
        $sCompany = $this->oCrypt->enCrypt(BChelpers_formHandler::getValue("f_CompanyName"));
        $sSQL = "UPDATE companys SET companyName=:Name WHERE companyID=:ID";
        $oQuery = $this->oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $p_iCompanyID);
        $oQuery->bindParam(":Name", $sCompany);
        $oQuery->execute();
        
        return $oQuery->rowCount();
    }
    
}