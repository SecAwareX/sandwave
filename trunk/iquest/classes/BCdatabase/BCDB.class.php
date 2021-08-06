<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BClibarys
 * @subpackage BCdatabase
 * @project 
 * @version  1.0
 * @date 2012-09-18
 * @description  : Database connection
 * **/

class BCdatabase_BCDB extends PDO
{
	/**
	 * 
	 * @access private
	 * @var string
	 */
	private $m_sHost;
	
	/**
	 * 
	 * @access private
	 * @var numeric
	 */
	private $m_nPort;
	
	/**
	 * 
	 * @access private
	 * @var string
	 */
	private $m_sDBName;
	
	/**
	 * 
	 * @access private
	 * @var string
	 */
	private $m_sUser;
	
	/**
	 * 
	 * @access private
	 * @var string
	 */
	private $m_sPass;
	
	/**
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->m_nPort = 0;
		$this->m_sDBName = "";
		$this->m_sHost = "";
		$this->m_sPass = "";
		$this->m_sUser = "";
		
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 * @param numeric $p_nPortNumber
	 * @throws BClibarys_BCdatabase_BCDBException
	 */
	public function setPort($p_nPortNumber)
	{
		if(is_numeric($p_nPortNumber))
		{
			$this->m_nPort = $p_nPortNumber;
		}else 
			{
				throw new BCdatabase_BCDBException("Port ".$p_nPortNumber." must be numeric");
			}
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 * @param string $p_sName
	 * @throws BClibarys_BCdatabase_BCDBException
	 */
	public function setDBName($p_sName)
	{
		if(is_string($p_sName))
		{
			$this->m_sDBName = $p_sName;
		}else 
			{
				throw new BCdatabase_BCDBException("DBName must be a string");
			}
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 * @param string $p_SHost
	 * @throws BClibarys_BCdatabase_BCDBException
	 */
	public function setHost($p_SHost)
	{
		if(is_string($p_SHost))
		{
			$this->m_sHost = $p_SHost;
		}else 
			{
				throw new BCdatabase_BCDBException("Host must be a string");
			}
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 * @param string $p_sUser
	 * @throws BClibarys_BCdatabase_BCDBException
	 */
	public function setUser($p_sUser)
	{
		if(is_string($p_sUser))
		{
			$this->m_sUser = $p_sUser;
		}else 
			{
				throw new BCdatabase_BCDBException("User must be a string");
			}
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 * @param string $p_sPass
	 * @throws BClibarys_BCdatabase_BCDBException
	 */
	public function setPass($p_sPass)
	{
		if(is_string($p_sPass))
		{
			$this->m_sPass = $p_sPass;
		}else 
			{
				throw new BCdatabase_BCDBException("Pass must be a string");
			}
	}
	
	/**
	 * connect() : checks first if all mandatory members are set, only pass and port are optional
	 * @access public
	 * @return void
	 * @throws BClibarys_BCdatabase_BCDBException
	 */
	public function connect()
	{
		if($this->m_sDBName == "" || $this->m_sHost == "" || $this->m_sUser == "")
		{
		
			throw new BCdatabase_BCDBException("There is a member not set, check DBname,Host,User.Only Port AND pass are optional");
		}else 
			{
				$sHostAndPort = $this->m_sHost;
				if($this->m_nPort !=0)
				{
					$sHostAndPort.= ";port=".$this->m_nPort;
				}
				
				parent::__construct("mysql:host=$sHostAndPort;dbname=".$this->m_sDBName,$this->m_sUser,$this->m_sPass);
				$this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			}
	}
	
	
	
	
}
?>