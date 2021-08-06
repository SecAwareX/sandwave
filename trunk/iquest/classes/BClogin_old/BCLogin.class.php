<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package Classes
 * @subpackage BClogin
 * @project iQuest
 * @version  1.0
 * @date 2012-10-07
 * @description :
 *  - login class 
 *
 */
class BClogin_BCLogin_old 
{ 
	
	/**
	 * Number of allowed login attemps
	 */
	const MAX_FAILEDATTEMPTS= 20;
	
	/**
	 * @access protected 
	 * @var string : is the username input from a form
	 */
	protected $m_sUserNameInput;
	
	/**
	 * @access protected
	 * @var string : is the password input from a form
	 */
	protected $m_sPasswordInput;
	
	/**
	 * @access protected
	 * @var string : 
	 */
	protected $m_sUserName;
	
	/**
	 * @access protected
	 * @var integer  : 
	 */
	protected $m_iUserID;
	
	/**
	 * @protected 
	 * @var string : het wachtwoord opgehaald uit db
	 */
	protected $m_sPassword;
	
	/**
	 * @access private
	 * @var integer
	 */
	private $m_iStatus;
	
	/**
	 * @access private
	 * @var string met eventuele errormessage
	 */
	private $m_sErrorMessage;
	
	/**
	 * constructor()
	 * @access public 
	 * @return void
	 */
	public function __construct()
	{
		$this->m_sUserNameInput="";
		$this->m_sPassWordInput="";
		$this->m_sUserName="";
		$this->m_sPassWord="";
		$this->m_iUserID=-1;
		(!isset($_SESSION["ATTEMPTS"])?$_SESSION["ATTEMPTS"]=0:"");
		$this->m_iStatus = 0;
	}
	
	/**
	 * handleLogin(): handelt daadweerkelijk de loginprocedure af, als er meer dan de waarde van max_FAILEDATTEPTS een foutieve combinatie wordt opgegevne dan altijd false
	 * inloggen is dan pas weer mogelijk met nieuwe sessie
	 * @access public 
	 * @return boolean 
	 * @todo : uitzoeken of beveiliging afdoende is tegen bruteforce etc
	 */
	public function handleLogin()
	{
		$bLogin=FALSE;
		if($_SESSION["ATTEMPTS"] > self::MAX_FAILEDATTEMPTS)
		{
			$this->m_iStatus= 1;
			$this->m_sErrorMessage="inloggen mislukt,controleer username en wachtwoord";
			$bLogin=FALSE;
		}else
			{//ingevulde vars vergelijken met de vars uit de db
				if($this->compare())
				{
					$_SESSION["LogedIn"]=TRUE;
					$_SESSION["UserID"]=$this->m_iUserID;
					$_SESSION["IP"]=$_SERVER["REMOTE_ADDR"];
					$_SESSION["User_Agent"]=$_SERVER["HTTP_USER_AGENT"];
					$bLogin=TRUE;
				}else
					{
						//vergelijk mislukt,login mislukt 
						$_SESSION["ATTEMPTS"]++;
						$bLogin=FALSE;
					}
			}//else
				//{
					
					//$this->m_iStatus=self::ERROR;
				//	//$bLogin=FALSE;
				//}
				
		return $bLogin;
	}
	
	
	/**
	 * setFormInput():set de vars om te vergelijken met de opgehaalde vars uit de db,string controle is reeds gebeurt in de form 
	 * @access public
	 * @return void
	 */
	public function setFormInput($p_sUsername,$p_sPassword)
	{
		$this->m_sUserNameInput= trim($p_sUsername);
		$this->m_sPassWordInput= trim($p_sPassword);
	}
	
	/**
	 * setFormInput():set de vars om te vergelijken met de opgehaalde vars uit de db,string controle is reeds gebeurt in de form 
	 * @access public
	 * @return void
	 */
	public function setUserData($p_iUserID,$p_sUsername,$p_sPassword)
	{
		$this->m_iUserID = $p_iUserID;
		$this->m_sUserName= trim($p_sUsername);
		$this->m_sPassWord= trim($p_sPassword);
	}
	
	/**
	 * vergelijkt de uit de db opgehaalde username & password met de ingevoerde Username en password
	 * @access private
	 * @return boolean vergelijking geslaag TRUE andersFALSE
	 */
	private function compare()
	{ 
		$bCompare=FALSE;
		
		if (($this->m_sUserNameInput == $this->m_sUserName) && (md5($this->m_sPassWordInput) == $this->m_sPassWord))
		{
			$bCompare=TRUE;
		}else
			{
				$bCompare=FALSE;
		
				
			}
			
		return $bCompare;
	}
	
	/**
	 *  getStatus() : if returd 1 than the max attemps reached
	 *  @access public
	 *  @return  integer
	 */
	public function getStatus()
	{
		return $this->m_iStatus;
	}
}