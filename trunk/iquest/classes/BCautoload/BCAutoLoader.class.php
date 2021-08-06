<?php
/**
 * 
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BCcore
 * @subpackage BCautoload
 * @project BCApplication framework
 * @version  1.0
 * @date 2012-09-01
 * @description : wordt aangeroepen door __autoLoad voor lazyloading van de classes. 
 * 				  controleert of de gevraagde class bestaat ergens in de include path, zo niet dan autoLoadException.
 *
 */
abstract class BCcore_BCautoload_BCAutoLoader 
{
	
	
	/**
	 * Register () : Register Autoloader AS SPL Loader
	 * @access public
	 * @return void
	 * @param string $p_sLoader
	 */
	public static function Register($p_sLoader)
	{
		spl_autoload_register($p_sLoader, true, true);
	}
	
	public static function autoLoad($p_sClassName)
	{
	   
		//split the pathname
		$aFilePath = preg_split("/_/",$p_sClassName);
		$sClassFileName = end($aFilePath);
		$sClassFileName = APPLICATIONREALPATH.$aFilePath[0]."/".$sClassFileName.".class.php";
		
		if(file_exists($sClassFileName))
		{
			require_once($sClassFileName);
		}else 
			{
			    //Try Module directory
			    
			    $sClassName = end($aFilePath);
			    $sClassFileName = preg_replace("#classes#","modules",APPLICATIONREALPATH)."/".$aFilePath[0]."/controlers/".$sClassName.".class.php";
			    if(file_exists($sClassFileName))
			    {
			        require_once($sClassFileName);
			    }elseif($aFilePath[1] == "Models")
			    {
			        $sClassFileName = preg_replace("#classes#","modules",APPLICATIONREALPATH);
			        $sDirPath = "/";
			        foreach($aFilePath as $iKey => $sDir)
			        {
			            if($sDir != $sClassName)
			            {
			                 $sDirPath .= $aFilePath[$iKey]."/";
			            }
			        }
			        
			        
			        $sClassFileName =  strtolower($sClassFileName.$sDirPath).$sClassName.".class.php";
			        if(file_exists($sClassFileName))
			        {
			            require_once($sClassFileName);
			        }else {
			            throw new BCautoload_BCAutoLoadException("bestand bestaat niet : ". $sClassFileName);
			        }
			    }else 
			         {
			             throw new BCautoload_BCAutoLoadException("bestand bestaat niet : ". $sClassFileName);
			         }
			}
	}
	
	
	
}