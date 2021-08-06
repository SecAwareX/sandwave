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
class BCEncryptionOSSL
{
	
	/**
	 * @access private
	 * @var string : The cipher(algorithm) of choice, default it is AES-256-CBC
	 */
	private $m_sCipher;
	
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
		$this->m_sCipher = "";
		$this->m_iKeySize = 0;
		$this->m_sSecretKey = "";
		$this->m_sVector = "";
		
		$this->checkEncryptionModule();
		$this->setCipher("aes-256-cbc");
		
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
	    if(!in_array("openssl",$aExtentions))
	    {
	        throw new Exception("openssl is not supported on this server");
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
	   
	    if(!in_array($p_sCipher, openssl_get_cipher_methods())) {
	        throw new Exception($p_sCipher."is not a supported Cipher");
	    }else {
	        return true;
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
	    $this->m_sVector = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->m_sCipher));
	    return base64_encode($this->m_sVector);
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
	    
	    $sKey = substr($sKey1, 0, $this->m_iKeySize/2) . substr(strtoupper($sKey2), (round(strlen($sKey2) / 2)), $this->m_iKeySize/2);
	    $sKey = substr($sKey.$sKey1.$sKey2.strtoupper($sKey1),0,$this->m_iKeySize);
	    
	    return base64_encode(sha1($sKey));
		
	}
	
	/**
	 *  setCipher() : sets the cipher and get the keysize.
	 *                Keysize is not mandatory in open ssl?? study this
	 *        
	 *  @access public
	 *  @param string $p_sCipher : cipher name
	 *  @return void
	 *  @throws Exception
	 *  @todo: keysize mandatory or not??
	 */
	public function setCipher($p_sCipher)
	{
	    if($this->checkCipher($p_sCipher)) {
	        $this->m_sCipher = $p_sCipher;
	        $this->m_iKeySize = openssl_cipher_iv_length($p_sCipher)*2;
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
	    if($this->isBase64($p_sKey)){
	        $this->m_sSecretKey = base64_decode($p_sKey);
	    }else {
	        throw new Exception("Base64 format is excepted");
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
	    if($this->isBase64($p_sVector)){
	        $this->m_sVector = base64_decode($p_sVector);
	    }else {
	        throw new Exception("Base64 format is excepted");
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
	        
	       // $p_sString = $p_sString."[".time();
	        $sEncrypted = "";
	        $sEncrypted = base64_encode(openssl_encrypt($p_sString, $this->m_sCipher, $this->m_sSecretKey, $options=0, $this->m_sVector));
	        
	        //When there is no padding, add one
	        if(!preg_match("/=$/",$sEncrypted)){
	            $sEncrypted = $sEncrypted."=";
	        }
	        
	        return $sEncrypted;
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
	        $sDecrypted = openssl_decrypt(base64_decode($p_sString), $this->m_sCipher, $this->m_sSecretKey, $options=0, $this->m_sVector);
	        
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
	 *  @return array
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
	 *  @return array
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