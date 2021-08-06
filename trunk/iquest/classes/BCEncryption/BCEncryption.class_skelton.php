<?php
/**
 * 
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package encryption-v.1.0
 * @subpackage 
 * @project 
 * @version  1.0
 * @date 11 nov. 2014
 * @description  : encryption class encrypts data en decrypts data supports only AES-256-CBC at this moment.
 *                 This class is changed for iQuest because mycrypt not longer is supported in php. 
 *                 We have tried to keep the class structure the same so we don't have do a large amount of refactoring work.
 * 
 * *
 */
class BCEncryption
{
	
	/**
	 * @access private
	 * @var resource : The cipher(algorithm) of choice, default it is RIJNDAEL 256 and default mode is cbc
	 */
	private $m_rCipher;
	
	/**
	 * @access private 
	 * @var integer : the lenght of a key supported by the chosen algorithm and mode
	 */
	private $m_iKeySize;
	
	/**
	 * @access private 
	 * @var string : The secret key 
	 */
	private $m_sSecretKey;
	
	/**
	 * @access private 
	 * @var string : vector string 
	 */
	private $m_sVector;
	
	/**
	 * 
	 *  constructor :
	 *  @access public
	 *  @return  void
	 */
	public function __construct()
	{
		$this->m_rCipher = NULL;
		$this->m_iKeySize = 0;
		$this->m_sSecretKey = "";
		$this->m_sVector = "";
		
		
		$this->setCipher("AES-256-CBC");
		
	}
	
	/**
	 *  checkEncryptionModule(): checks if the openssl module is supported on this server
	 *  @access private
	 *  @return void
	 *  @throws Exception
	 */
	private function checkEncryptionModule()
	{
	    $aExtentions = get_loaded_extensions();
	    if(!in_array("mcrypt",$aExtentions))
	    {
	        throw new Exception("Mycrypt is not supported on this server");
	    }
	}
	
	/**
	 *  checkCipher() : checks if the cipher exists and is available
	 *  @access private
	 *  @param string $p_sCipher : name of the chosen cipher (algorithm)
	 *  @return  void
	 *  @throws Exception
	 */
	private function checkCipher($p_sCipher)
	{
		
	}
	
	/**
	 *  checkCipher() : checks if the mode exists and is available
	 *  @access private
	 *  @param string $p_sMode : name of the chosen mode
	 *  @return  void
	 *  @throws Exception
	 */
	private function checkMode($p_sMode)
	{
		
	}
	
	/**
	 *  isBase64() : checks if a string has a base64encoded format
	 *  @access private
	 *  @param string 
	 *  @return boolean
	 */
	private function isBase64($p_sString)
	{
		if(preg_match("/^[a-zA-Z0-9\/\r\n+]*={1,2}$/",$p_sString))
		{
			return TRUE;
		}else 
			{
				return FALSE;
			}
	}
	
	/**
	 *  createVector() : creates a init vector with the right lenght for the algorithm
	 *  @access public
	 *  @return  string : base64 encoded
	 */
	public function createVector()
	{
		//return base64_encode(mcrypt_create_iv(mcrypt_enc_get_iv_size($this->m_rCipher), MCRYPT_RAND));
	}
	
	/**
	 *  createSecretKey() : creates a secret key with the right lenght for the algorithm of chose
	 *  @access public
	 *  @return  string
	 */
	public function createSecretKey()
	{
		
		
	}
	
	/**
	 *  setCipher() : opens the module with the algorithm and mode of chose
	 *  @access public
	 *  @param string $p_sCipher : cipher name
	 *  @param string $p_sMode : the mode name
	 *  @return void
	 *  @throws Exception
	 */
	public function setCipher($p_sCipher,$p_sMode)
	{
		
	}
	
	/**
	 *  setSecretKey();
	 *  @access public
	 *  @param string $p_sKey
	 *  @return  void
	 *  @throws Exception
	 */
	public function setSecretKey($p_sKey)
	{
		
	}
	
	/**
	 *  setVector(): 
	 *  @access public
	 *  @param string $p_sVector : base64 encoded string
	 *  @return void
	 *  @throws Exception
	 */
	public function setVector($p_sVector)
	{
		
	}
	
	/**
	 *  encrypt() : encrypts the string
	 *  @access public
	 *  @param string $p_sString : data to encrypt
	 *  @return  string : base64 format
	 */
	public function encrypt($p_sString)
	{
		
	}
	
	/**
	 *  decrypt(): decrypts a string
	 *  @access public
	 *  @param string $p_sString : base64 formatted
	 *  @return string
	 */
	public function decrypt($p_sString)
	{
		
	}
	
	/**
	 *  multiEncrypt() : encrypts a array entirely or when only the given offset or when a offset not exluded
	 *  @access public
	 *  @param array $p_aData : mandatory array with the data to be encrypted 
	 *  @param string $p_sVarname : optional name for the offset that must be encrypted
	 *  @param array $p_aExclusions : optional offset name that not will be encrypted
	 *  @return arrays
	 */
	public function multiEncrypt($p_aData,$p_sVarname ="",$p_aExclusions = array())
	{
		
	}
	
	/**
	 *  multiDecrypt() : Decrypts a array entirely or when only the given offset or when a offset not exluded
	 *  @access public
	 *  @param array $p_aData : mandatory array with the data to be decrypted 
	 *  @param string $p_sVarname : optional name for the offset that must be decrypted
	 *  @param array $p_aExclusions : optional offset name that not will be decrypted
	 *  @return arrays
	 */
	public function multiDecrypt($p_aData,$p_sVarname ="",$p_aExclusions = array())
	{
		
	}
}