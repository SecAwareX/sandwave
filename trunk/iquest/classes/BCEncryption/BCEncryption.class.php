<?php
/**
 * 
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package encryption-v.1.0
 * @subpackage 
 * @project 
 * @version  1.0
 * @date 11 nov. 2014
 * @description  : encryption class encrypts data en decrypts data 
 * 
 * @todo :  - lenght testing
 * 		    - Testing for the other algorithms than the default rijndael-256 in cbc mode
 * 
 * changelog : 07-09-2018 : multiDecrypt rtrim toegeveogd
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
		//first we check if the module is available 
		$this->checkEncryptionModule();
		
		$this->m_rCipher = NULL;
		$this->m_iKeySize = 0;
		$this->m_sSecretKey = "";
		$this->m_sVector = "";
		$this->setCipher("rijndael-256","cbc");
		
	}
	
	/**
	 *  checkEncryptionModule(): checks if the mcrypt module is supported on this server
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
		$aAlgorithms =  mcrypt_list_algorithms(ini_get("mcrypt.algorithms_dir"));
		if(!in_array($p_sCipher,$aAlgorithms))
		{
			$sAllowed = implode(" - ",$aAlgorithms);
			throw new Exception($p_sCipher." is a algorithm wat is not available. Possibilities are : ".$sAllowed);
		}
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
		$aModes = mcrypt_list_modes(ini_get("mcrypt.modes_dir"));
		if(!in_array($p_sMode,$aModes))
		{
			$sAllowed = implode(" - ",$aModes);
			throw new Exception($p_sMode." is a mode wat is not available. Possibilities are : ".$sAllowed);
		}
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
		return base64_encode(mcrypt_create_iv(mcrypt_enc_get_iv_size($this->m_rCipher), MCRYPT_RAND));
	}
	
	/**
	 *  createSecretKey() : creates a secret key with the right lenght for the algorithm of chose
	 *  @access public
	 *  @return  string
	 */
	public function createSecretKey()
	{
		
		//create array with chars
		$aChars = array();
		for($i=35;$i<123;$i++)
		{
			$aChars[] =  chr($i);
		}
			
		//randomize the chars
		shuffle($aChars);
		$sTempKey_1 = '';
		$sTempKey_2 = '';
			
		//construct two Temp keys
		for($k=0;$k<50;$k++)
		{
			$sTempKey_1 .= $aChars[mt_rand(0,80)];
			$sTempKey_2 .= $aChars[mt_rand(0,80)];
		}
		
		//Construct the finaly key
    	$sKey1 = md5($sTempKey_1);
   	 	$sKey2 = md5($sTempKey_2);
   	 		
   	 	//Construct the finaly key
    	$sKey1 = md5($sTempKey_1);
   	 	$sKey2 = md5($sTempKey_2);

    	$sKey = substr($sKey1, 0, $this->m_iKeySize/2) . substr(strtoupper($sKey2), (round(strlen($sKey2) / 2)), $this->m_iKeySize/2);
		$sKey = substr($sKey.$sKey1.$sKey2.strtoupper($sKey1),0,$this->m_iKeySize);
			
  		return $sKey;
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
		$this->checkCipher($p_sCipher);
		$this->checkMode($p_sMode);
		
		if(!$this->m_rCipher = mcrypt_module_open($p_sCipher,ini_get("mcrypt.algorithms_dir"), $p_sMode,ini_get("mcrypt.modes_dir")))
		{
			throw new Exception("Opening the cipher is failed");
		}else 
			{
				if(!$this->m_iKeySize = mcrypt_enc_get_key_size($this->m_rCipher))
				{
					throw new Exception("Setting the keysize is failed");
				}
				
			}
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
		if(is_string($p_sKey) && $p_sKey !="")
		{
			$this->m_sSecretKey = $p_sKey;
		}else 
			{
				throw new Exception("Key must be a string and can't be empty");
			}
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
		if($this->isBase64($p_sVector))
		{
			$this->m_sVector = base64_decode($p_sVector);
		}else 
			{
				throw new Exception("setVector expects a base64 encoded string");
			}
	}
	
	/**
	 *  encrypt() : encrypts the string
	 *  @access public
	 *  @param string $p_sString : data to encrypt
	 *  @return  string : base64 format
	 */
	public function encrypt($p_sString)
	{
		if(is_string($p_sString) && $p_sString !="")
		{
			$sEncrypted = "";
			mcrypt_generic_init($this->m_rCipher,$this->m_sSecretKey,$this->m_sVector);
			$sEncrypted = mcrypt_generic($this->m_rCipher,$p_sString);
			mcrypt_generic_deinit($this->m_rCipher);
		
			return base64_encode($sEncrypted);
		}
	}
	
	/**
	 *  decrypt(): decrypts a string
	 *  @access public
	 *  @param string $p_sString : base64 formatted
	 *  @return string
	 */
	public function decrypt($p_sString)
	{
		if($this->isBase64($p_sString))
		{
			$sDecrypted  = "";
			$p_sString = base64_decode($p_sString);
			mcrypt_generic_init($this->m_rCipher, $this->m_sSecretKey, $this->m_sVector);
			$sDecrypted = mdecrypt_generic($this->m_rCipher,$p_sString);
			mcrypt_generic_deinit($this->m_rCipher);
			
			return $sDecrypted;
		}else 
			{
				return $p_sString;
			}
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
		if(is_array($p_aData))
		{
			if($p_sVarname !="" && isset($p_aData[$p_sVarname]))
			{
				$p_aData[$p_sVarname] = $this->encrypt($p_aData[$p_sVarname]);
			}elseif($p_sVarname !="" && !isset($p_aData[$p_sVarname]))
				{
					throw new Exception("Can't encrypt the data for offset ".$p_sVarname." offset don't exists");
				}else 
					{
						foreach($p_aData AS $mKey => $mValue)
						{
							if(!in_array($mKey,$p_aExclusions))
							{
								$p_aData[$mKey] = $this->encrypt($mValue);
							}
						}
					}
		}else 
			{
				throw new Exception("multiEncrypt expects a array");
			}
			
		return $p_aData;
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
		if(is_array($p_aData))
		{
			if($p_sVarname !="" && isset($p_aData[$p_sVarname]))
			{
				$p_aData[$p_sVarname] = rtrim($this->decrypt($p_aData[$p_sVarname]));
			}elseif($p_sVarname !="" && !isset($p_aData[$p_sVarname]))
				{
					throw new Exception("Can't decrypt the data for offset ".$p_sVarname." offset don't exists");
				}else 
					{
						foreach($p_aData AS $mKey => $mValue)
						{
							if(!in_array($mKey,$p_aExclusions))
							{
							    $p_aData[$mKey] = rtrim($this->decrypt($mValue));
							}
						}
					}
		}else 
			{
				throw new Exception("multiDecrypt expects a array");
			}
			
		return $p_aData;
	}
}