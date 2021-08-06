<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BClibarys
 * @subpackage BChttpRequest
 * @project BCApplication framework
 * @version  1.0
 * @date 2012-09-01
 * @description :
 *  - class for handeling the http request uri, split parameters etc
 *
 */
class BChttprequest_BCHttpRequestHandler
{ 
	/**
	 * @access public
	 * @var object
	 */
	public static $s_Instance = NULL;
	
	/**
	 * 
	 * @access private
	 * @var string
	 */
	private $m_sWebhost;
	
	/**
	 * 
	 * @access private
	 * @var string
	 */
	private $m_sApplicationPath;
	
	/**
	 * 
	 * @access private
	 * @var array
	 */
	private  $m_aParameters;
	
	/**
	 * constructor():
	 * @access public
	 * @return void
	 */
	private function __construct()
	{
		
		$this->m_sWebhost = "";
		$this->m_sApplicationPath = "";
		$this->m_aParameters = array();
	}
	
	/**
	 * getInstance() : checks if there is all ready a instance. If so retun that instance otherwise instantiate a new one en return
	 * @access public
	 * @return object
	 */
	public static function getInstance()
	{
		if(self::$s_Instance == NULL)
		{
			self::$s_Instance = new BChttprequest_BCHttpRequestHandler();
		}
		
		return self::$s_Instance;
	}
	
	/**
	 * setWebhost() : sets the member after make the right construction of the parameter string
	 * @access public
	 * @param string $p_sWebhost
	 * @return void
	 */
	public function setWebhost($p_sWebhost)
	{
		if(!preg_match("#^http://#",$p_sWebhost) && !preg_match("#^https://#",$p_sWebhost) )
		{
			/**
			 * 
			 * When there is no protocol http or https, use http as default
			 */
			$this->m_sWebhost = "http://".$p_sWebhost;
		}else 
			{
				$this->m_sWebhost = $p_sWebhost;
			}
			
		/**
		 * the webhost must close with a backspace
		 */
		if(!preg_match("#/$#",$this->m_sWebhost))
		{
			//$this->m_sWebhost = $this->m_sWebhost."/";
		}
		
		
	}
	
	/**
	 * setApplicationPath():
	 * @access public
	 * @param string $p_sPath
	 * @return void
	 * @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function setApplicationPath($p_sPath)
	{
		$this->m_sApplicationPath = $p_sPath;
		
		if(!preg_match("#^/#",$p_sPath))
		{
			//$this->m_sApplicationPath = "/". $this->m_sApplicationPath;
		}
			
	   if(!preg_match("#/$#",$p_sPath))
		{
			$this->m_sApplicationPath = $this->m_sApplicationPath."/";
		}
		
	}
	
	/**
	 * getRequestURl() : Returns the requesturi without the applicationpath
	 * @access public
	 * @return string 
	 * @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function getRequestURI()
	{
		
		if(preg_match("#".$this->m_sApplicationPath."#",$_SERVER["REQUEST_URI"]))
		{
			if($this->m_sApplicationPath !="/")
			{
				$sURI =  preg_replace("#".$this->m_sApplicationPath."#","",$_SERVER["REQUEST_URI"]);
			}else 
				{
					$sURI = $_SERVER["REQUEST_URI"];
				}
			
			if(!preg_match("#^$#",$sURI))
			{
				if(!preg_match("#^/#",$sURI))
				{
					$sURI = "/".$sURI;
				}
			
				if(!preg_match("#/$#",$sURI))
				{
					$sURI = $sURI."/";
				}
				
				
			}
			
			return $sURI;
			
			
		}else 
			{
				throw new BClibarys_BChttprequest_BCHTTPRequestException("Applicationpath don't exist. can't extract request uri");
			}
	}
	
	/**
	 * extractParameters(): extract parameters from the request URI with a pattern as filter 
	 * @access public
	 * @param string $p_sFilterPart
	 * @return void
	 * @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function extractParameters($p_sFilterPart)
	{
		
		$p_sFilterPart = preg_replace("#^/#","",$p_sFilterPart);
		if(preg_match("#".$this->m_sApplicationPath.$p_sFilterPart."#",$_SERVER["REQUEST_URI"]."/"))
		{
			$sParameters =  preg_replace("#".$this->m_sApplicationPath.$p_sFilterPart."#","",$_SERVER["REQUEST_URI"]."/");
			$aParameters = explode("/",$sParameters);
			
			for($i=0;$i<count($aParameters);$i++)
			{
				if($aParameters[$i]!="")
				{
					array_push($this->m_aParameters,$aParameters[$i]);
				}
				
			}
			
		}else 
			{
				throw new BClibarys_BChttprequest_BCHTTPRequestException("can't extract parameters from filterpart : ".$p_sFilterPart);
			}
	}
	
	/**
	 * getParameterArray() ; returns the parameter array
	 * @access public
	 * @return array
	 */
	public function getParameterArray()
	{
		return $this->m_aParameters;
	}
	
	/**
	 * getParameter() : returns a parameter by offset 
	 * @access public
	 * @param integer $p_iOffset
	 * @return boolean
	 * @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function getParaMeter($p_iOffset)
	{
		if(is_int($p_iOffset))
		{
			if(array_key_exists($p_iOffset,$this->m_aParameters))
			{
				return $this->m_aParameters[$p_iOffset];
			}else 
				{
					return FALSE;
				}
		}else 
			{
				throw new BClibarys_BChttprequest_BCHTTPRequestException("integer or numeric expected, can't get parameter");
			}
	}
	
	/**
	 * 
	 *  deleteParameter():
	 *  @access public
	 *  @param integer $p_iOffset
	 *  @return boolean
	 *  @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function deleteParaMeter($p_iOffset)
	{
		if(is_int($p_iOffset))
		{
			if(array_key_exists($p_iOffset,$this->m_aParameters))
			{
				unset($this->m_aParameters[$p_iOffset]);
				return TRUE;
			}else 
				{
					return FALSE;
				}
		}else 
			{
				throw new BClibarys_BChttprequest_BCHTTPRequestException("integer or numeric expected, can't delete parameter");
			}
	}
	
	/**
	 *  resetParaMeter() :
	 *  @access public
	 *  @param integer $p_iOffset
	 *  @param mixed $p_mValue
	 *  @return boolean
	 *  @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function resetParaMeter($p_iOffset,$p_mValue)
	{
		if(is_int($p_iOffset))
		{
			if(array_key_exists($p_iOffset,$this->m_aParameters))
			{
				$this->m_aParameters[$p_iOffset]=$p_mValue;
				return TRUE;
			}else 
				{
					return FALSE;
				}
		}else 
			{
				throw new BClibarys_BChttprequest_BCHTTPRequestException("integer or numeric expected, can't reset parameter");
			}
	}
	
	/**
	 *  addParaMeter() :
	 *  @access public
	 *  @param integer $p_iOffset
	 *  @param mixed $p_mValue
	 *  @return void
	 *  @throws BClibarys_BChttprequest_BCHTTPRequestException
	 */
	public function addParaMeter($p_iOffset,$p_mValue)
	{
		if(is_int($p_iOffset))
		{
			if(!array_key_exists($p_iOffset,$this->m_aParameters))
			{
				$this->m_aParameters[$p_iOffset]=$p_mValue;
				
			}else 
				{
					throw new BClibarys_BChttprequest_BCHTTPRequestException("can't add parameter at offset :".$p_iOffset." this is in use");
				}
		}else 
			{
				throw new BClibarys_BChttprequest_BCHTTPRequestException("integer or numeric expected, can't add parameter");
			}
	
	}
	
	/**
	 *  redirect() :
	 *  @access public
	 *  @param string $p_sLocation 
	 *  @return void
	 *  @Todo : In some codeparts there comes a extra / in the URL
	 *          - Must be fixt, for now quick fix
	 */
	public static function redirect($p_sLocation)
	{
	   
	    if(preg_match("#//#",$p_sLocation))
	    {
	        $p_sLocation = preg_replace("#//#","/",$p_sLocation);
	        $p_sLocation = preg_replace("#http:/#","http://",$p_sLocation);
	        
	    }
	 
		//print_r( headers_list());
		header("Location:".$p_sLocation);
		exit();
	}
}