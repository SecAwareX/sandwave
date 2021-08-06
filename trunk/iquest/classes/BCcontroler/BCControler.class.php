<?php
/**
 *  BCControler.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 24 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

class BCcontroler_BCControler
{
    protected $aData;
    
    protected $sStatus;
    
    protected $oDB;
    
    protected $oCrypt;
    
    protected $oRequest;
    
    public function __construct($p_oRequest, $p_oDB,$p_oCrypt = null)
    {
        $this->aData = array();
        $this->sStatus = "Init";
        $this->oDB = $p_oDB;
        $this->oCrypt = $p_oCrypt;
        $this->oRequest = $p_oRequest;
    }
    
    public function addData($p_sValueName,$p_mValue)
    {
        $this->aData[$p_sValueName] = $p_mValue;
    }
    
    public function getData($p_sValueName)
    {
        if(isset($this->aData[$p_sValueName]))
        {
            return $this->aData[$p_sValueName];
        }
    }
    
    public function getStatus()
    {
        return $this->sStatus;
    }
}