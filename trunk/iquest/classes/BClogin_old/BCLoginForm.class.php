<?php
class BClibarys_BClogin_BCLoginForm extends BClibarys_BCForm_Form
{
	const USERNAMETYPE_EMAIL = 0;
	const USERNAMETYPE_NAME = 1;
	
	private $m_iUsernameType;
	
	public function __construct($p_iUsernameType,$p_sAction)
	{
		parent::__construct("LoginForm",$p_sAction,self::POST);
		$this->m_sEncType = "";
		$this->m_iUsernameType = $p_iUsernameType;
	}
	
	public function buildForm()
	{
		$sCheck = ($this->m_iUsernameType == self::USERNAMETYPE_EMAIL?"checkUserEmail":"checkUsername");
		$this->setSubMitValue("Login");
		
		$this->addField(BClibarys_BCForm_FieldFactory::TEXT,array("name"=>"username","label"=>"Gebruikersnaam :","value"=>(isset($_POST["username"])?$_POST["username"]:""),"placeholder"=>"Vul uw gebruikersnaam in"),TRUE,$sCheck,"BCCheckLibaryLogin");
		$this->addField(BClibarys_BCForm_FieldFactory::PASSWORD,array("name"=>"password","label"=>"Wachtwoord :","value"=>(isset($_POST["password"])?$_POST["password"]:""),"placeholder"=>"Vul uw wachtwoord in"),TRUE,"checkPassword","BCCheckLibaryLogin");
		
		$this->addField(BClibarys_BCForm_FieldFactory::SUBMIT,array("name"=>"Login","value"=>"inloggen","label"=>""));
	}
	
	
	
	
	/**
    * isValid() : overload de parent methode
    * eerst kijken of de opgegeven validatiefile in deze methode voorkomt, zo niet dan in de parentclass kijken of die voorkomt.
    * Als de opgegeven file in geen van beide voorkomt exception
    * @access protected
    * @return boolean True als de validatie is geslaagd anders FALSE
    */
   protected function isValid ($p_sValidationFile,$p_sFunctionName,$p_sValue)
	{
		
		$bValid=FALSE;
		switch ($p_sValidationFile)
		{
			case "BCCheckLibaryLogin" :
			if(BClibarys_BClogin_BCCheckLibaryLogin::$p_sFunctionName($p_sValue))
			{
				$bValid=TRUE;
			}
			break;
			default: if(parent::isValid($p_sValidationFile,$p_sFunctionName,$p_sValue))
					 {
					 	$bValid=TRUE;
					 }
		
		}
		
	return $bValid;
	}
}
?>